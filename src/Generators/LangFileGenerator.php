<?php

namespace Luthfi\CrudGenerator\Generators;

use Illuminate\Support\Str;

/**
 * Lang File Generator Class
 */
class LangFileGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'full')
    {
        $locale = config('app.locale');
        $langPath = $this->makeDirectory(resource_path('lang/'.$locale));

        $this->createAppLangFile($langPath);

        $this->generateFile($langPath.'/'.$this->modelNames['lang_name'].'.php', $this->getContent($locale));

        $this->command->info($this->modelNames['lang_name'].' lang files generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $locale)
    {
        $langStubPath = __DIR__.'/../stubs/resources/lang/'.$locale.'/master.stub';

        if ($this->files->exists($langStubPath)) {
            $stub = $this->files->get($langStubPath);
        } else {
            $stub = $this->files->get(__DIR__.'/../stubs/resources/lang/en/master.stub');
        }

        $displayModelName = ucwords(str_replace('_', ' ', Str::snake($this->modelNames['model_name'])));

        $properLangFileContent = str_replace(
            $this->modelNames['model_name'],
            $displayModelName,
            $this->replaceStubString($stub)
        );

        return $properLangFileContent;
    }

    /**
     * Generate lang/app.php file if it doesn't exists
     *
     * @param  string $langPath Directory path of lang files
     * @return void
     */
    private function createAppLangFile($langPath)
    {
        if (!$this->files->exists($langPath.'/app.php')) {
            $this->generateFile($langPath.'/app.php', $this->getAppLangFileContent());
            $this->command->info('lang/app.php generated.');
        }
    }

    /**
     * Get lang/app.php file content
     *
     * @return string
     */
    private function getAppLangFileContent()
    {
        $locale = config('app.locale');

        $langStubPath = __DIR__.'/../stubs/resources/lang/'.$locale.'/app.stub';

        if ($this->files->exists($langStubPath)) {
            $stub = $this->files->get($langStubPath);
        } else {
            $stub = $this->files->get(__DIR__.'/../stubs/resources/lang/en/app.stub');
        }
        return $stub;
    }
}
