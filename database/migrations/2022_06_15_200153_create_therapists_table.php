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
        Schema::create('therapists', function (Blueprint $table) {
            $table->id()->from(10000);
            $table->string("first_name");
            $table->string("last_name");
            $table->string("email");
            $table->string("phone")->nullable();
            $table->string("address")->nullable();
            $table->string("language")->nullable();
            $table->string("bsn_number")->nullable();
            $table->string("dob_number")->nullable();
            $table->string("insurance_number")->nullable();
            $table->string("emergency_contact")->nullable();
            $table->enum("gender",['Male', 'Female', 'Other'])->nullable();
            $table->date("date_of_birth")->nullable(); 
            $table->boolean("status");
            $table->foreignId("therapist_type_id")->nullable()->references('id')->on('therapist_types');
            $table->foreignId("blood_group_id")->nullable()->references('id')->on('blood_groups');
            $table->foreignId("state_id")->nullable()->references('id')->on('states');
            $table->foreignId("country_id")->nullable()->references('id')->on('countries');
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("updated_by")->nullable();
            $table->string("password");
            //$table->string('image')->nullable();
            //$table->string('image_url')->nullable();
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
        Schema::dropIfExists('therapists');
    }
};
