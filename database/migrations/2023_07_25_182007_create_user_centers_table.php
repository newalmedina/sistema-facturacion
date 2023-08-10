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
        Schema::create('user_centers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('center_id');
            $table->foreign('center_id')
                ->references('id')->on('centers')
                ->onDelete('cascade');
            $table->unique(['user_id', 'center_id'], 'user_center_unique_field');
            $table->timestamps();
        });

        Schema::table('user_profiles', function (Blueprint $table) {

            $table->unsignedBigInteger('selected_center')->after("user_id")->nullable();
            $table->foreign('selected_center', "user_fk_selected_center")
                ->references('id')->on('centers')
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
        Schema::dropIfExists('user_centers');
        Schema::disableForeignKeyConstraints();
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropForeign("user_fk_selected_center");
            $table->dropColumn("selected_center");
        });
        Schema::enableForeignKeyConstraints();
    }
};
