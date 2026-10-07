<?php

namespace App\Modules\Rutas\Models;

use App\Models\Destino;
use App\Models\User;
use App\Modules\Rutas\Database\Factories\RutaFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(RutaFactory::class)]
class Ruta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rutas';

    protected $fillable = [
        'destino_id',
        'operador_id',
        'nombre',
        'descripcion',
        'tipo_ruta',
        'duracion_estimada_horas',
        'distancia_km',
        'nivel_dificultad',
        'transporte_recomendado',
        'costo_estimado',
        'costo_min',
        'costo_max',
        'tipo_precio',
        'temporada_recomendada',
        'recomendaciones',
        'imagen_portada',
        'sitio_web_oficial',
        'url_reserva_oficial',
        'fuente_url',
        'fecha_verificacion',
        'estado_verificacion',
        'estado',
        'imagen_fuente_url',
        'imagen_autor',
        'imagen_licencia',
        'precio_fuente_url',
        'precio_verificado_en',
        'es_propuesta',
    ];

    protected function casts(): array
    {
        return [
            'precio_verificado_en' => 'datetime',
            'es_propuesta' => 'boolean',
            'duracion_estimada_horas' => 'decimal:1',
            'distancia_km' => 'decimal:2',
            'costo_estimado' => 'decimal:2',
            'costo_min' => 'decimal:2',
            'costo_max' => 'decimal:2',
            'fecha_verificacion' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function puntos(): HasMany
    {
        return $this->hasMany(RutaPunto::class, 'ruta_id')->orderBy('orden', 'asc');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeDeDestino($query, int $destinoId)
    {
        return $query->where('destino_id', $destinoId);
    }

    public function scopeDificultad($query, string $dificultad)
    {
        return $query->where('nivel_dificultad', $dificultad);
    }

    public function scopeTransporte($query, string $transporte)
    {
        return $query->where('transporte_recomendado', $transporte);
    }

    public function scopeTipoRuta($query, string $tipo)
    {
        return $query->where('tipo_ruta', $tipo);
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado_verificacion', 'aprobado')->whereNotNull('fuente_url')
            ->has('puntos', '>=', 2)
            ->whereBetween('fecha_verificacion', [now()->subDays(config('tourism.verification_days')), now()])
            ->whereDoesntHave('puntos', fn ($q) => $q->whereNotNull('atractivo_id')
                ->whereDoesntHave('atractivo', fn ($a) => $a->activos()->aprobados()));
    }

    public function getCostoFormateadoAttribute(): string
    {
        if (! $this->tieneCostoVerificado()) {
            return 'Consultar costos';
        }
        return 'Referencia: S/ '.number_format((float) $this->costo_estimado, 2);
    }

    public function tieneCostoVerificado(): bool
    {
        return $this->tipo_precio !== 'variable' && $this->costo_estimado !== null
            && filled($this->precio_fuente_url) && $this->precio_verificado_en !== null
            && $this->precio_verificado_en->between(now()->subDays(config('tourism.price_days')), now());
    }

    // ─── Accessors & Helpers ─────────────────────────────────────────────────

    public function getCantidadParadasAttribute(): int
    {
        return $this->relationLoaded('puntos') ? $this->puntos->count() : $this->puntos()->count();
    }

    public function getTiempoTotalMinutosAttribute(): ?int
    {
        if ($this->puntos->isEmpty() || $this->puntos->contains(fn ($p) => $p->tiempo_estadia_min === null || ($p->orden > 1 && $p->tiempo_traslado_min === null))) {
            return null;
        }
        $tiempoEstadias = (int) $this->puntos->sum('tiempo_estadia_min');
        $tiempoTraslados = (int) $this->puntos->sum('tiempo_traslado_min');

        return $tiempoEstadias + $tiempoTraslados;
    }

    public function getTransporteTextoAttribute(): string
    {
        return match ($this->transporte_recomendado) {
            'a_pie' => 'A pie / Senderismo',
            'bicicleta' => 'En Bicicleta',
            'automovil' => 'En Automóvil / Camioneta',
            'autobus' => 'En Bus Turístico',
            'mixto' => 'Transporte Mixto',
            default => $this->transporte_recomendado ? ucfirst(str_replace('_', ' ', $this->transporte_recomendado)) : 'Por confirmar',
        };
    }

    public function getBadgeDificultadColorAttribute(): string
    {
        return match ($this->nivel_dificultad) {
            'facil' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'moderada' => 'bg-blue-100 text-blue-800 border-blue-200',
            'dificil' => 'bg-amber-100 text-amber-800 border-amber-200',
            'experto' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
