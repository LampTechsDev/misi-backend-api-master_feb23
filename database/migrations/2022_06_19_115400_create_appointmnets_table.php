<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointmnets', function (Blueprint $table) {
            $table->id()->from(1111);
            $table->foreignId("therapist_id")->nullable()->references("id")->on("therapists");
            $table->foreignId("patient_id")->nullable()->references("id")->on("users");
            $table->foreignId("therapist_schedule_id")->nullable()->references("id")->on("therapist_schedules");
            $table->string('number');
            $table->string('history')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('fee');
            $table->string('language');
            $table->enum('type',['Online', 'Physical'])->nullable();
            $table->string('therapist_comment')->nullable();
            $table->string('remarks')->nullable();
            $table->boolean('status');
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("updated_by")->nullable();
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
        Schema::dropIfExists('appointmnets');
    }
};
