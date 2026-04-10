<main class="page-content">
 
      <div class="prof-layout">
 
        <!-- Colonne principale -->
        <div>
 
          <!-- ── Hero ── -->
          <div class="prof-hero">
            <div class="prof-hero-bg"></div>
            <div class="prof-hero-content">
              <!-- Avatar avec bouton photo -->
              <div class="prof-avatar-wrap">
                <div class="prof-avatar" id="prof-avatar">MK</div>
                <button class="prof-avatar-edit" onclick="openPhotoModal()" title="Changer la photo">
                  <i class="ri-camera-line"></i>
                </button>
              </div>
              <div class="prof-hero-info">
                <div class="prof-name">Moussa Koné</div>
                <div class="prof-phone">+225 07 00 11 22</div>
                <div class="prof-hero-badges">
                  <span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px">
                    <i class="ri-shield-check-line"></i> Compte validé
                  </span>
                  <span class="pill" style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.85);font-size:11px">
                    <i class="ri-calendar-line"></i> Depuis Jan 2023
                  </span>
                </div>
              </div>
            </div>
            <!-- Bouton modifier -->
            <button class="prof-edit-btn" onclick="openEditModal()" title="Modifier mes informations">
              <i class="ri-pencil-line"></i>
            </button>
          </div>
 
          <!-- ── Infos ── -->
          <div class="card prof-card">
            <div class="prof-card-header">
              <span class="prof-card-title"><i class="ri-user-line"></i> Informations personnelles</span>
              <button class="prof-card-edit-btn" onclick="openEditModal()">
                <i class="ri-pencil-line"></i> Modifier
              </button>
            </div>
            <div class="prof-info-list">
              <div class="prof-info-row">
                <div class="prof-info-label"><i class="ri-user-3-line"></i> Nom complet</div>
                <div class="prof-info-value">Moussa Koné</div>
              </div>
              <div class="prof-info-row">
                <div class="prof-info-label"><i class="ri-smartphone-line"></i> Téléphone</div>
                <div class="prof-info-value">+225 07 00 11 22</div>
              </div>
              <div class="prof-info-row">
                <div class="prof-info-label"><i class="ri-map-pin-line"></i> Adresse</div>
                <div class="prof-info-value">Adjamé, Abidjan</div>
              </div>
              <div class="prof-info-row">
                <div class="prof-info-label"><i class="ri-money-cny-circle-line"></i> Engagement mensuel</div>
                <div class="prof-info-value" style="color:var(--p);font-weight:800">10 000 FCFA</div>
              </div>
              <div class="prof-info-row">
                <div class="prof-info-label"><i class="ri-calendar-check-line"></i> Membre depuis</div>
                <div class="prof-info-value">15 Janvier 2023</div>
              </div>
              <div class="prof-info-row">
                <div class="prof-info-label"><i class="ri-group-line"></i> Code parrainage</div>
                <div class="prof-info-value">
                  <span class="prof-parrain-code">ISL-MK-2023</span>
                </div>
              </div>
            </div>
          </div>
 
          <!-- ── Stats rapides ── -->
          <div class="prof-stats-row">
            <div class="prof-stat-card">
              <div class="psc-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-money-cny-circle-line"></i></div>
              <div class="psc-val" style="color:#0ab39c">140 000</div>
              <div class="psc-label">FCFA cotisés</div>
            </div>
            <div class="prof-stat-card">
              <div class="psc-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-bank-card-line"></i></div>
              <div class="psc-val">12</div>
              <div class="psc-label">Paiements</div>
            </div>
            <div class="prof-stat-card">
              <div class="psc-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-time-line"></i></div>
              <div class="psc-val" style="color:#f06548">3</div>
              <div class="psc-label">Mois retard</div>
            </div>
          </div>
 
          <!-- ── Menu actions ── -->
          <div class="card prof-menu-card">
 
            <button class="prof-menu-item" onclick="window.location.href='documents.html'">
              <div class="pmi-icon" style="background:rgba(41,156,219,.10);color:#299cdb"><i class="ri-file-list-3-line"></i></div>
              <div class="pmi-body">
                <div class="pmi-title">Mes documents</div>
                <div class="pmi-sub">CNI, justificatifs de résidence</div>
              </div>
              <span class="pmi-badge warn">2 en attente</span>
              <i class="ri-arrow-right-s-line pmi-arrow"></i>
            </button>
 
            <button class="prof-menu-item" onclick="window.location.href='reclamations.html'">
              <div class="pmi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-flag-line"></i></div>
              <div class="pmi-body">
                <div class="pmi-title">Mes réclamations</div>
                <div class="pmi-sub">Signaler un problème</div>
              </div>
              <span class="pmi-badge info">1 en cours</span>
              <i class="ri-arrow-right-s-line pmi-arrow"></i>
            </button>
 
            <button class="prof-menu-item" onclick="window.location.href='notifications.html'">
              <div class="pmi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-notification-3-line"></i></div>
              <div class="pmi-body">
                <div class="pmi-title">Notifications</div>
                <div class="pmi-sub">Alertes et rappels</div>
              </div>
              <span class="pmi-badge danger">3 non lues</span>
              <i class="ri-arrow-right-s-line pmi-arrow"></i>
            </button>
 
            <button class="prof-menu-item" onclick="openEditModal()">
              <div class="pmi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-pencil-line"></i></div>
              <div class="pmi-body">
                <div class="pmi-title">Modifier mes informations</div>
                <div class="pmi-sub">Nom, adresse, contact</div>
              </div>
              <i class="ri-arrow-right-s-line pmi-arrow"></i>
            </button>
 
            <button class="prof-menu-item prof-menu-danger" onclick="confirmDeconnexion()">
              <div class="pmi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-logout-box-r-line"></i></div>
              <div class="pmi-body">
                <div class="pmi-title" style="color:#f06548">Déconnexion</div>
              </div>
              <i class="ri-arrow-right-s-line pmi-arrow"></i>
            </button>
 
          </div>
 
        </div><!-- /col principale -->
 
        <!-- Colonne droite desktop -->
        <div class="prof-right-col">
          <div class="card" style="padding:20px;margin-bottom:16px">
            <div style="font-size:13px;font-weight:800;color:var(--p);margin-bottom:14px;display:flex;align-items:center;gap:6px">
              <i class="ri-bar-chart-line"></i> Résumé
            </div>
            <div style="display:flex;flex-direction:column;gap:12px">
              <div class="prof-summary-row">
                <span>Total cotisé</span>
                <strong style="color:#0ab39c">140 000 FCFA</strong>
              </div>
              <div class="prof-summary-row">
                <span>Montant dû</span>
                <strong style="color:#f06548">30 000 FCFA</strong>
              </div>
              <div class="prof-summary-row">
                <span>Paiements</span>
                <strong>12</strong>
              </div>
              <div class="prof-summary-row">
                <span>Documents</span>
                <strong>2 / 2</strong>
              </div>
            </div>
          </div>
 
          <div class="card" style="padding:20px">
            <div style="font-size:13px;font-weight:800;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:6px">
              <i class="ri-information-line" style="color:var(--p)"></i> Statut du compte
            </div>
            <div class="prof-status-steps">
              <div class="pss-step done">
                <div class="pss-dot"><i class="ri-check-line"></i></div>
                <div class="pss-text">Inscription</div>
              </div>
              <div class="pss-step done">
                <div class="pss-dot"><i class="ri-check-line"></i></div>
                <div class="pss-text">Vérification OTP</div>
              </div>
              <div class="pss-step done">
                <div class="pss-dot"><i class="ri-check-line"></i></div>
                <div class="pss-text">Documents soumis</div>
              </div>
              <div class="pss-step done">
                <div class="pss-dot"><i class="ri-check-line"></i></div>
                <div class="pss-text">Compte validé</div>
              </div>
            </div>
          </div>
        </div>
 
      </div><!-- /prof-layout -->
 
      <div style="height:24px"></div>
    </main>



    @push('modal')

    <!-- ══════════════════════════════════════════════════════
        MODAL MODIFIER INFOS
    ══════════════════════════════════════════════════════ -->
    <div class="pwa-modal-overlay" id="edit-overlay" onclick="closeEditModal()">
    <div class="pwa-modal" onclick="event.stopPropagation()">
        <div class="pwa-modal-header">
        <div class="pwa-modal-drag"></div>
        <div class="pwa-modal-title-row">
            <div class="pwa-modal-title"><i class="ri-pencil-line"></i> Modifier mes informations</div>
            <button class="pwa-modal-close" onclick="closeEditModal()"><i class="ri-close-line"></i></button>
        </div>
        </div>
    
        <div class="pwa-modal-body">
        <div class="f-group">
            <label class="f-label">Nom <span class="req">*</span></label>
            <input type="text" class="f-input" id="edit-nom" value="Koné" placeholder="Votre nom"/>
        </div>
        <div class="f-group">
            <label class="f-label">Prénoms <span class="req">*</span></label>
            <input type="text" class="f-input" id="edit-prenom" value="Moussa" placeholder="Vos prénoms"/>
        </div>
        <div class="f-group">
            <label class="f-label">Adresse</label>
            <div class="f-input-wrap">
            <i class="ri-map-pin-line f-input-icon"></i>
            <input type="text" class="f-input" id="edit-adresse" value="Adjamé, Abidjan" placeholder="Votre adresse"/>
            </div>
        </div>
        <div class="f-group">
            <label class="f-label">Numéro de téléphone</label>
            <div class="f-input-wrap">
            <i class="ri-smartphone-line f-input-icon"></i>
            <input type="tel" class="f-input" id="edit-phone" value="07 00 11 22" placeholder="Numéro" inputmode="numeric"/>
            </div>
            <div class="f-hint">Le numéro est utilisé pour la connexion OTP. Un OTP sera envoyé pour confirmer le changement.</div>
        </div>
        <div class="f-group">
            <label class="f-label">Code parrainage reçu <span class="opt">(non modifiable)</span></label>
            <input type="text" class="f-input" value="ISL-MK-2023" disabled style="opacity:.6;cursor:not-allowed"/>
        </div>
        </div>
    
        <div class="pwa-modal-footer">
        <button class="btn-outline" style="height:46px;font-size:14px" onclick="closeEditModal()">
            <i class="ri-close-line"></i> Annuler
        </button>
        <button class="btn-main" style="height:46px;font-size:14px" onclick="saveEdit()">
            <i class="ri-save-line"></i> Enregistrer
        </button>
        </div>
    </div>
    </div>
    
    
    <!-- ══════════════════════════════════════════════════════
        MODAL PHOTO
    ══════════════════════════════════════════════════════ -->
    <div class="pwa-modal-overlay" id="photo-overlay" onclick="closePhotoModal()">
    <div class="pwa-modal pwa-modal-sm" onclick="event.stopPropagation()">
        <div class="pwa-modal-header">
        <div class="pwa-modal-drag"></div>
        <div class="pwa-modal-title-row">
            <div class="pwa-modal-title"><i class="ri-camera-line"></i> Photo de profil</div>
            <button class="pwa-modal-close" onclick="closePhotoModal()"><i class="ri-close-line"></i></button>
        </div>
        </div>
    
        <div class="pwa-modal-body">
        <!-- Aperçu -->
        <div class="photo-preview-wrap">
            <div class="photo-preview" id="photo-preview">MK</div>
            <div class="photo-preview-label">Aperçu</div>
        </div>
    
        <!-- Boutons -->
        <div class="photo-btns">
            <button class="photo-btn photo-btn-primary" onclick="triggerCamera()">
            <i class="ri-camera-fill"></i>
            <span>Prendre une photo</span>
            <div class="photo-btn-sub">Ouvrir la caméra</div>
            </button>
            <button class="photo-btn" onclick="triggerGallery()">
            <i class="ri-image-2-line"></i>
            <span>Choisir dans la galerie</span>
            <div class="photo-btn-sub">Depuis votre téléphone</div>
            </button>
            <button class="photo-btn photo-btn-danger" onclick="removePhoto()">
            <i class="ri-delete-bin-line"></i>
            <span>Supprimer la photo</span>
            <div class="photo-btn-sub">Retour aux initiales</div>
            </button>
        </div>
    
        <!-- Input caché -->
        <input type="file" id="file-input" accept="image/*" style="display:none" onchange="onFileSelect(this)"/>
        </div>
    </div>
    </div>
    @endpush