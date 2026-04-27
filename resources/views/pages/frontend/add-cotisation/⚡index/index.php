<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\TypeCotisation;
use App\Models\CoutEngagement;
use App\Models\Paiement;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;

new #[Layout('layouts.app-frontend')] class extends Component
{
    use UtilsSweetAlert;

    /* ── Champs formulaire ──────────────────────────────── */
    public ?int   $typeCotisationId = null;
    public ?int   $mois             = null;
    public ?int   $annee            = null;
    public ?int   $montantPaye      = null;
    public string $modePaiement     = 'mobile_money';

    /* ── Erreurs ────────────────────────────────────────── */
    public string $errorType    = '';
    public string $errorPeriode = '';
    public string $errorMontant = '';
    public string $errorMode    = '';

    public function mount(): void
    {
        $this->mois  = now()->month;
        $this->annee = now()->year;

        /* Si on revient d'un paiement (flash session) */
        if (session()->has('toast_success')) {
            $this->send_event_at_toast(session('toast_success'), 'success', 'top-end');
            session()->forget('toast_success');
        }
        if (session()->has('toast_error')) {
            $this->send_event_at_toast(session('toast_error'), 'error', 'top-end');
            session()->forget('toast_error');
        }
        if (session()->has('toast_info')) {
            $this->send_event_at_toast(session('toast_info'), 'info', 'top-end');
            session()->forget('toast_info');
        }
    }

    public function updatedTypeCotisationId(): void { $this->errorType = ''; $this->montantPaye = null; }
    public function updatedMontantPaye(): void       { $this->errorMontant = ''; }
    public function updatedMois(): void              { $this->errorPeriode = ''; }
    public function updatedAnnee(): void             { $this->errorPeriode = ''; }

    public function selectMode(string $mode): void
    {
        $this->modePaiement = $mode;
        $this->errorMode    = '';
    }

    /* ═══════════════════════════════════════════════════════
       SUBMIT
    ═══════════════════════════════════════════════════════ */
    public function submit(): void
    {
        $this->resetErrors();

        $customerId = auth('customer')->user()->id;
        $customer   = Customer::find($customerId);

        /* 1. Type */
        if (! $this->typeCotisationId) {
            $this->errorType = 'Veuillez choisir un type de cotisation.';
            return;
        }
        $tc = TypeCotisation::find($this->typeCotisationId);

        /* 2. Montant */
        if (! $this->montantPaye || $this->montantPaye < 1) {
            $this->errorMontant = 'Veuillez saisir un montant valide.';
            return;
        }

        /* 3. Mode */
        if (! $this->modePaiement) {
            $this->errorMode = 'Veuillez choisir un mode de paiement.';
            return;
        }

        /* 4. Calcul montant_du / statut selon le type */
        $isMensuel  = $tc->type === 'mensuel';
        $montantDu  = $isMensuel ? ($customer->montant_engagement ?? $this->montantPaye) : $this->montantPaye;
        $restant    = max(0, $montantDu - $this->montantPaye);
        $statut     = 'en_retard'; // toujours en_retard avant validation

        /* 5. Vérifier doublon période (mensuel) */
        if ($isMensuel && $this->mois && $this->annee) {
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

        /* 6. Créer la cotisation (en_retard — sera mise à jour après paiement) */
        $cotisation = Cotisation::create([
            'customer_id'        => $customerId,
            'libelle'            => $tc->libelle.' du'. now()->format('d/m/Y') ?? null,
            'type_cotisation_id' => $this->typeCotisationId,
            'mois'               => $isMensuel ? $this->mois  : null,
            'annee'              => $isMensuel ? $this->annee : null,
            'montant_du'         => $montantDu,
            'montant_paye'       => $this->montantPaye,
            'montant_restant'    => $restant,
            'statut'             => $statut,
            'mode_paiement'      => $this->modePaiement,
        ]);

        /* 7. Créer le Paiement en_attente */
        $paiement = Paiement::create([
            'customer_id'        => $customerId,
            'type_cotisation_id' => $this->typeCotisationId,
            'cotisation_id'      => $cotisation->id,
            'montant'            => $this->montantPaye,
            'mode_paiement'      => $this->modePaiement,
            'statut'             => 'en_attente',
            'date_paiement'      => now(),
        ]);

        /* Lier le paiement à la cotisation */
        $cotisation->update(['paiement_id' => $paiement->id]);

        /* 8. Branching selon le mode */
        if ($this->modePaiement === 'mobile_money') {
            /* ── PAIEMENT EN LIGNE : redirection vers AdjeminPay ── */
            try {
                $url = PaymentService::initierPaiementCotisation($paiement, $cotisation);
                $this->redirect($url);
            } catch (\Throwable $e) {
                /* En cas d'échec API → supprimer les enregistrements créés */
                $cotisation->delete();
                $paiement->delete();
                $this->send_event_at_sweet_alert_not_timer(
                    'Erreur de paiement',
                    $e->getMessage(),
                    'error'
                );
            }
            return;
        }

        /* ── ESPÈCES : enregistrement simple, validation manuelle BO ── */
        /* Créer la Transaction en_attente */
        $periode = ($isMensuel && $this->mois && $this->annee)
            ? ' — ' . Carbon::create($this->annee, $this->mois)->translatedFormat('F Y')
            : '';

        Transaction::create([
            'type'             => 'entree',
            'source'           => 'paiement',
            'source_id'        => $paiement->id,
            'montant'          => $this->montantPaye,
            'libelle'          => "Cotisation CMRP — {$tc->libelle}{$periode} (Espèces)",
            'date_transaction' => now(),
        ]);

        $this->send_event_at_toast(
            'Cotisation enregistrée ! Remettez le montant en espèces à l\'administration.',
            'success', 'top-end'
        );
        $this->redirect(route('customer.cotisations'));
    }

    private function resetErrors(): void
    {
        $this->errorType    = '';
        $this->errorPeriode = '';
        $this->errorMontant = '';
        $this->errorMode    = '';
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $customerId = auth('customer')->user()->id;
        $customer   = Customer::find($customerId);

        /* Exclure les types mensuels obligatoires (gérés par le cron) */
        $typesCotisation = TypeCotisation::where('status', 'actif')
            ->where(fn($q) =>
                $q->where('type', '!=', 'mensuel')
                  ->orWhere('is_required', false)
            )
            ->orderBy('libelle')
            ->get();

        $tcSelectionne        = $this->typeCotisationId
            ? $typesCotisation->firstWhere('id', $this->typeCotisationId)
            : null;
        $isMensuelObligatoire = $tcSelectionne?->type === 'mensuel' && $tcSelectionne?->is_required;
        $customerHasEngagement = (bool) $customer?->montant_engagement;

        $mensuelTC = $typesCotisation->firstWhere('type', 'mensuel');
        $retards   = $mensuelTC
            ? Cotisation::where('customer_id', $customerId)
                ->where('type_cotisation_id', $mensuelTC->id)
                ->whereIn('statut', ['en_retard', 'partiel'])
                ->orderBy('annee')->orderBy('mois')->get()
            : collect();

        $totalRetard  = $retards->sum('montant_restant');
        $recapType    = $tcSelectionne?->libelle ?? '—';
        $recapPeriode = $isMensuelObligatoire && $this->mois && $this->annee
            ? Carbon::create($this->annee, $this->mois)->translatedFormat('F Y') : '—';
        $recapMontant = $this->montantPaye
            ? number_format($this->montantPaye, 0, ',', ' ') . ' FCFA' : '—';
        $recapMode = match($this->modePaiement) {
            'mobile_money' => 'Mobile Money', 'espece' => 'Espèces', default => '—',
        };
        $isPartiel = $isMensuelObligatoire
            && $customerHasEngagement
            && $this->montantPaye
            && $this->montantPaye < $customer->montant_engagement;

        return compact(
            'customer', 'typesCotisation',
            'tcSelectionne', 'isMensuelObligatoire', 'customerHasEngagement',
            'retards', 'totalRetard',
            'recapType', 'recapPeriode', 'recapMontant', 'recapMode', 'isPartiel'
        );
    }
}
?>
