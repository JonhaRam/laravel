<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('spot_type_id')->unsigned();
            $table->string('name', 45);
            $table->string('image', 255);
            $table->string('number', 45);
            $table->string('street', 45);
            $table->string('zip_code', 5);
            $table->double('lat', 8, 6);
            $table->double('lng', 8, 6);
            $table->foreign('spot_type_id')->references('id')->on('spots_types')->onDelete('cascade');
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
        Schema::dropIfExists('spots');
    }
}
