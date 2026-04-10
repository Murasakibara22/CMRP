
<main class="page-content">
 
    <!-- Résumé rapide -->
    <div class="summary-strip">
      <div class="sum-item">
        <div class="sum-val" style="color:#f06548">3</div>
        <div class="sum-label">En retard</div>
      </div>
      <div class="sum-divider"></div>
      <div class="sum-item">
        <div class="sum-val" style="color:#0ab39c">8</div>
        <div class="sum-label">À jour</div>
      </div>
      <div class="sum-divider"></div>
      <div class="sum-item">
        <div class="sum-val" style="color:#f7b84b">1</div>
        <div class="sum-label">Partiel</div>
      </div>
      <div class="sum-divider"></div>
      <div class="sum-item">
        <div class="sum-val">12</div>
        <div class="sum-label">Total</div>
      </div>
    </div>
 
    <!-- Filtres statut -->
    <div class="status-tabs">
      <button class="stab active" data-statut="tous" onclick="filterCot(this)">Tous</button>
      <button class="stab" data-statut="retard"  onclick="filterCot(this)">
        <i class="ri-time-line"></i> En retard
        <span class="stab-count danger">3</span>
      </button>
      <button class="stab" data-statut="ajour" onclick="filterCot(this)">
        <i class="ri-checkbox-circle-line"></i> À jour
      </button>
      <button class="stab" data-statut="partiel" onclick="filterCot(this)">
        <i class="ri-error-warning-line"></i> Partiel
      </button>
    </div>
 
    <!-- ─── LISTE DES COTISATIONS ─── -->
 
    <!-- Mois : Avril 2025 -->
    <div class="cot-month-label">Avril 2025</div>
 
    <div class="cot-item" data-statut="retard" onclick="openDetail(this)"
      data-type="Cotisation mensuelle" data-type-icon="ri-calendar-check-line"
      data-periode="Avril 2025" data-montant-du="10 000 FCFA"
      data-montant-paye="0 FCFA" data-restant="10 000 FCFA" data-pct="0"
      data-engagement="10 000 FCFA/mois" data-mode="—" data-created="01/04/2025">
      <div class="cot-left">
        <div class="cot-icon" style="background:rgba(240,101,72,.10);color:#f06548">
          <i class="ri-time-line"></i>
        </div>
        <div class="cot-body">
          <div class="cot-name">Cotisation mensuelle</div>
          <div class="cot-sub">Engagement : 10 000 FCFA/mois</div>
          <div class="cot-progress">
            <div class="cot-fill" style="width:0%;background:#f06548"></div>
          </div>
        </div>
      </div>
      <div class="cot-right">
        <div class="cot-amount">10 000</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-danger" style="font-size:10px">En retard</span>
      </div>
    </div>
 
    <!-- Mois : Mars 2025 -->
    <div class="cot-month-label">Mars 2025</div>
 
    <div class="cot-item" data-statut="retard" onclick="openDetail(this)"
      data-type="Cotisation mensuelle" data-type-icon="ri-calendar-check-line"
      data-periode="Mars 2025" data-montant-du="10 000 FCFA"
      data-montant-paye="0 FCFA" data-restant="10 000 FCFA" data-pct="0"
      data-engagement="10 000 FCFA/mois" data-mode="—" data-created="01/03/2025">
      <div class="cot-left">
        <div class="cot-icon" style="background:rgba(240,101,72,.10);color:#f06548">
          <i class="ri-time-line"></i>
        </div>
        <div class="cot-body">
          <div class="cot-name">Cotisation mensuelle</div>
          <div class="cot-sub">Engagement : 10 000 FCFA/mois</div>
          <div class="cot-progress">
            <div class="cot-fill" style="width:0%;background:#f06548"></div>
          </div>
        </div>
      </div>
      <div class="cot-right">
        <div class="cot-amount">10 000</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-danger" style="font-size:10px">En retard</span>
      </div>
    </div>
 
    <div class="cot-item" data-statut="ajour" onclick="openDetail(this)"
      data-type="Quête du vendredi" data-type-icon="ri-hand-heart-line"
      data-periode="Mars 2025" data-montant-du="—"
      data-montant-paye="2 000 FCFA" data-restant="—" data-pct="100"
      data-engagement="Sans engagement" data-mode="Espèces" data-created="21/03/2025">
      <div class="cot-left">
        <div class="cot-icon" style="background:rgba(212,168,67,.12);color:#d4a843">
          <i class="ri-hand-heart-line"></i>
        </div>
        <div class="cot-body">
          <div class="cot-name">Quête du vendredi</div>
          <div class="cot-sub">Don ponctuel · 21/03/2025</div>
          <div class="cot-progress">
            <div class="cot-fill" style="width:100%;background:#0ab39c"></div>
          </div>
        </div>
      </div>
      <div class="cot-right">
        <div class="cot-amount">2 000</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-ok" style="font-size:10px">Reçu</span>
      </div>
    </div>
 
    <!-- Mois : Février 2025 -->
    <div class="cot-month-label">Février 2025</div>
 
    <div class="cot-item" data-statut="partiel" onclick="openDetail(this)"
      data-type="Cotisation mensuelle" data-type-icon="ri-calendar-check-line"
      data-periode="Février 2025" data-montant-du="10 000 FCFA"
      data-montant-paye="4 000 FCFA" data-restant="6 000 FCFA" data-pct="40"
      data-engagement="10 000 FCFA/mois" data-mode="Espèces" data-created="01/02/2025">
      <div class="cot-left">
        <div class="cot-icon" style="background:rgba(247,184,75,.12);color:#f7b84b">
          <i class="ri-error-warning-line"></i>
        </div>
        <div class="cot-body">
          <div class="cot-name">Cotisation mensuelle</div>
          <div class="cot-sub">Payé partiellement · 01/02/2025</div>
          <div class="cot-progress">
            <div class="cot-fill" style="width:40%;background:#f7b84b"></div>
          </div>
        </div>
      </div>
      <div class="cot-right">
        <div class="cot-amount">4 000</div>
        <div class="cot-unit">/ 10 000 FCFA</div>
        <span class="pill pill-warn" style="font-size:10px">Partiel</span>
      </div>
    </div>
 
    <div class="cot-item" data-statut="ajour" onclick="openDetail(this)"
      data-type="Ramadan 1446" data-type-icon="ri-moon-line"
      data-periode="Février 2025" data-montant-du="—"
      data-montant-paye="5 000 FCFA" data-restant="—" data-pct="100"
      data-engagement="Sans engagement" data-mode="Mobile Money" data-created="15/02/2025">
      <div class="cot-left">
        <div class="cot-icon" style="background:rgba(41,156,219,.12);color:#299cdb">
          <i class="ri-moon-line"></i>
        </div>
        <div class="cot-body">
          <div class="cot-name">Ramadan 1446</div>
          <div class="cot-sub">Don · 15/02/2025 · Mobile Money</div>
          <div class="cot-progress">
            <div class="cot-fill" style="width:100%;background:#0ab39c"></div>
          </div>
        </div>
      </div>
      <div class="cot-right">
        <div class="cot-amount">5 000</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-ok" style="font-size:10px">Reçu</span>
      </div>
    </div>
 
    <!-- Mois : Janvier 2025 -->
    <div class="cot-month-label">Janvier 2025</div>
 
    <div class="cot-item" data-statut="retard" onclick="openDetail(this)"
      data-type="Cotisation mensuelle" data-type-icon="ri-calendar-check-line"
      data-periode="Janvier 2025" data-montant-du="10 000 FCFA"
      data-montant-paye="0 FCFA" data-restant="10 000 FCFA" data-pct="0"
      data-engagement="10 000 FCFA/mois" data-mode="—" data-created="01/01/2025">
      <div class="cot-left">
        <div class="cot-icon" style="background:rgba(240,101,72,.10);color:#f06548">
          <i class="ri-time-line"></i>
        </div>
        <div class="cot-body">
          <div class="cot-name">Cotisation mensuelle</div>
          <div class="cot-sub">Engagement : 10 000 FCFA/mois</div>
          <div class="cot-progress">
            <div class="cot-fill" style="width:0%;background:#f06548"></div>
          </div>
        </div>
      </div>
      <div class="cot-right">
        <div class="cot-amount">10 000</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-danger" style="font-size:10px">En retard</span>
      </div>
    </div>
 
    <!-- Mois : Décembre 2024 -->
    <div class="cot-month-label">Décembre 2024</div>
 
    <div class="cot-item" data-statut="ajour" onclick="openDetail(this)"
      data-type="Cotisation mensuelle" data-type-icon="ri-calendar-check-line"
      data-periode="Décembre 2024" data-montant-du="10 000 FCFA"
      data-montant-paye="10 000 FCFA" data-restant="—" data-pct="100"
      data-engagement="10 000 FCFA/mois" data-mode="Mobile Money" data-created="02/12/2024">
      <div class="cot-left">
        <div class="cot-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
          <i class="ri-checkbox-circle-line"></i>
        </div>
        <div class="cot-body">
          <div class="cot-name">Cotisation mensuelle</div>
          <div class="cot-sub">Payé le 02/12/2024 · Mobile Money</div>
          <div class="cot-progress">
            <div class="cot-fill" style="width:100%;background:#0ab39c"></div>
          </div>
        </div>
      </div>
      <div class="cot-right">
        <div class="cot-amount">10 000</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-ok" style="font-size:10px">À jour</span>
      </div>
    </div>
 
    <div class="cot-item" data-statut="ajour" onclick="openDetail(this)"
      data-type="Cotisation mensuelle" data-type-icon="ri-calendar-check-line"
      data-periode="Novembre 2024" data-montant-du="10 000 FCFA"
      data-montant-paye="10 000 FCFA" data-restant="—" data-pct="100"
      data-engagement="10 000 FCFA/mois" data-mode="Espèces" data-created="01/11/2024">
      <div class="cot-left">
        <div class="cot-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
          <i class="ri-checkbox-circle-line"></i>
        </div>
        <div class="cot-body">
          <div class="cot-name">Cotisation mensuelle</div>
          <div class="cot-sub">Payé le 01/11/2024 · Espèces</div>
          <div class="cot-progress">
            <div class="cot-fill" style="width:100%;background:#0ab39c"></div>
          </div>
        </div>
      </div>
      <div class="cot-right">
        <div class="cot-amount">10 000</div>
        <div class="cot-unit">FCFA</div>
        <span class="pill pill-ok" style="font-size:10px">À jour</span>
      </div>
    </div>
 
    <!-- Empty state (caché par défaut, affiché par JS si filtre vide) -->
    <div class="empty-state" id="cot-empty" style="display:none">
      <i class="ri-inbox-line"></i>
      <div class="es-title">Aucune cotisation</div>
      <div class="es-sub">Aucune cotisation ne correspond à ce filtre.</div>
    </div>
 
    <div style="height:24px"></div>
 
  </main>


  @push('modal')
    <!-- ══════════════════════════════════════════════════════
        MODAL DÉTAIL COTISATION
    ══════════════════════════════════════════════════════ -->
    <div class="cot-modal-overlay" id="cot-modal-overlay" onclick="closeDetail()">
    <div class="cot-modal" onclick="event.stopPropagation()">
    
        <!-- Header coloré selon statut -->
        <div class="cot-modal-header" id="cot-modal-header">
        <div class="cmh-drag"></div>
        <div class="cmh-top">
            <div class="cmh-left">
            <div class="cmh-icon" id="cmh-icon"><i class="ri-calendar-check-line"></i></div>
            <div>
                <div class="cmh-type"   id="cmh-type">—</div>
                <div class="cmh-period" id="cmh-period">—</div>
            </div>
            </div>
            <button class="cmh-close" onclick="closeDetail()"><i class="ri-close-line"></i></button>
        </div>
        <div class="cmh-amount-row">
            <div class="cmh-amount" id="cmh-amount">—</div>
            <div id="cmh-pill"></div>
        </div>
        </div>
    
        <!-- Corps scrollable -->
        <div class="cot-modal-body">
    
        <!-- Grille infos -->
        <div class="cot-det-grid">
            <div class="cot-det-cell">
            <div class="cot-det-label"><i class="ri-error-warning-line"></i> Montant dû</div>
            <div class="cot-det-val" id="det-montant-du">—</div>
            </div>
            <div class="cot-det-cell">
            <div class="cot-det-label"><i class="ri-checkbox-circle-line"></i> Montant payé</div>
            <div class="cot-det-val" id="det-montant-paye">—</div>
            </div>
            <div class="cot-det-cell">
            <div class="cot-det-label"><i class="ri-refund-2-line"></i> Restant</div>
            <div class="cot-det-val" id="det-restant">—</div>
            </div>
            <div class="cot-det-cell">
            <div class="cot-det-label"><i class="ri-smartphone-line"></i> Mode paiement</div>
            <div class="cot-det-val" id="det-mode">—</div>
            </div>
            <div class="cot-det-cell">
            <div class="cot-det-label"><i class="ri-money-cny-circle-line"></i> Engagement</div>
            <div class="cot-det-val" id="det-engagement">—</div>
            </div>
            <div class="cot-det-cell">
            <div class="cot-det-label"><i class="ri-calendar-line"></i> Créée le</div>
            <div class="cot-det-val" id="det-created">—</div>
            </div>
        </div>
    
        <!-- Barre progression -->
        <div class="cot-det-prog-wrap">
            <div class="cot-det-prog-header">
            <span class="cot-det-prog-label"><i class="ri-bar-chart-line"></i> Progression</span>
            <span class="cot-det-prog-pct" id="det-pct-label">—</span>
            </div>
            <div class="cot-det-prog-track">
            <div class="cot-det-prog-fill" id="det-prog-fill" style="width:0%"></div>
            </div>
            <div class="cot-det-prog-footer">
            <span id="det-prog-paye-lbl">0 FCFA payé</span>
            <span id="det-prog-du-lbl">— dû</span>
            </div>
        </div>
    
        <!-- Liste paiements liés -->
        <div class="cot-det-section-title">
            <i class="ri-bank-card-line"></i> Paiements liés
        </div>
        <div class="cot-det-pay-list" id="det-pay-list">
            <!-- Paiements statiques — le JS en sélectionne selon la période -->
    
            <!-- Groupe : Avril 2025 -->
            <div class="cot-pay-row" data-periode="Avril 2025">
            <div class="cot-pay-icon" style="background:rgba(64,81,137,.10);color:#405189">
                <i class="ri-add-circle-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Cotisation créée</div>
                <div class="cot-pay-date">01/04/2025 · En attente</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:var(--muted)">—</span>
                <button class="cot-pay-eye" onclick="goToPaiement('FAIL202503004')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
            <div class="cot-pay-row" data-periode="Avril 2025">
            <div class="cot-pay-icon" style="background:rgba(240,101,72,.10);color:#f06548">
                <i class="ri-close-circle-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Paiement échoué</div>
                <div class="cot-pay-date">28/03/2025 · Orange Money — solde insuffisant</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:#f06548">-10 000</span>
                <button class="cot-pay-eye" onclick="goToPaiement('FAIL202503004')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
    
            <!-- Groupe : Mars 2025 -->
            <div class="cot-pay-row" data-periode="Mars 2025">
            <div class="cot-pay-icon" style="background:rgba(64,81,137,.10);color:#405189">
                <i class="ri-add-circle-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Cotisation créée</div>
                <div class="cot-pay-date">01/03/2025 · En attente</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:var(--muted)">—</span>
                <button class="cot-pay-eye" onclick="goToPaiement('OM202503003')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
            <div class="cot-pay-row" data-periode="Mars 2025">
            <div class="cot-pay-icon" style="background:rgba(212,168,67,.12);color:#d4a843">
                <i class="ri-hand-heart-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Quête du vendredi</div>
                <div class="cot-pay-date">21/03/2025 · Espèces</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:#0ab39c">+2 000</span>
                <button class="cot-pay-eye" onclick="goToPaiement('OM202503003')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
    
            <!-- Groupe : Février 2025 -->
            <div class="cot-pay-row" data-periode="Février 2025">
            <div class="cot-pay-icon" style="background:rgba(247,184,75,.12);color:#f7b84b">
                <i class="ri-error-warning-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Paiement partiel</div>
                <div class="cot-pay-date">01/02/2025 · Espèces</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:#f7b84b">+4 000</span>
                <button class="cot-pay-eye" onclick="goToPaiement('ESP202502006')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
            <div class="cot-pay-row" data-periode="Février 2025">
            <div class="cot-pay-icon" style="background:rgba(41,156,219,.12);color:#299cdb">
                <i class="ri-moon-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Ramadan 1446</div>
                <div class="cot-pay-date">15/02/2025 · Mobile Money</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:#0ab39c">+5 000</span>
                <button class="cot-pay-eye" onclick="goToPaiement('OM202502005')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
    
            <!-- Groupe : Janvier 2025 -->
            <div class="cot-pay-row" data-periode="Janvier 2025">
            <div class="cot-pay-icon" style="background:rgba(64,81,137,.10);color:#405189">
                <i class="ri-add-circle-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Cotisation créée</div>
                <div class="cot-pay-date">01/01/2025 · En attente</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:var(--muted)">—</span>
                <button class="cot-pay-eye" onclick="goToPaiement('VIR202501007')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
    
            <!-- Groupe : Décembre 2024 -->
            <div class="cot-pay-row" data-periode="Décembre 2024">
            <div class="cot-pay-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Paiement reçu</div>
                <div class="cot-pay-date">02/12/2024 · Mobile Money</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:#0ab39c">+10 000</span>
                <button class="cot-pay-eye" onclick="goToPaiement('OM202502005')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
    
            <!-- Groupe : Novembre 2024 -->
            <div class="cot-pay-row" data-periode="Novembre 2024">
            <div class="cot-pay-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Paiement reçu</div>
                <div class="cot-pay-date">01/11/2024 · Espèces</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:#0ab39c">+10 000</span>
                <button class="cot-pay-eye" onclick="goToPaiement('ESP202502006')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
    
            <!-- Groupe : Ramadan 1446 -->
            <div class="cot-pay-row" data-periode="Ramadan 1446">
            <div class="cot-pay-icon" style="background:rgba(41,156,219,.12);color:#299cdb">
                <i class="ri-moon-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Don Ramadan reçu</div>
                <div class="cot-pay-date">15/02/2025 · Orange Money</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:#0ab39c">+5 000</span>
                <button class="cot-pay-eye" onclick="goToPaiement('OM202502005')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
    
            <!-- Groupe : Quête du vendredi -->
            <div class="cot-pay-row" data-periode="Quête du vendredi">
            <div class="cot-pay-icon" style="background:rgba(212,168,67,.12);color:#d4a843">
                <i class="ri-hand-heart-line"></i>
            </div>
            <div class="cot-pay-body">
                <div class="cot-pay-title">Don reçu</div>
                <div class="cot-pay-date">21/03/2025 · Espèces</div>
            </div>
            <div class="cot-pay-right">
                <span class="cot-pay-amount" style="color:#0ab39c">+2 000</span>
                <button class="cot-pay-eye" onclick="goToPaiement('OM202503003')" title="Voir le paiement">
                <i class="ri-eye-line"></i>
                </button>
            </div>
            </div>
    
            <!-- Empty state paiements -->
            <div class="cot-pay-empty" id="det-pay-empty" style="display:none">
            <i class="ri-inbox-line"></i> Aucun paiement enregistré
            </div>
        </div>
    
        </div><!-- /cot-modal-body -->
    
        <!-- Footer actions -->
        <div class="cot-modal-footer">
        <button class="cot-footer-recla" onclick="openReclaModal()">
            <i class="ri-flag-line"></i> Faire une réclamation
        </button>
        <button class="cot-footer-pay" onclick="goToPay()">
            <i class="ri-add-circle-line"></i> Payer
        </button>
        </div>
    
    </div><!-- /cot-modal -->
    </div>
    
    
    <!-- ══════════════════════════════════════════════════════
        MODAL RÉCLAMATION (depuis le détail cotisation)
    ══════════════════════════════════════════════════════ -->
    <div class="cot-modal-overlay" id="recla-overlay" onclick="closeReclaModal()">
    <div class="cot-modal cot-modal-sm" onclick="event.stopPropagation()">
    
        <div class="cot-modal-header" style="background:linear-gradient(135deg,#e8a53a,#f7b84b)">
        <div class="cmh-drag"></div>
        <div class="cmh-top">
            <div class="cmh-left">
            <div class="cmh-icon" style="background:rgba(255,255,255,.2);color:#fff">
                <i class="ri-flag-line"></i>
            </div>
            <div>
                <div class="cmh-type" style="color:rgba(255,255,255,.85);font-size:11px">RÉCLAMATION</div>
                <div class="cmh-period" style="color:#fff;font-size:15px;font-weight:900">Signaler un problème</div>
            </div>
            </div>
            <button class="cmh-close" onclick="closeReclaModal()"><i class="ri-close-line"></i></button>
        </div>
        </div>
    
        <div class="cot-modal-body">
    
        <div class="f-group">
            <label class="f-label-sm">Cotisation concernée</label>
            <input type="text" class="f-input-sm" id="recla-cotisation" readonly/>
        </div>
    
        <div class="f-group">
            <label class="f-label-sm">Titre <span style="color:#f06548">*</span></label>
            <div class="f-input-wrap-sm">
            <i class="ri-text f-ico-sm"></i>
            <input type="text" class="f-input-sm" id="recla-titre"
                placeholder="ex : Paiement non enregistré"/>
            </div>
        </div>
    
        <div class="f-group">
            <label class="f-label-sm">Message <span style="color:#f06548">*</span></label>
            <textarea class="f-input-sm f-textarea-sm" id="recla-message"
            placeholder="Décrivez le problème en détail…"></textarea>
        </div>
    
        </div>
    
        <div class="cot-modal-footer" style="border-top:1px solid var(--border)">
        <button class="cot-footer-recla" style="border-color:rgba(135,138,153,.3);color:var(--muted)" onclick="closeReclaModal()">
            <i class="ri-close-line"></i> Annuler
        </button>
        <button class="cot-footer-pay" id="btn-recla-submit" onclick="submitRecla()">
            <i class="ri-send-plane-line"></i> Envoyer
        </button>
        </div>
    
    </div>
    </div>
  @endpush


@push('scripts')
    <script src="{{ asset('frontend/js/paiement.js') }}"></script>
@endpush