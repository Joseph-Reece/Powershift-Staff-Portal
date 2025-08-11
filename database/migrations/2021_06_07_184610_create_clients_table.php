<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;

class CreateClientsTable extends Migration
{
    use ColumnPropertiesTrait;
    public $tableName = "clients";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('initials')->unique();
            $table->string('logo')->unique();
            $table->string('email')->unique();
            $table->string('password');
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
        Schema::dropIfExists('clients');
        $this->deleteColumnProperties();
    }
}
