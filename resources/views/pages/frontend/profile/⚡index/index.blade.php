<div>
<main class="page-content">

  <div class="prof-layout">

    {{-- ══ COLONNE PRINCIPALE ══════════════════════════════ --}}
    <div>

      {{-- Hero --}}
      <div class="prof-hero">
        <div class="prof-hero-bg"></div>
        <div class="prof-hero-content">
          <div class="prof-avatar-wrap">
            {{-- Avatar : photo si disponible, sinon initiales --}}
            <div class="prof-avatar" id="prof-avatar">
              @if($photoUrl)
                <img src="{{ $photoUrl }}" id="prof-avatar-img"
                     style="width:100%;height:100%;object-fit:cover;border-radius:50%">
              @else
                <span id="prof-avatar-initiales">{{ $initiales }}</span>
              @endif
            </div>
            <button class="prof-avatar-edit" wire:click="openPhoto" title="Changer la photo">
              <i class="ri-camera-line"></i>
            </button>
          </div>
          <div class="prof-hero-info">
            <div class="prof-name">{{ $customer->prenom }} {{ $customer->nom }}</div>
            <div class="prof-phone">{{ $customer->dial_code }} {{ $customer->phone }}</div>
            <div class="prof-hero-badges">
              <span class="pill" style="background:rgba(255,255,255,.2);color:#fff;font-size:11px">
                <i class="{{ $customer->status === 'actif' ? 'ri-shield-check-line' : 'ri-time-line' }}"></i>
                {{ $customer->status === 'actif' ? 'Compte validé' : 'En attente de validation' }}
              </span>
              @if($customer->date_adhesion)
              <span class="pill" style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.85);font-size:11px">
                <i class="ri-calendar-line"></i>
                Depuis {{ $customer->date_adhesion->translatedFormat('M Y') }}
              </span>
              @endif
            </div>
          </div>
        </div>
        <button class="prof-edit-btn" wire:click="openEdit" title="Modifier mes informations">
          <i class="ri-pencil-line"></i>
        </button>
      </div>

      {{-- Infos personnelles --}}
      <div class="card prof-card">
        <div class="prof-card-header">
          <span class="prof-card-title"><i class="ri-user-line"></i> Informations personnelles</span>
          <button class="prof-card-edit-btn" wire:click="openEdit">
            <i class="ri-pencil-line"></i> Modifier
          </button>
        </div>
        <div class="prof-info-list">
          <div class="prof-info-row">
            <div class="prof-info-label"><i class="ri-user-3-line"></i> Nom complet</div>
            <div class="prof-info-value">{{ $customer->prenom }} {{ $customer->nom }}</div>
          </div>
          <div class="prof-info-row">
            <div class="prof-info-label"><i class="ri-smartphone-line"></i> Téléphone</div>
            <div class="prof-info-value">{{ $customer->dial_code }} {{ $customer->phone }}</div>
          </div>
          <div class="prof-info-row">
            <div class="prof-info-label"><i class="ri-map-pin-line"></i> Adresse</div>
            <div class="prof-info-value">{{ $customer->adresse ?? '—' }}</div>
          </div>
          @if($customer->typeCotisationMensuel)
          <div class="prof-info-row">
            <div class="prof-info-label"><i class="ri-calendar-check-line"></i> Type de cotisation</div>
            <div class="prof-info-value" style="color:var(--p);font-weight:700">
              {{ $customer->typeCotisationMensuel->libelle }}
            </div>
          </div>
          @endif
          @if($customer->montant_engagement)
          <div class="prof-info-row">
            <div class="prof-info-label"><i class="ri-money-cny-circle-line"></i> Engagement mensuel</div>
            <div class="prof-info-value" style="color:var(--p);font-weight:800">
              {{ number_format($customer->montant_engagement, 0, ',', ' ') }} FCFA
            </div>
          </div>
          @endif
          @if($customer->date_adhesion)
          <div class="prof-info-row">
            <div class="prof-info-label"><i class="ri-calendar-check-line"></i> Membre depuis</div>
            <div class="prof-info-value">{{ $customer->date_adhesion->translatedFormat('d F Y') }}</div>
          </div>
          @endif
          @if($demandeEnAttente)
          <div style="background:rgba(247,184,75,.08);border:1.5px solid #f7b84b;border-left:4px solid #f7b84b;border-radius:0 10px 10px 0;padding:12px 14px;margin-top:12px">
            <div style="font-size:12px;font-weight:700;color:#c07a10;margin-bottom:3px">
              <i class="ri-time-line me-1"></i>Demande en attente de validation
            </div>
            <div style="font-size:11px;color:#495057">
              @if($demandeEnAttente->isChangement())
                Changement vers « {{ $demandeEnAttente->nouveauType?->libelle }} »
                @if($demandeEnAttente->nouveau_montant_engagement)
                  — {{ number_format($demandeEnAttente->nouveau_montant_engagement, 0, ',', ' ') }} FCFA/mois
                @endif
              @else
                Arrêt de la cotisation mensuelle
              @endif
              @if($demandeEnAttente->supprimer_cotisations_retard)
                · Suppression des cotisations en retard demandée
              @endif
            </div>
          </div>
          @endif
        </div>
      </div>

      {{-- Stats rapides --}}
      <div class="prof-stats-row">
        <div class="prof-stat-card">
          <div class="psc-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-money-cny-circle-line"></i></div>
          <div class="psc-val" style="color:#0ab39c;font-size:15px">{{ number_format($totalCotise, 0, ',', ' ') }}</div>
          <div class="psc-label">FCFA cotisés</div>
        </div>
        <div class="prof-stat-card">
          <div class="psc-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-bank-card-line"></i></div>
          <div class="psc-val">{{ $nbPaiements }}</div>
          <div class="psc-label">Paiements</div>
        </div>
        <div class="prof-stat-card">
          <div class="psc-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-time-line"></i></div>
          <div class="psc-val" style="color:#f06548">{{ $moisRetard }}</div>
          <div class="psc-label">Mois retard</div>
        </div>
      </div>

      {{-- Menu actions --}}
      <div class="card prof-menu-card">

        <a href="{{ route('customer.documents') }}" class="prof-menu-item">
          <div class="pmi-icon" style="background:rgba(41,156,219,.10);color:#299cdb"><i class="ri-file-list-3-line"></i></div>
          <div class="pmi-body">
            <div class="pmi-title">Mes documents</div>
            <div class="pmi-sub">CNI, justificatifs de résidence</div>
          </div>
          @if($nbDocuments > 0)
          <span class="pmi-badge warn">{{ $nbDocuments }} en attente</span>
          @endif
          <i class="ri-arrow-right-s-line pmi-arrow"></i>
        </a>

        <a href="/customer/reclamations" class="prof-menu-item">
          <div class="pmi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-flag-line"></i></div>
          <div class="pmi-body">
            <div class="pmi-title">Mes réclamations</div>
            <div class="pmi-sub">Signaler un problème</div>
          </div>
          @if($nbReclammationsEnCours > 0)
          <span class="pmi-badge info">{{ $nbReclammationsEnCours }} en cours</span>
          @endif
          <i class="ri-arrow-right-s-line pmi-arrow"></i>
        </a>

        {{-- Bilan --}}
        <button class="prof-menu-item" wire:click="openBilan">
          <div class="pmi-icon" style="background:rgba(10,179,156,.10);color:#0ab39c"><i class="ri-file-chart-line"></i></div>
          <div class="pmi-body">
            <div class="pmi-title">Télécharger mon bilan</div>
            <div class="pmi-sub">Historique cotisations & paiements en PDF</div>
          </div>
          <i class="ri-arrow-right-s-line pmi-arrow"></i>
        </button>

        <button class="prof-menu-item" wire:click="openEdit">
          <div class="pmi-icon" style="background:rgba(64,81,137,.10);color:#405189"><i class="ri-pencil-line"></i></div>
          <div class="pmi-body">
            <div class="pmi-title">Modifier mes informations</div>
            <div class="pmi-sub">Nom, adresse, cotisation mensuelle</div>
          </div>
          <i class="ri-arrow-right-s-line pmi-arrow"></i>
        </button>

        <button class="prof-menu-item" wire:click="openDemandeChange">
          <div class="pmi-icon" style="background:rgba(247,184,75,.12);color:#f7b84b"><i class="ri-swap-line"></i></div>
          <div class="pmi-body">
            <div class="pmi-title">Changer ma cotisation mensuelle</div>
            <div class="pmi-sub">Changer de type ou arrêter la cotisation</div>
          </div>
          @if($demandeEnAttente)
          <span class="pmi-badge warn">En attente</span>
          @endif
          <i class="ri-arrow-right-s-line pmi-arrow"></i>
        </button>

        <button class="prof-menu-item prof-menu-danger" wire:click="deconnexion">
          <div class="pmi-icon" style="background:rgba(240,101,72,.10);color:#f06548"><i class="ri-logout-box-r-line"></i></div>
          <div class="pmi-body">
            <div class="pmi-title" style="color:#f06548">Déconnexion</div>
          </div>
          <i class="ri-arrow-right-s-line pmi-arrow"></i>
        </button>

      </div>

    </div>{{-- /col principale --}}

    {{-- ══ COLONNE DROITE DESKTOP ══════════════════════════ --}}
    <div class="prof-right-col">

      <div class="card" style="padding:20px;margin-bottom:16px">
        <div style="font-size:13px;font-weight:800;color:var(--p);margin-bottom:14px;display:flex;align-items:center;gap:6px">
          <i class="ri-bar-chart-line"></i> Résumé
        </div>
        <div style="display:flex;flex-direction:column;gap:12px">
          <div class="prof-summary-row">
            <span>Total cotisé</span>
            <strong style="color:#0ab39c">{{ number_format($totalCotise, 0, ',', ' ') }} FCFA</strong>
          </div>
          <div class="prof-summary-row">
            <span>Montant dû</span>
            <strong style="color:{{ $totalDu > 0 ? '#f06548' : '#0ab39c' }}">
              {{ $totalDu > 0 ? number_format($totalDu, 0, ',', ' ').' FCFA' : 'À jour ✓' }}
            </strong>
          </div>
          <div class="prof-summary-row">
            <span>Paiements</span>
            <strong>{{ $nbPaiements }}</strong>
          </div>
          <div class="prof-summary-row">
            <span>Documents</span>
            <strong>{{ $nbDocuments }}</strong>
          </div>
        </div>
        {{-- Bouton bilan desktop --}}
        <button wire:click="openBilan"
                style="margin-top:16px;width:100%;padding:10px;border:1.5px solid rgba(10,179,156,.3);border-radius:10px;background:rgba(10,179,156,.05);color:#0ab39c;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;font-family:inherit">
          <i class="ri-file-chart-line"></i> Télécharger mon bilan
        </button>
      </div>

      <div class="card" style="padding:20px">
        <div style="font-size:13px;font-weight:800;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:6px">
          <i class="ri-information-line" style="color:var(--p)"></i> Statut du compte
        </div>
        <div class="prof-status-steps">
          <div class="pss-step done">
            <div class="pss-dot"><i class="ri-check-line"></i></div>
            <div class="pss-text">Inscription</div>
          </div>
          <div class="pss-step done">
            <div class="pss-dot"><i class="ri-check-line"></i></div>
            <div class="pss-text">Vérification OTP</div>
          </div>
          <div class="pss-step {{ $customer->status === 'actif' ? 'done' : '' }}">
            <div class="pss-dot">
              @if($customer->status === 'actif')<i class="ri-check-line"></i>
              @else <i class="ri-time-line"></i>@endif
            </div>
            <div class="pss-text">Compte validé</div>
          </div>
        </div>
      </div>

    </div>

  </div>

  <div style="height:24px"></div>

