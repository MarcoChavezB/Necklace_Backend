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
        Schema::create('pet_device', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("device_id");
            $table->foreign("device_id")
                    ->references("id")
                    ->on("devices");
            $table->unsignedBigInteger("pet_id");
            $table->foreign("pet_id")
                    ->references("id")
                    ->on("pets");
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
        Schema::dropIfExists('pet_device');
    }
};
