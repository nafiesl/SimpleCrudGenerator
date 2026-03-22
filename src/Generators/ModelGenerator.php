<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Model Generator Class
 */
class ModelGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'full')
    {
        $modelPath = $this->modelNames['model_path'];
        $modelDirectory = $this->makeDirectory(app_path($modelPath));
        $modelClassPath = $modelDirectory.'/'.$this->modelNames['model_name'].'.php';

        if ($this->files->exists($modelClassPath)) {
            $this->command->warn('Use the existing '.$this->modelNames['model_name'].' model.');
            return;
        }

        if ($this->modelNames['parent_model_name']) {
            $parentModelClassPath = app_path($this->modelNames['parent_model_path'].'/'.$this->modelNames['parent_model_name'].'.php');
            if (!$this->files->exists($parentModelClassPath)) {
                $this->command->error("Parent model {$this->modelNames['parent_model_name']} not exists!");
                return;
            }
            $parentModelContent = file_get_contents($parentModelClassPath);
            $lastBracePos = strrpos($parentModelContent, '}');
            $parentModelRelationMethodContent = "
    public function {$this->modelNames['collection_model_var_name']}()
    {
        return \$this->hasMany({$this->modelNames['model_name']}::class);
    }
";
            if ($lastBracePos !== false) {
                $modelClassContent = substr_replace($parentModelContent, $parentModelRelationMethodContent."\n", $lastBracePos, 0);
                file_put_contents($parentModelClassPath, $modelClassContent);
                $this->command->info($this->modelNames['parent_model_name'].' model relation updated.');
            } else {
                $this->command->error($this->modelNames['parent_model_name'].' model relation not updated.');
            }
        }

        $this->generateFile($modelClassPath, $this->getContent('models/model'));

        $this->command->info($this->modelNames['model_name'].' model generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        if ($this->command->option('formfield')) {
            $stubName .= '-formfield';
        }
        if ($this->modelNames['parent_table_name']) {
            $stubName = $stubName.'-parentmodel';
        }

        $modelFileContent = $this->getStubFileContent($stubName);

        $userModel = config('auth.providers.users.model');

        if ('App\User' !== $userModel) {
            $modelFileContent = str_replace('App\User', $userModel, $modelFileContent);
        }

        if ($this->command->option('uuid')) {
            $string = "protected \$fillable = ['title', 'description', 'creator_id'];\n";
            $replacement = "public \$incrementing = false;\n\n";
            $replacement .= "    protected \$keyType = 'string';\n\n";
            $replacement .= "    protected \$fillable = ['id', 'title', 'description', 'creator_id'];\n";
            $modelFileContent = str_replace($string, $replacement, $modelFileContent);
        }

        return $this->replaceStubString($modelFileContent);
    }
}
