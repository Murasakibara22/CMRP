
<div>



<div class="page-content">
<div class="container-fluid">

  {{-- ══ PAGE HEADER ══════════════════════════════════════ --}}
  <div class="tc-page-header fu fu-1">
    <div>
      <h4>Types de Cotisation</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Types de cotisation</li>
        </ol>
      </nav>
    </div>
    <button class="btn-tc-primary" onclick="openAdd()">
      <i class="ri-add-circle-line"></i> Nouveau type
    </button>
  </div>

  {{-- ══ KPI STRIP ════════════════════════════════════════ --}}
  <div class="tc-kpi-strip">
    <div class="tc-kpi kc-all fu fu-1">
      <div class="ki-icon" style="background:rgba(64,81,137,.10);color:#405189">
        <i class="ri-file-list-3-line"></i>
      </div>
      <div>
        <div class="ki-label">Total types</div>
        <div class="ki-value" id="kpi-tc-total">—</div>
        <div class="ki-sub">Types configurés</div>
      </div>
    </div>
    <div class="tc-kpi kc-actif fu fu-2">
      <div class="ki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
        <i class="ri-checkbox-circle-line"></i>
      </div>
      <div>
        <div class="ki-label">Actifs</div>
        <div class="ki-value" id="kpi-tc-actifs">—</div>
        <div class="ki-sub">Actuellement actifs</div>
      </div>
    </div>
    <div class="tc-kpi kc-requis fu fu-3">
      <div class="ki-icon" style="background:rgba(247,184,75,.12);color:#f7b84b">
        <i class="ri-lock-line"></i>
      </div>
      <div>
        <div class="ki-label">Obligatoires</div>
        <div class="ki-value" id="kpi-tc-requis">—</div>
        <div class="ki-sub">Avec is_required = vrai</div>
      </div>
    </div>
    <div class="tc-kpi kc-encours fu fu-4">
      <div class="ki-icon" style="background:rgba(41,156,219,.10);color:#299cdb">
        <i class="ri-timer-line"></i>
      </div>
      <div>
        <div class="ki-label">En cours</div>
        <div class="ki-value" id="kpi-tc-cours">—</div>
        <div class="ki-sub">Collectes actives</div>
      </div>
    </div>
  </div>

  {{-- ══ LÉGENDE DES TYPES ════════════════════════════════ --}}
  <div class="fu fu-2 mb-4">
    <div style="background:var(--tc-surface);border:1px solid var(--tc-border);border-radius:var(--tc-radius);padding:16px 20px;box-shadow:var(--tc-shadow-sm);">
      <div style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:var(--tc-muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;">
        <i class="ri-information-line" style="font-size:14px;color:var(--tc-primary)"></i>
        Guide des types de cotisation
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px;">
        <div style="display:flex;align-items:center;gap:10px;background:var(--tc-bg);border-radius:9px;padding:10px 12px;">
          <span style="width:34px;height:34px;border-radius:9px;background:rgba(64,81,137,.10);color:#405189;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0"><i class="ri-calendar-check-line"></i></span>
          <div>
            <div style="font-size:13px;font-weight:700;color:#212529">Mensuel</div>
            <div style="font-size:11px;color:var(--tc-muted)">Engagement récurrent + is_required</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;background:var(--tc-bg);border-radius:9px;padding:10px 12px;">
          <span style="width:34px;height:34px;border-radius:9px;background:rgba(10,179,156,.10);color:#0ab39c;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0"><i class="ri-gift-line"></i></span>
          <div>
            <div style="font-size:13px;font-weight:700;color:#212529">Ordinaire</div>
            <div style="font-size:11px;color:var(--tc-muted)">Don libre, quand le fidèle veut</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;background:var(--tc-bg);border-radius:9px;padding:10px 12px;">
          <span style="width:34px;height:34px;border-radius:9px;background:rgba(212,168,67,.12);color:#d4a843;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0"><i class="ri-hand-heart-line"></i></span>
          <div>
            <div style="font-size:13px;font-weight:700;color:#212529">Jour précis</div>
            <div style="font-size:11px;color:var(--tc-muted)">Quête un jour fixe (ex: vendredi)</div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;background:var(--tc-bg);border-radius:9px;padding:10px 12px;">
          <span style="width:34px;height:34px;border-radius:9px;background:rgba(41,156,219,.12);color:#299cdb;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0"><i class="ri-moon-line"></i></span>
          <div>
            <div style="font-size:13px;font-weight:700;color:#212529">Ramadan</div>
            <div style="font-size:11px;color:var(--tc-muted)">Période bornée start_at / end_at</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ══ TOOLBAR ══════════════════════════════════════════ --}}
  <div class="tc-toolbar fu fu-3">
    <div class="search-wrap">
      <i class="ri-search-line"></i>
      <input type="text" id="tc-search" placeholder="Rechercher un type…">
    </div>
    <select class="tc-filter-sel" id="tc-filter-type">
      <option value="tous">Tous les types</option>
      <option value="mensuel">Mensuel</option>
      <option value="ordinaire">Ordinaire</option>
      <option value="jour_precis">Jour précis</option>
      <option value="ramadan">Ramadan</option>
    </select>
    <select class="tc-filter-sel" id="tc-filter-status" style="min-width:130px">
      <option value="tous">Tous les statuts</option>
      <option value="actif">Actif</option>
      <option value="inactif">Inactif</option>
    </select>
    <div class="tc-view-toggle">
      <button class="tc-vt active" id="btn-tc-grid" title="Vue grille"><i class="ri-layout-grid-line"></i></button>
      <button class="tc-vt" id="btn-tc-list" title="Vue liste"><i class="ri-list-check"></i></button>
    </div>
  </div>

  {{-- ══ VUE GRILLE ════════════════════════════════════════ --}}
  <div id="tc-grid-view-wrap">
    <div class="tc-grid" id="tc-grid-view">
      {{-- Peuplé par type-cotisation.js --}}
    </div>
  </div>

  {{-- ══ VUE LISTE ════════════════════════════════════════ --}}
  <div id="tc-list-view-wrap" style="display:none">
    <div class="tc-list-card">
      <div class="table-responsive">
        <table class="tc-list-table">
          <thead>
            <tr>
              <th>Type de cotisation</th>
              <th>Catégorie</th>
              <th>Obligatoire</th>
              <th>Objectif</th>
              <th>Collecte totale</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tc-list-tbody">
            {{-- Peuplé par type-cotisation.js --}}
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>{{-- /container-fluid --}}
</div>{{-- /page-content --}}


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL TYPE COTISATION
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailTC" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      {{-- Header coloré dynamique --}}
      <div class="modal-detail-tc-header" id="mdt-header" style="background:linear-gradient(130deg,#2d3a63,#405189);">
        <button class="mdt-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="mdt-inner">
          <div class="mdt-icon" id="mdt-icon"><i class="ri-file-list-3-line"></i></div>
          <div class="mdt-info">
            <div class="mdt-name" id="mdt-name">—</div>
            <div class="mdt-meta">
              <span><i class="ri-tag-line"></i> <span id="mdt-type-span">—</span></span>
              <span id="mdt-status-span" style="color:rgba(255,255,255,.65)">—</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Stats en overlap --}}
      <div class="mdt-stat-row">
        <div class="mdt-stat-box">
          <div class="msb-val" id="mdt-s1" style="font-size:13px">—</div>
          <div class="msb-lbl">Total collecté</div>
        </div>
        <div class="mdt-stat-box">
          <div class="msb-val" id="mdt-s2">—</div>
          <div class="msb-lbl">Contributions</div>
        </div>
        <div class="mdt-stat-box">
          <div class="msb-val" id="mdt-s3">—</div>
          <div class="msb-lbl">Fidèles concernés</div>
        </div>
      </div>

      <div class="mdt-body">

        {{-- Objectif progress --}}
        <div class="obj-progress-wrap" id="obj-progress-wrap">
          <div class="opw-header">
            <span class="opw-title"><i class="ri-target-line me-1"></i>Progression vers l'objectif</span>
            <span class="opw-pct" id="opw-pct">0%</span>
          </div>
          <div class="opw-bar">
            <div class="opw-fill" id="opw-fill" style="width:0%"></div>
          </div>
          <div class="opw-footer">
            <span id="opw-collecte">—</span>
            <span id="opw-objectif">—</span>
          </div>
        </div>

        {{-- Configuration --}}
        <div style="font-size:11px;font-weight:800;color:var(--tc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px dashed var(--tc-border);display:flex;align-items:center;gap:8px;">
          <span style="display:inline-block;width:3px;height:14px;background:var(--tc-primary);border-radius:2px"></span>
          Configuration
        </div>
        <div class="config-grid">
          <div class="config-item">
            <div class="ci-label"><i class="ri-tag-line me-1"></i>Type</div>
            <div class="ci-value" id="cfg-type">—</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-lock-line me-1"></i>Obligatoire</div>
            <div class="ci-value" id="cfg-required">—</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-target-line me-1"></i>Objectif global</div>
            <div class="ci-value" id="cfg-objectif">—</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-calendar-line me-1"></i>Jour récurrence</div>
            <div class="ci-value" id="cfg-jour">—</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-calendar-event-line me-1"></i>Début période</div>
            <div class="ci-value" id="cfg-start">—</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-calendar-close-line me-1"></i>Fin période</div>
            <div class="ci-value" id="cfg-end">—</div>
          </div>
          <div class="config-item full">
            <div class="ci-label"><i class="ri-text me-1"></i>Description</div>
            <div class="ci-value" id="cfg-desc" style="font-weight:500;font-size:13px;color:var(--tc-text)">—</div>
          </div>
        </div>

        {{-- Contributions récentes --}}
        <div style="font-size:11px;font-weight:800;color:var(--tc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;margin-top:4px;padding-bottom:8px;border-bottom:1px dashed var(--tc-border);display:flex;align-items:center;gap:8px;">
          <span style="display:inline-block;width:3px;height:14px;background:var(--tc-accent);border-radius:2px"></span>
          Contributions récentes
        </div>
        <div class="contrib-list" id="contrib-list">
          {{-- Peuplé par JS --}}
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2 mt-4 flex-wrap">
          <button class="btn btn-primary waves-effect" id="mdt-edit-btn">
            <i class="ri-edit-line me-1"></i> Modifier la configuration
          </button>
          <a href="#" class="btn btn-soft-success waves-effect">
            <i class="ri-list-check me-1"></i> Voir les cotisations liées
          </a>
        </div>

      </div>{{-- /mdt-body --}}
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL AJOUT / MODIFICATION TYPE COTISATION
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTypeCotisation" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      {{-- Header --}}
      <div class="modal-tc-header">
        <div class="mth-left">
          <div class="mth-icon"><i class="ri-settings-3-line"></i></div>
          <div>
            <p class="mth-title" id="modal-tc-title">Nouveau type de cotisation</p>
            <p class="mth-sub" id="modal-tc-sub">Définissez le type, les règles et la configuration.</p>
          </div>
        </div>
        <button class="modal-tc-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      {{-- Scrollable body --}}
      <div style="overflow-y:auto;max-height:calc(90vh - 180px);">
        <form id="tc-form" onsubmit="return false">

          {{-- Section 1 : Informations de base --}}
          <div style="padding:24px 24px 0">
            <div class="form-section">
              <div class="form-section-title">Informations générales</div>

              <div class="mb-3">
                <label class="form-label-tc">Libellé <span class="req">*</span></label>
                <div class="input-tc-wrap">
                  <i class="ri-text itw-icon"></i>
                  <input type="text" class="input-tc" id="tc-libelle" placeholder="ex : Cotisation mensuelle, Quête du vendredi…">
                </div>
                <div class="err-msg" id="tc-libelle-err"></div>
              </div>

              <div>
                <label class="form-label-tc">Description</label>
                <textarea class="input-tc" id="tc-description" rows="2" placeholder="Description optionnelle du type de cotisation…"></textarea>
              </div>
            </div>

            {{-- Section 2 : Type --}}
            <div class="form-section">
              <div class="form-section-title">Type de cotisation</div>
              <input type="hidden" id="hidden-type" value="">

              <div class="type-selector-grid">
                <button type="button" class="type-sel-btn" data-type="mensuel">
                  <div class="tsb-icon" style="background:rgba(64,81,137,.10);color:#405189">
                    <i class="ri-calendar-check-line"></i>
                  </div>
                  <div class="tsb-info">
                    <div class="tsb-name">Mensuel</div>
                    <div class="tsb-desc">Engagement récurrent chaque mois</div>
                  </div>
                </button>
                <button type="button" class="type-sel-btn" data-type="ordinaire">
                  <div class="tsb-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
                    <i class="ri-gift-line"></i>
                  </div>
                  <div class="tsb-info">
                    <div class="tsb-name">Ordinaire</div>
                    <div class="tsb-desc">Don libre, aucune contrainte</div>
                  </div>
                </button>
                <button type="button" class="type-sel-btn" data-type="jour_precis">
                  <div class="tsb-icon" style="background:rgba(212,168,67,.12);color:#d4a843">
                    <i class="ri-hand-heart-line"></i>
                  </div>
                  <div class="tsb-info">
                    <div class="tsb-name">Jour précis</div>
                    <div class="tsb-desc">Collecte un jour fixe (quête)</div>
                  </div>
                </button>
                <button type="button" class="type-sel-btn" data-type="ramadan">
                  <div class="tsb-icon" style="background:rgba(41,156,219,.12);color:#299cdb">
                    <i class="ri-moon-line"></i>
                  </div>
                  <div class="tsb-info">
                    <div class="tsb-name">Ramadan</div>
                    <div class="tsb-desc">Période bornée (Ramadan, fête…)</div>
                  </div>
                </button>
              </div>
            </div>

            {{-- Section 3 : Règles --}}
            <div class="form-section">
              <div class="form-section-title">Règles de cotisation</div>

              {{-- is_required --}}
              <div class="req-toggle-row mb-3">
                <div class="rtr-left">
                  <div class="rtr-icon"><i class="ri-lock-line"></i></div>
                  <div class="rtr-text">
                    <div class="rt-title">Cotisation obligatoire <span style="font-size:10px;background:rgba(247,184,75,.12);color:#f7b84b;padding:2px 7px;border-radius:10px;font-weight:700;margin-left:6px">is_required</span></div>
                    <div class="rt-sub">Permet de suivre les fidèles en retard et calculer les montants dus</div>
                  </div>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" id="tc-is-required">
                  <span class="toggle-slider"></span>
                </label>
              </div>

              {{-- Champ conditionnel : Jour de récurrence (jour_precis) --}}
              <div class="cond-field" id="cond-jour">
                <label class="form-label-tc"><i class="ri-calendar-line me-1"></i>Jour de collecte</label>
                <select class="input-tc" id="tc-jour" style="cursor:pointer">
                  <option value="">— Sélectionner un jour —</option>
                  <option value="lundi">Lundi</option>
                  <option value="mardi">Mardi</option>
                  <option value="mercredi">Mercredi</option>
                  <option value="jeudi">Jeudi</option>
                  <option value="vendredi">Vendredi (Jumu'ah)</option>
                  <option value="samedi">Samedi</option>
                  <option value="dimanche">Dimanche</option>
                </select>
              </div>

              {{-- Champ conditionnel : Période (ramadan / ordinaire) --}}
              <div class="cond-field" id="cond-periode">
                <div class="row g-2 mb-0">
                  <div class="col-6">
                    <label class="form-label-tc"><i class="ri-calendar-event-line me-1"></i>Date de début</label>
                    <input type="date" class="input-tc" id="tc-start">
                  </div>
                  <div class="col-6">
                    <label class="form-label-tc"><i class="ri-calendar-close-line me-1"></i>Date de fin</label>
                    <input type="date" class="input-tc" id="tc-end">
                  </div>
                </div>
              </div>

              {{-- Champ conditionnel : Objectif global --}}
              <div class="cond-field" id="cond-objectif">
                <label class="form-label-tc"><i class="ri-target-line me-1"></i>Objectif global de collecte
                  <span style="font-size:10px;color:var(--tc-muted);font-weight:500;text-transform:none;letter-spacing:0">(optionnel)</span>
                </label>
                <div class="input-tc-wrap">
                  <i class="ri-money-cny-circle-line itw-icon"></i>
                  <input type="number" class="input-tc with-suffix" id="tc-objectif" placeholder="ex : 500000" min="0">
                  <span class="itw-suffix">FCFA</span>
                </div>
              </div>

            </div>

            {{-- Note contextuelle --}}
            <div class="mb-4 p-3" style="background:rgba(64,81,137,.06);border-radius:10px;border-left:3px solid #405189;">
              <p style="font-size:12px;color:var(--tc-text);margin:0;">
                <i class="ri-information-line me-1" style="color:#405189"></i>
                <strong>Rappel :</strong> Le type <em>mensuel</em> + <em>is_required = oui</em> active le suivi automatique des retards.
                Les autres types (ordinaire, jour précis, Ramadan) peuvent être <em>non requis</em> — chacun cotise librement.
              </p>
            </div>

          </div>{{-- /padding --}}
        </form>
      </div>

      {{-- Footer --}}
      <div class="modal-tc-footer">
        <button class="btn-tc-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        <button class="btn-tc-primary" onclick="saveTcForm()">
          <i class="ri-save-line"></i> Enregistrer
        </button>
      </div>

    </div>{{-- /modal-content --}}
  </div>
</div>

</div>



@push('styles')
<link href="{{ asset('assets/css/type-cotisation.css') }}" rel="stylesheet" type="text/css" />
@endpush



@push('scripts')
<script src="{{ asset('assets/js/type-cotisation.js') }}"></script>
@endpush
