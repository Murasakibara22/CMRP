<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ PAGE HEADER ══════════════════════════════════════ --}}
  <div class="dcc-header fade-up">
    <div>
      <h4>Demandes de changement de cotisation</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Demandes changement</li>
        </ol>
      </nav>
    </div>
    @if($kpis['attente'] > 0)
    <span class="dcc-alert-badge">
      <i class="ri-alarm-warning-line"></i> {{ $kpis['attente'] }} en attente de traitement
    </span>
    @endif
  </div>

  {{-- ══ KPI STRIP ══════════════════════════════════════════ --}}
  <div class="dcc-kpi-strip">
    <div class="dcc-kpi fade-up fu-1">
      <div class="dcc-kpi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-swap-line"></i></div>
      <div>
        <div class="dcc-kpi-label">Total</div>
        <div class="dcc-kpi-val">{{ $kpis['total'] }}</div>
        <div class="dcc-kpi-sub">Toutes demandes</div>
      </div>
    </div>
    <div class="dcc-kpi fade-up fu-2">
      <div class="dcc-kpi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
      <div>
        <div class="dcc-kpi-label">En attente</div>
        <div class="dcc-kpi-val">{{ $kpis['attente'] }}</div>
        <div class="dcc-kpi-sub">À traiter</div>
      </div>
    </div>
    <div class="dcc-kpi fade-up fu-3">
      <div class="dcc-kpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="dcc-kpi-label">Validées</div>
        <div class="dcc-kpi-val">{{ $kpis['validees'] }}</div>
        <div class="dcc-kpi-sub">Appliquées</div>
      </div>
    </div>
    <div class="dcc-kpi fade-up fu-4">
      <div class="dcc-kpi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
      <div>
        <div class="dcc-kpi-label">Rejetées</div>
        <div class="dcc-kpi-val">{{ $kpis['rejetees'] }}</div>
        <div class="dcc-kpi-sub">Refusées</div>
      </div>
    </div>
    <div class="dcc-kpi fade-up fu-5">
      <div class="dcc-kpi-icon" style="background:rgba(41,156,219,.12);color:#299cdb"><i class="ri-arrow-left-right-line"></i></div>
      <div>
        <div class="dcc-kpi-label">Changements</div>
        <div class="dcc-kpi-val">{{ $kpis['changements'] }}</div>
        <div class="dcc-kpi-sub">Demandes de migration</div>
      </div>
    </div>
    <div class="dcc-kpi fade-up fu-6">
      <div class="dcc-kpi-icon" style="background:rgba(240,101,72,.08);color:#f06548"><i class="ri-stop-circle-line"></i></div>
      <div>
        <div class="dcc-kpi-label">Arrêts</div>
        <div class="dcc-kpi-val">{{ $kpis['arrets'] }}</div>
        <div class="dcc-kpi-sub">Demandes d'arrêt</div>
      </div>
    </div>
  </div>

  {{-- ══ TABS ════════════════════════════════════════════════ --}}
  <div class="dcc-tabs fade-up fu-2">
    <span class="dcc-tab-label"><i class="ri-filter-3-line me-1"></i>Statut :</span>
    <button class="dcc-tab {{ $filterStatut === 'tous' ? 'active' : '' }}" wire:click="$set('filterStatut','tous')">
      Tous <span class="dcc-tab-count">{{ $tabCounts['tous'] }}</span>
    </button>
    <button class="dcc-tab dcc-tab-pending {{ $filterStatut === 'en_attente' ? 'active' : '' }}" wire:click="$set('filterStatut','en_attente')">
      <i class="ri-time-line"></i>En attente <span class="dcc-tab-count">{{ $tabCounts['en_attente'] }}</span>
    </button>
    <button class="dcc-tab dcc-tab-success {{ $filterStatut === 'validee' ? 'active' : '' }}" wire:click="$set('filterStatut','validee')">
      <i class="ri-checkbox-circle-line"></i>Validées <span class="dcc-tab-count">{{ $tabCounts['validee'] }}</span>
    </button>
    <button class="dcc-tab dcc-tab-danger {{ $filterStatut === 'rejetee' ? 'active' : '' }}" wire:click="$set('filterStatut','rejetee')">
      <i class="ri-close-circle-line"></i>Rejetées <span class="dcc-tab-count">{{ $tabCounts['rejetee'] }}</span>
    </button>
  </div>

  {{-- ══ TOOLBAR ═════════════════════════════════════════════ --}}
  <div class="dcc-toolbar fade-up fu-3">
    <div class="dcc-search">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher un fidèle…">
    </div>
    <select class="dcc-select" wire:model.live="filterType">
      <option value="tous">Tous les types</option>
      <option value="changement">Changement de type</option>
      <option value="arret">Arrêt de cotisation</option>
    </select>
  </div>

  {{-- ══ TABLE ═══════════════════════════════════════════════ --}}
  <div class="dcc-table-card fade-up fu-4" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="dcc-table">
        <thead>
          <tr>
            <th>Fidèle</th>
            <th>Type de demande</th>
            <th>Ancien type</th>
            <th>Nouveau type</th>
            <th>Suppr. retards</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($demandes as $dr)
          @php
            $ac  = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843'];
            $col = $ac[($dr->customer_id - 1) % count($ac)];
            $ini = $dr->customer ? strtoupper(substr($dr->customer->prenom,0,1).substr($dr->customer->nom,0,1)) : '??';

            [$pillCls, $pillIcon, $pillLabel] = match($dr->statut) {
                'en_attente' => ['dcc-pill-pending', 'ri-time-line',            'En attente'],
                'validee'    => ['dcc-pill-success', 'ri-checkbox-circle-line', 'Validée'],
                'rejetee'    => ['dcc-pill-danger',  'ri-close-circle-line',    'Rejetée'],
                default      => ['dcc-pill-pending', 'ri-question-line',        $dr->statut],
            };

            $typePill = $dr->type_demande === 'changement'
                ? ['dcc-type-change', 'ri-arrow-left-right-line', 'Changement']
                : ['dcc-type-arret',  'ri-stop-circle-line',      'Arrêt'];
          @endphp

          <tr class="{{ $dr->statut === 'en_attente' ? 'dcc-row-pending' : ($dr->statut === 'validee' ? 'dcc-row-success' : 'dcc-row-danger') }}"
              wire:click="openDetail({{ $dr->id }})" style="cursor:pointer">

            <td>
              <div class="dcc-fidele-cell">
                <div class="dcc-avatar" style="background:{{ $col }}">{{ $ini }}</div>
                <div>
                  <div class="dcc-fidele-name">{{ $dr->customer?->prenom }} {{ $dr->customer?->nom }}</div>
                  <div class="dcc-fidele-phone">{{ $dr->customer?->dial_code }} {{ $dr->customer?->phone }}</div>
                </div>
              </div>
            </td>

            <td>
              <span class="dcc-type-badge {{ $typePill[0] }}">
                <i class="{{ $typePill[1] }}"></i>{{ $typePill[2] }}
              </span>
            </td>

            <td>
              <span class="dcc-type-label">{{ $dr->ancienType?->libelle ?? '—' }}</span>
              @if($dr->ancien_montant_engagement)
              <div class="dcc-type-sub">{{ number_format($dr->ancien_montant_engagement, 0, ',', ' ') }} FCFA/mois</div>
              @endif
            </td>

            <td>
              @if($dr->isChangement())
              <span class="dcc-type-label" style="color:#0ab39c">{{ $dr->nouveauType?->libelle ?? '—' }}</span>
              @if($dr->nouveau_montant_engagement)
              <div class="dcc-type-sub">{{ number_format($dr->nouveau_montant_engagement, 0, ',', ' ') }} FCFA/mois</div>
              @endif
              @else
              <span style="font-size:12px;color:#f06548;font-weight:600">Aucun (arrêt)</span>
              @endif
            </td>

            <td class="text-center">
              @if($dr->supprimer_cotisations_retard)
              <span class="dcc-pill-danger" style="font-size:10px;padding:3px 8px"><i class="ri-delete-bin-line"></i> Oui</span>
              @else
              <span style="font-size:11px;color:#878a99">Non</span>
              @endif
            </td>

            <td>
              <div style="font-size:12px;color:var(--dcc-text)">{{ $dr->created_at->format('d M Y') }}</div>
              <div style="font-size:10px;color:var(--dcc-muted)">{{ $dr->created_at->format('H:i') }}</div>
            </td>

            <td>
              <span class="dcc-pill {{ $pillCls }}">
                <i class="{{ $pillIcon }}"></i>{{ $pillLabel }}
              </span>
            </td>

            <td wire:click.stop="">
              <div class="dcc-actions">
                <button class="btn btn-soft-primary waves-effect btn-sm" wire:click="openDetail({{ $dr->id }})" title="Détails">
                  <i class="ri-eye-line"></i>
                </button>
                @if($dr->statut === 'en_attente')
                <button class="btn btn-soft-success waves-effect btn-sm" wire:click="confirmerValidation({{ $dr->id }})" title="Valider">
                  <i class="ri-checkbox-circle-line"></i>
                </button>
                <button class="btn btn-soft-danger waves-effect btn-sm" wire:click="ouvrirRejet({{ $dr->id }})" title="Rejeter">
                  <i class="ri-close-circle-line"></i>
                </button>
                @endif
              </div>
            </td>

          </tr>
          @empty
          <tr>
            <td colspan="8">
              <div class="dcc-empty">
                <i class="ri-swap-line"></i>
                <p>Aucune demande de changement</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="dcc-pagination">
      <span class="dcc-pag-info">
        {{ $demandes->firstItem() ?? 0 }}–{{ $demandes->lastItem() ?? 0 }} sur {{ $demandes->total() }} demande(s)
      </span>
      <div>{{ $demandes->links('livewire::bootstrap') }}</div>
    </div>
  </div>

