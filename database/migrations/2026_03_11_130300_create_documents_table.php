<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pr_id')->constrained('prs')->restrictOnDelete();
            $table->enum('type', [
                'DEFINICION',
                'COMPETENCIA',
                'TIMING',
                'PLAN_TRABAJO',
                'ESTIMACION_DEDICACION',
                'GUION100',
                'GUION600',
                'INSTALACION',
                'MANUAL',
                'PRESENTACION',
                'EJERCICIOS',
                'PRACTICA',
                'CUESTIONARIO',
            ]);
            $table->string('short_title');
            $table->string('canonical_name');
            $table->timestamps();

            $table->unique(['pr_id', 'canonical_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
