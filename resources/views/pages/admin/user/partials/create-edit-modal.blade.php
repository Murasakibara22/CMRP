<div wire:ignore.self class="modal fade" id="createEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px 20px 0 0; padding: 25px;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="ri-user-{{ $isEditMode ? 'settings' : 'add' }}-line me-2"></i>
                    {{ $isEditMode ? 'Modifier l\'Utilisateur' : 'Nouvel Utilisateur' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" wire:click="resetForm"></button>
            </div>

            <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Section Informations Personnelles -->
                        <div class="col-12">
                            <div class="alert border-0 mb-0" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 12px;">
                                <div class="d-flex align-items-start">
                                    <i class="ri-user-line fs-4 me-3" style="color: #2196f3;"></i>
                                    <div>
                                        <strong style="color: #2196f3;">Informations Personnelles</strong><br>
                                        <small>Nom complet et coordonnées de l'utilisateur</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nom -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri-user-line me-1"></i>Nom  <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   wire:model="nom"
                                   class="form-control @error('nom') is-invalid @enderror"
                                   placeholder="Ex: Jean Dupont"
                                   style="border-radius: 12px; border: 2px solid #e9ecef; padding: 12px;">
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri-user-line me-1"></i>Prenoms <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   wire:model="prenom"
                                   class="form-control @error('prenom') is-invalid @enderror"
                                   placeholder="Ex: Jean Dupont"
                                   style="border-radius: 12px; border: 2px solid #e9ecef; padding: 12px;">
                            @error('prenom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri-mail-line me-1"></i>Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   wire:model="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Ex: jean.dupont@example.com"
                                   style="border-radius: 12px; border: 2px solid #e9ecef; padding: 12px;">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Téléphone -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri-phone-line me-1"></i>Téléphone <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   wire:model="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="Ex: +225 0707070707"
                                   style="border-radius: 12px; border: 2px solid #e9ecef; padding: 12px;">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri-shield-user-line me-1"></i>Type d'Utilisateur <span class="text-danger">*</span>
                            </label>
                            <select wire:model="role_id"
                                    class="form-select @error('role_id') is-invalid @enderror"
                                    style="border-radius: 12px; border: 2px solid #e9ecef; padding: 12px;">
                                @forelse($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->code == "SUPER_ADMIN"? "🔱" : "⚙️"}}  {{ $role->libelle }}</option>
                                @empty
                                
                                @endforelse

                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-1 d-block">
                                <i class="ri-information-line me-1"></i>
                                Super Admin a tous les accès
                            </small>
                        </div>

                        <!-- Section Sécurité -->
                        <div class="col-12 mt-4">
                            <div class="alert border-0 mb-0" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border-radius: 12px;">
                                <div class="d-flex align-items-start">
                                    <i class="ri-lock-line fs-4 me-3" style="color: #ff9800;"></i>
                                    <div>
                                        <strong style="color: #ff9800;">Mot de Passe</strong><br>
                                        <small>{{ $isEditMode ? 'Laissez vide pour conserver l\'ancien' : 'Minimum 6 caractères' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mot de Passe -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri-key-line me-1"></i>Mot de Passe {{ $isEditMode ? '' : '*' }}
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       wire:model="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="{{ $isEditMode ? 'Nouveau mot de passe' : 'Entrez un mot de passe' }}"
                                       style="border-radius: 12px 0 0 12px; border: 2px solid #e9ecef; padding: 12px;">
                                <button type="button"
                                        wire:click="generatePassword"
                                        class="btn btn-outline-secondary"
                                        style="border-radius: 0 12px 12px 0; border: 2px solid #e9ecef; border-left: none;">
                                    <i class="ri-refresh-line"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmation Mot de Passe -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ri-shield-check-line me-1"></i>Confirmer {{ $isEditMode ? '' : '*' }}
                            </label>
                            <input type="password"
                                   wire:model="password_confirmation"
                                   class="form-control @error('password_confirmation') is-invalid @enderror"
                                   placeholder="Confirmez le mot de passe"
                                   style="border-radius: 12px; border: 2px solid #e9ecef; padding: 12px;">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($password)
                        <div class="col-12">
                            <div class="alert alert-info border-0 mb-0" style="border-radius: 12px;">
                                <div class="d-flex align-items-start">
                                    <i class="ri-information-line fs-4 me-2"></i>
                                    <div>
                                        <strong>Mot de passe généré :</strong>
                                        <code class="ms-2 fs-6">{{ $password }}</code>
                                        <br>
                                        <small class="text-muted">N'oubliez pas de le communiquer à l'utilisateur en toute sécurité</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Info Box -->
                        <div class="col-12 mt-3">
                            <div class="alert alert-warning border-0 mb-0" style="border-radius: 12px;">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-lightbulb-line fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <strong>Conseils :</strong>
                                        <ul class="mb-0 mt-2 small">
                                            <li>Utilisez un mot de passe fort et unique</li>
                                            <li>Le bouton 🔄 génère un mot de passe sécurisé automatiquement</li>
                                            <li>L'utilisateur devra changer son mot de passe à la première connexion</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4" style="background: #f8f9fa; border-radius: 0 0 20px 20px;">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                            wire:click="resetForm"
                            style="border-radius: 10px; padding: 10px 25px;">
                        <i class="ri-close-line me-1"></i>Annuler
                    </button>
                    @if(auth()->user()?->hasPermission($isEditMode ? 'ADMIN_EDIT' : 'ADMIN_CREATE'))
                    <button type="submit"
                            class="btn text-white"
                            wire:loading.attr="disabled"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 10px 25px;">
                        <span wire:loading.remove wire:target="{{ $isEditMode ? 'update' : 'store' }}">
                            <i class="ri-save-line me-1"></i>{{ $isEditMode ? 'Enregistrer' : 'Créer' }}
                        </span>
                        <span wire:loading wire:target="{{ $isEditMode ? 'update' : 'store' }}">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Enregistrement...
                        </span>
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
