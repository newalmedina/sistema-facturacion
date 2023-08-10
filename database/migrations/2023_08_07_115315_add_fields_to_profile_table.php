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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string("identification", "50")->nullable()->after("mobile");
            $table->unsignedBigInteger("province_id")->nullable()->after("identification");
            $table->unsignedBigInteger("municipio_id")->nullable()->after("province_id");
            $table->string("address")->nullable()->after("municipio_id");
            $table->date("birthday")->nullable()->after("identification");

            $table->foreign('province_id', 'user_profiles_fk_province')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');

            $table->foreign('municipio_id', 'user_profiles_fk_municipio')
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

        Schema::disableForeignKeyConstraints();
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropForeign("user_profiles_fk_province");
            $table->dropForeign("user_profiles_fk_municipio");
            $table->dropColumn("province_id");
            $table->dropColumn("municipio_id");
            $table->dropColumn("identification");
            $table->dropColumn("birthday");
            $table->dropColumn("address");
        });

        Schema::enableForeignKeyConstraints();
    }
};
