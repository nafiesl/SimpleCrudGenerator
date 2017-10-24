<?php

namespace Tests;

class ConfigTest extends TestCase
{
    /** @test */
    public function it_has_config_file()
    {
        $this->assertTrue(is_array(config('simple-crud')));
    }

    /** @test */
    public function it_has_default_layout_view_config()
    {
        $this->assertEquals('layouts.app', config('simple-crud.default_layout_view'));

        config(['simple-crud.default_layout_view' => 'layouts.master']);

        $this->assertEquals('layouts.master', config('simple-crud.default_layout_view'));
    }

    /** @test */
    public function it_has_base_test_path_config()
    {
        $this->assertEquals('tests/BrowserKitTest.php', config('simple-crud.base_test_path'));
    }

    /** @test */
    public function it_has_base_test_class_config()
    {
        $this->assertEquals('Tests\BrowserKitTest', config('simple-crud.base_test_class'));
    }

    /** @test */
    public function config_file_can_be_published()
    {
        $this->artisan('vendor:publish', ['--tag' => 'config', '--no-interaction' => true]);

        $this->assertFileExists(config_path('simple-crud.php'));

        $this->removeFileOrDir(config_path('simple-crud.php'));
    }
}
