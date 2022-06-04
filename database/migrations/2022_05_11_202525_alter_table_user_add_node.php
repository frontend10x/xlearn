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

// php artisan migrate --path=/database/migrations/2022_05_11_202525_alter_table_user_add_node.php

        Schema::table('users', function (Blueprint $table) {
            $table->string("link_facebook")->nullable();
            $table->string("link_google")->nullable();
            $table->string("link_linkedin")->nullable();
            $table->string("link_instagram")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
