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
    <button class="btn-msq-primary" wire:click="openAdd">
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
        <div class="ckpi-value">{{ $kpis['total'] }}</div>
        <div class="ckpi-sub">Inscrits dans la base</div>
      </div>
    </div>
    <div class="ckpi ck-ajour fade-up fu-2">
      <div class="ckpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
        <i class="ri-checkbox-circle-line"></i>
      </div>
      <div>
        <div class="ckpi-label">À jour</div>
        <div class="ckpi-value">{{ $kpis['ajour'] }}</div>
        <div class="ckpi-sub">Cotisation mensuelle OK</div>
      </div>
    </div>
    <div class="ckpi ck-retard fade-up fu-3">
      <div class="ckpi-icon" style="background:rgba(240,101,72,.10);color:#f06548">
        <i class="ri-time-line"></i>
      </div>
      <div>
        <div class="ckpi-label">En retard</div>
        <div class="ckpi-value">{{ $kpis['enRetard'] }}</div>
        <div class="ckpi-sub">ou paiement partiel</div>
      </div>
    </div>
    <div class="ckpi ck-libre fade-up fu-4">
      <div class="ckpi-icon" style="background:rgba(135,138,153,.10);color:#878a99">
        <i class="ri-user-line"></i>
      </div>
      <div>
        <div class="ckpi-label">Sans engagement</div>
        <div class="ckpi-value">{{ $kpis['sansEngagement'] }}</div>
        <div class="ckpi-sub">Pas de mensuel souscrit</div>
      </div>
    </div>
  </div>

  {{-- ══ TOOLBAR ══════════════════════════════════════════ --}}
  <div class="cust-toolbar fade-up fu-3">

    <div class="search-wrap">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher un fidèle…">
    </div>

    <select class="filter-select" wire:model.live="filterStatut">
      <option value="tous">Tous les statuts</option>
      <option value="ajour">À jour</option>
      <option value="retard">En retard</option>
      <option value="partiel">Partiel</option>
      <option value="libre">Sans engagement</option>
    </select>

    <select class="filter-select" wire:model.live="filterMois" style="min-width:130px">
      @foreach(range(1, 12) as $m)
        <option value="{{ $m }}">
          {{ Carbon\Carbon::create()->month($m)->locale('fr')->isoFormat('MMMM') }}
          {{ now()->year }}
        </option>
      @endforeach
    </select>

    <div class="view-toggle">
      <button class="vt-btn {{ $vue === 'table' ? 'active' : '' }}"
              wire:click="setVue('table')" title="Vue tableau">
        <i class="ri-list-check"></i>
      </button>
      <button class="vt-btn {{ $vue === 'grille' ? 'active' : '' }}"
              wire:click="setVue('grille')" title="Vue grille">
        <i class="ri-layout-grid-line"></i>
      </button>
    </div>

    <button class="btn btn-soft-success btn-sm waves-effect" title="Exporter Excel">
      <i class="ri-file-excel-2-line me-1"></i>Export
    </button>
  </div>

  {{-- ══ LOADING INDICATOR ════════════════════════════════ --}}
  <div wire:loading.flex class="cust-loading">
    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
    <span>Chargement…</span>
  </div>

  {{-- ══ VUE TABLEAU ══════════════════════════════════════ --}}
  @if($vue === 'table')
  <div class="fade-up fu-4" wire:loading.class="opacity-50">
    <div class="cust-table-card">
      <div class="table-responsive">
        <table class="cust-table">
          <thead>
            <tr>
              <th>Fidèle</th>
              <th>Téléphone</th>
              <th>Engagement mensuel</th>
              <th>Date adhésion</th>
              <th>Statut cotisation</th>
              <th>Type de cotisation mensuel</th>
              <th>Montant dû</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($customers as $customer)
            @php
              $cotisationMois = $customer->cotisations
                  ->where('annee', now()->year)
                  ->where('mois', now()->month)
                  ->first();
              $statut         = $cotisationMois?->statut ?? ($customer->montant_engagement ? 'en_retard' : null);
              $montantDu      = $cotisationMois?->montant_restant ?? 0;
              $initiales      = strtoupper(substr($customer->prenom, 0, 1) . substr($customer->nom, 0, 1));
              $avatarColors   = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc','#ea4c4c','#2dce89'];
              $avatarColor    = $avatarColors[($customer->id - 1) % count($avatarColors)];
            @endphp
            <tr wire:click="openDetail({{ $customer->id }})" title="Voir le profil">
              <td>
                <div style="display:flex;align-items:center;gap:10px;">
                  <div class="cust-avatar" style="background:{{ $avatarColor }}">{{ $initiales }}</div>
                  <div>
                    <div class="cust-name">{{ $customer->prenom }} {{ $customer->nom }}</div>
                    <div class="cust-phone"><i class="ri-phone-line me-1"></i>{{ $customer->dial_code }} {{ $customer->telephone }}</div>
                  </div>
                </div>
              </td>
              <td><span style="font-size:12px;color:var(--msq-muted);">{{ $customer->adresse ?? '—' }}</span></td>
              <td>
                @if($customer->montant_engagement)
                  <span class="cust-engagement">{{ number_format($customer->montant_engagement, 0, ',', ' ') }} FCFA/mois</span>
                @else
                  <span style="color:var(--msq-muted);font-style:italic;">Aucun</span>
                @endif
              </td>
              <td><span class="cust-date"><i class="ri-calendar-line me-1"></i>{{ $customer->date_adhesion->format('d M Y') }}</span></td>
              <td>
                @if(! $customer->montant_engagement)
                  <span class="statut-pill sp-libre"><i class="ri-user-line"></i> Sans engagement</span>
                @elseif($statut === 'a_jour')
                  <span class="statut-pill sp-ajour"><i class="ri-checkbox-circle-line"></i> À jour</span>
                @elseif($statut === 'partiel')
                  <span class="statut-pill sp-partiel"><i class="ri-error-warning-line"></i> Partiel</span>
                @else
                  <span class="statut-pill sp-retard"><i class="ri-time-line"></i> En retard</span>
                @endif
              </td>
              <td>
                @if($customer->typeCotisationMensuel)
                  <span class="statut-pill sp-ajour"><i class="ri-checkbox-circle-line"></i> {{$customer->typeCotisationMensuel->libelle}}</span>
                @else
                  <span class="statut-pill sp-libre"><i class="ri-user-line"></i> Sans engagement</span>
                @endif
              </td>
              <td>
                @if($montantDu > 0)
                  <span style="font-size:12px;font-weight:700;color:var(--msq-danger);">
                    {{ number_format($montantDu, 0, ',', ' ') }} FCFA
                  </span>
                @else
                  <span style="color:var(--msq-muted);font-size:12px;">—</span>
                @endif
              </td>
              <td wire:click.stop="">
                <div class="tbl-actions">
                  <button class="btn btn-soft-primary waves-effect" wire:click="openDetail({{ $customer->id }})" title="Voir détails">
                    <i class="ri-eye-line"></i>
                  </button>
                  <button class="btn btn-soft-warning waves-effect" wire:click="openEdit({{ $customer->id }})" title="Modifier">
                    <i class="ri-edit-line"></i>
                  </button>
                  <button class="btn btn-soft-danger waves-effect" wire:click="confirmDelete({{ $customer->id }})" title="Supprimer">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7">
                <div class="empty-state">
                  <i class="ri-user-search-line"></i>
                  <p>Aucun fidèle trouvé</p>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination Livewire --}}
      <div class="cust-pagination">
        <span class="pag-info">
          {{ $customers->firstItem() }}–{{ $customers->lastItem() }}
          sur {{ $customers->total() }} fidèles
        </span>
        <div class="pag-btns">
          {{ $customers->links('livewire::bootstrap') }}
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- ══ VUE GRILLE ════════════════════════════════════════ --}}
  @if($vue === 'grille')
  <div class="fade-up fu-4" wire:loading.class="opacity-50">
    <div class="cust-grid">
      @forelse($customers as $customer)
      @php
        $cotisationMois = $customer->cotisations->first();
        $statut         = $cotisationMois?->statut ?? ($customer->montant_engagement ? 'en_retard' : null);
        $initiales      = strtoupper(substr($customer->prenom, 0, 1) . substr($customer->nom, 0, 1));
        $avatarColors   = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc','#ea4c4c','#2dce89'];
        $avatarColor    = $avatarColors[($customer->id - 1) % count($avatarColors)];
        $statusColors   = ['a_jour' => '#0ab39c', 'en_retard' => '#f06548', 'partiel' => '#f7b84b', 'libre' => '#878a99'];
        $borderColor    = $customer->montant_engagement
            ? ($statusColors[$statut] ?? '#878a99')
            : '#878a99';
      @endphp
      <div class="cust-card" style="border-top-color:{{ $borderColor }}" wire:click="openDetail({{ $customer->id }})">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
          <div class="card-avatar" style="background:{{ $avatarColor }}">{{ $initiales }}</div>
          {{-- statut pill --}}
          @if(! $customer->montant_engagement)
            <span class="statut-pill sp-libre"><i class="ri-user-line"></i> Sans engagement</span>
          @elseif($statut === 'a_jour')
            <span class="statut-pill sp-ajour"><i class="ri-checkbox-circle-line"></i> À jour</span>
          @elseif($statut === 'partiel')
            <span class="statut-pill sp-partiel"><i class="ri-error-warning-line"></i> Partiel</span>
          @else
            <span class="statut-pill sp-retard"><i class="ri-time-line"></i> En retard</span>
          @endif
        </div>

        <div class="card-name">{{ $customer->prenom }} {{ $customer->nom }}</div>
        <div class="card-phone"><i class="ri-phone-line me-1"></i>{{ $customer->dial_code }} {{ $customer->telephone }}</div>

        <div class="card-info-row">
          <span class="ci-label"><i class="ri-map-pin-line me-1"></i>Adresse</span>
          <span class="ci-value">{{ Str::limit($customer->adresse ?? '—', 18) }}</span>
        </div>
        <div class="card-info-row">
          <span class="ci-label"><i class="ri-money-cny-circle-line me-1"></i>Engagement</span>
          <span class="ci-value" style="color:var(--msq-primary)">
            {{ $customer->montant_engagement ? number_format($customer->montant_engagement, 0, ',', ' ') . ' FCFA' : 'Aucun' }}
          </span>
        </div>
        <div class="card-info-row">
          <span class="ci-label"><i class="ri-calendar-line me-1"></i>Adhésion</span>
          <span class="ci-value">{{ $customer->date_adhesion->format('d M Y') }}</span>
        </div>

        <div class="card-actions" wire:click.stop="">
          <button class="btn btn-soft-primary waves-effect" wire:click="openDetail({{ $customer->id }})">
            <i class="ri-eye-line me-1"></i>Détails
          </button>
          <button class="btn btn-soft-warning waves-effect" wire:click="openEdit({{ $customer->id }})">
            <i class="ri-edit-line me-1"></i>Modifier
          </button>
        </div>

      </div>
      @empty
      <div class="empty-state" style="grid-column:1/-1">
        <i class="ri-user-search-line"></i>
        <p>Aucun fidèle trouvé</p>
      </div>
      @endforelse
    </div>

    <div class="cust-pagination mt-3">
      <span class="pag-info">
        {{ $customers->firstItem() }}–{{ $customers->lastItem() }}
        sur {{ $customers->total() }} fidèles
      </span>
      {{ $customers->links('livewire::bootstrap') }}
    </div>
  </div>
  @endif

