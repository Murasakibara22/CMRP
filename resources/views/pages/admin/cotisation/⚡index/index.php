<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Cotisation;
use App\Models\Customer;
use App\Models\TypeCotisation;
use App\Models\HistoriqueCotisation;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;

new class extends Component
{
    use WithPagination, UtilsSweetAlert, WithFileUploads;

    /* ── Filtres ────────────────────────────────────────── */
    public string $search      = '';
    public string $tabStatut   = 'tous';
    public string $filterType  = 'tous';
    public string $filterMois  = 'tous';
    public string $filterMode  = 'tous';

    /* ── Modal détail ───────────────────────────────────── */
    public ?int $detailId = null;

    /* ── Formulaire création / modification ─────────────── */
    public ?int    $editId           = null;
    public ?int    $customerId       = null;
    public string  $searchFidele     = '';
    public ?int    $typeCotisationId = null;
    public ?int    $mois             = null;
    public ?int    $annee            = null;
    public ?int    $montantPaye      = null;
    public string  $modePaiement     = '';
    public string  $reference        = '';
    public bool    $alerteEngagement = false;

    /* ═══════════════════════════════════════════════════════
       IMPORT EXCEL
       Étapes : upload → preview → running (Job) → done | error
       Le Job tourne en arrière-plan via Queue.
       Le composant poll le Cache toutes les 2s.
    ═══════════════════════════════════════════════════════ */
    public $importFile            = null;      // fichier uploadé (Livewire temp)
    public string $importStep     = 'upload';  // upload | preview | running | done | error
    public string $importError    = '';
    public array  $importStats    = [];        // résumé après parse (preview)
    public string $importCacheKey = '';        // clé Cache pour suivre le Job
    public int    $importProgress = 0;         // 0-100 lu depuis le Cache
    public string $importMessage  = '';        // message du Job lu depuis le Cache

    /* ── Reset pagination ───────────────────────────────── */
    public function updatedSearch(): void      { $this->resetPage(); }
    public function updatedTabStatut(): void   { $this->resetPage(); }
    public function updatedFilterType(): void  { $this->resetPage(); }
    public function updatedFilterMois(): void  { $this->resetPage(); }
    public function updatedFilterMode(): void  { $this->resetPage(); }

    /* ═══════════════════════════════════════════════════════
       MODAUX COTISATION
    ═══════════════════════════════════════════════════════ */
    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->launch_modal('modalDetailCotisation');
    }

    public function openCreate(?int $customerId = null): void
    {
        $this->resetForm();
        $this->mois  = now()->month;
        $this->annee = now()->year;
        if ($customerId) $this->customerId = $customerId;
        $this->launch_modal('modalCreateCotisation');
    }

    public function openEdit(int $id): void
    {
        $cot = Cotisation::with(['customer', 'typeCotisation'])->findOrFail($id);

        if ($cot->validated_at) {
            $this->send_event_at_sweet_alert_not_timer('Action impossible', 'Cette cotisation a déjà été validée.', 'warning');
            return;
        }

        $this->resetForm();
        $this->editId           = $id;
        $this->customerId       = $cot->customer_id;
        $this->typeCotisationId = $cot->type_cotisation_id;
        $this->mois             = $cot->mois;
        $this->annee            = $cot->annee;
        $this->montantPaye      = $cot->montant_paye;
        $this->modePaiement     = $cot->mode_paiement ?? '';
        $this->reference        = $cot->reference ?? '';
        $this->launch_modal('modalCreateCotisation');
    }

    public function selectFidele(int $id): void
    {
        $this->customerId       = $id;
        $this->searchFidele     = '';
        $this->alerteEngagement = false;
    }

    public function selectMode(string $mode): void
    {
        $this->modePaiement = $mode;
        $this->resetErrorBag();
    }

    /* ═══════════════════════════════════════════════════════
       SAVE COTISATION
    ═══════════════════════════════════════════════════════ */
    public function save(): void
    {
        $this->validate([
            'customerId'       => 'required|integer|exists:customers,id',
            'typeCotisationId' => 'required|integer|exists:type_cotisation,id',
            'montantPaye'      => 'required|integer|min:1',
            'modePaiement'     => 'required|string|in:espece,mobile_money,virement',
        ]);

        $customer = Customer::findOrFail($this->customerId);
        $tc       = TypeCotisation::findOrFail($this->typeCotisationId);

        if ($tc->montant_minimum && $this->montantPaye < $tc->montant_minimum) {
            $this->send_event_at_sweet_alert_not_timer('Montant insuffisant', "Ce type exige un minimum de " . number_format($tc->montant_minimum, 0, ',', ' ') . " FCFA.", 'warning');
            return;
        }

        if ($this->editId) {
            $this->_updateCotisation($customer, $tc);
            return;
        }

        $this->_createCotisation($customer, $tc);
    }

    private function _createCotisation(Customer $customer, TypeCotisation $tc): void
    {
        $isMensuel            = $tc->type === 'mensuel';
        $isMensuelObligatoire = $isMensuel && $tc->is_required;

        if ($isMensuelObligatoire && ! $customer->montant_engagement) {
            $this->alerteEngagement = true;
            $this->send_event_at_sweet_alert_not_timer('Engagement requis', "Ce fidèle n'a pas de montant d'engagement mensuel.", 'warning');
            return;
        }

        if (! $isMensuel) {
            $cot = Cotisation::create([
                'customer_id'        => $customer->id,
                'type_cotisation_id' => $tc->id,
                'mois'               => null, 'annee' => null,
                'montant_du'         => $this->montantPaye,
                'montant_paye'       => $this->montantPaye,
                'montant_restant'    => 0,
                'statut'             => 'en_retard',
                'mode_paiement'      => $this->modePaiement,
                'reference'          => $this->reference ?: null,
                'validated_by'       => null, 'validated_at' => null,
            ]);
            HistoriqueCotisation::log($cot, 'creation', $this->montantPaye);
            $this->_createPaiement($cot, $this->montantPaye);
            $this->_finishSave();
            return;
        }

        if ($isMensuelObligatoire && ! $customer->type_cotisation_mensuel_id) {
            $customer->update(['type_cotisation_mensuel_id' => $tc->id]);
        }

        $engagement = $customer->montant_engagement;
        $budget     = $this->montantPaye;

        $derniere = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $tc->id)
            ->orderByDesc('annee')->orderByDesc('mois')->first();

        $prochainMois = $derniere
            ? Carbon::create($derniere->annee, $derniere->mois)->addMonth()
            : Carbon::create($this->annee, $this->mois);

        while ($budget > 0) {
            $exists = Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $tc->id)
                ->where('mois', $prochainMois->month)->where('annee', $prochainMois->year)->exists();
            if ($exists) { $prochainMois->addMonth(); continue; }

            $montantCe = min($budget, $engagement);
            $restantCe = $engagement - $montantCe;
            $budget   -= $montantCe;

            $cot = Cotisation::create([
                'customer_id'        => $customer->id,
                'type_cotisation_id' => $tc->id,
                'mois'               => $prochainMois->month,
                'annee'              => $prochainMois->year,
                'montant_du'         => $engagement,
                'montant_paye'       => $montantCe,
                'montant_restant'    => $restantCe,
                'statut'             => 'en_retard',
                'mode_paiement'      => $this->modePaiement,
                'reference'          => $this->reference ?: null,
                'validated_by'       => null, 'validated_at' => null,
            ]);
            HistoriqueCotisation::log($cot, 'creation', $montantCe, "Cotisation {$prochainMois->month}/{$prochainMois->year}");
            $this->_createPaiement($cot, $montantCe);
            if ($restantCe > 0) break;
            $prochainMois->addMonth();
        }

        $this->_finishSave();
    }

    private function _updateCotisation(Customer $customer, TypeCotisation $tc): void
    {
        $cot       = Cotisation::findOrFail($this->editId);
        $isMensuel = $tc->type === 'mensuel';
        $montantDu = $isMensuel ? ($customer->montant_engagement ?? $this->montantPaye) : $this->montantPaye;
        $restant   = max(0, $montantDu - $this->montantPaye);
        $statut    = $this->montantPaye < $montantDu ? 'partiel' : 'en_retard';

        $cot->update([
            'type_cotisation_id' => $this->typeCotisationId,
            'mois'               => $isMensuel ? $this->mois : null,
            'annee'              => $isMensuel ? $this->annee : null,
            'montant_du'         => $montantDu,
            'montant_paye'       => $this->montantPaye,
            'montant_restant'    => $restant,
            'statut'             => $statut,
            'mode_paiement'      => $this->modePaiement,
            'reference'          => $this->reference ?: null,
        ]);
        HistoriqueCotisation::log($cot, 'ajustement', $this->montantPaye, 'Modification manuelle BO');

        $lastPaiement = $cot->paiements()->latest()->first();
        if ($lastPaiement) {
            $lastPaiement->update(['montant' => $this->montantPaye, 'mode_paiement' => $this->modePaiement, 'reference' => $this->reference ?: null]);
        } else {
            $this->_createPaiement($cot, $this->montantPaye);
        }

        $this->closeModal_after_edit('modalCreateCotisation');
        $this->resetForm();
        $this->send_event_at_toast('Cotisation modifiée avec succès', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       VALIDATION MANUELLE
    ═══════════════════════════════════════════════════════ */
    public function confirmerValidation(int $id): void
    {
        $cot = Cotisation::with(['paiements', 'typeCotisation'])->findOrFail($id);

        if ($cot->montant_du && $cot->montant_paye < $cot->montant_du) {
            $this->send_event_at_sweet_alert_not_timer('Validation impossible', "Cotisation incomplète (" . number_format($cot->montant_paye, 0, ',', ' ') . " / " . number_format($cot->montant_du, 0, ',', ' ') . " FCFA). Modifiez-la d'abord.", 'warning');
            return;
        }

        if ($cot->mois && $cot->annee) {
            $blocage = Cotisation::where('customer_id', $cot->customer_id)
                ->where('type_cotisation_id', $cot->type_cotisation_id)
                ->where('id', '!=', $cot->id)
                ->where(fn($q) => $q->where('annee', '<', $cot->annee)->orWhere(fn($q) => $q->where('annee', $cot->annee)->where('mois', '<', $cot->mois)))
                ->whereNull('validated_at')->orderBy('annee')->orderBy('mois')->first();

            if ($blocage) {
                $label = Carbon::create($blocage->annee, $blocage->mois)->translatedFormat('F Y');
                $this->send_event_at_sweet_alert_not_timer('Validation impossible', "La cotisation de {$label} n'est pas encore validée.", 'warning');
                return;
            }
        }

        $this->sweetAlert_confirm_options_with_button($cot, 'Valider ce paiement ?', 'Vous confirmez la réception de ' . number_format($cot->montant_paye, 0, ',', ' ') . ' FCFA.', 'validerPaiement', 'question', 'Oui, valider', 'Annuler');
    }

    #[On('validerPaiement')]
    public function validerPaiement(int $id): void
    {
        $cot = Cotisation::with(['paiements', 'typeCotisation'])->findOrFail($id);
        $cot->update(['statut' => 'a_jour', 'montant_restant' => 0, 'validated_by' => auth()->id(), 'validated_at' => now()]);
        HistoriqueCotisation::log($cot, 'validation', $cot->montant_paye, 'Validation admin');

        $lastPaiement = $cot->paiements()->latest()->first();
        if ($lastPaiement) {
            $lastPaiement->update(['statut' => 'success', 'date_paiement' => now()]);
            $txExists = \App\Models\Transaction::where('source', 'paiement')->where('source_id', $lastPaiement->id)->exists();
            if (! $txExists) {
                \App\Models\Transaction::create([
                    'type' => 'entree', 'source' => 'paiement', 'source_id' => $lastPaiement->id,
                    'status' => 'success', 'montant' => $lastPaiement->montant,
                    'libelle' => "Cotisation – {$cot->typeCotisation->libelle}" . ($cot->mois ? " – " . Carbon::create($cot->annee, $cot->mois)->translatedFormat('F Y') : ''),
                    'date_transaction' => now(),
                ]);
            }
        }

        $this->closeModal_after_edit('modalDetailCotisation');
        $this->detailId = null;
        $this->send_event_at_toast('Paiement validé avec succès', 'success', 'top-end');
    }

    public function changerStatut(int $id, string $nouveauStatut): void
    {
        $cot = Cotisation::findOrFail($id);
        if ($cot->validated_at && in_array($nouveauStatut, ['en_retard', 'partiel'])) {
            $this->send_event_at_sweet_alert_not_timer('Action impossible', 'Cette cotisation a été validée et ne peut plus être rétrogradée.', 'warning');
            return;
        }
        $ancienStatut = $cot->statut;
        $cot->update(['statut' => $nouveauStatut]);
        HistoriqueCotisation::log($cot, 'ajustement', $cot->montant_paye, "Statut : {$ancienStatut} → {$nouveauStatut}");

        if ($nouveauStatut === 'a_jour') {
            $cot->update(['montant_restant' => 0, 'validated_by' => auth()->id(), 'validated_at' => now()]);
            $lastPaiement = $cot->paiements()->latest()->first();
            if ($lastPaiement && $lastPaiement->statut !== 'success') {
                $lastPaiement->update(['statut' => 'success']);
                $txExists = \App\Models\Transaction::where('source', 'paiement')->where('source_id', $lastPaiement->id)->exists();
                if (! $txExists) {
                    \App\Models\Transaction::create(['type'=>'entree','source'=>'paiement','source_id'=>$lastPaiement->id,'status'=>'success','montant'=>$lastPaiement->montant,'libelle'=>"Régularisation – {$cot->typeCotisation->libelle}",'date_transaction'=>now()]);
                }
            } elseif (! $lastPaiement) {
                $cot->update(['montant_paye' => $cot->montant_du, 'montant_restant' => 0]);
                $this->_createPaiementEtTransaction($cot, $cot->montant_du ?? 0);
            }
        }
        $this->send_event_at_toast('Statut mis à jour', 'success', 'top-end');
    }

    public function confirmDelete(int $id): void
    {
        $cot = Cotisation::findOrFail($id);
        $this->sweetAlert_confirm_options_with_button($cot, 'Supprimer cette cotisation ?', 'Cette action est irréversible.', 'deleteConfirmed', 'warning', 'Oui, supprimer', 'Annuler');
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        $cot = Cotisation::find($id);
        if (! $cot) return;
        $cot->delete();
        if ($this->detailId === $id) { $this->detailId = null; $this->closeModal_after_edit('modalDetailCotisation'); }
        $this->send_event_at_toast('Cotisation supprimée', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       IMPORT — OUVERTURE
    ═══════════════════════════════════════════════════════ */
    public function openImport(): void
    {
        $this->importFile     = null;
        $this->importStep     = 'upload';
        $this->importError    = '';
        $this->importStats    = [];
        $this->importCacheKey = '';
        $this->importProgress = 0;
        $this->importMessage  = '';
        $this->launch_modal('modalImportCotisation');
    }

    /* ═══════════════════════════════════════════════════════
       IMPORT — ÉTAPE 1 : PARSE RAPIDE (analyse seule, pas d'import)
       Lit le fichier en mémoire, calcule les stats, stocke le
       fichier sur disque pour le Job, retourne la preview.
    ═══════════════════════════════════════════════════════ */
    public function parseImport(): void
    {
        $this->importError = '';

        /*
         * NE PAS utiliser validate() avec 'mimes' sur un fichier Livewire temp.
         * Livewire stocke le fichier dans livewire-tmp/ ; getSize() peut échouer
         * selon la config du disque. On valide l'extension manuellement.
         */
        if (! $this->importFile) {
            $this->importError = 'Veuillez sélectionner un fichier Excel (.xlsx ou .xls).';
            return;
        }

        $ext = strtolower($this->importFile->getClientOriginalExtension());
        if (! in_array($ext, ['xlsx', 'xls'])) {
            $this->importError = 'Format non supporté. Utilisez un fichier .xlsx ou .xls.';
            return;
        }

        try {
            /*
             * Livewire WithFileUploads utilise son propre disk ("livewire-tmp").
             * getRealPath() retourne false sur ce disk virtuel.
             * Solution : stocker d'abord le fichier sur le disk 'local',
             * puis utiliser storage_path() pour obtenir le vrai chemin absolu.
             */
            $stored = $this->importFile->storeAs(
                'imports',
                'cotisations_' . now()->format('YmdHis') . '_' . auth()->id() . '.xlsx',
                'local'
            );

            if (! $stored) {
                $this->importError = 'Impossible de stocker le fichier. Vérifiez les permissions de storage/.';
                return;
            }

            $storedPath = \Storage::disk('local')->path($stored);

            $rows = $this->_parseExcel($storedPath);

            if (empty($rows)) {
                /* Supprimer le fichier si inutilisable */
                @unlink($storedPath);
                $this->importError = 'Aucune ligne valide trouvée dans le fichier.';
                return;
            }

            /* Clé Cache unique pour ce Job */
            $this->importCacheKey = 'import_cotisations_' . auth()->id() . '_' . now()->timestamp;

            /* Stocker les infos du Job en session */
            session([
                'import_file_path'  => $storedPath,
                'import_cache_key'  => $this->importCacheKey,
                'import_admin_id'   => auth()->id(),
            ]);

            $this->importStats = $this->_buildImportStats($rows);
            $this->importStep  = 'preview';

        } catch (\Throwable $e) {
            $this->importError = "Erreur lors de la lecture : " . $e->getMessage();
        }
    }

    /* ═══════════════════════════════════════════════════════
       IMPORT — ÉTAPE 2 : LANCER LE JOB
       Dispatch le Job et passe en mode "running".
       Le polling JS interrogera pollImportStatus().
    ═══════════════════════════════════════════════════════ */
    public function confirmerImport(): void
    {
        $this->importError = '';

        $filePath  = session('import_file_path');
        $cacheKey  = session('import_cache_key', $this->importCacheKey);
        $adminId   = session('import_admin_id', auth()->id());

        if (! $filePath || ! file_exists($filePath)) {
            $this->importError = 'Fichier introuvable. Veuillez re-uploader.';
            $this->importStep  = 'upload';
            return;
        }

        /* Initialiser le statut dans Cache */
        \Cache::put($cacheKey, [
            'status'   => 'pending',
            'progress' => 0,
            'message'  => 'Import en file d\'attente…',
            'updated'  => now()->toDateTimeString(),
        ], now()->addHours(2));

        /* Dispatcher le Job */
        \App\Jobs\ImportCotisationsJob::dispatch($filePath, $cacheKey, $adminId)
            ->onQueue('default');

        $this->importCacheKey = $cacheKey;
        $this->importStep     = 'running';
        $this->importProgress = 0;
        $this->importMessage  = 'Import en file d\'attente…';
    }

    /* ═══════════════════════════════════════════════════════
       IMPORT — POLLING STATUT (appelé par JS toutes les 2s)
    ═══════════════════════════════════════════════════════ */
    public function pollImportStatus(): void
    {
        if (! $this->importCacheKey) return;

        $data = \Cache::get($this->importCacheKey);
        if (! $data) return;

        $this->importProgress = $data['progress'] ?? 0;
        $this->importMessage  = $data['message']  ?? '';

        if ($data['status'] === 'done') {
            $this->importStep = 'done';
            session()->forget(['import_file_path', 'import_cache_key', 'import_admin_id']);
            $this->send_event_at_toast('Import terminé avec succès !', 'success', 'top-end');
        } elseif ($data['status'] === 'error') {
            $this->importStep  = 'error';
            $this->importError = $data['message'] ?? 'Erreur inconnue.';
            session()->forget(['import_file_path', 'import_cache_key', 'import_admin_id']);
        }
    }

    public function closeImport(): void
    {
        /* On ne supprime pas le fichier si le Job tourne encore */
        if ($this->importStep !== 'running') {
            session()->forget(['import_file_path', 'import_cache_key', 'import_admin_id']);
        }
        $this->importFile     = null;
        $this->importStep     = 'upload';
        $this->importError    = '';
        $this->importStats    = [];
        $this->importCacheKey = '';
        $this->importProgress = 0;
        $this->importMessage  = '';
        $this->closeModal_after_edit('modalImportCotisation');
    }

    /* ─────────────────────────────────────────────────────
       _parseExcel — parse UNIQUEMENT pour la preview.
       Le vrai traitement est dans le Job.
    ───────────────────────────────────────────────────── */
    private function _parseExcel(string $path): array
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

                $dateAdh = $ws->getCell("F{$r}")->getFormattedValue();
                $mobile  = $ws->getCell("G{$r}")->getValue();

                $moisPaiements = [];
                for ($col = 8; $col <= 19; $col++) {
                    $cellAddr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $r;
                    $val      = $ws->getCell($cellAddr)->getCalculatedValue();
                    if ($val !== null && $val !== '' && trim((string) $val) !== '') {
                        $num = is_numeric($val) ? (int) round((float) $val) : 0;
                        if ($num > 0) $moisPaiements[$col - 7] = $num;
                    }
                }

                $engNum = ($engagement && is_numeric($engagement)) ? (int) round((float) $engagement) : 0;

                $dateAdhParsed = null;
                if ($dateAdh) {
                    try { $dateAdhParsed = Carbon::parse($dateAdh)->toDateString(); } catch (\Throwable) {}
                }

                $mobilePropre = $this->_nettoyerMobile($mobile);
                [$nom, $prenom] = $this->_splitNomPrenom(trim((string) $nomComplet));

                $rows[] = [
                    'matricule'    => $mle ? (string) $mle : null,
                    'nom'          => $nom,
                    'prenom'       => $prenom,
                    'engagement'   => $engNum,
                    'adresse'      => null,
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

    private function _buildImportStats(array $rows): array
    {
        $totalMembres = count($rows);
        $nouveaux     = 0;
        $existants    = 0;
        $cotPayees    = 0;
        $cotRetard    = 0;
        $totalMontant = 0;
        $parFeuille   = [];

        foreach ($rows as $row) {
            $existe = false;
            if ($row['matricule']) $existe = Customer::where('matricule', $row['matricule'])->exists();
            if (! $existe && $row['mobile']) $existe = Customer::where('phone', $row['mobile'])->exists();
            $existe ? $existants++ : $nouveaux++;

            $nb = count($row['mois_payes']);
            $cotPayees += $nb;
            foreach ($row['mois_payes'] as $m) $totalMontant += $m;

            $debut = Carbon::parse($row['date_adhesion'] ?? '2020-01-01')->startOfMonth();
            $cotRetard += max(0, $debut->diffInMonths(Carbon::now()->startOfMonth()) + 1 - $nb);

            $parFeuille[$row['tc_libelle']] = ($parFeuille[$row['tc_libelle']] ?? 0) + 1;
        }

        return [
            'total_membres'      => $totalMembres,
            'nouveaux'           => $nouveaux,
            'existants'          => $existants,
            'cotisations_payees' => $cotPayees,
            'cotisations_retard' => $cotRetard,
            'total_montant'      => $totalMontant,
            'par_feuille'        => $parFeuille,
        ];
    }

    private function _splitNomPrenom(string $s): array
    {
        $parts = explode(' ', $s, 2);
        if (count($parts) === 1) return [$parts[0], $parts[0]];
        if ($parts[0] === strtoupper($parts[0]) && strlen($parts[0]) > 1) {
            return [strtoupper($parts[0]), $parts[1]];
        }
        $words = explode(' ', $s);
        $prenom = array_pop($words);
        return [strtoupper(implode(' ', $words)), $prenom];
    }

    private function _nettoyerMobile($raw): ?string
    {
        if (! $raw) return null;
        $str = explode('/', (string) $raw)[0];
        $str = preg_replace('/[^\d]/', '', $str);
        if (strlen($str) > 10 && str_starts_with($str, '225')) $str = substr($str, 3);
        $str = substr($str, -10);
        return strlen($str) >= 8 ? $str : null;
    }

    /* ═══════════════════════════════════════════════════════
       RESET / HELPERS COMMUNS

    ═══════════════════════════════════════════════════════ */
    protected function resetForm(): void
    {
        $this->editId           = null;
        $this->customerId       = null;
        $this->searchFidele     = '';
        $this->typeCotisationId = null;
        $this->mois             = now()->month;
        $this->annee            = now()->year;
        $this->montantPaye      = null;
        $this->modePaiement     = '';
        $this->reference        = '';
        $this->alerteEngagement = false;
        $this->resetErrorBag();
    }

    private function _createPaiement(Cotisation $cot, int $montant): void
    {
        \App\Models\Paiement::create([
            'customer_id'        => $cot->customer_id,
            'type_cotisation_id' => $cot->type_cotisation_id,
            'cotisation_id'      => $cot->id,
            'montant'            => $montant,
            'mode_paiement'      => $this->modePaiement,
            'reference'          => $this->reference ?: null,
            'statut'             => 'en_attente',
            'date_paiement'      => now(),
        ]);
    }

    private function _createPaiementEtTransaction(Cotisation $cot, int $montant): void
    {
        $paiement = \App\Models\Paiement::create([
            'customer_id'        => $cot->customer_id,
            'type_cotisation_id' => $cot->type_cotisation_id,
            'cotisation_id'      => $cot->id,
            'montant'            => $montant,
            'mode_paiement'      => 'regul',
            'reference'          => 'Régularisation admin',
            'statut'             => 'success',
            'date_paiement'      => now(),
        ]);
        \App\Models\Transaction::create([
            'type'=>'entree','source'=>'paiement','source_id'=>$paiement->id,
            'status'=>'success','montant'=>$montant,
            'libelle'=>"Régularisation – {$cot->typeCotisation->libelle}",
            'date_transaction'=>now(),
        ]);
    }

    private function _finishSave(): void
    {
        $this->closeModal_after_edit('modalCreateCotisation');
        $this->resetForm();
        $this->send_event_at_toast('Cotisation enregistrée avec succès', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       DONNÉES VUE
    ═══════════════════════════════════════════════════════ */
    public function with(): array
    {
        $cotisations = Cotisation::with(['customer', 'typeCotisation', 'historiques'])
            ->when($this->tabStatut !== 'tous', fn($q) => $q->where('statut', $this->tabStatut))
            ->when($this->filterType !== 'tous', fn($q) => $q->where('type_cotisation_id', $this->filterType))
            ->when($this->filterMois !== 'tous', fn($q) => $q->where('mois', $this->filterMois))
            ->when($this->filterMode !== 'tous', fn($q) => $this->filterMode === 'nd' ? $q->whereNull('mode_paiement') : $q->where('mode_paiement', $this->filterMode))
            ->when($this->search, fn($q) =>
                $q->where(fn($q) =>
                    $q->whereHas('customer', fn($q) => $q->where('prenom', 'like', "%{$this->search}%")->orWhere('nom', 'like', "%{$this->search}%"))
                      ->orWhereHas('typeCotisation', fn($q) => $q->where('libelle', 'like', "%{$this->search}%"))
                )
            )
            ->latest()->paginate(15);

        $kpis = [
            'total'   => Cotisation::count(),
            'ajour'   => Cotisation::where('statut', 'a_jour')->count(),
            'partiel' => Cotisation::where('statut', 'partiel')->count(),
            'retard'  => Cotisation::where('statut', 'en_retard')->count(),
            'montant' => \App\Models\Paiement::where('statut', 'success')->sum('montant'),
        ];

        $base = Cotisation::query()
            ->when($this->filterType !== 'tous', fn($q) => $q->where('type_cotisation_id', $this->filterType))
            ->when($this->filterMois !== 'tous', fn($q) => $q->where('mois', $this->filterMois))
            ->when($this->filterMode !== 'tous', fn($q) => $this->filterMode === 'nd' ? $q->whereNull('mode_paiement') : $q->where('mode_paiement', $this->filterMode));

        $tabCounts = [
            'tous'      => (clone $base)->count(),
            'a_jour'    => (clone $base)->where('statut', 'a_jour')->count(),
            'partiel'   => (clone $base)->where('statut', 'partiel')->count(),
            'en_retard' => (clone $base)->where('statut', 'en_retard')->count(),
        ];

        $detailCotisation = $this->detailId
            ? Cotisation::with(['customer', 'typeCotisation', 'historiques', 'paiements'])->find($this->detailId)
            : null;

        $typesCotisation = TypeCotisation::where('status', 'actif')->orderBy('libelle')->get();

        $fidelesSuggeres = $this->searchFidele
            ? Customer::where(fn($q) => $q->where('prenom', 'like', "%{$this->searchFidele}%")->orWhere('nom', 'like', "%{$this->searchFidele}%")->orWhere('phone', 'like', "%{$this->searchFidele}%"))->limit(8)->get()
            : collect();

        $fideleCourant = $this->customerId
            ? Customer::with('typeCotisationMensuel')->find($this->customerId)
            : null;

        $previewReport = $this->_buildPreviewReport($fideleCourant);

        return compact(
            'cotisations', 'kpis', 'tabCounts',
            'detailCotisation', 'typesCotisation',
            'fidelesSuggeres', 'fideleCourant',
            'previewReport'
        );
    }

    private function _buildPreviewReport(?Customer $customer): array
    {
        if (! $customer || ! $this->typeCotisationId || ! $this->montantPaye) return [];
        $tc = TypeCotisation::find($this->typeCotisationId);
        if (! $tc || $tc->type !== 'mensuel' || ! $customer->montant_engagement) return [];

        $engagement = $customer->montant_engagement;
        $budget     = $this->montantPaye;
        $rows       = [];

        $derniere = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $this->typeCotisationId)
            ->orderByDesc('annee')->orderByDesc('mois')->first();

        $prochain = $derniere
            ? Carbon::create($derniere->annee, $derniere->mois)->addMonth()
            : Carbon::create($this->annee ?? now()->year, $this->mois ?? now()->month);

        while ($budget > 0) {
            $montantCe = min($budget, $engagement);
            $restantCe = $engagement - $montantCe;
            $budget   -= $montantCe;
            $rows[] = ['label' => $prochain->translatedFormat('F Y'), 'montant' => $montantCe, 'statut' => $restantCe > 0 ? 'partiel' : 'en_retard', 'tc' => $tc->libelle];
            if ($restantCe > 0) break;
            $prochain->addMonth();
        }
        return $rows;
    }
};
?>