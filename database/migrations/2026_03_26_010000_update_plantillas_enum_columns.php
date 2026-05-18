<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $allowedDocumentTypes = [
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
        ];
        $allowedPrefixes = [
            'PDE33',
            'TDC02',
            'PTI12',
            'PGC15',
            'PED01',
            'PTG9',
            'PTG2',
            'PDI13',
            'PDO22',
            'PRE7',
            'PPE19',
            'PPR18',
            'PPR19',
        ];

        $invalidDocumentTypes = DB::table('plantillas')
            ->whereNotIn('tipo_documento', $allowedDocumentTypes)
            ->pluck('tipo_documento')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $invalidPrefixes = DB::table('plantillas')
            ->whereNotIn('prefijo', $allowedPrefixes)
            ->pluck('prefijo')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($invalidDocumentTypes !== []) {
            throw new RuntimeException('Valores invalidos en plantillas.tipo_documento: ' . implode(', ', $invalidDocumentTypes));
        }

        if ($invalidPrefixes !== []) {
            throw new RuntimeException('Valores invalidos en plantillas.prefijo: ' . implode(', ', $invalidPrefixes));
        }

        Schema::rename('plantillas', 'plantillas_old');

        Schema::create('plantillas', function (Blueprint $table) use ($allowedDocumentTypes, $allowedPrefixes) {
            $table->id();
            $table->enum('tipo_documento', $allowedDocumentTypes);
            $table->enum('prefijo', $allowedPrefixes);
            $table->integer('version');
        });

        $rows = DB::table('plantillas_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('plantillas')->insert([
                'id' => $row->id,
                'tipo_documento' => $row->tipo_documento,
                'prefijo' => $row->prefijo,
                'version' => $row->version,
            ]);
        }

        Schema::drop('plantillas_old');
    }

    public function down(): void
    {
        Schema::rename('plantillas', 'plantillas_enum_old');

        Schema::create('plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->string('prefijo');
            $table->integer('version');
        });

        $rows = DB::table('plantillas_enum_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('plantillas')->insert([
                'id' => $row->id,
                'tipo_documento' => $row->tipo_documento,
                'prefijo' => $row->prefijo,
                'version' => $row->version,
            ]);
        }

        Schema::drop('plantillas_enum_old');
    }
};