<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePitFormulasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pit_formulas');
        Schema::create('pit_formulas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId("patient_id")->nullable()->references("id")->on('users');
            $table->foreignId("ticket_id")->nullable()->references("id")->on('tickets');
            $table->string('type_of_legitimation')->nullable();
            $table->string('document_number')->nullable();
            $table->date('identify_expire_date')->nullable();
            $table->enum("status", ["active", 'inactive', 'pending', 'cancel'])->default("active");
            $table->string("remarks")->nullable();
            $table->foreignId("created_by")->nullable()->references("id")->on('admins');
            $table->foreignId("updated_by")->nullable()->references("id")->on('admins');
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
        Schema::dropIfExists('pit_formulas');
    }
}
