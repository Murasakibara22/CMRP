<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\Paiement;
use App\Models\TypeCotisation;
use App\Models\CoutEngagement;
use App\Models\HistoriqueCotisation;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;

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

    /*
     * Cotisation mensuelle — modification depuis le profil.
     *
     * Flux :
     * 1. L'utilisateur ouvre le modal → on pré-remplit
     *    typeCotisationMensuelId et montantEngagement avec
     *    les valeurs actuelles.
     * 2. S'il change de type (type différent de l'actuel) →
     *    showConfirmChangementType = true → on lui demande
     *    de confirmer et de saisir le nouveau montant.
     * 3. saveEdit() vérifie, MAJ customer, crée cotisation
     *    du mois si nouveau type ou si premier type.
     */
    public ?int   $typeCotisationMensuelId   = null;
    public ?int   $montantEngagement         = null;
    public bool   $showConfirmChangementType = false;
    public string $confirmChangementMessage  = '';
    public ?int   $nouvelEngagement          = null;
    public string $errorEngagement           = '';

    public function mount(): void
    {
        $c = auth('customer')->user();
        $this->nom                    = $c->nom;
        $this->prenom                 = $c->prenom;
        $this->adresse                = $c->adresse ?? '';
        $this->phone                  = $c->phone   ?? '';
        $this->typeCotisationMensuelId = $c->type_cotisation_mensuel_id;
        $this->montantEngagement      = $c->montant_engagement;
    }

    /* ── Modaux ─────────────────────────────────────────── */
    public function openEdit(): void
    {
        $c = auth('customer')->user();
        $this->nom                    = $c->nom;
        $this->prenom                 = $c->prenom;
        $this->adresse                = $c->adresse ?? '';
        $this->phone                  = $c->phone   ?? '';
        $this->typeCotisationMensuelId = $c->type_cotisation_mensuel_id;
        $this->montantEngagement      = $c->montant_engagement;
        $this->errorNom               = '';
        $this->errorPrenom            = '';
        $this->errorEngagement        = '';
        $this->showConfirmChangementType = false;
        $this->confirmChangementMessage  = '';
        $this->nouvelEngagement          = null;

        $this->dispatch('OpenEditModal');
    }

    public function closeEdit(): void
    {
        $this->showConfirmChangementType = false;
        $this->errorEngagement           = '';
        $this->dispatch('closeEditModal');
    }

    public function openPhoto(): void { $this->dispatch('OpenPhotoModal'); }
    public function closePhoto(): void { $this->dispatch('closePhotoModal'); }

    /* ── Sélection type mensuel dans le modal ───────────── */
    public function selectTypeMensuel(?int $id): void
    {
        $this->typeCotisationMensuelId   = $id;
        $this->showConfirmChangementType = false;
        $this->confirmChangementMessage  = '';
        $this->nouvelEngagement          = null;
        $this->errorEngagement           = '';

        /* Si pas de type sélectionné → on réinitialise l'engagement */
        if (! $id) {
            $this->montantEngagement = auth('customer')->user()->montant_engagement;
        }
    }

    /* ── Sélection montant d'engagement ─────────────────── */
    public function selectEngagement(?int $montant): void
    {
        $this->montantEngagement = $montant;
        $this->errorEngagement   = '';
    }

    public function selectNouvelEngagement(?int $montant): void
    {
        $this->nouvelEngagement  = $montant;
        $this->errorEngagement   = '';
    }

    /* ── Sauvegarder ────────────────────────────────────── */
    public function saveEdit(): void
    {
        $this->errorNom       = '';
        $this->errorPrenom    = '';
        $this->errorEngagement = '';

        if (! trim($this->nom))    { $this->errorNom    = 'Le nom est obligatoire.'; }
        if (! trim($this->prenom)) { $this->errorPrenom = 'Le prénom est obligatoire.'; }
        if ($this->errorNom || $this->errorPrenom) return;

        $customer  = Customer::find(auth('customer')->user()->id);
        $ancienTypeId = $customer->type_cotisation_mensuel_id;

        $tcNouveau = $this->typeCotisationMensuelId
            ? TypeCotisation::find($this->typeCotisationMensuelId)
            : null;

        $estChangementDeType = $tcNouveau
            && $ancienTypeId
            && $ancienTypeId !== $this->typeCotisationMensuelId;

        $estPremierType = $tcNouveau && ! $ancienTypeId;

        /*
         * CAS 1 : Changement de type mensuel
         * → demander confirmation + nouveau montant d'engagement
         */
        if ($estChangementDeType && ! $this->showConfirmChangementType) {
            $ancienType = TypeCotisation::find($ancienTypeId);
            $minLabel   = $tcNouveau->montant_minimum
                ? ' (minimum ' . number_format($tcNouveau->montant_minimum, 0, ',', ' ') . ' FCFA/mois)'
                : '';

            $this->showConfirmChangementType = true;
            $this->nouvelEngagement          = null;
            $this->errorEngagement           = '';
            $this->confirmChangementMessage  =
                "Vous êtes actuellement en « {$ancienType?->libelle} » avec " .
                number_format($customer->montant_engagement ?? 0, 0, ',', ' ') .
                " FCFA/mois. Vous migrez vers « {$tcNouveau->libelle} »{$minLabel}. " .
                "Renseignez votre nouveau montant d'engagement mensuel.";
            return;
        }

        /*
         * CAS 2 : Confirmation changement de type — valider nouvelEngagement
         */
        if ($estChangementDeType && $this->showConfirmChangementType) {
            if (! $this->nouvelEngagement || $this->nouvelEngagement < 1) {
                $this->errorEngagement = 'Veuillez renseigner votre nouveau montant d\'engagement.';
                return;
            }
            if ($tcNouveau->montant_minimum && $this->nouvelEngagement < $tcNouveau->montant_minimum) {
                $this->errorEngagement =
                    "Le minimum pour « {$tcNouveau->libelle} » est " .
                    number_format($tcNouveau->montant_minimum, 0, ',', ' ') . " FCFA/mois.";
                return;
            }
            $this->montantEngagement = $this->nouvelEngagement;
        }

        /*
         * CAS 3 : Premier type mensuel — valider montantEngagement
         */
        if ($estPremierType) {
            if (! $this->montantEngagement || $this->montantEngagement < 1) {
                $this->errorEngagement = 'Veuillez renseigner votre montant d\'engagement mensuel.';
                return;
            }
            if ($tcNouveau->montant_minimum && $this->montantEngagement < $tcNouveau->montant_minimum) {
                $this->errorEngagement =
                    "Le minimum pour « {$tcNouveau->libelle} » est " .
                    number_format($tcNouveau->montant_minimum, 0, ',', ' ') . " FCFA/mois.";
                return;
            }
        }

        /* ── MAJ customer ────────────────────────────────── */
        $customer->update([
            'nom'                        => strtoupper(trim($this->nom)),
            'prenom'                     => ucwords(strtolower(trim($this->prenom))),
            'adresse'                    => trim($this->adresse) ?: null,
            'phone'                      => trim($this->phone) ?: $customer->phone,
            'type_cotisation_mensuel_id' => $tcNouveau?->id,
            'montant_engagement'         => $tcNouveau ? $this->montantEngagement : null,
        ]);

        /*
         * Créer une cotisation pour le mois en cours si :
         * - Premier type mensuel (pas de cotisation ce mois)
         * - Changement de type (pas de cotisation du nouveau type ce mois)
         */
        if ($tcNouveau && ($estPremierType || $estChangementDeType)) {
            $existeDejaCeMois = Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $tcNouveau->id)
                ->where('mois',  now()->month)
                ->where('annee', now()->year)
                ->exists();

            if (! $existeDejaCeMois) {
                $cot = Cotisation::create([
                    'customer_id'        => $customer->id,
                    'type_cotisation_id' => $tcNouveau->id,
                    'mois'               => now()->month,
                    'annee'              => now()->year,
                    'montant_du'         => $this->montantEngagement,
                    'montant_paye'       => 0,
                    'montant_restant'    => $this->montantEngagement,
                    'statut'             => 'en_retard',
                    'mode_paiement'      => null,
                    'reference'          => null,
                    'validated_by'       => null,
                    'validated_at'       => null,
                ]);

                HistoriqueCotisation::log($cot, 'creation', $this->montantEngagement,
                    $estChangementDeType
                        ? 'Première cotisation suite au changement de type'
                        : 'Première cotisation mensuelle');
            }
        }

        $this->showConfirmChangementType = false;
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
        $customer = Customer::with('typeCotisationMensuel')
            ->find(auth('customer')->user()->id);

        $totalCotise = Paiement::where('customer_id', $customer->id)
            ->where('statut', 'success')->sum('montant');

        $totalDu = Cotisation::where('customer_id', $customer->id)
            ->whereIn('statut', ['en_retard', 'partiel'])->sum('montant_restant');

        $nbPaiements            = Paiement::where('customer_id', $customer->id)->count();
        $moisRetard             = Cotisation::where('customer_id', $customer->id)->where('statut', 'en_retard')->count();
        $nbDocuments            = $customer->documents()->count();
        $nbReclammationsEnCours = $customer->reclammation()
            ->whereIn('status', ['ouverte', 'en_cours'])->count();

        $initiales = strtoupper(
            substr($customer->prenom, 0, 1) . substr($customer->nom, 0, 1)
        );

        $typesMensuels   = TypeCotisation::where('type', 'mensuel')
            ->where('is_required', true)
            ->where('status', 'actif')
            ->orderBy('libelle')
            ->get();

        $coutEngagements = CoutEngagement::actif()->orderBy('montant')->get();

        return compact(
            'customer', 'totalCotise', 'totalDu',
            'nbPaiements', 'moisRetard', 'nbDocuments',
            'nbReclammationsEnCours', 'initiales',
            'typesMensuels', 'coutEngagements'
        );
    }
};
?>
