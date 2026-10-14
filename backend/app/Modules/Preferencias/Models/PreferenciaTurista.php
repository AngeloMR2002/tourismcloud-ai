<?php
namespace App\Modules\Preferencias\Models;
use App\Modules\Usuarios\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PreferenciaTurista extends Model {
    protected $table = 'preferencias_turista';
    
    // Ajustado a las columnas de tu migración:
    protected $fillable = [
        'turista_id', 'presupuesto_max', 'dias_disponibles', 
        'hora_inicio_preferida', 'hora_fin_preferida'
    ];

    public function turista(): BelongsTo {
        return $this->belongsTo(Usuario::class, 'turista_id');
    }

    public function categorias(): BelongsToMany {
        return $this->belongsToMany(CategoriaInteres::class, 'preferencia_categoria', 'preferencia_id', 'categoria_id');
    }
}