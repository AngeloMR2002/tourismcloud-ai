<?php

namespace App\Modules\Usuarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\Usuario;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class AuthController extends Controller
{
    // Iniciar redirección a Google
    public function redirectToGoogle()
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->with('error', __('auth.google_not_configured'));
        }

        return Socialite::driver('google')->redirect();
    }

    // Callback de autenticación con Google
    public function handleGoogleCallback()
    {
        try {
            // $googleUser = Socialite::driver('google')->user(); //Activalor en producción
            // Creamos un cliente Guzzle que desactiva 'verify' solo para desarrollo local
            /** @var AbstractProvider $provider */
            $provider = Socialite::driver('google');

            $googleUser = $provider
                ->setHttpClient(new Client(['verify' => false]))
                ->stateless()
                ->user();
        } catch (\Throwable $e) {
            Log::error('Error en autenticación Google: '.$e->getMessage());

            return redirect()->route('login')->with('error', __('auth.google_failed'));
        }

        // Buscar si ya existe por google_id o por email
        $usuario = Usuario::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($usuario) {
            $updates = [];
            if (! $usuario->google_id) {
                $updates['google_id'] = $googleUser->getId();
            }
            if (! $usuario->email_verified_at) {
                $updates['email_verified_at'] = Carbon::now();
            }
            if (! empty($updates)) {
                $usuario->update($updates);
            }

            if ($usuario->estado !== 'activo') {
                return redirect()->route('login')->with('error', __('auth.account_inactive'));
            }

            Auth::login($usuario, true);
            request()->session()->regenerate();

            return redirect()->intended($usuario->homeUrl());
        }

        // Si no existe, crear nuevo usuario con rol turista
        $fullName = trim($googleUser->getName() ?? '');
        $parts = $fullName ? explode(' ', $fullName, 2) : ['Turista', 'Google'];
        $nombre = $parts[0] ?: 'Turista';
        $apellido = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : 'Google';

        $nuevoUsuario = Usuario::create([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'password' => Hash::make(Str::random(32)),
            'rol' => 'turista',
            'estado' => 'activo',
            'email_verified_at' => Carbon::now(),
        ]);

        Auth::login($nuevoUsuario, true);
        request()->session()->regenerate();

        return redirect()->intended($nuevoUsuario->homeUrl());
    }

    // Mostrar formulario de Login
    public function showLogin()
    {
        return view('usuarios.auth.login');
    }

    // Procesar Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 'estado' => 'activo' bloquea a los usuarios inactivos
        if (Auth::attempt([...$credentials, 'estado' => 'activo'], $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var Usuario $usuario */
            $usuario = Auth::user();

            return redirect()->intended($usuario->homeUrl());
        }

        return back()->withErrors([
            'email' => __('auth.invalid_credentials'),
        ])->onlyInput('email');
    }

    // Mostrar formulario de Registro
    public function showRegister()
    {
        return view('usuarios.auth.register');
    }

    // Procesar Registro
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:usuarios'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'turista', // Por defecto los que se registran por la web son turistas
            'estado' => 'activo',
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->intended($usuario->homeUrl());
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // ─── RECUPERACIÓN DE CONTRASEÑA ──────────────────────────────────────────

    // 1. Muestra el formulario para pedir correo
    public function showForgotForm()
    {
        return view('usuarios.auth.forgot-password');
    }

    // 2. Genera y envía el código de 6 dígitos
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:usuarios,email'],
        ], [
            'email.required' => __('auth.email_required'),
            'email.email' => __('auth.email_invalid'),
            'email.exists' => __('auth.email_not_found'),
        ]);

        // Generar código aleatorio de 6 dígitos
        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $code, 'created_at' => Carbon::now()]
        );

        // Enviar correo
        try {
            Mail::raw(__('auth.reset_email_body', ['code' => $code]), function ($message) use ($request) {
                $message->to($request->email)
                    ->subject(__('auth.reset_email_subject'));
            });
        } catch (\Throwable $e) {
            Log::error('Error al enviar correo de recuperación: '.$e->getMessage());

            return back()->withInput()->with('error', __('auth.reset_email_failed'));
        }

        return redirect()->route('password.verify')
            ->with('reset_email', $request->email)
            ->with('exito', __('auth.reset_code_sent'));
    }

    // 3. Muestra vista para poner el código y la nueva contraseña
    public function showVerifyCodeForm()
    {
        return view('usuarios.auth.reset-password');
    }

    // 4. Verifica el código y actualiza la contraseña
    public function verifyCodeAndReset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:usuarios,email'],
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => __('auth.email_required'),
            'email.exists' => __('auth.email_not_found'),
            'code.required' => __('auth.code_required'),
            'code.digits' => __('auth.code_six_digits'),
            'password.required' => __('auth.password_required'),
            'password.min' => __('auth.password_minimum'),
            'password.confirmed' => __('auth.password_confirmation_mismatch'),
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (! $resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
            return back()->withInput()->with('error', __('auth.code_invalid_or_expired'));
        }

        // Actualizar contraseña del usuario
        $usuario = Usuario::where('email', $request->email)->first();
        if ($usuario) {
            $usuario->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();
        }

        // Borrar token usado
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('exito', __('auth.password_reset_success'));
    }
}
