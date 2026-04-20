<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Bilan Fidèle — {{ $customer->prenom }} {{ $customer->nom }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size:11px; color:#1a1d2e; background:#fff; }

  /* ── Header mosquée ── */
  .pdf-header {
    background: linear-gradient(135deg, #2d3a63 0%, #405189 60%, #3577f1 100%);
    padding: 0px 32px 24px;
    color: #fff;
    position: relative;
    overflow: hidden;
  }
  .pdf-header::after {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
  }
  .mosque-logo {
    font-size: 32px;
    margin-bottom: 4px;
  }
  .mosque-name {
    font-size: 18px;
    font-weight: 700;
    letter-spacing: .5px;
  }
  .mosque-sub {
    font-size: 11px;
    color: rgba(255,255,255,.7);
    margin-top: 2px;
  }
  .doc-title {
    font-size: 22px;
    font-weight: 700;
    margin-top: 16px;
    letter-spacing: .3px;
  }
  .doc-periode {
    font-size: 12px;
    color: rgba(255,255,255,.75);
    margin-top: 4px;
  }
  .doc-meta {
    font-size: 10px;
    color: rgba(255,255,255,.55);
    margin-top: 8px;
  }

  /* ── Fidèle info ── */
  .fidele-block {
    background: #f8f9fc;
    border-left: 4px solid #405189;
    padding: 14px 20px;
    margin: 20px 32px;
    border-radius: 0 8px 8px 0;
    display: flex;
    gap: 32px;
  }
  .fidele-avatar {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: #405189;
    color: #fff;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .fidele-infos { flex: 1; }
  .fidele-name { font-size: 16px; font-weight: 700; color: #212529; margin-bottom: 6px; }
  .fidele-row { font-size: 11px; color: #495057; margin-bottom: 3px; display: flex; gap: 6px; }
  .fidele-row span:first-child { color: #878a99; min-width: 100px; }

  /* ── KPI cards ── */
  .kpi-row {
    display: flex;
    gap: 12px;
    margin: 0 32px 20px;
  }
  .kpi-box {
    flex: 1;
    border: 1px solid #e9ebec;
    border-radius: 10px;
    padding: 12px 14px;
    text-align: center;
  }
  .kpi-box .kval { font-size: 18px; font-weight: 700; }
  .kpi-box .klabel { font-size: 10px; color: #878a99; margin-top: 3px; text-transform: uppercase; letter-spacing: .5px; }
  .kpi-green { border-top: 3px solid #0ab39c; }
  .kpi-red   { border-top: 3px solid #f06548; }
  .kpi-warn  { border-top: 3px solid #f7b84b; }
  .kpi-blue  { border-top: 3px solid #405189; }

  /* ── Section titre ── */
  .section-title {
    font-size: 12px;
    font-weight: 700;
    color: #405189;
    text-transform: uppercase;
    letter-spacing: .8px;
    border-bottom: 2px solid #405189;
    padding-bottom: 5px;
    margin: 20px 32px 12px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* ── Table ── */
  .pdf-table {
    width: calc(100% - 64px);
    margin: 0 32px;
    border-collapse: collapse;
    font-size: 10.5px;
  }
  .pdf-table thead th {
    background: #405189;
    color: #fff;
    padding: 8px 10px;
    text-align: left;
    font-weight: 600;
    font-size: 10px;
    letter-spacing: .4px;
  }
  .pdf-table thead th:first-child { border-radius: 6px 0 0 0; }
  .pdf-table thead th:last-child  { border-radius: 0 6px 0 0; }
  .pdf-table tbody tr:nth-child(even) { background: #f8f9fc; }
  .pdf-table tbody td {
    padding: 7px 10px;
    border-bottom: 1px solid #e9ebec;
    color: #212529;
    vertical-align: middle;
  }
  .pdf-table tbody tr:last-child td { border-bottom: none; }

  /* ── Pills ── */
  .pill {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 9.5px;
    font-weight: 700;
  }
  .pill-ok     { background: rgba(10,179,156,.12); color: #0ab39c; }
  .pill-warn   { background: rgba(247,184,75,.15); color: #c07a10; }
  .pill-danger { background: rgba(240,101,72,.12); color: #f06548; }
  .pill-info   { background: rgba(64,81,137,.10); color: #405189; }

  /* ── Footer ── */
  .pdf-footer {
    border-top: 1px solid #e9ebec;
    padding: 12px 32px;
    margin-top: 28px;
    font-size: 9.5px;
    color: #878a99;
    display: flex;
    justify-content: space-between;
  }
  .pdf-footer strong { color: #405189; }

  .text-right { text-align: right; }
  .text-green { color: #0ab39c; font-weight: 700; }
  .text-red   { color: #f06548; font-weight: 700; }
  .text-warn  { color: #f7b84b; font-weight: 700; }
  .text-mono  { font-family: 'DejaVu Sans Mono', monospace; }
  .mt-8 { margin-top: 8px; }
  .mt-16 { margin-top: 16px; }
  .mb-0 { margin-bottom: 0; }
</style>
</head>
<body>

{{-- ══ HEADER ══ --}}
{{-- <div class="pdf-header">
  <div class="mosque-logo">🕌</div>
  <div class="mosque-name">CMRP Mosquée</div>
  <div class="mosque-sub">Espace Fidèle — Bilan personnel</div>
  <div class="doc-title">Relevé de cotisations</div>
  <div class="doc-periode">Période : {{ $periode }}</div>
  <div class="doc-meta">Généré le {{ now()->translatedFormat('d F Y à H:i') }}</div>
</div> --}}

{{-- ══ INFOS FIDÈLE ══ --}}
<div class="fidele-block">
  <div>
    <div class="fidele-avatar">
      {{ strtoupper(substr($customer->prenom,0,1).substr($customer->nom,0,1)) }}
    </div>
  </div>
  <div class="fidele-infos">
    <div class="fidele-name">{{ $customer->prenom }} {{ $customer->nom }}</div>
    <div class="fidele-row">
      <span>Téléphone</span>
      <span>{{ $customer->dial_code }} {{ $customer->phone }}</span>
    </div>
    <div class="fidele-row">
      <span>Adresse</span>
      <span>{{ $customer->adresse ?? '—' }}</span>
    </div>
    <div class="fidele-row">
      <span>Membre depuis</span>
      <span>{{ $customer->date_adhesion?->translatedFormat('d F Y') ?? '—' }}</span>
    </div>
    <div class="fidele-row">
      <span>Engagement mensuel</span>
      <span>{{ $customer->montant_engagement ? number_format($customer->montant_engagement,0,',',' ').' FCFA/mois' : 'Sans engagement' }}</span>
    </div>
    <div class="fidele-row">
      <span>Statut du compte</span>
      <span>{{ $customer->status === 'actif' ? '✅ Actif' : '⏸ En attente' }}</span>
    </div>
  </div>
</div>

{{-- ══ KPIs ══ --}}
<div class="kpi-row">
  <div class="kpi-box kpi-green">
    <div class="kval text-green">{{ number_format($totalPaye,0,',',' ') }} FCFA</div>
    <div class="klabel">Total payé</div>
  </div>
  <div class="kpi-box kpi-red">
    <div class="kval text-red">{{ number_format($totalDu,0,',',' ') }} FCFA</div>
    <div class="klabel">Total dû</div>
  </div>
  <div class="kpi-box kpi-warn">
    <div class="kval text-warn">{{ number_format($totalRestant,0,',',' ') }} FCFA</div>
    <div class="klabel">Restant</div>
  </div>
  <div class="kpi-box kpi-blue">
    <div class="kval">{{ $cotisations->count() }}</div>
    <div class="klabel">Cotisations</div>
  </div>
  <div class="kpi-box kpi-blue">
    <div class="kval">{{ $paiements->count() }}</div>
    <div class="klabel">Paiements reçus</div>
  </div>
</div>

{{-- ══ COTISATIONS ══ --}}
<div class="section-title">📋 Historique des cotisations</div>

@if($cotisations->count() > 0)
<table class="pdf-table">
  <thead>
    <tr>
      <th>Type</th>
      <th>Période</th>
      <th>Montant dû</th>
      <th>Montant payé</th>
      <th>Restant</th>
      <th>Statut</th>
      <th>Mode</th>
    </tr>
  </thead>
  <tbody>
    @foreach($cotisations as $cot)
    @php
      $periodeLabel = ($cot->mois && $cot->annee)
          ? \Carbon\Carbon::create($cot->annee, $cot->mois)->translatedFormat('F Y')
          : $cot->created_at->translatedFormat('M Y');
      $pillClass = match($cot->statut) { 'a_jour'=>'pill-ok', 'partiel'=>'pill-warn', default=>'pill-danger' };
      $pillLabel = match($cot->statut) { 'a_jour'=>'À jour', 'partiel'=>'Partiel', default=>'En retard' };
      $modeLabel = match($cot->mode_paiement) { 'mobile_money'=>'Mobile Money','espece'=>'Espèces','virement'=>'Virement',default=>'—'};
    @endphp
    <tr>
      <td>{{ $cot->typeCotisation?->libelle ?? '—' }}</td>
      <td>{{ $periodeLabel }}</td>
      <td class="text-mono text-right">{{ $cot->montant_du ? number_format($cot->montant_du,0,',',' ').' FCFA' : '—' }}</td>
      <td class="text-mono text-right text-green">{{ number_format($cot->montant_paye,0,',',' ') }} FCFA</td>
      <td class="text-mono text-right {{ $cot->montant_restant > 0 ? 'text-red' : '' }}">
        {{ $cot->montant_restant > 0 ? number_format($cot->montant_restant,0,',',' ').' FCFA' : '—' }}
      </td>
      <td><span class="pill {{ $pillClass }}">{{ $pillLabel }}</span></td>
      <td>{{ $modeLabel }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@else
<p style="text-align:center;color:#878a99;padding:20px;font-size:12px">Aucune cotisation sur cette période.</p>
@endif

{{-- ══ PAIEMENTS ══ --}}
<div class="section-title mt-16">💳 Paiements reçus</div>

@if($paiements->count() > 0)
<table class="pdf-table">
  <thead>
    <tr>
      <th>Référence</th>
      <th>Date</th>
      <th>Montant</th>
      <th>Mode</th>
      <th>Statut</th>
    </tr>
  </thead>
  <tbody>
    @foreach($paiements as $p)
    @php
      $ref = $p->reference ?? 'PAY-'.str_pad($p->id,6,'0',STR_PAD_LEFT);
      $modeLabel = match($p->mode_paiement){ 'mobile_money'=>'Mobile Money','espece'=>'Espèces','virement'=>'Virement',default=>'—'};
    @endphp
    <tr>
      <td class="text-mono" style="font-size:9.5px">{{ $ref }}</td>
      <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
      <td class="text-mono text-right text-green">+{{ number_format($p->montant,0,',',' ') }} FCFA</td>
      <td>{{ $modeLabel }}</td>
      <td><span class="pill pill-ok">Validé</span></td>
    </tr>
    @endforeach
  </tbody>
</table>
@else
<p style="text-align:center;color:#878a99;padding:20px;font-size:12px">Aucun paiement sur cette période.</p>
@endif

{{-- ══ FOOTER ══ --}}
<div class="pdf-footer">
  <div>
    <strong>CMRP Mosquée</strong> — Document généré automatiquement<br>
    Ce document est un relevé officiel de vos cotisations.
  </div>
  <div style="text-align:right">
    Période : {{ $periode }}<br>
    Page 1 / 1
  </div>
</div>

</body>
</html>
