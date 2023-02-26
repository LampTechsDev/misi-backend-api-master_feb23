<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentIntakesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_intakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("appointment_id")->nullable()->references("id")->on("appointmnets");
            $table->date("intake_date")->nullable();
            $table->integer("intake_number")->nullable();
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
        Schema::dropIfExists('appointment_intakes');
    }
}
