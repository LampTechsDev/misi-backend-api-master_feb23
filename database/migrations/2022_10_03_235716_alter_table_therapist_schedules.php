<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTherapistSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('therapist_schedules');

        Schema::create('therapist_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId("therapist_id")->references("id")->on("therapists");
            $table->foreignId("patient_id")->nullable()->references("id")->on("users");
            $table->date('date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum("status", ["open", "closed", "pending", "booked"])->default("open");
            $table->text('remarks')->nullable();
            $table->foreignId("created_by")->nullable()->references("id")->on("admins");
            $table->foreignId("updated_by")->nullable()->references("id")->on("admins");
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
        Schema::dropIfExists('therapist_schedules');
    }
}
