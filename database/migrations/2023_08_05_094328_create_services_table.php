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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->decimal("price", 18, 2)->default(0);
            $table->text("description")->nullable();
            $table->boolean("active")->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_insurance_carriers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('insurance_carrier_id');
            $table->decimal("price", 18, 2)->default(0);
            $table->foreign('service_id')
                ->references('id')->on('services')
                ->onDelete('cascade');

            $table->foreign('insurance_carrier_id')
                ->references('id')->on('insurance_carriers')
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
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_insurance_carriers');
    }
};
