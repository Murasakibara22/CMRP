<?php

namespace App\Services;

use App\Models\Cotisation;
use App\Models\Customer;
use App\Models\Paiement;
use App\Models\Transaction;
use App\Models\TypeCotisation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PaymentService — CMRP
 *
 * Gère les paiements en ligne via AdjeminPay (gateway XOF).
 * Toutes les clés sensibles sont dans le .env :
 *
 *   ADJEMINPAY_CLIENT_ID=
 *   ADJEMINPAY_CLIENT_SECRET=
 *   ADJEMINPAY_SELLER_USERNAME=
 *   ADJEMINPAY_BASE_URL=https://api.adjem.in/v3
 *   ADJEMINPAY_WEBHOOK_URL=https://cmrp.ci/api/webhook/paiement
 *   ADJEMINPAY_RETURN_URL=https://cmrp.ci/customer/paiements/success
 *   ADJEMINPAY_CANCEL_URL=https://cmrp.ci/customer/paiements
 */
class PaymentService
{
    /* ═══════════════════════════════════════════════════════
       POINT D'ENTRÉE PRINCIPAL
       Appelé depuis le composant Livewire quand le customer
       choisit "Mobile Money / En ligne".
       Retourne l'URL de la page de paiement AdjeminPay.
    ═══════════════════════════════════════════════════════ */

    /**
     * Initialiser un paiement en ligne pour une cotisation.
     *
     * @param  Paiement  $paiement   Paiement existant (statut en_attente)
     * @param  Cotisation $cotisation Cotisation liée
     * @return string                URL de la page de paiement gateway
     *
     * @throws \Exception si l'API est inaccessible ou retourne une erreur
     */
    public static function initierPaiementCotisation(
        Paiement   $paiement,
        Cotisation $cotisation
    ): string {
        /* 1. Obtenir le token OAuth */
        $token = self::getAccessToken();

        /* 2. Construire la désignation */
        $tc      = $cotisation->typeCotisation;
        $periode = ($cotisation->mois && $cotisation->annee)
            ? ' — ' . Carbon::create($cotisation->annee, $cotisation->mois)->translatedFormat('F Y')
            : '';
        $designation = "Cotisation CMRP — {$tc?->libelle}{$periode}";

        /* 3. Customer */
        $customer = $paiement->customer;

        /* 4. Référence unique pour AdjeminPay */
        $ref = 'CMRP-' . str_pad($paiement->id, 7, '0', STR_PAD_LEFT) . '-' . now()->format('YmdHis');

        /* Sauvegarder la ref sur le paiement pour retrouver lors du webhook */
        $paiement->update(['reference' => $ref]);

        /* 5. Appel API gateway */
        $response = Http::withToken($token)
            ->acceptJson()
            ->post(config('payment.base_url') . '/gateway/merchants/create_payment', [
                'amount'                   => intval($paiement->montant),
                'currency_code'            => 'XOF',
                'merchant_trans_id'        => $ref,
                'seller_username'          => config('payment.seller_username'),
                'payment_type'             => 'gateway',
                'designation'             => $designation,
                'webhook_url'              => config('payment.webhook_url'),
                'return_url'               => config('payment.return_url') . '?ref=' . $ref,
                'cancel_url'               => config('payment.cancel_url') . '?ref=' . $ref . '&status=cancel',
                'customer_recipient_number'=> $customer->phone,
                'customer_email'           => $customer->email ?? '',
                'customer_firstname'       => $customer->prenom,
                'customer_lastname'        => $customer->nom,
            ]);

        if (! $response->successful() || empty($response['data']['gateway_payment_url'])) {
            Log::error('AdjeminPay — initierPaiementCotisation failed', [
                'paiement_id' => $paiement->id,
                'status'      => $response->status(),
                'body'        => $response->json(),
            ]);
            throw new \Exception('Impossible d\'initialiser le paiement. Veuillez réessayer.');
        }

        Log::info('AdjeminPay — paiement initialisé', [
            'paiement_id' => $paiement->id,
            'ref'         => $ref,
            'url'         => $response['data']['gateway_payment_url'],
        ]);

        return $response['data']['gateway_payment_url'];
    }

    /* ═══════════════════════════════════════════════════════
       WEBHOOK — Traitement de la confirmation AdjeminPay
       Appelé par AdjeminPay sur ADJEMINPAY_WEBHOOK_URL.
       Met à jour Paiement, Cotisation et Transaction.
    ═══════════════════════════════════════════════════════ */

    /**
     * Traiter la notification webhook d'AdjeminPay.
     *
     * @param  array  $payload  Corps de la requête JSON
     * @return bool   true si traité avec succès
     */
    public static function traiterWebhook(array $payload): bool
    {
        $ref    = $payload['merchant_trans_id'] ?? null;
        $status = $payload['status']            ?? null;

        if (! $ref || ! $status) {
            Log::warning('AdjeminPay webhook — payload invalide', $payload);
            return false;
        }

        $paiement = Paiement::where('reference', $ref)
            ->with(['cotisation.typeCotisation', 'customer'])
            ->first();

        if (! $paiement) {
            Log::warning("AdjeminPay webhook — paiement introuvable pour ref {$ref}");
            return false;
        }

        /* Ignorer si déjà traité */
        if ($paiement->statut === 'success') {
            Log::info("AdjeminPay webhook — paiement {$ref} déjà traité, ignoré.");
            return true;
        }

        if (strtolower($status) === 'success') {
            return self::confirmerPaiement($paiement, $payload);
        }

        if (in_array(strtolower($status), ['failed', 'cancelled', 'expired'])) {
            return self::echec($paiement, $status);
        }

        Log::info("AdjeminPay webhook — statut inconnu : {$status}");
        return false;
    }

