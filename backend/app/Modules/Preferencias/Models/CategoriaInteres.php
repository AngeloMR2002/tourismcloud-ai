<?php
namespace App\Modules\Preferencias\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoriaInteres extends Model {
    protected $table = 'categorias_interes';
    protected $fillable = ['nombre', 'descripcion'];

    public function preferenciasTurista(): BelongsToMany {
        return $this->belongsToMany(PreferenciaTurista::class, 'preferencia_categoria', 'categoria_id', 'preferencia_id');
    }
}