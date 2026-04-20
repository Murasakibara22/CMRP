<?php

namespace App\Jobs;

use App\Models\Cotisation;
use App\Models\Customer;
use App\Models\HistoriqueCotisation;
use App\Models\Paiement;
use App\Models\Transaction;
use App\Models\TypeCotisation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ImportCotisationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;   // 1h max
    public int $tries   = 1;      // pas de retry auto (idempotent mais prudent)

    public function __construct(
        private readonly string $filePath,   // chemin absolu du fichier stocké
        private readonly string $cacheKey,   // clé Cache pour le statut
        private readonly int    $adminId,    // auth()->id() au moment du dispatch
    ) {}

    public function handle(): void
    {
        $this->setStatus('running', 0, 'Import démarré…');

        try {
            $rows = $this->parseExcel($this->filePath);
            $total = count($rows);

            if ($total === 0) {
                $this->setStatus('error', 0, 'Aucune ligne valide dans le fichier.');
                return;
            }

            $done = 0;
            foreach ($rows as $row) {
                DB::transaction(function () use ($row) {
                    $this->importerLigne($row);
                });
                $done++;
                /* Mise à jour statut tous les 5 membres */
                if ($done % 5 === 0 || $done === $total) {
                    $pct = (int) round($done / $total * 100);
                    $this->setStatus('running', $pct, "{$done}/{$total} membres traités…");
                }
            }

            /* Nettoyage fichier temporaire */
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
            }

            $this->setStatus('done', 100, "Import terminé : {$total} membres traités.");

        } catch (\Throwable $e) {
            $this->setStatus('error', 0, 'Erreur : ' . $e->getMessage());
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
            }
        }
    }

    /* ═══════════════════════════════════════════════════════
       PARSING EXCEL
    ═══════════════════════════════════════════════════════ */
    private function parseExcel(string $path): array
    {
        $wb = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);

        $sheetMapping = [
            'Cotisation Famille' => 'Cotisation Famille',
            'Cotisatios CA'      => 'Cotisation CA',
        ];

        $rows = [];

        foreach ($wb->getSheetNames() as $sheetName) {
            $tcLibelle = null;
            foreach ($sheetMapping as $pattern => $libelle) {
                if (stripos($sheetName, trim($pattern)) !== false) {
                    $tcLibelle = $libelle;
                    break;
                }
            }
            if (! $tcLibelle) continue;

            $ws      = $wb->getSheetByName($sheetName);
            $highRow = $ws->getHighestDataRow();

            for ($r = 4; $r <= $highRow; $r++) {
                $mle       = $ws->getCell("B{$r}")->getValue();
                $nomComplet= $ws->getCell("C{$r}")->getValue();
                $engagement= $ws->getCell("D{$r}")->getValue();

                if (! $mle && ! $nomComplet) continue;
                if (! $nomComplet || trim((string) $nomComplet) === '') continue;

                $adresse = $ws->getCell("E{$r}")->getValue();
                $dateAdh = $ws->getCell("F{$r}")->getFormattedValue();
                $mobile  = $ws->getCell("G{$r}")->getValue();

                /* Colonnes mois H..S (col 8..19 = Jan..Déc) */
                $moisPaiements = [];
                for ($col = 8; $col <= 19; $col++) {
                    $cellAddr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $r;
                    $val      = $ws->getCell($cellAddr)->getCalculatedValue();
                    if ($val !== null && $val !== '' && trim((string) $val) !== '') {
                        $num = is_numeric($val) ? (int) round((float) $val) : 0;
                        if ($num > 0) {
                            $moisPaiements[$col - 7] = $num; // clé = mois 1-12
                        }
                    }
                }

                $engNum = ($engagement && is_numeric($engagement))
                    ? (int) round((float) $engagement) : 0;

                $dateAdhParsed = null;
                if ($dateAdh) {
                    try { $dateAdhParsed = Carbon::parse($dateAdh)->toDateString(); } catch (\Throwable) {}
                }

                $mobilePropre = $this->nettoyerMobile($mobile);
                [$nom, $prenom] = $this->splitNomPrenom(trim((string) $nomComplet));

                $rows[] = [
                    'matricule'    => $mle ? (string) $mle : null,
                    'nom'          => $nom,
                    'prenom'       => $prenom,
                    'engagement'   => $engNum,
                    'adresse'      => $adresse ? trim((string) $adresse) : null,
                    'date_adhesion'=> $dateAdhParsed,
                    'mobile'       => $mobilePropre,
                    'tc_libelle'   => $tcLibelle,
                    'mois_payes'   => $moisPaiements,
                    'annee'        => 2026,
                ];
            }
        }

        return $rows;
    }

    /* ═══════════════════════════════════════════════════════
       IMPORT D'UNE LIGNE
    ═══════════════════════════════════════════════════════ */
    private function importerLigne(array $row): void
    {
        /* 1. TypeCotisation */
        $tc = TypeCotisation::where('libelle', $row['tc_libelle'])
            ->where('type', 'mensuel')->first();

        if (! $tc) {
            $tc = TypeCotisation::create([
                'libelle'         => $row['tc_libelle'],
                'type'            => 'mensuel',
                'is_required'     => true,
                'montant_minimum' => $row['tc_libelle'] === 'Cotisation CA' ? 10000 : 1000,
                'status'          => 'actif',
            ]);
        }

        /* 2. Customer — matcher par matricule, puis mobile, puis phone généré
              Si plusieurs feuilles concernent le même membre (ex: Famille + CA)
              → le customer existe déjà, on met à jour son type et son engagement. */
        $customer  = null;
        $phoneGen  = '000' . ($row['matricule'] ?? uniqid()); // phone de substitution si mobile absent

        /* Recherche dans l'ordre : matricule → mobile → phone généré */
        if ($row['matricule']) {
            $customer = Customer::where('matricule', $row['matricule'])->first();
        }
        if (! $customer && $row['mobile']) {
            $customer = Customer::where('phone', $row['mobile'])->first();
        }
        if (! $customer) {
            /* Tenter aussi par phone généré (cas d'une 2e feuille sans mobile) */
            $customer = Customer::where('phone', $phoneGen)->first();
        }

        if (! $customer) {
            /* Création — firstOrCreate pour absorber toute race condition */
            $customer = Customer::firstOrCreate(
                ['phone' => $row['mobile'] ?: $phoneGen],
                [
                    'nom'                        => strtoupper($row['nom']),
                    'prenom'                     => ucwords(strtolower($row['prenom'])),
                    'dial_code'                  => '+225',
                    'adresse'                    => $row['adresse'],
                    'date_adhesion'              => $row['date_adhesion'] ?? '2020-01-01',
                    'montant_engagement'         => $row['engagement'] ?: null,
                    'type_cotisation_mensuel_id' => $tc->id,
                    'status'                     => 'actif',
                ]
            );
        } else {
            /*
             * Customer déjà en base.
             * Si le type de cotisation change (ex: était Famille, maintenant CA)
             * → c'est un changement de type mensuel : on met à jour engagement + type.
             * Si le type est le même, on ne touche à rien (déjà correct).
             */
            $updates = [];

            $estChangementDeType = $customer->type_cotisation_mensuel_id
                && $customer->type_cotisation_mensuel_id !== $tc->id;

            if ($estChangementDeType || ! $customer->type_cotisation_mensuel_id) {
                $updates['type_cotisation_mensuel_id'] = $tc->id;
            }

            /* Toujours mettre à jour l'engagement si le fichier en fournit un */
            if ($row['engagement'] && $row['engagement'] !== $customer->montant_engagement) {
                $updates['montant_engagement'] = $row['engagement'];
            }

            if ($updates) $customer->update($updates);
        }

        $engagement = $customer->montant_engagement ?? $row['engagement'];
        if (! $engagement || $engagement <= 0) $engagement = 1000; // fallback
        $annee = $row['annee'];

        /* 3. Cotisations payées (colonnes mois) */
        foreach ($row['mois_payes'] as $mois => $montantVerse) {
            if ($montantVerse <= 0) continue;

            /* Calculer combien de mois entiers sont couverts */
            $moisEntiers = (int) floor($montantVerse / $engagement);
            $surplus     = $montantVerse % $engagement;

            /* Si le montant est inférieur à 1 mois → 1 mois partiel */
            if ($moisEntiers === 0 && $montantVerse > 0) {
                $this->creerCotisation($customer, $tc, $mois, $annee, $engagement, $montantVerse, $engagement - $montantVerse, 'partiel');
                continue;
            }

            $moisBase = Carbon::create($annee, $mois, 1);
            for ($i = 0; $i < $moisEntiers; $i++) {
                $m = $moisBase->copy()->addMonths($i);
                $this->creerCotisation($customer, $tc, $m->month, $m->year, $engagement, $engagement, 0, 'a_jour');
            }
            if ($surplus > 0) {
                $suivant = $moisBase->copy()->addMonths($moisEntiers);
                $this->creerCotisation($customer, $tc, $suivant->month, $suivant->year, $engagement, $surplus, $engagement - $surplus, 'partiel');
            }
        }

        /* 4. Cotisations en retard : de date_adhesion jusqu'au mois courant */
        $debut = Carbon::parse($customer->date_adhesion ?? '2020-01-01')->startOfMonth();
        $fin   = Carbon::now()->startOfMonth();
        $moisItere = $debut->copy();

        while ($moisItere->lte($fin)) {
            $existe = Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $tc->id)
                ->where('mois', $moisItere->month)
                ->where('annee', $moisItere->year)
                ->exists();

            if (! $existe) {
                /* Ne pas créer de cotisation en_retard pour un mois futur
                   (le cron s'en chargera le moment venu) */
                if (! $moisItere->isFuture()) {
                    $this->creerCotisation($customer, $tc, $moisItere->month, $moisItere->year, $engagement, 0, $engagement, 'en_retard');
                }
            }
            $moisItere->addMonth();
        }
    }

    /* ─────────────────────────────────────────────────────
       Crée une cotisation + paiement/transaction. Idempotent.
    ───────────────────────────────────────────────────── */
    private function creerCotisation(
        Customer      $customer,
        TypeCotisation $tc,
        int $mois, int $annee,
        int $montantDu, int $montantPaye, int $montantRestant,
        string $statut
    ): void {
        $existe = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $tc->id)
            ->where('mois', $mois)->where('annee', $annee)
            ->exists();
        if ($existe) return;

        $cot = Cotisation::create([
            'customer_id'        => $customer->id,
            'type_cotisation_id' => $tc->id,
            'mois'               => $mois,
            'annee'              => $annee,
            'montant_du'         => $montantDu ?: null,
            'montant_paye'       => $montantPaye,
            'montant_restant'    => $montantRestant,
            'statut'             => $statut,
            'mode_paiement'      => 'espece', // mode inconnu à l'import
            'validated_by'       => $statut === 'a_jour' ? $this->adminId : null,
            'validated_at'       => $statut === 'a_jour' ? now() : null,
        ]);

        HistoriqueCotisation::log($cot, 'creation', $montantPaye, "Import Excel {$annee}");

        /* Paiement uniquement si cotisation soldée ou partielle — pas pour en_retard */
        if ($montantPaye > 0 && $statut !== 'en_retard') {
            $paiement = Paiement::create([
                'customer_id'        => $customer->id,
                'type_cotisation_id' => $tc->id,
                'cotisation_id'      => $cot->id,
                'montant'            => $montantPaye,
                'mode_paiement'      => 'espece', // mode inconnu à l'import
                'statut'             => $statut === 'a_jour' ? 'success' : 'en_attente',
                'date_paiement'      => Carbon::create($annee, $mois, 1)->isFuture()
                    ? now()
                    : Carbon::create($annee, $mois, 1),
            ]);

            if ($statut === 'a_jour') {
                Transaction::create([
                    'type'             => 'entree',
                    'source'           => 'paiement',
                    'source_id'        => $paiement->id,
                    'status'           => 'success',
                    'montant'          => $montantPaye,
                    'libelle'          => "Import – {$tc->libelle} – " . Carbon::create($annee, $mois)->translatedFormat('F Y'),
                    'date_transaction' => Carbon::create($annee, $mois, 1)->isFuture()
                    ? now()
                    : Carbon::create($annee, $mois, 1),
                ]);
            }
        }
    }

    /* ─────────────────────────────────────────────────────
       Helpers
    ───────────────────────────────────────────────────── */
    private function splitNomPrenom(string $nomComplet): array
    {
        $parts = explode(' ', $nomComplet, 2);
        if (count($parts) === 1) return [$parts[0], $parts[0]];
        if ($parts[0] === strtoupper($parts[0]) && strlen($parts[0]) > 1) {
            return [strtoupper($parts[0]), $parts[1]];
        }
        $words  = explode(' ', $nomComplet);
        $prenom = array_pop($words);
        return [strtoupper(implode(' ', $words)), $prenom];
    }

    private function nettoyerMobile($raw): ?string
    {
        if (! $raw) return null;
        $str = explode('/', (string) $raw)[0];
        $str = preg_replace('/[^\d]/', '', $str);
        if (strlen($str) > 10 && str_starts_with($str, '225')) {
            $str = substr($str, 3);
        }
        $str = substr($str, -10);
        return strlen($str) >= 8 ? $str : null;
    }

    private function setStatus(string $status, int $progress, string $message): void
    {
        Cache::put($this->cacheKey, [
            'status'   => $status,   // pending | running | done | error
            'progress' => $progress, // 0-100
            'message'  => $message,
            'updated'  => now()->toDateTimeString(),
        ], now()->addHours(2));
    }
}
