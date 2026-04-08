<div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            @if($userToDelete)
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-radius: 20px 20px 0 0; padding: 25px;">
                <h5 class="modal-title text-white fw-bold">
                    <i class="ri-delete-bin-line me-2"></i>Confirmer la Suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" wire:click="reset(['userToDelete', 'confirmPassword'])"></button>
            </div>

            <form wire:submit.prevent="deleteUser">
                <div class="modal-body p-4">
                    <!-- Warning Alert -->
                    <div class="alert alert-danger border-0 mb-4" style="border-radius: 15px;">
                        <div class="d-flex align-items-start">
                            <i class="ri-alert-line fs-2 me-3"></i>
                            <div>
                                <h6 class="mb-2 fw-bold">⚠️ Action Irréversible</h6>
                                <p class="mb-0">Cette action supprimera définitivement l'utilisateur et toutes ses données associées.</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background: #f8f9fa;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md me-3">
                                    <div class="avatar-title rounded-circle" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 20px; font-weight: 600;">
                                        {{ strtoupper(substr($userToDelete->nom.' '.$userToDelete->prenom, 0, 2)) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ $userToDelete->nom.' '.$userToDelete->prenom }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="ri-mail-line me-1"></i>{{ $userToDelete->email }}
                                    </p>
                                    <span class="badge mt-1" style="background: linear-gradient(135deg, {{ $userToDelete->type === 'SUPER_ADMIN' ? '#f093fb 0%, #f5576c' : '#28a745 0%, #20c997' }} 100%); color: white; padding: 3px 10px; border-radius: 8px; font-size: 10px;">
                                        {{ $userToDelete->type === 'SUPER_ADMIN' ? 'Super Admin' : 'Admin' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Confirmation -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="ri-lock-password-line me-1"></i>Confirmez avec votre mot de passe <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                               wire:model="confirmPassword"
                               class="form-control"
                               placeholder="Entrez votre mot de passe"
                               style="border-radius: 12px; border: 2px solid #e9ecef; padding: 12px;"
                               required>
                        <small class="text-muted mt-1 d-block">
                            <i class="ri-information-line me-1"></i>Pour des raisons de sécurité, vous devez confirmer votre identité
                        </small>
                    </div>

                    <!-- Checklist -->
                    <div class="alert alert-light border mb-0" style="border-radius: 12px;">
                        <p class="mb-2 fw-semibold">Avant de continuer, assurez-vous que :</p>
                        <ul class="mb-0 small">
                            <li>Vous avez vérifié l'identité de l'utilisateur</li>
                            <li>Cette suppression est nécessaire</li>
                            <li>Vous avez informé les parties concernées</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4" style="background: #f8f9fa; border-radius: 0 0 20px 20px;">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                            wire:click="reset(['userToDelete', 'confirmPassword'])"
                            style="border-radius: 10px; padding: 10px 25px;">
                        <i class="ri-close-line me-1"></i>Annuler
                    </button>
                    <button type="submit"
                            class="btn btn-danger"
                            wire:loading.attr="disabled"
                            style="border-radius: 10px; padding: 10px 25px;">
                        <span wire:loading.remove wire:target="deleteUser">
                            <i class="ri-delete-bin-line me-1"></i>Supprimer Définitivement
                        </span>
                        <span wire:loading wire:target="deleteUser">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Suppression...
                        </span>
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
