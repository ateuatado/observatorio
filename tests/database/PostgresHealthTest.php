<?php

declare(strict_types=1);

use CodeIgniter\Database\Config as DatabaseConfig;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PostgresHealthTest extends CIUnitTestCase
{
    public function testTestGroupUsesAnIsolatedPostgresDatabaseWithPostgis(): void
    {
        $testDatabase = DatabaseConfig::connect('tests', false);
        $testDatabase->initialize();

        $this->assertSame('Postgre', $testDatabase->getPlatform());

        $identity = $testDatabase->query(
            'SELECT current_database() AS database_name, current_schema() AS schema_name',
        )->getRowArray();

        $databaseName = (string) ($identity['database_name'] ?? '');
        $this->assertNotSame('', $databaseName);
        $this->assertStringEndsWith('_test', $databaseName, 'O banco automatizado deve terminar com "_test".');
        $this->assertSame('public', $identity['schema_name'] ?? null);

        $postgis = $testDatabase->query(
            "SELECT EXISTS (SELECT 1 FROM pg_extension WHERE extname = 'postgis') AS enabled",
        )->getRowArray();

        $this->assertContains($postgis['enabled'] ?? null, [true, 1, '1', 't'], 'A extensão PostGIS não está habilitada.');

        $spatial = $testDatabase->query(
            'SELECT ST_SRID(ST_SetSRID(ST_MakePoint(-46.6333, -23.5505), 4326)) AS srid',
        )->getRowArray();

        $this->assertSame(4326, (int) ($spatial['srid'] ?? 0));
        $testDatabase->close();
    }
}
