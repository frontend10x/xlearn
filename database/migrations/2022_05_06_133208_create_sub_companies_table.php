<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // php artisan migrate --path=/database/migrations/2022_05_06_133208_create_sub_companies_table.php

        Schema::create('sub_companies', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("address")->nullable();
            $table->string("phone")->nullable();
            $table->string("representative")->nullable();
            $table->string("position")->nullable();
            $table->string("representative_cell")->nullable();
            $table->string("file_path")->nullable();
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
        Schema::dropIfExists('sub_companies');
    }
};
