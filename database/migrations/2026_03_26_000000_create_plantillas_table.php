<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->string('prefijo');
            $table->integer('version');
        });
    }

    public function down()
    {
        Schema::dropIfExists('plantillas');
    }
};