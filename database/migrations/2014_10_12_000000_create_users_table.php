<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;

class CreateUsersTable extends Migration
{
    use ColumnPropertiesTrait;
    public $tableName = "users";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->text('photo')->nullable();
            $table->string('role')->nullable();
            $table->boolean('is_role_permissions')->default(1);
            $table->string('permissions')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->datetime('password_expiry')->nullable();
            //foreign keys
            // $table->foreign('role')->references('code')->on('roles')->onUpdate('cascade')->onDelete('SET NULL');
            // $table->foreign('created_by')->references('email')->on('users')->onUpdate('cascade');
            // $table->foreign('updated_by')->references('email')->on('users')->onUpdate('cascade');

        });
        // Insert some stuff
        DB::table('users')->insert(
            array(
                'email' => 'admin@wenlasoftwares.com',
                'name' => "Admin",
                'role' => "SUPER ADMIN",
                'password' => '$2y$10$pzMh.g8vUvN7RBsh0Fh0guLuSY03RAcNJMUf8sbY.nuK5Fk.DxNwS'
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
        Schema::dropIfExists('users');
        $this->deleteColumnProperties();
    }
}
