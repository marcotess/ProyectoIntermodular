<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\PR;

class PRDocumentsSeeder extends Seeder
{
    public function run()
    {
        // PR específico (puedes cambiar el ID según tu base de datos)
        $pr = PR::first(); // O usa find(ID) para uno concreto
        if (!$pr) return;

        $docs = [
            [
                'type' => 'PLAN_TRABAJO',
                'short_title' => 'Plan de Trabajo',
                'canonical_name' => 'plan_trabajo.pdf',
            ],
            [
                'type' => 'MANUAL',
                'short_title' => 'Manual de Usuario',
                'canonical_name' => 'manual_usuario.pdf',
            ],
            [
                'type' => 'PRACTICA',
                'short_title' => 'Práctica 1',
                'canonical_name' => 'practica1.pdf',
            ],
            [
                'type' => 'GUION100',
                'short_title' => 'Guion 100',
                'canonical_name' => 'guion100.pdf',
            ],
            [
                'type' => 'CUESTIONARIO',
                'short_title' => 'Cuestionario Final',
                'canonical_name' => 'cuestionario_final.pdf',
            ],
        ];

        foreach ($docs as $doc) {
            $document = Document::create(array_merge($doc, ['pr_id' => $pr->id]));
            // Asignar el usuario 3 como revisor
            if ($document && $document->id) {
                \DB::table('document_reviewers')->insert([
                    'document_id' => $document->id,
                    'user_id' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
