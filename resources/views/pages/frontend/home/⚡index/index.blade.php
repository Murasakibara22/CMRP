<main class="page-content">
 
      <!-- Greeting -->
      <div class="greeting-section">
        <div class="greeting-left">
          <div class="greeting-hello">Bonjour, Moussa 👋</div>
          <div class="greeting-sub">Voici votre situation en un coup d'œil.</div>
        </div>
        <span class="pill pill-ok greeting-badge">
          <i class="ri-shield-check-line"></i> Validé
        </span>
      </div>
 
      <!-- Alerte retard -->
      <div class="alert-card">
        <div class="alert-card-left">
          <div class="alert-icon"><i class="ri-alarm-warning-line"></i></div>
          <div>
            <div class="alert-title">Vous avez 3 mois en retard</div>
            <div class="alert-sub">Mars, Février, Janvier 2025 — 30 000 FCFA dus</div>
          </div>
        </div>
        <button class="alert-btn" onclick="window.location.href='cotisations.html'">
          Régulariser <i class="ri-arrow-right-s-line"></i>
        </button>
      </div>
 
      <!-- KPIs -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-money-cny-circle-line"></i></div>
          <div class="kpi-label">Total cotisé</div>
          <div class="kpi-value">140 000</div>
          <div class="kpi-unit">FCFA</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-error-warning-line"></i></div>
          <div class="kpi-label">Montant dû</div>
          <div class="kpi-value" style="color:#f06548">30 000</div>
          <div class="kpi-unit">FCFA restant</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
          <div class="kpi-label">Mois en retard</div>
          <div class="kpi-value" style="color:#f7b84b">3</div>
          <div class="kpi-unit">Derniers mois</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-calendar-event-line"></i></div>
          <div class="kpi-label">Prochain paiement</div>
          <div class="kpi-value kpi-value-sm">Mai 2025</div>
          <div class="kpi-unit">10 000 FCFA</div>
        </div>
      </div>
 
      <!-- 2 colonnes desktop : historique + panel droit -->
      <div class="desktop-cols">
 
        <!-- Colonne gauche : historique -->
        <div>
          <div class="hist-header">
            <div class="hist-title">Historique</div>
          </div>
          <div class="hist-filters">
            <button class="hf-btn active" data-filter="tous" onclick="filterHist(this)">
              <i class="ri-list-check"></i> Tous
            </button>
            <button class="hf-btn" data-filter="cotisations" onclick="filterHist(this)">
              <i class="ri-calendar-check-line"></i> Cotisations
            </button>
            <button class="hf-btn" data-filter="reclamations" onclick="filterHist(this)">
              <i class="ri-flag-line"></i> Réclamations
            </button>
          </div>
 
          <div class="hist-list card">
            <div class="hist-item" data-type="cotisations">
              <div class="hi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
              <div class="hi-body">
                <div class="hi-title">Cotisation mensuelle — Avril 2025</div>
                <div class="hi-date">02/04/2025 · Mobile Money</div>
              </div>
              <div class="hi-right">
                <div class="hi-amount" style="color:#0ab39c">+10 000</div>
                <span class="pill pill-ok" style="font-size:10px">À jour</span>
              </div>
            </div>
            <div class="hist-item" data-type="cotisations">
              <div class="hi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-time-line"></i></div>
              <div class="hi-body">
                <div class="hi-title">Cotisation mensuelle — Mars 2025</div>
                <div class="hi-date">Non payé · En retard</div>
              </div>
              <div class="hi-right">
                <div class="hi-amount" style="color:#f06548">-10 000</div>
                <span class="pill pill-danger" style="font-size:10px">En retard</span>
              </div>
            </div>
            <div class="hist-item" data-type="reclamations">
              <div class="hi-icon" style="background:rgba(41,156,219,.12);color:#299cdb"><i class="ri-flag-line"></i></div>
              <div class="hi-body">
                <div class="hi-title">Réclamation — Cotisation Fév.</div>
                <div class="hi-date">28/03/2025 · En cours de traitement</div>
              </div>
              <div class="hi-right">
                <span class="pill pill-info" style="font-size:10px">En cours</span>
              </div>
            </div>
            <div class="hist-item" data-type="cotisations">
              <div class="hi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
              <div class="hi-body">
                <div class="hi-title">Cotisation mensuelle — Fév. 2025</div>
                <div class="hi-date">01/02/2025 · Espèces</div>
              </div>
              <div class="hi-right">
                <div class="hi-amount" style="color:#0ab39c">+10 000</div>
                <span class="pill pill-ok" style="font-size:10px">À jour</span>
              </div>
            </div>
            <div class="hist-item" data-type="cotisations">
              <div class="hi-icon" style="background:rgba(212,168,67,.12);color:#d4a843"><i class="ri-hand-heart-line"></i></div>
              <div class="hi-body">
                <div class="hi-title">Quête du vendredi — Mars 2025</div>
                <div class="hi-date">21/03/2025 · Espèces</div>
              </div>
              <div class="hi-right">
                <div class="hi-amount" style="color:#0ab39c">+2 000</div>
                <span class="pill pill-ok" style="font-size:10px">Reçu</span>
              </div>
            </div>
            <div class="hist-item" data-type="cotisations">
              <div class="hi-icon" style="background:rgba(41,156,219,.12);color:#299cdb"><i class="ri-moon-line"></i></div>
              <div class="hi-body">
                <div class="hi-title">Ramadan 1446 — Fév. 2025</div>
                <div class="hi-date">15/02/2025 · Mobile Money</div>
              </div>
              <div class="hi-right">
                <div class="hi-amount" style="color:#0ab39c">+5 000</div>
                <span class="pill pill-ok" style="font-size:10px">Reçu</span>
              </div>
            </div>
          </div>
        </div>
 
        <!-- Colonne droite (desktop uniquement) -->
        <div class="desktop-right">
          <!-- Rappel paiement dû -->
          <div class="reminder-card">
            <div class="reminder-card-title">
              <i class="ri-alarm-warning-line"></i> Prochain rappel
            </div>
            <div class="reminder-due">
              <div class="reminder-due-label">Cotisation due</div>
              <div class="reminder-due-amount">10 000 FCFA</div>
              <div class="reminder-due-period">Mai 2025</div>
              <button class="reminder-due-btn" onclick="window.location.href='ajout-cotisation.html'">
                <i class="ri-money-cny-circle-line"></i> Payer maintenant
              </button>
            </div>
          </div>
 
          <!-- Raccourcis rapides -->
          <div class="reminder-card">
            <div class="reminder-card-title">
              <i class="ri-flashlight-line"></i> Accès rapide
            </div>
            <div class="quick-links">
              <a class="quick-link" onclick="window.location.href='cotisations.html'">
                <i class="ri-calendar-check-line"></i>
                <span>Mes cotisations</span>
                <i class="ri-arrow-right-s-line ql-arrow"></i>
              </a>
              <a class="quick-link" onclick="window.location.href='paiements.html'">
                <i class="ri-bank-card-line"></i>
                <span>Mes paiements</span>
                <i class="ri-arrow-right-s-line ql-arrow"></i>
              </a>
              <a class="quick-link" onclick="window.location.href='profil.html'">
                <i class="ri-file-list-3-line"></i>
                <span>Mes documents</span>
                <i class="ri-arrow-right-s-line ql-arrow"></i>
              </a>
              <a class="quick-link" onclick="window.location.href='reclamations.html'">
                <i class="ri-flag-line"></i>
                <span>Mes réclamations</span>
                <i class="ri-arrow-right-s-line ql-arrow"></i>
              </a>
            </div>
          </div>
        </div>
 
      </div><!-- /desktop-cols -->
 
      <div style="height:24px"></div>
    </main>
