<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Rôles ─────────────────────────────────────────
        $roles = [
            [
                'code'        => 'SUPER_ADMIN',
                'libelle'     => 'Super Administrateur',
                'description' => 'Accès total à toutes les fonctionnalités',
            ],
            [
                'code'        => 'ADMIN',
                'libelle'     => 'Administrateur',
                'description' => 'Gestion générale de la plateforme',
            ],
            [
                'code'        => 'TRESORIER',
                'libelle'     => 'Trésorier',
                'description' => 'Gestion des finances, dépenses et rapports',
            ],
            [
                'code'        => 'SECRETAIRE',
                'libelle'     => 'Secrétaire',
                'description' => 'Gestion des fidèles et des cotisations',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['code' => $roleData['code']], $roleData);
        }

        $this->command->info('✅ Rôles créés.');

        // ─── 2. Users Super Admin ─────────────────────────────
        $superAdminRole = Role::where('code', 'SUPER_ADMIN')->first();

        $users = [
            [
                'nom'       => 'Admin',
                'prenom'    => 'Super',
                'email'     => 'superadmin@mosquee.ci',
                'dial_code' => '+225',
                'phone' => '0700000001',
                'password'  => Hash::make('SuperAdmin@2025!'),
                'status'    => 'actif',
                'role_id'   => $superAdminRole->id,
            ],
            [
                'nom'       => 'Système',
                'prenom'    => 'ISL',
                'email'     => 'isl@mosquee.ci',
                'dial_code' => '+225',
                'phone' => '0700000002',
                'password'  => Hash::make('ISL@Mosquee2025!'),
                'status'    => 'actif',
                'role_id'   => $superAdminRole->id,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Super admins créés.');
        $this->command->table(
            ['Email', 'Mot de passe'],
            [
                ['superadmin@mosquee.ci', 'SuperAdmin@2025!'],
                ['isl@mosquee.ci',        'ISL@Mosquee2025!'],
            ]
        );
    }
}
