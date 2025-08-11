<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsHiddenToColumnPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('column_properties', function (Blueprint $table) {
            $table->boolean('isHidden')->after('isEdit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('column_properties', function (Blueprint $table) {
            $table->dropColumn('isHidden');
        });
        $this->deleteColumns = ['isHidden'];
        $this->deleteColumnProperties();
    }
}
