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
        Schema::create('patient_monitorings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('center_id');
            $table->date('date');
            $table->decimal("height",10,2)->nullable();
            $table->decimal("weight",10,2)->nullable();
            $table->decimal("temperature",10,2)->nullable();
            $table->decimal("heart_rate",10,2)->nullable();
            $table->decimal("blood_presure",10,2)->nullable();
            $table->decimal("rheumatoid_factor",10,2)->nullable();
            $table->text('motive')->nullable();
            $table->text('physical_exploration')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('diagnoses')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('patient_monitorin_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_monitoring_id');
            $table->unsignedBigInteger('diagnosi_id');

            $table->foreign('patient_monitoring_id')
                ->references('id')->on('patient_monitorings')
                ->onDelete('cascade');

            $table->foreign('diagnosi_id')
                ->references('id')->on('diagnosis')
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
        Schema::dropIfExists('patient_monitorin_diagnosis');
        Schema::dropIfExists('patient_monitorings');
    }
};
