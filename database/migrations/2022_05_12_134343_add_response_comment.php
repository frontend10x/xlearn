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
        // php artisan migrate --path=/database/migrations/2022_05_12_134343_add_response_comment.php

        Schema::table('lesson_user_comments', function (Blueprint $table) {
            $table->unsignedBigInteger("lesson_user_comments_id")->nullable();
            $table->foreign("lesson_user_comments_id")->references("id")->on("lesson_user_comments");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lesson_user_comments', function (Blueprint $table) {
            //
        });
    }
};
