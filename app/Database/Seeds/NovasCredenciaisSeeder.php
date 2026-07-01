<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

/**
 * Cria usuários adicionais de teste alinhados aos grupos oficiais do OVPDH.
 * 
 * Uso: php spark db:seed NovasCredenciaisSeeder
 */
class NovasCredenciaisSeeder extends Seeder
{
    public function run(): void
    {
        $users = new UserModel();

        // Lista de novos usuários de teste
        $novosUsuarios = [
            [
                'username' => 'teste_superadmin',
                'email'    => 'superadmin@teste.ovp',
                'password' => 'Teste@Super2026',
                'group'    => 'superadmin',
            ],
            [
                'username' => 'teste_admin',
                'email'    => 'admin@teste.ovp',
                'password' => 'Teste@Admin2026',
                'group'    => 'admin',
            ],
            [
                'username' => 'teste_voluntario',
                'email'    => 'voluntario@teste.ovp',
                'password' => 'Teste@Voluntario2026',
                'group'    => 'voluntario',
            ],
            [
                'username' => 'teste_colaborador',
                'email'    => 'colaborador@teste.ovp',
                'password' => 'Teste@Colab2026',
                'group'    => 'colaborador',
            ],
            [
                'username' => 'teste_advogado',
                'email'    => 'advogado@teste.ovp',
                'password' => 'Teste@Advogado2026',
                'group'    => 'advogado',
            ],
            [
                'username' => 'teste_academico',
                'email'    => 'academico@teste.ovp',
                'password' => 'Teste@Academico2026',
                'group'    => 'academico',
            ],
        ];

        echo "\n🧪 Gerando Usuários de Teste Alinhados aos Grupos do OVPDH...\n";

        foreach ($novosUsuarios as $dados) {
            // Verifica se o usuário já existe pelo username
            $existente = $users->where('username', $dados['username'])->first();
            
            if ($existente) {
                echo "⚠️  [{$dados['group']}] '{$dados['username']}' já existe. Pulando...\n";
                continue;
            }

            $user = new User([
                'username' => $dados['username'],
                'active'   => 1,
            ]);

            $users->save($user);
            $userId = $users->getInsertID();

            $user = $users->findById($userId);
            $user->createEmailIdentity([
                'email'    => $dados['email'],
                'password' => $dados['password'],
            ]);

            $user->addGroup($dados['group']);

            echo "✅ [{$dados['group']}] {$dados['username']} criado!\n";
            echo "   📧 {$dados['email']} | 🔑 {$dados['password']}\n";
        }
    }
}
