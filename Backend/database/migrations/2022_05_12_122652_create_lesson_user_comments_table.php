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

        // php artisan migrate --path=/database/migrations/2022_05_12_122652_create_lesson_user_comments_table.php

        Schema::create('lesson_user_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("lesson_id");
            $table->unsignedBigInteger("user_id");
            $table->longText("comment");
            $table->integer("state")->default(1);
            $table->foreign("lesson_id")->references("id")->on("lessons");
            $table->foreign("user_id")->references("id")->on("users");
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
        Schema::dropIfExists('lesson_user_comments');
    }
};
