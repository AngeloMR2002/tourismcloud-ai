<?php

namespace App\Modules\Establecimientos\Services;

use App\Modules\Establecimientos\Models\Establecimiento;
use App\Models\Horario;
use App\Models\Imagen;

use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EstablecimientoService
{
    /**
     * Lista establecimientos con filtros opcionales y paginación.
     *
     * Usado tanto por el catálogo público como por el panel del proveedor.
     *
     * @param array{
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

        // Filtro por proveedor.
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

        // Solo activos.
        if (!empty($filtros['solo_activos'])) {
            $query->where('estado', 'activo');
        }

        return $query
            ->orderBy('nombre')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /**
     * Crea un nuevo establecimiento dentro de una transacción.
     *
     * Los horarios se almacenan en la tabla horarios,
     * no directamente en establecimientos.
     *
     * @param array $datos Datos validados del FormRequest.
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
        return DB::transaction(function () use (
            $datos,
            $categorias,
            $portada,
            $galeria
        ) {
            /*
             * Los horarios no son una columna de establecimientos.
             * Se extraen antes de crear el modelo.
             */
            $horarios = $datos['horarios'] ?? [];
            unset($datos['horarios']);

            if ($portada) {
                $datos['imagen_portada'] = $portada->store(
                    'establecimientos/portadas',
                    'public'
                );
            }

            $establecimiento = Establecimiento::create($datos);

            $this->sincronizarCategorias(
                $establecimiento,
                $categorias
            );

            $this->sincronizarHorarios(
                $establecimiento,
                $horarios
            );

            $this->guardarImagenes(
                $establecimiento,
                $galeria
            );

            return $establecimiento->fresh([
                'destino',
                'categorias',
                'horarios',
                'imagenes',
            ]);
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
        array $galeria = []
    ): Establecimiento {
        return DB::transaction(function () use (
            $establecimiento,
            $datos,
            $categorias,
            $portada,
            $galeria
        ) {
            /*
             * Extraemos los horarios porque no pertenecen
             * directamente a la tabla establecimientos.
             */
            $horarios = $datos['horarios'] ?? [];
            unset($datos['horarios']);

            if ($portada) {
                if ($establecimiento->imagen_portada) {
                    Storage::disk('public')->delete(
                        $establecimiento->imagen_portada
                    );
                }

                $datos['imagen_portada'] = $portada->store(
                    'establecimientos/portadas',
                    'public'
                );
            }

            $establecimiento->update($datos);

            $this->sincronizarCategorias(
                $establecimiento,
                $categorias
            );

            $this->sincronizarHorarios(
                $establecimiento,
                $horarios
            );

            $this->guardarImagenes(
                $establecimiento,
                $galeria
            );

            return $establecimiento->fresh([
                'destino',
                'categorias',
                'horarios',
                'imagenes',
            ]);
        });
    }

    /**
     * Alterna el estado entre 'activo' e 'inactivo'.
     *
     * NO realiza soft delete.
     */
    public function toggleEstado(
        Establecimiento $establecimiento
    ): Establecimiento {
        $establecimiento->estado =
            ($establecimiento->estado === 'activo')
                ? 'inactivo'
                : 'activo';

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
     * Sincroniza las categorías del establecimiento.
     */
    public function sincronizarCategorias(
        Establecimiento $establecimiento,
        array $categoriaIds
    ): void {
        $establecimiento->categorias()->sync($categoriaIds);
    }

    /**
     * Sincroniza los horarios del establecimiento.
     *
     * Se eliminan los horarios existentes y se crean
     * nuevamente según los datos recibidos.
     *
     * @param array<int, array{
     *     dia: string,
     *     hora_inicio: string,
     *     hora_fin: string
     * }> $horarios
     */
    public function sincronizarHorarios(
        Establecimiento $establecimiento,
        array $horarios
    ): void {
        $establecimiento->horarios()->delete();

        foreach ($horarios as $horario) {
            Horario::create([
                'establecimiento_id' => $establecimiento->id,
                'atractivo_id' => null,
                'dia' => $horario['dia'],
                'hora_inicio' => $horario['hora_inicio'],
                'hora_fin' => $horario['hora_fin'],
            ]);
        }
    }

    /**
     * Guarda archivos de galería adicional en storage
     * y registra los datos en la tabla imagenes.
     *
     * @param UploadedFile[] $archivos
     */
    public function guardarImagenes(
        Establecimiento $establecimiento,
        array $archivos
    ): void {
        foreach ($archivos as $orden => $archivo) {
            if (
                !($archivo instanceof UploadedFile)
                || !$archivo->isValid()
            ) {
                continue;
            }

            $url = $archivo->store(
                'establecimientos/galeria',
                'public'
            );

            Imagen::create([
                'entidad_tipo' => 'establecimiento',
                'entidad_id' => $establecimiento->id,
                'url' => $url,
                'orden' => (int) $orden,
                'alt_text' => $establecimiento->nombre,
            ]);
        }
    }

    /**
     * Elimina una imagen de galería por su ID,
     * verificando que pertenezca al establecimiento.
     */
    public function eliminarImagen(
        Establecimiento $establecimiento,
        int $imagenId
    ): void {
        $imagen = Imagen::where('id', $imagenId)
            ->where('entidad_tipo', 'establecimiento')
            ->where('entidad_id', $establecimiento->id)
            ->firstOrFail();

        Storage::disk('public')->delete($imagen->url);

        $imagen->delete();
    }
}