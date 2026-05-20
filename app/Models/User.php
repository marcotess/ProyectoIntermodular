<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo de usuario con autenticacion, roles y permisos de acceso al dominio.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    /**
     * Relacion muchos a muchos con los roles funcionales del sistema.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function reviewedDocuments()
    {
        return $this->belongsToMany(Document::class, 'document_reviewers');
    }

    public function taughtPrs()
    {
        return $this->belongsToMany(PR::class, 'pr_teachers', 'user_id', 'pr_id');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    /**
     * Comprueba si el usuario tiene un rol concreto.
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Comprueba si el usuario tiene alguno de los roles indicados.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function tokenRoleAbilities(): array
    {
        return $this->roles()
            ->pluck('name')
            ->map(fn (string $role) => 'role:' . $role)
            ->values()
            ->all();
    }

    public function accessibleCourseIds(): array
    {
        if ($this->hasRole('gestor')) {
            return Course::query()->pluck('id')->all();
        }

        $courseIds = [];

        if ($this->hasRole('revisor')) {
            $courseIds = array_merge(
                $courseIds,
                Course::query()
                    ->whereHas('prs.documents.reviewers', function ($query) {
                        $query->where('users.id', $this->id);
                    })
                    ->pluck('id')
                    ->all()
            );
        }

        if ($this->hasRole('docente')) {
            $courseIds = array_merge(
                $courseIds,
                Course::query()
                    ->whereHas('prs.teachers', function ($query) {
                        $query->where('users.id', $this->id);
                    })
                    ->pluck('id')
                    ->all()
            );
        }

        return array_values(array_unique($courseIds));
    }

    public function canAccessCourse(Course $course): bool
    {
        return in_array($course->id, $this->accessibleCourseIds(), true);
    }

    public function canAccessPr(PR $pr): bool
    {
        if ($this->hasRole('gestor')) {
            return true;
        }

        if ($this->canAccessCourseAsTeacher($pr->course)) {
            return true;
        }

        if ($this->hasRole('revisor')) {
            return $this->reviewedDocuments()->where('pr_id', $pr->id)->exists();
        }

        return false;
    }

    public function canViewAllDocumentsForPr(PR $pr): bool
    {
        if ($this->hasRole('gestor')) {
            return true;
        }

        return $this->canAccessCourseAsTeacher($pr->course);
    }

    public function canAccessCourseAsTeacher(Course $course): bool
    {
        if (!$this->hasRole('docente')) {
            return false;
        }

        return Course::query()
            ->whereKey($course->id)
            ->whereHas('prs.teachers', function ($query) {
                $query->where('users.id', $this->id);
            })
            ->exists();
    }

    public function canAccessDocument(Document $document): bool
    {
        if ($this->hasRole('gestor')) {
            return true;
        }

        if ($this->canAccessCourseAsTeacher($document->pr->course)) {
            return true;
        }

        if ($this->hasRole('revisor')) {
            return $document->reviewers()->where('users.id', $this->id)->exists();
        }

        return false;
    }

    public function canAccessVariant(DocumentVariant $variant): bool
    {
        return $this->canAccessDocument($variant->document);
    }
}
