<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('document_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->enum('status', [
                '01_desarrollo',
                '02_candidato',
                '03_produccion',
                '04_obsoleto',
            ]);
            $table->timestamp('deadline_target')->nullable();
            $table->string('drive_link_url')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['document_id', 'version']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_variants');
    }
};
