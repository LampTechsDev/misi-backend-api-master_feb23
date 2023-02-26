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
        Schema::create('therapist_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId("therapist_id")->nullable()->references("id")->on('therapists');
            $table->string("name");
            $table->boolean("status");
            $table->foreignId("service_category_id")->nullable()->references("id")->on('service_categories');
            $table->foreignId("service_sub_category_id")->nullable()->references("id")->on('service_sub_categories');
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
        Schema::dropIfExists('therapist_services');
    }
};
