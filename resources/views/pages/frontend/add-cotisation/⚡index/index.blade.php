<main class="page-content">
 
      <div class="ac-layout">
        <!-- Colonne formulaire -->
        <div class="ac-form-col">
 
          <div class="page-title">Nouvelle cotisation</div>
          <div class="page-sub">Enregistrez votre paiement de cotisation.</div>
 
          <!-- ─── SECTION 1 : TYPE DE COTISATION ─── -->
          <div class="ac-section">
            <div class="ac-section-title">
              <span class="ac-section-bar"></span> Type de cotisation
            </div>
 
            <div class="f-group">
              <label class="f-label">Type <span class="req">*</span></label>
              <div class="f-input-wrap">
                <i class="ri-tag-line f-input-icon"></i>
                <select class="f-input" id="select-type" onchange="onTypeChange(this)">
                  <option value="">— Choisir un type —</option>
                  <option value="mensuel">Cotisation mensuelle</option>
                  <option value="quete">Quête du vendredi</option>
                  <option value="ordinaire">Don ordinaire</option>
                  <option value="ramadan">Ramadan 1446</option>
                </select>
              </div>
            </div>
 
            <!-- Mois / Année (mensuel) — statique, visible si mensuel -->
            <div class="f-group ac-field-periode" id="field-periode">
              <label class="f-label">Période concernée <span class="req">*</span></label>
              <div class="ac-row2">
                <div class="f-input-wrap">
                  <i class="ri-calendar-line f-input-icon"></i>
                  <select class="f-input" id="select-mois">
                    <option value="">Mois</option>
                    <option value="1">Janvier</option>
                    <option value="2">Février</option>
                    <option value="3">Mars</option>
                    <option value="4">Avril</option>
                    <option value="5" selected>Mai</option>
                    <option value="6">Juin</option>
                    <option value="7">Juillet</option>
                    <option value="8">Août</option>
                    <option value="9">Septembre</option>
                    <option value="10">Octobre</option>
                    <option value="11">Novembre</option>
                    <option value="12">Décembre</option>
                  </select>
                </div>
                <div class="f-input-wrap">
                  <i class="ri-calendar-2-line f-input-icon"></i>
                  <select class="f-input" id="select-annee">
                    <option value="2024">2024</option>
                    <option value="2025" selected>2025</option>
                    <option value="2026">2026</option>
                  </select>
                </div>
              </div>
            </div>
 
          </div>
 
          <!-- ─── SECTION 2 : ENGAGEMENT (mensuel sans engagement) ─── -->
          <!-- Visible si mensuel ET aucun engagement défini -->
          <div class="ac-section ac-field-engagement" id="field-engagement">
            <div class="ac-section-title">
              <span class="ac-section-bar" style="background:#f7b84b"></span>
              Choisir votre engagement mensuel
            </div>
            <div class="ac-hint-box">
              <i class="ri-information-line"></i>
              Vous n'avez pas encore de montant d'engagement défini. Choisissez un palier ou saisissez un montant libre.
            </div>
 
            <!-- Paliers prédéfinis -->
            <div class="f-group">
              <label class="f-label">Paliers suggérés</label>
              <div class="ac-paliers" id="paliers">
                <button type="button" class="ac-palier" data-val="2500"  onclick="selectPalier(this)">2 500</button>
                <button type="button" class="ac-palier" data-val="5000"  onclick="selectPalier(this)">5 000</button>
                <button type="button" class="ac-palier" data-val="10000" onclick="selectPalier(this)">10 000</button>
                <button type="button" class="ac-palier" data-val="15000" onclick="selectPalier(this)">15 000</button>
                <button type="button" class="ac-palier" data-val="20000" onclick="selectPalier(this)">20 000</button>
                <button type="button" class="ac-palier ac-palier-autre" data-val="autre" onclick="selectPalier(this)">
                  <i class="ri-edit-line"></i> Autre
                </button>
              </div>
            </div>
 
            <!-- Champ montant libre (si "Autre") -->
            <div class="f-group ac-field-montant-libre" id="field-montant-libre">
              <label class="f-label">Montant libre <span class="req">*</span></label>
              <div class="f-input-wrap">
                <i class="ri-money-cny-circle-line f-input-icon"></i>
                <input type="number" class="f-input has-sfx" id="input-montant-libre"
                  placeholder="ex : 7500" inputmode="numeric" min="500"/>
                <span class="f-input-suffix">FCFA</span>
              </div>
              <div class="f-hint">Minimum recommandé : 500 FCFA</div>
            </div>
          </div>
 
          <!-- ─── SECTION 3 : MONTANT & MODE ─── -->
          <div class="ac-section">
            <div class="ac-section-title">
              <span class="ac-section-bar" style="background:#0ab39c"></span>
              Montant &amp; mode de paiement
            </div>
 
            <div class="f-group">
              <label class="f-label">Montant payé <span class="req">*</span></label>
              <div class="f-input-wrap">
                <i class="ri-money-cny-circle-line f-input-icon"></i>
                <input type="number" class="f-input has-sfx" id="input-montant"
                  placeholder="ex : 10000" inputmode="numeric" min="1"/>
                <span class="f-input-suffix">FCFA</span>
              </div>
              <!-- Info si montant partiel (mensuel) -->
              <div class="ac-partial-info" id="partial-info">
                <i class="ri-error-warning-line"></i>
                Un paiement inférieur à l'engagement sera enregistré comme <strong>partiel</strong>.
              </div>
            </div>
 
            <!-- Mode de paiement — pills -->
            <div class="f-group">
              <label class="f-label">Mode de paiement <span class="req">*</span></label>
              <div class="ac-modes" id="ac-modes">
                <button type="button" class="ac-mode active" data-mode="mobile_money" onclick="selectMode(this)">
                  <i class="ri-smartphone-line"></i><span>Mobile Money</span>
                </button>
                <button type="button" class="ac-mode" data-mode="espece" onclick="selectMode(this)">
                  <i class="ri-money-dollar-circle-line"></i><span>Espèces</span>
                </button>
                <button type="button" class="ac-mode" data-mode="virement" onclick="selectMode(this)">
                  <i class="ri-bank-line"></i><span>Virement</span>
                </button>
              </div>
            </div>
 
            <!-- Référence transaction (Mobile Money / Virement) -->
            <div class="f-group ac-field-reference" id="field-reference">
              <label class="f-label">Référence transaction <span class="opt">(optionnel)</span></label>
              <div class="f-input-wrap">
                <i class="ri-hashtag f-input-icon"></i>
                <input type="text" class="f-input" id="input-reference"
                  placeholder="ex : OM202505XXXX"/>
              </div>
            </div>
 
          </div>
 
          <!-- ─── SECTION 4 : VALIDATION ─── -->
          <div class="ac-section">
            <div class="ac-section-title">
              <span class="ac-section-bar" style="background:#f7b84b"></span>
              Validation (validated_by / validated_at)
            </div>
 
            <div class="ac-validation-row">
              <div class="ac-vr-left">
                <div class="ac-vr-icon"><i class="ri-shield-check-line"></i></div>
                <div>
                  <div class="ac-vr-title">Demander une validation immédiate</div>
                  <div class="ac-vr-sub">
                    Si activé, votre paiement sera soumis à validation par un administrateur.
                    <br>Pour les <strong>espèces</strong> : validation manuelle requise.
                  </div>
                </div>
              </div>
              <label class="toggle-wrap">
                <input type="checkbox" id="toggle-validation" checked onchange="onToggleValidation(this)"/>
                <span class="toggle-sl"></span>
              </label>
            </div>
 
          </div>
 
          <!-- ─── RÉCAP AVANT ENVOI ─── -->
          <div class="ac-recap" id="ac-recap">
            <div class="ac-recap-title"><i class="ri-receipt-line"></i> Récapitulatif</div>
            <div class="ac-recap-rows">
              <div class="ac-recap-row">
                <span>Type</span>
                <span id="recap-type">—</span>
              </div>
              <div class="ac-recap-row">
                <span>Période</span>
                <span id="recap-periode">—</span>
              </div>
              <div class="ac-recap-row">
                <span>Montant</span>
                <strong id="recap-montant" style="color:var(--p)">—</strong>
              </div>
              <div class="ac-recap-row">
                <span>Mode</span>
                <span id="recap-mode">Mobile Money</span>
              </div>
            </div>
          </div>
 
          <!-- Boutons -->
          <div class="ac-btns">
            <button class="btn-main" id="btn-submit" onclick="submitForm()">
              <i class="ri-send-plane-line"></i> Envoyer la demande
            </button>
            <button class="btn-outline" onclick="history.back()">
              Annuler
            </button>
          </div>
 
        </div><!-- /ac-form-col -->
 
        <!-- Colonne droite desktop -->
        <div class="ac-right-col">
          <div class="ac-info-card card">
            <div class="ac-info-card-title">
              <i class="ri-information-line"></i> Comment ça marche ?
            </div>
            <div class="ac-info-steps">
              <div class="ac-step">
                <div class="ac-step-num">1</div>
                <div class="ac-step-text">Choisissez le type de cotisation et la période concernée.</div>
              </div>
              <div class="ac-step">
                <div class="ac-step-num">2</div>
                <div class="ac-step-text">Saisissez le montant et le mode de paiement.</div>
              </div>
              <div class="ac-step">
                <div class="ac-step-num">3</div>
                <div class="ac-step-text">Envoyez votre demande. Un administrateur la validera.</div>
              </div>
              <div class="ac-step">
                <div class="ac-step-num">4</div>
                <div class="ac-step-text">Vous recevrez une notification dès la validation.</div>
              </div>
            </div>
          </div>
 
          <div class="ac-info-card card" style="margin-top:16px">
            <div class="ac-info-card-title" style="color:var(--danger)">
              <i class="ri-alarm-warning-line"></i> Mois en retard
            </div>
            <div style="font-size:13px;color:var(--muted);line-height:1.6">
              Vous avez <strong style="color:var(--danger)">3 mois en retard</strong> :<br>
              Mars, Février, Janvier 2025<br>
              <strong style="color:var(--danger)">30 000 FCFA</strong> dus au total.
            </div>
          </div>
        </div>
 
      </div><!-- /ac-layout -->
 
      <div style="height:24px"></div>
    </main>