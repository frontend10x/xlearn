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
        // php artisan migrate --path=/database/migrations/2022_05_06_134010_create_plans_table.php

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("description")->nullable();
            $table->string("price");
            $table->string("amount_people");
            $table->boolean("state")->default(1);
            $table->string("color_title")->nullable();
            $table->string("color_border")->nullable();
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
        Schema::dropIfExists('plans');
    }
};
