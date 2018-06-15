<?php

namespace Luthfi\CrudGenerator\Contracts;

interface Generator
{
    /**
     * Generate class file content.
     *
     * @param  string  $type Type of crud
     * @return void
     */
    public function generate(string $type = 'full');

    /**
     * Get class file content.
     *
     * @param  string  $stubName Name of stub file
     * @return string
     */
    public function getContent(string $stubName);
}
