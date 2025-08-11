<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_properties', function (Blueprint $table) {
            $table->id();
            $table->string("tableName");
            $table->string("columnName");
            $table->string("caption")->nullable();
            $table->string("placeholder")->nullable();
            $table->string("type")->nullable();
            $table->string("element")->nullable();
            $table->boolean("isList")->default(0);
            $table->boolean("isCreate")->default(0);
            $table->boolean("isEdit")->default(0);
            $table->boolean("isView")->default(1);
            $table->boolean("isSearch")->default(1);
            $table->boolean("isRequired")->default(0);
            $table->boolean("isMultiple")->default(0);
            $table->boolean("isEditor")->default(0);
            $table->string("validation")->nullable();
            $table->string("validation_messages",500)->nullable();
            $table->boolean("isImage")->default(0);
            $table->string("mimes")->nullable();
            $table->string("accept")->nullable();
            $table->string("storageName")->nullable();
            $table->string("options",400)->nullable();
            $table->string("dont_show",300)->nullable();
            $table->string("dont_edit",300)->nullable();
            $table->integer("sequence")->nullable();
            $table->integer("max_length")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('column_properties');
    }
}
