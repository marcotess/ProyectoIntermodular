<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('prs', function (Blueprint $table) {
            $table->date('fecha_limite')->nullable()->after('deadline');
        });
    }

    public function down()
    {
        Schema::table('prs', function (Blueprint $table) {
            $table->dropColumn('fecha_limite');
        });
    }
};
