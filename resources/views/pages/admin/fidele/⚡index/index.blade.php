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
        <div class="ckpi-value">{{ $this->kpis['total'] }}</div>
        <div class="ckpi-sub">Inscrits dans la base</div>
      </div>
    </div>
    <div class="ckpi ck-ajour fade-up fu-2">
      <div class="ckpi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
        <i class="ri-checkbox-circle-line"></i>
      </div>
      <div>
        <div class="ckpi-label">À jour</div>
        <div class="ckpi-value">{{ $this->kpis['ajour'] }}</div>
        <div class="ckpi-sub">Cotisation mensuelle OK</div>
      </div>
    </div>
    <div class="ckpi ck-retard fade-up fu-3">
      <div class="ckpi-icon" style="background:rgba(240,101,72,.10);color:#f06548">
        <i class="ri-time-line"></i>
      </div>
      <div>
        <div class="ckpi-label">En retard</div>
        <div class="ckpi-value">{{ $this->kpis['enRetard'] }}</div>
        <div class="ckpi-sub">ou paiement partiel</div>
      </div>
    </div>
    <div class="ckpi ck-libre fade-up fu-4">
      <div class="ckpi-icon" style="background:rgba(135,138,153,.10);color:#878a99">
        <i class="ri-user-line"></i>
      </div>
      <div>
        <div class="ckpi-label">Sans engagement</div>
        <div class="ckpi-value">{{ $this->kpis['sansEngagement'] }}</div>
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
              <th>Montant dû</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($customers as $customer)
            @php
              $cotisationMois = $customer->cotisations->first();
              $statut         = $cotisationMois?->statut ?? ($customer->montant_engagement ? 'en_retard' : null);
              $montantDu      = $cotisationMois?->montant_restant ?? 0;
              $initiales      = strtoupper(substr($customer->prenom, 0, 1) . substr($customer->nom, 0, 1));
            @endphp
            <tr>
              <td>
                <div class="cust-cell-name">
                  <div class="cust-avatar">{{ $initiales }}</div>
                  <div>
                    <div class="cn-name">{{ $customer->prenom }} {{ $customer->nom }}</div>
                    <div class="cn-sub">{{ $customer->adresse ?? '—' }}</div>
                  </div>
                </div>
              </td>
              <td>{{ $customer->dial_code }} {{ $customer->telephone }}</td>
              <td>
                @if($customer->montant_engagement)
                  <span class="eng-badge">
                    {{ number_format($customer->montant_engagement, 0, ',', ' ') }} FCFA/mois
                  </span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>{{ $customer->date_adhesion->format('d/m/Y') }}</td>
              <td>
                @if(! $customer->montant_engagement)
                  <span class="statut-badge libre">Libre</span>
                @elseif($statut === 'a_jour')
                  <span class="statut-badge ajour"><i class="ri-checkbox-circle-line me-1"></i>À jour</span>
                @elseif($statut === 'partiel')
                  <span class="statut-badge partiel"><i class="ri-time-line me-1"></i>Partiel</span>
                @else
                  <span class="statut-badge retard"><i class="ri-error-warning-line me-1"></i>En retard</span>
                @endif
              </td>
              <td>
                @if($montantDu > 0)
                  <span style="color:#f06548;font-weight:700;">
                    {{ number_format($montantDu, 0, ',', ' ') }} FCFA
                  </span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>
                <div class="action-btns">
                  <button class="ab-btn ab-detail" wire:click="openDetail({{ $customer->id }})"
                          title="Voir le détail">
                    <i class="ri-eye-line"></i>
                  </button>
                  <button class="ab-btn ab-edit" wire:click="openEdit({{ $customer->id }})"
                          title="Modifier">
                    <i class="ri-edit-line"></i>
                  </button>
                  <button class="ab-btn ab-delete" wire:click="confirmDelete({{ $customer->id }})"
                          title="Supprimer">
                    <i class="ri-delete-bin-line"></i>
                  </button>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">
                <i class="ri-group-line me-2" style="font-size:20px;"></i>
                Aucun fidèle trouvé
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
      @endphp
      <div class="cust-card">
        <div class="cc-header">
          <div class="cust-avatar lg">{{ $initiales }}</div>
          <div class="cc-actions">
            <button class="ab-btn ab-edit" wire:click="openEdit({{ $customer->id }})">
              <i class="ri-edit-line"></i>
            </button>
            <button class="ab-btn ab-delete" wire:click="confirmDelete({{ $customer->id }})">
              <i class="ri-delete-bin-line"></i>
            </button>
          </div>
        </div>
        <div class="cc-name">{{ $customer->prenom }} {{ $customer->nom }}</div>
        <div class="cc-tel">{{ $customer->dial_code }} {{ $customer->telephone }}</div>

        @if(! $customer->montant_engagement)
          <span class="statut-badge libre">Libre</span>
        @elseif($statut === 'a_jour')
          <span class="statut-badge ajour"><i class="ri-checkbox-circle-line me-1"></i>À jour</span>
        @elseif($statut === 'partiel')
          <span class="statut-badge partiel"><i class="ri-time-line me-1"></i>Partiel</span>
        @else
          <span class="statut-badge retard"><i class="ri-error-warning-line me-1"></i>En retard</span>
        @endif

        @if($customer->montant_engagement)
        <div class="cc-eng">
          {{ number_format($customer->montant_engagement, 0, ',', ' ') }} FCFA/mois
        </div>
        @endif

        <button class="cc-detail-btn" wire:click="openDetail({{ $customer->id }})">
          <i class="ri-eye-line me-1"></i> Voir le profil
        </button>
      </div>
      @empty
      <div class="col-12 text-center text-muted py-4">
        Aucun fidèle trouvé
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

      @if($this->detailCustomer)
      @php
        $dc        = $this->detailCustomer;
        $initiales = strtoupper(substr($dc->prenom, 0, 1) . substr($dc->nom, 0, 1));
        $statut    = $dc->statutGlobal();
        $totalPaye = $dc->paiements->where('statut', 'success')->sum('montant');

        // Calcul retard mensuel
        $cotisationsEnRetard = $dc->cotisations
            ->whereIn('statut', ['en_retard', 'partiel'])
            ->where('typeCotisation.type', 'mensuel');
        $totalDu = $cotisationsEnRetard->sum('montant_restant');
      @endphp

      <div class="modal-fidele-header">
        <button class="close-btn" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>

        <div class="mfh-badges">
          @if($statut === 'a_jour')
            <span class="mfh-badge ajour"><i class="ri-checkbox-circle-line me-1"></i>À jour</span>
          @elseif($statut === 'partiel')
            <span class="mfh-badge partiel"><i class="ri-time-line me-1"></i>Partiel</span>
          @elseif($statut === 'en_retard')
            <span class="mfh-badge retard"><i class="ri-error-warning-line me-1"></i>En retard</span>
          @else
            <span class="mfh-badge libre">Sans engagement</span>
          @endif
        </div>

        <div class="mfh-inner">
          <div class="mfh-avatar">{{ $initiales }}</div>
          <div class="mfh-info">
            <div class="mfh-name">{{ $dc->prenom }} {{ $dc->nom }}</div>
            <div class="mfh-meta">
              <span><i class="ri-phone-line"></i> {{ $dc->dial_code }} {{ $dc->telephone }}</span>
              <span><i class="ri-map-pin-line"></i> {{ $dc->adresse ?? 'Non renseignée' }}</span>
              <span><i class="ri-calendar-line"></i> {{ $dc->date_adhesion->format('d/m/Y') }}</span>
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
                @if($statut === 'a_jour') <span class="statut-badge ajour">À jour</span>
                @elseif($statut === 'partiel') <span class="statut-badge partiel">Partiel</span>
                @elseif($statut === 'en_retard') <span class="statut-badge retard">En retard</span>
                @else <span class="statut-badge libre">Sans engagement</span>
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
                      <span class="statut-badge ajour">À jour</span>
                    @elseif($cot->statut === 'partiel')
                      <span class="statut-badge partiel">Partiel</span>
                    @else
                      <span class="statut-badge retard">En retard</span>
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
          @forelse($dc->documents as $doc)
          <div class="doc-item">
            <i class="ri-file-line me-2"></i>
            <div>
              <div class="doc-name">{{ $doc->libelle }}</div>
              <div class="doc-type text-muted" style="font-size:12px;">{{ $doc->type_document }}</div>
            </div>
            <a href="{{ $doc->url }}" target="_blank" class="btn btn-soft-primary btn-sm ms-auto">
              <i class="ri-eye-line"></i>
            </a>
          </div>
          @empty
          <div class="text-center text-muted py-3">Aucun document</div>
          @endforelse

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

        <p style="font-size:13px;color:var(--msq-muted);margin-bottom:16px;">
          <i class="ri-information-line me-1"></i>
          Sélectionnez le montant d'engagement mensuel. Laissez vide pour un fidèle sans cotisation mensuelle.
        </p>

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

        <div class="mt-4 p-3" style="background:rgba(64,81,137,.06);border-radius:10px;border-left:3px solid #405189;">
          <p style="font-size:12px;color:var(--msq-text);margin:0;">
            <i class="ri-information-line me-1" style="color:#405189"></i>
            <strong>Note :</strong> Si un engagement est sélectionné, une première cotisation sera créée
            pour le mois en cours avec le statut <em>En retard</em> jusqu'au premier paiement.
          </p>
        </div>

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