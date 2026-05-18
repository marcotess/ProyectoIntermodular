<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('document_variants', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->foreignId('status_id')->constrained('document_statuses');
        });
    }

    public function down()
    {
        Schema::table('document_variants', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
            $table->enum('status', [
                '01_desarrollo',
                '02_candidato',
                '03_produccion',
                '04_obsoleto',
            ]);
        });
    }
};
