<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<title>Reçu de Paiement — ISL Mosquée</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DejaVu Sans',sans-serif; font-size:11px; color:#1a1d2e; background:#fff; width:148mm; margin:0 auto; }

  /* Header */
  .recu-header {
    background: linear-gradient(135deg,#2d3a63,#405189);
    padding: 24px 28px;
    color: #fff;
    text-align: center;
    position: relative;
  }
  .recu-logo { font-size: 36px; margin-bottom:6px; }
  .recu-mosque { font-size:18px; font-weight:700; }
  .recu-sub { font-size:10px; color:rgba(255,255,255,.65); margin-top:2px; }
  .recu-badge {
    display:inline-block;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 20px;
    padding: 4px 16px;
    font-size: 11px;
    font-weight: 700;
    margin-top: 12px;
    letter-spacing: .5px;
  }

  /* Référence */
  .recu-ref-block {
    background: #f8f9fc;
    border: 2px dashed #405189;
    border-radius: 10px;
    margin: 20px 24px;
    padding: 14px 20px;
    text-align: center;
  }
  .ref-label { font-size:10px; color:#878a99; text-transform:uppercase; letter-spacing:.8px; margin-bottom:4px; }
  .ref-val { font-size:20px; font-weight:700; color:#405189; font-family:'DejaVu Sans Mono',monospace; letter-spacing:1px; }

  /* Montant central */
  .montant-block {
    text-align: center;
    margin: 16px 24px;
    padding: 18px;
    border-radius: 10px;
    background: linear-gradient(135deg,rgba(10,179,156,.08),rgba(10,179,156,.04));
    border: 1px solid rgba(10,179,156,.2);
  }
  .montant-label { font-size:10px; color:#878a99; text-transform:uppercase; letter-spacing:.8px; margin-bottom:6px; }
  .montant-val { font-size:32px; font-weight:700; color:#0ab39c; font-family:'DejaVu Sans Mono',monospace; }
  .montant-statut {
    display:inline-block;
    background: rgba(10,179,156,.12);
    color: #0ab39c;
    border-radius: 12px;
    padding: 3px 12px;
    font-size:10px; font-weight:700;
    margin-top: 8px;
  }

  /* Infos grille */
  .info-grid {
    margin: 0 24px 16px;
    border: 1px solid #e9ebec;
    border-radius: 10px;
    overflow: hidden;
  }
  .info-row {
    display: flex;
    border-bottom: 1px solid #e9ebec;
    font-size: 11px;
  }
  .info-row:last-child { border-bottom:none; }
  .info-row:nth-child(even) { background:#f8f9fc; }
  .info-label {
    flex: 0 0 120px;
    padding: 9px 12px;
    color: #878a99;
    font-weight:600;
    font-size:10px;
    text-transform:uppercase;
    letter-spacing:.3px;
    border-right: 1px solid #e9ebec;
    background: rgba(64,81,137,.03);
  }
  .info-val {
    flex: 1;
    padding: 9px 14px;
    color: #212529;
    font-weight:600;
  }

  /* Fidèle bloc */
  .fidele-bloc {
    margin: 0 24px 16px;
    background: rgba(64,81,137,.05);
    border-left: 4px solid #405189;
    border-radius: 0 8px 8px 0;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .fidele-av {
    width:44px; height:44px; border-radius:50%;
    background:#405189; color:#fff;
    font-size:16px; font-weight:700;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
  }
  .fidele-name { font-size:14px; font-weight:700; color:#212529; }
  .fidele-phone { font-size:11px; color:#878a99; margin-top:2px; }

  /* Watermark */
  .watermark {
    text-align:center;
    margin:16px 24px 0;
    padding:10px;
    background:rgba(10,179,156,.04);
    border:1px solid rgba(10,179,156,.15);
    border-radius:8px;
    font-size:10px;
    color:#0ab39c;
  }

  /* Footer */
  .recu-footer {
    border-top:1px solid #e9ebec;
    padding:12px 24px;
    margin-top:16px;
    text-align:center;
    font-size:9.5px;
    color:#878a99;
    line-height:1.6;
  }

  .text-mono { font-family:'DejaVu Sans Mono',monospace; }
</style>
</head>
<body>

{{-- ══ HEADER ══ --}}
{{-- <div class="recu-header">
  <div class="recu-logo">🕌</div>
  <div class="recu-mosque">ISL Mosquée</div>
  <div class="recu-sub">Gestion des cotisations et paiements</div>
  <div class="recu-badge">✅ REÇU DE PAIEMENT OFFICIEL</div>
</div> --}}

{{-- ══ RÉFÉRENCE ══ --}}
<div class="recu-ref-block">
  <div class="ref-label">Numéro de référence</div>
  <div class="ref-val">{{ $paiement->reference ?? 'PAY-'.str_pad($paiement->id,6,'0',STR_PAD_LEFT) }}</div>
</div>

{{-- ══ MONTANT ══ --}}
<div class="montant-block">
  <div class="montant-label">Montant du paiement</div>
  <div class="montant-val">{{ number_format($paiement->montant,0,',',' ') }} FCFA</div>
  <div class="montant-statut">✅ Paiement validé</div>
</div>

{{-- ══ FIDÈLE ══ --}}
@if($paiement->customer)
<div class="fidele-bloc">
  <div class="fidele-av">
    {{ strtoupper(substr($paiement->customer->prenom,0,1).substr($paiement->customer->nom,0,1)) }}
  </div>
  <div>
    <div class="fidele-name">{{ $paiement->customer->prenom }} {{ $paiement->customer->nom }}</div>
    <div class="fidele-phone">{{ $paiement->customer->dial_code }} {{ $paiement->customer->phone }}</div>
  </div>
</div>
@endif

{{-- ══ DÉTAILS ══ --}}
<div class="info-grid">
  <div class="info-row">
    <div class="info-label">Type</div>
    <div class="info-val">{{ $paiement->cotisation?->typeCotisation?->libelle ?? '—' }}</div>
  </div>
  @if($paiement->cotisation?->mois && $paiement->cotisation?->annee)
  <div class="info-row">
    <div class="info-label">Période</div>
    <div class="info-val">{{ \Carbon\Carbon::create($paiement->cotisation->annee, $paiement->cotisation->mois)->translatedFormat('F Y') }}</div>
  </div>
  @endif
  <div class="info-row">
    <div class="info-label">Date paiement</div>
    <div class="info-val">{{ $paiement->date_paiement->format('d/m/Y à H:i') }}</div>
  </div>
  <div class="info-row">
    <div class="info-label">Mode</div>
    <div class="info-val">
      {{ match($paiement->mode_paiement){ 'mobile_money'=>'📱 Mobile Money','espece'=>'💵 Espèces','virement'=>'🏦 Virement',default=>'—'} }}
    </div>
  </div>
  @if(isset($paiement->validated_by) && $paiement->validated_by)
  <div class="info-row">
    <div class="info-label">Validé par</div>
    <div class="info-val">Admin #{{ $paiement->validated_by }}</div>
  </div>
  @endif
  <div class="info-row">
    <div class="info-label">Émis le</div>
    <div class="info-val text-mono">{{ now()->format('d/m/Y H:i') }}</div>
  </div>
</div>

{{-- ══ WATERMARK ══ --}}
<div class="watermark">
  ✅ Ce reçu confirme la réception et la validation du paiement par ISL Mosquée.
  Conservez ce document à titre de justificatif.
</div>

{{-- ══ FOOTER ══ --}}
<div class="recu-footer">
  <strong style="color:#405189">ISL Mosquée</strong> — Système de gestion des cotisations<br>
  Ce document a valeur de reçu officiel · Généré le {{ now()->translatedFormat('d F Y à H:i') }}
</div>

</body>
</html>
