<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\MessageGroupe;
use App\Models\MessageGroupeCustomer;
use App\Models\Customer;
use App\Traits\UtilsSweetAlert;

new  class extends Component
{
    use WithPagination;
    use UtilsSweetAlert;

    /* ── Formulaire ajout ───────────────────────────────── */
    public string $titre          = '';
    public string $message        = '';
    public string $canal          = 'sms';
    public bool   $tousLesCustomers = false;
    public array  $customerIds    = [];   // IDs sélectionnés via select2
    public string $envoyerLe      = '';  // datetime string

    public string $errorTitre     = '';
    public string $errorMessage   = '';
    public string $errorDest      = '';

    /* ── Recherche liste ────────────────────────────────── */
    public string $search       = '';
    public string $filterCanal  = 'tous';
    public string $filterStatut = 'tous';

    /* ── Modal détail ───────────────────────────────────── */
    public ?int $detailId = null;

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFilterCanal(): void  { $this->resetPage(); }
    public function updatingFilterStatut(): void { $this->resetPage(); }

    public function updatedTousLesCustomers(): void
    {
        if ($this->tousLesCustomers) {
            $this->customerIds = [];
        }
    }

    /* ── Ouvrir / fermer modal ajout ────────────────────── */
    public function openAdd(): void
    {
        $this->titre            = '';
        $this->message          = '';
        $this->canal            = 'sms';
        $this->tousLesCustomers = false;
        $this->customerIds      = [];
        $this->envoyerLe        = '';
        $this->errorTitre       = '';
        $this->errorMessage     = '';
        $this->errorDest        = '';
        $this->launch_modal('modalAddMessage');
    }

    public function closeAdd(): void
    {
        $this->closeModal_after_edit('modalAddMessage');
    }

    /* ── Ouvrir modal détail ────────────────────────────── */
    public function openDetail(int $id): void
    {
        $this->detailId = $id;
        $this->launch_modal('modalDetailMessage');
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
        $this->closeModal_after_edit('modalDetailMessage');
    }

    /* ── Envoyer / planifier ────────────────────────────── */
    public function envoyer(): void
    {
        $this->errorTitre   = '';
        $this->errorMessage = '';
        $this->errorDest    = '';

        if (! trim($this->titre))   { $this->errorTitre   = 'Le titre est obligatoire.'; }
        if (! trim($this->message)) { $this->errorMessage  = 'Le message est obligatoire.'; }
        if (! $this->tousLesCustomers && empty($this->customerIds)) {
            $this->errorDest = 'Sélectionnez au moins un destinataire ou cochez "Tous".';
        }
        if ($this->errorTitre || $this->errorMessage || $this->errorDest) return;

        /* Résoudre les destinataires */
        $ids = $this->tousLesCustomers
            ? Customer::where('status', 'actif')->pluck('id')->toArray()
            : $this->customerIds;

        $msg = MessageGroupe::create([
            'user_id'              => auth()->id(),
            'titre'                => trim($this->titre),
            'message'              => trim($this->message),
            'canal'                => $this->canal,
            'tous_les_customers'   => $this->tousLesCustomers,
            'envoyer_le'           => $this->envoyerLe ?: null,
            'statut'               => $this->envoyerLe ? 'planifie' : 'en_cours',
            'nb_destinataires'     => count($ids),
        ]);

        /* Créer les lignes pivot */
        $rows = collect($ids)->map(fn($cid) => [
            'message_groupe_id' => $msg->id,
            'customer_id'       => $cid,
            'statut'            => 'en_attente',
            'created_at'        => now(),
            'updated_at'        => now(),
        ])->toArray();

        MessageGroupeCustomer::insert($rows);

        /* Si immédiat → simuler envoi (brancher SmsService / Mailable ici) */
        if (! $this->envoyerLe) {
            $msg->update(['statut' => 'envoye', 'nb_envoyes' => count($ids)]);
            MessageGroupeCustomer::where('message_groupe_id', $msg->id)
                ->update(['statut' => 'envoye', 'envoye_le' => now()]);
        }

        $this->closeModal_after_edit('modalAddMessage');
        $this->send_event_at_toast(
            $this->envoyerLe ? 'Message planifié !' : 'Message envoyé !',
            'success', 'top-end'
        );
    }

    /* ── Supprimer ──────────────────────────────────────── */
    public function confirmDelete(int $id): void
    {
        $msg = MessageGroupe::find($id);
        if ($msg) {
            $this->sweetAlert_confirm_options_with_button(
                $msg, 'Supprimer ce message ?',
                'Les statistiques d\'envoi seront perdues.',
                'doDelete', 'warning', 'Supprimer', 'Annuler'
            );
        }
    }

    #[\Livewire\Attributes\On('doDelete')]
    public function doDelete(int $id): void
    {
        MessageGroupe::find($id)?->delete();
        $this->send_event_at_toast('Message supprimé.', 'success', 'top-end');
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $query = MessageGroupe::with('user')
            ->when($this->search, fn($q) => $q->where('titre', 'like', '%'.$this->search.'%'))
            ->when($this->filterCanal  !== 'tous', fn($q) => $q->where('canal', $this->filterCanal))
            ->when($this->filterStatut !== 'tous', fn($q) => $q->where('statut', $this->filterStatut))
            ->orderByDesc('created_at');

        $messages = $query->paginate(15);

        $kpis = [
            'total'     => MessageGroupe::count(),
            'planifie'  => MessageGroupe::where('statut', 'planifie')->count(),
            'envoye'    => MessageGroupe::where('statut', 'envoye')->count(),
            'echec'     => MessageGroupe::where('statut', 'echec')->count(),
        ];

        $customers = Customer::where('status', 'actif')
            ->orderBy('nom')
            ->get(['id', 'nom', 'prenom', 'phone', 'dial_code']);

        $detailMessage = $this->detailId
            ? MessageGroupe::with(['user', 'destinataires.customer'])->find($this->detailId)
            : null;

        return compact('messages', 'kpis', 'customers', 'detailMessage');
    }
};
?>
