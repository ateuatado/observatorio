<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use RuntimeException;

/**
 * Base para testes que precisam de PostgreSQL e isolamento por transação.
 *
 * Subclasses que precisem subir migrations devem definir $migrate = true.
 */
abstract class PostgresTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $DBGroup = 'tests';
    protected $migrate = false;
    protected $refresh = false;
    protected $namespace = null;

    private bool $transactionStarted = false;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->db->getPlatform() !== 'Postgre') {
            throw new RuntimeException('Os testes de banco do OVPDH exigem o grupo PostgreSQL "tests".');
        }

        if (! $this->db->transBegin()) {
            throw new RuntimeException('Não foi possível iniciar a transação isolada do teste.');
        }

        $this->transactionStarted = true;
    }

    protected function tearDown(): void
    {
        if ($this->transactionStarted) {
            $this->db->transRollback();
            $this->transactionStarted = false;
        }

        parent::tearDown();
    }
}
