
<div>



<div class="page-content">
<div class="container-fluid">

  {{-- ══ PAGE HEADER ══════════════════════════════════════ --}}
  <div class="co-page-header fu fu-1">
    <div>
      <h4>Cotisations</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Cotisations</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <button class="btn btn-soft-primary btn-sm waves-effect" onclick="openCreate()">
        <i class="ri-money-cny-circle-line me-1"></i> Enregistrer paiement BO
      </button>
      <button class="btn btn-soft-success btn-sm waves-effect">
        <i class="ri-file-excel-2-line me-1"></i> Exporter
      </button>
    </div>
  </div>

  {{-- ══ KPI STRIP ════════════════════════════════════════ --}}
  <div class="co-kpi-strip">
    <div class="co-kpi ck-total fu fu-1">
      <div class="cki" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-file-list-3-line"></i></div>
      <div>
        <div class="cki-label">Total</div>
        <div class="cki-val" id="kpi-co-total">—</div>
        <div class="cki-sub">Toutes cotisations</div>
      </div>
    </div>
    <div class="co-kpi ck-ajour fu fu-2">
      <div class="cki" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="cki-label">À jour</div>
        <div class="cki-val" id="kpi-co-ajour">—</div>
        <div class="cki-sub">Soldées</div>
      </div>
    </div>
    <div class="co-kpi ck-partiel fu fu-3">
      <div class="cki" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-error-warning-line"></i></div>
      <div>
        <div class="cki-label">Partielles</div>
        <div class="cki-val" id="kpi-co-partiel">—</div>
        <div class="cki-sub">Paiement partiel</div>
      </div>
    </div>
    <div class="co-kpi ck-retard fu fu-4">
      <div class="cki" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-time-line"></i></div>
      <div>
        <div class="cki-label">En retard</div>
        <div class="cki-val" id="kpi-co-retard">—</div>
        <div class="cki-sub">montant_paye = 0</div>
      </div>
    </div>
    <div class="co-kpi ck-montant fu fu-5">
      <div class="cki" style="background:rgba(212,168,67,.12);color:#d4a843"><i class="ri-money-cny-circle-line"></i></div>
      <div>
        <div class="cki-label">Total collecté</div>
        <div class="cki-val" id="kpi-co-montant" style="font-size:14px">—</div>
        <div class="cki-sub">Somme montant_paye</div>
      </div>
    </div>
  </div>

  {{-- ══ TABS STATUT ══════════════════════════════════════ --}}
  <div class="co-status-tabs fu fu-2">
    <span class="cst-label"><i class="ri-filter-3-line me-1"></i>Statut :</span>
    <button class="co-tab tab-tous active" data-statut="tous">
      <i class="ri-list-check"></i>Tous <span class="tab-count" id="cnt-tous">0</span>
    </button>
    <button class="co-tab tab-ajour" data-statut="a_jour">
      <i class="ri-checkbox-circle-line"></i>À jour <span class="tab-count" id="cnt-ajour">0</span>
    </button>
    <button class="co-tab tab-partiel" data-statut="partiel">
      <i class="ri-error-warning-line"></i>Partiel <span class="tab-count" id="cnt-partiel">0</span>
    </button>
    <button class="co-tab tab-retard" data-statut="en_retard">
      <i class="ri-time-line"></i>En retard <span class="tab-count" id="cnt-retard">0</span>
    </button>
  </div>

  {{-- ══ TOOLBAR ══════════════════════════════════════════ --}}
  <div class="co-toolbar fu fu-3">
    <div class="sw">
      <i class="ri-search-line"></i>
      <input type="text" id="co-search" placeholder="Rechercher fidèle, type, période…">
    </div>
    <select class="co-sel" id="co-filter-type">
      <option value="tous">Tous types</option>
      <option value="1">Cotisation mensuelle</option>
      <option value="2">Quête du vendredi</option>
      <option value="3">Don ordinaire</option>
      <option value="4">Ramadan 1446</option>
      <option value="5">Collecte Rénovation</option>
    </select>
    <select class="co-sel" id="co-filter-mois" style="min-width:110px">
      <option value="tous">Tous mois</option>
      @foreach(range(1,12) as $m)
      <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
      @endforeach
    </select>
    <select class="co-sel" id="co-filter-mode" style="min-width:140px">
      <option value="tous">Tous modes</option>
      <option value="mobile_money">Mobile Money</option>
      <option value="espece">Espèces</option>
      <option value="virement">Virement</option>
      <option value="nd">Non renseigné</option>
    </select>
  </div>

  {{-- ══ TABLE ════════════════════════════════════════════ --}}
  <div class="co-table-card fu fu-4">
    <div class="table-responsive">
      <table class="co-table">
        <thead>
          <tr>
            <th id="th-fidele" onclick="sortBy('fidele')">
              Fidèle <i class="sort-icon ri-arrow-up-down-line"></i>
            </th>
            <th>Type de cotisation</th>
            <th id="th-periode" onclick="sortBy('periode')">
              Période <i class="sort-icon ri-arrow-up-down-line"></i>
            </th>
            <th id="th-montant" onclick="sortBy('montant')">
              Montant dû <i class="sort-icon ri-arrow-up-down-line"></i>
            </th>
            <th id="th-paye" onclick="sortBy('paye')">
              Payé <i class="sort-icon ri-arrow-up-down-line"></i>
            </th>
            <th>Restant</th>
            <th id="th-statut" onclick="sortBy('statut')">
              Statut <i class="sort-icon ri-arrow-up-down-line"></i>
            </th>
            <th>Mode paiement</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="co-tbody">
          {{-- Peuplé par cotisation.js --}}
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="co-pagination">
      <span class="co-pag-info" id="co-pag-info">—</span>
      <div class="co-pag-btns" id="co-pag-btns"></div>
    </div>
  </div>

  {{-- Note métier --}}
  <div class="fu fu-5 mt-3">
    <div style="background:rgba(64,81,137,.05);border:1px dashed rgba(64,81,137,.2);border-radius:10px;padding:12px 16px;">
      <p style="font-size:11px;color:var(--co-muted);margin:0">
        <i class="ri-information-line me-1" style="color:#405189"></i>
        <strong>Logique de report :</strong> Lors d'un paiement mensuel, le système solde d'abord le dernier mois en retard,
        puis crée automatiquement les cotisations des mois suivants en fonction du surplus. Les cotisations non mensuelles
        (Quête, Don, Ramadan) n'ont pas de <code>mois</code>/<code>annee</code> — elles sont ponctuelles.
        <strong>validated_by</strong> est renseigné uniquement pour les paiements espèces validés manuellement par un admin.
      </p>
    </div>
  </div>

