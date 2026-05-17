<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

/**
 * Atualiza e-mail e senha do usuário marcosantofoto.
 * Uso: php spark db:seed AtualizarAdminSeeder
 */
class AtualizarAdminSeeder extends Seeder
{
    public function run(): void
    {
        $users = new UserModel();

        $user = $users->findByCredentials(['email' => 'admin@ovp.org.br'])
               ?? $users->where('username', 'marcosantofoto')->first();

        if (!$user) {
            echo "❌ Usuário marcosantofoto não encontrado.\n";
            return;
        }

        // Atualiza e-mail e senha
        $user->email    = 'marcosantofoto@gmail.com';
        $user->password = 'Lula#Eleito26';
        $users->save($user);

        echo "✅ Usuário atualizado!\n";
        echo "   E-mail : marcosantofoto@gmail.com\n";
        echo "   Senha  : Lula#Eleito26\n";
    }
}
