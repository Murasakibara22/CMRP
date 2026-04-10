<main class="page-content">
 
      <div class="page-title">Mes Paiements</div>
      <div class="page-sub">Historique de tous vos paiements enregistrés.</div>
 
      <!-- KPIs rapides -->
      <div class="pay-kpi-strip">
        <div class="pay-kpi">
          <div class="pay-kpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
          <div class="pay-kpi-val">9</div>
          <div class="pay-kpi-label">Succès</div>
        </div>
        <div class="pay-kpi">
          <div class="pay-kpi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
          <div class="pay-kpi-val">2</div>
          <div class="pay-kpi-label">En attente</div>
        </div>
        <div class="pay-kpi">
          <div class="pay-kpi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
          <div class="pay-kpi-val">1</div>
          <div class="pay-kpi-label">Échoué</div>
        </div>
        <div class="pay-kpi">
          <div class="pay-kpi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-money-cny-circle-line"></i></div>
          <div class="pay-kpi-val" style="font-size:14px">142 000</div>
          <div class="pay-kpi-label">FCFA total</div>
        </div>
      </div>
 
      <!-- Filtres statut -->
      <div class="pay-filters">
        <button class="pay-filter active" data-filter="tous" onclick="filterPay(this)">
          <i class="ri-list-check"></i> Tous
        </button>
        <button class="pay-filter" data-filter="success" onclick="filterPay(this)">
          <i class="ri-checkbox-circle-line"></i> Succès
        </button>
        <button class="pay-filter" data-filter="attente" onclick="filterPay(this)">
          <i class="ri-time-line"></i> En attente
        </button>
        <button class="pay-filter" data-filter="echec" onclick="filterPay(this)">
          <i class="ri-close-circle-line"></i> Échoué
        </button>
      </div>
 
      <!-- ── LISTE PAIEMENTS ── -->
      <div class="pay-list card">
 
        <!-- Paiement 1 -->
        <div class="pay-item" data-statut="success"
          onclick="openDetail({
            ref:'OM202504001', type:'Cotisation mensuelle', periode:'Avril 2025',
            montant:'10 000 FCFA', mode:'Mobile Money', operateur:'Orange Money',
            date:'02/04/2025 08:32', statut:'success',
            validated_by:'Admin Koné', validated_at:'02/04/2025 09:15',
            note:''
          })">
          <div class="pay-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
            <i class="ri-smartphone-line"></i>
          </div>
          <div class="pay-body">
            <div class="pay-title">Cotisation mensuelle — Avril 2025</div>
            <div class="pay-meta">02/04/2025 · Orange Money · <span class="pay-ref">OM202504001</span></div>
          </div>
          <div class="pay-right">
            <div class="pay-amount" style="color:#0ab39c">+10 000</div>
            <span class="pill pill-ok pay-pill">Succès</span>
          </div>
        </div>
 
        <!-- Paiement 2 -->
        <div class="pay-item" data-statut="attente"
          onclick="openDetail({
            ref:'ESP202504002', type:'Cotisation mensuelle', periode:'Mars 2025',
            montant:'10 000 FCFA', mode:'Espèces', operateur:'—',
            date:'05/04/2025 14:20', statut:'attente',
            validated_by:'—', validated_at:'—',
            note:'En attente de validation par un administrateur.'
          })">
          <div class="pay-icon" style="background:rgba(247,184,75,.12);color:#f7b84b">
            <i class="ri-money-dollar-circle-line"></i>
          </div>
          <div class="pay-body">
            <div class="pay-title">Cotisation mensuelle — Mars 2025</div>
            <div class="pay-meta">05/04/2025 · Espèces · <span class="pay-ref">ESP202504002</span></div>
          </div>
          <div class="pay-right">
            <div class="pay-amount" style="color:#f7b84b">10 000</div>
            <span class="pill pill-warn pay-pill">En attente</span>
          </div>
        </div>
 
        <!-- Paiement 3 -->
        <div class="pay-item" data-statut="success"
          onclick="openDetail({
            ref:'OM202503003', type:'Quête du vendredi', periode:'Mars 2025',
            montant:'2 000 FCFA', mode:'Mobile Money', operateur:'MTN Mobile Money',
            date:'21/03/2025 13:45', statut:'success',
            validated_by:'Système automatique', validated_at:'21/03/2025 13:46',
            note:''
          })">
          <div class="pay-icon" style="background:rgba(212,168,67,.12);color:#d4a843">
            <i class="ri-smartphone-line"></i>
          </div>
          <div class="pay-body">
            <div class="pay-title">Quête du vendredi — Mars 2025</div>
            <div class="pay-meta">21/03/2025 · MTN Mobile Money · <span class="pay-ref">OM202503003</span></div>
          </div>
          <div class="pay-right">
            <div class="pay-amount" style="color:#0ab39c">+2 000</div>
            <span class="pill pill-ok pay-pill">Succès</span>
          </div>
        </div>
 
        <!-- Paiement 4 -->
        <div class="pay-item" data-statut="echec"
          onclick="openDetail({
            ref:'FAIL202503004', type:'Cotisation mensuelle', periode:'Mars 2025',
            montant:'10 000 FCFA', mode:'Mobile Money', operateur:'Orange Money',
            date:'28/03/2025 17:45', statut:'echec',
            validated_by:'—', validated_at:'—',
            note:'Transaction rejetée — solde insuffisant.'
          })">
          <div class="pay-icon" style="background:rgba(240,101,72,.10);color:#f06548">
            <i class="ri-close-circle-line"></i>
          </div>
          <div class="pay-body">
            <div class="pay-title">Cotisation mensuelle — Mars 2025</div>
            <div class="pay-meta">28/03/2025 · Orange Money · <span class="pay-ref">FAIL202503004</span></div>
          </div>
          <div class="pay-right">
            <div class="pay-amount" style="color:#f06548">10 000</div>
            <span class="pill pill-danger pay-pill">Échoué</span>
          </div>
        </div>
 
        <!-- Paiement 5 -->
        <div class="pay-item" data-statut="success"
          onclick="openDetail({
            ref:'OM202502005', type:'Ramadan 1446', periode:'Février 2025',
            montant:'5 000 FCFA', mode:'Mobile Money', operateur:'Orange Money',
            date:'15/02/2025 20:00', statut:'success',
            validated_by:'Système automatique', validated_at:'15/02/2025 20:01',
            note:''
          })">
          <div class="pay-icon" style="background:rgba(41,156,219,.12);color:#299cdb">
            <i class="ri-smartphone-line"></i>
          </div>
          <div class="pay-body">
            <div class="pay-title">Ramadan 1446 — Février 2025</div>
            <div class="pay-meta">15/02/2025 · Orange Money · <span class="pay-ref">OM202502005</span></div>
          </div>
          <div class="pay-right">
            <div class="pay-amount" style="color:#0ab39c">+5 000</div>
            <span class="pill pill-ok pay-pill">Succès</span>
          </div>
        </div>
 
        <!-- Paiement 6 -->
        <div class="pay-item" data-statut="success"
          onclick="openDetail({
            ref:'ESP202502006', type:'Cotisation mensuelle', periode:'Février 2025',
            montant:'10 000 FCFA', mode:'Espèces', operateur:'—',
            date:'01/02/2025 09:00', statut:'success',
            validated_by:'Admin Koné', validated_at:'01/02/2025 10:30',
            note:''
          })">
          <div class="pay-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
            <i class="ri-money-dollar-circle-line"></i>
          </div>
          <div class="pay-body">
            <div class="pay-title">Cotisation mensuelle — Février 2025</div>
            <div class="pay-meta">01/02/2025 · Espèces · <span class="pay-ref">ESP202502006</span></div>
          </div>
          <div class="pay-right">
            <div class="pay-amount" style="color:#0ab39c">+10 000</div>
            <span class="pill pill-ok pay-pill">Succès</span>
          </div>
        </div>
 
        <!-- Paiement 7 -->
        <div class="pay-item" data-statut="attente"
          onclick="openDetail({
            ref:'VIR202501007', type:'Cotisation mensuelle', periode:'Janvier 2025',
            montant:'10 000 FCFA', mode:'Virement', operateur:'SIB Côte d\'Ivoire',
            date:'03/01/2025 11:00', statut:'attente',
            validated_by:'—', validated_at:'—',
            note:'Virement reçu, en attente de rapprochement bancaire.'
          })">
          <div class="pay-icon" style="background:rgba(64,81,137,.10);color:#405189">
            <i class="ri-bank-line"></i>
          </div>
          <div class="pay-body">
            <div class="pay-title">Cotisation mensuelle — Janvier 2025</div>
            <div class="pay-meta">03/01/2025 · Virement · <span class="pay-ref">VIR202501007</span></div>
          </div>
          <div class="pay-right">
            <div class="pay-amount" style="color:#f7b84b">10 000</div>
            <span class="pill pill-warn pay-pill">En attente</span>
          </div>
        </div>
 
      </div><!-- /pay-list -->
 
      <div style="height:24px"></div>
    </main>



