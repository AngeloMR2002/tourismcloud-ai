<?php

namespace App\Modules\Valoraciones\Models;

use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Valoracion extends Model
{
    use HasFactory;

    protected $table = 'valoraciones';

    protected $fillable = [
        'usuario_id',
        'valorable_id',
        'valorable_type',
        'puntuacion',
        'comentario',
        'visita_verificada',
    ];

    protected function casts(): array
    {
        return [
            'puntuacion'        => 'integer',
            'visita_verificada' => 'boolean',
            'created_at'        => 'datetime',
            'updated_at'        => 'datetime',
        ];
    }

    /**
     * Usuario autor de la valoración.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Entidad polimórfica calificada (Atractivo, Establecimiento, etc.).
     */
    public function valorable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope para ordenar por las reseñas más recientes.
     */
    public function scopeRecientes($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Scope para filtrar únicamente visitas verificadas.
     */
    public function scopeVerificadas($query)
    {
        return $query->where('visita_verificada', true);
    }
}
