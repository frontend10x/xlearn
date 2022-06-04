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

        // php artisan migrate --path=/database/migrations/2022_05_09_142911_alter_table_sub_companies.php

        Schema::table('sub_companies', function (Blueprint $table) {
            $table->string("facebook")->nullable();
            $table->string("twitter")->nullable();
            $table->string("linkedin")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_companies', function (Blueprint $table) {
            $table->dropColumn("facebook");
            $table->dropColumn("twitter");
            $table->dropColumn("linkedin");
        });
    }
};
