<?php

namespace App\Modules\Usuarios\Models;

// IMPORTANTE: Debe extender de Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Destinos\Models\Destino; // Relación

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Especificar la tabla (porque Laravel por defecto buscaría 'users')
    protected $table = 'usuarios';

    // 2. Campos asignables masivamente
    protected $fillable = [
        'organizacion_id',
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
        'estado',
    ];

    // 3. Campos ocultos (no se envían en JSON/API)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 4. Casteo de atributos
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Laravel 11 hashea automáticamente
        ];
    }

    // --- RELACIONES (basadas en tu esquema SQL) ---

    // Un usuario pertenece a una organización
    public function organizacion()
    {
        // NOTA: Cuando crees el modelo Organizacion en su módulo, actualiza la ruta aquí
        // return $this->belongsTo(Organizacion::class, 'organizacion_id');
    }

    // Un usuario (turista) tiene una preferencia registrada
    public function preferencia(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Modules\Preferencias\Models\PreferenciaTurista::class, 'turista_id');
    }

    public function preferencias(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->preferencia();
    }

    // Un usuario (operador_turistico) gestiona muchos destinos
    public function destinosGestionados()
    {
        return $this->hasMany(Destino::class, 'operador_id');
    }

    // --- MÉTODOS DE ROL (Helpers útiles) ---
    public function isAdministrador(): bool { return $this->rol === 'administrador'; }
    public function isOperador(): bool { return $this->rol === 'operador_turistico'; }
    public function isProveedor(): bool { return $this->rol === 'proveedor'; }
    public function isTurista(): bool { return $this->rol === 'turista'; }

    public function homeUrl(): string
    {
        return match ($this->rol) {
            'administrador'      => route('admin.usuarios.index'),
            'operador_turistico' => route('operador.atractivos.index'),
            'proveedor'          => route('proveedor.establecimientos.index'),
            default              => route('catalogo.atractivos.index'),
        };
    }

    public function rolEtiqueta(): string
    {
        return match ($this->rol) {
            'administrador'      => 'Administrador',
            'operador_turistico' => 'Operador turístico',
            'proveedor'          => 'Proveedor',
            default              => 'Turista',
        };
    }
}