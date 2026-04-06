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
      <button class="btn-co-primary" wire:click="openCreate()">
        <i class="ri-money-cny-circle-line"></i> Enregistrer paiement BO
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
        <div class="cki-val">{{ $kpis['total'] }}</div>
        <div class="cki-sub">Toutes cotisations</div>
      </div>
    </div>
    <div class="co-kpi ck-ajour fu fu-2">
      <div class="cki" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="cki-label">À jour</div>
        <div class="cki-val">{{ $kpis['ajour'] }}</div>
        <div class="cki-sub">Soldées</div>
      </div>
    </div>
    <div class="co-kpi ck-partiel fu fu-3">
      <div class="cki" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-error-warning-line"></i></div>
      <div>
        <div class="cki-label">Partielles</div>
        <div class="cki-val">{{ $kpis['partiel'] }}</div>
        <div class="cki-sub">Paiement partiel</div>
      </div>
    </div>
    <div class="co-kpi ck-retard fu fu-4">
      <div class="cki" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-time-line"></i></div>
      <div>
        <div class="cki-label">En retard</div>
        <div class="cki-val">{{ $kpis['retard'] }}</div>
        <div class="cki-sub">Non payées</div>
      </div>
    </div>
    <div class="co-kpi ck-montant fu fu-5">
      <div class="cki" style="background:rgba(212,168,67,.12);color:#d4a843"><i class="ri-money-cny-circle-line"></i></div>
      <div>
        <div class="cki-label">Total collecté</div>
        <div class="cki-val" style="font-size:14px">{{ number_format($kpis['montant'], 0, ',', ' ') }} FCFA</div>
        <div class="cki-sub">Somme montant_paye</div>
      </div>
    </div>
  </div>

  {{-- ══ TABS STATUT ══════════════════════════════════════ --}}
  <div class="co-status-tabs fu fu-2">
    <span class="cst-label"><i class="ri-filter-3-line me-1"></i>Statut :</span>
    <button class="co-tab tab-tous {{ $tabStatut === 'tous' ? 'active' : '' }}" wire:click="$set('tabStatut','tous')">
      <i class="ri-list-check"></i>Tous <span class="tab-count">{{ $tabCounts['tous'] }}</span>
    </button>
    <button class="co-tab tab-ajour {{ $tabStatut === 'a_jour' ? 'active' : '' }}" wire:click="$set('tabStatut','a_jour')">
      <i class="ri-checkbox-circle-line"></i>À jour <span class="tab-count">{{ $tabCounts['a_jour'] }}</span>
    </button>
    <button class="co-tab tab-partiel {{ $tabStatut === 'partiel' ? 'active' : '' }}" wire:click="$set('tabStatut','partiel')">
      <i class="ri-error-warning-line"></i>Partiel <span class="tab-count">{{ $tabCounts['partiel'] }}</span>
    </button>
    <button class="co-tab tab-retard {{ $tabStatut === 'en_retard' ? 'active' : '' }}" wire:click="$set('tabStatut','en_retard')">
      <i class="ri-time-line"></i>En retard <span class="tab-count">{{ $tabCounts['en_retard'] }}</span>
    </button>
  </div>

  {{-- ══ TOOLBAR ══════════════════════════════════════════ --}}
  <div class="co-toolbar fu fu-3">
    <div class="sw">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher fidèle, type, période…">
    </div>
    <select class="co-sel" wire:model.live="filterType">
      <option value="tous">Tous types</option>
      @foreach($typesCotisation as $tcOption)
        <option value="{{ $tcOption->id }}">{{ $tcOption->libelle }}</option>
      @endforeach
    </select>
    <select class="co-sel" wire:model.live="filterMois" style="min-width:110px">
      <option value="tous">Tous mois</option>
      @foreach(range(1,12) as $m)
        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
      @endforeach
    </select>
    <select class="co-sel" wire:model.live="filterMode" style="min-width:140px">
      <option value="tous">Tous modes</option>
      <option value="mobile_money">Mobile Money</option>
      <option value="espece">Espèces</option>
      <option value="virement">Virement</option>
      <option value="nd">Non renseigné</option>
    </select>
  </div>

  {{-- ══ TABLE ════════════════════════════════════════════ --}}
  <div class="co-table-card fu fu-4" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="co-table">
        <thead>
          <tr>
            <th>Fidèle</th>
            <th>Type de cotisation</th>
            <th>Période</th>
            <th>Montant dû</th>
            <th>Payé</th>
            <th>Restant</th>
            <th>Statut</th>
            <th>Mode</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cotisations as $cot)
          @php
            $ac = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc'];
            $avatarColor = $ac[($cot->customer_id - 1) % count($ac)];
            $initiales   = $cot->customer ? strtoupper(substr($cot->customer->prenom,0,1).substr($cot->customer->nom,0,1)) : '??';

            $typeMeta = match($cot->typeCotisation?->type) {
                'mensuel'     => ['#405189','rgba(64,81,137,.10)','ri-calendar-check-line'],
                'ordinaire'   => ['#0ab39c','rgba(10,179,156,.10)','ri-gift-line'],
                'jour_precis' => ['#d4a843','rgba(212,168,67,.12)','ri-hand-heart-line'],
                'ramadan'     => ['#299cdb','rgba(41,156,219,.12)','ri-moon-line'],
                default       => ['#878a99','rgba(135,138,153,.10)','ri-file-list-3-line'],
            };

            $pct = 0;
            if ($cot->montant_du > 0) $pct = min(round(($cot->montant_paye / $cot->montant_du) * 100), 100);
            elseif ($cot->montant_paye > 0) $pct = 100;
            $barColor = $pct === 100 ? 'var(--co-accent)' : ($pct > 0 ? 'var(--co-warning)' : 'var(--co-danger)');

            $rowCls = match($cot->statut) {
                'a_jour'    => 'row-ajour',
                'partiel'   => 'row-partiel',
                'en_retard' => 'row-retard',
                default     => '',
            };
            $modeMap = [
                'mobile_money' => ['mb-mm','ri-smartphone-line','Mobile Money'],
                'espece'       => ['mb-esp','ri-money-dollar-circle-line','Espèces'],
                'virement'     => ['mb-vir','ri-bank-line','Virement'],
            ];
            $modeCls = $cot->mode_paiement ? ($modeMap[$cot->mode_paiement] ?? ['mb-nd','ri-question-line','—']) : ['mb-nd','ri-question-line','—'];
          @endphp

          <tr class="{{ $rowCls }}" wire:click="openDetail({{ $cot->id }})">

            {{-- Fidèle --}}
            <td>
              <div class="td-fidele">
                <div class="td-avatar" style="background:{{ $avatarColor }}">{{ $initiales }}</div>
                <div>
                  <div class="td-fidele-name">{{ $cot->customer?->prenom }} {{ $cot->customer?->nom }}</div>
                  <div class="td-fidele-phone">{{ $cot->customer?->dial_code }} {{ $cot->customer?->phone }}</div>
                </div>
              </div>
            </td>

            {{-- Type --}}
            <td>
              <div style="display:flex;align-items:center;gap:6px">
                <span style="width:26px;height:26px;border-radius:7px;background:{{ $typeMeta[1] }};color:{{ $typeMeta[0] }};display:inline-flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0">
                  <i class="{{ $typeMeta[2] }}"></i>
                </span>
                <span style="font-size:12px;font-weight:600;color:var(--co-text)">{{ $cot->typeCotisation?->libelle ?? '—' }}</span>
              </div>
            </td>

            {{-- Période --}}
            <td>
              @if($cot->mois && $cot->annee)
                <span class="td-period">{{ \Carbon\Carbon::create()->month($cot->mois)->translatedFormat('F') }} {{ $cot->annee }}</span>
              @else
                <span class="td-period tp-nonmensuel">Ponctuel</span>
              @endif
            </td>

            {{-- Montant dû --}}
            <td>
              <div class="td-montants">
                <div class="tm-du">{{ $cot->montant_du !== null ? number_format($cot->montant_du, 0, ',', ' ') . ' FCFA' : '—' }}</div>
                @if($cot->montant_du !== null)
                <div class="tm-bar"><div class="tm-bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div></div>
                @endif
              </div>
            </td>

            {{-- Payé --}}
            <td><span class="tm-paye">{{ number_format($cot->montant_paye, 0, ',', ' ') }} FCFA</span></td>

            {{-- Restant --}}
            <td>
              @if($cot->montant_restant > 0)
                <span class="tm-restant">{{ number_format($cot->montant_restant, 0, ',', ' ') }} FCFA</span>
              @else
                <span style="color:var(--co-muted);font-size:11px">—</span>
              @endif
            </td>

            {{-- Statut --}}
            <td>
              @if($cot->statut === 'a_jour')
                <span class="co-pill cp-ajour"><i class="ri-checkbox-circle-line"></i>À jour</span>
              @elseif($cot->statut === 'partiel')
                <span class="co-pill cp-partiel"><i class="ri-error-warning-line"></i>Partiel</span>
              @else
                <span class="co-pill cp-retard"><i class="ri-time-line"></i>En retard</span>
              @endif
            </td>

            {{-- Mode --}}
            <td>
              <span class="mode-badge {{ $modeCls[0] }}"><i class="{{ $modeCls[1] }}"></i>{{ $modeCls[2] }}</span>
            </td>

            {{-- Actions --}}
            <td wire:click.stop="">
              <div class="td-actions">

                {{-- Voir détail --}}
                <button class="btn btn-soft-primary waves-effect" wire:click="openDetail({{ $cot->id }})" title="Détails">
                  <i class="ri-eye-line"></i>
                </button>

                {{-- Modifier si non validée --}}
                @if(! $cot->validated_at)
                <button class="btn btn-soft-info waves-effect" wire:click="openEdit({{ $cot->id }})" title="Modifier">
                  <i class="ri-edit-line"></i>
                </button>
                @endif

                {{-- Valider espèces --}}
                @if(! $cot->validated_at && $cot->mode_paiement === 'espece')
                <button class="btn btn-soft-success waves-effect" wire:click="confirmerValidation({{ $cot->id }})" title="Valider">
                  <i class="ri-checkbox-circle-line"></i>
                </button>
                @endif

                {{-- Enregistrer paiement si pas à jour --}}
                @if($cot->statut !== 'a_jour')
                <button class="btn btn-soft-warning waves-effect" wire:click="openCreate({{ $cot->customer_id }})" title="Enregistrer paiement">
                  <i class="ri-money-cny-circle-line"></i>
                </button>
                @endif

                {{-- Dropdown statut + supprimer --}}
                <div class="dropdown">
                  <button class="btn btn-soft-secondary waves-effect dropdown-toggle"
                          style="width:28px;height:28px;padding:0;display:flex;align-items:center;justify-content:center;font-size:14px;border-radius:7px"
                          data-bs-toggle="dropdown">
                    <i class="ri-more-2-fill"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" style="font-size:12px;min-width:170px">
                    <li><span class="dropdown-header" style="font-size:10px;font-weight:800;text-transform:uppercase">Changer statut</span></li>
                    @if($cot->statut !== 'a_jour')
                    <li><a class="dropdown-item" href="#" wire:click.prevent="changerStatut({{ $cot->id }},'a_jour')">
                      <i class="ri-checkbox-circle-line me-2" style="color:#0ab39c"></i>Marquer À jour
                    </a></li>
                    @endif
                    @if($cot->statut !== 'partiel')
                    <li><a class="dropdown-item" href="#" wire:click.prevent="changerStatut({{ $cot->id }},'partiel')">
                      <i class="ri-error-warning-line me-2" style="color:#f7b84b"></i>Marquer Partiel
                    </a></li>
                    @endif
                    @if($cot->statut !== 'en_retard')
                    <li><a class="dropdown-item" href="#" wire:click.prevent="changerStatut({{ $cot->id }},'en_retard')">
                      <i class="ri-time-line me-2" style="color:#f06548"></i>Marquer En retard
                    </a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" wire:click.prevent="confirmDelete({{ $cot->id }})">
                      <i class="ri-delete-bin-line me-2"></i>Supprimer
                    </a></li>
                  </ul>
                </div>

              </div>
            </td>
          </tr>

          @empty
          <tr>
            <td colspan="9">
              <div class="co-empty">
                <i class="ri-file-search-line"></i>
                <p>Aucune cotisation trouvée</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="co-pagination">
      <span class="co-pag-info">
        {{ $cotisations->firstItem() ?? 0 }}–{{ $cotisations->lastItem() ?? 0 }} sur {{ $cotisations->total() }} cotisation(s)
      </span>
      <div>{{ $cotisations->links('livewire::bootstrap') }}</div>
    </div>
  </div>

  {{-- Note métier --}}
  <div class="fu fu-5 mt-3">
    <div style="background:rgba(64,81,137,.05);border:1px dashed rgba(64,81,137,.2);border-radius:10px;padding:12px 16px;">
      <p style="font-size:11px;color:var(--co-muted);margin:0">
        <i class="ri-information-line me-1" style="color:#405189"></i>
        <strong>Logique de report :</strong> Pour une cotisation mensuelle obligatoire, le montant saisi solde d'abord les mois en retard (du plus ancien au plus récent), puis le surplus crée automatiquement les cotisations des mois suivants.
        Une cotisation <strong>non encore validée</strong> peut être modifiée via le bouton <i class="ri-edit-line"></i>.
      </p>
    </div>
  </div>

