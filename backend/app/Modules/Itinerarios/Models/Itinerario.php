<?php

namespace App\Modules\Itinerarios\Models;

use App\Modules\Destinos\Models\Destino;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Itinerario extends Model
{
    protected $table = 'itinerarios';

    protected $fillable = ['turista_id', 'destino_id', 'preferencia_id', 'nombre', 'descripcion', 'presupuesto_total', 'dias', 'estado'];

    protected function casts(): array
    {
        return ['presupuesto_total' => 'decimal:2', 'dias' => 'integer'];
    }

    public function turista(): BelongsTo { return $this->belongsTo(Usuario::class, 'turista_id'); }
    public function destino(): BelongsTo { return $this->belongsTo(Destino::class, 'destino_id'); }
    public function diasItinerario(): HasMany { return $this->hasMany(ItinerarioDia::class, 'itinerario_id')->orderBy('numero_dia'); }

    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(ItinerarioItem::class, ItinerarioDia::class, 'itinerario_id', 'itinerario_dia_id');
    }
}