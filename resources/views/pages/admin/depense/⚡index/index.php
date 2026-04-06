<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Depense;
use App\Models\TypeDepense;
use App\Models\Transaction;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;

new  class extends Component
{
    use WithPagination, UtilsSweetAlert;

    /* ── Filtres ────────────────────────────────────────── */
    public string $search      = '';
    public string $filterType  = 'tous';
    public string $filterMois  = 'tous';

    /* ── Modal détail ───────────────────────────────────── */
    public ?int $detailId = null;

    /* ── Formulaire ─────────────────────────────────────── */
    public ?int    $editId         = null;
    public ?int    $typeDepenseId  = null;
    public string  $libelle        = '';
    public ?int    $montant        = null;
    public string  $dateDepense    = '';
    public string  $note           = '';

    public function updatedSearch(): void     { $this->resetPage(); }
    public function updatedFilterType(): void { $this->resetPage(); }
    public function updatedFilterMois(): void { $this->resetPage(); }

    /* ── Ouvrir détail ──────────────────────────────────── */
    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->launch_modal('modalDetailDepense');
    }

    /* ── Ouvrir ajout ───────────────────────────────────── */
    public function openAdd(): void
    {
        $this->resetForm();
        $this->dateDepense = now()->format('Y-m-d');
        $this->launch_modal('modalFormDepense');
    }

    /* ── Ouvrir modification ────────────────────────────── */
    public function openEdit(int $id): void
    {
        $dep = Depense::findOrFail($id);
        $this->resetForm();
        $this->editId        = $id;
        $this->typeDepenseId = $dep->type_depense_id;
        $this->libelle       = $dep->libelle ?? '';
        $this->montant       = $dep->montant;
        $this->dateDepense   = $dep->date_depense->format('Y-m-d');
        $this->note          = $dep->note ?? '';
        $this->launch_modal('modalFormDepense');
    }

    /* ── Save ───────────────────────────────────────────── */
    public function save(): void
    {
        $this->validate([
            'typeDepenseId' => 'required|integer|exists:type_depenses,id',
            'montant'       => 'required|integer|min:1',
            'dateDepense'   => 'required|date',
        ]);

        $data = [
            'type_depense_id' => $this->typeDepenseId,
            'libelle'         => $this->libelle ?: null,
            'montant'         => $this->montant,
            'date_depense'    => $this->dateDepense,
            'note'            => $this->note ?: null,
        ];

        if ($this->editId) {
            $dep = Depense::findOrFail($this->editId);
            $dep->update($data);

            // Mettre à jour la transaction liée si elle existe
            Transaction::where('source', 'depense')->where('source_id', $dep->id)
                ->update([
                    'montant'          => $this->montant,
                    'date_transaction' => $this->dateDepense,
                    'libelle'          => $this->_libelleTx($dep),
                ]);

            $msg = 'Dépense modifiée avec succès';
        } else {
            $dep = Depense::create($data);

            // Créer la transaction de sortie
            Transaction::create([
                'type'             => 'sortie',
                'source'           => 'depense',
                'source_id'        => $dep->id,
                'montant'          => $dep->montant,
                'libelle'          => $this->_libelleTx($dep),
                'date_transaction' => Carbon::parse($dep->date_depense),
            ]);

            $msg = 'Dépense enregistrée avec succès';
        }

        $this->closeModal_after_edit('modalFormDepense');
        $this->resetForm();
        $this->send_event_at_toast($msg, 'success', 'top-end');
    }

    private function _libelleTx(Depense $dep): string
    {
        $type = $dep->typeDepense?->libelle ?? 'Dépense';
        return $dep->libelle ? "{$type} – {$dep->libelle}" : $type;
    }

    /* ── Supprimer ──────────────────────────────────────── */
    public function confirmDelete(int $id): void
    {
        $dep = Depense::findOrFail($id);
        $this->sweetAlert_confirm_options_with_button(
            $dep,
            'Supprimer cette dépense ?',
            'La transaction associée sera également supprimée. Cette action est irréversible.',
            'deleteConfirmed', 'warning', 'Oui, supprimer', 'Annuler'
        );
    }

    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        $dep = Depense::find($id);
        if (! $dep) return;

        // Supprimer la transaction liée
        Transaction::where('source', 'depense')->where('source_id', $id)->delete();
        $dep->delete();

        if ($this->detailId === $id) {
            $this->detailId = null;
            $this->closeModal_after_edit('modalDetailDepense');
        }

        $this->send_event_at_toast('Dépense supprimée', 'success', 'top-end');
    }

    protected function resetForm(): void
    {
        $this->editId        = null;
        $this->typeDepenseId = null;
        $this->libelle       = '';
        $this->montant       = null;
        $this->dateDepense   = now()->format('Y-m-d');
        $this->note          = '';
        $this->resetErrorBag();
    }

    public function with(): array
    {
        $depenses = Depense::with('typeDepense')
            ->when($this->search, fn($q) =>
                $q->where('libelle', 'like', "%{$this->search}%")
                  ->orWhereHas('typeDepense', fn($q) => $q->where('libelle', 'like', "%{$this->search}%"))
            )
            ->when($this->filterType !== 'tous', fn($q) => $q->where('type_depense_id', $this->filterType))
            ->when($this->filterMois !== 'tous', fn($q) =>
                $q->whereMonth('date_depense', $this->filterMois)
                  ->whereYear('date_depense', now()->year)
            )
            ->orderByDesc('date_depense')
            ->paginate(15);

        $kpis = [
            'total'       => Depense::count(),
            'ce_mois'     => Depense::whereMonth('date_depense', now()->month)->whereYear('date_depense', now()->year)->count(),
            'montant_total' => Depense::sum('montant'),
            'montant_mois'  => Depense::whereMonth('date_depense', now()->month)->whereYear('date_depense', now()->year)->sum('montant'),
        ];

        $typesDepense = TypeDepense::where('status', 'actif')->orderBy('libelle')->get();

        $detailDepense = $this->detailId
            ? Depense::with('typeDepense')->find($this->detailId)
            : null;

        // Graphe par mois (6 derniers mois)
        $graphData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mois = now()->subMonths($i);
            $graphData[] = [
                'label'   => $mois->translatedFormat('M'),
                'montant' => Depense::whereMonth('date_depense', $mois->month)
                                ->whereYear('date_depense', $mois->year)->sum('montant'),
            ];
        }

        return compact('depenses', 'kpis', 'typesDepense', 'detailDepense', 'graphData');
    }
};
?>
