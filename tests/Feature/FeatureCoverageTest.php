<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\Course;
use App\Models\Document;
use App\Models\DocumentStatus;
use App\Models\DocumentVariant;
use App\Models\Notificacion;
use App\Models\Plantilla;
use App\Models\PR;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeatureCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_profile(): void
    {
        $this->get(route('profile'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_log_in_and_view_profile(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect(route('profile'));

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertViewIs('profile')
            ->assertViewHas('user', fn (User $profileUser) => $profileUser->is($user));
    }

    public function test_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $this->from(route('login'))
            ->post(route('login.submit'), [
                'email' => $user->email,
                'password' => 'mal-credencial',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_teacher_only_sees_courses_linked_to_their_prs(): void
    {
        $teacher = $this->createUserWithRole('docente', ['name' => 'Dani Docente']);
        $linkedCourse = Course::query()->create(['code' => 'DAW1', 'name' => 'Desarrollo Web']);
        $hiddenCourse = Course::query()->create(['code' => 'ASIR1', 'name' => 'Servicios en Red']);
        $linkedPr = $this->createPr($linkedCourse, 1);
        $this->createPr($hiddenCourse, 1);

        $linkedPr->teachers()->attach($teacher);

        $this->actingAs($teacher)
            ->get(route('courses.index'))
            ->assertOk()
            ->assertViewIs('home')
            ->assertViewHas('courses', function (Collection $courses) use ($linkedCourse, $hiddenCourse) {
                return $courses->pluck('id')->all() === [$linkedCourse->id]
                    && ! $courses->contains('id', $hiddenCourse->id);
            });
    }

    public function test_reviewer_can_open_pr_documents_but_only_sees_assigned_documents(): void
    {
        $reviewer = $this->createUserWithRole('revisor', ['name' => 'Rocio Revisora']);
        $course = Course::query()->create(['code' => 'DAW2', 'name' => 'Proyecto Final']);
        $pr = $this->createPr($course, 7);
        $assignedDocument = $this->createDocument($pr, 'MANUAL', 'manual_principal');
        $hiddenDocument = $this->createDocument($pr, 'PRESENTACION', 'presentacion_final');

        $assignedDocument->reviewers()->attach($reviewer);

        $this->actingAs($reviewer)
            ->get(route('pr.documentos.index', ['pr' => $pr->id]))
            ->assertOk()
            ->assertViewIs('doc')
            ->assertViewHas('documents', function (Collection $documents) use ($assignedDocument, $hiddenDocument) {
                return $documents->count() === 1
                    && $documents->first()->is($assignedDocument)
                    && ! $documents->contains('id', $hiddenDocument->id);
            });
    }

    public function test_teacher_cannot_use_manager_only_routes(): void
    {
        $teacher = $this->createUserWithRole('docente');
        $course = Course::query()->create(['code' => 'SMR1', 'name' => 'Aplicaciones Ofimaticas']);

        $this->actingAs($teacher)
            ->post(route('courses.pr.create', ['course' => $course->id]))
            ->assertForbidden();
    }

    public function test_manager_can_create_a_pr_for_a_course(): void
    {
        $manager = $this->createUserWithRole('gestor');
        $course = Course::query()->create(['code' => 'DAW3', 'name' => 'Despliegue de Aplicaciones']);
        $this->createPr($course, 1);

        $this->actingAs($manager)
            ->postJson(route('courses.pr.create', ['course' => $course->id]))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('prs', [
            'course_id' => $course->id,
            'number' => 2,
            'nombre' => 'Proyecto 2',
        ]);
    }

    public function test_manager_can_create_a_document_with_initial_variant_and_file(): void
    {
        Storage::fake('public');

        $manager = $this->createUserWithRole('gestor');
        $course = Course::query()->create(['code' => 'DAW4', 'name' => 'Diseno de Interfaces']);
        $pr = $this->createPr($course, 3);
        $plantilla = $this->createStoredPlantilla('MANUAL');

        $response = $this->actingAs($manager)
            ->post(route('pr.documentos.create', ['pr' => $pr->id]), [
                'type' => 'MANUAL',
            ]);

        $response->assertRedirect(route('pr.documentos.index', ['pr' => $pr->id]));

        $document = Document::query()->where('pr_id', $pr->id)->first();

        $this->assertNotNull($document);
        $this->assertSame($plantilla->id, $document->plantilla_id);

        $variant = DocumentVariant::query()->where('document_id', $document->id)->first();

        $this->assertNotNull($variant);
        $this->assertNotNull($variant->drive_link_url);

        $variantPath = $this->storagePathFromUrl($variant->drive_link_url);

        $this->assertNotNull($variantPath);
        Storage::disk('public')->assertExists($variantPath);
    }

    public function test_reviewer_can_update_pr_phase_name_and_deadline_for_an_assigned_pr(): void
    {
        $reviewer = $this->createUserWithRole('revisor');
        $course = Course::query()->create(['code' => 'DAW7', 'name' => 'Revision de Entregas']);
        $pr = $this->createPr($course, 4, ['nombre' => 'Proyecto antiguo']);
        $document = $this->createDocument($pr, 'MANUAL', 'manual_revision');
        $document->reviewers()->attach($reviewer);

        $this->actingAs($reviewer)
            ->postJson(route('pr.fase.update', ['pr' => $pr->id]), [
                'fase' => 'Temario final',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($reviewer)
            ->postJson(route('pr.nombre.update', ['pr' => $pr->id]), [
                'nombre' => 'Proyecto revisado',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($reviewer)
            ->postJson(route('pr.fecha_limite.update', ['pr' => $pr->id]), [
                'fecha_limite' => '2026-06-30',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('prs', [
            'id' => $pr->id,
            'fase' => 'Temario final',
            'nombre' => 'Proyecto revisado',
            'fecha_limite' => '2026-06-30',
        ]);
    }

    public function test_unrelated_reviewer_cannot_update_another_pr(): void
    {
        $reviewer = $this->createUserWithRole('revisor');
        $course = Course::query()->create(['code' => 'DAW8', 'name' => 'Control de Accesos']);
        $pr = $this->createPr($course, 1, ['nombre' => 'Proyecto protegido']);

        $this->actingAs($reviewer)
            ->postJson(route('pr.nombre.update', ['pr' => $pr->id]), [
                'nombre' => 'Cambio no permitido',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('prs', [
            'id' => $pr->id,
            'nombre' => 'Proyecto protegido',
        ]);
    }

    public function test_variant_status_update_changes_state_and_blocks_conflicts(): void
    {
        $reviewer = $this->createUserWithRole('revisor');
        $course = Course::query()->create(['code' => 'DAW9', 'name' => 'Estados Documentales']);
        $pr = $this->createPr($course, 6);
        $document = $this->createDocument($pr, 'MANUAL', 'manual_estados');
        $document->reviewers()->attach($reviewer);

        $desarrollo = DocumentStatus::query()->firstOrCreate(['name' => '01_desarrollo']);
        $candidato = DocumentStatus::query()->firstOrCreate(['name' => '02_candidato']);
        $produccion = DocumentStatus::query()->firstOrCreate(['name' => '03_produccion']);

        $firstVariant = $this->createVariant($document, $reviewer, [
            'version' => 1,
            'status_id' => $desarrollo->id,
        ]);
        $secondVariant = $this->createVariant($document, $reviewer, [
            'version' => 2,
            'status_id' => $produccion->id,
        ]);

        $this->actingAs($reviewer)
            ->postJson(route('variant.status.update', ['variant' => $firstVariant->id]), [
                'status_id' => $candidato->id,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('document_variants', [
            'id' => $firstVariant->id,
            'status_id' => $candidato->id,
        ]);

        $thirdVariant = $this->createVariant($document, $reviewer, [
            'version' => 3,
            'status_id' => $desarrollo->id,
        ]);

        $this->actingAs($reviewer)
            ->postJson(route('variant.status.update', ['variant' => $thirdVariant->id]), [
                'status_id' => $candidato->id,
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Solo puede haber una variante activa (Desarrollo o Candidato) por documento.',
            ]);

        $this->assertDatabaseHas('document_variants', [
            'id' => $thirdVariant->id,
            'status_id' => $desarrollo->id,
        ]);
    }

    public function test_opening_a_notification_marks_it_as_read(): void
    {
        $user = User::factory()->create();
        $notification = Notificacion::query()->create([
            'tema' => 'Aviso de prueba',
            'user_id' => $user->id,
            'mensaje' => 'Hay un cambio que revisar.',
            'link' => route('profile'),
            'fecha_envio' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('notificaciones.open', ['notificacion' => $notification->id]))
            ->assertRedirect(route('profile'));

        $this->assertDatabaseMissing('notificaciones', [
            'id' => $notification->id,
            'fecha_lectura' => null,
        ]);
    }

    public function test_tasks_page_shows_teacher_and_reviewer_work_ordered_by_deadline(): void
    {
        $user = $this->createUserWithRoles(['docente', 'revisor'], ['name' => 'Nora Mixta']);
        $course = Course::query()->create(['code' => 'DAW5', 'name' => 'Proyecto Integrado']);
        $pr = $this->createPr($course, 5, [
            'fecha_limite' => Carbon::now()->addDays(2),
        ]);
        $pr->teachers()->attach($user);

        $document = $this->createDocument($pr, 'MANUAL', 'manual_tareas');
        $document->reviewers()->attach($user);
        $this->createVariant($document, $user, [
            'deadline_target' => Carbon::now()->addDay(),
        ]);

        $this->actingAs($user)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertViewIs('tareas')
            ->assertViewHas('summary', fn (array $summary) => $summary['total'] === 2 && $summary['next_seven_days'] === 2)
            ->assertSee('Proyectos y documentos asignados')
            ->assertSee('MANUAL')
            ->assertSee('PR 5')
            ->assertSee('Abrir documento')
            ->assertSee('Ir al elemento');
    }

    public function test_chat_message_creates_message_and_notification(): void
    {
        $manager = $this->createUserWithRole('gestor', ['name' => 'Paula Gestora']);
        $teacher = $this->createUserWithRole('docente', ['name' => 'Lucia Docente']);

        $this->actingAs($manager)
            ->post(route('chat.messages.store', ['contact' => $teacher->id]), [
                'message' => 'Necesito que revises el documento final',
            ])
            ->assertRedirect(route('chat.show', ['contact' => $teacher->id]));

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $manager->id,
            'recipient_id' => $teacher->id,
            'message' => 'Necesito que revises el documento final',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $teacher->id,
            'tema' => 'Nuevo mensaje de chat',
            'mensaje' => 'Paula Gestora te ha enviado un mensaje nuevo por el chat.',
        ]);
    }

    public function test_opening_an_explicit_chat_marks_incoming_messages_as_read(): void
    {
        $teacher = $this->createUserWithRole('docente', ['name' => 'Diego Docente']);
        $reviewer = $this->createUserWithRole('revisor', ['name' => 'Raul Revisor']);
        $incomingMessage = ChatMessage::query()->create([
            'sender_id' => $reviewer->id,
            'recipient_id' => $teacher->id,
            'message' => 'Tienes cambios pendientes',
        ]);

        $this->actingAs($teacher)
            ->get(route('chat.show', ['contact' => $reviewer->id]))
            ->assertOk()
            ->assertViewIs('chat');

        $this->assertDatabaseMissing('chat_messages', [
            'id' => $incomingMessage->id,
            'read_at' => null,
        ]);
    }

    public function test_profile_view_exposes_accessible_controls_for_settings_panel(): void
    {
        $user = $this->createUserWithRole('docente');

        $this->actingAs($user)
            ->get(route('profile'))
            ->assertOk()
            ->assertSee('aria-controls="profile-settings-panel"', false)
            ->assertSee('aria-expanded="false"', false)
            ->assertSee('role="region"', false)
            ->assertSee('aria-label="Panel de ajustes del perfil"', false);
    }

    public function test_chat_view_exposes_accessible_message_regions(): void
    {
        $teacher = $this->createUserWithRole('docente', ['name' => 'Marta Docente']);
        $reviewer = $this->createUserWithRole('revisor', ['name' => 'Santi Revisor']);

        ChatMessage::query()->create([
            'sender_id' => $reviewer->id,
            'recipient_id' => $teacher->id,
            'message' => 'Mensaje accesible',
        ]);

        $this->actingAs($teacher)
            ->get(route('chat.show', ['contact' => $reviewer->id]))
            ->assertOk()
            ->assertSee('role="log"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('aria-describedby="message-help"', false)
            ->assertSee('Historial de mensajes');
    }

    public function test_document_view_exposes_accessible_controls_for_document_management(): void
    {
        $manager = $this->createUserWithRole('gestor');
        $course = Course::query()->create(['code' => 'DAW6', 'name' => 'Interfaces Accesibles']);
        $pr = $this->createPr($course, 2);
        $document = $this->createDocument($pr, 'MANUAL', 'manual_accesibilidad');

        $this->actingAs($manager)
            ->get(route('pr.documentos.index', ['pr' => $pr->id]))
            ->assertOk()
            ->assertSee('aria-controls="create-document-form"', false)
            ->assertSee('aria-label="Formulario de creación de documento"', false)
            ->assertSee('aria-controls="variants-row-' . $document->id . '"', false)
            ->assertSee('Listado de documentos del proyecto con plantilla, tema, estado, revisores y variantes.', false);
    }

    public function test_api_login_returns_token_and_allows_access_to_courses(): void
    {
        $teacher = $this->createUserWithRole('docente', [
            'email' => 'api-docente@example.test',
            'password' => bcrypt('secret123'),
        ]);
        $course = Course::query()->create(['code' => 'API1', 'name' => 'Curso API']);
        $pr = $this->createPr($course, 1);
        $pr->teachers()->attach($teacher);

        $loginResponse = $this->postJson(route('api.login'), [
            'email' => 'api-docente@example.test',
            'password' => 'secret123',
        ]);

        $loginResponse
            ->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'user']);

        $token = $loginResponse->json('token');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson(route('api.courses.index'))
            ->assertOk()
            ->assertJsonFragment([
                'codigo' => 'API1',
                'nombre' => 'Curso API',
            ]);
    }

    public function test_api_logout_revokes_current_token(): void
    {
        $teacher = $this->createUserWithRole('docente', [
            'email' => 'logout-api@example.test',
            'password' => bcrypt('secret123'),
        ]);

        $token = $teacher->createToken('web-client', $teacher->tokenRoleAbilities())->plainTextToken;
        $tokenId = Str::before($token, '|');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.logout'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => (int) $tokenId,
        ]);
    }

    public function test_api_manager_routes_require_manager_role(): void
    {
        $teacher = $this->createUserWithRole('docente');
        $course = Course::query()->create(['code' => 'API2', 'name' => 'Ruta protegida']);
        $token = $teacher->createToken('teacher-client', $teacher->tokenRoleAbilities())->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.courses.pr.create', ['course' => $course->id]))
            ->assertForbidden();
    }

    public function test_api_manager_can_create_pr_and_read_document_listing(): void
    {
        $manager = $this->createUserWithRole('gestor');
        $course = Course::query()->create(['code' => 'API3', 'name' => 'Gestion API']);
        $token = $manager->createToken('manager-client', $manager->tokenRoleAbilities())->plainTextToken;

        $createResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.courses.pr.create', ['course' => $course->id]));

        $createResponse
            ->assertOk()
            ->assertJson(['success' => true]);

        $prId = $createResponse->json('pr_id');
        $document = $this->createDocument(PR::query()->findOrFail($prId), 'MANUAL', 'manual_api');

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson(route('api.pr.documentos.index', ['pr' => $prId]))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $document->id,
                'type' => 'MANUAL',
            ]);
    }

    private function createUserWithRole(string $roleName, array $attributes = []): User
    {
        return $this->createUserWithRoles([$roleName], $attributes);
    }

    private function createUserWithRoles(array $roleNames, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);

        foreach ($roleNames as $roleName) {
            $role = Role::query()->firstOrCreate(['name' => $roleName]);
            $user->roles()->attach($role);
        }

        return $user;
    }

    private function createPr(Course $course, int $number, array $attributes = []): PR
    {
        return PR::query()->create(array_merge([
            'course_id' => $course->id,
            'number' => $number,
            'fase' => PR::DEFAULT_FASE,
        ], $attributes));
    }

    private function createDocument(PR $pr, string $type, string $canonicalName): Document
    {
        return Document::query()->create([
            'pr_id' => $pr->id,
            'type' => $type,
            'short_title' => ucfirst(strtolower(str_replace('_', ' ', $type))),
            'canonical_name' => $canonicalName,
        ]);
    }

    private function createVariant(Document $document, User $user, array $attributes = []): DocumentVariant
    {
        $status = DocumentStatus::query()->firstOrCreate(['name' => '01_desarrollo']);

        return DocumentVariant::query()->create(array_merge([
            'document_id' => $document->id,
            'version' => 1,
            'status_id' => $status->id,
            'created_by' => $user->id,
        ], $attributes));
    }

    private function createStoredPlantilla(string $documentType): Plantilla
    {
        $plantilla = Plantilla::query()->create([
            'tipo_documento' => $documentType,
            'prefijo' => 'TPL',
            'version' => 1,
        ]);

        Storage::disk('public')->put('plantillas/' . $plantilla->display_prefijo . '.docx', 'contenido de prueba');

        return $plantilla;
    }

    private function storagePathFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if (! is_string($path) || ! str_contains($path, '/storage/')) {
            return null;
        }

        return ltrim(Str::after($path, '/storage/'), '/');
    }
}