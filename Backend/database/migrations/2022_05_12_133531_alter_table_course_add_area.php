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
        // php artisan migrate --path=/database/migrations/2022_05_12_133531_alter_table_course_add_area.php

        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger("area_id")->nullable();
            $table->foreign("area_id")->references("id")->on("areas");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            //
        });
    }
};
