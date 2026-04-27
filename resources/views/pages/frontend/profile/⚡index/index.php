<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Customer;
use App\Models\Cotisation;
use App\Models\Paiement;
use App\Models\TypeCotisation;
use App\Models\CoutEngagement;
use App\Models\HistoriqueCotisation;
use App\Models\DemandeChangeCotisationMensuel;
use App\Traits\UtilsSweetAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

new #[Layout('layouts.app-frontend')] class extends Component
{
    use UtilsSweetAlert, WithFileUploads;

    /* ── Formulaire édition ─────────────────────────────── */
    public string $nom     = '';
    public string $prenom  = '';
    public string $adresse = '';
    public string $phone   = '';
    public string $errorNom    = '';
    public string $errorPrenom = '';

    /* ── Photo de profil ────────────────────────────────── */
    public $photoFile       = null;  // fichier uploadé (Livewire WithFileUploads)
    public bool $photoReady = false; // true = photo sélectionnée, prête à sauvegarder

    /* ── Cotisation mensuelle ───────────────────────────── */
    public ?int   $typeCotisationMensuelId   = null;
    public ?int   $montantEngagement         = null;
    public bool   $showConfirmChangementType = false;
    public string $confirmChangementMessage  = '';
    public ?int   $nouvelEngagement          = null;
    public string $errorEngagement           = '';

    /* ── Demande changement cotisation mensuel ─────────── */
    public bool   $showDemandeChange        = false;
    public string $typeDemande              = '';
    public ?int   $demandeNouveauTypeId     = null;
    public ?int   $demandeNouvelEngagement  = null;
    public bool   $demandeSupprimerRetard   = false;
    public string $demandeMotif             = '';
    public string $errorTypeDemande         = '';
    public string $errorNouveauType         = '';
    public string $errorNouvelEngagement    = '';

        /* ── Bilan PDF ──────────────────────────────────────── */
    public bool   $showBilan   = false;
    public string $bilanDebut  = '';
    public string $bilanFin    = '';
    public bool   $bilanTout   = false;

    public function mount(): void
    {
        $c = auth('customer')->user();
        $this->nom                     = $c->nom;
        $this->prenom                  = $c->prenom;
        $this->adresse                 = $c->adresse ?? '';
        $this->phone                   = $c->phone   ?? '';
        $this->typeCotisationMensuelId = $c->type_cotisation_mensuel_id;
        $this->montantEngagement       = $c->montant_engagement;
    }

    /* ═══════════════════════════════════════════════════════
       MODAUX
    ═══════════════════════════════════════════════════════ */
    public function openEdit(): void
    {
        $c = auth('customer')->user();
        $this->nom                       = $c->nom;
        $this->prenom                    = $c->prenom;
        $this->adresse                   = $c->adresse ?? '';
        $this->phone                     = $c->phone   ?? '';
        $this->typeCotisationMensuelId   = $c->type_cotisation_mensuel_id;
        $this->montantEngagement         = $c->montant_engagement;
        $this->errorNom                  = '';
        $this->errorPrenom               = '';
        $this->errorEngagement           = '';
        $this->showConfirmChangementType = false;
        $this->confirmChangementMessage  = '';
        $this->nouvelEngagement          = null;
        $this->dispatch('OpenEditModal');
    }

    public function closeEdit(): void
    {
        $this->showConfirmChangementType = false;
        $this->errorEngagement           = '';
        $this->dispatch('closeEditModal');
    }

    public function openDemandeChange(): void
    {
        $c = auth('customer')->user();

        $dejaEnAttente = DemandeChangeCotisationMensuel::where('customer_id', $c->id)
            ->where('statut', 'en_attente')->exists();

        if ($dejaEnAttente) {
            $this->send_event_at_sweet_alert_not_timer(
                'Demande en cours',
                "Vous avez déjà une demande en attente de validation. Veuillez patienter.",
                'info'
            );
            return;
        }

        $this->typeDemande             = '';
        $this->demandeNouveauTypeId    = null;
        $this->demandeNouvelEngagement = null;
        $this->demandeSupprimerRetard  = false;
        $this->demandeMotif            = '';
        $this->errorTypeDemande        = '';
        $this->errorNouveauType        = '';
        $this->errorNouvelEngagement   = '';
        $this->showDemandeChange       = true;
        $this->dispatch('OpenDemandeChangeModal');
    }

    public function closeDemandeChange(): void
    {
        $this->showDemandeChange = false;
        $this->dispatch('closeDemandeChangeModal');
    }

    public function selectDemandeNouveauType(?int $id): void
    {
        $this->demandeNouveauTypeId    = $id;
        $this->demandeNouvelEngagement = null;
        $this->errorNouveauType        = '';
        $this->errorNouvelEngagement   = '';
    }

    public function selectDemandeNouvelEngagement(?int $montant): void
    {
        $this->demandeNouvelEngagement = $montant;
        $this->errorNouvelEngagement   = '';
    }

    public function submitDemandeChange(): void
    {
        $this->errorTypeDemande      = '';
        $this->errorNouveauType      = '';
        $this->errorNouvelEngagement = '';

        $customer = Customer::find(auth('customer')->user()->id);

        if (! $this->typeDemande) {
            $this->errorTypeDemande = 'Veuillez choisir un type de demande.';
            return;
        }

        if ($this->typeDemande === 'changement') {
            if (! $this->demandeNouveauTypeId) {
                $this->errorNouveauType = 'Veuillez sélectionner le nouveau type de cotisation.';
                return;
            }
            if ($this->demandeNouveauTypeId === $customer->type_cotisation_mensuel_id) {
                $this->errorNouveauType = 'Vous avez sélectionné le même type que votre type actuel. Veuillez en choisir un autre.';
                return;
            }
            $tcNouveau = TypeCotisation::find($this->demandeNouveauTypeId);
            if (! $this->demandeNouvelEngagement || $this->demandeNouvelEngagement < 1) {
                $this->errorNouvelEngagement = "Veuillez renseigner votre nouveau montant d'engagement.";
                return;
            }
            if ($tcNouveau?->montant_minimum && $this->demandeNouvelEngagement < $tcNouveau->montant_minimum) {
                $this->errorNouvelEngagement = "Le minimum pour « {$tcNouveau->libelle} » est " .
                    number_format($tcNouveau->montant_minimum, 0, ',', ' ') . " FCFA/mois.";
                return;
            }
        }

        DemandeChangeCotisationMensuel::create([
            'customer_id'                  => $customer->id,
            'created_by'                   => null,
            'type_demande'                 => $this->typeDemande,
            'ancien_type_cotisation_id'    => $customer->type_cotisation_mensuel_id,
            'ancien_montant_engagement'    => $customer->montant_engagement,
            'nouveau_type_cotisation_id'   => $this->typeDemande === 'changement' ? $this->demandeNouveauTypeId   : null,
            'nouveau_montant_engagement'   => $this->typeDemande === 'changement' ? $this->demandeNouvelEngagement : null,
            'supprimer_cotisations_retard' => $this->demandeSupprimerRetard,
            'motif'                        => trim($this->demandeMotif) ?: null,
            'statut'                       => 'en_attente',
        ]);

        $this->showDemandeChange = false;
        $this->dispatch('closeDemandeChangeModal');
        $this->send_event_at_toast('Demande envoyée ! Elle sera traitée par l\'administration.', 'success', 'top-end');
    }

    public function openPhoto(): void
    {
        $this->photoFile  = null;
        $this->photoReady = false;
        $this->dispatch('OpenPhotoModal');
    }

    public function closePhoto(): void
    {
        $this->photoFile  = null;
        $this->photoReady = false;
        $this->dispatch('closePhotoModal');
    }

    /* ─────────────────────────────────────────────────────
       PHOTO — Sauvegarde
       - Redimensionne à 400×400 si GD est disponible
       - Stocke dans storage/app/public/avatars/{customer_id}.jpg
       - MAJ champ photo_path sur le customer
    ───────────────────────────────────────────────────── */
    public function sauvegarderPhoto(): void
    {
        $this->validate([
            'photoFile' => 'required|image|max:5120', // max 5 Mo
        ]);

        $customer = Customer::findOrFail(auth('customer')->user()->id);

        /* Supprimer l'ancienne photo si elle existe */
        if ($customer->photo_path && Storage::disk('public')->exists($customer->photo_path)) {
            Storage::disk('public')->delete($customer->photo_path);
        }

        /* Stocker la nouvelle dans public/avatars/ */
        $ext      = $this->photoFile->getClientOriginalExtension() ?: 'jpg';
        $filename = 'avatars/' . $customer->id . '_' . now()->format('YmdHis') . '.' . $ext;

        /* Tenter un redimensionnement GD vers 400×400 */
        try {
            $tmpPath = $this->photoFile->getRealPath();
            $mime    = $this->photoFile->getMimeType();

            $src = match($mime) {
                'image/jpeg', 'image/jpg' => imagecreatefromjpeg($tmpPath),
                'image/png'               => imagecreatefrompng($tmpPath),
                'image/webp'              => imagecreatefromwebp($tmpPath),
                default                   => null,
            };

            if ($src) {
                $dst = imagecreatetruecolor(400, 400);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, 400, 400, imagesx($src), imagesy($src));

                ob_start();
                imagejpeg($dst, null, 90);
                $imgData = ob_get_clean();
                imagedestroy($src);
                imagedestroy($dst);

                Storage::disk('public')->put('avatars/' . $customer->id . '_' . now()->format('YmdHis') . '.jpg', $imgData);
                $filename = 'avatars/' . $customer->id . '_' . now()->format('YmdHis') . '.jpg';
            } else {
                /* GD ne supporte pas ce format → stocker brut */
                $filename = $this->photoFile->storeAs('avatars', $customer->id . '_' . now()->format('YmdHis') . '.' . $ext, 'public');
            }
        } catch (\Throwable $e) {
            /* Fallback sans redimensionnement */
            $filename = $this->photoFile->storeAs('avatars', $customer->id . '_' . now()->format('YmdHis') . '.' . $ext, 'public');
        }

        $customer->update([
            'photo_path' => $filename,
            'photo_url'  => Storage::disk('public')->url($filename),
            ]);

        $this->photoFile  = null;
        $this->photoReady = false;
        $this->dispatch('closePhotoModal');
        $this->dispatch('photoSauvegardee', path: Storage::disk('public')->url($filename));
        $this->send_event_at_toast('Photo de profil mise à jour !', 'success', 'top-end');
    }

    public function supprimerPhoto(): void
    {
        $customer = Customer::findOrFail(auth('customer')->user()->id);

        if ($customer->photo_path && Storage::disk('public')->exists($customer->photo_path)) {
            Storage::disk('public')->delete($customer->photo_path);
        }

        $customer->update([
            'photo_path' => null,
            'photo_url'  => null
            ]);
        $this->dispatch('closePhotoModal');
        $this->dispatch('photoSupprimee');
        $this->send_event_at_toast('Photo supprimée.', 'success', 'top-end');
    }

    /* ═══════════════════════════════════════════════════════
       BILAN PDF
    ═══════════════════════════════════════════════════════ */
    public function openBilan(): void
    {
        $this->bilanDebut = now()->startOfYear()->format('Y-m-d');
        $this->bilanFin   = now()->format('Y-m-d');
        $this->bilanTout  = false;
        $this->showBilan  = true;
        $this->dispatch('OpenBilanModal');
    }

    public function closeBilan(): void
    {
        $this->showBilan = false;
        $this->dispatch('closeBilanModal');
    }

    public function telechargerBilan()
    {
        $customer = Customer::with(['typeCotisationMensuel'])->findOrFail(auth('customer')->user()->id);

        $debut = $this->bilanTout ? null : Carbon::parse($this->bilanDebut)->startOfDay();
        $fin   = $this->bilanTout ? null : Carbon::parse($this->bilanFin)->endOfDay();

        $cotisations = Cotisation::with('typeCotisation')
            ->where('customer_id', $customer->id)
            ->when(! $this->bilanTout, fn($q) => $q->whereBetween('created_at', [$debut, $fin]))
            ->orderByDesc('annee')->orderByDesc('mois')
            ->get();

        $paiements = Paiement::where('customer_id', $customer->id)
            ->where('statut', 'success')
            ->when(! $this->bilanTout, fn($q) => $q->whereBetween('date_paiement', [$debut, $fin]))
            ->orderByDesc('date_paiement')
            ->get();

        $totalPaye    = $paiements->sum('montant');
        $totalDu      = $cotisations->whereIn('statut', ['en_retard', 'partiel'])->sum('montant_restant');
        $totalRestant = $cotisations->sum('montant_restant');
        $nbAjour      = $cotisations->where('statut', 'a_jour')->count();
        $nbRetard     = $cotisations->where('statut', 'en_retard')->count();
        $nbPartiel    = $cotisations->where('statut', 'partiel')->count();

        $periode = $this->bilanTout
            ? "Tout l'historique"
            : Carbon::parse($this->bilanDebut)->translatedFormat('d F Y') . ' au ' .
              Carbon::parse($this->bilanFin)->translatedFormat('d F Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.frontend.bilan-fidele', compact(
            'customer', 'cotisations', 'paiements',
            'totalPaye', 'totalDu', 'totalRestant',
            'nbAjour', 'nbRetard', 'nbPartiel',
            'periode'
        ))->setPaper('a4');

        $this->dispatch('closeBilanModal');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            "bilan-{$customer->nom}-{$customer->prenom}-" . now()->format('Ymd') . ".pdf"
        );
    }

    /* ═══════════════════════════════════════════════════════
       SÉLECTION TYPE MENSUEL
    ═══════════════════════════════════════════════════════ */
    public function selectTypeMensuel(?int $id): void
    {
        $this->typeCotisationMensuelId   = $id;
        $this->showConfirmChangementType = false;
        $this->confirmChangementMessage  = '';
        $this->nouvelEngagement          = null;
        $this->errorEngagement           = '';
        if (! $id) {
            $this->montantEngagement = auth('customer')->user()->montant_engagement;
        }
    }

    public function selectEngagement(?int $montant): void
    {
        $this->montantEngagement = $montant;
        $this->errorEngagement   = '';
    }

    public function selectNouvelEngagement(?int $montant): void
    {
        $this->nouvelEngagement = $montant;
        $this->errorEngagement  = '';
    }

    /* ═══════════════════════════════════════════════════════
       SAVE EDIT
    ═══════════════════════════════════════════════════════ */
    public function saveEdit(): void
    {
        $this->errorNom        = '';
        $this->errorPrenom     = '';
        $this->errorEngagement = '';

        if (! trim($this->nom))    { $this->errorNom    = 'Le nom est obligatoire.'; }
        if (! trim($this->prenom)) { $this->errorPrenom = 'Le prénom est obligatoire.'; }
        if ($this->errorNom || $this->errorPrenom) return;

        $customer     = Customer::find(auth('customer')->user()->id);
        $ancienTypeId = $customer->type_cotisation_mensuel_id;

        $tcNouveau = $this->typeCotisationMensuelId
            ? TypeCotisation::find($this->typeCotisationMensuelId)
            : null;

        $estChangementDeType = $tcNouveau && $ancienTypeId && $ancienTypeId !== $this->typeCotisationMensuelId;
        $estPremierType      = $tcNouveau && ! $ancienTypeId;

        if ($estChangementDeType && ! $this->showConfirmChangementType) {
            $ancienType = TypeCotisation::find($ancienTypeId);
            $minLabel   = $tcNouveau->montant_minimum
                ? ' (minimum ' . number_format($tcNouveau->montant_minimum, 0, ',', ' ') . ' FCFA/mois)' : '';
            $this->showConfirmChangementType = true;
            $this->nouvelEngagement          = null;
            $this->errorEngagement           = '';
            $this->confirmChangementMessage  =
                "Vous êtes actuellement en « {$ancienType?->libelle} » avec " .
                number_format($customer->montant_engagement ?? 0, 0, ',', ' ') .
                " FCFA/mois. Vous migrez vers « {$tcNouveau->libelle} »{$minLabel}. " .
                "Renseignez votre nouveau montant d'engagement mensuel.";
            return;
        }

        if ($estChangementDeType && $this->showConfirmChangementType) {
            if (! $this->nouvelEngagement || $this->nouvelEngagement < 1) {
                $this->errorEngagement = "Veuillez renseigner votre nouveau montant d'engagement.";
                return;
            }
            if ($tcNouveau->montant_minimum && $this->nouvelEngagement < $tcNouveau->montant_minimum) {
                $this->errorEngagement = "Le minimum pour « {$tcNouveau->libelle} » est " . number_format($tcNouveau->montant_minimum, 0, ',', ' ') . " FCFA/mois.";
                return;
            }
            $this->montantEngagement = $this->nouvelEngagement;
        }

        if ($estPremierType) {
            if (! $this->montantEngagement || $this->montantEngagement < 1) {
                $this->errorEngagement = "Veuillez renseigner votre montant d'engagement mensuel.";
                return;
            }
            if ($tcNouveau->montant_minimum && $this->montantEngagement < $tcNouveau->montant_minimum) {
                $this->errorEngagement = "Le minimum pour « {$tcNouveau->libelle} » est " . number_format($tcNouveau->montant_minimum, 0, ',', ' ') . " FCFA/mois.";
                return;
            }
        }

        $customer->update([
            'nom'                        => strtoupper(trim($this->nom)),
            'prenom'                     => ucwords(strtolower(trim($this->prenom))),
            'adresse'                    => trim($this->adresse) ?: null,
            'phone'                      => trim($this->phone) ?: $customer->phone,
            'type_cotisation_mensuel_id' => $tcNouveau?->id,
            'montant_engagement'         => $tcNouveau ? $this->montantEngagement : null,
        ]);

        if ($tcNouveau && ($estPremierType || $estChangementDeType)) {
            $existeDejaCeMois = Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $tcNouveau->id)
                ->where('mois', now()->month)->where('annee', now()->year)->exists();

            if (! $existeDejaCeMois) {
                $cot = Cotisation::create([
                    'customer_id'        => $customer->id,
                    'type_cotisation_id' => $tcNouveau->id,
                    'mois'               => now()->month,
                    'annee'              => now()->year,
                    'montant_du'         => $this->montantEngagement,
                    'montant_paye'       => 0,
                    'montant_restant'    => $this->montantEngagement,
                    'statut'             => 'en_retard',
                    'mode_paiement'      => null, 'reference' => null,
                    'validated_by'       => null, 'validated_at' => null,
                ]);
                HistoriqueCotisation::log($cot, 'creation', $this->montantEngagement,
                    $estChangementDeType ? 'Première cotisation suite au changement de type' : 'Première cotisation mensuelle');
            }
        }

        $this->showConfirmChangementType = false;
        $this->dispatch('closeEditModal');
        $this->send_event_at_toast('Informations mises à jour !', 'success', 'top-end');
    }

    /* ── Déconnexion ────────────────────────────────────── */
    public function deconnexion(): void
    {
        auth('customer')->logout();
        $this->redirect(route('login-user'));
    }

    /* ── Données vue ────────────────────────────────────── */
    public function with(): array
    {
        $customer = Customer::with('typeCotisationMensuel')
            ->find(auth('customer')->user()->id);

        $totalCotise            = Paiement::where('customer_id', $customer->id)->where('statut', 'success')->sum('montant');
        $totalDu                = Cotisation::where('customer_id', $customer->id)->whereIn('statut', ['en_retard', 'partiel'])->sum('montant_restant');
        $nbPaiements            = Paiement::where('customer_id', $customer->id)->count();
        $moisRetard             = Cotisation::where('customer_id', $customer->id)->where('statut', 'en_retard')->count();
        $nbDocuments            = $customer->documents()->count();
        $nbReclammationsEnCours = $customer->reclammation()->whereIn('status', ['ouverte', 'en_cours'])->count();

        $initiales = strtoupper(substr($customer->prenom, 0, 1) . substr($customer->nom, 0, 1));

        $typesMensuels   = TypeCotisation::where('type', 'mensuel')->where('is_required', true)->where('status', 'actif')->orderBy('libelle')->get();
        $coutEngagements = CoutEngagement::actif()->orderBy('montant')->get();

        /* URL photo de profil */
        $photoUrl = $customer->photo_path
            ? Storage::disk('public')->url($customer->photo_path)
            : null;

        $demandeEnAttente = DemandeChangeCotisationMensuel::where('customer_id', $customer->id)
            ->where('statut', 'en_attente')
            ->with(['ancienType', 'nouveauType'])
            ->latest()->first();

        $nbRetardAncienType = $customer->type_cotisation_mensuel_id
            ? Cotisation::where('customer_id', $customer->id)
                ->where('type_cotisation_id', $customer->type_cotisation_mensuel_id)
                ->where('statut', 'en_retard')
                ->count()
            : 0;

        return compact(
            'customer', 'totalCotise', 'totalDu',
            'nbPaiements', 'moisRetard', 'nbDocuments',
            'nbReclammationsEnCours', 'initiales',
            'typesMensuels', 'coutEngagements',
            'photoUrl',
            'demandeEnAttente', 'nbRetardAncienType'
        );
    }
}
?>