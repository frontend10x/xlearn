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

        // php artisan migrate --path=/database/migrations/2022_05_09_131715_create_table_subcompanies_group.php

        Schema::create('subcompanie_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("subcompanie_id");
            $table->unsignedBigInteger("group_id");
            $table->foreign("subcompanie_id")->references("id")->on("sub_companies");
            $table->foreign("group_id")->references("id")->on("groups");
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
        Schema::dropIfExists('subcompanie_group');
    }
};
