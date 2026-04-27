<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ PAGE HEADER ══════════════════════════════════════ --}}
  <div class="pay-page-header fu fu-1">
    <div>
      <h4>Paiements</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Paiements</li>
        </ol>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-soft-success btn-sm waves-effect">
        <i class="ri-file-excel-2-line me-1"></i> Exporter
      </button>
    </div>
  </div>

  {{-- ══ KPI STRIP ════════════════════════════════════════ --}}
  <div class="pay-kpi-strip">
    <div class="pay-kpi pk-total fu fu-1">
      <div class="pki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-bank-card-line"></i></div>
      <div>
        <div class="pki-label">Total</div>
        <div class="pki-val">{{ $kpis['total'] }}</div>
        <div class="pki-sub">Tous paiements</div>
      </div>
    </div>
    <div class="pay-kpi pk-success fu fu-2">
      <div class="pki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="pki-label">Validés</div>
        <div class="pki-val">{{ $kpis['success'] }}</div>
        <div class="pki-sub">Confirmés</div>
      </div>
    </div>
    <div class="pay-kpi pk-pending fu fu-3">
      <div class="pki-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
      <div>
        <div class="pki-label">En attente</div>
        <div class="pki-val">{{ $kpis['pending'] }}</div>
        <div class="pki-sub">À valider</div>
      </div>
    </div>
    <div class="pay-kpi pk-failed fu fu-4">
      <div class="pki-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
      <div>
        <div class="pki-label">Échoués</div>
        <div class="pki-val">{{ $kpis['failed'] }}</div>
        <div class="pki-sub">Rejetés / annulés</div>
      </div>
    </div>
    <div class="pay-kpi pk-montant fu fu-5">
      <div class="pki-icon" style="background:rgba(212,168,67,.12);color:#d4a843"><i class="ri-money-cny-circle-line"></i></div>
      <div>
        <div class="pki-label">Total collecté</div>
        <div class="pki-val" style="font-size:14px">{{ number_format($kpis['montant'], 0, ',', ' ') }} FCFA</div>
        <div class="pki-sub">Paiements validés</div>
      </div>
    </div>
  </div>

  {{-- ══ TABS STATUT ══════════════════════════════════════ --}}
  <div class="pay-tabs fu fu-2">
    <span class="tab-label"><i class="ri-filter-3-line me-1"></i>Statut :</span>
    <button class="pay-tab tab-tous {{ $filterStatut === 'tous' ? 'active' : '' }}" wire:click="$set('filterStatut','tous')">
      <i class="ri-list-check"></i>Tous <span class="tab-count">{{ $tabCounts['tous'] }}</span>
    </button>
    <button class="pay-tab tab-success {{ $filterStatut === 'success' ? 'active' : '' }}" wire:click="$set('filterStatut','success')">
      <i class="ri-checkbox-circle-line"></i>Validés <span class="tab-count">{{ $tabCounts['success'] }}</span>
    </button>
    <button class="pay-tab tab-pending {{ $filterStatut === 'en_attente' ? 'active' : '' }}" wire:click="$set('filterStatut','en_attente')">
      <i class="ri-time-line"></i>En attente <span class="tab-count">{{ $tabCounts['en_attente'] }}</span>
    </button>
    <button class="pay-tab tab-failed {{ $filterStatut === 'echec' ? 'active' : '' }}" wire:click="$set('filterStatut','echec')">
      <i class="ri-close-circle-line"></i>Échoués <span class="tab-count">{{ $tabCounts['echec'] }}</span>
    </button>
  </div>

  {{-- ══ TOOLBAR ══════════════════════════════════════════ --}}
  <div class="pay-toolbar fu fu-3">
    <div class="sw">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher fidèle, référence…">
    </div>
    <select class="pay-sel" wire:model.live="filterMode">
      <option value="tous">Tous modes</option>
      <option value="mobile_money">Mobile Money</option>
      <option value="espece">Espèces</option>
      <option value="virement">Virement</option>
    </select>
    <select class="pay-sel" wire:model.live="filterMois" style="min-width:120px">
      <option value="tous">Tous mois</option>
      @foreach(range(1,12) as $m)
        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
      @endforeach
    </select>
  </div>

  {{-- ══ TABLE ════════════════════════════════════════════ --}}
  <div class="pay-table-card fu fu-4" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="pay-table">
        <thead>
          <tr>
            <th>Fidèle</th>
            <th>Référence</th>
            <th>Montant</th>
            <th>Mode</th>
            <th>Source</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($paiements as $pay)
          @php
            $ac = ['#405189','#0ab39c','#f06548','#f7b84b','#299cdb','#d4a843','#3577f1','#6559cc'];
            $avatarColor = $ac[($pay->customer_id - 1) % count($ac)];
            $initiales   = $pay->customer ? strtoupper(substr($pay->customer->prenom,0,1).substr($pay->customer->nom,0,1)) : '??';

            $rowCls = match($pay->statut) {
                'success'    => 'row-success',
                'en_attente' => 'row-pending',
                'echec'      => 'row-failed',
                'refund'     => 'row-refund',
                default      => '',
            };
            $pillData = match($pay->statut) {
                'success'    => ['pp-success','ri-checkbox-circle-line','Validé'],
                'en_attente' => ['pp-pending','ri-time-line','En attente'],
                'echec'      => ['pp-failed','ri-close-circle-line','Échoué'],
                'refund'     => ['pp-refund','ri-refund-2-line','Remboursé'],
                default      => ['pp-pending','ri-question-line',$pay->statut],
            };
            $modeData = match($pay->mode_paiement) {
                'mobile_money' => ['pm-mm','ri-smartphone-line','Mobile Money'],
                'espece'       => ['pm-esp','ri-money-dollar-circle-line','Espèces'],
                'virement'     => ['pm-vir','ri-bank-line','Virement'],
                default        => ['pm-nd','ri-question-line','—'],
            };
            $periode = $pay->cotisation?->mois && $pay->cotisation?->annee
                ? \Carbon\Carbon::create()->month($pay->cotisation->mois)->translatedFormat('F') . ' ' . $pay->cotisation->annee
                : null;
          @endphp

          <tr class="{{ $rowCls }}" wire:click="openDetail({{ $pay->id }})">

            {{-- Fidèle --}}
            <td>
              <div style="display:flex;align-items:center;gap:9px">
                <div class="pay-avatar" style="background:{{ $avatarColor }}">{{ $initiales }}</div>
                <div>
                  <div class="pay-fidele-name">{{ $pay->customer?->prenom }} {{ $pay->customer?->nom }}</div>
                  <div class="pay-fidele-phone">{{ $pay->customer?->dial_code }} {{ $pay->customer?->phone }}</div>
                </div>
              </div>
            </td>

            {{-- Référence --}}
            <td>
              <span class="pay-ref">{{ $pay->reference ?? 'PAY-' . str_pad($pay->id, 5, '0', STR_PAD_LEFT) }}</span>
            </td>

            {{-- Montant --}}
            <td>
              <span class="pay-montant">{{ number_format($pay->montant, 0, ',', ' ') }} FCFA</span>
            </td>

            {{-- Mode --}}
            <td>
              <span class="pay-mode {{ $modeData[0] }}">
                <i class="{{ $modeData[1] }}"></i>{{ $modeData[2] }}
              </span>
            </td>

            {{-- Source --}}
            <td>
              <span style="font-size:11px;font-weight:600;color:var(--pay-text);display:flex;align-items:center;gap:5px">
                <i class="ri-link-m" style="color:var(--pay-muted)"></i>
                {{ $pay->cotisation?->typeCotisation?->libelle ?? '—' }}
              </span>
              @if($periode)
              <div style="font-size:10px;color:var(--pay-muted);margin-top:2px">{{ $periode }}</div>
              @endif
            </td>

            {{-- Date --}}
            <td>
              <div style="font-size:12px;color:var(--pay-text)">{{ $pay->date_paiement->format('d M Y') }}</div>
              <div style="font-size:10px;color:var(--pay-muted)">{{ $pay->date_paiement->format('H:i') }}</div>
            </td>

            {{-- Statut --}}
            <td>
              <span class="pay-pill {{ $pillData[0] }}">
                <i class="{{ $pillData[1] }}"></i>{{ $pillData[2] }}
              </span>
            </td>

            {{-- Actions --}}
            <td wire:click.stop="">
              <div class="pay-actions">
                <button class="btn btn-soft-primary waves-effect"
                        wire:click="openDetail({{ $pay->id }})" title="Voir détails">
                  <i class="ri-eye-line"></i>
                </button>
                <button wire:click="exportRecu({{ $pay->id }})"
                        class="btn btn-soft-danger btn-sm waves-effect" title="Reçu PDF">
                    <i class="ri-file-pdf-line"></i>
                </button>
                @if($pay->statut === 'en_attente')
                <button class="btn btn-soft-success waves-effect"
                        wire:click="ouvrirValidation({{ $pay->id }})" title="Valider">
                  <i class="ri-checkbox-circle-line"></i>
                </button>
                @endif
                @if(! in_array($pay->statut, ['annule']))
                <button class="btn btn-soft-warning waves-effect"
                        wire:click="ouvrirAnnulation({{ $pay->id }})" title="Annuler">
                  <i class="ri-close-circle-line"></i>
                </button>
                @endif
              </div>
            </td>
          </tr>

          @empty
          <tr>
            <td colspan="8">
              <div class="pay-empty">
                <i class="ri-bank-card-line"></i>
                <p>Aucun paiement trouvé</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="pay-pagination">
      <span class="pay-pag-info">
        {{ $paiements->firstItem() ?? 0 }}–{{ $paiements->lastItem() ?? 0 }} sur {{ $paiements->total() }} paiement(s)
      </span>
      <div>{{ $paiements->links('livewire::bootstrap') }}</div>
    </div>
  </div>

  {{-- ══ GRAPHS ════════════════════════════════════════════ --}}
  <div class="pay-graphs-grid fu fu-5">
    <div class="pay-graph-card">
      <div class="pgc-header">
        <div>
          <p class="pgc-title">Évolution mensuelle des paiements</p>
          <p class="pgc-sub">Montants collectés (validés) — {{ now()->year }}</p>
        </div>
        <span class="pgc-badge">{{ now()->year }}</span>
      </div>
      <canvas id="chartEvolution" height="160"></canvas>
    </div>
    <div class="pay-graph-card">
      <div class="pgc-header">
        <div>
          <p class="pgc-title">Répartition par mode</p>
          <p class="pgc-sub">Nombre de paiements par mode</p>
        </div>
        <span class="pgc-badge" style="background:rgba(64,81,137,.08);color:#405189">Donut</span>
      </div>
      <div style="display:flex;align-items:center;gap:20px;margin-top:8px">
        <div style="flex-shrink:0;width:160px"><canvas id="chartModes"></canvas></div>
        <div id="chartModesLegend" style="flex:1"></div>
      </div>
    </div>
  </div>

