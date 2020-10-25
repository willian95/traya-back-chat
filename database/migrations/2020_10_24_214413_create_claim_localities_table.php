<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimLocalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim_localities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("location_id")->unsigned();
            $table->string("emails")->nullable();
            $table->boolean("active")->default(false);
            $table->timestamps();

            $table->foreign("location_id")->references("id")->on("locations");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim_localities');
    }
}
