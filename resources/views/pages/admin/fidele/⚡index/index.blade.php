
<div>


<div class="page-content">
<div class="container-fluid">

  {{-- ══ PAGE HEADER ══════════════════════════════════════ --}}
  <div class="page-header-cust fade-up">
    <div>
      <h4>Gestion des fidèles</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Fidèles</li>
        </ol>
      </nav>
    </div>
    <button class="btn-msq-primary" onclick="openAdd()">
      <i class="ri-user-add-line"></i> Nouveau fidèle
    </button>
  </div>

  {{-- ══ KPI BAR ══════════════════════════════════════════ --}}
  <div class="cust-kpi-bar">
    <div class="ckpi ck-total fade-up fu-1">
      <div class="ckpi-icon" style="background:rgba(64,81,137,.10);color:#405189">
        <i class="ri-group-line"></i>
      </div>
      <div>
        <div class="ckpi-label">Total fidèles</div>
        <div class="ckpi-value" id="kpi-total">—</div>
        <div class="ckpi-sub">Inscrits dans la base</div>
      </div>
    </div>
    <div class="ckpi ck-ajour fade-up fu-2">
      <div class="ckpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
        <i class="ri-checkbox-circle-line"></i>
      </div>
      <div>
        <div class="ckpi-label">À jour</div>
        <div class="ckpi-value" id="kpi-ajour">—</div>
        <div class="ckpi-sub">Cotisation mensuelle OK</div>
      </div>
    </div>
    <div class="ckpi ck-retard fade-up fu-3">
      <div class="ckpi-icon" style="background:rgba(240,101,72,.10);color:#f06548">
        <i class="ri-time-line"></i>
      </div>
      <div>
        <div class="ckpi-label">En retard</div>
        <div class="ckpi-value" id="kpi-retard">—</div>
        <div class="ckpi-sub">ou paiement partiel</div>
      </div>
    </div>
    <div class="ckpi ck-libre fade-up fu-4">
      <div class="ckpi-icon" style="background:rgba(135,138,153,.10);color:#878a99">
        <i class="ri-user-line"></i>
      </div>
      <div>
        <div class="ckpi-label">Sans engagement</div>
        <div class="ckpi-value" id="kpi-libre">—</div>
        <div class="ckpi-sub">Pas de mensuel souscrit</div>
      </div>
    </div>
  </div>

  {{-- ══ TOOLBAR ══════════════════════════════════════════ --}}
  <div class="cust-toolbar fade-up fu-3">
    {{-- Recherche --}}
    <div class="search-wrap">
      <i class="ri-search-line"></i>
      <input type="text" id="cust-search" placeholder="Rechercher un fidèle…">
    </div>

    {{-- Filtre statut --}}
    <select class="filter-select" id="cust-filter">
      <option value="tous">Tous les statuts</option>
      <option value="ajour">À jour</option>
      <option value="retard">En retard</option>
      <option value="partiel">Partiel</option>
      <option value="libre">Sans engagement</option>
    </select>

    {{-- Filtre par mois (placeholder) --}}
    <select class="filter-select" style="min-width:130px">
      <option>Avril 2025</option>
      <option>Mars 2025</option>
      <option>Février 2025</option>
    </select>

    {{-- Vue --}}
    <div class="view-toggle">
      <button class="vt-btn active" id="btn-table-view" title="Vue tableau"><i class="ri-list-check"></i></button>
      <button class="vt-btn" id="btn-grid-view" title="Vue grille"><i class="ri-layout-grid-line"></i></button>
    </div>

    {{-- Export --}}
    <button class="btn btn-soft-success btn-sm waves-effect" title="Exporter Excel">
      <i class="ri-file-excel-2-line me-1"></i>Export
    </button>
  </div>

  {{-- ══ VUE TABLEAU ══════════════════════════════════════ --}}
  <div id="table-view" class="fade-up fu-4">
    <div class="cust-table-card">
      <div class="table-responsive">
        <table class="cust-table">
          <thead>
            <tr>
              <th>Fidèle</th>
              <th>Adresse</th>
              <th>Engagement mensuel</th>
              <th>Date adhésion</th>
              <th>Statut cotisation</th>
              <th>Montant dû</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="cust-tbody">
            {{-- Peuplé par customers.js --}}
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="cust-pagination">
        <span class="pag-info" id="pag-info">—</span>
        <div class="pag-btns" id="pag-btns"></div>
      </div>
    </div>
  </div>

  {{-- ══ VUE GRILLE ════════════════════════════════════════ --}}
  <div id="grid-view" style="display:none" class="fade-up fu-4">
    <div class="cust-grid" id="cust-grid">
      {{-- Peuplé par customers.js --}}
    </div>
    <div class="cust-pagination mt-3" style="background:var(--msq-surface);border-radius:var(--msq-radius);border:1px solid var(--msq-border);box-shadow:var(--msq-shadow-sm);">
      <span class="pag-info" id="pag-info-grid">—</span>
      <div class="pag-btns" id="pag-btns-grid"></div>
    </div>
  </div>

