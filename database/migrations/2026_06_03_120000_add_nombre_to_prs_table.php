<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('prs', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('number');
        });

        DB::table('prs')
            ->select(['id', 'number'])
            ->orderBy('id')
            ->chunkById(100, function ($prs) {
                foreach ($prs as $pr) {
                    DB::table('prs')
                        ->where('id', $pr->id)
                        ->update(['nombre' => 'Proyecto ' . $pr->number]);
                }
            });
    }

    public function down()
    {
        Schema::table('prs', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });
    }
};