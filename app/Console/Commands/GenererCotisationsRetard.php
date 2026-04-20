<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\TypeCotisation;

#[Signature('cotisations:generer-retards {--dry-run : Simuler sans écrire en DB}')]
#[Description('Génère les cotisations en retard pour tous les fidèles actifs (cron du 5 du mois)')]
class GenererCotisationsRetard extends Command
{
     protected $signature   = 'cotisations:generer-retards {--dry-run : Simuler sans écrire en DB}';
    protected $description = 'Génère les cotisations en retard pour tous les fidèles actifs (cron du 5 du mois)';

    public function handle(): int
    {
        $dryRun   = $this->option('dry-run');
        $moisEnCours = Carbon::now()->startOfMonth();

        $this->info("═══════════════════════════════════════════════════");
        $this->info("  Génération cotisations en retard — " . $moisEnCours->translatedFormat('F Y'));
        $this->info("  Mode : " . ($dryRun ? '🔍 Simulation (dry-run)' : '✅ Écriture réelle'));
        $this->info("═══════════════════════════════════════════════════");

        /* Fidèles avec un type mensuel obligatoire actif */
        $customers = Customer::whereNotNull('type_cotisation_mensuel_id')
            ->where('status', 'actif')
            ->with(['typeCotisationMensuel'])
            ->get();

        $this->info("👥 Fidèles concernés : {$customers->count()}");
        $this->newLine();

        $totalCrees  = 0;
        $totalIgnores = 0;

        $bar = $this->output->createProgressBar($customers->count());
        $bar->start();

        foreach ($customers as $customer) {
            $tc = $customer->typeCotisationMensuel;

            /* Sécurité : type supprimé ou désactivé */
            if (! $tc || $tc->status !== 'actif') {
                $bar->advance();
                continue;
            }

            $engagement = $customer->montant_engagement;

            /* Pas d'engagement défini → on ne peut pas créer de cotisation */
            if (! $engagement || $engagement <= 0) {
                $bar->advance();
                continue;
            }

            /* Date d'adhésion du fidèle (début des obligations) */
            $dateAdhesion = $customer->date_adhesion
                ? Carbon::parse($customer->date_adhesion)->startOfMonth()
                : $moisEnCours->copy();

            /* Dernière cotisation de ce type pour ce fidèle */
            $derniere = Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $tc->id)
                ->orderByDesc('annee')
                ->orderByDesc('mois')
                ->first();

            /* Mois de départ : le mois suivant la dernière cotisation,
             * ou la date d'adhésion si aucune cotisation n'existe. */
            $depart = $derniere
                ? Carbon::create($derniere->annee, $derniere->mois, 1)->addMonth()
                : $dateAdhesion->copy();

            /* Parcourir tous les mois entre $depart et $moisEnCours inclus */
            $curseur = $depart->copy()->startOfMonth();

            while ($curseur->lte($moisEnCours)) {
                /* Ne créer qu'à partir du mois d'adhésion */
                if ($curseur->lt($dateAdhesion)) {
                    $curseur->addMonth();
                    continue;
                }

                /* Vérifier si la cotisation existe déjà */
                $existe = Cotisation::where('customer_id', $customer->id)
                    ->where('type_cotisation_id', $tc->id)
                    ->where('mois',  $curseur->month)
                    ->where('annee', $curseur->year)
                    ->exists();

                if ($existe) {
                    $totalIgnores++;
                    $curseur->addMonth();
                    continue;
                }

                /* Créer la cotisation en_retard */
                if (! $dryRun) {
                    Cotisation::create([
                        'customer_id'        => $customer->id,
                        'type_cotisation_id' => $tc->id,
                        'mois'               => $curseur->month,
                        'annee'              => $curseur->year,
                        'montant_du'         => $engagement,
                        'montant_paye'       => 0,
                        'montant_restant'    => $engagement,
                        'statut'             => 'en_retard',
                        'mode_paiement'      => null,
                        'reference'          => null,
                        'validated_by'       => null,
                        'validated_at'       => null,
                    ]);
                }

                $totalCrees++;
                $curseur->addMonth();
            }

            /*
             * ──────────────────────────────────────────────────────
             * RAPPEL SMS — cotisation la plus ancienne en retard
             * ──────────────────────────────────────────────────────
             *
             * Décommenter et brancher sur votre service SMS (Orange CI,
             * MTN CI, Infobip, Twilio, etc.) quand il sera disponible.
             *
             * $plusAncienRetard = Cotisation::where('customer_id', $customer->id)
             *     ->where('statut', 'en_retard')
             *     ->orderBy('annee')->orderBy('mois')
             *     ->first();
             *
             * if ($plusAncienRetard && $customer->phone) {
             *     $moisLabel = Carbon::create($plusAncienRetard->annee, $plusAncienRetard->mois)
             *         ->translatedFormat('F Y');
             *     $montantFormate = number_format($plusAncienRetard->montant_du, 0, ',', ' ');
             *
             *     $message = "Salam {$customer->prenom}, votre cotisation de {$montantFormate} FCFA "
             *              . "pour {$moisLabel} est en retard. Merci de régulariser. — ISL Mosquée";
             *
             *     // \App\Services\SmsService::send($customer->dial_code . $customer->phone, $message);
             *     // \Illuminate\Support\Facades\Log::info("SMS envoyé à {$customer->phone} : {$message}");
             * }
             * ──────────────────────────────────────────────────────
             */

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Cotisations créées',  $totalCrees],
                ['Déjà existantes',    $totalIgnores],
                ['Fidèles traités',    $customers->count()],
                ['Mode',              $dryRun ? 'Simulation' : 'Écriture réelle'],
            ]
        );

        if ($dryRun) {
            $this->warn("⚠️  Dry-run : aucune donnée écrite en base.");
        } else {
            $this->info("✅  {$totalCrees} cotisation(s) en retard créée(s).");
        }

        return Command::SUCCESS;
    }
}
