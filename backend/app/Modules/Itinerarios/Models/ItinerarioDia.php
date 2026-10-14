<?php

namespace App\Modules\Itinerarios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItinerarioDia extends Model
{
    protected $table = 'itinerario_dias';
    public $timestamps = false;
    protected $fillable = ['itinerario_id', 'numero_dia', 'fecha'];
    protected function casts(): array { return ['fecha' => 'date', 'numero_dia' => 'integer']; }
    public function itinerario(): BelongsTo { return $this->belongsTo(Itinerario::class, 'itinerario_id'); }
}
