<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntoAppointmentTabe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("appointmnets", function(Blueprint $table){
            $table->dropColumn("time");
            $table->dateTime("start_time")->nullable()->after('date');
            $table->dateTime("end_time")->nullable()->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("appointmnets", function(Blueprint $table){
            $table->dateTime("time")->nullable()->after('date');
            $table->dropColumn("start_time")->after('date');
            $table->dropColumn("end_time")->after('start_time');
        });
    }
}
