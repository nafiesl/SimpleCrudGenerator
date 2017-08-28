<?php

namespace Tests\Generators;

use Tests\TestCase;

class MigrationGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_migration_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->tableName.'_table.php');
        $this->assertFileExists($migrationFilePath);
        $modelClassContent = "<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint \$table) {
            \$table->increments('id');
            \$table->string('name', 60);
            \$table->string('description');
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($migrationFilePath));
    }
}
