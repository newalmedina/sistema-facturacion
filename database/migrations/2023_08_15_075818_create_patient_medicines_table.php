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
        Schema::create('patient_medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('center_id');
            $table->date('date');
            $table->text('comment')->nullable();


            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('center_id')
                ->references('id')->on('centers')
                ->onDelete('cascade');

            $table->timestamps();

            $table->softDeletes();
        });
        Schema::create('patient_medicine_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_medicine_id');
            $table->string("medicine")->nullable();
            $table->string("dosis")->nullable();
            $table->string("amount")->nullable();
            $table->string("frecuency")->nullable();
            $table->string("period")->nullable();

            $table->foreign('patient_medicine_id')
                ->references('id')->on('patient_medicines')
                ->onDelete('cascade');

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
        Schema::dropIfExists('patient_medicine_details');
        Schema::dropIfExists('patient_medicines');
    }
};