</main>


{{-- ══════════════════════════════════════════════════════════
     MODAL MODIFIER INFOS
══════════════════════════════════════════════════════════ --}}
<div class="pwa-modal-overlay" id="edit-overlay" wire:ignore.self>
  <div class="pwa-modal" wire:click.stop>

    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-pencil-line"></i> Modifier mes informations</div>
        <button class="pwa-modal-close" wire:click="closeEdit"><i class="ri-close-line"></i></button>
      </div>
    </div>

    <div class="pwa-modal-body">

      <div class="f-group">
        <label class="f-label">Nom <span class="req">*</span></label>
        <input type="text" class="f-input {{ $errorNom ? 'f-input-err' : '' }}"
               wire:model.lazy="nom" placeholder="Votre nom"/>
        @if($errorNom)<div class="f-err">{{ $errorNom }}</div>@endif
      </div>

      <div class="f-group">
        <label class="f-label">Prénoms <span class="req">*</span></label>
        <input type="text" class="f-input {{ $errorPrenom ? 'f-input-err' : '' }}"
               wire:model.lazy="prenom" placeholder="Vos prénoms"/>
        @if($errorPrenom)<div class="f-err">{{ $errorPrenom }}</div>@endif
      </div>

      <div class="f-group">
        <label class="f-label">Adresse</label>
        <div class="f-input-wrap">
          <i class="ri-map-pin-line f-input-icon"></i>
          <input type="text" class="f-input" wire:model.lazy="adresse" placeholder="Votre adresse"/>
        </div>
      </div>

      <div class="f-group">
        <label class="f-label">Numéro de téléphone</label>
        <div class="f-input-wrap">
          <i class="ri-smartphone-line f-input-icon"></i>
          <input type="tel" class="f-input" wire:model.lazy="phone" placeholder="Numéro" inputmode="numeric"/>
        </div>
        <div class="f-hint">Le numéro est utilisé pour la connexion OTP.</div>
      </div>

      {{-- Cotisation mensuelle --}}
      {{-- <div style="padding-top:20px;border-top:1px dashed rgba(64,81,137,.2);margin-top:4px">
        <div style="font-size:13px;font-weight:800;color:#405189;margin-bottom:4px;display:flex;align-items:center;gap:6px">
          <i class="ri-calendar-check-line"></i> Type de cotisation mensuel
          <span style="font-size:10px;font-weight:500;color:#878a99">(optionnel)</span>
        </div>
        <div style="font-size:12px;color:#878a99;margin-bottom:14px;line-height:1.5">
          Choisissez votre catégorie de cotisation mensuelle.
        </div>

        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:14px">
          @foreach($typesMensuels as $tm)
          @php $selected = $typeCotisationMensuelId === $tm->id; @endphp
          <div wire:click="selectTypeMensuel({{ $tm->id }})"
               style="display:flex;align-items:center;justify-content:space-between;border:1.5px solid {{ $selected ? '#405189' : 'rgba(64,81,137,.15)' }};background:{{ $selected ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:12px 14px;cursor:pointer;transition:all .2s;">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:32px;height:32px;border-radius:8px;background:{{ $selected ? 'rgba(64,81,137,.15)' : 'rgba(135,138,153,.08)' }};color:{{ $selected ? '#405189' : '#878a99' }};display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0">
                <i class="ri-calendar-check-line"></i>
              </div>
              <div>
                <div style="font-size:13px;font-weight:700;color:{{ $selected ? '#405189' : '#212529' }}">{{ $tm->libelle }}</div>
                @if($tm->montant_minimum)
                <div style="font-size:11px;color:#878a99;margin-top:2px">Minimum {{ number_format($tm->montant_minimum, 0, ',', ' ') }} FCFA/mois</div>
                @endif
              </div>
            </div>
            <div style="width:20px;height:20px;border-radius:50%;border:2px solid {{ $selected ? '#405189' : '#e9ebec' }};background:{{ $selected ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
              @if($selected)<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
            </div>
          </div>
          @endforeach

          <div wire:click="selectTypeMensuel(null)"
               style="display:flex;align-items:center;gap:10px;border:1.5px solid {{ ! $typeCotisationMensuelId ? '#405189' : 'rgba(64,81,137,.15)' }};background:{{ ! $typeCotisationMensuelId ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:10px 14px;cursor:pointer;transition:all .2s;">
            <div style="width:32px;height:32px;border-radius:8px;background:rgba(135,138,153,.08);color:#878a99;display:flex;align-items:center;justify-content:center;font-size:15px">
              <i class="ri-user-line"></i>
            </div>
            <div style="font-size:13px;font-weight:700;color:{{ ! $typeCotisationMensuelId ? '#405189' : '#212529' }}">Sans cotisation mensuelle</div>
          </div>
        </div>

        
        @if($showConfirmChangementType)
        <div style="background:rgba(247,184,75,.07);border:1.5px solid #f7b84b;border-left:4px solid #f7b84b;border-radius:0 12px 12px 0;padding:14px 16px;margin-bottom:14px;">
          <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px">
            <i class="ri-swap-line" style="color:#f7b84b;font-size:20px;flex-shrink:0;margin-top:1px"></i>
            <div>
              <div style="font-size:13px;font-weight:800;color:#c07a10;margin-bottom:4px">Changement de catégorie</div>
              <div style="font-size:12px;color:#495057;line-height:1.6">{{ $confirmChangementMessage }}</div>
            </div>
          </div>
          <div style="font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            Nouveau montant mensuel <span style="color:#f06548">*</span>
          </div>
          @if($coutEngagements->count())
          <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
            @foreach($coutEngagements as $cout)
            <div wire:click="selectNouvelEngagement({{ $cout->montant }})"
                 style="padding:7px 12px;border-radius:20px;cursor:pointer;border:1.5px solid {{ $nouvelEngagement === $cout->montant ? '#405189' : '#e9ebec' }};background:{{ $nouvelEngagement === $cout->montant ? 'rgba(64,81,137,.08)' : '#fff' }};color:{{ $nouvelEngagement === $cout->montant ? '#405189' : '#495057' }};font-size:12px;font-weight:700;transition:all .15s;">
              {{ number_format($cout->montant, 0, ',', ' ') }} FCFA
            </div>
            @endforeach
          </div>
          @endif
          <div style="position:relative;margin-bottom:8px">
            <i class="ri-money-cny-circle-line" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#878a99;font-size:14px;pointer-events:none"></i>
            <input type="number" wire:model.live="nouvelEngagement" min="1" placeholder="Ou saisir un montant…" inputmode="numeric"
                   style="border:1.5px solid {{ $errorEngagement ? '#f06548' : '#e9ebec' }};border-radius:10px;height:42px;padding:0 12px 0 34px;font-size:13px;width:100%;background:#fff;color:#212529;"/>
          </div>
          @if($errorEngagement)
          <div style="font-size:12px;color:#f06548;margin-bottom:8px;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorEngagement }}</div>
          @endif
        </div>
        @endif


        @php $ancienTypeId = auth('customer')->user()->type_cotisation_mensuel_id; $estPremierTypeForm = $typeCotisationMensuelId && ! $ancienTypeId; @endphp
        @if($estPremierTypeForm && ! $showConfirmChangementType)
        <div style="margin-top:4px">
          <div style="font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            Montant d'engagement mensuel <span style="color:#f06548">*</span>
          </div>
          @if($coutEngagements->count())
          <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
            @foreach($coutEngagements as $cout)
            <div wire:click="selectEngagement({{ $cout->montant }})"
                 style="padding:7px 12px;border-radius:20px;cursor:pointer;border:1.5px solid {{ $montantEngagement === $cout->montant ? '#405189' : '#e9ebec' }};background:{{ $montantEngagement === $cout->montant ? 'rgba(64,81,137,.08)' : '#fff' }};color:{{ $montantEngagement === $cout->montant ? '#405189' : '#495057' }};font-size:12px;font-weight:700;transition:all .15s;">
              {{ number_format($cout->montant, 0, ',', ' ') }} FCFA
            </div>
            @endforeach
          </div>
          @endif
          <div style="position:relative">
            <i class="ri-money-cny-circle-line" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#878a99;font-size:14px;pointer-events:none"></i>
            <input type="number" wire:model.live="montantEngagement" min="1" placeholder="Ou saisir un montant…" inputmode="numeric"
                   style="border:1.5px solid {{ $errorEngagement ? '#f06548' : '#e9ebec' }};border-radius:10px;height:42px;padding:0 12px 0 34px;font-size:13px;width:100%;background:#fff;color:#212529;"/>
          </div>
          @if($errorEngagement)
          <div style="font-size:12px;color:#f06548;margin-top:6px;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorEngagement }}</div>
          @endif
          <div style="font-size:11px;color:#878a99;margin-top:6px;line-height:1.5">
            Une cotisation sera créée pour le mois en cours avec le statut <em>En retard</em>.
          </div>
        </div>
        @endif

      </div> --}}

    </div>

    <div class="pwa-modal-footer">
      <button class="btn-outline" style="height:46px;font-size:14px" wire:click="closeEdit">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="btn-main" style="height:46px;font-size:14px"
              wire:click="saveEdit" wire:loading.attr="disabled">
        <span wire:loading wire:target="saveEdit"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="saveEdit">
          <i class="ri-save-line"></i>
          {{ $showConfirmChangementType ? 'Confirmer et enregistrer' : 'Enregistrer' }}
        </span>
      </button>
    </div>

  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL PHOTO DE PROFIL