</div>{{-- /container-fluid --}}
</div>{{-- /page-content --}}


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL COTISATION
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailCotisation" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      {{-- Header couleur dynamique selon statut --}}
      <div class="co-modal-header" id="co-modal-hdr">
        <button class="cmh-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="cmh-inner">
          <div class="cmh-icon" id="cmh-icon"><i class="ri-file-list-line"></i></div>
          <div class="cmh-info">
            <div class="cmh-name" id="cmh-name">—</div>
            <div class="cmh-meta">
              <span><i class="ri-tag-line"></i><span id="cmh-type">—</span></span>
              <span><i class="ri-calendar-line"></i><span id="cmh-period">—</span></span>
              <span id="cmh-statut"></span>
            </div>
          </div>
        </div>
      </div>

      {{-- Stats overlap --}}
      <div class="co-modal-stats">
        <div class="co-ms-box">
          <div class="msb-v" id="cms1" style="font-size:13px">—</div>
          <div class="msb-l">Montant dû</div>
        </div>
        <div class="co-ms-box">
          <div class="msb-v" id="cms2" style="font-size:13px;color:#0ab39c">—</div>
          <div class="msb-l">Montant payé</div>
        </div>
        <div class="co-ms-box">
          <div class="msb-v" id="cms3" style="font-size:13px;color:#f06548">—</div>
          <div class="msb-l">Restant</div>
        </div>
        <div class="co-ms-box">
          <div class="msb-v" id="cms4">—</div>
          <div class="msb-l">Progression</div>
        </div>
      </div>

      <div class="co-modal-body">

        {{-- Barre de progression paiement --}}
        <div class="co-pay-progress">
          <div class="cpp-header">
            <span class="cpp-title"><i class="ri-bar-chart-line me-1"></i>Progression du paiement</span>
            <span class="cpp-pct" id="cpp-pct">0%</span>
          </div>
          <div class="cpp-track">
            <div class="cpp-fill" id="cpp-fill" style="width:0%"></div>
          </div>
          <div class="cpp-footer">
            <span class="paid" id="cpp-paid">—</span>
            <span class="due"  id="cpp-due">—</span>
          </div>
        </div>

        {{-- Grille infos --}}
        <div style="font-size:11px;font-weight:800;color:var(--co-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;padding-bottom:7px;border-bottom:1px dashed var(--co-border);display:flex;align-items:center;gap:8px;">
          <span style="display:inline-block;width:3px;height:13px;background:var(--co-primary);border-radius:2px"></span>
          Détails de la cotisation
        </div>
        <div class="co-detail-grid">
          <div class="co-detail-item">
            <div class="di-l"><i class="ri-user-line me-1"></i>Fidèle</div>
            <div class="di-v" id="di-fidele">—</div>
          </div>
          <div class="co-detail-item">
            <div class="di-l"><i class="ri-money-cny-circle-line me-1"></i>Engagement mensuel</div>
            <div class="di-v" id="di-engagement" style="color:#405189">—</div>
          </div>
          <div class="co-detail-item">
            <div class="di-l"><i class="ri-tag-line me-1"></i>Type de cotisation</div>
            <div class="di-v" id="di-type">—</div>
          </div>
          <div class="co-detail-item">
            <div class="di-l"><i class="ri-calendar-line me-1"></i>Période (mois/annee)</div>
            <div class="di-v" id="di-period">—</div>
          </div>
          <div class="co-detail-item">
            <div class="di-l"><i class="ri-smartphone-line me-1"></i>Mode paiement</div>
            <div class="di-v" id="di-mode">—</div>
          </div>
          <div class="co-detail-item">
            <div class="di-l"><i class="ri-calendar-event-line me-1"></i>Date création</div>
            <div class="di-v" id="di-created">—</div>
          </div>
          <div class="co-detail-item full">
            <div class="di-l"><i class="ri-shield-check-line me-1"></i>Validation (validated_by / validated_at)</div>
            <div class="di-v" id="di-validated">—</div>
          </div>
        </div>

        {{-- Historique --}}
        <div style="font-size:11px;font-weight:800;color:var(--co-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;margin-top:4px;padding-bottom:7px;border-bottom:1px dashed var(--co-border);display:flex;align-items:center;gap:8px;">
          <span style="display:inline-block;width:3px;height:13px;background:var(--co-accent);border-radius:2px"></span>
          Historique (snapshot_cotisation)
        </div>
        <div class="co-hist-list" id="co-hist-list">
          {{-- Peuplé par JS --}}
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2 mt-4 flex-wrap" id="detail-actions">
          {{-- Peuplé dynamiquement selon l'état --}}
        </div>

      </div>{{-- /co-modal-body --}}
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL CRÉER COTISATION BO
     Champs : customer_id, type_cotisation_id, mois, annee,
               montant_paye, mode_paiement, validated_by, validated_at
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCreateCotisation" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      {{-- Header --}}
      <div class="co-create-header">
        <div class="cch-inner">
          <div class="cch-icon"><i class="ri-money-cny-circle-line"></i></div>
          <div>
            <h5 id="co-create-title">Enregistrer un paiement</h5>
            <p class="cch-sub" id="co-create-sub">Saisie manuelle BO – Espèces ou autre mode</p>
          </div>
        </div>
        <button class="co-create-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      {{-- Scrollable body --}}
      <div style="overflow-y:auto;max-height:calc(90vh - 170px);">
        <form id="co-form" onsubmit="return false">
          <div style="padding:20px 22px 0">

            {{-- Section 1 : Fidèle --}}
            <div style="font-size:10px;font-weight:800;color:var(--co-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;padding-bottom:6px;border-bottom:1px dashed var(--co-border);display:flex;align-items:center;gap:8px;">
              <span style="display:inline-block;width:3px;height:12px;background:var(--co-primary);border-radius:2px"></span>
              Fidèle
            </div>

            <div class="mb-3">
              <label class="form-label-co">Sélectionner le fidèle <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="ri-user-line iw-icon"></i>
                <select class="input-co" id="f-customer" style="padding-left:38px;cursor:pointer">
                  <option value="">— Choisir un fidèle —</option>
                  <option value="1">Moussa Koné · 10 000 FCFA/mois</option>
                  <option value="2">Fatoumata Traoré · 5 000 FCFA/mois</option>
                  <option value="3">Ibrahim Diabaté · 15 000 FCFA/mois</option>
                  <option value="4">Aminata Coulibaly · 5 000 FCFA/mois</option>
                  <option value="5">Ousmane Bamba · 20 000 FCFA/mois</option>
                  <option value="6">Daouda Ouattara · 10 000 FCFA/mois</option>
                  <option value="7">Kadiatou Sanogo · (pas d'engagement)</option>
                  <option value="8">Seydou Touré · 5 000 FCFA/mois</option>
                </select>
              </div>
              <div class="err-co" id="f-customer-err"></div>
            </div>

            {{-- Card fidèle sélectionné --}}
            <div class="fidele-selected-card mb-3" id="fidele-card" style="display:none">
              <div class="fsc-avatar" id="fsc-avatar">??</div>
              <div>
                <div class="fsc-name" id="fsc-name">—</div>
                <div class="fsc-detail" id="fsc-phone">—</div>
              </div>
              <div class="fsc-eng" id="fsc-eng">
                <div class="fe-val" id="fsc-eng-val">—</div>
                <div class="fe-lbl">FCFA/mois</div>
              </div>
            </div>

            {{-- Section 2 : Type de cotisation --}}
            <div style="font-size:10px;font-weight:800;color:var(--co-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;padding-bottom:6px;border-bottom:1px dashed var(--co-border);display:flex;align-items:center;gap:8px;">
              <span style="display:inline-block;width:3px;height:12px;background:#0ab39c;border-radius:2px"></span>
              Type de cotisation
            </div>

            <div class="mb-3">
              <label class="form-label-co">Type <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="ri-tag-line iw-icon"></i>
                <select class="input-co" id="f-type" style="padding-left:38px;cursor:pointer">
                  <option value="">— Choisir un type —</option>
                  <option value="1">Cotisation mensuelle (Mensuel)</option>
                  <option value="2">Quête du vendredi (Jour précis)</option>
                  <option value="3">Don ordinaire</option>
                  <option value="4">Ramadan 1446</option>
                  <option value="5">Collecte Rénovation</option>
                </select>
              </div>
              <div class="err-co" id="f-type-err"></div>
            </div>

            {{-- Période mois/annee (visible si mensuel) --}}
            <div id="periode-wrap" style="display:none" class="mb-3">
              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label-co"><i class="ri-calendar-line me-1"></i>Mois (mois)</label>
                  <select class="input-co" id="f-mois" style="cursor:pointer">
                    <option value="">—</option>
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                      {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6">
                  <label class="form-label-co"><i class="ri-calendar-2-line me-1"></i>Année (annee)</label>
                  <select class="input-co" id="f-annee" style="cursor:pointer">
                    @foreach([2023,2024,2025,2026] as $y)
                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            {{-- Section 3 : Paiement --}}
            <div style="font-size:10px;font-weight:800;color:var(--co-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;padding-bottom:6px;border-bottom:1px dashed var(--co-border);display:flex;align-items:center;gap:8px;">
              <span style="display:inline-block;width:3px;height:12px;background:var(--co-gold);border-radius:2px"></span>
              Montant & mode
            </div>

            <div class="mb-3">
              <label class="form-label-co">Montant payé <span class="req">*</span></label>
              <div class="input-wrap">
                <i class="ri-money-cny-circle-line iw-icon"></i>
                <input type="number" class="input-co has-sfx" id="f-montant" placeholder="ex : 10000" min="1">
                <span class="iw-suffix">FCFA</span>
              </div>
              <div class="err-co" id="f-montant-err"></div>
            </div>

            {{-- Calcul report automatique --}}
            <div class="report-calc mb-3" id="report-calc" style="display:none">
              <div class="rc-title"><i class="ri-calculator-line"></i>Calcul du report automatique</div>
              <div class="rc-rows" id="rc-rows"></div>
            </div>

            {{-- Mode paiement --}}
            <div class="mb-3">
              <label class="form-label-co">Mode de paiement <span class="req">*</span></label>
              <input type="hidden" id="hidden-mode" value="">
              <div class="mode-grid">
                <button type="button" class="mode-btn" data-mode="espece">
                  <i class="ri-money-dollar-circle-line" style="color:#f7b84b"></i>
                  <span>Espèces</span>
                </button>
                <button type="button" class="mode-btn" data-mode="mobile_money">
                  <i class="ri-smartphone-line" style="color:#0ab39c"></i>
                  <span>Mobile Money</span>
                </button>
                <button type="button" class="mode-btn" data-mode="virement">
                  <i class="ri-bank-line" style="color:#405189"></i>
                  <span>Virement</span>
                </button>
              </div>
            </div>

            {{-- Référence (mobile money) --}}
            <div class="mb-3" id="ref-wrap" style="display:none">
              <label class="form-label-co"><i class="ri-hashtag me-1"></i>Référence transaction</label>
              <div class="input-wrap">
                <i class="ri-hashtag iw-icon"></i>
                <input type="text" class="input-co" id="f-reference" placeholder="ex : OM202504XXXX">
              </div>
            </div>

            {{-- Section 4 : Validation admin --}}
            <div style="font-size:10px;font-weight:800;color:var(--co-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;padding-bottom:6px;border-bottom:1px dashed var(--co-border);display:flex;align-items:center;gap:8px;">
              <span style="display:inline-block;width:3px;height:12px;background:var(--co-warning);border-radius:2px"></span>
              Validation (validated_by / validated_at)
            </div>

            <div class="validation-row mb-4">
              <div class="vr-left">
                <div class="vr-icon"><i class="ri-shield-check-line"></i></div>
                <div>
                  <div class="vr-title">Valider immédiatement</div>
                  <div class="vr-sub">
                    Renseigne <code>validated_by</code> = votre ID et <code>validated_at</code> = maintenant.<br>
                    Si désactivé : paiement en attente de validation.
                  </div>
                </div>
              </div>
              <label class="toggle-sw">
                <input type="checkbox" id="validate-toggle" checked>
                <span class="toggle-sl"></span>
              </label>
            </div>

          </div>{{-- /padding --}}
        </form>
      </div>

      {{-- Footer --}}
      <div class="co-modal-footer">
        <button class="btn-co-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        <button class="btn-co-primary" onclick="saveCreateForm()">
          <i class="ri-save-line"></i> Enregistrer la cotisation
        </button>
      </div>

    </div>{{-- /modal-content --}}
  </div>
</div>

</div>



@push('styles')
<link href="{{ asset('assets/css/cotisation.css') }}" rel="stylesheet" type="text/css" />
@endpush



@push('scripts')
<script src="{{ asset('assets/js/cotisation.js') }}"></script>
@endpush
