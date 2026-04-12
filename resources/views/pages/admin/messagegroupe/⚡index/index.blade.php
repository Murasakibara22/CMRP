<div>
<div class="page-content">
<div class="container-fluid">

  {{-- ══ HEADER ════════════════════════════════════════════ --}}
  <div class="mg-page-header fu fu-1">
    <div>
      <h4>Messages groupés</h4>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Messages groupés</li>
        </ol>
      </nav>
    </div>
    <button class="btn-mg-primary" wire:click="openAdd">
      <i class="ri-send-plane-line"></i> Nouveau message
    </button>
  </div>

  {{-- ══ KPI STRIP ══════════════════════════════════════════ --}}
  <div class="mg-kpi-strip">
    <div class="mg-kpi mk-total fu fu-1">
      <div class="mki-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-message-3-line"></i></div>
      <div>
        <div class="mki-label">Total</div>
        <div class="mki-val">{{ $kpis['total'] }}</div>
        <div class="mki-sub">Tous les envois</div>
      </div>
    </div>
    <div class="mg-kpi mk-planifie fu fu-2">
      <div class="mki-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-time-line"></i></div>
      <div>
        <div class="mki-label">Planifiés</div>
        <div class="mki-val">{{ $kpis['planifie'] }}</div>
        <div class="mki-sub">À envoyer</div>
      </div>
    </div>
    <div class="mg-kpi mk-envoye fu fu-3">
      <div class="mki-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-checkbox-circle-line"></i></div>
      <div>
        <div class="mki-label">Envoyés</div>
        <div class="mki-val">{{ $kpis['envoye'] }}</div>
        <div class="mki-sub">Avec succès</div>
      </div>
    </div>
    <div class="mg-kpi mk-echec fu fu-4">
      <div class="mki-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-close-circle-line"></i></div>
      <div>
        <div class="mki-label">Échecs</div>
        <div class="mki-val">{{ $kpis['echec'] }}</div>
        <div class="mki-sub">Erreurs d'envoi</div>
      </div>
    </div>
  </div>

  {{-- ══ TOOLBAR ════════════════════════════════════════════ --}}
  <div class="mg-toolbar fu fu-2">
    <div class="sw">
      <i class="ri-search-line"></i>
      <input type="text" wire:model.live.debounce.400ms="search" placeholder="Rechercher un message…">
    </div>
    <select class="mg-sel" wire:model.live="filterCanal">
      <option value="tous">Tous canaux</option>
      <option value="sms">SMS</option>
      <option value="email">Email</option>
    </select>
    <select class="mg-sel" wire:model.live="filterStatut">
      <option value="tous">Tous statuts</option>
      <option value="planifie">Planifié</option>
      <option value="en_cours">En cours</option>
      <option value="envoye">Envoyé</option>
      <option value="echec">Échec</option>
    </select>
  </div>

  {{-- ══ TABLE ══════════════════════════════════════════════ --}}
  <div class="mg-table-card fu fu-3" wire:loading.class="opacity-50">
    <div class="table-responsive">
      <table class="mg-table">
        <thead>
          <tr>
            <th>Titre</th>
            <th>Canal</th>
            <th>Destinataires</th>
            <th>Envoi prévu</th>
            <th>Statut</th>
            <th>Stats</th>
            <th>Créé par</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($messages as $m)
          @php
            [$pillClass, $pillLabel] = match($m->statut) {
                'planifie' => ['mp-planifie', 'Planifié'],
                'en_cours' => ['mp-encours',  'En cours'],
                'envoye'   => ['mp-envoye',   'Envoyé'],
                'echec'    => ['mp-echec',    'Échec'],
                default    => ['mp-planifie', $m->statut],
            };
            $canalIcon = $m->canal === 'sms' ? 'ri-smartphone-line' : 'ri-mail-line';
            $canalColor = $m->canal === 'sms' ? '#405189' : '#0ab39c';
          @endphp
          <tr>
            <td>
              <div style="font-size:13px;font-weight:700;color:#212529;max-width:220px">
                {{ \Str::limit($m->titre, 40) }}
              </div>
              <div style="font-size:11px;color:var(--mg-muted);margin-top:2px">
                {{ \Str::limit($m->message, 55) }}
              </div>
            </td>
            <td>
              <span class="mg-canal-badge" style="color:{{ $canalColor }}">
                <i class="{{ $canalIcon }}"></i>
                {{ strtoupper($m->canal) }}
              </span>
            </td>
            <td>
              <span style="font-size:13px;font-weight:700;color:#405189">
                {{ $m->tous_les_customers ? 'Tous' : $m->nb_destinataires }}
              </span>
              <span style="font-size:10px;color:var(--mg-muted);margin-left:3px">fidèles</span>
            </td>
            <td>
              @if($m->envoyer_le)
              <span style="font-size:12px;color:var(--mg-muted)">{{ $m->envoyer_le->format('d/m/Y H:i') }}</span>
              @else
              <span style="font-size:12px;color:#0ab39c;font-weight:600">Immédiat</span>
              @endif
            </td>
            <td>
              <span class="mg-pill {{ $pillClass }}">{{ $pillLabel }}</span>
            </td>
            <td>
              <div style="font-size:11px;color:var(--mg-muted)">
                <span style="color:#0ab39c;font-weight:700">{{ $m->nb_envoyes }} ✓</span>
                @if($m->nb_echecs > 0)
                  <span style="color:#f06548;font-weight:700;margin-left:4px">{{ $m->nb_echecs }} ✗</span>
                @endif
              </div>
            </td>
            <td>
              <span style="font-size:12px;color:var(--mg-muted)">{{ $m->user?->name ?? '—' }}</span>
            </td>
            <td>
              <div class="mg-actions">
                <button class="btn btn-soft-primary waves-effect"
                        wire:click="openDetail({{ $m->id }})" title="Détails">
                  <i class="ri-eye-line"></i>
                </button>
                <button class="btn btn-soft-danger waves-effect"
                        wire:click="confirmDelete({{ $m->id }})" title="Supprimer">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8">
              <div class="mg-empty">
                <i class="ri-message-3-line"></i>
                <p>Aucun message groupé trouvé</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mg-pagination">
      <span class="mg-pag-info">
        {{ $messages->firstItem() ?? 0 }}–{{ $messages->lastItem() ?? 0 }} sur {{ $messages->total() }}
      </span>
      <div>{{ $messages->links('livewire::bootstrap') }}</div>
    </div>
  </div>

