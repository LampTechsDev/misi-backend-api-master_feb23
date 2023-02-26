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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->from(10000);
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('blood_group_id')->nullable();

            $table->enum('source',["ZD", "Own", "Others"])->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('alternet_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('area')->nullable();
            $table->string('city')->nullable();

            $table->string('bsn_number')->nullable();
            $table->string('dob_number')->nullable();
            $table->string('insurance_number')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->string('age')->nullable();
            $table->string('bio')->nullable();
            $table->enum('gender',["Male", "Female", "Other"])->nullable();
            $table->enum("marital_status", ["Single", "Married", "Divorced"])->nullable();
            $table->text("medical_history")->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string("occupation")->nullable();
            $table->text("remarks")->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('image')->nullable();
            $table->string('image_url')->nullable();

            $table->rememberToken();
            $table->unsignedBigInteger("created_by")->nullable();
            $table->unsignedBigInteger("updated_by")->nullable();
            $table->timestamps();
            $table->string('group')->nullable();

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
        Schema::dropIfExists('users');
    }
};
