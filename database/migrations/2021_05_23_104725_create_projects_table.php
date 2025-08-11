<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;
class CreateProjectsTable extends Migration
{
    use ColumnPropertiesTrait;
    public $tableName = "projects";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->integer('client');
            $table->string('project_name');
            $table->date('start_date');
            $table->date('expected_end_date');
            $table->date('actual_end_date');
            $table->timestamps();
        });
        $this->insertColumnProperties();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
        $this->deleteColumnProperties();
    }
}
