<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            /* ── DASHBOARD ───────────────────────────────────── */
            ['code' => 'DASHBOARD_SHOW',    'libelle' => 'Consulter le tableau de bord',      'description' => 'Accès au dashboard principal'],

            /* ── FIDÈLES ─────────────────────────────────────── */
            ['code' => 'FIDELE_SHOW',             'libelle' => 'Consulter les fidèles',              'description' => 'Voir la liste et le détail des fidèles'],
            ['code' => 'FIDELE_SHOW_ONE',            'libelle' => 'Voir les détails d\'un fidèle',                'description' => 'Afficher les informations détaillées d\'un fidèle'],
            ['code' => 'FIDELE_CREATE',            'libelle' => 'Créer un fidèle',                   'description' => 'Ajouter un nouveau fidèle'],
            ['code' => 'FIDELE_EDIT',              'libelle' => 'Modifier un fidèle',                'description' => 'Modifier les informations d\'un fidèle'],
            ['code' => 'FIDELE_DELETE',            'libelle' => 'Supprimer un fidèle',              'description' => 'Supprimer un fidèle'],
            ['code' => 'FIDELE_EXPORT',            'libelle' => 'Exporter les fidèles',             'description' => 'Exporter le bilan PDF d\'un fidèle'],
            ['code' => 'FIDELE_AVANCE_PAIEMENT',   'libelle' => 'Paiement en avance fidèle',        'description' => 'Enregistrer un paiement en avance pour un fidèle'],
            ['code' => 'FIDELE_SHOW_COTISATIONS', 'libelle' => 'Voir les cotisations d\'un fidèle', 'description' => 'Afficher la liste des cotisations d\'un fidèle'],
            ['code' => 'FIDELE_SHOW_DOCUMENTS',   'libelle' => 'Voir les documents d\'un fidèle',    'description' => 'Afficher la liste des documents d\'un fidèle'],

            /* ── COTISATIONS ─────────────────────────────────── */
            ['code' => 'COTISATION_SHOW',     'libelle' => 'Consulter les cotisations',   'description' => 'Voir la liste et le détail des cotisations'],
            ['code' => 'COTISATION_SHOW_ONE', 'libelle' => 'Voir les détails d\'une cotisation', 'description' => 'Afficher les informations détaillées d\'une cotisation'],
            ['code' => 'COTISATION_CREATE',   'libelle' => 'Créer une cotisation',        'description' => 'Enregistrer un paiement de cotisation'],
            ['code' => 'COTISATION_EDIT',     'libelle' => 'Modifier une cotisation',     'description' => 'Modifier une cotisation avant validation'],
            ['code' => 'COTISATION_DELETE',   'libelle' => 'Supprimer une cotisation',    'description' => 'Supprimer une cotisation'],
            ['code' => 'COTISATION_VALIDATE', 'libelle' => 'Valider une cotisation',      'description' => 'Valider manuellement le paiement d\'une cotisation'],
            ['code' => 'COTISATION_IMPORT',   'libelle' => 'Importer des cotisations',    'description' => 'Importer un fichier Excel de cotisations'],
            ['code' => 'COTISATION_EXPORT',   'libelle' => 'Exporter les cotisations',    'description' => 'Exporter les cotisations'],

            /* ── DÉPENSES ────────────────────────────────────── */
            ['code' => 'DEPENSE_SHOW',     'libelle' => 'Consulter les dépenses',  'description' => 'Voir la liste et le détail des dépenses'],
            ['code' => 'DEPENSE_SHOW_ONE', 'libelle' => 'Voir les détails d\'une dépense', 'description' => 'Afficher les informations détaillées d\'une dépense'],
            ['code' => 'DEPENSE_CREATE',   'libelle' => 'Créer une dépense',       'description' => 'Enregistrer une nouvelle dépense'],
            ['code' => 'DEPENSE_EDIT',     'libelle' => 'Modifier une dépense',    'description' => 'Modifier une dépense'],
            ['code' => 'DEPENSE_DELETE',   'libelle' => 'Supprimer une dépense',   'description' => 'Supprimer une dépense'],
            ['code' => 'DEPENSE_EXPORT',   'libelle' => 'Exporter les dépenses',    'description' => 'Exporter les dépenses'],
            ['code' => 'DEPENSE_VALIDATE', 'libelle' => 'Valider une dépense',     'description' => 'Valider une dépense'],

            /* ── PAIEMENTS ───────────────────────────────────── */
            ['code' => 'PAIEMENT_SHOW',       'libelle' => 'Consulter les paiements',      'description' => 'Voir la liste et le détail des paiements'],
            ['code' => 'PAIEMENT_SHOW_ONE',   'libelle' => 'Voir les détails d\'un paiement', 'description' => 'Afficher les informations détaillées d\'un paiement'],
            ['code' => 'PAIEMENT_VALIDATE',   'libelle' => 'Valider un paiement',          'description' => 'Valider un paiement en attente'],
            ['code' => 'PAIEMENT_ANNULER',    'libelle' => 'Annuler un paiement',          'description' => 'Annuler un paiement (code admin requis)'],
            ['code' => 'PAIEMENT_EXPORT_PDF', 'libelle' => 'Exporter le reçu PDF',         'description' => 'Télécharger le reçu PDF d\'un paiement'],

            /* ── TYPES DE DÉPENSES ───────────────────────────── */
            ['code' => 'TYPE_DEPENSE_SHOW',     'libelle' => 'Consulter les types de dépenses',  'description' => 'Voir la liste des types de dépenses'],
            ['code' => 'TYPE_DEPENSE_SHOW_ONE', 'libelle' => 'Voir les détails d\'un type de dépense', 'description' => 'Afficher les informations détaillées d\'un type de dépense'],
            ['code' => 'TYPE_DEPENSE_CREATE',   'libelle' => 'Créer un type de dépense',         'description' => 'Ajouter un type de dépense'],
            ['code' => 'TYPE_DEPENSE_EDIT',     'libelle' => 'Modifier un type de dépense',      'description' => 'Modifier un type de dépense'],
            ['code' => 'TYPE_DEPENSE_DELETE',   'libelle' => 'Supprimer un type de dépense',     'description' => 'Supprimer un type de dépense'],
            ['code' => 'TYPE_DEPENSE_ACTIVATE', 'libelle' => 'Activer/désactiver un type de dépense', 'description' => 'Changer le statut d\'un type de dépense'],

            /* ── TYPES DE COTISATIONS ────────────────────────── */
            ['code' => 'TYPE_COTISATION_SHOW',     'libelle' => 'Consulter les types de cotisations',       'description' => 'Voir la liste des types de cotisations'],
            ['code' => 'TYPE_COTISATION_SHOW_ONE', 'libelle' => 'Voir les détails d\'un type de cotisation', 'description' => 'Afficher les informations détaillées d\'un type de cotisation'],
            ['code' => 'TYPE_COTISATION_CREATE',   'libelle' => 'Créer un type de cotisation',              'description' => 'Ajouter un type de cotisation'],
            ['code' => 'TYPE_COTISATION_EDIT',     'libelle' => 'Modifier un type de cotisation',           'description' => 'Modifier un type de cotisation'],
            ['code' => 'TYPE_COTISATION_DELETE',   'libelle' => 'Supprimer un type de cotisation',          'description' => 'Supprimer un type de cotisation'],
            ['code' => 'TYPE_COTISATION_ACTIVATE', 'libelle' => 'Activer/désactiver un type de cotisation', 'description' => 'Changer le statut d\'un type de cotisation'],

            /* ── COÛT D'ENGAGEMENT ───────────────────────────── */
            ['code' => 'COUT_ENGAGEMENT_SHOW',   'libelle' => 'Consulter les coûts d\'engagement', 'description' => 'Voir la liste des paliers d\'engagement'],
            ['code' => 'COUT_ENGAGEMENT_SHOW_ONE', 'libelle' => 'Voir les détails d\'un coût d\'engagement', 'description' => 'Afficher les informations détaillées d\'un coût d\'engagement'],
            ['code' => 'COUT_ENGAGEMENT_CREATE', 'libelle' => 'Créer un coût d\'engagement',       'description' => 'Ajouter un palier d\'engagement'],
            ['code' => 'COUT_ENGAGEMENT_EDIT',   'libelle' => 'Modifier un coût d\'engagement',    'description' => 'Modifier un palier d\'engagement'],
            ['code' => 'COUT_ENGAGEMENT_DELETE', 'libelle' => 'Supprimer un coût d\'engagement',   'description' => 'Supprimer un palier d\'engagement'],

            /* ── BILAN FINANCIER ─────────────────────────────── */
            ['code' => 'BILAN_SHOW',   'libelle' => 'Consulter le bilan financier', 'description' => 'Voir le bilan financier'],
            ['code' => 'BILAN_SHOW_ONE', 'libelle' => 'Voir les détails du bilan financier', 'description' => 'Afficher les informations détaillées du bilan financier'],
            ['code' => 'BILAN_EXPORT', 'libelle' => 'Exporter le bilan financier',  'description' => 'Exporter le bilan financier en PDF'],

            ['code' => 'TRANSACTION_SHOW',   'libelle' => 'Consulter les transactions financières', 'description' => 'Voir les transactions financières'],
            ['code' => 'TRANSACTION_SHOW_ONE', 'libelle' => 'Voir les détails d\'une transaction financière', 'description' => 'Afficher les informations détaillées d\'une transaction financière'],
            ['code' => 'TRANSACTION_EXPORT', 'libelle' => 'Exporter les transactions financières',  'description' => 'Exporter les transactions financières en PDF'],

            /* ── ADMINISTRATEURS ─────────────────────────────── */
            ['code' => 'ADMIN_SHOW',              'libelle' => 'Consulter les administrateurs',       'description' => 'Voir la liste des comptes admin'],
            ['code' => 'ADMIN_SHOW_ONE',          'libelle' => 'Voir les détails d\'un administrateur', 'description' => 'Afficher les informations détaillées d\'un compte admin'],
            ['code' => 'ADMIN_CREATE',            'libelle' => 'Créer un administrateur',             'description' => 'Créer un nouveau compte admin'],
            ['code' => 'ADMIN_EDIT',              'libelle' => 'Modifier un administrateur',          'description' => 'Modifier un compte admin'],
            ['code' => 'ADMIN_SUSPEND',           'libelle' => 'Suspendre un administrateur',         'description' => 'Suspendre un compte admin'],
            ['code' => 'ADMIN_ACTIVATE',          'libelle' => 'Activer un administrateur',           'description' => 'Réactiver un compte admin suspendu'],
            ['code' => 'ADMIN_MANAGE_PERMISSION', 'libelle' => 'Gérer les permissions d\'un admin',   'description' => 'Attribuer ou révoquer des permissions à un admin'],

            /* ── RÔLES & PERMISSIONS ─────────────────────────── */
            ['code' => 'ROLE_SHOW',   'libelle' => 'Consulter les rôles',  'description' => 'Voir la liste des rôles'],
            ['code' => 'ROLE_SHOW_ONE', 'libelle' => 'Voir les détails d\'un rôle', 'description' => 'Afficher les informations détaillées d\'un rôle'],
            ['code' => 'ROLE_CREATE', 'libelle' => 'Créer un rôle',        'description' => 'Créer un nouveau rôle'],
            ['code' => 'ROLE_EDIT',   'libelle' => 'Modifier un rôle',     'description' => 'Modifier un rôle et ses permissions'],
            ['code' => 'ROLE_DELETE', 'libelle' => 'Supprimer un rôle',   'description' => 'Supprimer un rôle'],

            /* --PERMISSIONS  -------------------------------*/
            ['code' => 'PERMISSION_SHOW',   'libelle' => 'Consulter les permissions',  'description' => 'Voir la liste des permissions'],
            ['code' => 'PERMISSION_ATTRIBUATE',   'libelle' => 'Attribuer une permission à un rôle ou un admin',  'description' => 'Attribuer une permission à un rôle ou un admin'],



            /* ── RÉCLAMATIONS ────────────────────────────────── */
            ['code' => 'RECLAMATION_SHOW',  'libelle' => 'Consulter les réclamations', 'description' => 'Voir la liste des réclamations'],
            ['code' => 'RECLAMATION_SHOW_ONE', 'libelle' => 'Voir les détails d\'une réclamation', 'description' => 'Afficher les informations détaillées d\'une réclamation'],
            ['code' => 'RECLAMATION_EDIT',  'libelle' => 'Traiter une réclamation',    'description' => 'Répondre ou modifier une réclamation'],
            ['code' => 'RECLAMATION_CLOSE', 'libelle' => 'Clôturer une réclamation',   'description' => 'Fermer une réclamation résolue'],
            ['code' => 'RECLAMATION_DELETE', 'libelle' => 'Supprimer une réclamation', 'description' => 'Supprimer une réclamation'],

            /* ── MESSAGES GROUPER ────────────────────────────── */
            ['code' => 'MESSAGE_GROUPER_SHOW',   'libelle' => 'Consulter les messages groupés', 'description' => 'Voir les messages envoyés en groupe'],
            ['code' => 'MESSAGE_GROUPER_SHOW_ONE', 'libelle' => 'Voir les détails d\'un message groupé', 'description' => 'Afficher les informations détaillées d\'un message groupé'],
            ['code' => 'MESSAGE_GROUPER_CREATE', 'libelle' => 'Envoyer un message groupé',      'description' => 'Envoyer un message à un groupe de fidèles'],
            ['code' => 'MESSAGE_GROUPER_DELETE', 'libelle' => 'Supprimer un message groupé',    'description' => 'Supprimer un message groupé'],

            /* ── DEMANDES DE REMBOURSEMENT ───────────────────── */
            ['code' => 'REMBOURSEMENT_SHOW',    'libelle' => 'Consulter les demandes de remboursement', 'description' => 'Voir la liste des demandes de remboursement'],
            ['code' => 'REMBOURSEMENT_SHOW_ONE', 'libelle' => 'Voir les détails d\'un remboursement', 'description' => 'Afficher les informations détaillées d\'un remboursement'],
            ['code' => 'REMBOURSEMENT_VALIDATE', 'libelle' => 'Valider un remboursement',               'description' => 'Valider une demande de remboursement et créer la transaction sortie'],
            ['code' => 'REMBOURSEMENT_REJETER',  'libelle' => 'Rejeter un remboursement',               'description' => 'Rejeter une demande de remboursement'],

            /* ── DEMANDES DE CHANGEMENT DE COTISATION ────────── */
            ['code' => 'DEMANDE_CHANGE_COTISATION_SHOW',    'libelle' => 'Consulter les demandes de changement',  'description' => 'Voir la liste des demandes de changement de cotisation mensuelle'],
            ['code' => 'DEMANDE_CHANGE_COTISATION_SHOW_ONE', 'libelle' => 'Voir les détails d\'une demande de changement', 'description' => 'Afficher les informations détaillées d\'une demande de changement'],
            ['code' => 'DEMANDE_CHANGE_COTISATION_EDIT',    'libelle' => 'Modifier une demande de changement',    'description' => 'Modifier une demande de changement avant traitement'],
            ['code' => 'DEMANDE_CHANGE_COTISATION_VALIDATE','libelle' => 'Valider une demande de changement',     'description' => 'Valider et appliquer un changement de cotisation mensuelle'],
            ['code' => 'DEMANDE_CHANGE_COTISATION_REJETER', 'libelle' => 'Rejeter une demande de changement',     'description' => 'Rejeter une demande de changement de cotisation mensuelle'],

            /* ── AUDIT & LOGS ────────────────────────────────── */
            ['code' => 'AUDIT_SHOW',   'libelle' => 'Consulter les logs d\'audit', 'description' => 'Voir l\'historique des actions du système'],
            ['code' => 'AUDIT_EXPORT', 'libelle' => 'Exporter les logs d\'audit',  'description' => 'Exporter les logs d\'audit'],

            /* ── NOTIFICATIONS ───────────────────────────────── */
            ['code' => 'NOTIFICATION_SHOW',   'libelle' => 'Consulter les notifications', 'description' => 'Voir les notifications système'],
            ['code' => 'NOTIFICATION_CREATE', 'libelle' => 'Créer une notification',       'description' => 'Envoyer une notification manuelle'],
            ['code' => 'NOTIFICATION_SHOW_ONE', 'libelle' => 'Voir les détails d\'une notification', 'description' => 'Afficher les informations détaillées d\'une notification'],

        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['code' => $perm['code']],
                [
                    'libelle'     => $perm['libelle'],
                    'description' => $perm['description'],
                ]
            );
        }

        $this->command->info('✅ ' . count($permissions) . ' permissions CMRP insérées.');
    }
}