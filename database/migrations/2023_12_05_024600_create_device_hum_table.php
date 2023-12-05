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
        Schema::create('device_hum', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pet_device_id");
            $table->foreign("pet_device_id")->references("id")->on("pet_device");
            $table->float("value");
            $table->timestamp('created_at');
            $table->integer('feed_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_hum');
    }
};
