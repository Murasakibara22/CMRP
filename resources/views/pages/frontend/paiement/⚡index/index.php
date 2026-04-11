<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Paiement;
use Illuminate\Support\Carbon;

new #[Layout('layouts.app-frontend')] class extends Component
{
    public ?int $detailId = null;

    public function showDetail(int $id): void
    {
        $this->dispatch('OpenPayDetail');
        $this->detailId = $id;
    }

    public function closeDetail(): void
    {
        $this->dispatch('closePayDetail');
        $this->detailId = null;
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
};
?>