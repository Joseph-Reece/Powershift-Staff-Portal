<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;

class CreateNotificationsTable extends Migration
{
    use ColumnPropertiesTrait;
    public $tableName = "notifications";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('category');
            $table->string('subject');
            $table->string('sender');
            $table->string('receiver');
            $table->string('message',500);
            $table->boolean('is_sent')->default(0);
            $table->datetime('date_sent')->nullable();
            $table->string('response_status')->nullable();
            $table->string('response_message')->nullable();
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
        Schema::dropIfExists('notifications');
        $this->deleteColumnProperties();
    }
}
