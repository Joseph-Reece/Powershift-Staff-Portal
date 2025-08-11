<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;

class AddPublishDateToSolutions extends Migration
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
        Schema::table('solutions', function (Blueprint $table) {
            $table->datetime('publish_date')->nullable();
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
        Schema::table('solutions', function (Blueprint $table) {
            $table->dropColumn('publish_date');
        });
        $this->deleteColumns = ['publish_date'];
        $this->deleteColumnProperties();
    }
}
