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

        // php artisan migrate --path=/database/migrations/2022_05_08_155818_alter_tabler_sub_compani_add_campos.php

        Schema::table('sub_companies', function (Blueprint $table) {
            $table->boolean("state")->default(1);
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
            $table->dropColumn("state");
        });
    }
};
