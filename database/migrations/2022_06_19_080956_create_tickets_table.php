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
        Schema::dropIfExists('tickets');
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId("patient_id")->nullable()->references("id")->on("users");
            $table->foreignId("therapist_id")->nullable()->references("id")->on("therapists");
            $table->foreignId("ticket_department_id")->nullable()->references("id")->on("ticket_departments");
            $table->date("date");
            $table->string("title");
            $table->string("strike")->nullable();
            $table->string("strike_history")->nullable();
            $table->string("remarks")->nullable();
            $table->boolean("status")->default(true);
            $table->foreignId("assigned")->nullable()->references("id")->on("admins");
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
        Schema::dropIfExists('tickets');
    }
};
