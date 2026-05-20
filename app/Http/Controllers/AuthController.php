<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Notificacion;
use App\Models\PR;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Nombre del token emitido para el cliente web cuando se usa la API.
     */
    private const API_TOKEN_NAME = 'web-client';

    private function isApiRequest(Request $request): bool
    {
        // El mismo controlador sirve para vistas Blade y para endpoints JSON bajo /api.
        return $request->is('api/*') || $request->expectsJson() || $request->wantsJson();
    }

    private function apiUserPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles()->pluck('name')->values()->all(),
        ];
    }

    private function apiCoursePayloads(User $user): array
    {
        return Course::query()
            ->whereIn('id', $user->accessibleCourseIds())
            ->orderBy('code')
            ->get()
            ->map(fn ($course) => [
                'id' => $course->id,
                'codigo' => $course->code,
                'nombre' => $course->name,
            ])
            ->values()
            ->all();
    }

    private function accessibleCourses(User $user)
    {
        return Course::query()
            ->with([
                'prs' => function ($query) {
                    $query->with(['teachers', 'documents'])
                        ->orderByDesc('number');
                },
            ])
            ->whereIn('id', $user->accessibleCourseIds())
            ->orderBy('code')
            ->get();
    }

    private function recentNotifications(User $user, int $limit = 8)
    {
        return $user->notificaciones()
            ->orderByRaw('fecha_lectura is null desc')
            ->orderByDesc('fecha_envio')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Muestra la pantalla de acceso para la interfaz web.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('profile');
        }

        return view('login');
    }

    /**
     * Valida las credenciales y autentica al usuario en web o API.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($this->isApiRequest($request)) {
            $user = User::query()->where('email', $credentials['email'])->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'message' => 'Credenciales incorrectas',
                ], 401);
            }

            // Se invalida el token anterior de este cliente y se emite uno nuevo al iniciar sesión.
            $user->tokens()->where('name', self::API_TOKEN_NAME)->delete();
            $token = $user->createToken(self::API_TOKEN_NAME, $user->tokenRoleAbilities())->plainTextToken;

            // La API devuelve el token y el usuario serializado para que el frontend lo guarde.
            return response()->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => $this->apiUserPayload($user),
            ]);
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('profile');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas',
        ]);
    }

    /**
     * Devuelve la vista inicial o el listado de cursos accesibles en formato JSON.
     */
    public function home(Request $request)
    {
        $user = Auth::user();

        if ($this->isApiRequest($request)) {
            // En /api/home equivalente, la pantalla de cursos se representa como JSON.
            return response()->json($this->apiCoursePayloads($user));
        }

        $courses = \App\Models\Course::query()->whereIn('id', $user->accessibleCourseIds())->get();

        return view('home', compact('courses'));
    }

    /**
     * Muestra el perfil-resumen del usuario autenticado.
     */
    public function profile()
    {
        $user = Auth::user();
        $courses = $this->accessibleCourses($user);
        $recentNotifications = $this->recentNotifications($user);
        $latestPr = $courses
            ->flatMap(fn ($course) => $course->prs)
            ->sortByDesc(function (PR $pr) {
                return $pr->updated_at ?? $pr->created_at;
            })
            ->first();
        $latestNotification = $recentNotifications->first();

        return view('profile', compact('user', 'courses', 'recentNotifications', 'latestPr', 'latestNotification'));
    }

    /**
     * Muestra la vista de cursos accesibles para el usuario.
     */
    public function courses()
    {
        $user = Auth::user();
        $courses = $this->accessibleCourses($user);

        return view('home', compact('courses'));
    }

    /**
     * Cierra sesión de usuario.
     */
    public function logout(Request $request)
    {
        if ($this->isApiRequest($request)) {
            // Si la petición entró con Bearer token, se revoca solo ese token actual.
            $request->user()?->currentAccessToken()?->delete();

            if ($request->hasSession()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return response()->json(['success' => true]);
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
