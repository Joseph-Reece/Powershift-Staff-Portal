<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Traits\ColumnPropertiesTrait;

class CreateCompanyInfoTable extends Migration
{
    use ColumnPropertiesTrait;
    public $tableName = "company_info";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_info', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('location');
            $table->string('address');
            $table->string('about_company',2000);
            $table->string('vision')->nullable();
            $table->string('mission',1000)->nullable();
            $table->string('logo')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
        DB::table('company_info')->insert(
            [
                [
                    'name' => 'Default Company',
                    'email' => 'default@default.com',
                    'phone_number' => "07########",
                    'location' => "07########",
                    'address' => "xxxx-000 Nairobi",
                    'about_company' => "Default Company was started in the year 1901 by....",
                    'vision' => "To become leaders in the industry of...",
                    'mission' => "To create efficient and reliable...",
                ]
            ]
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
        Schema::dropIfExists('company_info');
        $this->deleteColumnProperties();
    }
}
