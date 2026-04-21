<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      font-family: 'DejaVu Sans', Arial, sans-serif;
      font-size: 13px;
      color: #212529;
      background: #fff;
      padding: 0;
    }

    /* ── Page ── */
    .page {
      width: 100%;
      min-height: 297mm;
      padding: 5mm 3mm;
      position: relative;
    }

    /* ── Header ── */
    .header {
      display: table;
      width: 100%;
      margin-bottom: 28px;
      border-bottom: 3px solid #405189;
      padding-bottom: 18px;
    }
    .header-left  { display: table-cell; vertical-align: middle; width: 60%; }
    .header-right { display: table-cell; vertical-align: middle; text-align: right; }

    .org-name {
      font-size: 20px;
      font-weight: 700;
      color: #405189;
      letter-spacing: .5px;
    }
    .org-sub {
      font-size: 11px;
      color: #878a99;
      margin-top: 3px;
    }
    .org-contact {
      font-size: 10px;
      color: #878a99;
      margin-top: 6px;
      line-height: 1.6;
    }

    .recu-badge {
      display: inline-block;
      background: #405189;
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      padding: 6px 16px;
      border-radius: 6px;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .recu-ref {
      font-size: 11px;
      color: #878a99;
      font-family: monospace;
    }

    /* ── Bandeau montant ── */
    .amount-banner {
      background: linear-gradient(135deg, #2d3a63, #405189);
      color: #fff;
      border-radius: 12px;
      padding: 22px 28px;
      margin-bottom: 28px;
      display: table;
      width: 100%;
    }
    .amount-left  { display: table-cell; vertical-align: middle; }
    .amount-right { display: table-cell; vertical-align: middle; text-align: right; }

    .amount-label { font-size: 11px; opacity: .75; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
    .amount-value { font-size: 28px; font-weight: 700; }
    .amount-sub   { font-size: 11px; opacity: .75; margin-top: 4px; }

    .statut-badge {
      display: inline-block;
      padding: 5px 14px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
      background: rgba(255,255,255,.2);
      color: #fff;
      border: 1.5px solid rgba(255,255,255,.4);
    }

    /* ── Section fidèle ── */
    .section-title {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #405189;
      margin-bottom: 10px;
      padding-bottom: 6px;
      border-bottom: 1px solid #e9ebec;
    }

    .fidele-box {
      background: #f8f9fa;
      border-radius: 8px;
      padding: 14px 16px;
      margin-bottom: 24px;
      display: table;
      width: 100%;
    }
    .fidele-left  { display: table-cell; vertical-align: middle; width: 70%; }
    .fidele-right { display: table-cell; vertical-align: middle; text-align: right; }
    .fidele-name  { font-size: 15px; font-weight: 700; color: #212529; }
    .fidele-phone { font-size: 11px; color: #878a99; margin-top: 2px; }
    .fidele-mat   { font-size: 10px; color: #878a99; margin-top: 2px; }

    /* ── Tableau détails ── */
    .details-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }
    .details-table tr:nth-child(even) td { background: #f8f9fa; }
    .details-table td {
      padding: 10px 12px;
      font-size: 12px;
      border-bottom: 1px solid #e9ebec;
    }
    .details-table td:first-child {
      color: #878a99;
      font-weight: 600;
      width: 38%;
    }
    .details-table td:last-child {
      color: #212529;
      font-weight: 600;
    }
    .mono { font-family: monospace; font-size: 12px; }

    /* ── Footer ── */
    .footer {
      position: absolute;
      bottom: 14mm;
      left: 18mm;
      right: 18mm;
      border-top: 1px dashed #e9ebec;
      padding-top: 12px;
    }
    .footer-inner {
      display: table;
      width: 100%;
    }
    .footer-left  { display: table-cell; vertical-align: middle; font-size: 10px; color: #878a99; }
    .footer-right { display: table-cell; vertical-align: middle; text-align: right; font-size: 10px; color: #878a99; }

    .footer-note {
      font-size: 10px;
      color: #878a99;
      text-align: center;
      margin-top: 8px;
      font-style: italic;
    }

    /* ── Filigrane "PAYÉ" ── */
    .watermark {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-35deg);
      font-size: 90px;
      font-weight: 900;
      color: rgba(10, 179, 156, 0.07);
      letter-spacing: 8px;
      pointer-events: none;
      text-transform: uppercase;
      white-space: nowrap;
    }

    /* ── Séparateur décoratif ── */
    .sep {
      border: none;
      border-top: 1px dashed #e9ebec;
      margin: 18px 0;
    }

    .validity-box {
      background: rgba(10,179,156,.06);
      border: 1px solid rgba(10,179,156,.2);
      border-left: 4px solid #0ab39c;
      border-radius: 0 8px 8px 0;
      padding: 10px 14px;
      font-size: 11px;
      color: #089383;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<div class="page">

  <!-- Filigrane -->
  <div class="watermark">PAYÉ</div>

  <!-- ── Header ── -->
  <div class="header">
    <div class="header-left">
      <div class="org-name">🕌 CMRP</div>
      <div class="org-sub">Comité des Mosquées de la Riviera Palmeraie</div>
      <div class="org-contact">
        Riviera Palmeraie, Abidjan — Côte d'Ivoire<br>
        Espace Fidèle · cmrp.ci
      </div>
    </div>
    <div class="header-right">
      <div class="recu-badge">Reçu de paiement</div>
      <div class="recu-ref">Réf. {{ $ref }}</div>
      <div style="font-size:10px;color:#878a99;margin-top:4px">
        Émis le {{ $genereLe }}
      </div>
    </div>
  </div>

  <!-- ── Bandeau montant ── -->
  {{-- <div class="amount-banner">
    <div class="amount-left">
      <div class="amount-label">Montant payé</div>
      <div class="amount-value">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</div>
      <div class="amount-sub">{{ $typeLabel }}{{ $periodeLabel !== '—' ? ' — ' . $periodeLabel : '' }}</div>
    </div>
    <div class="amount-right">
      <div class="statut-badge">✓ Validé</div>
      <div style="font-size:10px;opacity:.7;margin-top:8px">
        {{ $paiement->date_paiement->format('d/m/Y') }}
      </div>
    </div>
  </div> --}}

  <!-- ── Fidèle ── -->
  <div class="section-title">Informations du fidèle</div>
  <div class="fidele-box">
    <div class="fidele-left">
      <div class="fidele-name">{{ $customer->prenom }} {{ $customer->nom }}</div>
      <div class="fidele-phone">{{ $customer->dial_code }} {{ $customer->phone }}</div>
      @if($customer->matricule)
      <div class="fidele-mat">Matricule : {{ $customer->matricule }}</div>
      @endif
    </div>
    <div class="fidele-right">
      @if($customer->adresse)
      <div style="font-size:11px;color:#878a99">{{ $customer->adresse }}</div>
      @endif
      @if($customer->date_adhesion)
      <div style="font-size:10px;color:#878a99;margin-top:4px">
        Membre depuis {{ $customer->date_adhesion->translatedFormat('F Y') }}
      </div>
      @endif
    </div>
  </div>

  <!-- ── Détails paiement ── -->
  <div class="section-title">Détails du paiement</div>
  <table class="details-table">
    <tr>
      <td>Référence</td>
      <td class="mono">{{ $ref }}</td>
    </tr>
    <tr>
      <td>Type de cotisation</td>
      <td>{{ $typeLabel }}</td>
    </tr>
    <tr>
      <td>Période</td>
      <td>{{ $periodeLabel }}</td>
    </tr>
    <tr>
      <td>Montant</td>
      <td><strong style="color:#405189;font-size:14px">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</strong></td>
    </tr>
    <tr>
      <td>Mode de paiement</td>
      <td>{{ $modeLabel }}</td>
    </tr>
    @if($operateur)
    <tr>
      <td>Opérateur</td>
      <td>{{ $operateur }}</td>
    </tr>
    @endif
    <tr>
      <td>Date du paiement</td>
      <td>{{ $paiement->date_paiement->translatedFormat('d F Y à H:i') }}</td>
    </tr>
    <tr>
      <td>Statut</td>
      <td><strong style="color:#0ab39c">✓ Validé</strong></td>
    </tr>
  </table>

  <!-- ── Note validité ── -->
  <div class="validity-box">
    Ce reçu constitue la preuve officielle de votre paiement auprès du CMRP.
    Conservez ce document pour vos archives. En cas de litige, contactez l'administration.
  </div>

  <!-- ── Footer ── -->
  <div class="footer">
    <div class="footer-inner">
      <div class="footer-left">
        CMRP · Riviera Palmeraie · Abidjan, Côte d'Ivoire
      </div>
      <div class="footer-right">
        Document généré le {{ $genereLe }}
      </div>
    </div>
    <div class="footer-note">
      Ce document est généré automatiquement par le système CMRP et ne nécessite pas de signature manuscrite.
    </div>
  </div>

</div>

</body>
</html>
