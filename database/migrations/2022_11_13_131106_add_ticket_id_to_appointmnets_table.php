<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketIdToAppointmnetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointmnets', function (Blueprint $table) {
            $table->foreignId("ticket_id")->nullable()->references("id")->on('tickets');
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
            Schema::dropIfExists('appointmnets');
        });
    }
}
