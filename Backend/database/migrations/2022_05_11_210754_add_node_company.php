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

        //php artisan migrate --path=/database/migrations/2022_05_11_210754_add_node_company.php

        Schema::table('companies', function (Blueprint $table) {
            $table->string("link_facebook")->nullable();
            $table->string("link_google")->nullable();
            $table->string("link_linkedin")->nullable();
            $table->string("link_instagram")->nullable();
            $table->string("website")->nullable();
            $table->string("nit")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn("link_facebook");
            $table->dropColumn("link_google");
            $table->dropColumn("link_linkedin");
            $table->dropColumn("link_instagram");
            $table->dropColumn("website");
            $table->dropColumn("nit");
        });
    }
};
