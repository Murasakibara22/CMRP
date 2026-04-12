<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\Paiement;
use App\Traits\UtilsSweetAlert;

new #[Layout('layouts.app-frontend')] class extends Component
{
    use UtilsSweetAlert;

    /* ── Formulaire édition ─────────────────────────────── */
    public string $nom     = '';
    public string $prenom  = '';
    public string $adresse = '';
    public string $phone   = '';

    public string $errorNom    = '';
    public string $errorPrenom = '';

    public function mount(): void
    {
        $c = auth('customer')->user();
        $this->nom     = $c->nom;
        $this->prenom  = $c->prenom;
        $this->adresse = $c->adresse ?? '';
        $this->phone   = $c->phone   ?? '';
    }

    /* ── Modaux ─────────────────────────────────────────── */
    public function openEdit(): void
    {
        $c = auth('customer')->user();
        $this->nom     = $c->nom;
        $this->prenom  = $c->prenom;
        $this->adresse = $c->adresse ?? '';
        $this->phone   = $c->phone   ?? '';
        $this->errorNom    = '';
        $this->errorPrenom = '';
        $this->dispatch('OpenEditModal');
    }

    public function closeEdit(): void
    {
        $this->dispatch('closeEditModal');
    }

    public function openPhoto(): void
    {
        $this->dispatch('OpenPhotoModal');
    }

    public function closePhoto(): void
    {
        $this->dispatch('closePhotoModal');
    }

    /* ── Sauvegarder ────────────────────────────────────── */
    public function saveEdit(): void
    {
        $this->errorNom    = '';
        $this->errorPrenom = '';

        if (! trim($this->nom))    { $this->errorNom    = 'Le nom est obligatoire.'; }
        if (! trim($this->prenom)) { $this->errorPrenom = 'Le prénom est obligatoire.'; }
        if ($this->errorNom || $this->errorPrenom) return;

        Customer::find(auth('customer')->user()->id)->update([
            'nom'     => strtoupper(trim($this->nom)),
            'prenom'  => ucwords(strtolower(trim($this->prenom))),
            'adresse' => trim($this->adresse) ?: null,
            'phone'   => trim($this->phone)   ?: auth('customer')->user()->phone,
        ]);

        $this->dispatch('closeEditModal');
        $this->send_event_at_toast('Informations mises à jour !', 'success', 'top-end');
    }

    /* ── Déconnexion ────────────────────────────────────── */
    public function deconnexion(): void
    {
        auth('customer')->logout();
        $this->redirect(route('login-user'));
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $customer = Customer::find(auth('customer')->user()->id);

        $totalCotise = Paiement::where('customer_id', $customer->id)
            ->where('statut', 'success')->sum('montant');

        $totalDu = Cotisation::where('customer_id', $customer->id)
            ->whereIn('statut', ['en_retard', 'partiel'])->sum('montant_restant');

        $nbPaiements  = Paiement::where('customer_id', $customer->id)->count();
        $moisRetard   = Cotisation::where('customer_id', $customer->id)->where('statut', 'en_retard')->count();
        $nbDocuments  = $customer->documents()->count();
        $nbReclammationsEnCours = $customer->reclammation()
            ->whereIn('status', ['ouverte', 'en_cours'])->count();

        $initiales = strtoupper(
            substr($customer->prenom, 0, 1) . substr($customer->nom, 0, 1)
        );

        return compact(
            'customer', 'totalCotise', 'totalDu',
            'nbPaiements', 'moisRetard', 'nbDocuments',
            'nbReclammationsEnCours', 'initiales'
        );
    }
};
?>