<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppointmentTypeStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointmnets', function (Blueprint $table) {
            $table->string("cancel_appointment_type")->nullable();
            $table->text("cancel_reason")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointmnets', function (Blueprint $table) {
            Schema::dropIfExists("appointmnets");
        });
    }
}
