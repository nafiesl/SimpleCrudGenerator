<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Lang File Generator Class
 */
class LangFileGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $locale = config('app.locale');
        $langPath = $this->makeDirectory(resource_path('lang/'.$locale));

        $this->createAppLangFile($langPath);
        $this->generateFile($langPath.'/'.$this->modelNames['lang_name'].'.php', $this->getContent());

        $this->command->info($this->modelNames['lang_name'].' lang files generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/lang.stub');

        $displayModelName = ucwords(str_replace('_', ' ', snake_case($this->modelNames['model_name'])));

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
        if ( ! $this->files->exists($langPath.'/app.php')) {
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
        return $this->files->get(__DIR__.'/../stubs/lang-app.stub');
    }
}
