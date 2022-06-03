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
        // php artisan migrate --path=/database/migrations/2022_05_09_094749_alter_table_sub_companies.php

        Schema::table('sub_companies', function (Blueprint $table) {
            $table->boolean("unlimited_access")->default(0);
            $table->date("start_date")->nullable();
            $table->date("end_date")->nullable();
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
            $table->dropColumn("unlimited_access");
            $table->dropColumn("start_date");
            $table->dropColumn("end_date");
        });
    }
};