</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL / ÉDITION
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailDemande" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:660px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">

      @if($detailDemande)
      @php
        $dd = $detailDemande;
        $headerGrad = match($dd->statut) {
            'validee' => 'linear-gradient(130deg,#089383,#0ab39c)',
            'rejetee' => 'linear-gradient(130deg,#c0341a,#f06548)',
            default   => $dd->isChangement()
                ? 'linear-gradient(130deg,#1a5fa8,#405189)'
                : 'linear-gradient(130deg,#8a2020,#c0341a)',
        };
        [$pillCls, $pillIcon, $pillLabel] = match($dd->statut) {
            'en_attente' => ['dcc-pill-pending', 'ri-time-line',            'En attente'],
            'validee'    => ['dcc-pill-success', 'ri-checkbox-circle-line', 'Validée'],
            'rejetee'    => ['dcc-pill-danger',  'ri-close-circle-line',    'Rejetée'],
            default      => ['dcc-pill-pending', 'ri-question-line',        $dd->statut],
        };
      @endphp

      {{-- Header --}}
      <div class="dcc-modal-header" style="background:{{ $headerGrad }}">
        <button class="dcc-modal-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="dcc-modal-header-inner">
          <div class="dcc-modal-icon">
            <i class="{{ $dd->isChangement() ? 'ri-swap-line' : 'ri-stop-circle-line' }}"></i>
          </div>
          <div>
            <div class="dcc-modal-name">{{ $dd->customer?->prenom }} {{ $dd->customer?->nom }}</div>
            <div class="dcc-modal-meta">
              <span><i class="ri-calendar-line"></i>{{ $dd->created_at->format('d M Y H:i') }}</span>
              <span><i class="ri-smartphone-line"></i>{{ $dd->customer?->dial_code }} {{ $dd->customer?->phone }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Stats overlap --}}
      <div class="dcc-modal-stats">
        <div class="dcc-ms-box">
          <div class="dcc-ms-v">{{ $dd->labelTypeDemande }}</div>
          <div class="dcc-ms-l">Type de demande</div>
        </div>
        <div class="dcc-ms-box">
          <div class="dcc-ms-v">
            <span class="dcc-pill {{ $pillCls }}" style="font-size:11px">
              <i class="{{ $pillIcon }}"></i>{{ $pillLabel }}
            </span>
          </div>
          <div class="dcc-ms-l">Statut</div>
        </div>
        <div class="dcc-ms-box">
          <div class="dcc-ms-v">{{ $nbRetardAncienType }}</div>
          <div class="dcc-ms-l">Mois en retard</div>
        </div>
      </div>

      <div style="overflow-y:auto;max-height:calc(90vh - 260px)">
        <div class="dcc-modal-body">

          @if(! $modeEdit)
          {{-- ── MODE LECTURE ── --}}

          <div class="dcc-section-title">Détails de la demande</div>
          <div class="dcc-detail-grid">
            <div class="dcc-detail-item">
              <div class="dcc-di-l"><i class="ri-calendar-check-line me-1"></i>Type actuel</div>
              <div class="dcc-di-v">{{ $dd->ancienType?->libelle ?? '—' }}
                @if($dd->ancien_montant_engagement)
                <span style="font-size:11px;color:#878a99;margin-left:4px">· {{ number_format($dd->ancien_montant_engagement, 0, ',', ' ') }} FCFA/mois</span>
                @endif
              </div>
            </div>
            <div class="dcc-detail-item">
              <div class="dcc-di-l"><i class="ri-arrow-right-line me-1"></i>Nouveau type</div>
              <div class="dcc-di-v" style="{{ $dd->isChangement() ? 'color:#0ab39c' : 'color:#f06548' }}">
                @if($dd->isChangement())
                  {{ $dd->nouveauType?->libelle ?? '—' }}
                  @if($dd->nouveau_montant_engagement)
                  <span style="font-size:11px;color:#878a99;margin-left:4px">· {{ number_format($dd->nouveau_montant_engagement, 0, ',', ' ') }} FCFA/mois</span>
                  @endif
                @else
                  <span>Arrêt — aucun type mensuel</span>
                @endif
              </div>
            </div>
            <div class="dcc-detail-item">
              <div class="dcc-di-l"><i class="ri-delete-bin-line me-1"></i>Supprimer retards</div>
              <div class="dcc-di-v">
                @if($dd->supprimer_cotisations_retard)
                  <span class="dcc-pill-danger" style="font-size:11px;padding:3px 8px"><i class="ri-check-line me-1"></i>Oui — {{ $nbRetardAncienType }} cotisations</span>
                @else
                  <span style="color:#878a99">Non</span>
                @endif
              </div>
            </div>
            @if($dd->motif)
            <div class="dcc-detail-item full">
              <div class="dcc-di-l"><i class="ri-chat-1-line me-1"></i>Motif</div>
              <div class="dcc-di-v">{{ $dd->motif }}</div>
            </div>
            @endif
            @if($dd->validatedBy)
            <div class="dcc-detail-item">
              <div class="dcc-di-l"><i class="ri-user-settings-line me-1"></i>Traité par</div>
              <div class="dcc-di-v">{{ $dd->validatedBy->name }}</div>
            </div>
            <div class="dcc-detail-item">
              <div class="dcc-di-l"><i class="ri-time-line me-1"></i>Traité le</div>
              <div class="dcc-di-v">{{ $dd->validated_at?->format('d M Y H:i') }}</div>
            </div>
            @endif
            @if($dd->motif_rejet)
            <div class="dcc-detail-item full">
              <div class="dcc-di-l" style="color:#f06548"><i class="ri-close-circle-line me-1"></i>Motif du rejet</div>
              <div class="dcc-di-v" style="color:#f06548">{{ $dd->motif_rejet }}</div>
            </div>
            @endif
          </div>

          @if($dd->statut === 'validee')
          <div style="background:rgba(10,179,156,.06);border:1px solid rgba(10,179,156,.2);border-radius:10px;padding:12px 14px;margin-top:14px">
            <div style="font-size:12px;color:#089383;font-weight:700;margin-bottom:3px"><i class="ri-checkbox-circle-line me-1"></i>Demande appliquée</div>
            <div style="font-size:11px;color:#495057;line-height:1.6">
              Le profil du fidèle a été mis à jour.
              @if($dd->isChangement())
                Son type de cotisation mensuelle est maintenant « {{ $dd->nouveauType?->libelle }} ».
              @else
                Il n'a plus de cotisation mensuelle obligatoire.
              @endif
              @if($dd->supprimer_cotisations_retard) Les cotisations en retard de l'ancien type ont été supprimées.@endif
            </div>
          </div>
          @endif

          {{-- Actions lecture --}}
          @if($dd->statut === 'en_attente')
          <div class="d-flex gap-2 mt-4 flex-wrap">
            <button class="btn btn-success waves-effect" wire:click="confirmerValidation({{ $dd->id }})" data-bs-dismiss="modal">
              <i class="ri-checkbox-circle-line me-1"></i>Valider
            </button>
            <button class="btn btn-soft-warning waves-effect" wire:click="activerEdition">
              <i class="ri-edit-line me-1"></i>Modifier
            </button>
            <button class="btn btn-soft-danger waves-effect" wire:click="ouvrirRejet({{ $dd->id }})">
              <i class="ri-close-circle-line me-1"></i>Rejeter
            </button>
          </div>
          @endif

          @else
          {{-- ── MODE ÉDITION ── --}}
          <div class="dcc-section-title">
            <i class="ri-edit-line me-1"></i>Modifier la demande
          </div>

          {{-- Type demande --}}
          <div style="margin-bottom:16px">
            <div style="font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">Type de demande</div>
            <div style="display:flex;gap:8px">
              <div wire:click="$set('editTypeDemande','changement')"
                   style="flex:1;display:flex;align-items:center;gap:8px;border:1.5px solid {{ $editTypeDemande === 'changement' ? '#405189' : '#e9ebec' }};background:{{ $editTypeDemande === 'changement' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:10px;padding:10px;cursor:pointer;transition:all .2s">
                <i class="ri-arrow-left-right-line" style="color:{{ $editTypeDemande === 'changement' ? '#405189' : '#878a99' }}"></i>
                <span style="font-size:12px;font-weight:700;color:{{ $editTypeDemande === 'changement' ? '#405189' : '#212529' }}">Changement</span>
              </div>
              <div wire:click="$set('editTypeDemande','arret')"
                   style="flex:1;display:flex;align-items:center;gap:8px;border:1.5px solid {{ $editTypeDemande === 'arret' ? '#f06548' : '#e9ebec' }};background:{{ $editTypeDemande === 'arret' ? 'rgba(240,101,72,.04)' : '#fff' }};border-radius:10px;padding:10px;cursor:pointer;transition:all .2s">
                <i class="ri-stop-circle-line" style="color:{{ $editTypeDemande === 'arret' ? '#f06548' : '#878a99' }}"></i>
                <span style="font-size:12px;font-weight:700;color:{{ $editTypeDemande === 'arret' ? '#f06548' : '#212529' }}">Arrêt</span>
              </div>
            </div>
          </div>

          {{-- Nouveau type (si changement) --}}
          @if($editTypeDemande === 'changement')
          <div style="margin-bottom:16px">
            <div style="font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
              Nouveau type <span style="color:#f06548">*</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px">
              @foreach($typesMensuels as $tm)
              @php $sel = $editNouveauTypeId === $tm->id; @endphp
              <div wire:click="selectEditNouveauType({{ $tm->id }})"
                   style="display:flex;align-items:center;justify-content:space-between;border:1.5px solid {{ $sel ? '#405189' : '#e9ebec' }};background:{{ $sel ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:10px;padding:10px 14px;cursor:pointer;transition:all .2s">
                <div>
                  <div style="font-size:12px;font-weight:700;color:{{ $sel ? '#405189' : '#212529' }}">{{ $tm->libelle }}</div>
                  @if($tm->montant_minimum)<div style="font-size:11px;color:#878a99">Min. {{ number_format($tm->montant_minimum, 0, ',', ' ') }} FCFA/mois</div>@endif
                </div>
                <div style="width:18px;height:18px;border-radius:50%;border:2px solid {{ $sel ? '#405189' : '#e9ebec' }};background:{{ $sel ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  @if($sel)<i class="ri-check-line" style="color:#fff;font-size:10px"></i>@endif
                </div>
              </div>
              @endforeach
            </div>
            @if($errorEditNouveauType)
            <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorEditNouveauType }}</div>
            @endif

            {{-- Nouveau montant --}}
            @if($editNouveauTypeId)
            <div style="margin-top:12px">
              <div style="font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
                Nouveau montant d'engagement <span style="color:#f06548">*</span>
              </div>
              @if($coutEngagements->count())
              <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px">
                @foreach($coutEngagements as $ce)
                <div wire:click="selectEditNouvelEngagement({{ $ce->montant }})"
                     style="padding:6px 10px;border-radius:16px;cursor:pointer;border:1.5px solid {{ $editNouvelEngagement === $ce->montant ? '#405189' : '#e9ebec' }};background:{{ $editNouvelEngagement === $ce->montant ? 'rgba(64,81,137,.08)' : '#fff' }};color:{{ $editNouvelEngagement === $ce->montant ? '#405189' : '#495057' }};font-size:11px;font-weight:700;transition:all .15s">
                  {{ number_format($ce->montant, 0, ',', ' ') }}
                </div>
                @endforeach
              </div>
              @endif
              <div style="position:relative">
                <i class="ri-money-cny-circle-line" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#878a99;font-size:13px;pointer-events:none"></i>
                <input type="number" wire:model.live="editNouvelEngagement" min="1" placeholder="Montant libre…"
                       style="width:100%;border:1.5px solid {{ $errorEditNouvelEngagement ? '#f06548' : '#e9ebec' }};border-radius:9px;height:40px;padding:0 12px 0 32px;font-size:13px;background:#fff;color:#212529"/>
              </div>
              @if($errorEditNouvelEngagement)
              <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">{{ $errorEditNouvelEngagement }}</div>
              @endif
            </div>
            @endif
          </div>
          @endif

          {{-- Supprimer retards --}}
          <div style="margin-bottom:14px">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;font-weight:600;color:#495057">
              <input type="checkbox" wire:model="editSupprimerRetard" style="width:16px;height:16px;accent-color:#f06548"/>
              Supprimer les cotisations en retard de l'ancien type
              @if($nbRetardAncienType > 0)
              <span style="font-size:11px;color:#f06548;font-weight:700">({{ $nbRetardAncienType }} mois)</span>
              @endif
            </label>
          </div>

          {{-- Motif --}}
          <div style="margin-bottom:16px">
            <label style="display:block;font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Motif</label>
            <textarea wire:model="editMotif" rows="2"
                      style="width:100%;border:1.5px solid #e9ebec;border-radius:9px;padding:10px 12px;font-size:13px;resize:none;font-family:inherit;color:#212529"
                      placeholder="Motif de la demande…"></textarea>
          </div>

          {{-- Actions édition --}}
          <div class="d-flex gap-2">
            <button class="btn btn-light" wire:click="annulerEdition" style="border-radius:9px;font-weight:700">
              <i class="ri-close-line me-1"></i>Annuler
            </button>
            <button class="btn btn-primary waves-effect" wire:click="sauvegarderEdition" wire:loading.attr="disabled">
              <span wire:loading wire:target="sauvegarderEdition" class="spinner-border spinner-border-sm me-1"></span>
              <i class="ri-save-line" wire:loading.remove wire:target="sauvegarderEdition"></i>
              <span wire:loading.remove wire:target="sauvegarderEdition"> Sauvegarder</span>
              <span wire:loading wire:target="sauvegarderEdition"> Traitement…</span>
            </button>
          </div>

          @endif{{-- /modeEdit --}}

        </div>
      </div>

      <div class="dcc-modal-footer">
        <button class="btn btn-light" style="border-radius:9px;font-weight:700" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Fermer
        </button>
      </div>

      @endif
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL REJET
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalRejet" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:480px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">

      <div style="background:linear-gradient(135deg,#c0341a,#f06548);padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff">
            <i class="ri-close-circle-line"></i>
          </div>
          <div>
            <div style="font-size:15px;font-weight:800;color:#fff">Rejeter la demande</div>
            <div style="font-size:11px;color:rgba(255,255,255,.75)">Motif obligatoire</div>
          </div>
        </div>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal" wire:click="fermerRejet"></button>
      </div>

      <div style="padding:20px 24px">
        <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
          Motif du rejet <span style="color:#f06548">*</span>
        </div>
        <textarea wire:model="rejetMotif" rows="3"
                  placeholder="Expliquez la raison du rejet au fidèle…"
                  style="width:100%;border:1.5px solid {{ $errorRejet ? '#f06548' : '#e9ebec' }};border-radius:10px;padding:10px 12px;font-size:13px;resize:none;font-family:inherit;color:#212529;margin-bottom:6px"></textarea>
        @if($errorRejet)
        <div style="font-size:12px;color:#f06548;font-weight:600;margin-bottom:10px"><i class="ri-error-warning-line me-1"></i>{{ $errorRejet }}</div>
        @endif

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:14px">
          <button class="btn btn-light" style="border-radius:9px;font-weight:700" data-bs-dismiss="modal" wire:click="fermerRejet">
            Annuler
          </button>
          <button wire:click="confirmerRejet" wire:loading.attr="disabled"
                  style="background:linear-gradient(135deg,#c0341a,#f06548);border:none;border-radius:9px;color:#fff;font-size:13px;font-weight:700;padding:10px 20px;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
            <span wire:loading wire:target="confirmerRejet" class="spinner-border spinner-border-sm"></span>
            <i class="ri-close-circle-line" wire:loading.remove wire:target="confirmerRejet"></i>
            <span wire:loading.remove wire:target="confirmerRejet">Confirmer le rejet</span>
            <span wire:loading wire:target="confirmerRejet">Traitement…</span>
          </button>
        </div>
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/demande-change.css') }}" rel="stylesheet" type="text/css" />
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
        confirmButtonColor: '#0ab39c', cancelButtonColor: '#878a99',
    }).then(r => { if (r.isConfirmed) Livewire.dispatch(data.eventRetour, { id: data.id }); });
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
</script>
@endpush

@push('styles')
<style>
    .feedback-text {
        width: 100%;
        margin-top: .25rem;
        font-size: .875em;
        color: #f06548;
    }
</style>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>

@endpush