</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL PAIEMENT
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailPaiement" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:620px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;">

      @if($detailPaiement)
      @php
        $dp = $detailPaiement;
        [$dpGrad, $dpIcon] = match($dp->statut) {
            'success'    => ['linear-gradient(130deg,#0a7a6a,#0ab39c)', 'ri-checkbox-circle-line'],
            'en_attente' => ['linear-gradient(130deg,#a07c10,#d4a843)', 'ri-time-line'],
            'echec'      => ['linear-gradient(130deg,#c43520,#f06548)', 'ri-close-circle-line'],
            'refund'     => ['linear-gradient(130deg,#1a6080,#299cdb)', 'ri-refund-2-line'],
            default      => ['linear-gradient(130deg,#2d3a63,#405189)', 'ri-bank-card-line'],
        };
        $pillData = match($dp->statut) {
            'success'    => ['pp-success','ri-checkbox-circle-line','Validé'],
            'en_attente' => ['pp-pending','ri-time-line','En attente'],
            'echec'      => ['pp-failed','ri-close-circle-line','Échoué'],
            'refund'     => ['pp-refund','ri-refund-2-line','Remboursé'],
            default      => ['pp-pending','ri-question-line',$dp->statut],
        };
        $modeData = match($dp->mode_paiement) {
            'mobile_money' => ['pm-mm','ri-smartphone-line','Mobile Money'],
            'espece'       => ['pm-esp','ri-money-dollar-circle-line','Espèces'],
            'virement'     => ['pm-vir','ri-bank-line','Virement'],
            default        => ['pm-nd','ri-question-line','—'],
        };
        $periode = $dp->cotisation?->mois && $dp->cotisation?->annee
            ? \Carbon\Carbon::create()->month($dp->cotisation->mois)->translatedFormat('F') . ' ' . $dp->cotisation->annee
            : 'Ponctuel';
        $ref = $dp->reference ?? 'PAY-' . str_pad($dp->id, 5, '0', STR_PAD_LEFT);
      @endphp

      {{-- Header --}}
      <div class="pay-modal-header" style="background:{{ $dpGrad }}">
        <button class="pmh-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
        <div class="pmh-inner">
          <div class="pmh-icon"><i class="{{ $dpIcon }}"></i></div>
          <div>
            <div class="pmh-name">{{ $dp->customer?->prenom }} {{ $dp->customer?->nom }}</div>
            <div class="pmh-meta">
              <span><i class="ri-hashtag"></i>{{ $ref }}</span>
              <span><i class="ri-calendar-line"></i>{{ $dp->date_paiement->format('d M Y H:i') }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Stats overlap --}}
      <div class="pay-modal-stats">
        <div class="pay-ms-box">
          <div class="pmsb-v">{{ number_format($dp->montant, 0, ',', ' ') }} FCFA</div>
          <div class="pmsb-l">Montant</div>
        </div>
        <div class="pay-ms-box">
          <div class="pmsb-v">
            <span class="pay-pill {{ $pillData[0] }}" style="font-size:11px">
              <i class="{{ $pillData[1] }}"></i>{{ $pillData[2] }}
            </span>
          </div>
          <div class="pmsb-l">Statut</div>
        </div>
        <div class="pay-ms-box">
          <div class="pmsb-v">
            <span class="pay-mode {{ $modeData[0] }}" style="font-size:11px">
              <i class="{{ $modeData[1] }}"></i>{{ $modeData[2] }}
            </span>
          </div>
          <div class="pmsb-l">Mode</div>
        </div>
      </div>

      <div style="overflow-y:auto;max-height:calc(90vh - 240px);">
        <div class="pay-modal-body">

          <div class="pay-section-title">Détails du paiement</div>
          <div class="pay-detail-grid">
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-user-line me-1"></i>Fidèle</div>
              <div class="pdi-v">{{ $dp->customer?->prenom }} {{ $dp->customer?->nom }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-phone-line me-1"></i>Téléphone</div>
              <div class="pdi-v">{{ $dp->customer?->dial_code }} {{ $dp->customer?->phone }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-hashtag me-1"></i>Référence</div>
              <div class="pdi-v" style="font-family:var(--pay-mono);color:var(--pay-primary)">{{ $ref }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-calendar-line me-1"></i>Date paiement</div>
              <div class="pdi-v">{{ $dp->date_paiement->format('d M Y à H:i') }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-link-m me-1"></i>Cotisation liée</div>
              <div class="pdi-v">{{ $dp->cotisation?->typeCotisation?->libelle ?? '—' }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-calendar-check-line me-1"></i>Période</div>
              <div class="pdi-v">{{ $periode }}</div>
            </div>
            @if($dp->validated_by || $dp->validated_at)
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-shield-check-line me-1"></i>Validé par</div>
              <div class="pdi-v">Admin #{{ $dp->validated_by }}</div>
            </div>
            <div class="pay-detail-item">
              <div class="pdi-l"><i class="ri-time-line me-1"></i>Validé le</div>
              <div class="pdi-v">{{ $dp->validated_at?->format('d M Y H:i') ?? '—' }}</div>
            </div>
            @endif
          </div>

          {{-- Actions --}}
          <div class="d-flex gap-2 mt-3 flex-wrap">
            @if($dp->statut === 'en_attente')
            <button class="btn btn-success waves-effect"
                    wire:click="ouvrirValidation({{ $dp->id }})">
              <i class="ri-shield-check-line me-1"></i>Valider ce paiement
            </button>
            @endif
            @if(! in_array($dp->statut, ['annule']))
            <button class="btn btn-soft-warning waves-effect"
                    wire:click="ouvrirAnnulation({{ $dp->id }})">
              <i class="ri-close-circle-line me-1"></i>Annuler
            </button>
            @endif
          </div>

        </div>
      </div>

      <div class="pay-modal-footer">
        <button class="btn-pay-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Fermer
        </button>
      </div>

      @endif
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL VALIDATION MULTI-COTISATIONS
     Affiché quand un paiement couvre plusieurs cotisations.
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalValidationMulti" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:580px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">

      <div style="background:linear-gradient(135deg,#089383,#0ab39c);padding:20px 24px">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:42px;height:42px;border-radius:10px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff">
            <i class="ri-shield-check-line"></i>
          </div>
          <div>
            <div style="font-size:15px;font-weight:800;color:#fff">Validation du paiement</div>
            <div style="font-size:11px;color:rgba(255,255,255,.75)">Ce paiement couvre plusieurs cotisations</div>
          </div>
        </div>
      </div>

      @if($validationPaiement)
      <div style="padding:20px 24px">

        {{-- Récap paiement --}}
        <div style="background:rgba(10,179,156,.06);border:1px solid rgba(10,179,156,.2);border-radius:12px;padding:14px 16px;margin-bottom:18px">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-size:13px;font-weight:700;color:#212529">{{ $validationPaiement->customer?->prenom }} {{ $validationPaiement->customer?->nom }}</div>
              <div style="font-size:11px;color:#878a99">{{ $validationPaiement->reference ?? 'PAY-' . str_pad($validationPaiement->id,5,'0',STR_PAD_LEFT) }}</div>
            </div>
            <div style="font-size:20px;font-weight:900;color:#0ab39c">{{ number_format($validationPaiement->montant, 0, ',', ' ') }} FCFA</div>
          </div>
        </div>

        {{-- Liste des cotisations --}}
        <div style="font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
          {{ count($validationCotisations) }} cotisations qui seront validées
        </div>

        <div style="display:flex;flex-direction:column;gap:6px;max-height:240px;overflow-y:auto;margin-bottom:18px">
          @foreach($validationCotisations as $vc)
          <div style="display:flex;align-items:center;justify-content:space-between;background:#f8f9fa;border-radius:10px;padding:10px 14px">
            <div style="display:flex;align-items:center;gap:8px">
              <div style="width:26px;height:26px;border-radius:7px;background:rgba(10,179,156,.12);color:#0ab39c;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0">
                <i class="ri-calendar-check-line"></i>
              </div>
              <div style="font-size:12px;font-weight:600;color:#212529">{{ $vc['label'] }}</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <span style="font-size:12px;font-weight:700;color:#0ab39c">{{ $vc['montant'] }} FCFA</span>
              <span style="font-size:10px;font-weight:700;color:{{ $vc['statut'] === 'a_jour' ? '#0ab39c' : '#f7b84b' }}">
                {{ $vc['statut'] === 'a_jour' ? '✓ Déjà validée' : '→ À jour' }}
              </span>
            </div>
          </div>
          @endforeach
        </div>

        <div style="background:rgba(64,81,137,.05);border-radius:10px;padding:12px 14px;margin-bottom:18px">
          <div style="font-size:12px;color:#495057;line-height:1.6">
            <i class="ri-information-line me-1" style="color:#405189"></i>
            En confirmant, toutes ces cotisations passeront au statut <strong>À jour</strong>
            et une transaction d'entrée sera créée.
          </div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button class="btn btn-light" style="border-radius:9px;font-weight:700"
                  wire:click="$set('validationPaiementId', null)"
                  data-bs-dismiss="modal">
            <i class="ri-close-line me-1"></i>Annuler
          </button>
          <button wire:click="confirmerValidationMulti"
                  wire:loading.attr="disabled"
                  style="background:linear-gradient(135deg,#089383,#0ab39c);border:none;border-radius:9px;color:#fff;font-size:13px;font-weight:700;padding:10px 22px;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
            <span wire:loading wire:target="confirmerValidationMulti" class="spinner-border spinner-border-sm"></span>
            <i class="ri-shield-check-line" wire:loading.remove wire:target="confirmerValidationMulti"></i>
            <span wire:loading.remove wire:target="confirmerValidationMulti">Confirmer la validation</span>
            <span wire:loading wire:target="confirmerValidationMulti">Traitement…</span>
          </button>
        </div>

      </div>
      @endif

    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL ANNULATION PAIEMENT
     Code administrateur requis + motif obligatoire.
     Si paiement était success → crée DemandeRemboursement.
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalAnnulation" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:520px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">

      <div style="background:linear-gradient(135deg,#c0341a,#f06548);padding:20px 24px">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:42px;height:42px;border-radius:10px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff">
            <i class="ri-close-circle-line"></i>
          </div>
          <div>
            <div style="font-size:15px;font-weight:800;color:#fff">Annuler ce paiement</div>
            <div style="font-size:11px;color:rgba(255,255,255,.75)">Action irréversible — code administrateur requis</div>
          </div>
        </div>
      </div>

      @if($annulationPaiement)
      <div style="padding:20px 24px">

        {{-- Récap paiement --}}
        <div style="background:rgba(240,101,72,.06);border:1px solid rgba(240,101,72,.2);border-radius:12px;padding:14px 16px;margin-bottom:18px">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-size:13px;font-weight:700;color:#212529">{{ $annulationPaiement->customer?->prenom }} {{ $annulationPaiement->customer?->nom }}</div>
              <div style="font-size:11px;color:#878a99">{{ $annulationPaiement->reference ?? 'PAY-' . str_pad($annulationPaiement->id,5,'0',STR_PAD_LEFT) }}</div>
            </div>
            <div style="text-align:right">
              <div style="font-size:20px;font-weight:900;color:#f06548">{{ number_format($annulationPaiement->montant, 0, ',', ' ') }} FCFA</div>
              <div style="font-size:10px;color:#878a99">Statut actuel : {{ match($annulationPaiement->statut){ 'success'=>'Validé','en_attente'=>'En attente','echec'=>'Échoué',default=>$annulationPaiement->statut } }}</div>
            </div>
          </div>
        </div>

        @if($annulationPaiement->statut === 'success')
        <div style="background:rgba(247,184,75,.08);border:1.5px solid #f7b84b;border-left:4px solid #f7b84b;border-radius:0 10px 10px 0;padding:12px 14px;margin-bottom:16px">
          <div style="font-size:12px;font-weight:700;color:#c07a10;margin-bottom:3px">
            <i class="ri-alert-line me-1"></i>Ce paiement a déjà été validé
          </div>
          <div style="font-size:11px;color:#495057;line-height:1.6">
            L'annulation créera automatiquement une <strong>demande de remboursement</strong>
            en attente de validation. Les cotisations liées repaseront en <strong>En retard</strong>.
          </div>
        </div>
        @else
        <div style="background:rgba(240,101,72,.05);border-radius:10px;padding:12px 14px;margin-bottom:16px">
          <div style="font-size:11px;color:#c44a2e;line-height:1.6">
            <i class="ri-information-line me-1"></i>
            Les cotisations liées à ce paiement repasseront en <strong>En retard</strong>.
          </div>
        </div>
        @endif

        {{-- Motif --}}
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
            Motif d'annulation <span style="color:#f06548">*</span>
          </label>
          <textarea wire:model="annulationMotif"
                    rows="3"
                    placeholder="Expliquez la raison de l'annulation…"
                    style="width:100%;border:1.5px solid {{ $errorAnnulationMotif ? '#f06548' : '#e9ebec' }};border-radius:10px;padding:10px 12px;font-size:13px;resize:none;font-family:inherit;color:#212529"></textarea>
          @if($errorAnnulationMotif)
          <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">{{ $errorAnnulationMotif }}</div>
          @endif
        </div>

        {{-- Code admin --}}
        <div style="margin-bottom:18px">
          <label style="display:block;font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
            Code administrateur <span style="color:#f06548">*</span>
          </label>
          <div style="position:relative">
            <i class="ri-lock-password-line" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#878a99;font-size:15px;pointer-events:none"></i>
            <input type="password"
                   wire:model="annulationCode"
                   placeholder="••••••"
                   autocomplete="off"
                   style="width:100%;border:1.5px solid {{ $errorAnnulationCode ? '#f06548' : '#e9ebec' }};border-radius:10px;height:44px;padding:0 14px 0 38px;font-size:15px;letter-spacing:4px;font-family:inherit;color:#212529"/>
          </div>
          @if($errorAnnulationCode)
          <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600">
            <i class="ri-error-warning-line me-1"></i>{{ $errorAnnulationCode }}
          </div>
          @endif
          <div style="font-size:10px;color:#878a99;margin-top:4px">
            Saisissez votre code PIN d'administrateur pour confirmer.
          </div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
          <button class="btn btn-light" style="border-radius:9px;font-weight:700"
                  wire:click="fermerAnnulation">
            <i class="ri-close-line me-1"></i>Annuler
          </button>
          <button wire:click="confirmerAnnulation"
                  wire:loading.attr="disabled"
                  style="background:linear-gradient(135deg,#c0341a,#f06548);border:none;border-radius:9px;color:#fff;font-size:13px;font-weight:700;padding:10px 22px;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
            <span wire:loading wire:target="confirmerAnnulation" class="spinner-border spinner-border-sm"></span>
            <i class="ri-close-circle-line" wire:loading.remove wire:target="confirmerAnnulation"></i>
            <span wire:loading.remove wire:target="confirmerAnnulation">Confirmer l'annulation</span>
            <span wire:loading wire:target="confirmerAnnulation">Traitement…</span>
          </button>
        </div>

      </div>
      @endif

    </div>
  </div>
</div>


{{-- ══ PATCH : NOTE COTISATION BO ══════════════════════════
  Dans le composant cotisation BO (index.php), dans
  confirmerValidation(), ajouter AVANT le SweetAlert :

  // Vérifier si le paiement est partagé entre plusieurs cotisations
  $paiementId = $cot->paiement_id;
  if ($paiementId) {
      $nbCotisationsLiees = Cotisation::where('paiement_id', $paiementId)->count();
      if ($nbCotisationsLiees > 1) {
          $this->send_event_at_sweet_alert_not_timer(
              'Paiement groupé',
              "Ce paiement est lié à {$nbCotisationsLiees} cotisations. "
              . "Validez directement le paiement depuis le module Paiements pour valider toutes les cotisations en une seule action.",
              'info'
          );
          return;
      }
  }
══════════════════════════════════════════════════════════ --}}
</div>


@push('styles')
<link href="{{ asset('assets/css/paiements.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const GRAPH_DATA = @json($graphData);

(function() {
  const ctx = document.getElementById('chartEvolution');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: GRAPH_DATA.mois_labels,
      datasets: [{
        data: GRAPH_DATA.mois_montants,
        backgroundColor: 'rgba(64,81,137,.15)',
        borderColor: '#405189', borderWidth: 2,
        borderRadius: 6, borderSkipped: false,
      }, {
        data: GRAPH_DATA.mois_counts, type: 'line',
        borderColor: '#0ab39c', backgroundColor: 'rgba(10,179,156,.08)',
        borderWidth: 2, pointBackgroundColor: '#0ab39c', pointRadius: 4,
        yAxisID: 'y2', tension: 0.4, fill: true,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y:  { ticks: { font:{size:10}, callback: v => v >= 1000 ? (v/1000)+'k' : v }, grid: { color:'rgba(0,0,0,.04)' } },
        y2: { position:'right', ticks: { font:{size:10} }, grid: { display:false } },
        x:  { ticks: { font:{size:10} }, grid: { display:false } },
      }
    }
  });
})();

(function() {
  const ctx = document.getElementById('chartModes');
  if (!ctx) return;
  const colors = ['#0ab39c','#f7b84b','#405189'];
  const labels = GRAPH_DATA.modes_labels;
  const vals   = GRAPH_DATA.modes_vals;
  new Chart(ctx, {
    type: 'doughnut',
    data: { labels, datasets: [{ data: vals, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
    options: { responsive: true, cutout: '72%', plugins: { legend: { display: false } } }
  });
  const legend = document.getElementById('chartModesLegend');
  if (legend) {
    legend.innerHTML = labels.map((l, i) => `
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
        <span style="width:12px;height:12px;border-radius:3px;background:${colors[i]};flex-shrink:0"></span>
        <div>
          <div style="font-size:12px;font-weight:700;color:#212529">${l}</div>
          <div style="font-size:11px;color:#878a99">${vals[i]} paiements</div>
        </div>
      </div>`).join('');
  }
})();

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
    .feedback-text{ width:100%; margin-top:.25rem; font-size:.875em; color:#f06548; }
</style>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush