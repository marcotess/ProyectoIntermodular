<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->unsignedInteger('number');
            $table->timestamp('deadline')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('prs');
    }
};
