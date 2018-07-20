<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Migration Generator Class
 */
class MigrationGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'full')
    {
        $prefix = date('Y_m_d_His');
        $tableName = $this->modelNames['table_name'];

        $migrationPath = $this->makeDirectory(database_path('migrations'));

        $migrationFilePath = $migrationPath.'/'.$prefix."_create_{$tableName}_table.php";
        $this->generateFile($migrationFilePath, $this->getContent('database/migrations/migration-create'));

        $this->command->info($this->modelNames['model_name'].' table migration generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        return $this->replaceStubString($this->getStubFileContent($stubName));
    }
}