══════════════════════════════════════════════════════════ --}}
<div class="pwa-modal-overlay" id="photo-overlay" wire:ignore.self>
  <div class="pwa-modal pwa-modal-sm" wire:click.stop>

    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-camera-line"></i> Photo de profil</div>
        <button class="pwa-modal-close" wire:click="closePhoto"><i class="ri-close-line"></i></button>
      </div>
    </div>

    <div class="pwa-modal-body">

      {{-- Aperçu --}}
      <div class="photo-preview-wrap">
        <div class="photo-preview" id="photo-preview-modal">
          @if($photoUrl)
            <img src="{{ $photoUrl }}" id="photo-preview-img"
                 style="width:100%;height:100%;object-fit:cover;border-radius:50%">
          @else
            <span id="photo-preview-initiales">{{ $initiales }}</span>
          @endif
        </div>
        <div class="photo-preview-label">Aperçu</div>
      </div>

      {{-- Upload via Livewire (caché) --}}
      <input type="file" id="file-input-lw" accept="image/*"
             wire:model="photoFile"
             style="display:none"
             onchange="previewPhotoLivewire(this)">

      {{-- Boutons de sélection --}}
      <div class="photo-btns" id="photo-select-btns">
        <button class="photo-btn photo-btn-primary" onclick="triggerCamera()">
          <i class="ri-camera-fill"></i>
          <span>Prendre une photo</span>
          <div class="photo-btn-sub">Ouvrir la caméra</div>
        </button>
        <button class="photo-btn" onclick="triggerGallery()">
          <i class="ri-image-2-line"></i>
          <span>Choisir dans la galerie</span>
          <div class="photo-btn-sub">Depuis votre téléphone</div>
        </button>
        @if($photoUrl)
        <button class="photo-btn photo-btn-danger" wire:click="supprimerPhoto" wire:loading.attr="disabled">
          <i class="ri-delete-bin-line"></i>
          <span>Supprimer la photo</span>
          <div class="photo-btn-sub">Retour aux initiales</div>
        </button>
        @endif
      </div>

      {{-- Boutons confirmation (visible après sélection) --}}
      <div id="photo-confirm-btns" style="display:none;gap:10px;margin-top:14px">
        <button onclick="annulerPhoto()"
                style="flex:1;padding:12px;border:1.5px solid #e9ebec;border-radius:12px;background:#fff;color:#878a99;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit">
          <i class="ri-close-line me-1"></i>Annuler
        </button>
        <button wire:click="sauvegarderPhoto"
                wire:loading.attr="disabled"
                wire:target="sauvegarderPhoto"
                style="flex:1;padding:12px;border:none;border-radius:12px;background:linear-gradient(135deg,#2d3a63,#405189);color:#fff;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;justify-content:center;gap:6px">
          <span wire:loading wire:target="sauvegarderPhoto" class="spinner"></span>
          <i class="ri-save-line" wire:loading.remove wire:target="sauvegarderPhoto"></i>
          <span wire:loading.remove wire:target="sauvegarderPhoto">Sauvegarder</span>
          <span wire:loading wire:target="sauvegarderPhoto">Enregistrement…</span>
        </button>
      </div>

    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL BILAN PDF
