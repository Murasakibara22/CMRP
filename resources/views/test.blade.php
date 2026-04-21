<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
  <meta name="theme-color" content="#405189"/>
  <title>Notifications — Espace Fidèle</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css"/>
  <link rel="stylesheet" href="pwa.css"/>
  <link rel="stylesheet" href="shell.css"/>
  <link rel="stylesheet" href="notifications.css"/>
</head>
<body>
<div class="app-shell">

  <!-- ══ SIDEBAR desktop ══════════════════════════════════ -->
  <aside class="sidebar">
    <div class="sb-logo">
      <div class="sb-logo-icon"><i class="ri-mosque-line"></i></div>
      <div>
        <div class="sb-logo-name">Espace Fidèle</div>
        <div class="sb-logo-sub">ISL Mosquée</div>
      </div>
    </div>
    <nav class="sb-nav">
      <button class="sb-item" onclick="window.location.href='accueil.html'"><i class="ri-home-5-line"></i> Accueil</button>
      <button class="sb-item" onclick="window.location.href='cotisations.html'"><i class="ri-calendar-check-line"></i> Cotisations</button>
      <button class="sb-item" onclick="window.location.href='ajout-cotisation.html'"><i class="ri-add-circle-line"></i> Nouvelle cotisation</button>
      <button class="sb-item" onclick="window.location.href='paiements.html'"><i class="ri-bank-card-line"></i> Paiements</button>
      <button class="sb-item active" onclick="window.location.href='notifications.html'">
        <i class="ri-notification-3-line"></i> Notifications
        <span class="sb-notif" id="sb-badge">3</span>
      </button>
      <button class="sb-item" onclick="window.location.href='profil.html'"><i class="ri-user-3-line"></i> Profil</button>
    </nav>
    <div class="sb-profile">
      <div class="sb-avatar">MK</div>
      <div>
        <div class="sb-profile-name">Moussa Koné</div>
        <div class="sb-profile-phone">+225 07 00 11 22</div>
      </div>
    </div>
  </aside>

  <!-- ══ MAIN WRAPPER ════════════════════════════════════ -->
  <div class="main-wrapper">

    <header class="app-bar">
      <div class="ab-left">
        <button class="ab-back" onclick="history.back()"><i class="ri-arrow-left-line"></i></button>
        <div class="ab-brand">
          <div class="ab-logo"><i class="ri-mosque-line"></i></div>
          <div class="ab-name">ISL Mosquée</div>
        </div>
      </div>
      <span class="ab-page-title">Notifications</span>
      <div class="ab-actions">
        <button class="notif-mark-all-btn" id="btn-mark-all" onclick="markAllRead()" title="Tout marquer comme lu">
          <i class="ri-check-double-line"></i>
          <span>Tout lire</span>
        </button>
      </div>
    </header>

    <!-- ══ CONTENU ══════════════════════════════════════ -->
    <main class="page-content">

      <!-- Résumé + actions -->
      <div class="notif-top-row">
        <div class="notif-summary">
          <span class="notif-unread-count" id="unread-count">3 non lues</span>
          <span class="notif-total">sur 8 notifications</span>
        </div>
        <button class="notif-clear-btn" onclick="confirmClearAll()">
          <i class="ri-delete-bin-line"></i> Tout effacer
        </button>
      </div>

      <!-- Filtres -->
      <div class="notif-filters">
        <button class="nf-btn active" data-filter="tous" onclick="filterNotif(this)">
          <i class="ri-list-check"></i> Toutes
        </button>
        <button class="nf-btn" data-filter="non_lu" onclick="filterNotif(this)">
          <i class="ri-checkbox-blank-circle-fill"></i> Non lues
          <span class="nf-badge" id="nf-badge-unread">3</span>
        </button>
        <button class="nf-btn" data-filter="cotisation" onclick="filterNotif(this)">
          <i class="ri-calendar-check-line"></i> Cotisations
        </button>
        <button class="nf-btn" data-filter="paiement" onclick="filterNotif(this)">
          <i class="ri-bank-card-line"></i> Paiements
        </button>
        <button class="nf-btn" data-filter="compte" onclick="filterNotif(this)">
          <i class="ri-shield-check-line"></i> Compte
        </button>
        <button class="nf-btn" data-filter="reclamation" onclick="filterNotif(this)">
          <i class="ri-flag-line"></i> Réclamations
        </button>
      </div>

      <!-- ── LISTE DES NOTIFICATIONS ── -->
      <div class="notif-list card" id="notif-list">

        <!-- ═══ NON LUES ═══ -->

        <!-- 1 -->
        <div class="notif-item unread" data-id="1" data-cat="cotisation" data-read="false"
          onclick="openDetail(this)"
          data-title="Cotisation en retard — Mars 2025"
          data-time="Il y a 2 jours"
          data-date="09/04/2025 à 08:15"
          data-body="Votre cotisation mensuelle de Mars 2025 est toujours impayée. Le montant dû est de 10 000 FCFA. Régularisez au plus tôt pour éviter un retard supplémentaire."
          data-action-label="Payer maintenant"
          data-action-href="ajout-cotisation.html"
          data-icon="ri-time-line"
          data-icon-bg="rgba(240,101,72,.10)"
          data-icon-color="#f06548">
          <div class="ni-unread-dot"></div>
          <div class="ni-icon" style="background:rgba(240,101,72,.10);color:#f06548">
            <i class="ri-time-line"></i>
          </div>
          <div class="ni-body">
            <div class="ni-title">Cotisation en retard — Mars 2025</div>
            <div class="ni-msg">Votre cotisation mensuelle de Mars 2025 est toujours impayée. Montant : 10 000 FCFA.</div>
            <div class="ni-meta">
              <span class="ni-time"><i class="ri-time-line"></i> Il y a 2 jours</span>
              <span class="ni-cat cotisation">Cotisation</span>
            </div>
          </div>
          <button class="ni-more" onclick="event.stopPropagation(); openItemMenu(this)" title="Options">
            <i class="ri-more-2-line"></i>
          </button>
        </div>

        <!-- 2 -->
        <div class="notif-item unread" data-id="2" data-cat="cotisation" data-read="false"
          onclick="openDetail(this)"
          data-title="Cotisation Avril 2025 disponible"
          data-time="Il y a 5 jours"
          data-date="06/04/2025 à 09:00"
          data-body="Votre cotisation du mois d'Avril 2025 est maintenant disponible. Montant : 10 000 FCFA. Pensez à régler avant la fin du mois pour rester à jour."
          data-action-label="Voir la cotisation"
          data-action-href="cotisations.html"
          data-icon="ri-calendar-event-line"
          data-icon-bg="rgba(247,184,75,.12)"
          data-icon-color="#f7b84b">
          <div class="ni-unread-dot"></div>
          <div class="ni-icon" style="background:rgba(247,184,75,.12);color:#f7b84b">
            <i class="ri-calendar-event-line"></i>
          </div>
          <div class="ni-body">
            <div class="ni-title">Cotisation Avril 2025 disponible</div>
            <div class="ni-msg">Votre cotisation du mois d'Avril 2025 est disponible. Montant : 10 000 FCFA.</div>
            <div class="ni-meta">
              <span class="ni-time"><i class="ri-time-line"></i> Il y a 5 jours</span>
              <span class="ni-cat cotisation">Cotisation</span>
            </div>
          </div>
          <button class="ni-more" onclick="event.stopPropagation(); openItemMenu(this)" title="Options">
            <i class="ri-more-2-line"></i>
          </button>
        </div>

        <!-- 3 -->
        <div class="notif-item unread" data-id="3" data-cat="reclamation" data-read="false"
          onclick="openDetail(this)"
          data-title="Réclamation reçue et en cours"
          data-time="Il y a 10 jours"
          data-date="01/04/2025 à 14:30"
          data-body="Votre réclamation du 28/03/2025 concernant la cotisation de Février 2025 a bien été enregistrée. Notre équipe va l'examiner dans les 48 heures. Vous serez notifié dès qu'une réponse sera disponible."
          data-action-label="Voir la réclamation"
          data-action-href="reclamations.html"
          data-icon="ri-flag-line"
          data-icon-bg="rgba(41,156,219,.12)"
          data-icon-color="#299cdb">
          <div class="ni-unread-dot"></div>
          <div class="ni-icon" style="background:rgba(41,156,219,.12);color:#299cdb">
            <i class="ri-flag-line"></i>
          </div>
          <div class="ni-body">
            <div class="ni-title">Réclamation reçue et en cours</div>
            <div class="ni-msg">Votre réclamation du 28/03/2025 est enregistrée et en cours de traitement par l'équipe.</div>
            <div class="ni-meta">
              <span class="ni-time"><i class="ri-time-line"></i> Il y a 10 jours</span>
              <span class="ni-cat reclamation">Réclamation</span>
            </div>
          </div>
          <button class="ni-more" onclick="event.stopPropagation(); openItemMenu(this)" title="Options">
            <i class="ri-more-2-line"></i>
          </button>
        </div>

        <!-- ═══ LUES ═══ -->

        <!-- 4 -->
        <div class="notif-item" data-id="4" data-cat="paiement" data-read="true"
          onclick="openDetail(this)"
          data-title="Paiement confirmé — Février 2025"
          data-time="Il y a 1 mois"
          data-date="01/03/2025 à 10:22"
          data-body="Votre paiement de cotisation de Février 2025 a bien été enregistré et validé. Montant : 10 000 FCFA via Espèces. Référence : ESP202502006. Merci pour votre régularité !"
          data-action-label="Voir le paiement"
          data-action-href="paiements.html"
          data-icon="ri-checkbox-circle-line"
          data-icon-bg="rgba(10,179,156,.10)"
          data-icon-color="#0ab39c">
          <div class="ni-read-dot"></div>
          <div class="ni-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
            <i class="ri-checkbox-circle-line"></i>
          </div>
          <div class="ni-body">
            <div class="ni-title">Paiement confirmé — Février 2025</div>
            <div class="ni-msg">Votre paiement de 10 000 FCFA pour Février 2025 a été validé. Merci !</div>
            <div class="ni-meta">
              <span class="ni-time"><i class="ri-time-line"></i> Il y a 1 mois</span>
              <span class="ni-cat paiement">Paiement</span>
            </div>
          </div>
          <button class="ni-more" onclick="event.stopPropagation(); openItemMenu(this)" title="Options">
            <i class="ri-more-2-line"></i>
          </button>
        </div>

        <!-- 5 -->
        <div class="notif-item" data-id="5" data-cat="reclamation" data-read="true"
          onclick="openDetail(this)"
          data-title="Réclamation résolue"
          data-time="Il y a 2 mois"
          data-date="18/02/2025 à 16:00"
          data-body="Votre réclamation concernant l'erreur de montant de Janvier 2025 a été résolue. Après vérification, le surplus de 5 000 FCFA a été crédité sur votre prochain mois. Votre compte est à jour."
          data-action-label="Voir les réclamations"
          data-action-href="reclamations.html"
          data-icon="ri-check-double-line"
          data-icon-bg="rgba(10,179,156,.10)"
          data-icon-color="#0ab39c">
          <div class="ni-read-dot"></div>
          <div class="ni-icon" style="background:rgba(10,179,156,.10);color:#0ab39c">
            <i class="ri-check-double-line"></i>
          </div>
          <div class="ni-body">
            <div class="ni-title">Réclamation résolue</div>
            <div class="ni-msg">Votre réclamation de Janvier 2025 a été résolue. Surplus de 5 000 FCFA crédité.</div>
            <div class="ni-meta">
              <span class="ni-time"><i class="ri-time-line"></i> Il y a 2 mois</span>
              <span class="ni-cat reclamation">Réclamation</span>
            </div>
          </div>
          <button class="ni-more" onclick="event.stopPropagation(); openItemMenu(this)" title="Options">
            <i class="ri-more-2-line"></i>
          </button>
        </div>

        <!-- 6 -->
        <div class="notif-item" data-id="6" data-cat="compte" data-read="true"
          onclick="openDetail(this)"
          data-title="Compte validé par l'administrateur"
          data-time="Il y a 2 mois"
          data-date="15/02/2025 à 11:05"
          data-body="Votre compte Espace Fidèle a été validé manuellement par l'administrateur. Vous pouvez désormais accéder à toutes les fonctionnalités : suivi des cotisations, paiements, réclamations. Bienvenue dans la communauté !"
          data-action-label="Aller à l'accueil"
          data-action-href="accueil.html"
          data-icon="ri-shield-check-line"
          data-icon-bg="rgba(64,81,137,.10)"
          data-icon-color="#405189">
          <div class="ni-read-dot"></div>
          <div class="ni-icon" style="background:rgba(64,81,137,.10);color:#405189">
            <i class="ri-shield-check-line"></i>
          </div>
          <div class="ni-body">
            <div class="ni-title">Compte validé par l'administrateur</div>
            <div class="ni-msg">Votre compte a été validé. Accédez à toutes les fonctionnalités de l'espace fidèle.</div>
            <div class="ni-meta">
              <span class="ni-time"><i class="ri-time-line"></i> Il y a 2 mois</span>
              <span class="ni-cat compte">Compte</span>
            </div>
          </div>
          <button class="ni-more" onclick="event.stopPropagation(); openItemMenu(this)" title="Options">
            <i class="ri-more-2-line"></i>
          </button>
        </div>

        <!-- 7 -->
        <div class="notif-item" data-id="7" data-cat="cotisation" data-read="true"
          onclick="openDetail(this)"
          data-title="Rappel — Cotisation de Janvier 2025"
          data-time="Il y a 3 mois"
          data-date="25/01/2025 à 08:00"
          data-body="Rappel automatique : votre cotisation de Janvier 2025 est en retard. Montant dû : 10 000 FCFA. Pensez à régulariser votre situation pour maintenir un bon historique de paiement."
          data-action-label="Payer maintenant"
          data-action-href="ajout-cotisation.html"
          data-icon="ri-alarm-warning-line"
          data-icon-bg="rgba(240,101,72,.08)"
          data-icon-color="#f06548">
          <div class="ni-read-dot"></div>
          <div class="ni-icon" style="background:rgba(240,101,72,.08);color:#f06548">
            <i class="ri-alarm-warning-line"></i>
          </div>
          <div class="ni-body">
            <div class="ni-title">Rappel — Cotisation de Janvier 2025</div>
            <div class="ni-msg">Rappel automatique : cotisation de Janvier 2025 en retard. 10 000 FCFA dus.</div>
            <div class="ni-meta">
              <span class="ni-time"><i class="ri-time-line"></i> Il y a 3 mois</span>
              <span class="ni-cat cotisation">Cotisation</span>
            </div>
          </div>
          <button class="ni-more" onclick="event.stopPropagation(); openItemMenu(this)" title="Options">
            <i class="ri-more-2-line"></i>
          </button>
        </div>

        <!-- 8 -->
        <div class="notif-item" data-id="8" data-cat="compte" data-read="true"
          onclick="openDetail(this)"
          data-title="Bienvenue sur l'Espace Fidèle"
          data-time="Il y a 2 ans"
          data-date="15/01/2023 à 10:00"
          data-body="Bienvenue, Moussa Koné ! Votre inscription a bien été enregistrée. Votre compte est en cours de validation par un administrateur. Vous serez notifié dès que votre compte sera activé. Merci de rejoindre la communauté ISL Mosquée."
          data-action-label="Découvrir l'application"
          data-action-href="accueil.html"
          data-icon="ri-hand-heart-line"
          data-icon-bg="rgba(64,81,137,.10)"
          data-icon-color="#405189">
          <div class="ni-read-dot"></div>
          <div class="ni-icon" style="background:rgba(64,81,137,.10);color:#405189">
            <i class="ri-hand-heart-line"></i>
          </div>
          <div class="ni-body">
            <div class="ni-title">Bienvenue sur l'Espace Fidèle</div>
            <div class="ni-msg">Inscription enregistrée. Votre compte est en cours de validation par l'administrateur.</div>
            <div class="ni-meta">
              <span class="ni-time"><i class="ri-time-line"></i> Il y a 2 ans</span>
              <span class="ni-cat compte">Compte</span>
            </div>
          </div>
          <button class="ni-more" onclick="event.stopPropagation(); openItemMenu(this)" title="Options">
            <i class="ri-more-2-line"></i>
          </button>
        </div>

      </div><!-- /notif-list -->

      <!-- Empty state (filtre vide) -->
      <div class="notif-empty" id="notif-empty" style="display:none">
        <div class="ne-icon"><i class="ri-notification-off-line"></i></div>
        <div class="ne-title">Aucune notification</div>
        <div class="ne-sub">Aucune notification dans cette catégorie.</div>
      </div>

      <div style="height:24px"></div>
    </main>

    <nav class="bottom-bar">
      <button class="bb-item" onclick="window.location.href='accueil.html'"><i class="ri-home-5-line"></i><span>Accueil</span></button>
      <button class="bb-item" onclick="window.location.href='cotisations.html'"><i class="ri-calendar-check-line"></i><span>Cotisations</span></button>
      <div class="bb-fab-wrap"><button class="bb-fab" onclick="window.location.href='ajout-cotisation.html'"><i class="ri-add-line"></i></button></div>
      <button class="bb-item" onclick="window.location.href='paiements.html'"><i class="ri-bank-card-line"></i><span>Paiements</span></button>
      <button class="bb-item" onclick="window.location.href='profil.html'"><i class="ri-user-3-line"></i><span>Profil</span></button>
    </nav>

  </div><!-- /main-wrapper -->
