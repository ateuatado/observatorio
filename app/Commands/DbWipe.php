<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class DbWipe extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:wipe';
    protected $description = 'Wipes the database by dropping all tables.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // Disable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        
        $tables = $db->listTables();
        
        if (empty($tables)) {
            CLI::write('The database is already empty.', 'green');
            return;
        }

        foreach ($tables as $table) {
            $db->query('DROP TABLE IF EXISTS `' . $table . '` CASCADE');
            CLI::write('Dropped table: ' . $table, 'yellow');
        }

        $db->query('SET FOREIGN_KEY_CHECKS=1');
        
        CLI::write('All tables dropped successfully!', 'green');
    }
}
