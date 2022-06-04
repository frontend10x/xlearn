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

 // php artisan migrate --path=/database/migrations/2022_05_11_120521_create_table_user_course_rating.php

        Schema::create('user_course_rating', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("course_id");
            $table->text("observation")->nullable();
            $table->integer("rating");
            $table->integer("state")->default(1);
            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("course_id")->references("id")->on("courses");
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
        Schema::dropIfExists('user_course_rating');
    }
};
