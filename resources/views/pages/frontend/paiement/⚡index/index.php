<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Paiement;
use App\Models\Customer;
use Illuminate\Support\Carbon;

new #[Layout('layouts.app-frontend')] class extends Component
{
    public ?int $detailId = null;

    public function showDetail(int $id): void
    {
        $this->detailId = $id;
        $this->dispatch('OpenPayDetail');
    }

    public function closeDetail(): void
    {
        $this->dispatch('closePayDetail');
        $this->detailId = null;
    }

    /* ═══════════════════════════════════════════════════════
       TÉLÉCHARGER LE REÇU PDF
       Génère et retourne un PDF via DomPDF.
       Accessible depuis le modal détail (bouton) ou
       directement depuis la liste.
    ═══════════════════════════════════════════════════════ */
    public function telechargerRecu(int $id)
    {
        $paiement  = Paiement::with(['cotisation.typeCotisation'])->findOrFail($id);
        $customer  = Customer::findOrFail(auth('customer')->user()->id);

        /* Sécurité : le paiement appartient bien à ce customer */
        if ($paiement->customer_id !== $customer->id) abort(403);

        /* Seuls les paiements validés ont un reçu officiel */
        if ($paiement->statut !== 'success') {
            $this->send_event_at_sweet_alert_not_timer(
                'Reçu indisponible',
                'Le reçu n\'est disponible que pour les paiements validés.',
                'info'
            );
            return;
        }

        $ref = $paiement->reference
            ?? 'PAY-' . str_pad($paiement->id, 6, '0', STR_PAD_LEFT);

        $typeLabel    = $paiement->cotisation?->typeCotisation?->libelle ?? '—';
        $periodeLabel = ($paiement->cotisation?->mois && $paiement->cotisation?->annee)
            ? Carbon::create($paiement->cotisation->annee, $paiement->cotisation->mois)
                ->translatedFormat('F Y')
            : '—';

        $modeLabel = match($paiement->mode_paiement) {
            'mobile_money' => 'Mobile Money',
            'espece'       => 'Espèces',
            'virement'     => 'Virement',
            default        => '—',
        };

        $metadata  = $paiement->metadata ? json_decode($paiement->metadata, true) : [];
        $operateur = $metadata['operateur'] ?? null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.frontend.recu-paiement', [
            'paiement'     => $paiement,
            'customer'     => $customer,
            'ref'          => $ref,
            'typeLabel'    => $typeLabel,
            'periodeLabel' => $periodeLabel,
            'modeLabel'    => $modeLabel,
            'operateur'    => $operateur,
            'genereLe'     => now()->translatedFormat('d F Y à H:i'),
        ])->setPaper('a4');

        $filename = "recu-{$ref}-" . now()->format('Ymd') . ".pdf";

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $filename
        );
    }

    public function with(): array
    {
        $customerId = auth('customer')->user()->id;

        $paiements = Paiement::with(['cotisation.typeCotisation'])
            ->where('customer_id', $customerId)
            ->orderByDesc('date_paiement')
            ->get();

        $kpis = [
            'success' => $paiements->where('statut', 'success')->count(),
            'attente' => $paiements->where('statut', 'en_attente')->count(),
            'echec'   => $paiements->where('statut', 'echec')->count(),
            'total'   => $paiements->where('statut', 'success')->sum('montant'),
        ];

        $detailPaiement = $this->detailId
            ? Paiement::with(['cotisation.typeCotisation'])->find($this->detailId)
            : null;

        return compact('paiements', 'kpis', 'detailPaiement');
    }
}
?>
