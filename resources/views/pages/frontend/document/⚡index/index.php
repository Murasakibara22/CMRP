<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\DocumentCustomer;
use App\Traits\UtilsSweetAlert;

new #[Layout('layouts.app-frontend')] class extends Component
{
    use WithFileUploads;
    use UtilsSweetAlert;

    /* ── Upload ─────────────────────────────────────────── */
    public $fichier      = null;
    public string $typeDoc    = '';
    public string $errorFichier = '';
    public string $errorType    = '';

    /* ── Modal détail ───────────────────────────────────── */
    public ?int $detailId = null;

    /* ── Types de documents acceptés ───────────────────── */
    public array $typesDoc = [
        'cni_recto'            => "Carte Nationale d'Identité recto",
        'cni_verso'            => "Carte Nationale d'Identité verso",
        'passeport'      => 'Passeport',
        'residence'      => 'Justificatif de résidence',
        'photo'          => 'Photo d\'identité',
        'autre'          => 'Autre document',
    ];

    /* ── Ouvrir / fermer modal upload ───────────────────── */
    public function openAdd(): void
    {
        $this->fichier      = null;
        $this->typeDoc      = '';
        $this->errorFichier = '';
        $this->errorType    = '';
        $this->dispatch('OpenAddDoc');
    }

    public function closeAdd(): void
    {
        $this->dispatch('closeAddDoc');
    }

    /* ── Ouvrir / fermer modal détail ───────────────────── */
    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->dispatch('OpenDetailDoc');
    }

    public function closeDetail(): void
    {
        $this->dispatch('closeDetailDoc');
        $this->detailId = null;
    }

    /* ── Soumettre document ─────────────────────────────── */
    public function saveDoc(): void
    {
        $this->errorFichier = '';
        $this->errorType    = '';

        if (! $this->typeDoc)   { $this->errorType    = 'Veuillez choisir un type de document.'; }
        if (! $this->fichier)   { $this->errorFichier = 'Veuillez sélectionner un fichier.'; }
        if ($this->errorType || $this->errorFichier) return;

        $customerId = auth('customer')->user()->id;
        $path = $this->fichier->store("documents/customer/{$customerId}", 'public');

        DocumentCustomer::create([
            'customer_id' => $customerId,
            'type'        => $this->typeDoc,
            'nom'         => $this->typesDoc[$this->typeDoc] ?? $this->typeDoc,
            'chemin'      => $path,
            'extension'   => $this->fichier->getClientOriginalExtension(),
            'taille'      => $this->fichier->getSize(),
            'status'      => 'en_attente',
        ]);

        $this->fichier  = null;
        $this->typeDoc  = '';
        $this->dispatch('closeAddDoc');
        $this->send_event_at_toast('Document soumis avec succès !', 'success', 'top-end');
    }

    /* ── Supprimer document ─────────────────────────────── */
    public function deleteDoc(int $id): void
    {
        $doc = DocumentCustomer::where('customer_id', auth('customer')->user()->id)->find($id);
        if ($doc) {
            \Storage::disk('public')->delete($doc->chemin);
            $doc->delete();
            $this->send_event_at_toast('Document supprimé.', 'success', 'top-end');
        }
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $customerId = auth('customer')->user()->id;

        $documents = DocumentCustomer::where('customer_id', $customerId)
            ->orderByDesc('created_at')->get();

        $stats = [
            'total'     => $documents->count(),
            'valide'    => $documents->where('status', 'valide')->count(),
            'attente'   => $documents->where('status', 'en_attente')->count(),
            'rejete'    => $documents->where('status', 'rejete')->count(),
        ];

        $detailDoc = $this->detailId
            ? DocumentCustomer::where('customer_id', $customerId)->find($this->detailId)
            : null;

        return compact('documents', 'stats', 'detailDoc');
    }
};
?>