<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePibScalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pib_scales');
        Schema::create('pib_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId("patient_id")->references("id")->on("users");
            $table->foreignId("pib_formula_id")->references("id")->on("pib_formulas");
            $table->foreignId("question_id")->nullable()->references("id")->on("questions");
            $table->integer("scale_value");
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
        Schema::dropIfExists('pib_scales');
    }
}
