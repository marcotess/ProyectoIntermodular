<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_variant_id')->constrained('document_variants')->restrictOnDelete();
            $table->enum('from_status', [
                '01_desarrollo',
                '02_candidato',
                '03_produccion',
                '04_obsoleto',
            ]);
            $table->enum('to_status', [
                '01_desarrollo',
                '02_candidato',
                '03_produccion',
                '04_obsoleto',
            ]);
            $table->foreignId('user_id')->constrained('users');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_status_histories');
    }
};
