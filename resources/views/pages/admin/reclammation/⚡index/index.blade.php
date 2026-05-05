<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ HEADER ════════════════════════════════════════════ --}}
  <div class="rc-page-header fu fu-1">
    <div>
      <h4>Réclamations</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Réclamations</li>
        </ol>
      </nav>
    </div>
  </div>

  {{-- ══ KPI STRIP ══════════════════════════════════════════ --}}
  <div class="rc-kpi-strip">
    <div class="rc-kpi rk-total fu fu-1">
      <div class="rki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-flag-2-line"></i></div>
      <div>
        <div class="rki-label">Total</div>
        <div class="rki-val">{{ $kpis['total'] }}</div>
        <div class="rki-sub">Toutes réclamations</div>
      </div>
    </div>
    <div class="rc-kpi rk-ouverte fu fu-2">
      <div class="rki-icon" style="background:rgba(41,156,219,.12);color:#299cdb"><i class="ri-time-line"></i></div>
      <div>
        <div class="rki-label">En cours</div>
        <div class="rki-val">{{ $kpis['ouverte'] }}</div>
        <div class="rki-sub">À traiter</div>
      </div>
    </div>
    <div class="rc-kpi rk-resolu fu fu-3">
      <div class="rki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-check-double-line"></i></div>
      <div>
        <div class="rki-label">Résolues</div>
        <div class="rki-val">{{ $kpis['resolu'] }}</div>
        <div class="rki-sub">Traitées</div>
      </div>
    </div>
    <div class="rc-kpi rk-rejete fu fu-4">
      <div class="rki-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
      <div>
        <div class="rki-label">Rejetées</div>
        <div class="rki-val">{{ $kpis['rejete'] }}</div>
        <div class="rki-sub">Non retenues</div>
      </div>
    </div>
  </div>

  {{-- ══ TOOLBAR ════════════════════════════════════════════ --}}
  <div class="rc-toolbar fu fu-2">
    <div class="sw">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.400ms="search"
             placeholder="Rechercher par fidèle ou sujet…">
    </div>
    <select class="rc-sel" wire:model.live="filterStatut">
      <option value="tous">Tous les statuts</option>
      <option value="ouverte">Ouvertes</option>
      <option value="en_cours">En cours</option>
      <option value="resolu">Résolues</option>
      <option value="rejete">Rejetées</option>
    </select>
  </div>

  {{-- ══ TABLE ══════════════════════════════════════════════ --}}
  <div class="rc-table-card fu fu-3" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="rc-table">
        <thead>
          <tr>
            <th>Fidèle</th>
            <th>Sujet</th>
            <th>Cotisation liée</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Chargé</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reclammations as $r)
          @php
            [$pillClass, $pillLabel] = match($r->status) {
                'ouverte'  => ['rp-ouverte',  'Ouverte'],
                'en_cours' => ['rp-encours',  'En cours'],
                'resolu'   => ['rp-resolu',   'Résolu'],
                'rejete'   => ['rp-rejete',   'Rejeté'],
                default    => ['rp-ouverte',  $r->status],
            };
            $cotLabel = $r->cotisation
                ? ($r->cotisation->typeCotisation?->libelle ?? '—')
                  . ($r->cotisation->mois && $r->cotisation->annee
                      ? ' · ' . \Carbon\Carbon::create($r->cotisation->annee, $r->cotisation->mois)->translatedFormat('M Y')
                      : '')
                : '—';
          @endphp
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="rc-avatar">
                  {{ strtoupper(substr($r->customer?->prenom ?? '?', 0, 1) . substr($r->customer?->nom ?? '?', 0, 1)) }}
                </div>
                <div>
                  <div style="font-size:13px;font-weight:700;color:#212529">
                    {{ $r->customer ? $r->customer->prenom.' '.$r->customer->nom : '—' }}
                  </div>
                  <div style="font-size:11px;color:var(--rc-muted)">
                    {{ $r->customer?->dial_code }} {{ $r->customer?->phone }}
                  </div>
                </div>
              </div>
            </td>
            <td>
              <div style="font-size:13px;font-weight:600;color:#212529;max-width:200px">
                {{ \Str::limit($r->sujet, 45) }}
              </div>
              <div style="font-size:11px;color:var(--rc-muted);margin-top:2px">
                {{ \Str::limit($r->description, 60) }}
              </div>
            </td>
            <td>
              <span style="font-size:12px;color:var(--rc-muted)">{{ $cotLabel }}</span>
            </td>
            <td>
              <span style="font-size:12px;color:var(--rc-muted)">{{ $r->created_at->format('d/m/Y') }}</span>
              <div style="font-size:10px;color:var(--rc-muted)">{{ $r->created_at->diffForHumans() }}</div>
            </td>
            <td>
              <span class="rc-pill {{ $pillClass }}">{{ $pillLabel }}</span>
            </td>
            <td>
              <span style="font-size:12px;color:var(--rc-muted)">
                {{ $r->userCharged ? $r->userCharged->name : '—' }}
              </span>
            </td>
            <td>
              <div class="rc-actions">
                @if(auth()->user()?->hasPermission('RECLAMATION_SHOW_ONE'))
                <button class="btn btn-soft-primary waves-effect"
                        wire:click="openDetail({{ $r->id }})" title="Voir / Répondre">
                  <i class="ri-eye-line"></i>
                </button>
                @endif

                @if(in_array($r->status, ['ouverte', 'en_cours']) && auth()->user()?->hasPermission('RECLAMATION_CLOSE'))
                @if(in_array($r->status, ['ouverte', 'en_cours']))
                <button class="btn btn-soft-success waves-effect"
                        wire:click="changerStatut({{ $r->id }}, 'en_cours')" title="Marquer En cours">
                  <i class="ri-time-line"></i>
                </button>
                @endif
                @endif

                @if(auth()->user()?->hasPermission('RECLAMATION_DELETE'))
                <button class="btn btn-soft-danger waves-effect"
                        wire:click="confirmDelete({{ $r->id }})" title="Supprimer">
                  <i class="ri-delete-bin-line"></i>
                </button>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7">
              <div class="rc-empty">
                <i class="ri-flag-2-line"></i>
                <p>Aucune réclamation trouvée</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="rc-pagination">
      <span class="rc-pag-info">
        {{ $reclammations->firstItem() ?? 0 }}–{{ $reclammations->lastItem() ?? 0 }} sur {{ $reclammations->total() }}
      </span>
      <div>{{ $reclammations->links('livewire::bootstrap') }}</div>
    </div>
  </div>

