<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\DemandeRemboursement;
use App\Models\Paiement;
use App\Models\Cotisation;
use App\Models\Transaction;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    use WithPagination, UtilsSweetAlert;

    /* ── Filtres ── */
    public string $search       = '';
    public string $filterStatut = 'tous';
    public string $filterMois   = 'tous';

    /* ── Modal détail ── */
    public ?int $detailId = null;

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatut(): void { $this->resetPage(); }
    public function updatedFilterMois(): void   { $this->resetPage(); }

    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->launch_modal('modalDetailDemande');
    }

    /* ═══════════════════════════════════════════════════════
       VALIDER UNE DEMANDE DE REMBOURSEMENT
       → Paiement reste 'annule'
       → Transaction sortie créée
       → Cotisations liées restent 'en_retard' (déjà fait à l'annulation)
       → Demande → 'validee'
    ═══════════════════════════════════════════════════════ */
    public function confirmerValidation(int $id): void
    {
        $demande = DemandeRemboursement::with(['paiement', 'customer'])->findOrFail($id);

        $this->sweetAlert_confirm_options_with_button(
            $demande,
            'Valider le remboursement ?',
            'Un remboursement de ' . number_format($demande->montant, 0, ',', ' ') . ' FCFA sera enregistré pour ' . $demande->customer?->prenom . ' ' . $demande->customer?->nom . '.',
            'validerRemboursement',
            'question',
            'Oui, valider',
            'Annuler'
        );
    }

    #[On('validerRemboursement')]
    public function validerRemboursement(int $id): void
    {
        $demande = DemandeRemboursement::with(['paiement.cotisation.typeCotisation', 'customer'])
            ->findOrFail($id);

        if ($demande->statut !== 'en_attente') {
            $this->send_event_at_sweet_alert_not_timer('Action impossible', 'Cette demande a déjà été traitée.', 'warning');
            return;
        }

        DB::transaction(function () use ($demande) {

            /* Créer la Transaction de sortie */
            $paiement = $demande->paiement;
            $libelle  = $paiement?->cotisation?->typeCotisation
                ? "Remboursement — {$paiement->cotisation->typeCotisation->libelle} — {$demande->customer?->prenom} {$demande->customer?->nom}"
                : "Remboursement paiement #{$paiement?->id} — {$demande->customer?->prenom} {$demande->customer?->nom}";
                

            Transaction::create([
                'type'             => 'sortie',
                'source'           => 'paiement',
                'source_id'        => $paiement?->id,
                'status'           => 'success',
                'montant'          => $demande->montant,
                'libelle'          => $libelle,
                'date_transaction' => now(),
            ]);

            $paiement->update([
                'statut'   => 'annule',
                'metadata' => array_merge($paiement->metadata ?? [], [
                    'annule_at'                => now()->toDateTimeString(),
                    'remboursement_demande_id' => $demande->id,
                    'remboursement_by'         => auth()->id(),
                    'remboursement_at'         => now()->toDateTimeString(),
                ]),
            ]);

            /* MAJ Demande → validee */
            $demande->update([
                'statut'       => 'validee',
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);
        });

        $this->closeModal_after_edit('modalDetailDemande');
        $this->detailId = null;
        $this->send_event_at_toast('Remboursement validé. Transaction de sortie créée.', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       REJETER UNE DEMANDE
       → Demande → 'rejetee'
       → Paiement reste 'annule', cotisations restent 'en_retard'
    ═══════════════════════════════════════════════════════ */
    public function confirmerRejet(int $id): void
    {
        $demande = DemandeRemboursement::with(['customer'])->findOrFail($id);

        $this->sweetAlert_confirm_options_with_button(
            $demande,
            'Rejeter la demande ?',
            'La demande de remboursement de ' . $demande->customer?->prenom . ' ' . $demande->customer?->nom . ' sera rejetée définitivement.',
            'rejeterRemboursement',
            'warning',
            'Oui, rejeter',
            'Annuler'
        );
    }

    #[On('rejeterRemboursement')]
    public function rejeterRemboursement(int $id): void
    {
        $demande = DemandeRemboursement::findOrFail($id);

        if ($demande->statut !== 'en_attente') {
            $this->send_event_at_sweet_alert_not_timer('Action impossible', 'Cette demande a déjà été traitée.', 'warning');
            return;
        }

        $paiement = $demande->paiement;


        $demande->update([
            'statut'       => 'rejetee',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        $this->closeModal_after_edit('modalDetailDemande');
        $this->detailId = null;
        $this->send_event_at_toast('Demande rejetée.', 'success', 'top-end');
    }

    /* ── Données vue ── */
    public function with(): array
    {
        $demandes = DemandeRemboursement::with(['customer', 'paiement.cotisation.typeCotisation', 'createdBy', 'validatedBy'])
            ->when($this->search, fn($q) =>
                $q->whereHas('customer', fn($q) =>
                    $q->where('prenom', 'like', "%{$this->search}%")
                      ->orWhere('nom',   'like', "%{$this->search}%")
                      ->orWhere('phone', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterStatut !== 'tous', fn($q) => $q->where('statut', $this->filterStatut))
            ->when($this->filterMois   !== 'tous', fn($q) =>
                $q->whereMonth('created_at', $this->filterMois)
                  ->whereYear('created_at', now()->year)
            )
            ->latest()
            ->paginate(15);

        $kpis = [
            'total'     => DemandeRemboursement::count(),
            'attente'   => DemandeRemboursement::where('statut', 'en_attente')->count(),
            'validees'  => DemandeRemboursement::where('statut', 'validee')->count(),
            'rejetees'  => DemandeRemboursement::where('statut', 'rejetee')->count(),
            'montant'   => DemandeRemboursement::where('statut', 'validee')->sum('montant'),
        ];

        $base = DemandeRemboursement::query()
            ->when($this->filterMois !== 'tous', fn($q) =>
                $q->whereMonth('created_at', $this->filterMois)->whereYear('created_at', now()->year)
            );

        $tabCounts = [
            'tous'       => (clone $base)->count(),
            'en_attente' => (clone $base)->where('statut', 'en_attente')->count(),
            'validee'    => (clone $base)->where('statut', 'validee')->count(),
            'rejetee'    => (clone $base)->where('statut', 'rejetee')->count(),
        ];

        $detailDemande = $this->detailId
            ? DemandeRemboursement::with([
                'customer',
                'paiement.cotisation.typeCotisation',
                'createdBy',
                'validatedBy',
              ])->find($this->detailId)
            : null;

        return compact('demandes', 'kpis', 'tabCounts', 'detailDemande');
    }
};
?>