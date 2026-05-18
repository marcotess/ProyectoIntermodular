<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('plantillas')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::rename('plantillas', 'plantillas_prefijo_enum_old');

        Schema::create('plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->string('prefijo');
            $table->integer('version');
        });

        $rows = DB::table('plantillas_prefijo_enum_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('plantillas')->insert([
                'id' => $row->id,
                'tipo_documento' => $row->tipo_documento,
                'prefijo' => $row->prefijo,
                'version' => $row->version,
            ]);
        }

        Schema::drop('plantillas_prefijo_enum_old');

        $this->refreshDocumentsPlantillaForeignKeyForSqlite();

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        if (! Schema::hasTable('plantillas')) {
            return;
        }

        Schema::disableForeignKeyConstraints();

        Schema::rename('plantillas', 'plantillas_prefijo_string_old');

        Schema::create('plantillas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->string('prefijo');
            $table->integer('version');
        });

        $rows = DB::table('plantillas_prefijo_string_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('plantillas')->insert([
                'id' => $row->id,
                'tipo_documento' => $row->tipo_documento,
                'prefijo' => $row->prefijo,
                'version' => $row->version,
            ]);
        }

        Schema::drop('plantillas_prefijo_string_old');

        $this->refreshDocumentsPlantillaForeignKeyForSqlite();

        Schema::enableForeignKeyConstraints();
    }

    private function refreshDocumentsPlantillaForeignKeyForSqlite(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        if (! Schema::hasTable('documents') || ! Schema::hasColumn('documents', 'plantilla_id')) {
            return;
        }

        Schema::rename('documents', 'documents_plantilla_fk_old');
        DB::statement('DROP INDEX IF EXISTS documents_pr_id_canonical_name_unique');

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pr_id')->constrained('prs')->restrictOnDelete();
            $table->foreignId('plantilla_id')->nullable()->constrained('plantillas')->nullOnDelete();
            $table->integer('tema')->nullable();
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

        $rows = DB::table('documents_plantilla_fk_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('documents')->insert([
                'id' => $row->id,
                'pr_id' => $row->pr_id,
                'plantilla_id' => $row->plantilla_id,
                'tema' => $row->tema,
                'type' => $row->type,
                'short_title' => $row->short_title,
                'canonical_name' => $row->canonical_name,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        Schema::drop('documents_plantilla_fk_old');

        $this->refreshDocumentReviewersForeignKeyForSqlite();
        $this->refreshDocumentNameAliasesForeignKeyForSqlite();
        $this->refreshDocumentVariantsForeignKeyForSqlite();
        $this->refreshDocumentStatusHistoriesForeignKeyForSqlite();
    }

    private function refreshDocumentReviewersForeignKeyForSqlite(): void
    {
        if (! Schema::hasTable('document_reviewers')) {
            return;
        }

        Schema::rename('document_reviewers', 'document_reviewers_fk_old');
        DB::statement('DROP INDEX IF EXISTS document_reviewers_document_id_user_id_unique');

        Schema::create('document_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['document_id', 'user_id']);
        });

        $rows = DB::table('document_reviewers_fk_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('document_reviewers')->insert([
                'id' => $row->id,
                'document_id' => $row->document_id,
                'user_id' => $row->user_id,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        Schema::drop('document_reviewers_fk_old');
    }

    private function refreshDocumentNameAliasesForeignKeyForSqlite(): void
    {
        if (! Schema::hasTable('document_name_aliases')) {
            return;
        }

        Schema::rename('document_name_aliases', 'document_name_aliases_fk_old');

        Schema::create('document_name_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->restrictOnDelete();
            $table->string('canonical_name');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        $rows = DB::table('document_name_aliases_fk_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('document_name_aliases')->insert([
                'id' => $row->id,
                'document_id' => $row->document_id,
                'canonical_name' => $row->canonical_name,
                'created_by' => $row->created_by,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }

        Schema::drop('document_name_aliases_fk_old');
    }

    private function refreshDocumentVariantsForeignKeyForSqlite(): void
    {
        if (! Schema::hasTable('document_variants')) {
            return;
        }

        Schema::rename('document_variants', 'document_variants_fk_old');
        DB::statement('DROP INDEX IF EXISTS document_variants_document_id_version_unique');

        Schema::create('document_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->timestamp('deadline_target')->nullable();
            $table->string('drive_link_url')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->foreignId('status_id')->constrained('document_statuses');

            $table->unique(['document_id', 'version']);
        });

        $rows = DB::table('document_variants_fk_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('document_variants')->insert([
                'id' => $row->id,
                'document_id' => $row->document_id,
                'version' => $row->version,
                'deadline_target' => $row->deadline_target,
                'drive_link_url' => $row->drive_link_url,
                'created_by' => $row->created_by,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
                'status_id' => $row->status_id,
            ]);
        }

        Schema::drop('document_variants_fk_old');
    }

    private function refreshDocumentStatusHistoriesForeignKeyForSqlite(): void
    {
        if (! Schema::hasTable('document_status_histories')) {
            return;
        }

        Schema::rename('document_status_histories', 'document_status_histories_fk_old');

        Schema::create('document_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_variant_id')->constrained('document_variants')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->foreignId('from_status_id')->constrained('document_statuses');
            $table->foreignId('to_status_id')->constrained('document_statuses');
        });

        $rows = DB::table('document_status_histories_fk_old')->orderBy('id')->get();

        foreach ($rows as $row) {
            DB::table('document_status_histories')->insert([
                'id' => $row->id,
                'document_variant_id' => $row->document_variant_id,
                'user_id' => $row->user_id,
                'comment' => $row->comment,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
                'from_status_id' => $row->from_status_id,
                'to_status_id' => $row->to_status_id,
            ]);
        }

        Schema::drop('document_status_histories_fk_old');
    }
};