</div>
</div>


{{-- ══ MODAL DÉTAIL / RÉPONSE ═════════════════════════════════ --}}
<div class="modal fade" id="modalDetailRecla" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:600px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;display:flex;flex-direction:column;max-height:90vh">

      {{-- Header --}}
      <div class="rc-modal-header" style="flex-shrink:0">
        <div class="rcmh-left">
          <div class="rcmh-icon"><i class="ri-flag-line"></i></div>
          <div>
            <p class="rcmh-title">Détail de la réclamation</p>
            <p class="rcmh-sub">Visualisez et répondez à la réclamation</p>
          </div>
        </div>
        <button class="rc-modal-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      {{-- Scrollable body --}}
      <div style="overflow-y:auto;flex:1;padding:20px 24px 0">

        @if($detailRecla)
        @php
          $dr = $detailRecla;
          [$pillClass, $pillLabel] = match($dr->status) {
              'ouverte'  => ['rp-ouverte', 'Ouverte'],
              'en_cours' => ['rp-encours', 'En cours'],
              'resolu'   => ['rp-resolu',  'Résolu'],
              'rejete'   => ['rp-rejete',  'Rejeté'],
              default    => ['rp-ouverte', $dr->status],
          };
          $cotLabel = $dr->cotisation
              ? ($dr->cotisation->typeCotisation?->libelle ?? '—')
                . ($dr->cotisation->mois && $dr->cotisation->annee
                    ? ' — ' . \Carbon\Carbon::create($dr->cotisation->annee, $dr->cotisation->mois)->translatedFormat('F Y')
                    : '')
              : null;
        @endphp

        {{-- Info fidèle --}}
        <div class="rc-detail-fidele">
          <div class="rc-avatar rc-avatar-lg">
            {{ strtoupper(substr($dr->customer?->prenom ?? '?', 0, 1) . substr($dr->customer?->nom ?? '?', 0, 1)) }}
          </div>
          <div>
            <div class="rc-detail-name">{{ $dr->customer ? $dr->customer->prenom.' '.$dr->customer->nom : '—' }}</div>
            <div class="rc-detail-phone">{{ $dr->customer?->dial_code }} {{ $dr->customer?->phone }}</div>
          </div>
          <span class="rc-pill {{ $pillClass }}" style="margin-left:auto">{{ $pillLabel }}</span>
        </div>

        {{-- Sujet + cotisation --}}
        <div class="rc-detail-section">
          <div class="rc-detail-label">Sujet</div>
          <div class="rc-detail-val">{{ $dr->sujet }}</div>
        </div>

        @if($cotLabel)
        <div class="rc-detail-section">
          <div class="rc-detail-label"><i class="ri-calendar-line"></i> Cotisation liée</div>
          <div class="rc-detail-val">{{ $cotLabel }}</div>
        </div>
        @endif

        <div class="rc-detail-section">
          <div class="rc-detail-label"><i class="ri-calendar-line"></i> Soumise le</div>
          <div class="rc-detail-val">{{ $dr->created_at->format('d/m/Y à H:i') }}</div>
        </div>

        {{-- Message fidèle --}}
        <div class="rc-detail-section">
          <div class="rc-detail-label"><i class="ri-message-3-line"></i> Message du fidèle</div>
          <div class="rc-detail-msg">{{ $dr->description }}</div>
        </div>

        {{-- Historique --}}
        @if($dr->historiqueReclammation->count() > 1)
        <div class="rc-detail-section">
          <div class="rc-detail-label"><i class="ri-history-line"></i> Historique</div>
          @foreach($dr->historiqueReclammation->skip(1) as $h)
          <div class="rc-histo-item">
            <div class="rc-histo-dot"></div>
            <div class="rc-histo-body">
              <div class="rc-histo-text">{{ $h->description }}</div>
              <div class="rc-histo-meta">{{ $h->created_at->format('d/m/Y H:i') }} · {{ match($h->status){ 'resolu'=>'Résolu','rejete'=>'Rejeté','en_cours'=>'En cours',default=>'Ouvert' } }}</div>
            </div>
          </div>
          @endforeach
        </div>
        @endif

        {{-- Réponse admin --}}
        <div class="rc-detail-section">
          <div class="rc-detail-label"><i class="ri-reply-line"></i> Votre réponse</div>

          @if($errorReponse)
          <div style="background:rgba(240,101,72,.06);border-left:3px solid #f06548;border-radius:0 8px 8px 0;padding:8px 12px;margin-bottom:12px;font-size:12px;color:#c44a2e;">
            {{ $errorReponse }}
          </div>
          @endif

          <textarea class="rc-input-textarea"
                    wire:model="reponse"
                    placeholder="Rédigez votre réponse au fidèle…"
                    rows="4"></textarea>

          <div style="margin-top:10px">
            <label class="rc-input-label">Nouveau statut</label>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px">
              <button type="button"
                      class="rc-statut-btn {{ $newStatut === 'en_cours' ? 'selected-encours' : '' }}"
                      wire:click="$set('newStatut','en_cours')">
                <i class="ri-time-line" style="color:#299cdb"></i> En cours
              </button>
              <button type="button"
                      class="rc-statut-btn {{ $newStatut === 'resolu' ? 'selected-resolu' : '' }}"
                      wire:click="$set('newStatut','resolu')">
                <i class="ri-check-double-line" style="color:#0ab39c"></i> Résoudre
              </button>
              <button type="button"
                      class="rc-statut-btn {{ $newStatut === 'rejete' ? 'selected-rejete' : '' }}"
                      wire:click="$set('newStatut','rejete')">
                <i class="ri-close-circle-line" style="color:#f06548"></i> Rejeter
              </button>
            </div>
          </div>
        </div>

        @endif

      </div>{{-- /scroll --}}

      {{-- Footer fixe --}}
      <div class="rc-modal-footer" style="flex-shrink:0">
        <button class="btn-rc-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Fermer
        </button>

        @if(auth()->user()?->hasPermission('RECLAMATION_EDIT'))
        <button class="btn-rc-primary" wire:click="repondre" wire:loading.attr="disabled">
          <span wire:loading wire:target="repondre" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-send-plane-line" wire:loading.remove wire:target="repondre"></i>
          <span wire:loading.remove wire:target="repondre"> Envoyer la réponse</span>
          <span wire:loading wire:target="repondre">Envoi…</span>
        </button>
        @endif
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/reclammation.css') }}" rel="stylesheet" type="text/css" />
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
  }).then(r => { if (r.isConfirmed) Livewire.dispatch(data.eventRetour, { id: data.id }); });
});
Livewire.on('modalShowmessageToast', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
      .fire({ icon: data.type, title: data.title });
});
</script>
@endpush


@push('styles')
<style>
  .feedback-text{ width:100%; margin-top:.25rem; font-size:.875em; color:#f06548; }
</style>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush
