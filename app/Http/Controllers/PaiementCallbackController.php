<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

/**
 * PaiementCallbackController
 *
 * Gère les URLs de retour après paiement AdjeminPay.
 *
 * GET /customer/paiements/success?ref=CMRP-XXXXXXX   → retour OK
 * GET /customer/paiements?ref=CMRP-XXXXXXX&status=cancel → annulation
 */
class PaiementCallbackController extends Controller
{
    /**
     * Return URL — le customer revient depuis la page de paiement.
     * On vérifie le statut en temps réel et on redirige vers
     * la page paiements avec un message approprié.
     */
    public function success(Request $request)
    {
        $ref = $request->query('ref');

        if (! $ref) {
            return redirect()->route('customer.paiements')
                ->with('toast_error', 'Référence de paiement manquante.');
        }

        $result   = PaymentService::verifierStatut($ref);
        $statut   = $result['statut'];
        $paiement = $result['paiement'];

        return match($statut) {
            'success' => redirect()->route('customer.paiements')
                ->with('toast_success', 'Paiement validé ! Votre cotisation est à jour.'),

            'echec'   => redirect()->route('customer.paiements')
                ->with('toast_error', 'Paiement échoué ou annulé. Veuillez réessayer.'),

            default   => redirect()->route('customer.paiements')
                ->with('toast_info', 'Paiement en cours de traitement. Votre cotisation sera mise à jour sous peu.'),
        };
    }

    /**
     * Cancel URL — le customer a annulé depuis la page AdjeminPay.
     */
    public function cancel(Request $request)
    {
        $ref = $request->query('ref');

        if ($ref) {
            $paiement = \App\Models\Paiement::where('reference', $ref)->first();
            if ($paiement && $paiement->statut === 'en_attente') {
                $paiement->update([
                    'statut'   => 'echec',
                    'metadata' => array_merge($paiement->metadata ?? [], [
                        'echec_reason' => 'cancelled_by_customer',
                        'echec_at'     => now()->toDateTimeString(),
                    ]),
                ]);
            }
        }

        return redirect()->route('customer.paiements')
            ->with('toast_info', 'Paiement annulé.');
    }
}
