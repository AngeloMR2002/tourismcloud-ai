<?php

namespace App\Modules\Rutas\Models;

use App\Modules\Atractivos\Models\Atractivo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaParada extends Model
{
    protected $table = 'ruta_paradas';

    public $timestamps = false;

    protected $fillable = [
        'ruta_id',
        'atractivo_id',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'orden' => 'integer',
        ];
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }

    public function atractivo(): BelongsTo
    {
        return $this->belongsTo(Atractivo::class, 'atractivo_id');
    }
}
