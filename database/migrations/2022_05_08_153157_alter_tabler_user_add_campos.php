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

        // php artisan migrate --path=/database/migrations/2022_05_08_153157_alter_tabler_user_add_campos.php

        Schema::table('users', function (Blueprint $table) {
            $table->string("surname")->nullable();
            $table->string("phone")->nullable();
            $table->boolean("state")->default(1);
            $table->unsignedBigInteger("subcompanies_id")->nullable();
            $table->foreign("subcompanies_id")->references("id")->on("sub_companies");
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
