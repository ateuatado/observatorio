<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Models\UserModel;

/**
 * Cria o usuário administrador inicial do OVP.
 * Uso: php spark db:seed AdminUserSeeder
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = new UserModel();

        // Dados do usuário
        $username = 'marcosantofoto';
        $email    = 'admin@ovp.org.br';
        $senha    = 'Lula@Eleito26';

        // Verifica se já existe
        $existente = $users->where('username', $username)->first();
        if ($existente) {
            echo "Usuário '{$username}' já existe (ID: {$existente->id}).\n";
            return;
        }

        // Cria o usuário via Shield
        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => $username,
            'active'   => 1,
        ]);

        // Shield requer salvar o model primeiro para obter o ID
        $users->save($user);
        $userId = $users->getInsertID();

        // Recarrega a entidade com o ID
        $user = $users->findById($userId);

        // Define e-mail + senha via identity
        $user->createEmailIdentity([
            'email'    => $email,
            'password' => $senha,
        ]);

        // Adiciona ao grupo 'admin'
        $user->addGroup('admin');

        echo "✅ Usuário criado com sucesso!\n";
        echo "   Username : {$username}\n";
        echo "   E-mail   : {$email}\n";
        echo "   Grupo    : admin\n";
        echo "   ID       : {$userId}\n";
    }
}
