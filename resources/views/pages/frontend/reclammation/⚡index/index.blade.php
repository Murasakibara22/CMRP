<main class="page-content">
 
      <!-- En-tête page -->
      <div class="recla-page-header">
        <div>
          <div class="page-title">Mes Réclamations</div>
          <div class="page-sub" style="margin-bottom:0">Signalez un problème lié à vos cotisations ou à votre compte.</div>
        </div>
        <button class="btn-add-recla" onclick="openAddModal()">
          <i class="ri-add-line"></i> Nouvelle
        </button>
      </div>
 
      <!-- Filtres -->
      <div class="recla-filters">
        <button class="recla-filter active" data-filter="tous" onclick="filterRecla(this)">
          <i class="ri-list-check"></i> Toutes
        </button>
        <button class="recla-filter" data-filter="en_cours" onclick="filterRecla(this)">
          <i class="ri-time-line"></i> En cours
        </button>
        <button class="recla-filter" data-filter="resolu" onclick="filterRecla(this)">
          <i class="ri-checkbox-circle-line"></i> Résolus
        </button>
        <button class="recla-filter" data-filter="rejete" onclick="filterRecla(this)">
          <i class="ri-close-circle-line"></i> Rejetés
        </button>
      </div>
 
      <!-- ── LISTE DES RÉCLAMATIONS ── -->
      <div class="recla-list">
 
        <!-- Réclamation 1 -->
        <div class="recla-item" data-statut="en_cours" onclick="openDetailModal({
          id:'REC-2025-001',
          titre:'Paiement non enregistré — Fév. 2025',
          cotisation:'Cotisation mensuelle — Février 2025',
          message:'J\'ai effectué un paiement de 10 000 FCFA via Orange Money le 01/02/2025 (réf. OM20250101) mais il n\'apparaît pas dans mon historique. Merci de vérifier.',
          date:'28/03/2025',
          statut:'en_cours',
          reponse:''
        })">
          <div class="ri-left">
            <div class="ri-icon" style="background:rgba(41,156,219,.12);color:#299cdb">
              <i class="ri-flag-line"></i>
            </div>
          </div>
          <div class="ri-body">
            <div class="ri-header">
              <div class="ri-title">Paiement non enregistré — Fév. 2025</div>
              <span class="pill pill-info">En cours</span>
            </div>
            <div class="ri-cot"><i class="ri-calendar-line"></i> Cotisation mensuelle — Février 2025</div>
            <div class="ri-msg">J'ai effectué un paiement de 10 000 FCFA via Orange Money le 01/02/2025 mais il n'apparaît pas dans mon historique…</div>
            <div class="ri-date"><i class="ri-time-line"></i> Soumise le 28/03/2025</div>
          </div>
        </div>
 
        <!-- Réclamation 2 -->
        <div class="recla-item" data-statut="resolu" onclick="openDetailModal({
          id:'REC-2025-002',
          titre:'Erreur sur le montant — Janv. 2025',
          cotisation:'Cotisation mensuelle — Janvier 2025',
          message:'Le montant déduit de mon compte était de 15 000 FCFA au lieu de 10 000 FCFA. Je demande une correction.',
          date:'15/02/2025',
          statut:'resolu',
          reponse:'Après vérification, une erreur de saisie a été identifiée. Le montant a été corrigé et le surplus de 5 000 FCFA crédité sur votre prochain mois. Merci de votre confiance.'
        })">
          <div class="ri-left">
            <div class="ri-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
              <i class="ri-check-double-line"></i>
            </div>
          </div>
          <div class="ri-body">
            <div class="ri-header">
              <div class="ri-title">Erreur sur le montant — Janv. 2025</div>
              <span class="pill pill-ok">Résolu</span>
            </div>
            <div class="ri-cot"><i class="ri-calendar-line"></i> Cotisation mensuelle — Janvier 2025</div>
            <div class="ri-msg">Le montant déduit était de 15 000 FCFA au lieu de 10 000 FCFA. Demande de correction envoyée…</div>
            <div class="ri-date"><i class="ri-time-line"></i> Soumise le 15/02/2025 · Résolue le 18/02/2025</div>
          </div>
        </div>
 
        <!-- Réclamation 3 -->
        <div class="recla-item" data-statut="rejete" onclick="openDetailModal({
          id:'REC-2024-003',
          titre:'Demande d\'exonération — Déc. 2024',
          cotisation:'',
          message:'En raison de difficultés financières temporaires, je souhaite demander une exonération de cotisation pour le mois de décembre 2024.',
          date:'20/12/2024',
          statut:'rejete',
          reponse:'Après examen par le comité, votre demande d\'exonération ne peut être accordée pour cette période. Veuillez contacter l\'administration pour un arrangement de paiement.'
        })">
          <div class="ri-left">
            <div class="ri-icon" style="background:rgba(240,101,72,.10);color:#f06548">
              <i class="ri-close-circle-line"></i>
            </div>
          </div>
          <div class="ri-body">
            <div class="ri-header">
              <div class="ri-title">Demande d'exonération — Déc. 2024</div>
              <span class="pill pill-danger">Rejeté</span>
            </div>
            <div class="ri-cot"><i class="ri-information-line"></i> Sans cotisation liée</div>
            <div class="ri-msg">En raison de difficultés financières temporaires, demande d'exonération pour décembre 2024…</div>
            <div class="ri-date"><i class="ri-time-line"></i> Soumise le 20/12/2024 · Rejetée le 22/12/2024</div>
          </div>
        </div>
 
        <!-- Empty state (filtre) -->
        <div class="empty-state" id="recla-empty" style="display:none">
          <i class="ri-flag-line"></i>
          <div class="es-title">Aucune réclamation</div>
          <div class="es-sub">Aucune réclamation ne correspond à ce filtre.</div>
        </div>
 
      </div><!-- /recla-list -->
 
      <div style="height:24px"></div>
    </main>


    @push('modal')

     <!-- ══════════════════════════════════════════════════════
     MODAL NOUVELLE RÉCLAMATION