</div>{{-- /container-fluid --}}
</div>{{-- /page-content --}}


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL FIDÈLE
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailFidele" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      @if($detailCustomer)
      @php
        $dc             = $detailCustomer;
        $initiales      = strtoupper(substr($dc->prenom, 0, 1) . substr($dc->nom, 0, 1));
        $statut         = $dc->statutGlobal();
        $totalPaye      = $dc->paiements->where('statut', 'success')->sum('montant');
        $avatarColors   = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc','#ea4c4c','#2dce89'];
        $avatarColorDetail = $avatarColors[($dc->id - 1) % count($avatarColors)];
        $cotisationsEnRetard = $dc->cotisations
            ->whereIn('statut', ['en_retard', 'partiel']);
        $totalDu = $cotisationsEnRetard->sum('montant_restant');
      @endphp

      <div class="modal-fidele-header">
        <button class="close-btn" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>

        <div class="mfh-badges">
          @if($statut === 'a_jour')
            <span class="mfh-badge" style="background:rgba(10,179,156,.2);color:#0ab39c;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">✓ À jour</span>
          @elseif($statut === 'partiel')
            <span class="mfh-badge" style="background:rgba(247,184,75,.2);color:#f7b84b;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">◑ Partiel</span>
          @elseif($statut === 'en_retard')
            <span class="mfh-badge" style="background:rgba(240,101,72,.2);color:#f06548;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">⚠ En retard</span>
          @else
            <span class="mfh-badge" style="background:rgba(135,138,153,.2);color:#878a99;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">○ Sans engagement</span>
          @endif
        </div>

        <div class="mfh-inner">
          <div class="mfh-avatar" style="background:{{ $avatarColorDetail }}">{{ $initiales }}</div>
          <div class="mfh-info">
            <div class="mfh-name">{{ $dc->prenom }} {{ $dc->nom }}</div>
            <div class="mfh-meta">
              <span><i class="ri-phone-line"></i> {{ $dc->dial_code }} {{ $dc->telephone }}</span>
              <span><i class="ri-map-pin-line"></i> {{ $dc->adresse ?? 'Non renseignée' }}</span>
              <span><i class="ri-calendar-line"></i> Adhérent depuis {{ $dc->date_adhesion->format('d M Y') }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-stat-row">
        <div class="modal-stat-box">
          <div class="msb-value">{{ $dc->paiements->count() }}</div>
          <div class="msb-label">Paiements effectués</div>
        </div>
        <div class="modal-stat-box">
          <div class="msb-value" style="font-size:13px;">
            {{ number_format($totalPaye, 0, ',', ' ') }} FCFA
          </div>
          <div class="msb-label">Total payé</div>
        </div>
        <div class="modal-stat-box">
          <div class="msb-value">{{ $dc->documents->count() }}</div>
          <div class="msb-label">Documents</div>
        </div>
      </div>

      <div class="modal-fidele-body">

        {{-- Onglets -- gérés en JS vanilla (UX pure) --}}
        <div class="fidele-tabs" id="fideleTabs">
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

        {{-- Panel Infos --}}
        <div class="fidele-tab-panel active" id="tab-infos">
          <div class="detail-grid">
            <div class="detail-item">
              <div class="di-label"><i class="ri-user-line me-1"></i>Nom complet</div>
              <div class="di-value">{{ $dc->prenom }} {{ $dc->nom }}</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-phone-line me-1"></i>Téléphone</div>
              <div class="di-value">{{ $dc->dial_code }} {{ $dc->telephone }}</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-map-pin-line me-1"></i>Adresse</div>
              <div class="di-value">{{ $dc->adresse ?? '—' }}</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-calendar-line me-1"></i>Date d'adhésion</div>
              <div class="di-value">{{ $dc->date_adhesion->format('d/m/Y') }}</div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-money-cny-circle-line me-1"></i>Engagement mensuel</div>
              <div class="di-value" style="color:#405189;font-weight:800">
                @if($dc->montant_engagement)
                  {{ number_format($dc->montant_engagement, 0, ',', ' ') }} FCFA/mois
                @else
                  <span class="text-muted">Sans engagement</span>
                @endif
              </div>
            </div>
            <div class="detail-item">
              <div class="di-label"><i class="ri-checkbox-circle-line me-1"></i>Statut actuel</div>
              <div class="di-value">
                @if($statut === 'a_jour') <span class="statut-pill sp-ajour"><i class="ri-checkbox-circle-line"></i> À jour</span>
                @elseif($statut === 'partiel') <span class="statut-pill sp-partiel"><i class="ri-error-warning-line"></i> Partiel</span>
                @elseif($statut === 'en_retard') <span class="statut-pill sp-retard"><i class="ri-time-line"></i> En retard</span>
                @else <span class="statut-pill sp-libre"><i class="ri-user-line"></i> Sans engagement</span>
                @endif
              </div>
            </div>
          </div>

          @if($totalDu > 0)
          <div class="detail-item mt-3" style="border-left:3px solid #f06548;border-radius:10px;padding:12px;">
            <div class="di-label"><i class="ri-time-line me-1"></i>Retard de cotisation</div>
            <div class="di-value" style="color:#f06548">
              {{ number_format($totalDu, 0, ',', ' ') }} FCFA dus
            </div>
          </div>
          @endif

          <div class="d-flex gap-2 mt-4 flex-wrap">
            <button class="btn btn-primary waves-effect"
                    wire:click="openEdit({{ $dc->id }})"
                    data-bs-dismiss="modal">
            <i class="ri-edit-line me-1"></i> Modifier
            </button>
            <button class="btn btn-soft-danger waves-effect"
                    wire:click="confirmDelete({{ $dc->id }})"
                    data-bs-dismiss="modal">
            <i class="ri-delete-bin-line me-1"></i> Supprimer
            </button>
            <button wire:click="openExportFidele"
                    class="btn btn-sm btn-soft-success waves-effect">
                <i class="ri-file-pdf-line me-1"></i> Bilan PDF
            </button>
            {{-- Bouton payer en avance : grisé si pas de type mensuel --}}
            @if($dc->type_cotisation_mensuel_id && $dc->montant_engagement)
            <button wire:click="openAvance({{ $dc->id }})"
                    class="btn btn-sm waves-effect"
                    style="background:linear-gradient(135deg,#0a5a50,#0ab39c);color:#fff;border:none">
                <i class="ri-calendar-check-2-line me-1"></i> Payer en avance
            </button>
            @else
            <button class="btn btn-sm btn-soft-secondary waves-effect" disabled
                    title="Ce fidèle n'a pas de cotisation mensuelle définie">
                <i class="ri-calendar-check-2-line me-1"></i> Payer en avance
            </button>
            @endif
        </div>
        </div>

        {{-- Panel Cotisations --}}
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
              <tbody>
                @forelse($dc->cotisations->sortByDesc('annee')->sortByDesc('mois') as $cot)
                <tr>
                  <td>{{ $cot->periode ?? '—' }}</td>
                  <td>{{ $cot->typeCotisation?->libelle ?? '—' }}</td>
                  <td>{{ number_format($cot->montant_du, 0, ',', ' ') }} FCFA</td>
                  <td>{{ number_format($cot->montant_paye, 0, ',', ' ') }} FCFA</td>
                  <td>
                    @if($cot->statut === 'a_jour')
                      <span class="statut-pill sp-ajour"><i class="ri-checkbox-circle-line"></i> À jour</span>
                    @elseif($cot->statut === 'partiel')
                      <span class="statut-pill sp-partiel"><i class="ri-error-warning-line"></i> Partiel</span>
                    @else
                      <span class="statut-pill sp-retard"><i class="ri-time-line"></i> En retard</span>
                    @endif
                  </td>
                  <td>{{ $cot->mode_paiement ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-3">Aucune cotisation</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Panel Documents --}}
        <div class="fidele-tab-panel" id="tab-documents">
          <div class="doc-list">
            @forelse($dc->documents as $doc)
            <div class="doc-item">
              <div class="doc-icon" style="background:rgba(64,81,137,.10);color:#405189">
                <i class="ri-file-text-line"></i>
              </div>
              <div>
                <div class="doc-name">{{ $doc->libelle }}</div>
                <div class="doc-type">{{ $doc->type_document }}</div>
              </div>
              <a href="{{ $doc->url }}" target="_blank" class="btn btn-sm btn-soft-primary waves-effect ms-auto">
                <i class="ri-download-line"></i>
              </a>
            </div>
            @empty
            <div style="text-align:center;padding:20px;color:var(--msq-muted);font-size:13px;">
              <i class="ri-folder-open-line" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4"></i>
              Aucun document enregistré
            </div>
            @endforelse
          </div>
          <div class="mt-3">
            <button class="btn btn-soft-primary btn-sm waves-effect">
              <i class="ri-upload-cloud-line me-1"></i> Ajouter un document
            </button>
          </div>
        </div>

      </div>{{-- /modal-fidele-body --}}
      @endif

    </div>{{-- /modal-content --}}
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL AJOUT / MODIFICATION FIDÈLE
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalAddFidele" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      <div class="modal-add-header">
        <div class="mah-title">
          <div class="mah-icon">
            <i class="{{ $customerId ? 'ri-edit-line' : 'ri-user-add-line' }}"></i>
          </div>
          <div>
            <h5>{{ $customerId ? 'Modifier le fidèle' : 'Nouveau fidèle' }}</h5>
            <p>{{ $formStep === 1 ? 'Renseigner les informations' : 'Choisir l\'engagement mensuel' }}</p>
          </div>
        </div>
        <button class="close-btn-wh" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      {{-- Steps indicator --}}
      <div class="add-steps">
        <button class="add-step-btn {{ $formStep >= 1 ? 'active' : '' }}">
          <span class="step-num">1</span> Identité
        </button>
        <button class="add-step-btn {{ $formStep >= 2 ? 'active' : '' }}">
          <span class="step-num">2</span> Engagement
        </button>
      </div>

      {{-- ── PANEL 1 : Identité ────────────────────────────── --}}
      @if($formStep === 1)
      <div class="add-panel active">
        <div class="row g-3">

          <div class="col-6">
            <label class="form-label-msq">Prénom <span class="req">*</span></label>
            <div class="input-with-icon">
              <i class="ri-user-line ii-icon"></i>
              <input type="text" class="input-msq {{ $errors->has('prenom') ? 'is-invalid' : '' }}"
                     wire:model="prenom" placeholder="ex : Mamadou">
            </div>
            @error('prenom') <div class="error-msg">{{ $message }}</div> @enderror
          </div>

          <div class="col-6">
            <label class="form-label-msq">Nom <span class="req">*</span></label>
            <div class="input-with-icon">
              <i class="ri-user-line ii-icon"></i>
              <input type="text" class="input-msq {{ $errors->has('nom') ? 'is-invalid' : '' }}"
                     wire:model="nom" placeholder="ex : Koné">
            </div>
            @error('nom') <div class="error-msg">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label class="form-label-msq">Téléphone <span class="req">*</span></label>
            <div class="dial-group">
              <select class="dial-select" wire:model="dialCode">
                <option value="+225">🇨🇮 +225</option>
                <option value="+223">🇲🇱 +223</option>
                <option value="+226">🇧🇫 +226</option>
                <option value="+227">🇳🇪 +227</option>
                <option value="+228">🇹🇬 +228</option>
                <option value="+229">🇧🇯 +229</option>
              </select>
              <div class="input-with-icon" style="flex:1">
                <i class="ri-phone-line ii-icon"></i>
                <input type="tel" class="input-msq {{ $errors->has('telephone') ? 'is-invalid' : '' }}"
                       wire:model="telephone" placeholder="07 00 00 00 00">
              </div>
            </div>
            @error('telephone') <div class="error-msg">{{ $message }}</div> @enderror
          </div>

          <div class="col-12">
            <label class="form-label-msq">Adresse</label>
            <div class="input-with-icon">
              <i class="ri-map-pin-line ii-icon"></i>
              <input type="text" class="input-msq" wire:model="adresse"
                     placeholder="ex : Yopougon, Abidjan">
            </div>
          </div>

          <div class="col-12">
            <label class="form-label-msq">Date d'adhésion <span class="req">*</span></label>
            <div class="input-with-icon">
              <i class="ri-calendar-line ii-icon"></i>
              <input type="date" class="input-msq {{ $errors->has('dateAdhesion') ? 'is-invalid' : '' }}"
                     wire:model="dateAdhesion">
            </div>
            @error('dateAdhesion') <div class="error-msg">{{ $message }}</div> @enderror
          </div>

        </div>
      </div>
      @endif

      {{-- ── PANEL 2 : Engagement mensuel ─────────────────── --}}
      @if($formStep === 2)
        <div class="add-panel active">

        <p style="font-size:13px;color:var(--msq-muted);margin-bottom:20px">
            <i class="ri-information-line me-1"></i>
            Choisissez le type de cotisation mensuel et le montant d'engagement.
            Ces champs sont optionnels — le cron créera automatiquement la cotisation chaque mois.
        </p>

        {{-- ── Type de cotisation mensuel ─────────────────── --}}
        <div class="mb-4">
            <label class="form-label-msq" style="margin-bottom:10px">
            Type de cotisation mensuel
            <span style="font-size:10px;font-weight:500;color:var(--msq-muted)">(optionnel)</span>
            </label>

            <div style="display:flex;flex-direction:column;gap:8px">
            @foreach($typesMensuels as $tm)
            @php
                $selected = $typeCotisationMensuelId === $tm->id;
                $isRequis = $tm->is_required;
            @endphp
            <div wire:click="selectTypeMensuel({{ $tm->id }})"
                style="
                    display:flex;align-items:center;justify-content:space-between;
                    border:1.5px solid {{ $selected ? '#405189' : '#e9ebec' }};
                    background:{{ $selected ? 'rgba(64,81,137,.06)' : '#fff' }};
                    border-radius:10px;padding:12px 14px;cursor:pointer;
                    transition:all .2s;
                ">
                <div style="display:flex;align-items:center;gap:10px">
                <div style="
                    width:34px;height:34px;border-radius:8px;
                    background:{{ $selected ? 'rgba(64,81,137,.12)' : 'rgba(135,138,153,.08)' }};
                    color:{{ $selected ? '#405189' : '#878a99' }};
                    display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0
                ">
                    <i class="ri-calendar-check-line"></i>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:{{ $selected ? '#405189' : '#212529' }}">
                    {{ $tm->libelle }}
                    </div>
                    <div style="font-size:11px;color:var(--msq-muted);display:flex;align-items:center;gap:6px;margin-top:2px">
                    @if($isRequis)
                        <span style="background:rgba(240,101,72,.1);color:#f06548;padding:1px 6px;border-radius:4px;font-size:9px;font-weight:700">OBLIGATOIRE</span>
                    @endif
                    @if($tm->montant_minimum)
                        <span>min. {{ number_format($tm->montant_minimum, 0, ',', ' ') }} FCFA/mois</span>
                    @endif
                    </div>
                </div>
                </div>
                <div style="
                width:20px;height:20px;border-radius:50%;
                border:2px solid {{ $selected ? '#405189' : '#e9ebec' }};
                background:{{ $selected ? '#405189' : 'transparent' }};
                display:flex;align-items:center;justify-content:center;flex-shrink:0
                ">
                @if($selected)
                <i class="ri-check-line" style="color:#fff;font-size:11px"></i>
                @endif
                </div>
            </div>
            @endforeach

            {{-- Aucun type --}}
            <div wire:click="selectTypeMensuel(null)"
                style="
                    display:flex;align-items:center;gap:10px;
                    border:1.5px solid {{ ! $typeCotisationMensuelId ? '#405189' : '#e9ebec' }};
                    background:{{ ! $typeCotisationMensuelId ? 'rgba(64,81,137,.06)' : '#fff' }};
                    border-radius:10px;padding:12px 14px;cursor:pointer;transition:all .2s;
                ">
                <div style="width:34px;height:34px;border-radius:8px;background:rgba(135,138,153,.08);color:#878a99;display:flex;align-items:center;justify-content:center;font-size:16px">
                <i class="ri-user-line"></i>
                </div>
                <div style="font-size:13px;font-weight:700;color:{{ ! $typeCotisationMensuelId ? '#405189' : '#212529' }}">
                Sans cotisation mensuelle
                </div>
            </div>
            </div>
        </div>

        {{-- ── Bloc confirmation changement de type ───────── --}}
        @if($showConfirmChangementType)
        <div style="
            background:rgba(247,184,75,.07);border:1.5px solid #f7b84b;
            border-left:4px solid #f7b84b;border-radius:0 10px 10px 0;
            padding:16px;margin-bottom:20px;
        ">
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:14px">
            <i class="ri-swap-line" style="color:#f7b84b;font-size:20px;flex-shrink:0;margin-top:1px"></i>
            <div>
                <div style="font-size:13px;font-weight:800;color:#c07a10;margin-bottom:6px">
                Changement de catégorie de cotisation
                </div>
                <div style="font-size:12px;color:#495057;line-height:1.6">
                {{ $confirmChangementMessage }}
                </div>
            </div>
            </div>

            {{-- Champ nouveau montant d'engagement --}}
            <div style="margin-bottom:10px">
            <label style="display:block;font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
                Nouveau montant d'engagement mensuel <span style="color:#f06548">*</span>
            </label>
            <div style="position:relative">
                <i class="ri-money-cny-circle-line" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:15px;color:#878a99;pointer-events:none"></i>
                <input type="number"
                    wire:model="nouvelEngagement"
                    min="1"
                    placeholder="ex : 10000"
                    inputmode="numeric"
                    style="
                        border:1.5px solid {{ $errorNouvelEngagement ? '#f06548' : '#e9ebec' }};
                        border-radius:9px;height:40px;padding:0 14px 0 38px;
                        font-size:13px;font-family:'Nunito',sans-serif;font-weight:600;
                        background:#fff;color:#212529;width:100%;
                    "/>
            </div>
            @if($errorNouvelEngagement)
            <div style="font-size:11px;color:#f06548;margin-top:4px;font-weight:600">{{ $errorNouvelEngagement }}</div>
            @endif
            </div>

            @if($nbRetardsAncienType > 0)
            <div style="background:rgba(240,101,72,.06);border:1.5px solid rgba(240,101,72,.2);border-radius:10px;padding:12px 14px;margin-bottom:12px">
            <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer">
                <input type="checkbox"
                    wire:model="supprimerRetardsChangement"
                    style="width:16px;height:16px;accent-color:#f06548;margin-top:1px;flex-shrink:0"/>
                <div>
                <div style="font-size:13px;font-weight:700;color:#f06548">
                    Supprimer les {{ $nbRetardsAncienType }} cotisation(s) en retard de l'ancien type
                </div>
                <div style="font-size:11px;color:#878a99;margin-top:3px;line-height:1.5">
                    Si coché, toutes les cotisations en retard de l'ancien type seront supprimées définitivement.
                </div>
                </div>
            </label>
            </div>
            @endif

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
            <button type="button" wire:click="annulerChangementType"
                    style="border:1.5px solid #e9ebec;border-radius:8px;background:#fff;color:#878a99;font-size:12px;font-weight:700;padding:7px 14px;cursor:pointer;font-family:'Nunito',sans-serif">
                <i class="ri-close-line me-1"></i>Annuler
            </button>
            <button type="button" wire:click="confirmerChangementType" wire:loading.attr="disabled"
                    style="background:linear-gradient(135deg,#c07a10,#f7b84b);border:none;border-radius:8px;color:#fff;font-size:12px;font-weight:700;padding:7px 16px;cursor:pointer;font-family:'Nunito',sans-serif;display:inline-flex;align-items:center;gap:5px">
                <span wire:loading wire:target="confirmerChangementType" class="spinner-border spinner-border-sm"></span>
                <i class="ri-check-double-line" wire:loading.remove wire:target="confirmerChangementType"></i>
                <span wire:loading.remove wire:target="confirmerChangementType">Confirmer</span>
                <span wire:loading wire:target="confirmerChangementType">…</span>
            </button>
            </div>
        </div>
        @endif

        {{-- ── Montant d'engagement ─────────────────────── --}}
        @if(! $showConfirmChangementType)
        <div class="mb-3">
            <label class="form-label-msq">Montant d'engagement mensuel</label>
            <div class="engagement-grid">
            @foreach($coutEngagements as $cout)
            <div class="eng-pill {{ $montantEngagement === $cout->montant ? 'selected' : '' }}"
                wire:click="selectEngagement({{ $cout->montant }})">
                <div class="ep-val">{{ number_format($cout->montant, 0, ',', ' ') }}</div>
                <div class="ep-lbl">FCFA / mois</div>
            </div>
            @endforeach
            </div>

            <div class="mt-3">
            <label class="form-label-msq">Ou saisir un montant personnalisé</label>
            <div class="input-with-icon">
                <i class="ri-money-cny-circle-line ii-icon"></i>
                <input type="number" class="input-msq"
                    wire:model.live="montantEngagement"
                    placeholder="Montant en FCFA">
            </div>
            </div>
        </div>

        <div class="mt-3 p-3" style="background:rgba(64,81,137,.06);border-radius:10px;border-left:3px solid #405189;">
            <p style="font-size:12px;color:var(--msq-text);margin:0">
            <i class="ri-information-line me-1" style="color:#405189"></i>
            <strong>Note :</strong> La cotisation mensuelle sera créée automatiquement chaque 5 du mois
            par le système, basée sur le type et le montant d'engagement définis ici.
            </p>
        </div>
        @endif

        </div>
        @endif

      {{-- Footer --}}
      <div class="modal-add-footer">
        <button class="btn-msq-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        <div class="d-flex gap-2">
          @if($formStep === 2)
          <button class="btn-msq-secondary" wire:click="prevStep">
            <i class="ri-arrow-left-line me-1"></i> Précédent
          </button>
          @endif

          @if($formStep === 1)
          <button class="btn-msq-primary" wire:click="nextStep" wire:loading.attr="disabled">
            <span wire:loading wire:target="nextStep" class="spinner-border spinner-border-sm me-1"></span>
            Suivant <i class="ri-arrow-right-line ms-1"></i>
          </button>
          @endif

          @if($formStep === 2)
          <button class="btn-msq-primary" wire:click="save" wire:loading.attr="disabled">
            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
            <span wire:loading.remove wire:target="save">
              <i class="ri-save-line me-1"></i> Enregistrer
            </span>
            <span wire:loading wire:target="save">Enregistrement…</span>
          </button>
          @endif
        </div>
      </div>

    </div>{{-- /modal-content --}}
  </div>
</div>

{{-- ══ MODAL EXPORT BILAN FIDÈLE ═════════════════════════ --}}
<div class="modal fade" id="modalExportFidele" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:420px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;display:flex;flex-direction:column">
      <div style="background:linear-gradient(135deg,#c0341a,#f06548);padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff">
            <i class="ri-file-pdf-line"></i>
          </div>
          <div>
            <div style="font-size:15px;font-weight:800;color:#fff">Exporter le bilan</div>
            <div style="font-size:11px;color:rgba(255,255,255,.65)">Choisissez la période</div>
          </div>
        </div>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div style="padding:22px 24px 0">
        <div class="mb-3">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;font-weight:600;color:#495057">
            <input type="checkbox" wire:model.live="exportTout" style="width:18px;height:18px;accent-color:#405189"/>
            Tout l'historique du fidèle
          </label>
        </div>
        @if(! $exportTout)
        <div class="row g-2">
          <div class="col-6">
            <label style="font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px">Du</label>
            <input type="date" class="form-control" wire:model="exportDebut"
                   style="border-radius:9px;border:1.5px solid #e9ebec;font-size:13px"/>
          </div>
          <div class="col-6">
            <label style="font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px">Au</label>
            <input type="date" class="form-control" wire:model="exportFin"
                   style="border-radius:9px;border:1.5px solid #e9ebec;font-size:13px"/>
          </div>
        </div>
        @endif
      </div>
      <div style="padding:16px 24px;display:flex;justify-content:space-between;gap:10px;border-top:1px solid #e9ebec;margin-top:20px">
        <button class="btn btn-light" data-bs-dismiss="modal" style="border-radius:9px;font-weight:700">Annuler</button>
        <button wire:click="exportBilanFidele"
                style="background:linear-gradient(135deg,#c0341a,#f06548);border:none;border-radius:9px;color:#fff;font-size:13px;font-weight:700;padding:9px 20px;cursor:pointer;display:flex;align-items:center;gap:6px">
          <i class="ri-download-line"></i> Télécharger le PDF
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ══ MODAL PAIEMENT EN AVANCE (BO Fidèle) ════════════════ --}}
<div class="modal fade" id="modalAvanceFidele" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:580px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
 
      {{-- Header --}}
      <div style="background:linear-gradient(135deg,#0a5a50,#0ab39c);padding:20px 24px;display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:42px;height:42px;border-radius:10px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff">
            <i class="ri-calendar-check-2-line"></i>
          </div>
          <div>
            <div style="font-size:15px;font-weight:800;color:#fff">Paiement en avance</div>
            @if($avanceCustomer)
            <div style="font-size:11px;color:rgba(255,255,255,.75)">
              {{ $avanceCustomer->prenom }} {{ $avanceCustomer->nom }}
              — {{ $avanceCustomer->typeCotisationMensuel?->libelle }}
              — {{ number_format($avanceCustomer->montant_engagement, 0, ',', ' ') }} FCFA/mois
            </div>
            @endif
          </div>
        </div>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
 
      {{-- Total en header --}}
      @if($avanceCustomer && count($avancePreview))
      <div style="background:rgba(10,179,156,.08);border-bottom:1px solid rgba(10,179,156,.15);padding:12px 24px;display:flex;justify-content:space-between;align-items:center">
        <span style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px">
          {{ count($avancePreview) }} mois · Total
        </span>
        <span style="font-size:20px;font-weight:900;color:#0ab39c">
          {{ number_format(count($avancePreview) * ($avanceCustomer->montant_engagement ?? 0), 0, ',', ' ') }} FCFA
        </span>
      </div>
      @endif
 
      <div style="overflow-y:auto;max-height:calc(90vh - 200px);padding:20px 24px">
 
        {{-- Slider nombre de mois --}}
        <div style="margin-bottom:24px">
          <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">
            Nombre de mois à payer en avance
          </div>
          <div style="display:flex;align-items:center;gap:14px">
            <button wire:click="$set('avanceNbMois', {{ max(1, $avanceNbMois - 1) }})"
                    style="width:36px;height:36px;border-radius:50%;border:2px solid #e9ebec;background:#fff;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#405189;flex-shrink:0;font-weight:700">
              −
            </button>
            <div style="flex:1;text-align:center">
              <div style="font-size:40px;font-weight:900;color:#405189;line-height:1">{{ $avanceNbMois }}</div>
              <div style="font-size:11px;color:#878a99;margin-top:2px">mois</div>
            </div>
            <button wire:click="$set('avanceNbMois', {{ min(24, $avanceNbMois + 1) }})"
                    style="width:36px;height:36px;border-radius:50%;border:2px solid #e9ebec;background:#fff;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#405189;flex-shrink:0;font-weight:700">
              +
            </button>
          </div>
          <input type="range" wire:model.live="avanceNbMois" min="1" max="24"
                 style="width:100%;margin-top:12px;accent-color:#0ab39c"/>
        </div>
 
        {{-- Preview mois --}}
        @if(count($avancePreview))
        <div style="margin-bottom:24px">
          <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
            Cotisations qui seront créées
          </div>
          <div style="display:flex;flex-direction:column;gap:6px;max-height:200px;overflow-y:auto">
            @foreach($avancePreview as $i => $row)
            <div style="display:flex;align-items:center;justify-content:space-between;background:rgba(10,179,156,.05);border:1px solid rgba(10,179,156,.12);border-radius:10px;padding:10px 14px">
              <div style="display:flex;align-items:center;gap:10px">
                <div style="width:28px;height:28px;border-radius:7px;background:rgba(10,179,156,.12);color:#0ab39c;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0">
                  {{ $i + 1 }}
                </div>
                <div>
                  <div style="font-size:13px;font-weight:600;color:#212529">{{ $row['label'] }}</div>
                  <div style="font-size:10px;color:#878a99;margin-top:1px">Statut : En attente de validation</div>
                </div>
              </div>
              <div style="font-size:13px;font-weight:800;color:#0ab39c;font-family:monospace">
                {{ number_format($row['montant'], 0, ',', ' ') }} FCFA
              </div>
            </div>
            @endforeach
          </div>
 
          {{-- Total recap --}}
          @if($avanceCustomer)
          <div style="background:linear-gradient(135deg,#2d3a63,#405189);border-radius:10px;padding:14px 16px;margin-top:10px;display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-size:11px;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:.5px">Total à encaisser</div>
              <div style="font-size:11px;color:rgba(255,255,255,.6);margin-top:2px">{{ count($avancePreview) }} mois × {{ number_format($avanceCustomer->montant_engagement, 0, ',', ' ') }} FCFA</div>
            </div>
            <div style="font-size:22px;font-weight:900;color:#fff">
              {{ number_format(count($avancePreview) * ($avanceCustomer->montant_engagement ?? 0), 0, ',', ' ') }} FCFA
            </div>
          </div>
          @endif
        </div>
        @endif
 
        {{-- Mode de paiement --}}
        <div style="margin-bottom:20px">
          <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px">
            Mode de paiement
          </div>
 
          <div style="display:flex;flex-direction:column;gap:8px">
 
            <div wire:click="selectAvanceMode('espece')"
                 style="display:flex;align-items:center;gap:12px;border:2px solid {{ $avanceMode === 'espece' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'espece' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s">
              <div style="width:40px;height:40px;border-radius:10px;background:rgba(247,184,75,.1);display:flex;align-items:center;justify-content:center;font-size:20px;color:#d4a843;flex-shrink:0">
                <i class="ri-money-dollar-circle-line"></i>
              </div>
              <div>
                <div style="font-size:13px;font-weight:700;color:{{ $avanceMode === 'espece' ? '#405189' : '#212529' }}">Espèces</div>
                <div style="font-size:11px;color:#878a99;margin-top:2px">Remise physique à l'administration</div>
              </div>
              <div style="margin-left:auto;width:22px;height:22px;border-radius:50%;border:2px solid {{ $avanceMode === 'espece' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'espece' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                @if($avanceMode === 'espece')<i class="ri-check-line" style="color:#fff;font-size:12px"></i>@endif
              </div>
            </div>
 
            <div wire:click="selectAvanceMode('mobile_money')"
                 style="display:flex;align-items:center;gap:12px;border:2px solid {{ $avanceMode === 'mobile_money' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'mobile_money' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s">
              <div style="width:40px;height:40px;border-radius:10px;background:rgba(10,179,156,.1);display:flex;align-items:center;justify-content:center;font-size:20px;color:#0ab39c;flex-shrink:0">
                <i class="ri-smartphone-line"></i>
              </div>
              <div>
                <div style="font-size:13px;font-weight:700;color:{{ $avanceMode === 'mobile_money' ? '#405189' : '#212529' }}">Mobile Money</div>
                <div style="font-size:11px;color:#878a99;margin-top:2px">Orange Money, MTN MoMo, Wave</div>
              </div>
              <div style="margin-left:auto;width:22px;height:22px;border-radius:50%;border:2px solid {{ $avanceMode === 'mobile_money' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'mobile_money' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                @if($avanceMode === 'mobile_money')<i class="ri-check-line" style="color:#fff;font-size:12px"></i>@endif
              </div>
            </div>
 
            <div wire:click="selectAvanceMode('virement')"
                 style="display:flex;align-items:center;gap:12px;border:2px solid {{ $avanceMode === 'virement' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'virement' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s">
              <div style="width:40px;height:40px;border-radius:10px;background:rgba(64,81,137,.1);display:flex;align-items:center;justify-content:center;font-size:20px;color:#405189;flex-shrink:0">
                <i class="ri-bank-line"></i>
              </div>
              <div>
                <div style="font-size:13px;font-weight:700;color:{{ $avanceMode === 'virement' ? '#405189' : '#212529' }}">Virement bancaire</div>
                <div style="font-size:11px;color:#878a99;margin-top:2px">Virement SGCI, BICICI, etc.</div>
              </div>
              <div style="margin-left:auto;width:22px;height:22px;border-radius:50%;border:2px solid {{ $avanceMode === 'virement' ? '#405189' : '#e9ebec' }};background:{{ $avanceMode === 'virement' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                @if($avanceMode === 'virement')<i class="ri-check-line" style="color:#fff;font-size:12px"></i>@endif
              </div>
            </div>
 
          </div>
 
          @if($avanceErrorMode)
          <div style="font-size:12px;color:#f06548;margin-top:8px;font-weight:600">
            <i class="ri-error-warning-line me-1"></i>{{ $avanceErrorMode }}
          </div>
          @endif
        </div>
 
        {{-- Note --}}
        <div style="background:rgba(64,81,137,.04);border:1px dashed rgba(64,81,137,.2);border-radius:10px;padding:12px 14px">
          <p style="font-size:11px;color:#878a99;margin:0;line-height:1.6">
            <i class="ri-information-line me-1" style="color:#405189"></i>
            Les cotisations seront créées en <strong>attente de validation</strong>.
            Un seul paiement sera créé pour l'ensemble des mois.
            Validez-le depuis le module Paiements une fois les fonds reçus.
          </p>
        </div>
 
      </div>
 
      {{-- Footer --}}
      <div style="padding:16px 24px;border-top:1px solid #e9ebec;display:flex;justify-content:space-between;gap:10px">
        <button class="btn btn-light" style="border-radius:9px;font-weight:700" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i>Annuler
        </button>
        <button wire:click="submitAvance"
                wire:loading.attr="disabled"
                style="background:linear-gradient(135deg,#0a5a50,#0ab39c);border:none;border-radius:9px;color:#fff;font-size:13px;font-weight:700;padding:10px 22px;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
          <span wire:loading wire:target="submitAvance" class="spinner-border spinner-border-sm"></span>
          <i class="ri-check-double-line" wire:loading.remove wire:target="submitAvance"></i>
          <span wire:loading.remove wire:target="submitAvance">
            Créer {{ count($avancePreview) }} cotisation(s)
          </span>
          <span wire:loading wire:target="submitAvance">Traitement…</span>
        </button>
      </div>
 
    </div>
  </div>
</div>


@push('styles')
<link href="{{ asset('assets/css/customers.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script>
// ── Onglets du modal détail (UX pure, JS vanilla) ──────────
document.addEventListener('click', function (e) {
    const tab = e.target.closest('.fidele-tab');
    if (!tab) return;

    const container = tab.closest('.modal-fidele-body');
    if (!container) return;

    container.querySelectorAll('.fidele-tab').forEach(t => t.classList.remove('active'));
    container.querySelectorAll('.fidele-tab-panel').forEach(p => p.classList.remove('active'));

    tab.classList.add('active');
    const target = document.getElementById(tab.dataset.tab);
    if (target) target.classList.add('active');
});

// ── Ouvrir/fermer les modals Bootstrap via events Livewire ──
Livewire.on('OpenModalModilEdit', ({ name_modal }) => {
    const el = document.getElementById(name_modal);
    if (el) bootstrap.Modal.getOrCreateInstance(el).show();
});

Livewire.on('closeModalModilEdit', ({ name_modal }) => {
    const el = document.getElementById(name_modal);
    if (el) bootstrap.Modal.getOrCreateInstance(el).hide();
});

// ── SweetAlert : confirmation avec boutons ─────────────────
Livewire.on('swal:modalDeleteOptionsWithButton', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    Swal.fire({
        title: data.title,
        text:  data.text,
        icon:  data.type,
        showCancelButton:  true,
        confirmButtonText: data.succesButton ?? 'Confirmer',
        cancelButtonText:  data.cancelButton ?? 'Annuler',
        confirmButtonColor: '#f06548',
        cancelButtonColor:  '#878a99',
    }).then(result => {
        if (result.isConfirmed) {
            Livewire.dispatch(data.eventRetour, { id: data.id });
        }
    });
});

// ── Toast ─────────────────────────────────────────────────
Livewire.on('modalShowmessageToast', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    const posMap = {
        'top-end':    'top-end',
        'top-start':  'top-start',
        'bottom-end': 'bottom-end',
    };
    Swal.fire({
        toast:    true,
        position: posMap[data.position] ?? 'top-end',
        icon:     data.type,
        title:    data.title,
        showConfirmButton: false,
        timer:    3000,
        timerProgressBar: true,
    });
});

// ── SweetAlert : message simple avec timer ─────────────────
Livewire.on('swal:modalGetInfo_message', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    Swal.fire({ title: data.title, text: data.text, icon: data.type, timer: 3000 });
});

Livewire.on('swal:modalGetInfo_message_not_timer', (payload) => {
    const data = Array.isArray(payload) ? payload[0] : payload;
    Swal.fire({ title: data.title, text: data.text, icon: data.type });
});
</script>
@endpush

</div>

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
