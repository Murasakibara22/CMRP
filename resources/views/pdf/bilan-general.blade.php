<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<title>Bilan Financier — ISL Mosquée</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DejaVu Sans',sans-serif; font-size:10.5px; color:#1a1d2e; background:#fff; }

  /* ── Header ── */
  .pdf-header {
    background: linear-gradient(135deg,#2d3a63,#405189);
    padding: 22px 28px 18px;
    color: #fff;
  }
  .header-top { display:flex; justify-content:space-between; align-items:flex-start; }
  .mosque-brand { }
  .mosque-logo { font-size:28px; }
  .mosque-name { font-size:16px; font-weight:700; margin-top:2px; }
  .mosque-sub  { font-size:10px; color:rgba(255,255,255,.65); }
  .doc-info { text-align:right; }
  .doc-title { font-size:18px; font-weight:700; }
  .doc-periode { font-size:11px; color:rgba(255,255,255,.75); margin-top:3px; }
  .doc-gen { font-size:9.5px; color:rgba(255,255,255,.5); margin-top:6px; }

  /* ── Solde banner ── */
  .solde-banner {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 10px;
    margin-top: 14px;
    padding: 12px 16px;
    display: flex;
    gap: 32px;
    align-items: center;
  }
  .sb-item { flex: 1; text-align:center; }
  .sb-val { font-size: 16px; font-weight:700; }
  .sb-label { font-size: 9.5px; color:rgba(255,255,255,.65); margin-top:2px; }
  .sb-divider { width:1px; background:rgba(255,255,255,.2); align-self:stretch; }

  /* ── KPI grid ── */
  .kpi-grid {
    display: flex;
    gap: 10px;
    margin: 16px 24px;
  }
  .kpi-card {
    flex: 1;
    border: 1px solid #e9ebec;
    border-radius: 8px;
    padding: 10px 12px;
    text-align: center;
  }
  .kpi-card .kv { font-size:15px; font-weight:700; }
  .kpi-card .kl { font-size:9.5px; color:#878a99; margin-top:2px; text-transform:uppercase; letter-spacing:.5px; }
  .kpi-top-green { border-top:3px solid #0ab39c; }
  .kpi-top-red   { border-top:3px solid #f06548; }
  .kpi-top-blue  { border-top:3px solid #405189; }
  .kpi-top-warn  { border-top:3px solid #f7b84b; }

  /* ── Section ── */
  .section-title {
    font-size:11px; font-weight:700; color:#405189;
    text-transform:uppercase; letter-spacing:.7px;
    border-bottom:2px solid #405189; padding-bottom:4px;
    margin: 18px 24px 10px;
  }

  /* ── Tables ── */
  .pdf-table {
    width: calc(100% - 48px);
    margin: 0 24px;
    border-collapse: collapse;
    font-size: 10px;
  }
  .pdf-table thead th {
    background: #405189; color:#fff;
    padding: 7px 8px; text-align:left;
    font-weight:600; font-size:9.5px; letter-spacing:.3px;
  }
  .pdf-table thead th:first-child { border-radius:5px 0 0 0; }
  .pdf-table thead th:last-child  { border-radius:0 5px 0 0; }
  .pdf-table tbody tr:nth-child(even) { background:#f8f9fc; }
  .pdf-table tbody td {
    padding: 6px 8px;
    border-bottom: 1px solid #e9ebec;
    color: #212529;
    vertical-align: middle;
  }
  .pdf-table tbody tr:last-child td { border-bottom:none; }

  /* ── Recouvrement ── */
  .rec-table { width:calc(100% - 48px); margin:0 24px; border-collapse:collapse; font-size:10px; }
  .rec-table td { padding:8px; border-bottom:1px solid #e9ebec; vertical-align:middle; }
  .rec-table tr:last-child td { border-bottom:none; }
  .rec-bar-wrap { background:#e9ebec; border-radius:4px; height:8px; overflow:hidden; }
  .rec-bar-fill { height:8px; border-radius:4px; }

  /* ── 2 colonnes ── */
  .two-cols { display:flex; gap:16px; margin:0 24px; }
  .col-left  { flex:1.5; }
  .col-right { flex:1; }

  /* ── Pills ── */
  .pill { display:inline-block; padding:1.5px 6px; border-radius:10px; font-size:9px; font-weight:700; }
  .pill-ok     { background:rgba(10,179,156,.12); color:#0ab39c; }
  .pill-warn   { background:rgba(247,184,75,.15); color:#c07a10; }
  .pill-danger { background:rgba(240,101,72,.12); color:#f06548; }
  .pill-info   { background:rgba(64,81,137,.10); color:#405189; }

  /* ── Footer ── */
  .pdf-footer {
    border-top:1px solid #e9ebec; padding:10px 24px;
    margin-top:24px; font-size:9.5px; color:#878a99;
    display:flex; justify-content:space-between;
  }

  .text-right { text-align:right; }
  .text-green { color:#0ab39c; font-weight:700; }
  .text-red   { color:#f06548; font-weight:700; }
  .text-mono  { font-family:'DejaVu Sans Mono',monospace; }
  .mt-0 { margin-top:0; }
  .fw7  { font-weight:700; }
</style>
</head>
<body>

{{-- ══ HEADER ══ --}}
<div class="pdf-header">
  {{-- <div class="header-top">
    <div class="mosque-brand">
      <div class="mosque-logo">🕌</div>
      <div class="mosque-name">ISL Mosquée</div>
      <div class="mosque-sub">Tableau de bord financier</div>
    </div>
    <div class="doc-info">
      <div class="doc-title">Bilan Financier</div>
      <div class="doc-periode">{{ $periode }}</div>
      <div class="doc-gen">Généré le {{ $genereLe }}</div>
    </div>
  </div> --}}

  <div class="solde-banner">
    <div class="sb-item">
      <div class="sb-val">{{ number_format($totalEntrees,0,',',' ') }} FCFA</div>
      <div class="sb-label">Total Entrées</div>
    </div>
    <div class="sb-divider"></div>
    <div class="sb-item">
      <div class="sb-val">{{ number_format($totalSorties,0,',',' ') }} FCFA</div>
      <div class="sb-label">Total Dépenses</div>
    </div>
    <div class="sb-divider"></div>
    <div class="sb-item">
      <div class="sb-val" style="{{ $soldeNet >= 0 ? 'color:#a0f0e0' : 'color:#ffb0a0' }}">
        {{ $soldeNet >= 0 ? '+' : '' }}{{ number_format($soldeNet,0,',',' ') }} FCFA
      </div>
      <div class="sb-label">Solde Net</div>
    </div>
    <div class="sb-divider"></div>
    <div class="sb-item">
      <div class="sb-val">{{ number_format($totalDepenses,0,',',' ') }} FCFA</div>
      <div class="sb-label">Dépenses enregistrées</div>
    </div>
  </div>
</div>

{{-- ══ KPIs ══ --}}
<div class="kpi-grid">
  <div class="kpi-card kpi-top-blue">
    <div class="kv">{{ $nbCotisations }}</div>
    <div class="kl">Cotisations</div>
  </div>
  <div class="kpi-card kpi-top-green">
    <div class="kv">{{ $nbPaiements }}</div>
    <div class="kl">Paiements validés</div>
  </div>
  <div class="kpi-card kpi-top-warn">
    <div class="kv">{{ $nbFidelesActif }}</div>
    <div class="kl">Fidèles actifs</div>
  </div>
  <div class="kpi-card kpi-top-red">
    <div class="kv">{{ number_format($totalDepenses,0,',',' ') }} FCFA</div>
    <div class="kl">Total dépenses</div>
  </div>
</div>

{{-- ══ TAUX DE RECOUVREMENT ══ --}}
@if($tauxRecouvrement->count() > 0)
<div class="section-title">📊 Taux de recouvrement par type</div>
<table class="rec-table" style="width:calc(100% - 48px);margin:0 24px">
  <thead style="background:#f3f6f9">
    <tr>
      <td style="padding:7px 8px;font-weight:700;font-size:9.5px;color:#878a99;text-transform:uppercase;letter-spacing:.5px">Type</td>
      <td style="padding:7px 8px;font-weight:700;font-size:9.5px;color:#878a99;text-transform:uppercase;letter-spacing:.5px;text-align:right">Dû</td>
      <td style="padding:7px 8px;font-weight:700;font-size:9.5px;color:#878a99;text-transform:uppercase;letter-spacing:.5px;text-align:right">Collecté</td>
      <td style="padding:7px 8px;font-weight:700;font-size:9.5px;color:#878a99;text-transform:uppercase;letter-spacing:.5px">Progression</td>
      <td style="padding:7px 8px;font-weight:700;font-size:9.5px;color:#878a99;text-transform:uppercase;letter-spacing:.5px;text-align:center">Taux</td>
    </tr>
  </thead>
  @foreach($tauxRecouvrement as $r)
  @php $barColor = $r['taux'] >= 80 ? '#0ab39c' : ($r['taux'] >= 50 ? '#f7b84b' : '#f06548'); @endphp
  <tr>
    <td style="padding:8px;border-bottom:1px solid #e9ebec;font-weight:600">{{ $r['libelle'] }}</td>
    <td style="padding:8px;border-bottom:1px solid #e9ebec;text-align:right;font-family:'DejaVu Sans Mono',monospace">{{ number_format($r['du'],0,',',' ') }}</td>
    <td style="padding:8px;border-bottom:1px solid #e9ebec;text-align:right;font-family:'DejaVu Sans Mono',monospace;color:#0ab39c;font-weight:700">{{ number_format($r['paye'],0,',',' ') }}</td>
    <td style="padding:8px;border-bottom:1px solid #e9ebec;min-width:80px">
      <div class="rec-bar-wrap">
        <div class="rec-bar-fill" style="width:{{ $r['taux'] }}%;background:{{ $barColor }}"></div>
      </div>
    </td>
    <td style="padding:8px;border-bottom:1px solid #e9ebec;text-align:center;font-weight:700;color:{{ $barColor }}">{{ $r['taux'] }}%</td>
  </tr>
  @endforeach
</table>
@endif

{{-- ══ 2 COLONNES : Transactions + Dépenses par type ══ --}}
<div style="display:flex;gap:16px;margin:18px 24px 0">

  {{-- Transactions récentes --}}
  <div style="flex:1.5">
    <div class="section-title mt-0">💳 Transactions récentes</div>
    <table class="pdf-table" style="width:100%;margin:0">
      <thead>
        <tr>
          <th>Libellé</th>
          <th>Date</th>
          <th>Type</th>
          <th style="text-align:right">Montant</th>
        </tr>
      </thead>
      <tbody>
        @forelse($transactions->take(8) as $tx)
        <tr>
          <td>{{ \Str::limit($tx->libelle ?? '—', 30) }}</td>
          <td>{{ $tx->date_transaction->format('d/m/Y') }}</td>
          <td>
            @if($tx->type === 'entree')
              <span class="pill pill-ok">Entrée</span>
            @else
              <span class="pill pill-danger">Sortie</span>
            @endif
          </td>
          <td class="text-mono text-right {{ $tx->type === 'entree' ? 'text-green' : 'text-red' }}">
            {{ $tx->type === 'entree' ? '+' : '-' }}{{ number_format($tx->montant,0,',',' ') }}
          </td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#878a99;padding:12px">Aucune transaction</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Dépenses par type --}}
  <div style="flex:1">
    <div class="section-title mt-0">📦 Dépenses par catégorie</div>
    <table style="width:100%;border-collapse:collapse;font-size:10px">
      <thead style="background:#f3f6f9">
        <tr>
          <td style="padding:6px 8px;font-weight:700;font-size:9.5px;color:#878a99;text-transform:uppercase">Catégorie</td>
          <td style="padding:6px 8px;font-weight:700;font-size:9.5px;color:#878a99;text-transform:uppercase;text-align:right">Total</td>
        </tr>
      </thead>
      @forelse($depensesParType as $dep)
      <tr>
        <td style="padding:6px 8px;border-bottom:1px solid #e9ebec">{{ $dep['libelle'] }}</td>
        <td style="padding:6px 8px;border-bottom:1px solid #e9ebec;text-align:right;font-family:'DejaVu Sans Mono',monospace;color:#f06548;font-weight:700">
          {{ number_format($dep['total'],0,',',' ') }}
        </td>
      </tr>
      @empty
      <tr><td colspan="2" style="text-align:center;color:#878a99;padding:12px">Aucune dépense</td></tr>
      @endforelse
      @if($depensesParType->count() > 0)
      <tr style="background:#f3f6f9">
        <td style="padding:6px 8px;font-weight:700">Total</td>
        <td style="padding:6px 8px;text-align:right;font-family:'DejaVu Sans Mono',monospace;font-weight:700;color:#f06548">
          {{ number_format($depensesParType->sum('total'),0,',',' ') }} FCFA
        </td>
      </tr>
      @endif
    </table>
  </div>

</div>

{{-- ══ FOOTER ══ --}}
<div class="pdf-footer">
  <div>
    <strong style="color:#405189">ISL Mosquée</strong> — Bilan financier officiel<br>
    Ce document est généré automatiquement depuis le système de gestion.
  </div>
  <div style="text-align:right">
    Période : {{ $periode }}<br>
    Généré le {{ $genereLe }}
  </div>
</div>

</body>
</html>
