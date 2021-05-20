<?php

namespace Tests\Generators;

use Tests\TestCase;

class MigrationGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_migration_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);
        $modelClassContent = "<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create{$this->plural_model_name}Table extends Migration
{
    public function up()
    {
        Schema::create('{$this->table_name}', function (Blueprint \$table) {
            \$table->bigIncrements('id');
            \$table->string('title', 60);
            \$table->string('description')->nullable();
            \$table->foreignId('creator_id')->constrained('users')->onDelete('restrict');
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$this->table_name}');
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($migrationFilePath));
    }
}
