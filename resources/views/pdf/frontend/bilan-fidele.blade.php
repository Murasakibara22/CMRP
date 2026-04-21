<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DejaVu Sans', Arial, sans-serif; font-size:12px; color:#212529; background:#fff; padding: 1% }
    .page { width:100%;  }

    /* ── Header ── */
    .header { display:table; width:100%; margin-bottom:22px; border-bottom:3px solid #405189; padding-bottom:16px; }
    .header-left  { display:table-cell; vertical-align:middle; width:60%; }
    .header-right { display:table-cell; vertical-align:middle; text-align:right; }
    .org-name { font-size:20px; font-weight:700; color:#405189; }
    .org-sub  { font-size:11px; color:#878a99; margin-top:2px; }
    .org-contact { font-size:10px; color:#878a99; margin-top:6px; line-height:1.6; }
    .bilan-badge { display:inline-block; background:#405189; color:#fff; font-size:12px; font-weight:700; padding:5px 14px; border-radius:6px; letter-spacing:1px; text-transform:uppercase; margin-bottom:5px; }
    .periode-label { font-size:11px; color:#878a99; }

    /* ── Fidèle ── */
    .fidele-box { background:#f8f9fa; border-radius:8px; padding:12px 16px; margin-bottom:20px; display:table; width:100%; }
    .fidele-left  { display:table-cell; vertical-align:middle; width:65%; }
    .fidele-right { display:table-cell; vertical-align:middle; text-align:right; }
    .fidele-name  { font-size:15px; font-weight:700; color:#212529; }
    .fidele-meta  { font-size:11px; color:#878a99; margin-top:3px; }

    /* ── KPIs ── */
    .kpi-strip { display:table; width:100%; margin-bottom:20px; border-collapse:separate; border-spacing:8px 0; }
    .kpi-box { display:table-cell; text-align:center; padding:12px 8px; border-radius:8px; vertical-align:middle; }
    .kpi-val { font-size:18px; font-weight:800; margin-bottom:3px; }
    .kpi-label { font-size:10px; color:#878a99; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }

    /* ── Section title ── */
    .section-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#405189; margin-bottom:8px; padding-bottom:5px; border-bottom:1px solid #e9ebec; }

    /* ── Tableau cotisations ── */
    .cot-table { width:100%; border-collapse:collapse; margin-bottom:20px; font-size:11px; }
    .cot-table thead tr { background:#405189; color:#fff; }
    .cot-table thead th { padding:8px 10px; text-align:left; font-weight:700; font-size:10px; text-transform:uppercase; letter-spacing:.5px; }
    .cot-table tbody tr:nth-child(even) { background:#f8f9fa; }
    .cot-table tbody td { padding:7px 10px; border-bottom:1px solid #e9ebec; color:#212529; }
    .pill-ok     { color:#0ab39c; font-weight:700; }
    .pill-warn   { color:#f7b84b; font-weight:700; }
    .pill-danger { color:#f06548; font-weight:700; }
    .text-right  { text-align:right; }
    .text-center { text-align:center; }
    .mono { font-family:monospace; }

    /* ── Tableau paiements ── */
    .pay-table { width:100%; border-collapse:collapse; margin-bottom:20px; font-size:11px; }
    .pay-table thead tr { background:#0ab39c; color:#fff; }
    .pay-table thead th { padding:8px 10px; text-align:left; font-weight:700; font-size:10px; text-transform:uppercase; letter-spacing:.5px; }
    .pay-table tbody tr:nth-child(even) { background:#f8f9fa; }
    .pay-table tbody td { padding:7px 10px; border-bottom:1px solid #e9ebec; }

    /* ── Résumé final ── */
    .summary-box { background:linear-gradient(135deg,#2d3a63,#405189); color:#fff; border-radius:10px; padding:16px 20px; margin-bottom:20px; display:table; width:100%; }
    .sum-cell { display:table-cell; text-align:center; vertical-align:middle; }
    .sum-val { font-size:18px; font-weight:800; }
    .sum-label { font-size:10px; opacity:.75; margin-top:3px; text-transform:uppercase; letter-spacing:.5px; }

    /* ── Footer ── */
    .footer { border-top:1px dashed #e9ebec; padding-top:10px; margin-top:10px; display:table; width:100%; }
    .footer-left  { display:table-cell; font-size:10px; color:#878a99; }
    .footer-right { display:table-cell; text-align:right; font-size:10px; color:#878a99; }
    .footer-note  { font-size:10px; color:#878a99; text-align:center; margin-top:6px; font-style:italic; }

    /* ── Filigrane ── */
    .watermark { position:fixed; top:45%; left:50%; transform:translate(-50%,-50%) rotate(-35deg); font-size:80px; font-weight:900; color:rgba(64,81,137,.05); letter-spacing:6px; pointer-events:none; text-transform:uppercase; white-space:nowrap; }

    /* Page break */
    .page-break { page-break-after:always; }
  </style>
</head>
<body>
<div class="page">

  <div class="watermark">CMRP</div>

  <!-- ── Header ── -->
  <div class="header">
    <div class="header-left">
      <div class="org-name">🕌 CMRP</div>
      <div class="org-sub">Comité des Mosquées de la Riviera Palmeraie</div>
      <div class="org-contact">Riviera Palmeraie, Abidjan — Côte d'Ivoire</div>
    </div>
    <div class="header-right">
      <div class="bilan-badge">Bilan Fidèle</div>
      <div class="periode-label">Période : {{ $periode }}</div>
      <div style="font-size:10px;color:#878a99;margin-top:4px">Généré le {{ now()->translatedFormat('d F Y à H:i') }}</div>
    </div>
  </div>

  <!-- ── Fidèle ── -->
  <div class="fidele-box">
    <div class="fidele-left">
      <div class="fidele-name">{{ $customer->prenom }} {{ $customer->nom }}</div>
      <div class="fidele-meta">{{ $customer->dial_code }} {{ $customer->phone }}</div>
      @if($customer->matricule)
      <div class="fidele-meta">Matricule : {{ $customer->matricule }}</div>
      @endif
      @if($customer->typeCotisationMensuel)
      <div class="fidele-meta">Type : {{ $customer->typeCotisationMensuel->libelle }}
        @if($customer->montant_engagement) — {{ number_format($customer->montant_engagement, 0, ',', ' ') }} FCFA/mois @endif
      </div>
      @endif
    </div>
    <div class="fidele-right">
      @if($customer->adresse)
      <div style="font-size:11px;color:#878a99">{{ $customer->adresse }}</div>
      @endif
      @if($customer->date_adhesion)
      <div style="font-size:10px;color:#878a99;margin-top:3px">Membre depuis {{ $customer->date_adhesion->translatedFormat('F Y') }}</div>
      @endif
    </div>
  </div>

  <!-- ── KPIs ── -->
  <table class="kpi-strip">
    <tr>
      <td class="kpi-box" style="background:rgba(10,179,156,.08)">
        <div class="kpi-val" style="color:#0ab39c">{{ number_format($totalPaye, 0, ',', ' ') }} F</div>
        <div class="kpi-label">Total payé</div>
      </td>
      <td class="kpi-box" style="background:rgba(240,101,72,.08)">
        <div class="kpi-val" style="color:#f06548">{{ number_format($totalDu, 0, ',', ' ') }} F</div>
        <div class="kpi-label">Montant dû</div>
      </td>
      <td class="kpi-box" style="background:rgba(10,179,156,.06)">
        <div class="kpi-val" style="color:#0ab39c">{{ $nbAjour }}</div>
        <div class="kpi-label">À jour</div>
      </td>
      <td class="kpi-box" style="background:rgba(247,184,75,.08)">
        <div class="kpi-val" style="color:#f7b84b">{{ $nbPartiel }}</div>
        <div class="kpi-label">Partiel</div>
      </td>
      <td class="kpi-box" style="background:rgba(240,101,72,.06)">
        <div class="kpi-val" style="color:#f06548">{{ $nbRetard }}</div>
        <div class="kpi-label">En retard</div>
      </td>
      <td class="kpi-box" style="background:rgba(64,81,137,.06)">
        <div class="kpi-val" style="color:#405189">{{ $cotisations->count() }}</div>
        <div class="kpi-label">Cotisations</div>
      </td>
    </tr>
  </table>

  <!-- ── Cotisations ── -->
  <div class="section-title">Historique des cotisations ({{ $cotisations->count() }})</div>
  @if($cotisations->count())
  <table class="cot-table">
    <thead>
      <tr>
        <th>Période</th>
        <th>Type</th>
        <th class="text-right">Montant dû</th>
        <th class="text-right">Montant payé</th>
        <th class="text-right">Restant</th>
        <th class="text-center">Statut</th>
        <th class="text-center">Validé</th>
      </tr>
    </thead>
    <tbody>
      @foreach($cotisations as $cot)
      <tr>
        <td>
          @if($cot->mois && $cot->annee)
            {{ \Carbon\Carbon::create($cot->annee, $cot->mois)->translatedFormat('F Y') }}
          @else
            {{ $cot->created_at->translatedFormat('d M Y') }}
          @endif
        </td>
        <td>{{ $cot->typeCotisation?->libelle ?? '—' }}</td>
        <td class="text-right">{{ $cot->montant_du ? number_format($cot->montant_du, 0, ',', ' ') : '—' }}</td>
        <td class="text-right" style="color:#0ab39c;font-weight:700">
          {{ number_format($cot->montant_paye, 0, ',', ' ') }}
        </td>
        <td class="text-right" style="{{ $cot->montant_restant > 0 ? 'color:#f06548' : 'color:#878a99' }}">
          {{ $cot->montant_restant > 0 ? number_format($cot->montant_restant, 0, ',', ' ') : '—' }}
        </td>
        <td class="text-center">
          @if($cot->statut === 'a_jour')
            <span class="pill-ok">✓ À jour</span>
          @elseif($cot->statut === 'partiel')
            <span class="pill-warn">◑ Partiel</span>
          @else
            <span class="pill-danger">⚠ Retard</span>
          @endif
        </td>
        <td class="text-center" style="color:#878a99">
          {{ $cot->validated_at ? $cot->validated_at->format('d/m/Y') : '—' }}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <p style="color:#878a99;font-size:12px;margin-bottom:16px">Aucune cotisation sur cette période.</p>
  @endif

  <!-- ── Paiements validés ── -->
  <div class="section-title">Paiements validés ({{ $paiements->count() }})</div>
  @if($paiements->count())
  <table class="pay-table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Référence</th>
        <th>Type</th>
        <th>Mode</th>
        <th class="text-right">Montant</th>
      </tr>
    </thead>
    <tbody>
      @foreach($paiements as $p)
      <tr>
        <td>{{ $p->date_paiement->format('d/m/Y') }}</td>
        <td class="mono">{{ $p->reference ?? 'PAY-' . str_pad($p->id, 6, '0', STR_PAD_LEFT) }}</td>
        <td>{{ $p->cotisation?->typeCotisation?->libelle ?? '—' }}</td>
        <td>{{ match($p->mode_paiement){ 'mobile_money'=>'Mobile Money','espece'=>'Espèces','virement'=>'Virement',default=>'—'} }}</td>
        <td class="text-right" style="color:#0ab39c;font-weight:700">+{{ number_format($p->montant, 0, ',', ' ') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <p style="color:#878a99;font-size:12px;margin-bottom:16px">Aucun paiement validé sur cette période.</p>
  @endif

  <!-- ── Résumé ── -->
  <table class="summary-box">
    <tr>
      <td class="sum-cell">
        <div class="sum-val">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</div>
        <div class="sum-label">Total payé</div>
      </td>
      <td class="sum-cell" style="border-left:1px solid rgba(255,255,255,.2)">
        <div class="sum-val" style="color:{{ $totalDu > 0 ? '#f7b84b' : '#fff' }}">
          {{ $totalDu > 0 ? number_format($totalDu, 0, ',', ' ').' FCFA' : 'À jour ✓' }}
        </div>
        <div class="sum-label">Montant dû</div>
      </td>
      <td class="sum-cell" style="border-left:1px solid rgba(255,255,255,.2)">
        <div class="sum-val">{{ $cotisations->count() }}</div>
        <div class="sum-label">Cotisations</div>
      </td>
      <td class="sum-cell" style="border-left:1px solid rgba(255,255,255,.2)">
        <div class="sum-val">{{ $paiements->count() }}</div>
        <div class="sum-label">Paiements</div>
      </td>
    </tr>
  </table>

  <!-- ── Footer ── -->
  <div class="footer">
    <div class="footer-left">CMRP · Riviera Palmeraie · Abidjan, Côte d'Ivoire</div>
    <div class="footer-right">Généré le {{ now()->translatedFormat('d F Y à H:i') }}</div>
  </div>
  <div class="footer-note">
    Ce document est généré automatiquement. Pour toute contestation, contactez l'administration du CMRP.
  </div>

</div>
</body>
</html>
