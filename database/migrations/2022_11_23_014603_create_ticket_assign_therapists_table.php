<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketAssignTherapistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_assign_therapists', function (Blueprint $table) {
            $table->id();
            $table->foreignId("ticket_id")->nullable()->references("id")->on("tickets");
            $table->foreignId("therapist_id")->nullable()->references("id")->on("therapists");
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
        Schema::dropIfExists('ticket_assign_therapists');
    }
}