</div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL NOUVEAU MESSAGE
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalAddMessage" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:580px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;display:flex;flex-direction:column;max-height:92vh">

      {{-- Header --}}
      <div class="mg-modal-header" style="flex-shrink:0">
        <div class="mgmh-left">
          <div class="mgmh-icon"><i class="ri-send-plane-line"></i></div>
          <div>
            <p class="mgmh-title">Nouveau message groupé</p>
            <p class="mgmh-sub">SMS ou email envoyé aux fidèles sélectionnés</p>
          </div>
        </div>
        <button class="mg-modal-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      {{-- Scrollable --}}
      <div style="overflow-y:auto;flex:1;padding:22px 24px 0">

        {{-- Erreurs --}}
        @if($errorTitre || $errorMessage || $errorDest)
        <div style="background:rgba(240,101,72,.06);border:1px solid rgba(240,101,72,.25);border-left:3px solid #f06548;border-radius:0 10px 10px 0;padding:10px 14px;margin-bottom:16px;">
          @foreach(array_filter([$errorTitre, $errorMessage, $errorDest]) as $err)
            <div style="font-size:12px;color:#c44a2e;">• {{ $err }}</div>
          @endforeach
        </div>
        @endif

        {{-- Canal --}}
        <div class="mb-3">
          <label class="form-label-mg">Canal d'envoi <span class="req">*</span></label>
          <div style="display:flex;gap:10px">
            <button type="button"
                    class="mg-canal-btn {{ $canal === 'sms' ? 'selected' : '' }}"
                    wire:click="$set('canal','sms')">
              <i class="ri-smartphone-line" style="color:#405189"></i> SMS
            </button>
            <button type="button"
                    class="mg-canal-btn {{ $canal === 'email' ? 'selected' : '' }}"
                    wire:click="$set('canal','email')">
              <i class="ri-mail-line" style="color:#0ab39c"></i> Email
            </button>
          </div>
        </div>

        {{-- Destinataires --}}
        <div class="mb-3">
          <label class="form-label-mg">Destinataires <span class="req">*</span></label>

          {{-- Checkbox Tous --}}
          <div class="mg-tous-wrap">
            <label class="mg-tous-label">
              <input type="checkbox" wire:model.live="tousLesCustomers" class="mg-checkbox"/>
              <span>Envoyer à <strong>tous les fidèles actifs</strong></span>
            </label>
          </div>

          {{-- Select2 (masqué si "Tous") --}}
          @if(! $tousLesCustomers)
          <div class="mt-2" >
            <select id="select2-customers" multiple class="mg-select2-input" style="width:100%">
              @foreach($customers as $c)
                <option value="{{ $c->id }}">
                  {{ $c->prenom }} {{ $c->nom }} — {{ $c->dial_code }} {{ $c->phone }}
                </option>
              @endforeach
            </select>
            @if($errorDest)
            <div style="font-size:11px;color:#f06548;margin-top:4px;font-weight:600">{{ $errorDest }}</div>
            @endif
          </div>
          @endif
        </div>

        {{-- Titre --}}
        <div class="mb-3">
          <label class="form-label-mg">Titre du message <span class="req">*</span></label>
          <div class="input-mg-wrap">
            <i class="ri-text itw-icon"></i>
            <input type="text"
                   class="input-mg {{ $errorTitre ? 'is-err' : '' }}"
                   wire:model="titre"
                   placeholder="ex : Rappel cotisation Avril 2025">
          </div>
          @if($errorTitre)<div class="err-mg">{{ $errorTitre }}</div>@endif
        </div>

        {{-- Message --}}
        <div class="mb-3">
          <label class="form-label-mg">Message <span class="req">*</span></label>
          <textarea class="input-mg {{ $errorMessage ? 'is-err' : '' }}"
                    wire:model="message"
                    rows="5"
                    style="height:auto;padding:10px 14px;resize:vertical;min-height:100px"
                    placeholder="Rédigez votre message ici…"></textarea>
          @if($errorMessage)<div class="err-mg">{{ $errorMessage }}</div>@endif
          @if($canal === 'sms')
          <div style="font-size:11px;color:var(--mg-muted);margin-top:4px">
            <i class="ri-information-line"></i>
            {{ strlen($message) }}/160 caractères
            @if(strlen($message) > 160) ({{ ceil(strlen($message)/160) }} SMS) @endif
          </div>
          @endif
        </div>

        {{-- DateTime planification --}}
        <div class="mb-4">
          <label class="form-label-mg">Planifier l'envoi <span style="font-size:10px;color:var(--mg-muted);font-weight:500;text-transform:none">(optionnel — laisser vide pour envoi immédiat)</span></label>
          <div class="input-mg-wrap">
            <i class="ri-calendar-line itw-icon"></i>
            <input type="datetime-local"
                   class="input-mg"
                   wire:model="envoyerLe"
                   min="{{ now()->format('Y-m-d\TH:i') }}">
          </div>
        </div>

      </div>{{-- /scroll --}}

      {{-- Footer fixe --}}
      <div class="mg-modal-footer" style="flex-shrink:0">
        <button class="btn-mg-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Annuler
        </button>
        <button class="btn-mg-primary" wire:click="envoyer" wire:loading.attr="disabled">
          <span wire:loading wire:target="envoyer" class="spinner-border spinner-border-sm me-1"></span>
          <i class="ri-send-plane-line" wire:loading.remove wire:target="envoyer"></i>
          <span wire:loading.remove wire:target="envoyer">
            {{ $envoyerLe ? ' Planifier' : ' Envoyer maintenant' }}
          </span>
          <span wire:loading wire:target="envoyer">Envoi…</span>
        </button>
      </div>

    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL DÉTAIL MESSAGE
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailMessage" tabindex="-1" aria-hidden="true" wire:ignore.self>
  <div class="modal-dialog" style="max-width:560px">
    <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;display:flex;flex-direction:column;max-height:90vh">

      <div class="mg-modal-header" style="flex-shrink:0">
        <div class="mgmh-left">
          <div class="mgmh-icon"><i class="ri-eye-line"></i></div>
          <div>
            <p class="mgmh-title">Détail du message</p>
            <p class="mgmh-sub">Résumé et statistiques d'envoi</p>
          </div>
        </div>
        <button class="mg-modal-close" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
      </div>

      <div style="overflow-y:auto;flex:1;padding:20px 24px 0">

        @if($detailMessage)
        @php $dm = $detailMessage; @endphp

        <div class="mg-detail-stats">
          <div class="mg-ds-box">
            <div class="mg-ds-val" style="color:#405189">{{ $dm->nb_destinataires }}</div>
            <div class="mg-ds-label">Destinataires</div>
          </div>
          <div class="mg-ds-box">
            <div class="mg-ds-val" style="color:#0ab39c">{{ $dm->nb_envoyes }}</div>
            <div class="mg-ds-label">Envoyés</div>
          </div>
          <div class="mg-ds-box">
            <div class="mg-ds-val" style="color:#f06548">{{ $dm->nb_echecs }}</div>
            <div class="mg-ds-label">Échecs</div>
          </div>
        </div>

        <div class="mg-d-section">
          <div class="mg-d-label">Titre</div>
          <div class="mg-d-val">{{ $dm->titre }}</div>
        </div>

        <div class="mg-d-section">
          <div class="mg-d-label">Message</div>
          <div class="mg-d-msg">{{ $dm->message }}</div>
        </div>

        <div class="mg-d-grid">
          <div class="mg-d-item">
            <div class="mg-d-label">Canal</div>
            <div class="mg-d-val">
              <i class="{{ $dm->canal === 'sms' ? 'ri-smartphone-line' : 'ri-mail-line' }}"></i>
              {{ strtoupper($dm->canal) }}
            </div>
          </div>
          <div class="mg-d-item">
            <div class="mg-d-label">Statut</div>
            <div>
              @php
                [$pc, $pl] = match($dm->statut){ 'planifie'=>['mp-planifie','Planifié'],'en_cours'=>['mp-encours','En cours'],'envoye'=>['mp-envoye','Envoyé'],default=>['mp-echec','Échec'] };
              @endphp
              <span class="mg-pill {{ $pc }}">{{ $pl }}</span>
            </div>
          </div>
          <div class="mg-d-item">
            <div class="mg-d-label">Envoi prévu</div>
            <div class="mg-d-val">{{ $dm->envoyer_le ? $dm->envoyer_le->format('d/m/Y H:i') : 'Immédiat' }}</div>
          </div>
          <div class="mg-d-item">
            <div class="mg-d-label">Créé par</div>
            <div class="mg-d-val">{{ $dm->user?->name ?? '—' }}</div>
          </div>
          <div class="mg-d-item">
            <div class="mg-d-label">Tous les fidèles</div>
            <div class="mg-d-val">{{ $dm->tous_les_customers ? 'Oui' : 'Non' }}</div>
          </div>
          <div class="mg-d-item">
            <div class="mg-d-label">Créé le</div>
            <div class="mg-d-val">{{ $dm->created_at->format('d/m/Y H:i') }}</div>
          </div>
        </div>

        @if($dm->destinataires->count() > 0 && ! $dm->tous_les_customers)
        <div class="mg-d-section">
          <div class="mg-d-label">Destinataires ({{ $dm->destinataires->count() }})</div>
          <div class="mg-dest-list">
            @foreach($dm->destinataires->take(10) as $dest)
            <div class="mg-dest-item">
              <div class="rc-avatar" style="width:28px;height:28px;font-size:10px">
                {{ strtoupper(substr($dest->customer?->prenom ?? '?', 0, 1) . substr($dest->customer?->nom ?? '?', 0, 1)) }}
              </div>
              <span style="font-size:12px;color:#212529;font-weight:600">
                {{ $dest->customer ? $dest->customer->prenom.' '.$dest->customer->nom : '—' }}
              </span>
              <span class="mg-pill {{ match($dest->statut){ 'envoye'=>'mp-envoye','echec'=>'mp-echec',default=>'mp-planifie'} }}" style="margin-left:auto">
                {{ match($dest->statut){ 'envoye'=>'Envoyé','echec'=>'Échec',default=>'Attente'} }}
              </span>
            </div>
            @endforeach
            @if($dm->destinataires->count() > 10)
            <div style="font-size:11px;color:var(--mg-muted);text-align:center;padding:8px">
              + {{ $dm->destinataires->count() - 10 }} autres…
            </div>
            @endif
          </div>
        </div>
        @endif

        @endif

      </div>

      <div class="mg-modal-footer" style="flex-shrink:0">
        <button class="btn-mg-secondary" data-bs-dismiss="modal">
          <i class="ri-close-line me-1"></i> Fermer
        </button>
      </div>

    </div>
  </div>
