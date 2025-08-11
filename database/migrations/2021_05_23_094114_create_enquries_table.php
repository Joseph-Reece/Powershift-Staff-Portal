<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;

class CreateEnquriesTable extends Migration
{
    use ColumnPropertiesTrait;
    public $tableName = "enquiries";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("subject");
            $table->string("email");
            $table->string("phone_number");
            $table->string("message",1000);
            $table->boolean("is_read")->default(0);
            $table->boolean("is_responded")->default(0);
            $table->datetime("date_responded")->nullable();
            $table->string('responded_by')->nullable();
            $table->string('response_medium')->nullable();
            $table->string('response_message',1000)->nullable();
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
        Schema::dropIfExists('enquiries');
        $this->deleteColumnProperties();
    }
}