</div><!-- /app-shell -->


<!-- ══════════════════════════════════════════════════════
     MENU CONTEXTUEL (options par notif)
══════════════════════════════════════════════════════ -->
<div class="ni-ctx-menu" id="ctx-menu">
  <button class="ni-ctx-item" id="ctx-mark" onclick="ctxMarkRead()">
    <i class="ri-eye-line"></i> Marquer comme lu
  </button>
  <button class="ni-ctx-item" onclick="ctxDelete()">
    <i class="ri-delete-bin-line"></i> Supprimer
  </button>
</div>
<div class="ni-ctx-overlay" id="ctx-overlay" onclick="closeCtxMenu()"></div>


<!-- ══════════════════════════════════════════════════════
     MODAL DÉTAIL NOTIFICATION
══════════════════════════════════════════════════════ -->
<div class="notif-modal-overlay" id="notif-modal-overlay" onclick="closeDetail()">
  <div class="notif-modal" onclick="event.stopPropagation()">

    <!-- Header -->
    <div class="nm-header" id="nm-header">
      <div class="nm-drag"></div>
      <div class="nm-header-row">
        <div class="nm-icon-wrap" id="nm-icon-wrap">
          <div class="nm-icon" id="nm-icon"><i class="ri-notification-3-line"></i></div>
        </div>
        <div class="nm-header-info">
          <div class="nm-cat-badge" id="nm-cat-badge">—</div>
          <div class="nm-date" id="nm-date">—</div>
        </div>
        <button class="nm-close" onclick="closeDetail()"><i class="ri-close-line"></i></button>
      </div>
    </div>

    <!-- Corps -->
    <div class="nm-body">

      <!-- Titre -->
      <div class="nm-title" id="nm-title">—</div>

      <!-- Message complet -->
      <div class="nm-section-label">Message</div>
      <div class="nm-message-box" id="nm-message">—</div>

    </div>

    <!-- Footer -->
    <div class="nm-footer">
      <button class="nm-btn-secondary" onclick="closeDetail()">
        <i class="ri-close-line"></i> Fermer
      </button>
      <button class="nm-btn-primary" id="nm-action-btn" onclick="goToAction()">
        <i class="ri-arrow-right-line"></i> <span id="nm-action-label">Voir</span>
      </button>
    </div>

  </div>
</div>

<script src="notifications.js"></script>
</body>
</html>
