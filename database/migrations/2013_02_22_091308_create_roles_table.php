<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;

class CreateRolesTable extends Migration
{
    use ColumnPropertiesTrait;
    public $tableName = "roles";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->unique();
            $table->string('permissions')->nullable();
            $table->boolean('is_default')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            //foreign keys
            // $table->foreign('created_by')->references('email')->on('users')->onUpdate('cascade');
            // $table->foreign('updated_by')->references('email')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });
        // Insert some stuff
        DB::table('roles')->insert(
            array(
                array(
                    'code' => 'SUPER ADMIN',
                    'description' => 'Super Admin',
                    'is_default' => 1,
                ),
                array(
                    'code' => 'CLIENT',
                    'description' => 'Client',
                    'is_default' => 1,
                )
            )
        );
        $this->insertColumnProperties();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
        $this->deleteColumnProperties();
    }
}
