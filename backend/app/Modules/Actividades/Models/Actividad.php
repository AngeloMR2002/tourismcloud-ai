<?php

namespace App\Modules\Actividades\Models;

use App\Models\Atractivo;
use App\Models\Destino;
use App\Models\User;
use App\Modules\Actividades\Database\Factories\ActividadFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(ActividadFactory::class)]
class Actividad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actividades';

    protected $fillable = [
        'destino_id',
        'atractivo_id',
        'operador_id',
        'nombre',
        'descripcion',
        'categoria',
        'duracion_min',
        'precio',
        'precio_min',
        'precio_max',
        'tipo_precio',
        'nivel_dificultad',
        'cupo_maximo',
        'horario_inicio',
        'horario_fin',
        'dias_operacion',
        'punto_encuentro',
        'incluye',
        'no_incluye',
        'requisitos',
        'imagen_portada',
        'sitio_web_oficial',
        'url_reserva_oficial',
        'fuente_url',
        'fecha_verificacion',
        'estado_verificacion',
        'latitud',
        'longitud',
        'estado',
        'imagen_fuente_url',
        'imagen_autor',
        'imagen_licencia',
        'precio_fuente_url',
        'precio_verificado_en',
        'tipo_registro',
        'evento_inicio',
        'evento_fin',
    ];

    protected function casts(): array
    {
        return [
            'precio_verificado_en' => 'datetime',
            'evento_inicio' => 'datetime',
            'evento_fin' => 'datetime',
            'precio' => 'decimal:2',
            'precio_min' => 'decimal:2',
            'precio_max' => 'decimal:2',
            'fecha_verificacion' => 'datetime',
            'duracion_min' => 'integer',
            'cupo_maximo' => 'integer',
            'dias_operacion' => 'array',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
            'deleted_at' => 'datetime',
        ];
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }

    public function atractivo(): BelongsTo
    {
        return $this->belongsTo(Atractivo::class, 'atractivo_id');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeDeDestino($query, int $destinoId)
    {
        return $query->where('destino_id', $destinoId);
    }

    public function scopeDeAtractivo($query, int $atractivoId)
    {
        return $query->where('atractivo_id', $atractivoId);
    }

    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeDificultad($query, string $dificultad)
    {
        return $query->where('nivel_dificultad', $dificultad);
    }

    public function scopePrecioMaximo($query, float $precioMax)
    {
        return $query->where('tipo_precio', 'fijo')->whereNotNull('precio_fuente_url')
            ->where('precio_verificado_en', '>=', now()->subDays(config('tourism.price_days')))
            ->where('precio_verificado_en', '<=', now())
            ->where('precio', '<=', $precioMax);
    }

    public function scopeDuracionMaxima($query, int $minutosMax)
    {
        return $query->where('duracion_min', '<=', $minutosMax);
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado_verificacion', 'aprobado')->whereNotNull('fuente_url')
            ->where('fecha_verificacion', '>=', now()->subDays(config('tourism.verification_days')))
            ->where('fecha_verificacion', '<=', now())
            ->where(fn ($q) => $q->whereNull('atractivo_id')->orWhereHas('atractivo', fn ($a) => $a->activos()->aprobados()))
            ->where(fn ($q) => $q->where('tipo_registro', '!=', 'evento')
                ->orWhere(fn ($e) => $e->whereNotNull('evento_inicio')->where('evento_fin', '>=', now())
                    ->whereColumn('evento_inicio', '<=', 'evento_fin')));
    }

    // ─── Accessors & Helpers ─────────────────────────────────────────────────

    public function getDuracionFormateadaAttribute(): string
    {
        if ($this->duracion_min === null) {
            return 'Por confirmar';
        }
        $minutos = (int) $this->duracion_min;
        if ($minutos < 60) {
            return "{$minutos} min";
        }
        $horas = floor($minutos / 60);
        $restoMin = $minutos % 60;

        return $restoMin > 0 ? "{$horas}h {$restoMin}m" : "{$horas}h";
    }

    public function getPrecioFormateadoAttribute(): string
    {
        if (! $this->tienePrecioVerificado()) {
            return 'Consultar precio';
        }
        if ($this->tipo_precio === 'variable') {
            return 'Precio variable: consultar';
        }
        if ($this->tipo_precio === 'fijo' && (float) $this->precio === 0.0) {
            return 'Acceso libre';
        }
        return ($this->tipo_precio === 'estimado' ? 'Referencia: ' : '').'S/ '.number_format((float) $this->precio, 2);
    }

    public function tienePrecioVerificado(): bool
    {
        return $this->tipo_precio !== 'variable' && $this->precio !== null
            && filled($this->precio_fuente_url) && $this->precio_verificado_en !== null
            && $this->precio_verificado_en->between(now()->subDays(config('tourism.price_days')), now());
    }

    public function getBadgeDificultadColorAttribute(): string
    {
        return match ($this->nivel_dificultad) {
            'baja' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'media' => 'bg-amber-100 text-amber-800 border-amber-200',
            'alta' => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
