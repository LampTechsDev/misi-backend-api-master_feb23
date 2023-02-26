<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId("ticket_id")->references("id")->on("tickets")->cascadeOnDelete();
            $table->string("file_name");
            $table->string("file_url");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_uploads');
    }
}
