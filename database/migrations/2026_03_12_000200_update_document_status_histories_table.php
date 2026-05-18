<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_status_histories', function (Blueprint $table) {
            $table->dropColumn(['from_status', 'to_status']);
            $table->foreignId('from_status_id')->constrained('document_statuses');
            $table->foreignId('to_status_id')->constrained('document_statuses');
        });
    }

    public function down()
    {
        Schema::table('document_status_histories', function (Blueprint $table) {
            $table->dropForeign(['from_status_id']);
            $table->dropForeign(['to_status_id']);
            $table->dropColumn(['from_status_id', 'to_status_id']);
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
        });
    }
};
