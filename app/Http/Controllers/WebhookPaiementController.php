<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * WebhookPaiementController
 *
 * Reçoit les notifications POST d'AdjeminPay sur :
 * POST /api/webhook/paiement
 *
 * AdjeminPay envoie le statut de chaque transaction dès qu'il change.
 * On délègue tout le traitement à PaymentService::traiterWebhook().
 */
class WebhookPaiementController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('AdjeminPay webhook reçu', [
            'ip'      => $request->ip(),
            'payload' => $payload,
        ]);

        /* Validation minimale */
        if (empty($payload['merchant_trans_id'])) {
            return response()->json(['error' => 'payload invalide'], 400);
        }

        $ok = PaymentService::traiterWebhook($payload);

        return response()->json([
            'received' => true,
            'handled'  => $ok,
        ]);
    }
}
