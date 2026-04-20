<main class="page-content">

  <div class="ac-layout">

    {{-- ══ COLONNE FORMULAIRE ══════════════════════════════ --}}
    <div class="ac-form-col">

      <div class="page-title">Nouvelle cotisation</div>
      <div class="page-sub">Enregistrez votre paiement de cotisation.</div>

      {{-- ═══════════════════════════════════════════════════
           SECTION 1 : TYPE DE COTISATION
      ═══════════════════════════════════════════════════ --}}
      <div class="ac-section">
        <div class="ac-section-title">
          <span class="ac-section-bar"></span> Type de cotisation
        </div>

        <div class="f-group">
          <label class="f-label">Type <span class="req">*</span></label>
          <div class="f-input-wrap">
            <i class="ri-tag-line f-input-icon"></i>
            <select class="f-input {{ $errorType ? 'f-input-err' : '' }}"
                    wire:model.live="typeCotisationId">
              <option value="">— Choisir un type —</option>
              @foreach($typesCotisation as $tc)
                <option value="{{ $tc->id }}">
                  {{ $tc->libelle }}
                  @if($tc->type === 'mensuel') (Mensuel) @endif
                </option>
              @endforeach
            </select>
          </div>
          @if($errorType)
          <div class="f-err">{{ $errorType }}</div>
          @endif
        </div>

        {{-- Période mois/année — visible si mensuel obligatoire --}}
        @if($isMensuelObligatoire)
        <div class="f-group">
          <label class="f-label">Période concernée <span class="req">*</span></label>
          <div class="ac-row2">
            <div class="f-input-wrap">
              <i class="ri-calendar-line f-input-icon"></i>
              <select class="f-input {{ $errorPeriode ? 'f-input-err' : '' }}" wire:model="mois">
                <option value="">Mois</option>
                @foreach(range(1,12) as $m)
                  <option value="{{ $m }}" @selected($m == $mois)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="f-input-wrap">
              <i class="ri-calendar-2-line f-input-icon"></i>
              <select class="f-input {{ $errorPeriode ? 'f-input-err' : '' }}" wire:model="annee">
                @foreach([2023, 2024, 2025, 2026] as $y)
                  <option value="{{ $y }}" @selected($y == $annee)>{{ $y }}</option>
                @endforeach
              </select>
            </div>
          </div>
          @if($errorPeriode)
          <div class="f-err">{{ $errorPeriode }}</div>
          @endif
        </div>
        @endif

      </div>{{-- /section 1 --}}







      {{-- ═══════════════════════════════════════════════════
           SECTION 3 : MONTANT & MODE
      ═══════════════════════════════════════════════════ --}}
      @if($tcSelectionne)
      <div class="ac-section">
        <div class="ac-section-title">
          <span class="ac-section-bar" style="background:#0ab39c"></span>
          Montant &amp; mode de paiement
        </div>

        <div class="f-group">
          <label class="f-label">Montant payé <span class="req">*</span></label>
          <div class="f-input-wrap">
            <i class="ri-money-cny-circle-line f-input-icon"></i>
            <input type="number"
                   class="f-input has-sfx {{ $errorMontant ? 'f-input-err' : '' }}"
                   wire:model.live="montantPaye"
                   placeholder="{{ $isMensuelObligatoire && $customerHasEngagement ? number_format($customer->montant_engagement, 0, ',', ' ') : 'ex : 10000' }}"
                   inputmode="numeric"
                   min="1"/>
            <span class="f-input-suffix">FCFA</span>
          </div>

          @if($errorMontant)
          <div class="f-err">{{ $errorMontant }}</div>
          @endif

          {{-- Avertissement paiement partiel --}}
          @if($isPartiel)
          <div class="ac-partial-info">
            <i class="ri-error-warning-line"></i>
            Un paiement inférieur à l'engagement
            ({{ number_format($customer->montant_engagement, 0, ',', ' ') }} FCFA)
            sera enregistré comme <strong>partiel</strong>.
          </div>
          @endif
        </div>

        {{-- Mode de paiement --}}
        <div class="f-group">
          <label class="f-label">Mode de paiement <span class="req">*</span></label>
          <div class="ac-modes">
            <button type="button"
                    class="ac-mode {{ $modePaiement === 'mobile_money' ? 'active' : '' }}"
                    wire:click="selectMode('mobile_money')">
              <i class="ri-smartphone-line"></i><span>En ligne</span>
            </button>
            <button type="button"
                    class="ac-mode {{ $modePaiement === 'espece' ? 'active' : '' }}"
                    wire:click="selectMode('espece')">
              <i class="ri-money-dollar-circle-line"></i><span>Espèces</span>
            </button>
          </div>
          @if($errorMode)
          <div class="f-err">{{ $errorMode }}</div>
          @endif
        </div>

      </div>{{-- /section 3 --}}
      @endif


      {{-- ═══════════════════════════════════════════════════
           RÉCAP AVANT ENVOI
      ═══════════════════════════════════════════════════ --}}
      @if($tcSelectionne && $montantPaye)
      <div class="ac-recap">
        <div class="ac-recap-title"><i class="ri-receipt-line"></i> Récapitulatif</div>
        <div class="ac-recap-rows">
          <div class="ac-recap-row">
            <span>Type</span>
            <span>{{ $recapType }}</span>
          </div>
          @if($isMensuelObligatoire)
          <div class="ac-recap-row">
            <span>Période</span>
            <span>{{ $recapPeriode }}</span>
          </div>
          @endif
          <div class="ac-recap-row">
            <span>Montant</span>
            <strong style="color:var(--p)">{{ $recapMontant }}</strong>
          </div>
          <div class="ac-recap-row">
            <span>Mode</span>
            <span>{{ $recapMode }}</span>
          </div>
          @if($isPartiel)
          <div class="ac-recap-row">
            <span>Statut prévu</span>
            <span style="color:#f7b84b;font-weight:700">Partiel</span>
          </div>
          @endif
        </div>
      </div>
      @endif

      {{-- ═══ BOUTONS ═══ --}}
      <div class="ac-btns">
        <button class="btn-main"
                wire:click="submit"
                wire:loading.attr="disabled"
                @if(!$tcSelectionne || !$montantPaye) style="opacity:.5;pointer-events:none" @endif>
          <span wire:loading wire:target="submit"><div class="spinner"></div></span>
          <span wire:loading.remove wire:target="submit">
            <i class="ri-send-plane-line"></i> Valider le paiement
          </span>
        </button>
        <button class="btn-outline" wire:navigate href="{{ route('customer.cotisations') }}">
          Annuler
        </button>
      </div>

    </div>{{-- /ac-form-col --}}


    {{-- ══ COLONNE DROITE DESKTOP ══════════════════════════ --}}
    <div class="ac-right-col">

      {{-- Comment ça marche --}}
      <div class="ac-info-card card">
        <div class="ac-info-card-title">
          <i class="ri-information-line"></i> Comment ça marche ?
        </div>
        <div class="ac-info-steps">
          <div class="ac-step">
            <div class="ac-step-num">1</div>
            <div class="ac-step-text">Choisissez le type de cotisation et la période concernée.</div>
          </div>
          <div class="ac-step">
            <div class="ac-step-num">2</div>
            <div class="ac-step-text">Saisissez le montant et le mode de paiement.</div>
          </div>
          <div class="ac-step">
            <div class="ac-step-num">3</div>
            <div class="ac-step-text">Envoyez votre demande. Un administrateur la validera.</div>
          </div>
          <div class="ac-step">
            <div class="ac-step-num">4</div>
            <div class="ac-step-text">Vous recevrez une notification dès la validation.</div>
          </div>
        </div>
      </div>

      {{-- Mois en retard --}}
      @if($retards->count() > 0)
      <div class="ac-info-card card" style="margin-top:16px">
        <div class="ac-info-card-title" style="color:var(--danger)">
          <i class="ri-alarm-warning-line"></i> Mois en retard
        </div>
        <div style="font-size:13px;color:var(--muted);line-height:1.6">
          Vous avez
          <strong style="color:var(--danger)">{{ $retards->count() }} mois en retard</strong> :<br>
          {{ $retards->map(fn($r) => \Carbon\Carbon::create($r->annee, $r->mois)->translatedFormat('F Y'))->join(', ') }}<br>
          <strong style="color:var(--danger)">{{ number_format($totalRetard, 0, ',', ' ') }} FCFA</strong> dus au total.
        </div>
      </div>
      @elseif($customer?->montant_engagement)
      <div class="ac-info-card card" style="margin-top:16px">
        <div class="ac-info-card-title" style="color:#0ab39c">
          <i class="ri-checkbox-circle-line"></i> Cotisations à jour
        </div>
        <div style="font-size:13px;color:var(--muted);line-height:1.6">
          Toutes vos cotisations mensuelles sont à jour. Bravo !
        </div>
      </div>
      @endif

    </div>{{-- /ac-right-col --}}

  </div>{{-- /ac-layout --}}

  <div style="height:24px"></div>

</main>


@push('scripts')
{{-- <script src="{{ asset('frontend/js/paiement.js') }}"></script> --}}
<script>
Livewire.on('modalShowmessageToast', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  if (typeof Swal !== 'undefined') {
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
        .fire({ icon: data.type, title: data.title });
  }
});
</script>
@endpush


@push('styles')
<style>
  .f-err { font-size:12px; color:#f06548; margin-top:5px; font-weight:600; display:flex; align-items:center; gap:4px; }
  .f-input-err { border-color:#f06548 !important; background:rgba(240,101,72,.03) !important; }
  .ac-palier.selected { border-color:var(--p,#405189); background:rgba(64,81,137,.08); color:var(--p,#405189); font-weight:800; }
  .spinner { width:18px; height:18px; border:2.5px solid rgba(255,255,255,.3); border-top-color:#fff; border-radius:50%; animation:_spin .7s linear infinite; display:inline-block; }
  @keyframes _spin { to { transform:rotate(360deg); } }
</style>
@endpush