══════════════════════════════════════════════════════ -->
<div class="pwa-modal-overlay" id="add-overlay" onclick="closeAddModal()">
  <div class="pwa-modal" onclick="event.stopPropagation()">
    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-flag-line"></i> Nouvelle réclamation</div>
        <button class="pwa-modal-close" onclick="closeAddModal()"><i class="ri-close-line"></i></button>
      </div>
    </div>
    <div class="pwa-modal-body">
 
      <div class="f-group">
        <label class="f-label">Cotisation concernée <span class="opt">(optionnel)</span></label>
        <div class="f-input-wrap">
          <i class="ri-calendar-line f-input-icon"></i>
          <select class="f-input" id="add-cotisation">
            <option value="">— Aucune cotisation spécifique —</option>
            <option value="avr2025">Cotisation mensuelle — Avril 2025</option>
            <option value="mar2025">Cotisation mensuelle — Mars 2025</option>
            <option value="fev2025">Cotisation mensuelle — Février 2025</option>
            <option value="jan2025">Cotisation mensuelle — Janvier 2025</option>
            <option value="quete">Quête du vendredi — Mars 2025</option>
          </select>
        </div>
      </div>
 
      <div class="f-group">
        <label class="f-label">Titre <span class="req">*</span></label>
        <div class="f-input-wrap">
          <i class="ri-text f-input-icon"></i>
          <input type="text" class="f-input" id="add-titre"
            placeholder="ex : Paiement non enregistré"/>
        </div>
      </div>
 
      <div class="f-group">
        <label class="f-label">Description du problème <span class="req">*</span></label>
        <textarea class="f-input" id="add-message"
          placeholder="Décrivez votre problème en détail. Plus vous êtes précis, plus vite nous pourrons vous aider."></textarea>
      </div>
 
      <div class="recla-info-note">
        <i class="ri-information-line"></i>
        Votre réclamation sera traitée par un administrateur dans les plus brefs délais. Vous recevrez une notification dès qu'une réponse sera disponible.
      </div>
 
    </div>
    <div class="pwa-modal-footer">
      <button class="btn-outline" style="height:46px;font-size:14px" onclick="closeAddModal()">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="btn-main" style="height:46px;font-size:14px" id="btn-submit-recla" onclick="submitRecla()">
        <i class="ri-send-plane-line"></i> Envoyer
      </button>
    </div>
  </div>
</div>
 
 
<!-- ══════════════════════════════════════════════════════
     MODAL DÉTAIL RÉCLAMATION
══════════════════════════════════════════════════════ -->
<div class="pwa-modal-overlay" id="detail-overlay" onclick="closeDetailModal()">
  <div class="pwa-modal" onclick="event.stopPropagation()">
    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-eye-line"></i> Détail</div>
        <button class="pwa-modal-close" onclick="closeDetailModal()"><i class="ri-close-line"></i></button>
      </div>
    </div>
    <div class="pwa-modal-body">
 
      <!-- ID + statut -->
      <div class="recla-detail-meta">
        <span class="recla-detail-id" id="det-id">—</span>
        <span id="det-statut-pill"></span>
      </div>
 
      <!-- Titre -->
      <div class="recla-detail-title" id="det-titre">—</div>
 
      <!-- Cotisation liée -->
      <div class="recla-detail-cot" id="det-cot-wrap">
        <i class="ri-calendar-line"></i>
        <span id="det-cotisation">—</span>
      </div>
 
      <!-- Date -->
      <div class="recla-detail-date" id="det-date">—</div>
 
      <!-- Message -->
      <div class="recla-section-label">Votre message</div>
      <div class="recla-detail-msg" id="det-message">—</div>
 
      <!-- Réponse admin -->
      <div id="det-reponse-wrap">
        <div class="recla-section-label">Réponse de l'administration</div>
        <div class="recla-reponse-box" id="det-reponse">—</div>
      </div>
 
    </div>
    <div class="pwa-modal-footer">
      <button class="btn-main" style="height:46px;font-size:14px;flex:1" onclick="closeDetailModal()">
        <i class="ri-check-line"></i> Fermer
      </button>
    </div>
  </div>
</div>

    @endpush