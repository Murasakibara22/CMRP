<?php

use Livewire\Component;
 
use App\Models\CoutEngagement;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;
use Livewire\WithPagination;

new class extends Component
{
    
    use WithPagination, UtilsSweetAlert;
 
    // ─── Recherche & Filtres ───────────────────────────────
    public string $search      = '';
    public string $filterStatut = 'tous';
    public string $filterMois  = '';
    public string $filterAnnee = '';
    public string $vue         = 'table'; // table | grille
 
    // ─── Formulaire ajout / édition ───────────────────────
    public ?int   $customerId      = null; // null = création, int = édition
    public int    $formStep        = 1;
    public string $prenom          = '';
    public string $nom             = '';
    public string $dialCode        = '+225';
    public string $telephone       = '';
    public string $adresse         = '';
    public string $dateAdhesion    = '';
    public ?int   $montantEngagement = null;
 
    // ─── Modal détail ─────────────────────────────────────
    public ?int $detailCustomerId = null;
 
    // ─── KPIs (computed) ──────────────────────────────────
    public function getKpisProperty(): array
    {
        $total         = Customer::count();
        $sansEngagement = Customer::whereNull('montant_engagement')->count();
 
        $avecEngagement = Customer::whereNotNull('montant_engagement')->get();
 
        $ajour   = 0;
        $enRetard = 0;
 
        foreach ($avecEngagement as $c) {
            $statut = $c->statutGlobal();
            if ($statut === 'a_jour') {
                $ajour++;
            } else {
                $enRetard++;
            }
        }
 
        return compact('total', 'ajour', 'enRetard', 'sansEngagement');
    }
 
    // ─── Customer sélectionné pour le détail ──────────────
    public function getDetailCustomerProperty(): ?Customer
    {
        if (! $this->detailCustomerId) return null;
 
        return Customer::with([
            'cotisations.typeCotisation',
            'paiements',
            'documents',
        ])->find($this->detailCustomerId);
    }
 
    // ─── Liste paginée ────────────────────────────────────
    public function with()
    {
        $mois  = $this->filterMois  ?: now()->month;
        $annee = $this->filterAnnee ?: now()->year;
 
        $query = Customer::with(['cotisations' => function ($q) use ($mois, $annee) {
            $q->whereHas('typeCotisation', fn($q) => $q->where('type', 'mensuel'))
              ->where('mois', $mois)
              ->where('annee', $annee);
        }])
        ->when($this->search, function ($q) {
            $q->where(function ($q) {
                $q->where('nom', 'like', "%{$this->search}%")
                  ->orWhere('prenom', 'like', "%{$this->search}%")
                  ->orWhere('telephone', 'like', "%{$this->search}%");
            });
        })
        ->when($this->filterStatut !== 'tous', function ($q) {
            match ($this->filterStatut) {
                'ajour'  => $q->whereHas('cotisations', fn($q) => $q->where('statut', 'a_jour')),
                'retard' => $q->whereHas('cotisations', fn($q) => $q->where('statut', 'en_retard')),
                'partiel'=> $q->whereHas('cotisations', fn($q) => $q->where('statut', 'partiel')),
                'libre'  => $q->whereNull('montant_engagement'),
                default  => null,
            };
        })
        ->latest();
 
        $customers      = $query->paginate(15);
        $coutEngagements = CoutEngagement::actif()->orderBy('montant')->get();
 
        return [
            
            'customers'      => $customers,
            'coutEngagements' => $coutEngagements,
            
        ];
    }
 
    // ─── Reset pagination quand filtre change ─────────────
    public function updatedSearch(): void      { $this->resetPage(); }
    public function updatedFilterStatut(): void { $this->resetPage(); }
 
    // ─── Ouvrir modal ajout ───────────────────────────────
    public function openAdd(): void
    {
        $this->resetForm();
        $this->customerId   = null;
        $this->dateAdhesion = now()->format('Y-m-d');
        $this->launch_modal('modalAddFidele');
    }
 
    // ─── Ouvrir modal édition ─────────────────────────────
    public function openEdit(int $id): void
    {
        $customer = Customer::findOrFail($id);
 
        $this->customerId       = $customer->id;
        $this->prenom           = $customer->prenom;
        $this->nom              = $customer->nom;
        $this->dialCode         = $customer->dial_code;
        $this->telephone        = $customer->telephone;
        $this->adresse          = $customer->adresse ?? '';
        $this->dateAdhesion     = $customer->date_adhesion->format('Y-m-d');
        $this->montantEngagement = $customer->montant_engagement;
        $this->formStep         = 1;
 
        $this->launch_modal('modalAddFidele');
    }
 
    // ─── Ouvrir modal détail ──────────────────────────────
    public function openDetail(int $id): void
    {
        $this->detailCustomerId = $id;
        $this->launch_modal('modalDetailFidele');
    }
 
    // ─── Navigation dans le formulaire ────────────────────
    public function nextStep(): void
    {
        if ($this->formStep === 1) {
            $this->validateStep1();
        }
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
            'prenom'      => 'required|string|min:2|max:100',
            'nom'         => 'required|string|min:2|max:100',
            'dialCode'    => 'required|string',
            'telephone'   => 'required|string|min:8|max:20',
            'dateAdhesion'=> 'required|date',
        ]);
    }
 
    // ─── Enregistrer (création ou édition) ────────────────
    public function save(): void
    {
        $this->validateStep1();
 
        $data = [
            'prenom'           => $this->prenom,
            'nom'              => $this->nom,
            'dial_code'        => $this->dialCode,
            'telephone'        => $this->telephone,
            'adresse'          => $this->adresse ?: null,
            'date_adhesion'    => $this->dateAdhesion,
            'montant_engagement' => $this->montantEngagement ?: null,
            'status'           => 'actif',
        ];
 
        if ($this->customerId) {
            // ── Édition
            $customer = Customer::findOrFail($this->customerId);
            $ancienEngagement = $customer->montant_engagement;
            $customer->update($data);
 
            // Si engagement nouvellement ajouté → créer la cotisation mensuelle courante
            if (! $ancienEngagement && $this->montantEngagement) {
                $this->creerCotisationMensuelle($customer);
            }
 
            $this->send_event_at_toast('Fidèle modifié avec succès', 'success', 'top-end');
        } else {
            // ── Création
            $customer = Customer::create($data);
 
            if ($this->montantEngagement) {
                $this->creerCotisationMensuelle($customer);
            }
 
            $this->send_event_at_toast('Fidèle ajouté avec succès', 'success', 'top-end');
        }
 
        $this->closeModal_after_edit('modalAddFidele');
        $this->resetForm();
    }
 
    // ─── Créer la cotisation mensuelle courante (UC1) ─────
    protected function creerCotisationMensuelle(Customer $customer): void
    {
        $typeMensuel = \App\Models\TypeCotisation::where('type', 'mensuel')
            ->where('is_required', true)
            ->where('status', 'actif')
            ->first();
 
        if (! $typeMensuel) return;
 
        $now = Carbon::now();
 
        Cotisation::firstOrCreate(
            [
                'customer_id'       => $customer->id,
                'type_cotisation_id'=> $typeMensuel->id,
                'mois'              => $now->month,
                'annee'             => $now->year,
            ],
            [
                'montant_du'      => $customer->montant_engagement,
                'montant_paye'    => 0,
                'montant_restant' => $customer->montant_engagement,
                'statut'          => 'en_retard',
            ]
        );
 
        // Log historique
        $cotisation = Cotisation::where('customer_id', $customer->id)
            ->where('type_cotisation_id', $typeMensuel->id)
            ->where('mois', $now->month)
            ->where('annee', $now->year)
            ->first();
 
        if ($cotisation) {
            \App\Models\HistoriqueCotisation::log($cotisation, 'creation', $customer->montant_engagement);
        }
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
 
    // ─── Suppression confirmée (event retour sweetAlert) ──
    #[\Livewire\Attributes\On('deleteConfirmed')]
    public function deleteConfirmed(int $id): void
    {
        $customer = Customer::find($id);
 
        if (! $customer) {
            $this->send_event_at_toast('Fidèle introuvable', 'error', 'top-end');
            return;
        }
 
        $customer->delete();
 
        // Fermer le modal détail s'il était ouvert
        if ($this->detailCustomerId === $id) {
            $this->detailCustomerId = null;
            $this->closeModal_after_edit('modalDetailFidele');
        }
 
        $this->send_event_at_toast("{$customer->prenom} {$customer->nom} supprimé", 'success', 'top-end');
    }
 
    // ─── Changer la vue table / grille ────────────────────
    public function setVue(string $vue): void
    {
        $this->vue = $vue;
    }
 
    // ─── Sélectionner un montant d'engagement ─────────────
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

};