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
        Schema::create('centers', function (Blueprint $table) {
            $table->id();

            $table->string("name");
            // $table->string("image")->nullable();
            $table->string("address")->nullable();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->text("specialities")->nullable();
            $table->string("schedule")->nullable();

            $table->unsignedBigInteger("province_id")->nullable();
            $table->unsignedBigInteger("municipio_id")->nullable();
            $table->boolean("active")->default(0);
            $table->string("default")->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');

            $table->foreign('municipio_id')
                ->references('id')
                ->on('municipios')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('centers');
    }
};
