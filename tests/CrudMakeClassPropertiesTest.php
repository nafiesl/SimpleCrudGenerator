<?php

namespace Tests;

use Luthfi\CrudGenerator\CrudMake;

class CrudMakeClassPropertiesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->crudMaker = app(CrudMake::class);
    }

    /** @test */
    public function it_has_stub_model_names_property()
    {
        $this->assertEquals([
            'model_namespace' => 'mstrNmspc',
            'full_model_name' => 'fullMstr',
            'plural_model_name' => 'Masters',
            'model_name' => 'Master',
            'table_name' => 'masters',
            'lang_name' => 'master',
            'collection_model_var_name' => 'mstrCollections',
            'single_model_var_name' => 'singleMstr',
        ], $this->crudMaker->stubModelNames);
    }

    /** @test */
    public function it_has_model_names_property()
    {
        $this->assertEquals([
            'full_model_name' => 'App\Models\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => 'Models',
            'model_namespace' => 'App\Models',
            'parent_model_name' => null,
            'parent_table_name' => null,
            'parent_lang_name' => null,
            'single_parent_model_var_name' => null,
            'full_parent_model_name' => null,
            'parent_model_path' => null,
        ], $this->crudMaker->getModelName('Category'));

        $this->assertEquals([
            'full_model_name' => 'App\Models\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => 'Models',
            'model_namespace' => 'App\Models',
            'parent_model_name' => null,
            'parent_table_name' => null,
            'parent_lang_name' => null,
            'single_parent_model_var_name' => null,
            'full_parent_model_name' => null,
            'parent_model_path' => null,
        ], $this->crudMaker->getModelName('category'));
    }

    /** @test */
    public function it_set_proper_model_names_property_for_namespaced_model_title_entry()
    {
        $this->assertEquals([
            'model_namespace' => 'App\Entities\References',
            'full_model_name' => 'App\Entities\References\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => 'Entities/References',
            'parent_model_name' => null,
            'parent_table_name' => null,
            'parent_lang_name' => null,
            'single_parent_model_var_name' => null,
            'full_parent_model_name' => null,
            'parent_model_path' => null,
        ], $this->crudMaker->getModelName('Entities/References/Category'));

        $this->assertEquals([
            'model_namespace' => 'App\Models',
            'full_model_name' => 'App\Models\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => 'Models',
            'parent_model_name' => null,
            'parent_table_name' => null,
            'parent_lang_name' => null,
            'single_parent_model_var_name' => null,
            'full_parent_model_name' => null,
            'parent_model_path' => null,
        ], $this->crudMaker->getModelName('Models/Category'));

        $this->assertEquals([
            'model_namespace' => 'App\Models',
            'full_model_name' => 'App\Models\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => 'Models',
            'parent_model_name' => null,
            'parent_table_name' => null,
            'parent_lang_name' => null,
            'single_parent_model_var_name' => null,
            'full_parent_model_name' => null,
            'parent_model_path' => null,
        ], $this->crudMaker->getModelName('models/category'));
    }
}
