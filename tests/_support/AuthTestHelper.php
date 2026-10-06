<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Shield\Test\AuthenticationTesting;
use RuntimeException;

/**
 * Cria identidades isoladas e autentica perfis nos testes de integração.
 *
 * A classe de teste que usa este trait precisa preparar as migrations do Shield.
 */
trait AuthTestHelper
{
    use AuthenticationTesting;

    /**
     * @param array<string, mixed> $attributes
     */
    protected function createTestUser(string $group = 'voluntario', array $attributes = []): User
    {
        $suffix = bin2hex(random_bytes(6));
        $user   = new User(array_merge([
            'username' => 'test_' . $suffix,
            'email'    => 'test_' . $suffix . '@example.test',
            'password' => 'Test@123456!',
            'active'   => true,
        ], $attributes));

        /** @var UserModel $users */
        $users = model(UserModel::class);

        if (! $users->save($user)) {
            throw new RuntimeException('Não foi possível criar o usuário de teste: ' . implode('; ', $users->errors()));
        }

        $savedUser = $users->findById($users->getInsertID());

        if (! $savedUser instanceof User) {
            throw new RuntimeException('O usuário de teste criado não pôde ser recuperado.');
        }

        $savedUser->addGroup($group);

        return $savedUser;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    protected function actingAsTestUser(string $group = 'voluntario', array $attributes = []): User
    {
        $user = $this->createTestUser($group, $attributes);
        $this->actingAs($user);

        return $user;
    }
}
