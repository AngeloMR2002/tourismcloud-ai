<?php

namespace App\Models;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Establecimientos\Models\Establecimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';

    public $timestamps = false;

    protected $fillable = [
        'establecimiento_id',
        'atractivo_id',
        'dia',
        'hora_inicio',
        'hora_fin',
    ];

    public function atractivo(): BelongsTo
    {
        return $this->belongsTo(Atractivo::class, 'atractivo_id');
    }

    public function establecimiento(): BelongsTo
    {
        return $this->belongsTo(
            Establecimiento::class,
            'establecimiento_id'
        );
    }
}