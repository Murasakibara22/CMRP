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
    <button class="btn-tc-primary" wire:click="openAdd">
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
        <div class="ki-value">{{ $kpis['total'] }}</div>
        <div class="ki-sub">Types configurés</div>
      </div>
    </div>
    <div class="tc-kpi kc-actif fu fu-2">
      <div class="ki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
        <i class="ri-checkbox-circle-line"></i>
      </div>
      <div>
        <div class="ki-label">Actifs</div>
        <div class="ki-value">{{ $kpis['actifs'] }}</div>
        <div class="ki-sub">Actuellement actifs</div>
      </div>
    </div>
    <div class="tc-kpi kc-requis fu fu-3">
      <div class="ki-icon" style="background:rgba(247,184,75,.12);color:#f7b84b">
        <i class="ri-lock-line"></i>
      </div>
      <div>
        <div class="ki-label">Obligatoires</div>
        <div class="ki-value">{{ $kpis['requis'] }}</div>
        <div class="ki-sub">Avec is_required = vrai</div>
      </div>
    </div>
    <div class="tc-kpi kc-encours fu fu-4">
      <div class="ki-icon" style="background:rgba(41,156,219,.10);color:#299cdb">
        <i class="ri-timer-line"></i>
      </div>
      <div>
        <div class="ki-label">En cours</div>
        <div class="ki-value">{{ $kpis['enCours'] }}</div>
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
      <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher un type…">
    </div>
    <select class="tc-filter-sel" wire:model.live="filterType">
      <option value="tous">Tous les types</option>
      <option value="mensuel">Mensuel</option>
      <option value="ordinaire">Ordinaire</option>
      <option value="jour_precis">Jour précis</option>
      <option value="ramadan">Ramadan</option>
    </select>
    <select class="tc-filter-sel" wire:model.live="filterStatus" style="min-width:130px">
      <option value="tous">Tous les statuts</option>
      <option value="actif">Actif</option>
      <option value="inactif">Inactif</option>
    </select>
    <div class="tc-view-toggle">
      <button class="tc-vt {{ $vue === 'grille' ? 'active' : '' }}" wire:click="setVue('grille')" title="Vue grille">
        <i class="ri-layout-grid-line"></i>
      </button>
      <button class="tc-vt {{ $vue === 'liste' ? 'active' : '' }}" wire:click="setVue('liste')" title="Vue liste">
        <i class="ri-list-check"></i>
      </button>
    </div>
  </div>

  {{-- ══ VUE GRILLE ════════════════════════════════════════ --}}
  <div id="tc-grid-view-wrap" @if($vue !== 'grille') style="display:none" @endif>
    <div class="tc-grid" id="tc-grid-view">
      @forelse($typeCotisations as $tc)
      @php
        $totalCollecte = \App\Models\Cotisation::where('type_cotisation_id', $tc->id)->sum('montant_paye');
        [$tcColor, $tcBg, $tcIcon, $tcLabel] = match($tc->type) {
            'mensuel'     => ['#405189', 'rgba(64,81,137,.12)',  'ri-calendar-check-line', 'Mensuel'],
            'ordinaire'   => ['#0ab39c', 'rgba(10,179,156,.12)', 'ri-gift-line',           'Ordinaire'],
            'jour_precis' => ['#d4a843', 'rgba(212,168,67,.12)', 'ri-hand-heart-line',     'Jour précis'],
            'ramadan'     => ['#299cdb', 'rgba(41,156,219,.12)', 'ri-moon-line',            'Ramadan'],
            default       => ['#878a99', 'rgba(135,138,153,.12)','ri-file-list-3-line',    $tc->type],
        };
        $pct = ($tc->montant_objectif > 0)
               ? min(round(($totalCollecte / $tc->montant_objectif) * 100), 100)
               : null;
        $collecteFormate = $totalCollecte >= 1000000
            ? round($totalCollecte / 1000000, 1) . 'M'
            : ($totalCollecte >= 1000 ? round($totalCollecte / 1000) . 'k' : $totalCollecte);
        $periodeEnCours = $tc->isEnCours();
        $periodeExpiree = $tc->end_at && $tc->end_at->isPast() && !$periodeEnCours;
      @endphp

      <div class="tc-card fu" style="animation-delay:{{ $loop->index * 0.06 }}s" wire:click="openDetail({{ $tc->id }})">
        <div class="tc-card-header" style="border-top-color:{{ $tcColor }}">
          <div class="tc-type-icon" style="background:{{ $tcBg }};color:{{ $tcColor }}">
            <i class="{{ $tcIcon }}"></i>
          </div>
          <div class="tc-name">{{ $tc->libelle }}</div>
          <div class="tc-desc">{{ $tc->description ?? '—' }}</div>

          <div class="tc-badges" wire:click.stop="">
            <span class="tc-badge tb-type"><i class="{{ $tcIcon }} me-1"></i>{{ $tcLabel }}</span>
            @if($tc->status === 'actif')
              <span class="tc-badge tb-actif"><i class="ri-checkbox-circle-line me-1"></i>Actif</span>
            @else
              <span class="tc-badge tb-inactif"><i class="ri-close-circle-line me-1"></i>Inactif</span>
            @endif
            @if($tc->is_required)
              <span class="tc-badge tb-requis"><i class="ri-lock-line me-1"></i>Obligatoire</span>
            @endif
            @if($periodeEnCours)
              <span class="tc-badge tb-encours">En cours</span>
            @elseif($periodeExpiree)
              <span class="tc-badge tb-expire">Expiré</span>
            @endif
          </div>
        </div>

        <div class="tc-card-body">
          <div class="tc-info-grid">
            <div class="tc-info-item">
              <div class="tii-label"><i class="ri-bar-chart-line me-1"></i>Total collecté</div>
              <div class="tii-value" style="color:var(--tc-accent)">{{ number_format($totalCollecte, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="tc-info-item">
              <div class="tii-label"><i class="ri-group-line me-1"></i>Contributions</div>
              <div class="tii-value">{{ $tc->cotisations_count }}</div>
            </div>

            @if($tc->type === 'mensuel')
            <div class="tc-info-item full">
              <div class="tii-label"><i class="ri-money-cny-circle-line me-1"></i>Engagement fidèle</div>
              <div class="tii-value" style="color:var(--tc-primary)">Selon palier choisi à l'adhésion</div>
            </div>
            @endif

            @if($tc->type === 'jour_precis' && $tc->jour_recurrence)
            <div class="tc-info-item full">
              <div class="tii-label"><i class="ri-calendar-line me-1"></i>Jour de collecte</div>
              <div class="tii-value" style="color:var(--tc-gold);text-transform:capitalize">{{ $tc->jour_recurrence }}</div>
            </div>
            @endif

            @if(($tc->start_at || $tc->end_at) && in_array($tc->type, ['ramadan', 'ordinaire']))
            <div class="tc-info-item full">
              <div class="tii-label"><i class="ri-calendar-event-line me-1"></i>Période</div>
              <div class="tii-value" style="font-size:12px">
                {{ $tc->start_at?->format('d/m/Y') ?? '—' }} → {{ $tc->end_at?->format('d/m/Y') ?? '—' }}
              </div>
            </div>
            @endif
          </div>

          @if($pct !== null)
          <div class="tc-obj-bar">
            <div class="ob-header">
              <span class="ob-label"><i class="ri-target-line me-1"></i>Objectif {{ number_format($tc->montant_objectif, 0, ',', ' ') }} FCFA</span>
              <span class="ob-pct" style="color:{{ $tcColor }}">{{ $pct }}%</span>
            </div>
            <div class="ob-track">
              <div class="ob-fill" style="width:{{ $pct }}%;background:{{ $tcColor }}"></div>
            </div>
          </div>
          @endif
        </div>

        <div class="tc-card-footer" wire:click.stop="">
          <div class="tc-stat">
            <div class="ts-val">{{ $collecteFormate }}</div>
            <div class="ts-lbl">Collecté</div>
          </div>
          <div class="divider"></div>
          <div class="tc-stat">
            <div class="ts-val">{{ $tc->cotisations_count }}</div>
            <div class="ts-lbl">Fidèles</div>
          </div>
          <div class="divider"></div>
          <div class="tc-actions">
            <button class="btn btn-soft-primary waves-effect" wire:click="openDetail({{ $tc->id }})" title="Voir détails"><i class="ri-eye-line"></i></button>
            <button class="btn btn-soft-warning waves-effect" wire:click="openEdit({{ $tc->id }})" title="Modifier"><i class="ri-edit-line"></i></button>
            <button class="btn btn-soft-{{ $tc->status === 'actif' ? 'secondary' : 'success' }} waves-effect" wire:click="toggleStatus({{ $tc->id }})" title="{{ $tc->status === 'actif' ? 'Désactiver' : 'Activer' }}">
              <i class="ri-{{ $tc->status === 'actif' ? 'pause' : 'play' }}-circle-line"></i>
            </button>
            <button class="btn btn-soft-danger waves-effect" wire:click="confirmDelete({{ $tc->id }})" title="Supprimer"><i class="ri-delete-bin-line"></i></button>
          </div>
        </div>
      </div>

      @empty
      <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--tc-muted)">
        <i class="ri-search-line" style="font-size:40px;display:block;margin-bottom:10px;opacity:.4"></i>
        <p style="font-size:14px;font-weight:600">Aucun type de cotisation trouvé</p>
      </div>
      @endforelse
    </div>
  </div>

  {{-- ══ VUE LISTE ════════════════════════════════════════ --}}
  <div id="tc-list-view-wrap" @if($vue !== 'liste') style="display:none" @endif>
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
            @forelse($typeCotisations as $tc)
            @php
              $totalCollecte = \App\Models\Cotisation::where('type_cotisation_id', $tc->id)->sum('montant_paye');
              [$tcColor, $tcBg, $tcIcon, $tcLabel] = match($tc->type) {
                  'mensuel'     => ['#405189', 'rgba(64,81,137,.12)',  'ri-calendar-check-line', 'Mensuel'],
                  'ordinaire'   => ['#0ab39c', 'rgba(10,179,156,.12)', 'ri-gift-line',           'Ordinaire'],
                  'jour_precis' => ['#d4a843', 'rgba(212,168,67,.12)', 'ri-hand-heart-line',     'Jour précis'],
                  'ramadan'     => ['#299cdb', 'rgba(41,156,219,.12)', 'ri-moon-line',            'Ramadan'],
                  default       => ['#878a99', 'rgba(135,138,153,.12)','ri-file-list-3-line',    $tc->type],
              };
              $periodeEnCours = $tc->isEnCours();
              $periodeExpiree = $tc->end_at && $tc->end_at->isPast() && !$periodeEnCours;
            @endphp
            <tr wire:click="openDetail({{ $tc->id }})" style="cursor:pointer">
              <td>
                <div style="display:flex;align-items:center;gap:10px">
                  <div class="type-icon-sm" style="background:{{ $tcBg }};color:{{ $tcColor }}">
                    <i class="{{ $tcIcon }}"></i>
                  </div>
                  <div class="tc-name-cell">
                    <div class="tcn-name">{{ $tc->libelle }}</div>
                    <div class="tcn-desc">{{ Str::limit($tc->description ?? '', 50) }}</div>
                  </div>
                </div>
              </td>
              <td><span class="tc-badge tb-type"><i class="{{ $tcIcon }} me-1"></i>{{ $tcLabel }}</span></td>
              <td>
                @if($tc->is_required)
                  <span class="tc-badge tb-requis"><i class="ri-lock-line me-1"></i>Oui</span>
                @else
                  <span style="color:var(--tc-muted);font-size:12px">Non</span>
                @endif
              </td>
              <td>
                @if($tc->montant_objectif)
                  <span style="font-weight:700;font-size:13px;color:var(--tc-accent)">{{ number_format($tc->montant_objectif, 0, ',', ' ') }}</span>
                  <span style="font-size:11px;color:var(--tc-muted)"> FCFA</span>
                @else
                  <span style="color:var(--tc-muted);font-size:12px">—</span>
                @endif
              </td>
              <td>
                <span style="font-weight:700;color:var(--tc-accent)">{{ number_format($totalCollecte, 0, ',', ' ') }} FCFA</span><br>
                <span style="font-size:11px;color:var(--tc-muted)">{{ $tc->cotisations_count }} contributions</span>
              </td>
              <td>
                @if($tc->status === 'actif')
                  <span class="tc-badge tb-actif"><i class="ri-checkbox-circle-line me-1"></i>Actif</span>
                @else
                  <span class="tc-badge tb-inactif"><i class="ri-close-circle-line me-1"></i>Inactif</span>
                @endif
                @if($periodeEnCours)
                  <span class="tc-badge tb-encours ms-1">En cours</span>
                @elseif($periodeExpiree)
                  <span class="tc-badge tb-expire ms-1">Expiré</span>
                @endif
              </td>
              <td wire:click.stop="">
                <div style="display:flex;gap:5px">
                  <button class="btn btn-soft-primary waves-effect" style="width:30px;height:30px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px" wire:click="openDetail({{ $tc->id }})"><i class="ri-eye-line"></i></button>
                  <button class="btn btn-soft-warning waves-effect" style="width:30px;height:30px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px" wire:click="openEdit({{ $tc->id }})"><i class="ri-edit-line"></i></button>
                  <button class="btn btn-soft-danger waves-effect" style="width:30px;height:30px;padding:0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px" wire:click="confirmDelete({{ $tc->id }})"><i class="ri-delete-bin-line"></i></button>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--tc-muted)">Aucun résultat</td></tr>
            @endforelse
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
<div class="modal fade" id="modalDetailTC" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      @if($detailTC)
      @php
        $dtc        = $detailTC;
        $dtcTotal   = \App\Models\Cotisation::where('type_cotisation_id', $dtc->id)->sum('montant_paye');
        $dtcFideles = \App\Models\Cotisation::where('type_cotisation_id', $dtc->id)->distinct('customer_id')->count('customer_id');
        $dtcPct     = ($dtc->montant_objectif > 0) ? min(round(($dtcTotal / $dtc->montant_objectif) * 100), 100) : null;
        [$dtcColor, $dtcBg, $dtcIcon, $dtcLabel, $dtcGrad] = match($dtc->type) {
            'mensuel'     => ['#405189', 'rgba(64,81,137,.12)',  'ri-calendar-check-line', 'Mensuel',     'linear-gradient(130deg,#2d3a63,#405189)'],
            'ordinaire'   => ['#0ab39c', 'rgba(10,179,156,.12)', 'ri-gift-line',           'Ordinaire',   'linear-gradient(130deg,#0a7a6a,#0ab39c)'],
            'jour_precis' => ['#d4a843', 'rgba(212,168,67,.12)', 'ri-hand-heart-line',     'Jour précis', 'linear-gradient(130deg,#a07c10,#d4a843)'],
            'ramadan'     => ['#299cdb', 'rgba(41,156,219,.12)', 'ri-moon-line',            'Ramadan',     'linear-gradient(130deg,#1a6080,#299cdb)'],
            default       => ['#878a99', 'rgba(135,138,153,.12)','ri-file-list-3-line',    $dtc->type,    'linear-gradient(130deg,#5a5d6e,#878a99)'],
        };
      @endphp

      <div class="modal-detail-tc-header" style="background:{{ $dtcGrad }}">
        <button class="mdt-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="mdt-inner">
          <div class="mdt-icon"><i class="{{ $dtcIcon }}"></i></div>
          <div class="mdt-info">
            <div class="mdt-name">{{ $dtc->libelle }}</div>
            <div class="mdt-meta">
              <span><i class="ri-tag-line"></i> {{ $dtcLabel }}</span>
              <span style="color:{{ $dtc->status === 'actif' ? '#6ef5e5' : 'rgba(255,255,255,.5)' }}">
                {{ $dtc->status === 'actif' ? '● Actif' : '○ Inactif' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="mdt-stat-row">
        <div class="mdt-stat-box">
          <div class="msb-val" style="font-size:13px">{{ number_format($dtcTotal, 0, ',', ' ') }} FCFA</div>
          <div class="msb-lbl">Total collecté</div>
        </div>
        <div class="mdt-stat-box">
          <div class="msb-val">{{ $dtc->cotisations_count }}</div>
          <div class="msb-lbl">Contributions</div>
        </div>
        <div class="mdt-stat-box">
          <div class="msb-val">{{ $dtcFideles }}</div>
          <div class="msb-lbl">Fidèles concernés</div>
        </div>
      </div>

      <div class="mdt-body">

        @if($dtcPct !== null)
        <div class="obj-progress-wrap">
          <div class="opw-header">
            <span class="opw-title"><i class="ri-target-line me-1"></i>Progression vers l'objectif</span>
            <span class="opw-pct" style="color:{{ $dtcColor }}">{{ $dtcPct }}%</span>
          </div>
          <div class="opw-bar">
            <div class="opw-fill" style="width:{{ $dtcPct }}%;background:{{ $dtcColor }}"></div>
          </div>
          <div class="opw-footer">
            <span>{{ number_format($dtcTotal, 0, ',', ' ') }} FCFA collectés</span>
            <span>/ {{ number_format($dtc->montant_objectif, 0, ',', ' ') }} FCFA</span>
          </div>
        </div>
        @endif

        <div style="font-size:11px;font-weight:800;color:var(--tc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px dashed var(--tc-border);display:flex;align-items:center;gap:8px;">
          <span style="display:inline-block;width:3px;height:14px;background:var(--tc-primary);border-radius:2px"></span>
          Configuration
        </div>
        <div class="config-grid">
          <div class="config-item">
            <div class="ci-label"><i class="ri-tag-line me-1"></i>Type</div>
            <div class="ci-value">{{ $dtcLabel }}</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-lock-line me-1"></i>Obligatoire</div>
            <div class="ci-value">
              @if($dtc->is_required)
                <span class="tc-badge tb-requis">Oui – obligatoire</span>
              @else
                <span style="color:var(--tc-muted);font-size:13px">Non</span>
              @endif
            </div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-target-line me-1"></i>Objectif global</div>
            <div class="ci-value">{{ $dtc->montant_objectif ? number_format($dtc->montant_objectif, 0, ',', ' ') . ' FCFA' : 'Pas défini' }}</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-calendar-line me-1"></i>Jour récurrence</div>
            <div class="ci-value" style="text-transform:capitalize">{{ $dtc->jour_recurrence ?? '—' }}</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-calendar-event-line me-1"></i>Début période</div>
            <div class="ci-value">{{ $dtc->start_at?->format('d/m/Y') ?? '—' }}</div>
          </div>
          <div class="config-item">
            <div class="ci-label"><i class="ri-calendar-close-line me-1"></i>Fin période</div>
            <div class="ci-value">{{ $dtc->end_at?->format('d/m/Y') ?? '—' }}</div>
          </div>
          <div class="config-item full">
            <div class="ci-label"><i class="ri-text me-1"></i>Description</div>
            <div class="ci-value" style="font-weight:500;font-size:13px;color:var(--tc-text)">{{ $dtc->description ?? '—' }}</div>
          </div>
        </div>

        <div style="font-size:11px;font-weight:800;color:var(--tc-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;margin-top:4px;padding-bottom:8px;border-bottom:1px dashed var(--tc-border);display:flex;align-items:center;gap:8px;">
          <span style="display:inline-block;width:3px;height:14px;background:var(--tc-accent);border-radius:2px"></span>
          Contributions récentes
        </div>
        <div class="contrib-list">
          @forelse($dtc->cotisations as $cot)
          <div class="contrib-item">
            <div class="ca-avatar" style="background:{{ $dtcColor }}">
              {{ strtoupper(substr($cot->customer?->prenom ?? '?', 0, 1) . substr($cot->customer?->nom ?? '?', 0, 1)) }}
            </div>
            <div class="ca-name">{{ $cot->customer?->prenom }} {{ $cot->customer?->nom }}</div>
            <div class="ca-amount">+{{ number_format($cot->montant_paye, 0, ',', ' ') }} FCFA</div>
            <div class="ca-date">{{ $cot->updated_at->format('d/m/Y') }}</div>
          </div>
          @empty
          <div style="text-align:center;padding:20px;color:var(--tc-muted);font-size:13px">
            <i class="ri-inbox-line" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4"></i>
            Aucune contribution récente
          </div>
          @endforelse
        </div>

        <div class="d-flex gap-2 mt-4 flex-wrap">
          <button class="btn btn-primary waves-effect" wire:click="openEdit({{ $dtc->id }})" data-bs-dismiss="modal">
            <i class="ri-edit-line me-1"></i> Modifier la configuration
          </button>
          <a href="#" class="btn btn-soft-success waves-effect">
            <i class="ri-list-check me-1"></i> Voir les cotisations liées
          </a>
        </div>

      </div>{{-- /mdt-body --}}
      @endif

    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL AJOUT / MODIFICATION TYPE COTISATION
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalTypeCotisation" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      <div class="modal-tc-header">
        <div class="mth-left">
          <div class="mth-icon"><i class="ri-settings-3-line"></i></div>
          <div>
            <p class="mth-title">{{ $editId ? 'Modifier le type' : 'Nouveau type de cotisation' }}</p>
            <p class="mth-sub">Définissez le type, les règles et la configuration.</p>
          </div>
        </div>
        <button class="modal-tc-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      <div style="overflow-y:auto;max-height:calc(90vh - 180px);">
        <div style="padding:24px 24px 0">

          @if($errors->any())
          <div style="background:rgba(240,101,72,.06);border:1px solid rgba(240,101,72,.25);border-left:3px solid #f06548;border-radius:0 10px 10px 0;padding:12px 14px;margin-bottom:16px;">
            <div style="font-size:13px;font-weight:700;color:#f06548;margin-bottom:4px;"><i class="ri-error-warning-line me-1"></i>Veuillez corriger les erreurs</div>
            @foreach($errors->all() as $err)
              <div style="font-size:12px;color:#c44a2e;">{{ $err }}</div>
            @endforeach
          </div>
          @endif

          {{-- Section 1 : Informations générales --}}
          <div class="form-section">
            <div class="form-section-title">Informations générales</div>
            <div class="mb-3">
              <label class="form-label-tc">Libellé <span class="req">*</span></label>
              <div class="input-tc-wrap">
                <i class="ri-text itw-icon"></i>
                <input type="text" class="input-tc {{ $errors->has('libelle') ? 'is-err' : '' }}"
                       wire:model="libelle"
                       placeholder="ex : Cotisation mensuelle, Quête du vendredi…">
              </div>
              @error('libelle') <div class="err-msg show">{{ $message }}</div> @enderror
            </div>
            <div>
              <label class="form-label-tc">Description</label>
              <textarea class="input-tc" wire:model="description" rows="2"
                        placeholder="Description optionnelle du type de cotisation…"></textarea>
            </div>
          </div>

          {{-- Section 2 : Type --}}
          <div class="form-section">
            <div class="form-section-title">Type de cotisation</div>
            @error('type') <div class="err-msg show mb-2">{{ $message }}</div> @enderror
            <div class="type-selector-grid">
              <button type="button" class="type-sel-btn {{ $type === 'mensuel' ? 'selected' : '' }}" wire:click="selectType('mensuel')">
                <div class="tsb-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-calendar-check-line"></i></div>
                <div class="tsb-info">
                  <div class="tsb-name">Mensuel</div>
                  <div class="tsb-desc">Engagement récurrent chaque mois</div>
                </div>
              </button>
              <button type="button" class="type-sel-btn {{ $type === 'ordinaire' ? 'selected' : '' }}" wire:click="selectType('ordinaire')">
                <div class="tsb-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-gift-line"></i></div>
                <div class="tsb-info">
                  <div class="tsb-name">Ordinaire</div>
                  <div class="tsb-desc">Don libre, aucune contrainte</div>
                </div>
              </button>
              <button type="button" class="type-sel-btn {{ $type === 'jour_precis' ? 'selected' : '' }}" wire:click="selectType('jour_precis')">
                <div class="tsb-icon" style="background:rgba(212,168,67,.12);color:#d4a843"><i class="ri-hand-heart-line"></i></div>
                <div class="tsb-info">
                  <div class="tsb-name">Jour précis</div>
                  <div class="tsb-desc">Collecte un jour fixe (quête)</div>
                </div>
              </button>
            </div>
          </div>

          {{-- Section 3 : Règles (visible seulement quand un type est sélectionné) --}}
          @if($type)
          <div class="form-section">
            <div class="form-section-title">Règles de cotisation</div>

            <div class="req-toggle-row mb-3 {{ $isRequired ? 'active' : '' }}">
              <div class="rtr-left">
                <div class="rtr-icon"><i class="ri-lock-line"></i></div>
                <div class="rtr-text">
                  <div class="rt-title">Cotisation obligatoire <span style="font-size:10px;background:rgba(247,184,75,.12);color:#f7b84b;padding:2px 7px;border-radius:10px;font-weight:700;margin-left:6px">is_required</span></div>
                  <div class="rt-sub">Permet de suivre les fidèles en retard et calculer les montants dus</div>
                </div>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" wire:model="isRequired" {{ $type === 'mensuel' ? 'disabled' : '' }}>
                <span class="toggle-slider"></span>
              </label>
            </div>

            {{-- Jour de collecte --}}
            <div class="cond-field {{ $type === 'jour_precis' ? 'show' : '' }}">
              <label class="form-label-tc"><i class="ri-calendar-line me-1"></i>Jour de collecte</label>
              <select class="input-tc" wire:model="jourRecurrence" style="cursor:pointer">
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

            {{-- Période --}}
            <div class="cond-field {{ in_array($type, ['ramadan', 'ordinaire', 'jour_precis']) ? 'show' : '' }}">
              <div class="row g-2 mb-0">
                <div class="col-6">
                  <label class="form-label-tc"><i class="ri-calendar-event-line me-1"></i>Date de début</label>
                  <input type="date" class="input-tc" wire:model="startAt">
                </div>
                <div class="col-6">
                  <label class="form-label-tc"><i class="ri-calendar-close-line me-1"></i>Date de fin</label>
                  <input type="date" class="input-tc" wire:model="endAt">
                </div>
              </div>
              @error('endAt') <div class="err-msg show mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Objectif global --}}
            <div class="cond-field {{ in_array($type, ['ramadan', 'ordinaire', 'jour_precis']) ? 'show' : '' }}">
              <label class="form-label-tc">
                <i class="ri-target-line me-1"></i>Objectif global de collecte
                <span style="font-size:10px;color:var(--tc-muted);font-weight:500;text-transform:none;letter-spacing:0">(optionnel)</span>
              </label>
              <div class="input-tc-wrap">
                <i class="ri-money-cny-circle-line itw-icon"></i>
                <input type="number" class="input-tc with-suffix" wire:model="montantObjectif" placeholder="ex : 500000" min="0">
                <span class="itw-suffix">FCFA</span>
              </div>
              @error('montantObjectif') <div class="err-msg show">{{ $message }}</div> @enderror
            </div>

          </div>
          @endif

          <div class="mb-4 p-3" style="background:rgba(64,81,137,.06);border-radius:10px;border-left:3px solid #405189;">
            <p style="font-size:12px;color:var(--tc-text);margin:0;">
              <i class="ri-information-line me-1" style="color:#405189"></i>
              <strong>Rappel :</strong> Le type <em>mensuel</em> + <em>is_required = oui</em> active le suivi automatique des retards.
              Les autres types (ordinaire, jour précis, Ramadan) peuvent être <em>non requis</em> — chacun cotise librement.
            </p>
          </div>

        </div>{{-- /padding --}}
      </div>

      <div class="modal-tc-footer">
        <button class="btn-tc-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        <button class="btn-tc-primary" wire:click="save" wire:loading.attr="disabled">
          <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-save-line" wire:loading.remove wire:target="save"></i>
          <span wire:loading.remove wire:target="save"> Enregistrer</span>
          <span wire:loading wire:target="save">Enregistrement…</span>
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
<script>
Livewire.on('OpenModalModilEdit', ({ name_modal }) => {
    const el = document.getElementById(name_modal);
    if (el) bootstrap.Modal.getOrCreateInstance(el).show();
});
Livewire.on('closeModalModilEdit', ({ name_modal }) => {
    const el = document.getElementById(name_modal);
    if (el) bootstrap.Modal.getOrCreateInstance(el).hide();
});
</script>
@endpush


@push('styles')
<style>
    .feedback-text{
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #f06548;
    }
</style>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush
