<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTherapistScheduleSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('therapist_schedule_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("therapist_id")->references("id")->on("therapists");
            $table->integer("interval_time")->comment("Time in Mins");
            $table->time("start_time");
            $table->time("end_time");
            $table->string("holiday")->nullable();
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
        Schema::dropIfExists('therapist_schedule_settings');
    }
}
