<?php

namespace Luthfi\CrudGenerator\Generators;

/**
* Web Route Generator Class
*/
class WebRouteGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $webRoutePath = $this->makeRouteFile(base_path('routes'), 'web.php');
        $this->files->append($webRoutePath, $this->getContent());

        $this->command->info($this->modelNames['model_name'].' resource route generated on routes/web.php.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/route-web.stub');

        $webRouteFileContent = $this->replaceStubString($stub);

        if (! is_null($parentName = $this->command->option('parent'))) {

            $pluralModelName = $this->modelNames['plural_model_name'];

            $webRouteFileContent = str_replace(
                $pluralModelName.'Controller',
                $parentName.'\\'.$pluralModelName.'Controller',
                $webRouteFileContent
            );
        }

        return $webRouteFileContent;
    }

    /**
     * Create php route file if not exists
     * @param  string $routeDirPath Absolute directory path
     * @param  string $filename     File name to be created
     * @return string               Absolute path of create route file
     */
    protected function makeRouteFile($routeDirPath, $filename)
    {
        if (! $this->files->isDirectory($routeDirPath)) {
            $this->files->makeDirectory($routeDirPath, 0777, true, true);
        }

        if (! $this->files->exists($routeDirPath.'/'.$filename)) {
            $this->generateFile($routeDirPath.'/'.$filename, "<?php\n");
        }

        return $routeDirPath.'/'.$filename;
    }
}