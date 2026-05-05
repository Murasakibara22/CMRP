<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

new class extends Component
{
    use WithFileUploads;

    /* ── Étape active ── */
    public string $activeTab = 'infos'; // infos | password

    /* ── Infos personnelles ── */
    public string $nom      = '';
    public string $prenom   = '';
    public string $email    = '';
    public string $dialCode = '+225';
    public string $phone    = '';
    public $photoFile       = null;
    public bool   $photoReady = false;

    public string $errorNom    = '';
    public string $errorPrenom = '';
    public string $errorEmail  = '';

    /* ── Changement mot de passe ── */
    /* Étapes : form | otp_sent | otp_verified */
    public string $mdpStep        = 'form';
    public string $ancienMdp      = '';
    public string $nouveauMdp     = '';
    public string $confirmMdp     = '';
    public string $otpSaisi       = '';

    public string $errorAncienMdp  = '';
    public string $errorNouveauMdp = '';
    public string $errorConfirmMdp = '';
    public string $errorOtp        = '';
    public string $successMsg      = '';

    public function mount(): void
    {
        $u = auth()->user();
        $this->nom      = $u->nom;
        $this->prenom   = $u->prenom;
        $this->email    = $u->email;
        $this->dialCode = $u->dial_code ?? '+225';
        $this->phone    = $u->phone    ?? '';
    }

    /* ═══════════════════════════════════════════════════════
       INFOS PERSONNELLES
    ═══════════════════════════════════════════════════════ */
    public function sauvegarderInfos(): void
    {
        $this->errorNom    = '';
        $this->errorPrenom = '';
        $this->errorEmail  = '';

        if (! trim($this->nom))    { $this->errorNom    = 'Le nom est obligatoire.'; }
        if (! trim($this->prenom)) { $this->errorPrenom = 'Le prénom est obligatoire.'; }
        if (! trim($this->email))  { $this->errorEmail  = 'L\'email est obligatoire.'; }

        if ($this->errorNom || $this->errorPrenom || $this->errorEmail) return;

        /* Vérifier unicité email (sauf pour l'user courant) */
        $emailExiste = User::where('email', $this->email)
            ->where('id', '!=', auth()->id())
            ->exists();

        if ($emailExiste) {
            $this->errorEmail = 'Cet email est déjà utilisé par un autre administrateur.';
            return;
        }

        auth()->user()->update([
            'nom'      => strtoupper(trim($this->nom)),
            'prenom'   => ucwords(strtolower(trim($this->prenom))),
            'email'    => strtolower(trim($this->email)),
            'dial_code'=> $this->dialCode,
            'phone'    => trim($this->phone) ?: null,
        ]);

        $this->successMsg = 'Informations mises à jour avec succès.';
    }

    /* ── Photo de profil ── */
    public function sauvegarderPhoto(): void
    {
        $this->validate(['photoFile' => 'required|image|max:5120']);

        $user = auth()->user();

        /* Supprimer l'ancienne */
        if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
            Storage::disk('public')->delete($user->photo_url);
        }

        /* Redimensionner via GD si disponible */
        $ext     = $this->photoFile->getClientOriginalExtension() ?: 'jpg';
        $filename = 'admin-avatars/' . $user->id . '_' . now()->format('YmdHis');

        try {
            $tmp  = $this->photoFile->getRealPath();
            $mime = $this->photoFile->getMimeType();
            $src  = match($mime) {
                'image/jpeg', 'image/jpg' => imagecreatefromjpeg($tmp),
                'image/png'               => imagecreatefrompng($tmp),
                'image/webp'              => imagecreatefromwebp($tmp),
                default                   => null,
            };
            if ($src) {
                $dst = imagecreatetruecolor(400, 400);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, 400, 400, imagesx($src), imagesy($src));
                ob_start();
                imagejpeg($dst, null, 90);
                $data = ob_get_clean();
                imagedestroy($src); imagedestroy($dst);
                Storage::disk('public')->put($filename . '.jpg', $data);
                $stored = $filename . '.jpg';
            } else {
                $stored = $this->photoFile->storeAs('admin-avatars', $user->id . '_' . now()->format('YmdHis') . '.' . $ext, 'public');
            }
        } catch (\Throwable) {
            $stored = $this->photoFile->storeAs('admin-avatars', $user->id . '_' . now()->format('YmdHis') . '.' . $ext, 'public');
        }

        $user->update(['photo_url' => $stored]);
        $this->photoFile  = null;
        $this->photoReady = false;
        $this->dispatch('photoAdminSauvegardee', url: Storage::disk('public')->url($stored));
        $this->successMsg = 'Photo de profil mise à jour.';
    }

    public function supprimerPhoto(): void
    {
        $user = auth()->user();
        if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
            Storage::disk('public')->delete($user->photo_url);
        }
        $user->update(['photo_url' => null]);
        $this->dispatch('photoAdminSupprimee');
        $this->successMsg = 'Photo supprimée.';
    }

    /* ═══════════════════════════════════════════════════════
       MOT DE PASSE — ÉTAPE 1 : Vérifier ancien + envoyer OTP
    ═══════════════════════════════════════════════════════ */
    public function envoyerOtpMdp(): void
    {
        $this->errorAncienMdp  = '';
        $this->errorNouveauMdp = '';
        $this->errorConfirmMdp = '';

        /* Vérifier ancien mot de passe */
        if (! $this->ancienMdp) {
            $this->errorAncienMdp = 'Veuillez saisir votre mot de passe actuel.';
            return;
        }

        if (! Hash::check($this->ancienMdp, auth()->user()->password)) {
            $this->errorAncienMdp = 'Mot de passe actuel incorrect.';
            return;
        }

        /* Vérifier nouveau mot de passe */
        if (strlen($this->nouveauMdp) < 8) {
            $this->errorNouveauMdp = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
            return;
        }

        if ($this->nouveauMdp !== $this->confirmMdp) {
            $this->errorConfirmMdp = 'Les mots de passe ne correspondent pas.';
            return;
        }

        if ($this->nouveauMdp === $this->ancienMdp) {
            $this->errorNouveauMdp = 'Le nouveau mot de passe doit être différent de l\'ancien.';
            return;
        }

        /* Générer et envoyer l'OTP */
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        auth()->user()->update([
            'otp'             => $otp,
            'otp_verified_at' => null,
            'otp_expired_at'  => now()->addMinutes(10),
        ]);

        Mail::to(auth()->user()->email)->send(
            new OtpMail($otp, auth()->user()->prenom . ' ' . auth()->user()->nom)
        );

        $this->mdpStep  = 'otp_sent';
        $this->otpSaisi = '';
        $this->errorOtp = '';
    }

    /* ── Renvoi OTP ── */
    public function renvoyerOtp(): void
    {
        $user = auth()->user();
        $otp  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp'             => $otp,
            'otp_verified_at' => null,
            'otp_expired_at'  => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(
            new OtpMail($otp, $user->prenom . ' ' . $user->nom)
        );

        $this->otpSaisi = '';
        $this->errorOtp = 'Un nouveau code a été envoyé à ' . $user->email . '.';
    }

    /* ═══════════════════════════════════════════════════════
       MOT DE PASSE — ÉTAPE 2 : Vérifier OTP + changer mdp
    ═══════════════════════════════════════════════════════ */
    public function confirmerOtpMdp(): void
    {
        $this->errorOtp = '';
        $user = auth()->user();

        $code = preg_replace('/\D/', '', $this->otpSaisi);

        if (strlen($code) < 6) {
            $this->errorOtp = 'Veuillez entrer le code complet à 6 chiffres.';
            return;
        }

        if ($user->otp !== $code) {
            $this->errorOtp = 'Code incorrect. Vérifiez votre email et réessayez.';
            return;
        }

        if (! $user->isOtpValid()) {
            $this->errorOtp = 'Ce code a expiré. Veuillez en demander un nouveau.';
            return;
        }

        /* OTP valide → changer le mot de passe */
        $user->update([
            'password'                         => Hash::make($this->nouveauMdp),
            'is_first_connexion'               => false,
            'first_connexion_modal_dismissed_at'=> null,
            'otp'                              => null,
            'otp_verified_at'                  => now(),
            'otp_expired_at'                   => null,
        ]);

        /* Reset formulaire */
        $this->ancienMdp  = '';
        $this->nouveauMdp = '';
        $this->confirmMdp = '';
        $this->otpSaisi   = '';
        $this->mdpStep    = 'form';
        $this->successMsg = 'Mot de passe modifié avec succès.';

        /* Notifier le SFC first-login pour fermer le modal */
        $this->dispatch('passwordChanged');
    }

    /* ── Annuler le changement de mdp ── */
    public function annulerChangementMdp(): void
    {
        $this->mdpStep    = 'form';
        $this->ancienMdp  = '';
        $this->nouveauMdp = '';
        $this->confirmMdp = '';
        $this->otpSaisi   = '';
        $this->errorAncienMdp  = '';
        $this->errorNouveauMdp = '';
        $this->errorConfirmMdp = '';
        $this->errorOtp        = '';
        /* Nettoyer l'OTP en base */
        auth()->user()->clearOtp();
    }

    public function with(): array
    {
        $user     = auth()->user();
        $initiales = strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1));
        $photoUrl  = $user->photo_url
            ? Storage::disk('public')->url($user->photo_url)
            : null;

        return compact('user', 'initiales', 'photoUrl');
    }
};
?>
