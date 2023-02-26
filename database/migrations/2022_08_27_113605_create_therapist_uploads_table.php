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
        Schema::create('therapist_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId("therapist_id")->nullable()->references("id")->on('therapists')->cascadeOnDelete();
            $table->string("file_name");
            $table->string("file_url");
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
        Schema::dropIfExists('therapist_uploads');
    }
};
