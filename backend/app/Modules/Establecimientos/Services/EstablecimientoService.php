<?php

namespace App\Modules\Establecimientos\Services;

use App\Modules\Atractivos\Models\Imagen;
use App\Modules\Establecimientos\Models\Establecimiento;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EstablecimientoService
{
    /**
     * Lista establecimientos con filtros opcionales y paginación.
     * Usado tanto por el catálogo público como por el panel del proveedor.
     *
     * @param  array{
     *   destino_id?: int,
     *   tipo?: string,
     *   rango_precio?: string,
     *   categoria_id?: int,
     *   busqueda?: string,
     *   proveedor_id?: int,
     *   solo_activos?: bool,
     * } $filtros
     */
    public function listar(array $filtros = [], int $porPagina = 15): LengthAwarePaginator
    {
        $query = Establecimiento::with(['destino', 'categorias'])
            ->withCount('imagenes');

        // Filtro por destino.
        if (!empty($filtros['destino_id'])) {
            $query->where('destino_id', $filtros['destino_id']);
        }

        // Filtro por proveedor: el panel del proveedor siempre pasa este filtro.
        // Garantiza que un proveedor solo vea sus propios establecimientos.
        if (!empty($filtros['proveedor_id'])) {
            $query->where('proveedor_id', $filtros['proveedor_id']);
        }

        // Filtro por tipo de establecimiento.
        if (!empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }

        // Filtro por rango de precio.
        if (!empty($filtros['rango_precio'])) {
            $query->where('rango_precio', $filtros['rango_precio']);
        }

        // Filtro por categoría de interés.
        if (!empty($filtros['categoria_id'])) {
            $query->whereHas('categorias', function ($q) use ($filtros) {
                $q->where('categorias_interes.id', $filtros['categoria_id']);
            });
        }

        // Búsqueda por nombre, descripción o dirección.
        if (!empty($filtros['busqueda'])) {
            $termino = '%' . $filtros['busqueda'] . '%';
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'ilike', $termino)
                  ->orWhere('descripcion', 'ilike', $termino)
                  ->orWhere('direccion', 'ilike', $termino);
            });
        }

        // Solo activos (catálogo público siempre filtra así).
        if (!empty($filtros['solo_activos'])) {
            $query->where('estado', 'activo');
        }

        return $query->orderBy('nombre')->paginate($porPagina)->withQueryString();
    }

    /**
     * Crea un nuevo establecimiento dentro de una transacción.
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
    ): Establecimiento {
        return DB::transaction(function () use ($datos, $categorias, $portada, $galeria) {
            $horarios = $datos['horarios'] ?? null;
            unset($datos['horarios']);

            if ($portada) {
                $datos['imagen_portada'] = $portada->store('establecimientos/portadas', 'public');
            }

            $establecimiento = Establecimiento::create($datos);

            $this->sincronizarCategorias($establecimiento, $categorias);
            $this->guardarHorarios($establecimiento, $horarios);
            $this->guardarImagenes($establecimiento, $galeria);

            return $establecimiento->fresh(['destino', 'categorias', 'imagenes']);
        });
    }

    /**
     * Actualiza un establecimiento existente dentro de una transacción.
     */
    public function actualizar(
        Establecimiento $establecimiento,
        array $datos,
        array $categorias = [],
        ?UploadedFile $portada = null,
        array $galeria = [],
        array $eliminarImagenes = []
    ): Establecimiento {
        return DB::transaction(function () use ($establecimiento, $datos, $categorias, $portada, $galeria, $eliminarImagenes) {
            $horarios = $datos['horarios'] ?? null;
            unset($datos['horarios']);

            if ($portada) {
                if ($establecimiento->imagen_portada) {
                    Storage::disk('public')->delete($establecimiento->imagen_portada);
                }
                $datos['imagen_portada'] = $portada->store('establecimientos/portadas', 'public');
            }

            $establecimiento->update($datos);

            $this->sincronizarCategorias($establecimiento, $categorias);
            if ($horarios !== null) {
                $this->guardarHorarios($establecimiento, $horarios);
            }

            // Eliminar imágenes seleccionadas
            foreach ($eliminarImagenes as $imagenId) {
                if (!empty($imagenId)) {
                    $this->eliminarImagen($establecimiento, (int) $imagenId);
                }
            }

            $this->guardarImagenes($establecimiento, $galeria);

            return $establecimiento->fresh(['destino', 'categorias', 'imagenes']);
        });
    }

    /**
     * Sincroniza los horarios semanales del establecimiento en la tabla horarios.
     */
    protected function guardarHorarios(Establecimiento $establecimiento, ?array $horarios): void
    {
        if ($horarios === null) {
            return;
        }

        DB::table('horarios')->where('establecimiento_id', $establecimiento->id)->delete();

        foreach ($horarios as $dia => $horas) {
            if (is_array($horas) && !empty($horas['abre']) && !empty($horas['cierra'])) {
                DB::table('horarios')->insert([
                    'atractivo_id'       => null,
                    'establecimiento_id' => $establecimiento->id,
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
    public function toggleEstado(Establecimiento $establecimiento): Establecimiento
    {
        $establecimiento->estado = ($establecimiento->estado === 'activo') ? 'inactivo' : 'activo';
        $establecimiento->save();

        return $establecimiento;
    }

    /**
     * Realiza un soft delete del establecimiento.
     */
    public function eliminar(Establecimiento $establecimiento): void
    {
        $establecimiento->delete();
    }

    /**
     * Sincroniza las categorías del establecimiento (reemplaza el conjunto actual).
     *
     * @param int[] $categoriaIds
     */
    public function sincronizarCategorias(Establecimiento $establecimiento, array $categoriaIds): void
    {
        $establecimiento->categorias()->sync($categoriaIds);
    }

    /**
     * Guarda archivos de galería adicional en storage y registra en la tabla imagenes.
     *
     * @param UploadedFile[] $archivos
     */
    public function guardarImagenes(Establecimiento $establecimiento, array $archivos): void
    {
        foreach ($archivos as $orden => $archivo) {
            if (!($archivo instanceof UploadedFile) || !$archivo->isValid()) {
                continue;
            }

            $url = $archivo->store('establecimientos/galeria', 'public');

            Imagen::create([
                'entidad_tipo' => 'establecimiento',
                'entidad_id'   => $establecimiento->id,
                'url'          => $url,
                'orden'        => (int) $orden,
                'alt_text'     => $establecimiento->nombre,
            ]);
        }
    }

    /**
     * Elimina una imagen de galería por su ID, verificando que pertenezca al establecimiento.
     */
    public function eliminarImagen(Establecimiento $establecimiento, int $imagenId): void
    {
        $imagen = Imagen::where('id', $imagenId)
            ->where('entidad_tipo', 'establecimiento')
            ->where('entidad_id', $establecimiento->id)
            ->first();

        if ($imagen) {
            Storage::disk('public')->delete($imagen->url);
            $imagen->delete();
        }
    }
}
