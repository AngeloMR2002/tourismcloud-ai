<?php

namespace App\Modules\Rutas\Models;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Destinos\Models\Destino;
use Database\Factories\RutaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Ruta extends Model
{
    /** @use HasFactory<RutaFactory> */
    use HasFactory;

    protected $table = 'rutas';

    protected $fillable = [
        'destino_id',
        'nombre',
        'descripcion',
        'distancia_km',
        'duracion_estimada_min',
        'estado',
        'imagen_portada',
    ];

    protected function casts(): array
    {
        return [
            'distancia_km' => 'decimal:2',
        ];
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Destino::class, 'destino_id');
    }

    public function paradas(): HasMany
    {
        return $this->hasMany(RutaParada::class, 'ruta_id')->orderBy('orden');
    }

    public function atractivos(): BelongsToMany
    {
        return $this->belongsToMany(Atractivo::class, 'ruta_paradas', 'ruta_id', 'atractivo_id')
            ->withPivot('orden')
            ->orderByPivot('orden');
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('estado', 'activo');
    }

    protected static function newFactory(): RutaFactory
    {
        return RutaFactory::new();
    }

    public function scopeDeDestino(Builder $query, int $destinoId): Builder
    {
        return $query->where('destino_id', $destinoId);
    }

    public function getCostoEntradasReferencialAttribute(): string
    {
        if (! $this->relationLoaded('paradas')) {
            $this->load('paradas.atractivo');
        }

        $centavos = (int) $this->paradas->sum(
            fn (RutaParada $parada): int => $this->centavosDeMonto($parada->atractivo?->costo_entrada)
        );

        return sprintf('%d.%02d', intdiv($centavos, 100), $centavos % 100);
    }

    public function getPortadaUrlAttribute(): ?string
    {
        $portadaPropia = $this->urlDeArchivoPublicoSeguro(
            $this->imagen_portada,
            ['rutas/portadas/', 'actividades-rutas-demo/']
        );

        if ($portadaPropia !== null) {
            return $portadaPropia;
        }

        if (! $this->relationLoaded('paradas')) {
            $this->load('paradas.atractivo');
        }

        foreach ($this->paradas as $parada) {
            $portadaAtractivo = $this->urlDeArchivoPublicoSeguro(
                $parada->atractivo?->imagen_portada,
                ['atractivos/portadas/', 'actividades-rutas-demo/']
            );

            if ($portadaAtractivo !== null) {
                return $portadaAtractivo;
            }
        }

        return null;
    }

    /**
     * @param  array<int, string>  $prefijosPermitidos
     */
    private function urlDeArchivoPublicoSeguro(?string $ruta, array $prefijosPermitidos): ?string
    {
        if (! is_string($ruta)) {
            return null;
        }

        $ruta = ltrim($ruta, '/');

        if (
            $ruta === ''
            || str_contains($ruta, '..')
            || str_contains($ruta, '\\')
            || preg_match('/^[a-z][a-z0-9+.-]*:/i', $ruta) === 1
            || ! Str::startsWith($ruta, $prefijosPermitidos)
        ) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($ruta)) {
            return null;
        }

        return $disk->url($ruta);
    }

    private function centavosDeMonto(mixed $monto): int
    {
        $montoNormalizado = is_string($monto) || is_int($monto) || is_float($monto)
            ? (string) $monto
            : '0';

        if (preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $montoNormalizado, $coincidencias) !== 1) {
            return 0;
        }

        $enteros = (int) $coincidencias[1];
        $fraccion = str_pad($coincidencias[2] ?? '', 2, '0');

        return ($enteros * 100) + (int) $fraccion;
    }
}
