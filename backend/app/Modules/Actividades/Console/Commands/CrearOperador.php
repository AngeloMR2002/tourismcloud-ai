<?php

namespace App\Modules\Actividades\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CrearOperador extends Command
{
    protected $signature = 'tourism:crear-operador {email} {--nombre=} {--administrador}';
    protected $description = 'Crear una cuenta del panel con contraseña introducida de forma privada';

    public function handle(): int
    {
        $email = $this->argument('email');
        if (User::where('email', $email)->exists()) {
            $this->error('Ese correo ya está registrado. No se modificó su cuenta.');
            return self::FAILURE;
        }
        if (! $this->input->isInteractive()) {
            $this->error('Ejecuta el comando en una terminal interactiva para introducir la contraseña.');
            return self::FAILURE;
        }
        $data = ['email' => $email, 'name' => $this->option('nombre') ?: $this->ask('Nombre'),
            'password' => $this->secret('Contraseña (mínimo 12 caracteres)'),
            'password_confirmation' => $this->secret('Repite la contraseña')];
        $validator = Validator::make($data, ['email' => ['required', 'email'], 'name' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'min:12', 'confirmed']]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }
        $user = new User($validator->validated());
        $user->rol = $this->option('administrador') ? 'administrador' : 'operador';
        $user->save();
        $this->info('Cuenta creada. Puedes iniciar sesión en /login.');
        return self::SUCCESS;
    }
}
