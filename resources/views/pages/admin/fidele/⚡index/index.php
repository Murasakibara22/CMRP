<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\CoutEngagement;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;

new  class extends Component
{
    use WithPagination, UtilsSweetAlert;

    // ─── Filtres & Vue ────────────────────────────────────
    public string $search       = '';
    public string $filterStatut = 'tous';
    public string $filterMois   = '';
    public string $filterAnnee  = '';
    public string $vue          = 'table';

    // ─── Formulaire ajout / édition ───────────────────────
    public ?int   $customerId        = null;
    public int    $formStep          = 1;
    public string $prenom            = '';
    public string $nom               = '';
    public string $dialCode          = '+225';
    public string $telephone         = '';
    public string $adresse           = '';
    public string $dateAdhesion      = '';
    public ?int   $montantEngagement = null;

    // ─── Modal détail ─────────────────────────────────────
    public ?int $detailCustomerId = null;

    // ─── Reset pagination sur filtre ──────────────────────
    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatut(): void { $this->resetPage(); }

    // ─── Ouvrir modal ajout ───────────────────────────────
    public function openAdd(): void
    {
        $this->resetForm();
        $this->dateAdhesion = now()->format('Y-m-d');
        $this->launch_modal('modalAddFidele');
    }

    // ─── Ouvrir modal édition ─────────────────────────────
    public function openEdit(int $id): void
    {
        $customer = Customer::findOrFail($id);

        $this->customerId        = $customer->id;
        $this->prenom            = $customer->prenom;
        $this->nom               = $customer->nom;
        $this->dialCode          = $customer->dial_code;
        $this->telephone         = $customer->phone; // ← corrigé
        $this->adresse           = $customer->adresse ?? '';
        $this->dateAdhesion      = $customer->date_adhesion->format('Y-m-d');
        $this->montantEngagement = $customer->montant_engagement;
        $this->formStep          = 1;

        $this->launch_modal('modalAddFidele');
    }

    // ─── Ouvrir modal détail ──────────────────────────────
    public function openDetail(int $id): void
    {
        $this->detailCustomerId = $id;
        $this->launch_modal('modalDetailFidele');
    }

    // ─── Navigation formulaire ────────────────────────────
    public function nextStep(): void
    {
        $this->validateStep1();
        $this->formStep = 2;
    }

    public function prevStep(): void
    {
        $this->formStep = 1;
    }

    // ─── Validation step 1 ────────────────────────────────
    protected function validateStep1(): void
    {
        $this->validate([
            'prenom'       => 'required|string|min:2|max:100',
            'nom'          => 'required|string|min:2|max:100',
            'dialCode'     => 'required|string',
            'telephone'    => 'required|string|min:8|max:20',
            'dateAdhesion' => 'required|date',
        ]);
    }

    // ─── Enregistrer ─────────────────────────────────────
    public function save(): void
    {
        $this->validateStep1();

        $data = [
            'prenom'             => $this->prenom,
            'nom'                => $this->nom,
            'dial_code'          => $this->dialCode,
            'phone'          => $this->telephone, // ← corrigé
            'adresse'            => $this->adresse ?: null,
            'date_adhesion'      => $this->dateAdhesion,
            'montant_engagement' => $this->montantEngagement ?: null,
            'status'             => 'actif',
        ];

        if ($this->customerId) {
            $customer         = Customer::findOrFail($this->customerId);
            $ancienEngagement = $customer->montant_engagement;
            $customer->update($data);

            if (! $ancienEngagement && $this->montantEngagement) {
                $this->creerCotisationMensuelle($customer);
            }

            $this->send_event_at_toast('Fidèle modifié avec succès', 'success', 'top-end');
        } else {
            $customer = Customer::create($data);

            // Pas de cotisation auto à la création : l'engagement est optionnel.
            // La cotisation mensuelle sera créée si le fidèle souscrit au mensuel.

            $this->send_event_at_toast('Fidèle ajouté avec succès', 'success', 'top-end');
        }

        $this->closeModal_after_edit('modalAddFidele');
        $this->resetForm();
    }

    // ─── Créer la cotisation mensuelle du mois courant ────
    protected function creerCotisationMensuelle(Customer $customer): void
    {
        $typeMensuel = \App\Models\TypeCotisation::where('type', 'mensuel')
            ->where('is_required', true)
            ->where('status', 'actif')
            ->first();

        if (! $typeMensuel || ! $customer->montant_engagement) return;

        $now = Carbon::now();

        $cotisation = Cotisation::firstOrCreate(
            [
                'customer_id'        => $customer->id,
                'type_cotisation_id' => $typeMensuel->id,
                'mois'               => $now->month,
                'annee'              => $now->year,
            ],
            [
                'montant_du'      => $customer->montant_engagement,
                'montant_paye'    => 0,
                'montant_restant' => $customer->montant_engagement,
                'statut'          => 'en_retard',
            ]
        );

        \App\Models\HistoriqueCotisation::log($cotisation, 'creation', $customer->montant_engagement);
    }

    // ─── Confirmation suppression ─────────────────────────
    public function confirmDelete(int $id): void
    {
        $customer = Customer::findOrFail($id);

        $this->sweetAlert_confirm_options_with_button(
            $customer,
            'Supprimer ce fidèle ?',
            "La suppression de {$customer->prenom} {$customer->nom} est irréversible.",
            'deleteConfirmed',
            'warning',
            'Oui, supprimer',
            'Annuler'
        );
    }

    // ─── Suppression confirmée ────────────────────────────
    #[On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        $customer = Customer::find($id);

        if (! $customer) {
            $this->send_event_at_toast('Fidèle introuvable', 'error', 'top-end');
            return;
        }

        $nom = "{$customer->prenom} {$customer->nom}";
        $customer->delete();

        if ($this->detailCustomerId === $id) {
            $this->detailCustomerId = null;
            $this->closeModal_after_edit('modalDetailFidele');
        }

        $this->send_event_at_toast("{$nom} supprimé", 'success', 'top-end');
    }

    // ─── Changer vue ──────────────────────────────────────
    public function setVue(string $vue): void
    {
        $this->vue = $vue;
    }

    // ─── Sélectionner montant engagement ──────────────────
    public function selectEngagement(?int $montant): void
    {
        $this->montantEngagement = ($this->montantEngagement === $montant) ? null : $montant;
    }

    // ─── Reset formulaire ─────────────────────────────────
    protected function resetForm(): void
    {
        $this->customerId        = null;
        $this->formStep          = 1;
        $this->prenom            = '';
        $this->nom               = '';
        $this->dialCode          = '+225';
        $this->telephone         = '';
        $this->adresse           = '';
        $this->dateAdhesion      = '';
        $this->montantEngagement = null;
        $this->resetErrorBag();
    }

    // ─── Données pour la vue ──────────────────────────────
    public function with(): array
    {
        $mois  = $this->filterMois  ?: now()->month;
        $annee = $this->filterAnnee ?: now()->year;

        $customers = Customer::with(['cotisations' => function ($q) use ($mois, $annee) {
            $q->whereHas('typeCotisation', fn($q) => $q->where('type', 'mensuel'))
              ->where('mois', $mois)
              ->where('annee', $annee);
        }])
        ->when($this->search, fn($q) =>
            $q->where(fn($q) =>
                $q->where('nom', 'like', "%{$this->search}%")
                  ->orWhere('prenom', 'like', "%{$this->search}%")
                  ->orWhere('telephone', 'like', "%{$this->search}%")
            )
        )
        ->when($this->filterStatut !== 'tous', function ($q) {
            match ($this->filterStatut) {
                'ajour'   => $q->whereHas('cotisations', fn($q) => $q->where('statut', 'a_jour')),
                'retard'  => $q->whereHas('cotisations', fn($q) => $q->where('statut', 'en_retard')),
                'partiel' => $q->whereHas('cotisations', fn($q) => $q->where('statut', 'partiel')),
                'libre'   => $q->whereNull('montant_engagement'),
                default   => null,
            };
        })
        ->latest()
        ->paginate(15);

        // KPIs
        $total          = Customer::count();
        $sansEngagement = Customer::whereNull('montant_engagement')->count();
        $ajour          = 0;
        $enRetard       = 0;

        Customer::whereNotNull('montant_engagement')->get()->each(function ($c) use (&$ajour, &$enRetard) {
            $c->statutGlobal() === 'a_jour' ? $ajour++ : $enRetard++;
        });

        $kpis = compact('total', 'ajour', 'enRetard', 'sansEngagement');

        // Détail fidèle
        $detailCustomer = $this->detailCustomerId
            ? Customer::with(['cotisations.typeCotisation', 'paiements', 'documents'])
                ->find($this->detailCustomerId)
            : null;

        $coutEngagements = CoutEngagement::actif()->orderBy('montant')->get();

        return compact('customers', 'kpis', 'detailCustomer', 'coutEngagements');
    }
};
?>
