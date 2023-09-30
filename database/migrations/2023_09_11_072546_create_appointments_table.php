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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('center_id')->nullable();
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->decimal("price", 18, 2)->default(0);

            $table->unsignedBigInteger('insurance_carrier_id')->nullable();

            $table->boolean("applicated_insurance")->default(0);

            $table->decimal("price_with_insurance", 18, 2)->default(0);

            $table->decimal("total", 18, 2)->default(0);

            $table->text('comment')->nullable();
            $table->boolean("paid")->default(0);
            $table->string("color")->nullable();

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('doctor_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('center_id')
                ->references('id')->on('centers')
                ->onDelete('cascade');

            $table->foreign('service_id')
                ->references('id')->on('services')
                ->onDelete('cascade');

            $table->foreign('insurance_carrier_id')
                ->references('id')->on('insurance_carriers')
                ->onDelete('cascade');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