@push('modal')


<div class="pay-modal-overlay" id="pay-modal-overlay" onclick="closeDetail()">
  <div class="pay-modal" onclick="event.stopPropagation()">
 
    <!-- Header dynamique -->
    <div class="pay-modal-header" id="pay-modal-header">
      <div class="pmh-inner">
        <div class="pmh-icon" id="pmh-icon"><i class="ri-bank-card-line"></i></div>
        <div>
          <div class="pmh-title" id="pmh-title">Détail du paiement</div>
          <div class="pmh-ref"   id="pmh-ref">—</div>
        </div>
      </div>
      <button class="pmh-close" onclick="closeDetail()"><i class="ri-close-line"></i></button>
    </div>
 
    <!-- Corps -->
    <div class="pay-modal-body">
 
      <!-- Montant central -->
      <div class="pmd-amount-wrap">
        <div class="pmd-amount" id="pmd-amount">—</div>
        <div class="pmd-statut" id="pmd-statut"></div>
      </div>
 
      <!-- Grille infos -->
      <div class="pmd-grid">
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-tag-line"></i> Type</div>
          <div class="pmd-value" id="pmd-type">—</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-calendar-line"></i> Période</div>
          <div class="pmd-value" id="pmd-periode">—</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-smartphone-line"></i> Mode</div>
          <div class="pmd-value" id="pmd-mode">—</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-bank-line"></i> Opérateur</div>
          <div class="pmd-value" id="pmd-operateur">—</div>
        </div>
        <div class="pmd-item pmd-full">
          <div class="pmd-label"><i class="ri-hashtag"></i> Référence</div>
          <div class="pmd-value pmd-mono" id="pmd-ref-val">—</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-time-line"></i> Date paiement</div>
          <div class="pmd-value" id="pmd-date">—</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-shield-check-line"></i> Validé par</div>
          <div class="pmd-value" id="pmd-validated-by">—</div>
        </div>
        <div class="pmd-item">
          <div class="pmd-label"><i class="ri-time-line"></i> Validé le</div>
          <div class="pmd-value" id="pmd-validated-at">—</div>
        </div>
        <div class="pmd-item pmd-full" id="pmd-note-wrap" style="display:none">
          <div class="pmd-label"><i class="ri-information-line"></i> Note</div>
          <div class="pmd-value pmd-note" id="pmd-note">—</div>
        </div>
      </div>
 
    </div>
 
    <!-- Footer -->
    <div class="pay-modal-footer">
      <button class="btn-outline" style="height:46px;font-size:14px" onclick="closeDetail()">
        <i class="ri-close-line"></i> Fermer
      </button>
      <button class="btn-main" style="height:46px;font-size:14px" onclick="printDetail()">
        <i class="ri-printer-line"></i> Imprimer / PDF
      </button>
    </div>
 
  </div>