</div>

</div>


@push('styles')
<link href="{{ asset('assets/css/message-groupe.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
/* ── Select2 init / sync avec Livewire ── */
function initSelect2() {
  const sel = $('#select2-customers');
  if (!sel.length) return;

  sel.select2({
    placeholder: 'Rechercher et sélectionner des fidèles…',
    allowClear: true,
    width: '100%',
  });

  sel.on('change', function () {
    const ids = $(this).val() || [];
    @this.set('customerIds', ids.map(Number));
  });
}

/* Livewire events Bootstrap --*/
Livewire.on('OpenModalModilEdit', ({ name_modal }) => {
  const el = document.getElementById(name_modal);
  if (el) {
    bootstrap.Modal.getOrCreateInstance(el).show();
    /* Init select2 après ouverture modale */
    el.addEventListener('shown.bs.modal', initSelect2, { once: true });
  }
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
  /* Select2 override pour coller au design BO */
  .select2-container--default .select2-selection--multiple {
    border:1.5px solid #e9ebec; border-radius:9px; background:#f3f6f9;
    font-family:'Nunito',sans-serif; min-height:40px; padding:4px 8px;
  }
  .select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color:#405189; background:#fff; box-shadow:0 0 0 3px rgba(64,81,137,.08);
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background:#405189; border:none; color:#fff; border-radius:6px;
    font-size:11px; font-weight:700; padding:2px 8px;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color:rgba(255,255,255,.7); margin-right:4px;
  }
  .select2-dropdown { border:1.5px solid #e9ebec; border-radius:10px; box-shadow:0 4px 20px rgba(64,81,137,.13); }
  .select2-container--default .select2-results__option--highlighted { background:#405189; }
  .select2-search--dropdown .select2-search__field { border:1.5px solid #e9ebec; border-radius:7px; font-family:'Nunito',sans-serif; }
</style>
@endpush


@push('styles')
<style>
  .feedback-text{ width:100%; margin-top:.25rem; font-size:.875em; color:#f06548; }
</style>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
@endpush

