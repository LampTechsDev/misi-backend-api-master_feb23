<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketHistoryActivitiesLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_history_activities_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id')->nullable();
            $table->integer('patient_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->text('activity_message')->nullable();
            $table->dateTime('date_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_history_activities_logs');
    }
}
