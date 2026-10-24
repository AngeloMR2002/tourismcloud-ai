<?php

namespace App\Modules\Itinerarios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItinerarioItem extends Model
{
    protected $table = 'itinerario_items';

    public $timestamps = false;

    protected $fillable = ['itinerario_dia_id', 'entidad_tipo', 'entidad_id', 'hora_inicio', 'hora_fin', 'orden', 'costo_estimado'];

    protected function casts(): array
    {
        return ['costo_estimado' => 'decimal:2', 'orden' => 'integer'];
    }

    public function dia(): BelongsTo
    {
        return $this->belongsTo(ItinerarioDia::class, 'itinerario_dia_id');
    }
}
