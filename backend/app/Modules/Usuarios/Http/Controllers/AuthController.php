<?php

namespace App\Modules\Usuarios\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
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
            'estado' => 'activo'
        ]);

        Auth::login($usuario);

        return redirect($usuario->homeUrl());
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
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Ingresa un correo electrónico válido.',
            'email.exists'   => 'No encontramos ninguna cuenta registrada con este correo.',
        ]);
        
        // Generar código aleatorio de 6 dígitos
        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $code, 'created_at' => Carbon::now()]
        );

        // Enviar correo
        try {
            Mail::raw("Tu código de verificación para recuperar tu contraseña en TourismCloud AI es: {$code}\n\nEste código expira en 15 minutos.\nSi no solicitaste este cambio, puedes ignorar este mensaje.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Código de Recuperación - TourismCloud AI');
            });
        } catch (\Throwable $e) {
            Log::error('Error al enviar correo de recuperación: ' . $e->getMessage());
            return back()->withInput()->with('error', 'No se pudo enviar el correo de verificación. Por favor revisa tu conexión o intenta más tarde.');
        }

        return redirect()->route('password.verify')
            ->with('reset_email', $request->email)
            ->with('exito', 'Te hemos enviado un código de seguridad de 6 dígitos a tu correo.');
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
            'email'    => ['required', 'email', 'exists:usuarios,email'],
            'code'     => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.exists'       => 'No existe una cuenta registrada con este correo.',
            'code.required'      => 'El código de verificación es obligatorio.',
            'code.digits'        => 'El código debe tener exactamente 6 dígitos.',
            'password.required'  => 'La nueva contraseña es obligatoria.',
            'password.min'       => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
            return back()->withInput()->with('error', 'El código es inválido o ha expirado. Por favor solicita uno nuevo.');
        }

        // Actualizar contraseña del usuario
        $usuario = Usuario::where('email', $request->email)->first();
        if ($usuario) {
            $usuario->forceFill([
                'password'       => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();
        }

        // Borrar token usado
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('exito', 'Tu contraseña ha sido restablecida exitosamente. Ya puedes iniciar sesión.');
    }
}