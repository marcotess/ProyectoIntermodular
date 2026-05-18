<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $phases = [
            'Temario preliminar',
            'Temario final',
            'Generación de contenidos',
            'Generación de contenidos y vídeos',
            'Generación de vídeos',
            'Finalizado',
        ];

        Schema::table('prs', function (Blueprint $table) use ($phases) {
            $table->enum('fase', $phases)->default('Temario preliminar')->after('number');
        });
    }

    public function down()
    {
        Schema::table('prs', function (Blueprint $table) {
            $table->dropColumn('fase');
        });
    }
};