</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL COTISATION
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailCotisation" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog modal-lg" style="max-width:680px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      @if($detailCotisation)
      @php
        $dc   = $detailCotisation;
        $tc   = $dc->typeCotisation;
        $cust = $dc->customer;
        $typeMeta = match($tc?->type) {
            'mensuel'     => ['linear-gradient(130deg,#2d3a63,#405189)', 'ri-calendar-check-line'],
            'ordinaire'   => ['linear-gradient(130deg,#0a7a6a,#0ab39c)', 'ri-gift-line'],
            'jour_precis' => ['linear-gradient(130deg,#a07c10,#d4a843)', 'ri-hand-heart-line'],
            'ramadan'     => ['linear-gradient(130deg,#1a6080,#299cdb)', 'ri-moon-line'],
            default       => ['linear-gradient(130deg,#5a5d6e,#878a99)', 'ri-file-list-3-line'],
        };
        $pct = 0;
        if ($dc->montant_du > 0) $pct = min(round(($dc->montant_paye / $dc->montant_du) * 100), 100);
        elseif ($dc->montant_paye > 0) $pct = 100;
        $barColor  = $pct === 100 ? '#0ab39c' : ($pct > 0 ? '#f7b84b' : '#f06548');
        $periode   = ($dc->mois && $dc->annee) ? \Carbon\Carbon::create()->month($dc->mois)->translatedFormat('F') . ' ' . $dc->annee : 'Ponctuel';
        $opMeta    = [
            'paiement'   => ['hi-paiement',   '#0ab39c','rgba(10,179,156,.12)','ri-money-dollar-circle-line'],
            'creation'   => ['hi-creation',   '#405189','rgba(64,81,137,.10)', 'ri-add-circle-line'],
            'ajustement' => ['hi-ajustement', '#f7b84b','rgba(247,184,75,.12)','ri-tools-line'],
            'validation' => ['hi-paiement',   '#0ab39c','rgba(10,179,156,.12)','ri-shield-check-line'],
            'echec'      => ['hi-echec',      '#f06548','rgba(240,101,72,.10)','ri-close-circle-line'],
        ];
      @endphp

      {{-- Header --}}
      <div class="co-modal-header" style="background:{{ $typeMeta[0] }}">
        <button class="cmh-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="cmh-inner">
          <div class="cmh-icon"><i class="{{ $typeMeta[1] }}"></i></div>
          <div class="cmh-info">
            <div class="cmh-name">{{ $cust?->prenom }} {{ $cust?->nom }}</div>
            <div class="cmh-meta">
              <span><i class="ri-tag-line"></i>{{ $tc?->libelle ?? '—' }}</span>
              <span><i class="ri-calendar-line"></i>{{ $periode }}</span>
              <span>
                @if($dc->statut === 'a_jour')
                  <span style="background:rgba(255,255,255,.2);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px">✓ À jour</span>
                @elseif($dc->statut === 'partiel')
                  <span style="background:rgba(255,255,255,.2);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px">◑ Partiel</span>
                @else
                  <span style="background:rgba(255,255,255,.2);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px">⚠ En retard</span>
                @endif
                @if(! $dc->validated_at)
                  <span style="background:rgba(247,184,75,.3);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;margin-left:4px">
                    <i class="ri-time-line"></i> Non validée
                  </span>
                @endif
              </span>
            </div>
          </div>
        </div>
      </div>

      {{-- Stats overlap --}}
      <div class="co-modal-stats">
        <div class="co-ms-box">
          <div class="msb-v" style="font-size:13px">{{ $dc->montant_du !== null ? number_format($dc->montant_du,0,',',' ').' FCFA' : '—' }}</div>
          <div class="msb-l">Montant dû</div>
        </div>
        <div class="co-ms-box">
          <div class="msb-v" style="font-size:13px;color:#0ab39c">{{ number_format($dc->montant_paye,0,',',' ') }} FCFA</div>
          <div class="msb-l">Montant payé</div>
        </div>
        <div class="co-ms-box">
          <div class="msb-v" style="font-size:13px;color:#f06548">{{ $dc->montant_restant > 0 ? number_format($dc->montant_restant,0,',',' ').' FCFA' : '—' }}</div>
          <div class="msb-l">Restant</div>
        </div>
        <div class="co-ms-box">
          <div class="msb-v">{{ $pct }}%</div>
          <div class="msb-l">Progression</div>
        </div>
      </div>

      <div style="overflow-y:auto;max-height:calc(90vh - 220px);">
        <div class="co-modal-body">

          {{-- Barre progression --}}
          <div class="co-pay-progress">
            <div class="cpp-header">
              <span class="cpp-title"><i class="ri-bar-chart-line me-1"></i>Progression du paiement</span>
              <span class="cpp-pct" style="color:{{ $barColor }}">{{ $pct }}%</span>
            </div>
            <div class="cpp-track">
              <div class="cpp-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
            </div>
            <div class="cpp-footer">
              <span class="paid">{{ number_format($dc->montant_paye,0,',',' ') }} FCFA payés</span>
              @if($dc->montant_restant > 0)
              <span class="due">{{ number_format($dc->montant_restant,0,',',' ') }} FCFA restants</span>
              @endif
            </div>
          </div>

          {{-- Détails --}}
          <div style="font-size:11px;font-weight:800;color:var(--co-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;padding-bottom:7px;border-bottom:1px dashed var(--co-border);display:flex;align-items:center;gap:8px;">
            <span style="display:inline-block;width:3px;height:13px;background:var(--co-primary);border-radius:2px"></span>
            Détails de la cotisation
          </div>
          <div class="co-detail-grid">
            <div class="co-detail-item">
              <div class="di-l"><i class="ri-user-line me-1"></i>Fidèle</div>
              <div class="di-v">{{ $cust?->prenom }} {{ $cust?->nom }}</div>
            </div>
            <div class="co-detail-item">
              <div class="di-l"><i class="ri-money-cny-circle-line me-1"></i>Engagement mensuel</div>
              <div class="di-v" style="color:#405189">
                {{ $cust?->montant_engagement ? number_format($cust->montant_engagement,0,',',' ').' FCFA/mois' : 'Aucun' }}
              </div>
            </div>
            <div class="co-detail-item">
              <div class="di-l"><i class="ri-tag-line me-1"></i>Type</div>
              <div class="di-v">{{ $tc?->libelle ?? '—' }}</div>
            </div>
            <div class="co-detail-item">
              <div class="di-l"><i class="ri-calendar-line me-1"></i>Période</div>
              <div class="di-v">{{ $periode }}</div>
            </div>
            <div class="co-detail-item">
              <div class="di-l"><i class="ri-smartphone-line me-1"></i>Mode paiement</div>
              <div class="di-v">
                @if($dc->mode_paiement === 'mobile_money') <span class="mode-badge mb-mm"><i class="ri-smartphone-line"></i>Mobile Money</span>
                @elseif($dc->mode_paiement === 'espece')   <span class="mode-badge mb-esp"><i class="ri-money-dollar-circle-line"></i>Espèces</span>
                @elseif($dc->mode_paiement === 'virement') <span class="mode-badge mb-vir"><i class="ri-bank-line"></i>Virement</span>
                @else <span class="mode-badge mb-nd"><i class="ri-question-line"></i>—</span>
                @endif
              </div>
            </div>
            <div class="co-detail-item">
              <div class="di-l"><i class="ri-calendar-event-line me-1"></i>Date création</div>
              <div class="di-v">{{ $dc->created_at->format('d M Y') }}</div>
            </div>
            <div class="co-detail-item full">
              <div class="di-l"><i class="ri-shield-check-line me-1"></i>Validation</div>
              <div class="di-v">
                @if($dc->validated_at && $dc->validated_by)
                  <span class="val-badge validated"><i class="ri-shield-check-line"></i>Admin · {{ $dc->validated_at->format('d M Y H:i') }}</span>
                @elseif($dc->validated_at)
                  <span class="val-badge auto"><i class="ri-robot-line"></i>Auto · {{ $dc->validated_at->format('d M Y') }}</span>
                @else
                  <span class="val-badge pending"><i class="ri-time-line"></i>Non validée — peut encore être modifiée</span>
                @endif
              </div>
            </div>
          </div>

          {{-- Historique --}}
          <div style="font-size:11px;font-weight:800;color:var(--co-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;padding-bottom:7px;border-bottom:1px dashed var(--co-border);display:flex;align-items:center;gap:8px;">
            <span style="display:inline-block;width:3px;height:13px;background:var(--co-accent);border-radius:2px"></span>
            Historique
          </div>
          <div class="co-hist-list">
            @forelse($dc->historiques->sortByDesc('created_at') as $hist)
            @php $om = $opMeta[$hist->type_operation] ?? $opMeta['creation']; @endphp
            <div class="co-hist-item {{ $om[0] }}">
              <div class="hi-icon" style="background:{{ $om[2] }};color:{{ $om[1] }}"><i class="{{ $om[3] }}"></i></div>
              <div class="hi-body">
                <div class="hi-op">{{ $hist->note ?? ucfirst($hist->type_operation) }}</div>
                <div class="hi-meta">{{ $hist->created_at->format('d M Y H:i') }}</div>
              </div>
              <div class="hi-amt" style="color:{{ $om[1] }}">+{{ number_format($hist->montant,0,',',' ') }} FCFA</div>
            </div>
            @empty
            <div style="text-align:center;padding:20px;color:var(--co-muted);font-size:13px">
              <i class="ri-inbox-line" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4"></i>Aucun historique
            </div>
            @endforelse
          </div>

          {{-- Actions --}}
          <div class="d-flex gap-2 mt-4 flex-wrap">
            @if(! $dc->validated_at)
            <button class="btn btn-soft-info waves-effect"
                    wire:click="openEdit({{ $dc->id }})"
                    data-bs-dismiss="modal">
              <i class="ri-edit-line me-1"></i>Modifier
            </button>
            @endif
            @if(! $dc->validated_at && $dc->mode_paiement === 'espece')
            <button class="btn btn-success waves-effect"
                    wire:click="confirmerValidation({{ $dc->id }})"
                    data-bs-dismiss="modal">
              <i class="ri-shield-check-line me-1"></i>Valider ce paiement
            </button>
            @endif
            @if($dc->statut !== 'a_jour')
            <button class="btn btn-primary waves-effect"
                    wire:click="openCreate({{ $dc->customer_id }})"
                    data-bs-dismiss="modal">
              <i class="ri-money-cny-circle-line me-1"></i>Enregistrer paiement
            </button>
            @endif
          </div>

        </div>
      </div>

      @endif
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL CRÉER / MODIFIER COTISATION BO
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalCreateCotisation" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:600px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      <div class="co-create-header">
        <div class="cch-inner">
          <div class="cch-icon">
            <i class="{{ $editId ? 'ri-edit-line' : 'ri-money-cny-circle-line' }}"></i>
          </div>
          <div>
            <h5>{{ $editId ? 'Modifier la cotisation' : 'Enregistrer un paiement' }}</h5>
            <p class="cch-sub">{{ $editId ? 'Modification avant validation admin' : 'Saisie manuelle BO – Espèces ou autre mode' }}</p>
          </div>
        </div>
        <button class="co-create-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      <div style="overflow-y:auto;max-height:calc(90vh - 142px);">
        <div style="padding:20px 22px 0">

          {{-- Erreurs Livewire --}}
          @if($errors->any())
          <div style="background:rgba(240,101,72,.06);border:1px solid rgba(240,101,72,.25);border-left:3px solid #f06548;border-radius:0 10px 10px 0;padding:10px 14px;margin-bottom:14px;">
            <div style="font-size:12px;font-weight:700;color:#f06548;margin-bottom:3px;"><i class="ri-error-warning-line me-1"></i>Veuillez corriger les erreurs</div>
            @foreach($errors->all() as $err)
              <div style="font-size:11px;color:#c44a2e;">{{ $err }}</div>
            @endforeach
          </div>
          @endif

          {{-- ── Section 1 : Fidèle ─────────────────────── --}}
          <div class="co-section-title">Fidèle</div>

          {{-- Recherche fidèle --}}
          @if(! $fideleCourant)
          <div class="mb-3">
            <label class="form-label-co">Sélectionner le fidèle <span class="req">*</span></label>
            <div class="input-wrap">
              <i class="ri-user-search-line iw-icon"></i>
              <input type="text"
                     class="input-co {{ $errors->has('customerId') ? 'is-err' : '' }}"
                     wire:model.live.debounce.300ms="searchFidele"
                     placeholder="Taper nom, prénom ou téléphone…">
            </div>
            @error('customerId') <div class="err-co show">{{ $message }}</div> @enderror

            {{-- Suggestions --}}
            @if($searchFidele && $fidelesSuggeres->count())
            <div style="border:1.5px solid var(--co-border);border-radius:9px;background:#fff;box-shadow:var(--co-shadow-md);max-height:220px;overflow-y:auto;margin-top:4px;">
              @foreach($fidelesSuggeres as $f)
              @php
                $acColors = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc'];
                $fAc  = $acColors[($f->id - 1) % count($acColors)];
                $fIni = strtoupper(substr($f->prenom,0,1).substr($f->nom,0,1));
              @endphp
              <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--co-border);transition:background .15s"
                   onmouseover="this.style.background='rgba(64,81,137,.04)'"
                   onmouseout="this.style.background=''"
                   wire:click="selectFidele({{ $f->id }})">
                <div style="width:34px;height:34px;border-radius:50%;background:{{ $fAc }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">{{ $fIni }}</div>
                <div>
                  <div style="font-size:13px;font-weight:700;color:#212529">{{ $f->prenom }} {{ $f->nom }}</div>
                  <div style="font-size:11px;color:var(--co-muted)">{{ $f->dial_code }} {{ $f->phone }}</div>
                </div>
                @if($f->montant_engagement)
                <div style="margin-left:auto;font-size:11px;font-weight:700;color:var(--co-primary)">
                  {{ number_format($f->montant_engagement,0,',',' ') }} FCFA/mois
                </div>
                @endif
              </div>
              @endforeach
            </div>
            @elseif($searchFidele && $fidelesSuggeres->isEmpty())
            <div style="font-size:12px;color:var(--co-muted);padding:8px 0">Aucun fidèle trouvé.</div>
            @endif
          </div>
          @endif

          {{-- Card fidèle sélectionné --}}
          @if($fideleCourant)
          @php
            $fcAc  = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc'];
            $fcCol = $fcAc[($fideleCourant->id - 1) % count($fcAc)];
            $fcIni = strtoupper(substr($fideleCourant->prenom,0,1).substr($fideleCourant->nom,0,1));
          @endphp
          <div class="fidele-selected-card mb-3">
            <div class="fsc-avatar" style="background:{{ $fcCol }}">{{ $fcIni }}</div>
            <div>
              <div class="fsc-name">{{ $fideleCourant->prenom }} {{ $fideleCourant->nom }}</div>
              <div class="fsc-detail">{{ $fideleCourant->dial_code }} {{ $fideleCourant->phone }}</div>
            </div>
            @if($fideleCourant->montant_engagement)
            <div class="fsc-eng">
              <div class="fe-val">{{ number_format($fideleCourant->montant_engagement,0,',',' ') }}</div>
              <div class="fe-lbl">FCFA/mois</div>
            </div>
            @else
            <div class="fsc-eng"><div style="font-size:11px;color:var(--co-muted)">Sans engagement</div></div>
            @endif
            @if(! $editId)
            <button type="button" style="margin-left:auto;border:none;background:none;color:var(--co-muted);cursor:pointer;font-size:16px"
                    wire:click="$set('customerId',null)" title="Changer">
              <i class="ri-close-circle-line"></i>
            </button>
            @endif
          </div>

          {{-- Alerte engagement manquant --}}
          @if($alerteEngagement)
          <div style="background:rgba(240,101,72,.06);border:1.5px solid rgba(240,101,72,.3);border-radius:10px;padding:12px 14px;margin-bottom:14px;">
            <div style="font-size:13px;font-weight:700;color:#f06548;margin-bottom:4px;"><i class="ri-alert-line me-1"></i>Engagement mensuel requis</div>
            <p style="font-size:12px;color:var(--co-text);margin:0">
              Ce fidèle n'a pas de montant d'engagement mensuel défini. Modifiez d'abord sa fiche avant de créer une cotisation mensuelle obligatoire.
            </p>
          </div>
          @endif
          @endif

          {{-- ── Section 2 : Type ────────────────────────── --}}
          <div class="co-section-title accent">Type de cotisation</div>

          <div class="mb-3">
            <label class="form-label-co">Type <span class="req">*</span></label>
            <div class="input-wrap">
              <i class="ri-tag-line iw-icon"></i>
              <select class="input-co {{ $errors->has('typeCotisationId') ? 'is-err' : '' }}"
                      wire:model.live="typeCotisationId"
                      style="padding-left:38px;cursor:pointer">
                <option value="">— Choisir un type —</option>
                @foreach($typesCotisation as $tc)
                  <option value="{{ $tc->id }}">
                    {{ $tc->libelle }}
                    @if($tc->type === 'mensuel') · Mensuel @endif
                    @if($tc->is_required) · Obligatoire @endif
                  </option>
                @endforeach
              </select>
            </div>
            @error('typeCotisationId') <div class="err-co show">{{ $message }}</div> @enderror
          </div>

          {{-- Période mois/année si mensuel --}}
          @php
            $tcSelectionne = $typeCotisationId ? $typesCotisation->firstWhere('id', $typeCotisationId) : null;
            $isMensuelForm = $tcSelectionne?->type === 'mensuel';
          @endphp
          @if($isMensuelForm)
          <div class="mb-3">
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label-co"><i class="ri-calendar-line me-1"></i>Mois</label>
                <select class="input-co" wire:model="mois" style="cursor:pointer">
                  @foreach(range(1,12) as $m)
                  <option value="{{ $m }}" @selected($m == ($mois ?? now()->month))>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="col-6">
                <label class="form-label-co"><i class="ri-calendar-2-line me-1"></i>Année</label>
                <select class="input-co" wire:model="annee" style="cursor:pointer">
                  @foreach([2023,2024,2025,2026] as $y)
                  <option value="{{ $y }}" @selected($y == ($annee ?? now()->year))>{{ $y }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @if($fideleCourant && ! $fideleCourant->montant_engagement && $tcSelectionne?->is_required)
            <div class="err-co show mt-2"><i class="ri-alert-line me-1"></i>Ce fidèle n'a pas d'engagement mensuel défini.</div>
            @endif
          </div>
          @endif

          {{-- ── Section 3 : Montant & mode ─────────────── --}}
          <div class="co-section-title gold">Montant &amp; mode</div>

          <div class="mb-3">
            <label class="form-label-co">Montant payé <span class="req">*</span></label>
            <div class="input-wrap">
              <i class="ri-money-cny-circle-line iw-icon"></i>
              <input type="number"
                     class="input-co has-sfx {{ $errors->has('montantPaye') ? 'is-err' : '' }}"
                     wire:model.live="montantPaye"
                     placeholder="ex : 10000" min="1">
              <span class="iw-suffix">FCFA</span>
            </div>
            @error('montantPaye') <div class="err-co show">{{ $message }}</div> @enderror
          </div>

          {{-- Preview report automatique (calcul PHP côté Livewire) --}}
          @if(count($previewReport) > 0)
          <div class="report-calc mb-3">
            <div class="rc-title"><i class="ri-calculator-line"></i>Répartition automatique du paiement</div>
            <div class="rc-rows">
              @foreach($previewReport as $row)
              <div class="rc-row">
                <span class="rr-mois">
                  <i class="{{ $row['type'] === 'solde' ? 'ri-history-line' : 'ri-calendar-line' }} me-1"
                     style="color:{{ $row['type'] === 'solde' ? 'var(--co-warning)' : 'var(--co-primary)' }}"></i>
                  {{ $row['label'] }}
                  @if($row['type'] === 'solde')
                    <span style="font-size:9px;font-weight:700;background:rgba(247,184,75,.15);color:#d4870a;padding:1px 5px;border-radius:4px;margin-left:4px">Arrieré</span>
                  @else
                    <span style="font-size:9px;font-weight:700;background:rgba(64,81,137,.1);color:#405189;padding:1px 5px;border-radius:4px;margin-left:4px">Nouveau</span>
                  @endif
                </span>
                <span style="display:flex;align-items:center;gap:6px">
                  <span class="rr-amount">{{ number_format($row['montant'],0,',',' ') }} FCFA</span>
                  <span class="rr-status {{ $row['statut'] === 'a_jour' ? 's-solde' : 's-partiel' }}">
                    {{ $row['statut'] === 'a_jour' ? 'Soldé' : 'Partiel' }}
                  </span>
                </span>
              </div>
              @endforeach
            </div>
          </div>
          @endif

          {{-- Mode paiement --}}
          <div class="mb-3">
            <label class="form-label-co">Mode de paiement <span class="req">*</span></label>
            @error('modePaiement') <div class="err-co show mb-2">{{ $message }}</div> @enderror
            <div class="mode-grid">
              <button type="button" class="mode-btn {{ $modePaiement === 'espece' ? 'selected' : '' }}" wire:click="selectMode('espece')">
                <i class="ri-money-dollar-circle-line" style="color:#f7b84b"></i><span>Espèces</span>
              </button>
              <button type="button" class="mode-btn {{ $modePaiement === 'mobile_money' ? 'selected' : '' }}" wire:click="selectMode('mobile_money')">
                <i class="ri-smartphone-line" style="color:#0ab39c"></i><span>Mobile Money</span>
              </button>
              <button type="button" class="mode-btn {{ $modePaiement === 'virement' ? 'selected' : '' }}" wire:click="selectMode('virement')">
                <i class="ri-bank-line" style="color:#405189"></i><span>Virement</span>
              </button>
            </div>
          </div>

          {{-- Référence --}}
          @if(in_array($modePaiement, ['mobile_money','virement']))
          <div class="mb-3">
            <label class="form-label-co"><i class="ri-hashtag me-1"></i>Référence transaction</label>
            <div class="input-wrap">
              <i class="ri-hashtag iw-icon"></i>
              <input type="text" class="input-co" wire:model="reference" placeholder="ex : OM202504XXXX">
            </div>
          </div>
          @endif

          {{-- ── Section 4 : Validation ──────────────────── --}}
          <div class="co-section-title warning">Validation (validated_by / validated_at)</div>

          <div class="validation-row mb-4">
            <div class="vr-left">
              <div class="vr-icon"><i class="ri-shield-check-line"></i></div>
              <div>
                <div class="vr-title">Valider immédiatement</div>
                <div class="vr-sub">
                  Renseigne <code>validated_by</code> = votre ID et <code>validated_at</code> = maintenant.<br>
                  Si désactivé : paiement en attente — <strong>modifiable</strong>.
                </div>
              </div>
            </div>
            <label class="toggle-sw">
              <input type="checkbox" wire:model="validerImmediatement">
              <span class="toggle-sl"></span>
            </label>
          </div>

        </div>
      </div>

      <div class="co-modal-footer">
        <button class="btn-co-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        <button class="btn-co-primary" wire:click="save" wire:loading.attr="disabled">
          <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-save-line" wire:loading.remove wire:target="save"></i>
          <span wire:loading.remove wire:target="save">
            {{ $editId ? 'Enregistrer les modifications' : 'Enregistrer la cotisation' }}
          </span>
          <span wire:loading wire:target="save">Traitement…</span>
        </button>
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/cotisation.css') }}" rel="stylesheet" type="text/css" />
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
Livewire.on('swal:modalDeleteOptionsWithButton', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    Swal.fire({
        title: data.title, text: data.text, icon: data.type,
        showCancelButton: true,
        confirmButtonText: data.succesButton ?? 'Confirmer',
        cancelButtonText:  data.cancelButton ?? 'Annuler',
        confirmButtonColor: '#f06548', cancelButtonColor: '#878a99',
    }).then(result => {
        if (result.isConfirmed) Livewire.dispatch(data.eventRetour, { id: data.id });
    });
});
Livewire.on('modalShowmessageToast', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
        .fire({ icon: data.type, title: data.title });
});
Livewire.on('swal:modalGetInfo_message_not_timer', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    Swal.fire({ title: data.title, text: data.text, icon: data.type });
});
Livewire.on('swal:modalGetInfo_message', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    Swal.fire({ title: data.title, text: data.text, icon: data.type, timer: 3000 });
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