</div>{{-- /container-fluid --}}
</div>{{-- /page-content --}}


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL FIDÈLE
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailFidele" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      {{-- Header coloré --}}
      <div class="modal-fidele-header">
        <button class="close-btn" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>

        {{-- Badge statut en haut à gauche --}}
        <div class="mfh-badges">
          <span class="mfh-badge" id="mfh-statut-badge">—</span>
        </div>

        {{-- Avatar + Infos --}}
        <div class="mfh-inner">
          <div class="mfh-avatar" id="mfh-avatar">AB</div>
          <div class="mfh-info">
            <div class="mfh-name" id="mfh-name">—</div>
            <div class="mfh-meta">
              <span><i class="ri-phone-line"></i> <span id="mfh-phone">—</span></span>
              <span><i class="ri-map-pin-line"></i> <span id="mfh-adresse">—</span></span>
              <span><i class="ri-calendar-line"></i> <span id="mfh-date">—</span></span>
            </div>
          </div>
        </div>
      </div>

      {{-- Stat boxes en overlap --}}
      <div class="modal-stat-row">
        <div class="modal-stat-box">
          <div class="msb-value" id="ms-paiements">—</div>
          <div class="msb-label">Paiements effectués</div>
        </div>
        <div class="modal-stat-box">
          <div class="msb-value" id="ms-total" style="font-size:13px;">—</div>
          <div class="msb-label">Total payé</div>
        </div>
        <div class="modal-stat-box">
          <div class="msb-value" id="ms-docs">—</div>
          <div class="msb-label">Documents</div>
        </div>
      </div>

      {{-- Body avec onglets --}}
      <div class="modal-fidele-body">

        {{-- Onglets --}}
        <div class="fidele-tabs">
          <button class="fidele-tab active" data-tab="tab-infos">
            <i class="ri-user-line"></i> Informations
          </button>
          <button class="fidele-tab" data-tab="tab-cotisations">
            <i class="ri-calendar-check-line"></i> Cotisations
          </button>
          <button class="fidele-tab" data-tab="tab-documents">
            <i class="ri-folder-line"></i> Documents
          </button>
        </div>

        {{-- Panel : Infos --}}
        <div class="fidele-tab-panel active" id="tab-infos">
          <div class="detail-grid">
            <div class="detail-item">
              <div class="di-label"><i class="ri-user-line me-1"></i>Nom complet</div>
              <div class="di-value" id="di-nom">—</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-phone-line me-1"></i>Téléphone</div>
              <div class="di-value" id="di-tel">—</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-map-pin-line me-1"></i>Adresse</div>
              <div class="di-value" id="di-adresse">—</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-calendar-line me-1"></i>Date d'adhésion</div>
              <div class="di-value" id="di-adhesion">—</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-money-cny-circle-line me-1"></i>Engagement mensuel</div>
              <div class="di-value" id="di-eng" style="color:#405189;font-weight:800">—</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-checkbox-circle-line me-1"></i>Statut actuel</div>
              <div class="di-value" id="di-statut">—</div>
            </div>
          </div>

          {{-- Retard info --}}
          <div class="detail-item" style="border-left:3px solid #f06548;border-radius:10px;">
            <div class="di-label"><i class="ri-time-line me-1"></i>Retard de cotisation</div>
            <div class="di-value" id="di-retard" style="color:#f06548">—</div>
          </div>

          {{-- Actions --}}
          <div class="d-flex gap-2 mt-4 flex-wrap">
            <button class="btn btn-primary waves-effect" onclick="openEdit(state.currentId)">
              <i class="ri-edit-line me-1"></i> Modifier
            </button>
            <button class="btn btn-soft-success waves-effect" onclick="alert('Ajouter cotisation (à implémenter via Livewire)')">
              <i class="ri-add-circle-line me-1"></i> Créer cotisation
            </button>
            <button class="btn btn-soft-danger waves-effect" onclick="confirmDelete(state.currentId)">
              <i class="ri-delete-bin-line me-1"></i> Supprimer
            </button>
          </div>
        </div>

        {{-- Panel : Cotisations --}}
        <div class="fidele-tab-panel" id="tab-cotisations">
          <div style="overflow-x:auto">
            <table class="hist-table">
              <thead>
                <tr>
                  <th>Période</th>
                  <th>Type</th>
                  <th>Montant dû</th>
                  <th>Montant payé</th>
                  <th>Statut</th>
                  <th>Mode</th>
                </tr>
              </thead>
              <tbody id="hist-tbody">
                {{-- Peuplé par JS --}}
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            <a href="#" class="btn btn-soft-primary btn-sm waves-effect">
              <i class="ri-list-check me-1"></i> Voir toutes les cotisations
            </a>
          </div>
        </div>

        {{-- Panel : Documents --}}
        <div class="fidele-tab-panel" id="tab-documents">
          <div class="doc-list" id="docs-list">
            {{-- Peuplé par JS --}}
          </div>
          <div class="mt-3">
            <button class="btn btn-soft-primary btn-sm waves-effect">
              <i class="ri-upload-cloud-line me-1"></i> Ajouter un document
            </button>
          </div>
        </div>

      </div>{{-- /modal-fidele-body --}}
    </div>{{-- /modal-content --}}
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL AJOUT / MODIFICATION FIDÈLE
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalAddFidele" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      {{-- Header --}}
      <div class="modal-add-header">
        <div class="mah-title">
          <div class="mah-icon"><i class="ri-user-add-line"></i></div>
          <div>
            <h5 id="modal-add-title">Nouveau fidèle</h5>
            <p id="modal-add-subtitle">Renseigner les informations</p>
          </div>
        </div>
        <button class="close-btn-wh" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      {{-- Steps --}}
      <div class="add-steps">
        <button class="add-step-btn active" id="step-btn-1">
          <span class="step-num">1</span> Identité
        </button>
        <button class="add-step-btn" id="step-btn-2">
          <span class="step-num">2</span> Engagement
        </button>
      </div>

      {{-- Form --}}
      <form id="add-fidele-form" onsubmit="return false">

        {{-- Panel 1 : Identité --}}
        <div class="add-panel active" id="panel-1">
          <div class="row g-3">

            <div class="col-6">
              <label class="form-label-msq">Prénom <span class="req">*</span></label>
              <div class="input-with-icon">
                <i class="ri-user-line ii-icon"></i>
                <input type="text" class="input-msq" id="f-prenom" placeholder="ex : Mamadou">
              </div>
              <div class="error-msg" id="f-prenom-err"></div>
            </div>

            <div class="col-6">
              <label class="form-label-msq">Nom <span class="req">*</span></label>
              <div class="input-with-icon">
                <i class="ri-user-line ii-icon"></i>
                <input type="text" class="input-msq" id="f-nom" placeholder="ex : Koné">
              </div>
              <div class="error-msg" id="f-nom-err"></div>
            </div>

            <div class="col-12">
              <label class="form-label-msq">Téléphone <span class="req">*</span></label>
              <div class="dial-group">
                <select class="dial-select" id="f-dial">
                  <option value="+225">🇨🇮 +225</option>
                  <option value="+223">🇲🇱 +223</option>
                  <option value="+226">🇧🇫 +226</option>
                  <option value="+227">🇳🇪 +227</option>
                  <option value="+228">🇹🇬 +228</option>
                  <option value="+229">🇧🇯 +229</option>
                </select>
                <div class="input-with-icon" style="flex:1">
                  <i class="ri-phone-line ii-icon"></i>
                  <input type="tel" class="input-msq" id="f-tel" placeholder="07 00 00 00 00">
                </div>
              </div>
              <div class="error-msg" id="f-tel-err"></div>
            </div>

            <div class="col-12">
              <label class="form-label-msq">Adresse</label>
              <div class="input-with-icon">
                <i class="ri-map-pin-line ii-icon"></i>
                <input type="text" class="input-msq" id="f-adresse" placeholder="ex : Yopougon, Abidjan">
              </div>
            </div>

            <div class="col-12">
              <label class="form-label-msq">Date d'adhésion <span class="req">*</span></label>
              <div class="input-with-icon">
                <i class="ri-calendar-line ii-icon"></i>
                <input type="date" class="input-msq" id="f-adhesion" value="{{ date('Y-m-d') }}">
              </div>
              <div class="error-msg" id="f-adhesion-err"></div>
            </div>

          </div>
        </div>

        {{-- Panel 2 : Engagement mensuel --}}
        <div class="add-panel" id="panel-2">

          <p style="font-size:13px;color:var(--msq-muted);margin-bottom:16px;">
            <i class="ri-information-line me-1"></i>
            Sélectionnez le montant d'engagement mensuel du fidèle.
            Laissez vide si le fidèle ne souscrit pas à une cotisation mensuelle.
          </p>

          <label class="form-label-msq">Montant d'engagement mensuel</label>
          <div class="engagement-grid">
            <div class="eng-pill" data-val="1000">
              <div class="ep-val">1 000</div>
              <div class="ep-lbl">FCFA / mois</div>
            </div>
            <div class="eng-pill" data-val="2000">
              <div class="ep-val">2 000</div>
              <div class="ep-lbl">FCFA / mois</div>
            </div>
            <div class="eng-pill" data-val="5000">
              <div class="ep-val">5 000</div>
              <div class="ep-lbl">FCFA / mois</div>
            </div>
            <div class="eng-pill" data-val="10000">
              <div class="ep-val">10 000</div>
              <div class="ep-lbl">FCFA / mois</div>
            </div>
            <div class="eng-pill" data-val="15000">
              <div class="ep-val">15 000</div>
              <div class="ep-lbl">FCFA / mois</div>
            </div>
            <div class="eng-pill" data-val="20000">
              <div class="ep-val">20 000</div>
              <div class="ep-lbl">FCFA / mois</div>
            </div>
            <div class="eng-pill" data-val="25000">
              <div class="ep-val">25 000</div>
              <div class="ep-lbl">FCFA / mois</div>
            </div>
            <div class="eng-pill" data-val="50000">
              <div class="ep-val">50 000</div>
              <div class="ep-lbl">FCFA / mois</div>
            </div>
          </div>

          {{-- Montant personnalisé --}}
          <div class="mt-3">
            <label class="form-label-msq">Ou saisir un montant personnalisé</label>
            <div class="input-with-icon">
              <i class="ri-money-cny-circle-line ii-icon"></i>
              <input type="number" class="input-msq" id="f-engagement-custom"
                     placeholder="Montant en FCFA"
                     oninput="document.getElementById('selected-engagement').value=this.value; document.querySelectorAll('.eng-pill').forEach(p=>p.classList.remove('selected'))">
            </div>
          </div>

          {{-- Champ caché valeur sélectionnée --}}
          <input type="hidden" id="selected-engagement" value="">

          {{-- Note --}}
          <div class="mt-4 p-3" style="background:rgba(64,81,137,.06);border-radius:10px;border-left:3px solid #405189;">
            <p style="font-size:12px;color:var(--msq-text);margin:0;">
              <i class="ri-information-line me-1" style="color:#405189"></i>
              <strong>Note :</strong> Si un engagement est sélectionné, une première cotisation sera automatiquement créée
              pour le mois en cours avec le statut <em>En retard</em> jusqu'au premier paiement.
            </p>
          </div>
        </div>

      </form>

      {{-- Footer --}}
      <div class="modal-add-footer">
        <button class="btn-msq-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        <div class="d-flex gap-2">
          <button class="btn-msq-secondary" id="btn-prev" onclick="prevStep()" style="display:none">
            <i class="ri-arrow-left-line me-1"></i> Précédent
          </button>
          <button class="btn-msq-primary" id="btn-next" onclick="nextStep()">
            Suivant <i class="ri-arrow-right-line ms-1"></i>
          </button>
          <button class="btn-msq-primary" id="btn-save" onclick="saveForm()" style="display:none">
            <i class="ri-save-line me-1"></i> Enregistrer
          </button>
        </div>
      </div>

    </div>{{-- /modal-content --}}
  </div>
</div>


</div>


@push('styles')
<link href="{{ asset('assets/css/customers.css') }}" rel="stylesheet" type="text/css" />
@endpush



@push('scripts')
<script src="{{ asset('assets/js/customers.js') }}"></script>
@endpush