</div>
 
<!-- Zone d'impression -->
<div class="print-zone" id="print-zone" style="display:none">
  <div class="print-header">
    <div class="print-logo">🕌 ISL Mosquée — Espace Fidèle</div>
    <div class="print-title">Reçu de paiement</div>
  </div>
  <div class="print-fidele">
    Fidèle : <strong>Moussa Koné</strong> — +225 07 00 11 22
  </div>
  <table class="print-table" id="print-table">
    <tr><td>Référence</td><td id="pr-ref">—</td></tr>
    <tr><td>Type</td><td id="pr-type">—</td></tr>
    <tr><td>Période</td><td id="pr-periode">—</td></tr>
    <tr><td>Montant</td><td id="pr-montant">—</td></tr>
    <tr><td>Mode de paiement</td><td id="pr-mode">—</td></tr>
    <tr><td>Opérateur</td><td id="pr-operateur">—</td></tr>
    <tr><td>Date</td><td id="pr-date">—</td></tr>
    <tr><td>Validé par</td><td id="pr-valby">—</td></tr>
    <tr><td>Validé le</td><td id="pr-valat">—</td></tr>
    <tr><td>Statut</td><td id="pr-statut">—</td></tr>
  </table>
  <div class="print-footer">
    Document généré le <span id="pr-now"></span> — ISL Mosquée © 2025
  </div>
</div>

@endpush


@push('scripts')
<script src="{{ asset('frontend/js/paiement.js') }}"></script>
@endpush