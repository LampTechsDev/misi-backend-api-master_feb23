<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string("mono_multi_zd")->nullable();
            $table->string("mono_multi_screeing")->nullable();
            $table->string("intakes_therapist")->nullable();
            $table->string("tresonit_number")->nullable();
            $table->string("datum_intake")->nullable();
            $table->string("datum_intake_2")->nullable();
            $table->string("nd_account")->nullable();
            $table->string("avc_alfmvm_sbg")->nullable();
            $table->string("honos")->nullable();
            $table->string("berha_intake")->nullable();
            $table->string("rom_start")->nullable();
            $table->string("rom_end")->nullable();
            $table->string("berha_eind")->nullable();
            $table->string("vtcb_date")->nullable();
            $table->string("closure")->nullable();
            $table->string("aanm_intake_1")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            Schema::dropIfExists("tickets");
        });
    }
}
