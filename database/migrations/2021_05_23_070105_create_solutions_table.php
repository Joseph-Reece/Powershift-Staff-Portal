<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;

class CreateSolutionsTable extends Migration
{
    use ColumnPropertiesTrait;
    public $tableName = "solutions";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('cover_image')->unique();
            $table->string('summary',500);
            $table->string('content',3000);
            $table->integer('sequence')->nullable();
            $table->boolean('is_publish')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('solutions');
        $this->deleteColumnProperties();
    }
}
