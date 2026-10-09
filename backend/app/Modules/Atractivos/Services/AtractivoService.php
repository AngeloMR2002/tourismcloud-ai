<?php

namespace App\Modules\Atractivos\Services;

use App\Modules\Atractivos\Models\Atractivo;
use App\Modules\Atractivos\Models\Imagen;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AtractivoService
{
    /**
     * Lista atractivos con filtros opcionales y paginación.
     * Usado tanto por el catálogo público como por el panel del operador.
     *
     * @param  array{
     *   destino_id?: int,
     *   categoria_id?: int,
     *   costo_min?: float,
     *   costo_max?: float,
     *   busqueda?: string,
     *   operador_id?: int,
     *   solo_activos?: bool,
     * } $filtros
     */
    public function listar(array $filtros = [], int $porPagina = 15): LengthAwarePaginator
    {
        $query = Atractivo::with(['destino', 'categorias'])
            ->withCount('imagenes');

        // Filtro por destino.
        if (!empty($filtros['destino_id'])) {
            $query->where('destino_id', $filtros['destino_id']);
        }

        // Filtro por operador: solo atractivos de destinos que le pertenecen.
        if (!empty($filtros['operador_id'])) {
            $query->whereHas('destino', function ($q) use ($filtros) {
                $q->where('operador_id', $filtros['operador_id']);
            });
        }

        // Filtro por categoría de interés.
        if (!empty($filtros['categoria_id'])) {
            $query->whereHas('categorias', function ($q) use ($filtros) {
                $q->where('categorias_interes.id', $filtros['categoria_id']);
            });
        }

        // Filtro de costo mínimo.
        if (isset($filtros['costo_min']) && $filtros['costo_min'] !== '') {
            $query->where('costo_entrada', '>=', $filtros['costo_min']);
        }

        // Filtro de costo máximo.
        if (isset($filtros['costo_max']) && $filtros['costo_max'] !== '') {
            $query->where('costo_entrada', '<=', $filtros['costo_max']);
        }

        // Búsqueda por nombre o descripción.
        if (!empty($filtros['busqueda'])) {
            $termino = '%' . $filtros['busqueda'] . '%';
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'ilike', $termino)
                  ->orWhere('descripcion', 'ilike', $termino);
            });
        }

        // Solo activos (catálogo público siempre filtra así).
        if (!empty($filtros['solo_activos'])) {
            $query->where('estado', 'activo');
        }

        return $query->orderBy('nombre')->paginate($porPagina)->withQueryString();
    }

    /**
     * Crea un nuevo atractivo dentro de una transacción.
     * Sincroniza categorías y guarda imágenes si se suministran.
     *
     * @param array $datos      Datos validados del FormRequest.
     * @param array $categorias IDs de categorías seleccionadas.
     * @param UploadedFile|null $portada Archivo de imagen de portada.
     * @param UploadedFile[] $galeria Archivos de galería adicional.
     */
    public function crear(
        array $datos,
        array $categorias = [],
        ?UploadedFile $portada = null,
        array $galeria = []
    ): Atractivo {
        return DB::transaction(function () use ($datos, $categorias, $portada, $galeria) {
            $horarios = $datos['horarios'] ?? null;
            unset($datos['horarios']);

            // Subir imagen de portada si se suministró.
            if ($portada) {
                $datos['imagen_portada'] = $portada->store('atractivos/portadas', 'public');
            }

            $atractivo = Atractivo::create($datos);

            $this->sincronizarCategorias($atractivo, $categorias);
            $this->guardarHorarios($atractivo, $horarios);
            $this->guardarImagenes($atractivo, $galeria);

            return $atractivo->fresh(['destino', 'categorias', 'imagenes']);
        });
    }

    /**
     * Actualiza un atractivo existente dentro de una transacción.
     */
    public function actualizar(
        Atractivo $atractivo,
        array $datos,
        array $categorias = [],
        ?UploadedFile $portada = null,
        array $galeria = [],
        array $eliminarImagenes = []
    ): Atractivo {
        return DB::transaction(function () use ($atractivo, $datos, $categorias, $portada, $galeria, $eliminarImagenes) {
            $horarios = $datos['horarios'] ?? null;
            unset($datos['horarios']);

            // Reemplazar imagen de portada si se subió una nueva.
            if ($portada) {
                // Eliminar la portada anterior del storage si existía.
                if ($atractivo->imagen_portada) {
                    Storage::disk('public')->delete($atractivo->imagen_portada);
                }
                $datos['imagen_portada'] = $portada->store('atractivos/portadas', 'public');
            }

            $atractivo->update($datos);

            $this->sincronizarCategorias($atractivo, $categorias);
            if ($horarios !== null) {
                $this->guardarHorarios($atractivo, $horarios);
            }

            // Eliminar imágenes seleccionadas
            foreach ($eliminarImagenes as $imagenId) {
                if (!empty($imagenId)) {
                    $this->eliminarImagen($atractivo, (int) $imagenId);
                }
            }

            $this->guardarImagenes($atractivo, $galeria);

            return $atractivo->fresh(['destino', 'categorias', 'imagenes']);
        });
    }

    /**
     * Sincroniza los horarios semanales del atractivo en la tabla horarios.
     */
    protected function guardarHorarios(Atractivo $atractivo, ?array $horarios): void
    {
        if ($horarios === null) {
            return;
        }

        DB::table('horarios')->where('atractivo_id', $atractivo->id)->delete();

        foreach ($horarios as $dia => $horas) {
            if (is_array($horas) && !empty($horas['abre']) && !empty($horas['cierra'])) {
                DB::table('horarios')->insert([
                    'atractivo_id'       => $atractivo->id,
                    'establecimiento_id' => null,
                    'dia'                => $dia,
                    'hora_inicio'        => $horas['abre'],
                    'hora_fin'           => $horas['cierra'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
        }
    }

    /**
     * Alterna el estado entre 'activo' e 'inactivo'.
     * NO realiza soft delete — solo cambia el campo 'estado'.
     */
    public function toggleEstado(Atractivo $atractivo): Atractivo
    {
        $atractivo->estado = ($atractivo->estado === 'activo') ? 'inactivo' : 'activo';
        $atractivo->save();

        return $atractivo;
    }

    /**
     * Realiza un soft delete del atractivo.
     * Separado del toggle de estado para no confundir ambas operaciones.
     */
    public function eliminar(Atractivo $atractivo): void
    {
        $atractivo->delete();
    }

    /**
     * Sincroniza las categorías del atractivo (reemplaza el conjunto actual).
     *
     * @param int[] $categoriaIds
     */
    public function sincronizarCategorias(Atractivo $atractivo, array $categoriaIds): void
    {
        $atractivo->categorias()->sync($categoriaIds);
    }

    /**
     * Guarda archivos de galería adicional en storage y registra en la tabla imagenes.
     *
     * @param UploadedFile[] $archivos
     */
    public function guardarImagenes(Atractivo $atractivo, array $archivos): void
    {
        foreach ($archivos as $orden => $archivo) {
            if (!($archivo instanceof UploadedFile) || !$archivo->isValid()) {
                continue;
            }

            $url = $archivo->store('atractivos/galeria', 'public');

            Imagen::create([
                'entidad_tipo' => 'atractivo',
                'entidad_id'   => $atractivo->id,
                'url'          => $url,
                'orden'        => (int) $orden,
                'alt_text'     => $atractivo->nombre,
            ]);
        }
    }

    /**
     * Elimina una imagen de galería por su ID, verificando que pertenezca al atractivo.
     */
    public function eliminarImagen(Atractivo $atractivo, int $imagenId): void
    {
        $imagen = Imagen::where('id', $imagenId)
            ->where('entidad_tipo', 'atractivo')
            ->where('entidad_id', $atractivo->id)
            ->first();

        if ($imagen) {
            Storage::disk('public')->delete($imagen->url);
            $imagen->delete();
        }
    }
}
