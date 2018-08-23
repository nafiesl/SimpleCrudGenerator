<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Route Generator Class
 */
class RouteGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'web')
    {
        $webRoutePath = $this->makeRouteFile(base_path('routes'), $type.'.php');

        $this->files->append($webRoutePath, $this->getContent('routes/'.$type));

        $this->command->info($this->modelNames['model_name'].' resource route generated on routes/'.$type.'.php.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        $stub = $this->getStubFileContent($stubName);

        $webRouteFileContent = $this->replaceStubString($stub);

        if (!is_null($parentName = $this->command->option('parent'))) {
            $modelName = $this->modelNames['model_name'];

            $webRouteFileContent = str_replace(
                $modelName.'Controller',
                $parentName.'\\'.$modelName.'Controller',
                $webRouteFileContent
            );
        }

        return $webRouteFileContent;
    }

    /**
     * Create php route file if not exists
     *
     * @param  string $routeDirPath Absolute directory path
     * @param  string $filename     File name to be created
     *
     * @return string               Absolute path of create route file
     */
    protected function makeRouteFile($routeDirPath, $filename)
    {
        if (!$this->files->isDirectory($routeDirPath)) {
            $this->files->makeDirectory($routeDirPath, 0777, true, true);
        }

        if (!$this->files->exists($routeDirPath.'/'.$filename)) {
            $this->generateFile($routeDirPath.'/'.$filename, "<?php\n");
        }

        return $routeDirPath.'/'.$filename;
    }
}
