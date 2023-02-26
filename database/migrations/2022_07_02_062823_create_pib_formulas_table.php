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
        Schema::create('pib_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId("patient_id")->nullable()->references("id")->on('users');
            $table->string('type')->nullable();
            $table->string('number')->nullable();
            $table->date('expiration_date')->nullable();
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
        Schema::dropIfExists('pib_formulas');
    }
};
