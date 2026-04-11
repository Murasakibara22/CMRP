<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\TypeCotisation;
use App\Models\CoutEngagement;
use App\Models\Paiement;
use App\Models\Transaction;
use App\Traits\UtilsSweetAlert;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

new #[Layout('layouts.app-frontend')] class extends Component
{
    use UtilsSweetAlert;

    /* ── Champs formulaire ──────────────────────────────── */
    public ?int    $typeCotisationId = null;
    public ?int    $mois             = null;
    public ?int    $annee            = null;
    public ?int    $montantPaye      = null;
    public string  $modePaiement     = 'mobile_money';

    /* ── Engagement (si mensuel + pas encore défini) ────── */
    public ?int    $engagementChoisi  = null;   // palier sélectionné
    public ?int    $montantLibre      = null;   // si "Autre"
    public bool    $showMontantLibre  = false;

    /* ── Erreurs ────────────────────────────────────────── */
    public string  $errorType        = '';
    public string  $errorPeriode     = '';
    public string  $errorMontant     = '';
    public string  $errorMode        = '';
    public string  $errorEngagement  = '';

    public function mount(): void
    {
        $this->mois  = now()->month;
        $this->annee = now()->year;
    }

    /* ── Réinitialiser erreurs quand les champs changent ── */
    public function updatedTypeCotisationId(): void
    {
        $this->errorType       = '';
        $this->engagementChoisi = null;
        $this->montantLibre    = null;
        $this->showMontantLibre = false;
        $this->montantPaye     = null;
    }

    public function updatedMontantPaye(): void   { $this->errorMontant = ''; }
    public function updatedMois(): void          { $this->errorPeriode = ''; }
    public function updatedAnnee(): void         { $this->errorPeriode = ''; }
    public function updatedMontantLibre(): void  { $this->errorEngagement = ''; }

    /* ── Sélection palier engagement ───────────────────── */
    public function selectPalier(int $montant): void
    {
        $this->engagementChoisi = $montant;
        $this->showMontantLibre = false;
        $this->montantLibre     = null;
        $this->montantPaye      = $montant;
        $this->errorEngagement  = '';
    }

    public function selectPalierAutre(): void
    {
        $this->engagementChoisi = null;
        $this->showMontantLibre = true;
        $this->montantPaye      = null;
        $this->errorEngagement  = '';
    }

    /* ── Sélection mode paiement ────────────────────────── */
    public function selectMode(string $mode): void
    {
        $this->modePaiement = $mode;
        $this->errorMode    = '';
    }

    /* ── Valider et soumettre ───────────────────────────── */
    public function submit(): void
    {
        $this->errorType       = '';
        $this->errorPeriode    = '';
        $this->errorMontant    = '';
        $this->errorMode       = '';
        $this->errorEngagement = '';

        $customerId = auth('customer')->user()->id;
        $customer   = Customer::find($customerId);

        /* 1. Type obligatoire */
        if (! $this->typeCotisationId) {
            $this->errorType = 'Veuillez choisir un type de cotisation.';
            return;
        }

        $tc = TypeCotisation::find($this->typeCotisationId);

        /* 2. Mensuel obligatoire : vérifier/définir engagement */
        $isMensuelObligatoire = $tc->type === 'mensuel' && $tc->is_required;

        if ($isMensuelObligatoire) {
            /* Période obligatoire */
            if (! $this->mois || ! $this->annee) {
                $this->errorPeriode = 'Veuillez sélectionner le mois et l\'année.';
                return;
            }

            /* Engagement : déjà défini ou à choisir maintenant */
            if (! $customer->montant_engagement) {
                $engagement = $this->showMontantLibre
                    ? $this->montantLibre
                    : $this->engagementChoisi;

                if (! $engagement || $engagement < 500) {
                    $this->errorEngagement = 'Veuillez choisir un montant d\'engagement (minimum 500 FCFA).';
                    return;
                }

                /* Sauvegarder l'engagement sur le customer */
                $customer->update(['montant_engagement' => $engagement]);
            }
        }

        /* 3. Montant payé */
        $montant = $this->montantPaye;
        if (! $montant || $montant < 1) {
            $this->errorMontant = 'Veuillez saisir un montant valide.';
            return;
        }

        /* 4. Mode paiement */
        if (! $this->modePaiement) {
            $this->errorMode = 'Veuillez choisir un mode de paiement.';
            return;
        }

        /* 5. Calcul du montant_du et statut */
        $montantDu      = null;
        $montantRestant = 0;
        $statut         = 'a_jour';

        if ($isMensuelObligatoire) {
            $engagement     = $customer->fresh()->montant_engagement;
            $montantDu      = $engagement;
            $montantRestant = max(0, $montantDu - $montant);
            $statut         = $montant >= $montantDu ? 'a_jour'
                : ($montant > 0 ? 'partiel' : 'en_retard');
        }

        /* 6. Vérifier doublon mois/année (mensuel) */
        if ($isMensuelObligatoire) {
            $existe = Cotisation::where('customer_id', $customerId)
                ->where('type_cotisation_id', $this->typeCotisationId)
                ->where('mois', $this->mois)
                ->where('annee', $this->annee)
                ->exists();

            if ($existe) {
                $this->errorPeriode = 'Une cotisation existe déjà pour cette période.';
                return;
            }
        }

        /* 7. Créer la cotisation — statut en_attente (validation admin) */
        $cotisation = Cotisation::create([
            'customer_id'        => $customerId,
            'type_cotisation_id' => $this->typeCotisationId,
            'mois'               => $isMensuelObligatoire ? $this->mois   : null,
            'annee'              => $isMensuelObligatoire ? $this->annee  : null,
            'montant_du'         => $montantDu,
            'montant_paye'       => $montant,
            'montant_restant'    => $montantRestant,
            'statut'             => 'en_retard',   // en attente validation admin
            'mode_paiement'      => $this->modePaiement,
        ]);

        /* 8. Créer le paiement associé (en_attente) */
        $paiement = Paiement::create([
            'customer_id'        => $customerId,
            'type_cotisation_id' => $this->typeCotisationId,
            'cotisation_id'      => $cotisation->id,
            'montant'            => $montant,
            'mode_paiement'      => $this->modePaiement,
            'statut'             => 'en_attente',
            'date_paiement'      => now(),
        ]);

        /* 9. Créer la transaction (entrée en attente) */
        $periode = $isMensuelObligatoire
            ? ' – ' . Carbon::create($this->annee, $this->mois)->translatedFormat('F Y')
            : '';
        Transaction::create([
            'type'             => 'entree',
            'source'           => 'paiement',
            'source_id'        => $paiement->id,
            'montant'          => $montant,
            'libelle'          => "Paiement cotisation – {$tc->libelle}{$periode}",
            'date_transaction' => now(),
        ]);

        /* 10. Feedback + redirect */
        $this->send_event_at_toast('Cotisation soumise ! En attente de validation.', 'success', 'top-end');
        $this->redirect(route('customer.cotisations'));
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $customerId = auth('customer')->user()->id;
        $customer   = Customer::find($customerId);

        $typesCotisation = TypeCotisation::where('status', 'actif')
            ->orderBy('libelle')
            ->get();

        $coutEngagements = CoutEngagement::orderBy('montant')->get();

        $tcSelectionne   = $this->typeCotisationId
            ? $typesCotisation->firstWhere('id', $this->typeCotisationId)
            : null;

        $isMensuelObligatoire = $tcSelectionne?->type === 'mensuel' && $tcSelectionne?->is_required;
        $customerHasEngagement = $customer?->montant_engagement !== null;

        /* Résumé des mois en retard (mensuel) */
        $mensuelTC = $typesCotisation->firstWhere('type', 'mensuel');
        $retards   = $mensuelTC
            ? Cotisation::where('customer_id', $customerId)
                ->where('type_cotisation_id', $mensuelTC->id)
                ->whereIn('statut', ['en_retard', 'partiel'])
                ->orderBy('annee')->orderBy('mois')
                ->get()
            : collect();

        $totalRetard = $retards->sum('montant_restant');

        /* Preview recap */
        $recapType    = $tcSelectionne?->libelle ?? '—';
        $recapPeriode = $isMensuelObligatoire && $this->mois && $this->annee
            ? Carbon::create($this->annee, $this->mois)->translatedFormat('F Y')
            : '—';
        $recapMontant = $this->montantPaye
            ? number_format($this->montantPaye, 0, ',', ' ') . ' FCFA'
            : '—';
        $recapMode = match($this->modePaiement) {
            'mobile_money' => 'Mobile Money',
            'espece'       => 'Espèces',
            'virement'     => 'Virement',
            default        => '—',
        };

        /* Info partiel si mensuel */
        $isPartiel = $isMensuelObligatoire
            && $customerHasEngagement
            && $this->montantPaye
            && $this->montantPaye < $customer->montant_engagement;

        return compact(
            'customer', 'typesCotisation', 'coutEngagements',
            'tcSelectionne', 'isMensuelObligatoire', 'customerHasEngagement',
            'retards', 'totalRetard',
            'recapType', 'recapPeriode', 'recapMontant', 'recapMode',
            'isPartiel'
        );
    }
};
?>