══════════════════════════════════════════════════════════ --}}
<div class="pwa-modal-overlay" id="bilan-overlay" wire:ignore.self>
  <div class="pwa-modal pwa-modal-sm" wire:click.stop>

    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-file-chart-line"></i> Télécharger mon bilan</div>
        <button class="pwa-modal-close" wire:click="closeBilan"><i class="ri-close-line"></i></button>
      </div>
    </div>

    <div class="pwa-modal-body">

      <div style="font-size:13px;color:#878a99;margin-bottom:20px;line-height:1.6">
        Générez un PDF de votre historique de cotisations et paiements.
      </div>

      {{-- Option : Tout l'historique --}}
      <div wire:click="$set('bilanTout', true)"
           style="display:flex;align-items:center;gap:12px;border:1.5px solid {{ $bilanTout ? '#405189' : 'rgba(64,81,137,.15)' }};background:{{ $bilanTout ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s;margin-bottom:10px">
        <div style="width:38px;height:38px;border-radius:10px;background:{{ $bilanTout ? 'rgba(64,81,137,.15)' : 'rgba(135,138,153,.08)' }};color:{{ $bilanTout ? '#405189' : '#878a99' }};display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
          <i class="ri-history-line"></i>
        </div>
        <div>
          <div style="font-size:13px;font-weight:700;color:{{ $bilanTout ? '#405189' : '#212529' }}">Tout l'historique</div>
          <div style="font-size:11px;color:#878a99;margin-top:2px">Depuis la date d'adhésion jusqu'à aujourd'hui</div>
        </div>
        <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid {{ $bilanTout ? '#405189' : '#e9ebec' }};background:{{ $bilanTout ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
          @if($bilanTout)<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
        </div>
      </div>

      {{-- Option : Période précise --}}
      <div wire:click="$set('bilanTout', false)"
           style="display:flex;align-items:center;gap:12px;border:1.5px solid {{ ! $bilanTout ? '#405189' : 'rgba(64,81,137,.15)' }};background:{{ ! $bilanTout ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s;margin-bottom:16px">
        <div style="width:38px;height:38px;border-radius:10px;background:{{ ! $bilanTout ? 'rgba(64,81,137,.15)' : 'rgba(135,138,153,.08)' }};color:{{ ! $bilanTout ? '#405189' : '#878a99' }};display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
          <i class="ri-calendar-range-line"></i>
        </div>
        <div>
          <div style="font-size:13px;font-weight:700;color:{{ ! $bilanTout ? '#405189' : '#212529' }}">Période précise</div>
          <div style="font-size:11px;color:#878a99;margin-top:2px">Choisir une plage de dates</div>
        </div>
        <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid {{ ! $bilanTout ? '#405189' : '#e9ebec' }};background:{{ ! $bilanTout ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
          @if(! $bilanTout)<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
        </div>
      </div>

      {{-- Champs de dates --}}
      @if(! $bilanTout)
      <div style="display:flex;flex-direction:column;gap:10px;animation:fadeIn .2s ease">
        <div>
          <label style="display:block;font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
            <i class="ri-calendar-line me-1"></i>Date de début
          </label>
          <input type="date" wire:model="bilanDebut"
                 style="width:100%;border:1.5px solid #e9ebec;border-radius:10px;height:42px;padding:0 12px;font-size:13px;background:#fff;color:#212529;font-family:inherit"/>
        </div>
        <div>
          <label style="display:block;font-size:11px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
            <i class="ri-calendar-check-line me-1"></i>Date de fin
          </label>
          <input type="date" wire:model="bilanFin"
                 style="width:100%;border:1.5px solid #e9ebec;border-radius:10px;height:42px;padding:0 12px;font-size:13px;background:#fff;color:#212529;font-family:inherit"/>
        </div>
      </div>
      @endif

    </div>

    <div class="pwa-modal-footer">
      <button class="btn-outline" style="height:46px;font-size:14px" wire:click="closeBilan">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="btn-main" style="height:46px;font-size:14px"
              wire:click="telechargerBilan"
              wire:loading.attr="disabled"
              wire:target="telechargerBilan">
        <span wire:loading wire:target="telechargerBilan"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="telechargerBilan">
          <i class="ri-download-2-line"></i> Télécharger le PDF
        </span>
        <span wire:loading wire:target="telechargerBilan">Génération…</span>
      </button>
    </div>

  </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     MODAL DEMANDE CHANGEMENT COTISATION MENSUELLE
══════════════════════════════════════════════════════════ --}}
<div class="pwa-modal-overlay" id="demande-change-overlay" wire:ignore.self>
  <div class="pwa-modal" wire:click.stop>

    <div class="pwa-modal-header">
      <div class="pwa-modal-drag"></div>
      <div class="pwa-modal-title-row">
        <div class="pwa-modal-title"><i class="ri-swap-line"></i> Changer ma cotisation mensuelle</div>
        <button class="pwa-modal-close" wire:click="closeDemandeChange"><i class="ri-close-line"></i></button>
      </div>
    </div>

    <div class="pwa-modal-body">

      <div style="font-size:13px;color:#878a99;margin-bottom:20px;line-height:1.6">
        Votre demande sera examinée par l'administration avant d'être appliquée.
      </div>

      {{-- Cotisation actuelle --}}
      @if($customer->typeCotisationMensuel)
      <div style="background:rgba(64,81,137,.06);border:1px solid rgba(64,81,137,.15);border-radius:12px;padding:12px 16px;margin-bottom:20px">
        <div style="font-size:11px;color:#878a99;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Cotisation actuelle</div>
        <div style="font-size:14px;font-weight:700;color:#405189">{{ $customer->typeCotisationMensuel->libelle }}</div>
        @if($customer->montant_engagement)
        <div style="font-size:12px;color:#878a99;margin-top:2px">{{ number_format($customer->montant_engagement, 0, ',', ' ') }} FCFA/mois</div>
        @endif
        @if($nbRetardAncienType > 0)
        <div style="font-size:11px;color:#f06548;margin-top:4px;font-weight:600">
          <i class="ri-time-line me-1"></i>{{ $nbRetardAncienType }} mois en retard
        </div>
        @endif
      </div>
      @endif

      {{-- Type de demande --}}
      <div style="margin-bottom:18px">
        <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
          Type de demande <span style="color:#f06548">*</span>
        </div>

        <div style="display:flex;flex-direction:column;gap:8px">
          <div wire:click="$set('typeDemande', 'changement')"
               style="display:flex;align-items:center;gap:12px;border:1.5px solid {{ $typeDemande === 'changement' ? '#405189' : 'rgba(64,81,137,.15)' }};background:{{ $typeDemande === 'changement' ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s">
            <div style="width:36px;height:36px;border-radius:9px;background:rgba(64,81,137,.1);color:#405189;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
              <i class="ri-swap-line"></i>
            </div>
            <div>
              <div style="font-size:13px;font-weight:700;color:{{ $typeDemande === 'changement' ? '#405189' : '#212529' }}">Changer de type</div>
              <div style="font-size:11px;color:#878a99;margin-top:2px">Migrer vers un autre type de cotisation mensuelle</div>
            </div>
            <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid {{ $typeDemande === 'changement' ? '#405189' : '#e9ebec' }};background:{{ $typeDemande === 'changement' ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
              @if($typeDemande === 'changement')<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
            </div>
          </div>

          <div wire:click="$set('typeDemande', 'arret')"
               style="display:flex;align-items:center;gap:12px;border:1.5px solid {{ $typeDemande === 'arret' ? '#f06548' : 'rgba(64,81,137,.15)' }};background:{{ $typeDemande === 'arret' ? 'rgba(240,101,72,.04)' : '#fff' }};border-radius:12px;padding:14px;cursor:pointer;transition:all .2s">
            <div style="width:36px;height:36px;border-radius:9px;background:rgba(240,101,72,.1);color:#f06548;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
              <i class="ri-close-circle-line"></i>
            </div>
            <div>
              <div style="font-size:13px;font-weight:700;color:{{ $typeDemande === 'arret' ? '#f06548' : '#212529' }}">Arrêter la cotisation mensuelle</div>
              <div style="font-size:11px;color:#878a99;margin-top:2px">Ne plus être soumis à une cotisation mensuelle</div>
            </div>
            <div style="margin-left:auto;width:20px;height:20px;border-radius:50%;border:2px solid {{ $typeDemande === 'arret' ? '#f06548' : '#e9ebec' }};background:{{ $typeDemande === 'arret' ? '#f06548' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
              @if($typeDemande === 'arret')<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
            </div>
          </div>
        </div>

        @if($errorTypeDemande)
        <div style="font-size:12px;color:#f06548;margin-top:6px;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorTypeDemande }}</div>
        @endif
      </div>

      {{-- Nouveau type (si changement) --}}
      @if($typeDemande === 'changement')
      <div style="margin-bottom:18px;animation:fadeIn .2s ease">
        <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
          Nouveau type souhaité <span style="color:#f06548">*</span>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px">
          @foreach($typesMensuels as $tm)
          @php $sel = $demandeNouveauTypeId === $tm->id; @endphp
          <div wire:click="selectDemandeNouveauType({{ $tm->id }})"
               style="display:flex;align-items:center;justify-content:space-between;border:1.5px solid {{ $sel ? '#405189' : 'rgba(64,81,137,.15)' }};background:{{ $sel ? 'rgba(64,81,137,.06)' : '#fff' }};border-radius:12px;padding:12px 14px;cursor:pointer;transition:all .2s">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:30px;height:30px;border-radius:8px;background:{{ $sel ? 'rgba(64,81,137,.15)' : 'rgba(135,138,153,.08)' }};color:{{ $sel ? '#405189' : '#878a99' }};display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0">
                <i class="ri-calendar-check-line"></i>
              </div>
              <div>
                <div style="font-size:13px;font-weight:700;color:{{ $sel ? '#405189' : '#212529' }}">{{ $tm->libelle }}</div>
                @if($tm->montant_minimum)
                <div style="font-size:11px;color:#878a99;margin-top:1px">Minimum {{ number_format($tm->montant_minimum, 0, ',', ' ') }} FCFA/mois</div>
                @endif
              </div>
            </div>
            <div style="width:20px;height:20px;border-radius:50%;border:2px solid {{ $sel ? '#405189' : '#e9ebec' }};background:{{ $sel ? '#405189' : 'transparent' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
              @if($sel)<i class="ri-check-line" style="color:#fff;font-size:11px"></i>@endif
            </div>
          </div>
          @endforeach
        </div>
        @if($errorNouveauType)
        <div style="font-size:12px;color:#f06548;margin-top:6px;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorNouveauType }}</div>
        @endif

        {{-- Nouveau montant --}}
        @if($demandeNouveauTypeId)
        <div style="margin-top:14px">
          <div style="font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">
            Nouveau montant d'engagement <span style="color:#f06548">*</span>
          </div>
          @if($coutEngagements->count())
          <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px">
            @foreach($coutEngagements as $ce)
            <div wire:click="selectDemandeNouvelEngagement({{ $ce->montant }})"
                 style="padding:7px 12px;border-radius:20px;cursor:pointer;border:1.5px solid {{ $demandeNouvelEngagement === $ce->montant ? '#405189' : '#e9ebec' }};background:{{ $demandeNouvelEngagement === $ce->montant ? 'rgba(64,81,137,.08)' : '#fff' }};color:{{ $demandeNouvelEngagement === $ce->montant ? '#405189' : '#495057' }};font-size:12px;font-weight:700;transition:all .15s">
              {{ number_format($ce->montant, 0, ',', ' ') }} FCFA
            </div>
            @endforeach
          </div>
          @endif
          <div style="position:relative">
            <i class="ri-money-cny-circle-line" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#878a99;font-size:14px;pointer-events:none"></i>
            <input type="number" wire:model.live="demandeNouvelEngagement" min="1"
                   placeholder="Ou saisir un montant…" inputmode="numeric"
                   style="border:1.5px solid {{ $errorNouvelEngagement ? '#f06548' : '#e9ebec' }};border-radius:10px;height:42px;padding:0 12px 0 34px;font-size:13px;width:100%;background:#fff;color:#212529"/>
          </div>
          @if($errorNouvelEngagement)
          <div style="font-size:12px;color:#f06548;margin-top:4px;font-weight:600"><i class="ri-error-warning-line me-1"></i>{{ $errorNouvelEngagement }}</div>
          @endif
        </div>
        @endif
      </div>
      @endif

      {{-- Supprimer les cotisations en retard --}}
      @if($typeDemande && $nbRetardAncienType > 0)
      <div style="background:rgba(240,101,72,.05);border:1.5px solid rgba(240,101,72,.2);border-radius:12px;padding:14px 16px;margin-bottom:18px">
        <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer">
          <input type="checkbox" wire:model="demandeSupprimerRetard"
                 style="width:18px;height:18px;accent-color:#f06548;margin-top:1px;flex-shrink:0"/>
          <div>
            <div style="font-size:13px;font-weight:700;color:#f06548">Supprimer mes {{ $nbRetardAncienType }} cotisation(s) en retard</div>
            <div style="font-size:11px;color:#878a99;margin-top:3px;line-height:1.5">
              Si coché, toutes vos cotisations en retard de l'ancien type seront supprimées lors de la validation de la demande.
            </div>
          </div>
        </label>
      </div>
      @endif

      {{-- Motif (optionnel) --}}
      @if($typeDemande)
      <div style="margin-bottom:4px">
        <label style="display:block;font-size:12px;font-weight:700;color:#495057;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">
          Motif <span style="font-size:10px;font-weight:400;text-transform:none">(optionnel)</span>
        </label>
        <textarea wire:model="demandeMotif" rows="2"
                  placeholder="Expliquez la raison de votre demande…"
                  style="width:100%;border:1.5px solid #e9ebec;border-radius:10px;padding:10px 12px;font-size:13px;resize:none;font-family:inherit;color:#212529"></textarea>
      </div>
      @endif

    </div>

    <div class="pwa-modal-footer">
      <button class="btn-outline" style="height:46px;font-size:14px" wire:click="closeDemandeChange">
        <i class="ri-close-line"></i> Annuler
      </button>
      <button class="btn-main" style="height:46px;font-size:14px"
              wire:click="submitDemandeChange"
              wire:loading.attr="disabled"
              wire:target="submitDemandeChange">
        <span wire:loading wire:target="submitDemandeChange"><div class="spinner"></div></span>
        <span wire:loading.remove wire:target="submitDemandeChange">
          <i class="ri-send-plane-line"></i> Envoyer la demande
        </span>
      </button>
    </div>

  </div>
</div>

</div>{{-- /root Livewire --}}






@push('scripts')
<script>
/* ── Overlays ── */
function openOverlay(id)  { document.getElementById(id)?.classList.add('open');    document.body.style.overflow = 'hidden'; }
function closeOverlay(id) { document.getElementById(id)?.classList.remove('open'); document.body.style.overflow = ''; }

window.addEventListener('OpenEditModal',   () => openOverlay('edit-overlay'));
window.addEventListener('closeEditModal',  () => closeOverlay('edit-overlay'));
window.addEventListener('OpenPhotoModal',  () => openOverlay('photo-overlay'));
window.addEventListener('closePhotoModal', () => closeOverlay('photo-overlay'));
window.addEventListener('OpenBilanModal',  () => openOverlay('bilan-overlay'));
window.addEventListener('OpenDemandeChangeModal',  () => openOverlay('demande-change-overlay'));
window.addEventListener('closeDemandeChangeModal', () => closeOverlay('demande-change-overlay'));
window.addEventListener('closeBilanModal', () => closeOverlay('bilan-overlay'));

/* ── Photo : sélection ── */
function triggerCamera() {
  const f = document.getElementById('file-input-lw');
  f.setAttribute('capture', 'user');
  f.click();
}
function triggerGallery() {
  const f = document.getElementById('file-input-lw');
  f.removeAttribute('capture');
  f.click();
}

function previewPhotoLivewire(input) {
  if (!input.files?.[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const preview = document.getElementById('photo-preview-modal');
    if (preview) preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
    /* Afficher les boutons de confirmation */
    document.getElementById('photo-select-btns').style.display = 'none';
    const confirm = document.getElementById('photo-confirm-btns');
    if (confirm) confirm.style.display = 'flex';
  };
  reader.readAsDataURL(input.files[0]);
}

function annulerPhoto() {
  /* Remettre l'aperçu initial */
  const preview = document.getElementById('photo-preview-modal');
  const img = document.getElementById('photo-preview-img');
  const initiales = document.getElementById('photo-preview-initiales');
  if (preview) {
    if (img) preview.innerHTML = img.outerHTML;
    else if (initiales) preview.innerHTML = initiales.outerHTML;
  }
  document.getElementById('photo-select-btns').style.display = '';
  const confirm = document.getElementById('photo-confirm-btns');
  if (confirm) confirm.style.display = 'none';
  /* Réinitialiser l'input file */
  const input = document.getElementById('file-input-lw');
  if (input) input.value = '';
}

/* ── Photo sauvegardée → MAJ avatar dans le hero ── */
Livewire.on('photoSauvegardee', ({ path }) => {
  closeOverlay('photo-overlay');
  const avatar = document.getElementById('prof-avatar');
  if (avatar) {
    avatar.innerHTML = `<img src="${path}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
  }
  /* Reset modal */
  document.getElementById('photo-select-btns').style.display = '';
  const confirm = document.getElementById('photo-confirm-btns');
  if (confirm) confirm.style.display = 'none';
});

Livewire.on('photoSupprimee', () => {
  closeOverlay('photo-overlay');
});

/* ── Toast ── */
Livewire.on('modalShowmessageToast', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  if (typeof Swal !== 'undefined') {
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true })
        .fire({ icon: data.type, title: data.title });
  }
});

/* ── SweetAlert ── */
Livewire.on('swal:modalGetInfo_message_not_timer', (payload) => {
  const data = Array.isArray(payload) ? payload[0] : payload;
  if (typeof Swal !== 'undefined') Swal.fire({ title: data.title, text: data.text, icon: data.type });
});
</script>
@endpush

@push('styles')
<style>
  .f-err { font-size:12px; color:#f06548; margin-top:4px; font-weight:600; }
  .f-input-err { border-color:#f06548 !important; }
  .spinner { width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:_spin .7s linear infinite;display:inline-block; }
  @keyframes _spin { to { transform:rotate(360deg); } }
  @keyframes fadeIn { from { opacity:0;transform:translateY(-4px); } to { opacity:1;transform:translateY(0); } }
</style>
@endpush