    /* ═══════════════════════════════════════════════════════
       CALLBACK RETURN URL — Page de retour après paiement
       Appelé quand le customer revient depuis la page AdjeminPay.
       Vérification du statut en temps réel (évite les délais webhook).
    ═══════════════════════════════════════════════════════ */

    /**
     * Vérifier le statut d'un paiement depuis l'URL de retour.
     *
     * @param  string  $ref   merchant_trans_id (référence CMRP)
     * @return array   ['statut' => 'success|pending|echec', 'paiement' => Paiement]
     */
    public static function verifierStatut(string $ref): array
    {
        $paiement = Paiement::where('reference', $ref)
            ->with(['cotisation.typeCotisation'])
            ->first();

        if (! $paiement) {
            return ['statut' => 'inconnu', 'paiement' => null];
        }

        /* Si déjà confirmé par webhook → renvoyer le statut actuel */
        if ($paiement->statut === 'success') {
            return ['statut' => 'success', 'paiement' => $paiement];
        }

        /* Vérifier en temps réel via l'API */
        try {
            $token    = self::getAccessToken();
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(config('payment.base_url') . '/gateway/merchants/check_payment', [
                    'merchant_trans_id' => $ref,
                ]);

            if ($response->successful()) {
                $status = strtolower($response['data']['status'] ?? 'pending');

                if ($status === 'success') {
                    self::confirmerPaiement($paiement, $response['data']);
                    return ['statut' => 'success', 'paiement' => $paiement->fresh()];
                }

                if (in_array($status, ['failed', 'cancelled', 'expired'])) {
                    self::echec($paiement, $status);
                    return ['statut' => 'echec', 'paiement' => $paiement->fresh()];
                }
            }
        } catch (\Throwable $e) {
            Log::error('AdjeminPay — verifierStatut failed', [
                'ref'   => $ref,
                'error' => $e->getMessage(),
            ]);
        }

        return ['statut' => 'pending', 'paiement' => $paiement];
    }

    /* ─────────────────────────────────────────────────────
       Confirmer un paiement (succès)
       MAJ : Paiement → success, Cotisation → a_jour, Transaction créée
    ───────────────────────────────────────────────────── */
    private static function confirmerPaiement(Paiement $paiement, array $payload): bool
    {
        try {
            \DB::transaction(function () use ($paiement, $payload) {

                /* MAJ Paiement */
                $paiement->update([
                    'statut'       => 'success',
                    'date_paiement'=> now(),
                    'metadata'     => array_merge($paiement->metadata ?? [], [
                        'gateway_ref'  => $payload['transaction_id']  ?? null,
                        'operateur'    => $payload['operator']         ?? null,
                        'phone_paye'   => $payload['customer_msisdn']  ?? null,
                        'confirmed_at' => now()->toDateTimeString(),
                    ]),
                ]);

                /* MAJ Cotisation */
                $cotisation = $paiement->cotisation;
                if ($cotisation && $cotisation->statut !== 'a_jour') {
                    $cotisation->update([
                        'statut'          => 'a_jour',
                        'montant_restant' => 0,
                        'validated_by'    => null,  // validé automatiquement (gateway)
                        'validated_at'    => now(),
                    ]);
                }

                /* Créer la Transaction si absente */
                $txExists = Transaction::where('source', 'paiement')
                    ->where('source_id', $paiement->id)
                    ->where('status', 'success')
                    ->exists();

                if (! $txExists) {
                    $tc      = $cotisation?->typeCotisation;
                    $periode = ($cotisation?->mois && $cotisation?->annee)
                        ? ' — ' . Carbon::create($cotisation->annee, $cotisation->mois)->translatedFormat('F Y')
                        : '';

                    Transaction::create([
                        'type'             => 'entree',
                        'source'           => 'paiement',
                        'source_id'        => $paiement->id,
                        'status'           => 'success',
                        'montant'          => $paiement->montant,
                        'libelle'          => "Cotisation CMRP — {$tc?->libelle}{$periode} (Mobile Money)",
                        'date_transaction' => now(),
                    ]);
                }

            });

            Log::info('PaymentService — paiement confirmé', ['paiement_id' => $paiement->id]);
            return true;

        } catch (\Throwable $e) {
            Log::error('PaymentService — confirmerPaiement failed', [
                'paiement_id' => $paiement->id,
                'error'       => $e->getMessage(),
            ]);
            return false;
        }
    }

    /* ─────────────────────────────────────────────────────
       Marquer un paiement comme échoué
    ───────────────────────────────────────────────────── */
    private static function echec(Paiement $paiement, string $reason): bool
    {
        $paiement->update([
            'statut'   => 'echec',
            'metadata' => array_merge($paiement->metadata ?? [], [
                'echec_reason' => $reason,
                'echec_at'     => now()->toDateTimeString(),
            ]),
        ]);

        Log::info('PaymentService — paiement échoué', [
            'paiement_id' => $paiement->id,
            'reason'      => $reason,
        ]);

        return true;
    }

    /* ─────────────────────────────────────────────────────
       OAuth — Obtenir le token d'accès AdjeminPay
    ───────────────────────────────────────────────────── */
    private static function getAccessToken(): string
    {
        $response = Http::asForm()->post(
            config('payment.base_url') . '/oauth/token',
            [
                'client_id'     => config('payment.client_id'),
                'client_secret' => config('payment.client_secret'),
                'grant_type'    => 'client_credentials',
            ]
        );

        if (! $response->successful() || empty($response['access_token'])) {
            Log::error('AdjeminPay — getAccessToken failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            throw new \Exception('Authentification gateway impossible.');
        }

        return $response['access_token'];
    }
}
