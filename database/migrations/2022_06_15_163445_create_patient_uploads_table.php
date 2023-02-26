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
        Schema::create('patient_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId("patient_id")->references("id")->on("users");
            $table->string("file_name");
            $table->string("file_url");
            $table->enum("file_type", ["NID","Driving", "Others"]);
            $table->boolean("status");
            $table->text("remarks")->nullable();
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
        Schema::dropIfExists('patient_uploads');
    }
};
