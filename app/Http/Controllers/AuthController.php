<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // nombre del tokken
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
        return \App\Models\Course::query()
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

    /**
     * muestro el formulario(voy a cambiar las vistas seguramente)
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/home');
        }

        return view('login');
    }

    /**
     * aqui valido credenciales y autentico al usuario. si es correcto lo redirijo a home. sino vuelvo al login con un error
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
            return redirect('/home');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas',
        ]);
    }

    /**
     *pagina de inicio despus de autenticar
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
