<?php

namespace App\Modules\Actividades\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Importa un conjunto pequeño y explícitamente demostrativo para Actividades y Rutas.
 *
 * El importador no crea destinos ni atractivos. Antes de iniciar la transacción valida
 * el CSV, el mapeo de alias y todas las referencias existentes. Esto permite que los
 * datos de demostración se mantengan separados de los datos reales que registra el equipo.
 */
final class ImportadorDemoActividadesRutas
{
    private const int AdvisoryLockId = 20261011;

    private const string DestinoAlias = 'cusco';

    /** @var list<string> */
    private const array AtractivoAliases = [
        'saqsaywaman',
        'qenqo',
        'tambomachay',
        'koricancha',
        'catedral',
    ];

    /**
     * Importa las seis actividades y las dos rutas demostrativas.
     *
     * @param  array{destinos: array<string, mixed>, atractivos: array<string, mixed>}  $mapping
     * @param  array{actividades?: string, rutas?: string, paradas?: string}|null  $paths
     * @return array{
     *     actividades_creadas: int,
     *     actividades_ya_existentes: int,
     *     actividades_eliminadas_omitidas: int,
     *     rutas_creadas: int,
     *     rutas_ya_existentes: int,
     *     paradas_creadas: int,
     *     portadas_omitidas_por_esquema: int,
     *     portadas_omitidas_sin_archivo: int,
     *     imagenes_copiadas: int,
     *     imagenes_ya_existentes: int,
     *     imagenes_fuente_ausente: int,
     *     imagenes_con_error: int
     * }
     */
    public function import(array $mapping, ?array $paths = null): array
    {
        $csvPaths = $this->resolveCsvPaths($paths);
        $dataset = $this->loadDataset($csvPaths);
        $this->validateDataset($dataset);

        $context = $this->normalizeMapping($mapping, $dataset);
        $this->validateReferences($context, false);

        $supportsActivityCover = Schema::hasColumn('actividades', 'imagen_portada');
        $supportsRouteCover = Schema::hasColumn('rutas', 'imagen_portada');
        $availableCoverPaths = $this->availableCoverPaths($dataset['imagenes_portada']);

        $counts = DB::transaction(function () use ($context, $dataset, $supportsActivityCover, $supportsRouteCover, $availableCoverPaths): array {
            $this->acquireImportLock();
            $this->validateReferences($context, true);

            $activityCounts = $this->importActivities(
                $dataset['actividades'],
                $context['destino_id'],
                $supportsActivityCover,
                $availableCoverPaths,
            );

            $routeCounts = $this->importRoutes(
                $dataset['rutas'],
                $dataset['paradas_por_ruta'],
                $context,
                $supportsRouteCover,
                $availableCoverPaths,
            );

            return [
                ...$activityCounts,
                ...$routeCounts,
                'portadas_omitidas_por_esquema' => $this->countCoverPathsOmittedBySchema(
                    $dataset,
                    $supportsActivityCover,
                    $supportsRouteCover,
                ),
                'portadas_omitidas_sin_archivo' => count($dataset['imagenes_portada']) - count($availableCoverPaths),
            ];
        }, 3);

        return [
            ...$counts,
            ...$this->copyDemoImages($dataset['imagenes_portada']),
        ];
    }

    /**
     * Carga un archivo JSON de mapeo sin depender de una variable de configuración compartida.
     *
     * @return array{destinos: array<string, mixed>, atractivos: array<string, mixed>}
     */
    public function loadMappingFromJson(string $path): array
    {
        $resolvedPath = $this->resolveReadablePath($path, 'el archivo de mapeo JSON');
        $json = file_get_contents($resolvedPath);

        if ($json === false) {
            throw new RuntimeException("No se pudo leer el archivo de mapeo: {$resolvedPath}");
        }

        try {
            $mapping = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new RuntimeException("El JSON de mapeo no es válido: {$resolvedPath}", previous: $exception);
        }

        if (! is_array($mapping)) {
            throw new RuntimeException("El JSON de mapeo debe contener un objeto: {$resolvedPath}");
        }

        return $mapping;
    }

    /**
     * @return array{actividades: string, rutas: string, paradas: string}
     */
    public function defaultCsvPaths(): array
    {
        $directory = base_path('app/Modules/Actividades/Resources/demo');

        return [
            'actividades' => $directory.DIRECTORY_SEPARATOR.'actividades.csv',
            'rutas' => $directory.DIRECTORY_SEPARATOR.'rutas.csv',
            'paradas' => $directory.DIRECTORY_SEPARATOR.'ruta-paradas.csv',
        ];
    }

    public function defaultLocalMappingPath(): string
    {
        return dirname(base_path()).DIRECTORY_SEPARATOR.'LOCAL'.DIRECTORY_SEPARATOR.'actividades-rutas'.DIRECTORY_SEPARATOR.'map.example.json';
    }

    /**
     * @param  array{actividades?: string, rutas?: string, paradas?: string}|null  $paths
     * @return array{actividades: string, rutas: string, paradas: string}
     */
    private function resolveCsvPaths(?array $paths): array
    {
        $resolvedPaths = array_replace($this->defaultCsvPaths(), $paths ?? []);

        foreach ($resolvedPaths as $type => $path) {
            if (! is_string($path) || trim($path) === '') {
                throw new RuntimeException("La ruta del CSV de {$type} es obligatoria.");
            }

            $resolvedPaths[$type] = $this->resolveReadablePath($path, "el CSV de {$type}");
        }

        /** @var array{actividades: string, rutas: string, paradas: string} $resolvedPaths */
        return $resolvedPaths;
    }

    private function resolveReadablePath(string $path, string $label): string
    {
        $trimmedPath = trim($path);

        if (is_file($trimmedPath)) {
            return $trimmedPath;
        }

        $backendRelativePath = base_path($trimmedPath);
        if (is_file($backendRelativePath)) {
            return $backendRelativePath;
        }

        throw new RuntimeException("No se encontró {$label}: {$trimmedPath}");
    }

    /**
     * @param  array{actividades: string, rutas: string, paradas: string}  $paths
     * @return array{
     *     actividades: list<array<string, string>>,
     *     rutas: list<array<string, string>>,
     *     paradas: list<array<string, string>>,
     *     paradas_por_ruta: array<string, list<array<string, string>>>,
     *     imagenes_portada: list<string>
     * }
     */
    private function loadDataset(array $paths): array
    {
        $activities = $this->readCsv($paths['actividades'], [
            'alias',
            'destino_alias',
            'nombre',
            'descripcion',
            'costo',
            'duracion_estimada_min',
            'latitud',
            'longitud',
            'estado',
            'imagen_portada',
        ]);

        $routes = $this->readCsv($paths['rutas'], [
            'alias',
            'destino_alias',
            'nombre',
            'descripcion',
            'distancia_km',
            'duracion_estimada_min',
            'estado',
            'imagen_portada',
        ]);

        $stops = $this->readCsv($paths['paradas'], [
            'ruta_alias',
            'atractivo_alias',
            'orden',
        ]);

        $stopsByRoute = [];
        foreach ($stops as $stop) {
            $stopsByRoute[$stop['ruta_alias']][] = $stop;
        }

        $coverPaths = [];
        foreach ([...$activities, ...$routes] as $row) {
            if ($row['imagen_portada'] !== '') {
                $coverPaths[] = $row['imagen_portada'];
            }
        }

        return [
            'actividades' => $activities,
            'rutas' => $routes,
            'paradas' => $stops,
            'paradas_por_ruta' => $stopsByRoute,
            'imagenes_portada' => array_values(array_unique($coverPaths)),
        ];
    }

    /**
     * @param  list<string>  $expectedHeaders
     * @return list<array<string, string>>
     */
    private function readCsv(string $path, array $expectedHeaders): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir el CSV: {$path}");
        }

        try {
            $header = fgetcsv($handle, 0, ',', '"', '\\');
            if ($header === false) {
                throw new RuntimeException("El CSV no tiene cabecera: {$path}");
            }

            $headers = array_map(static function (mixed $header): string {
                return trim((string) $header);
            }, $header);
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]) ?? $headers[0];

            if (
                count($headers) !== count($expectedHeaders)
                || count($headers) !== count(array_unique($headers))
                || array_diff($expectedHeaders, $headers) !== []
                || array_diff($headers, $expectedHeaders) !== []
            ) {
                throw new RuntimeException(sprintf(
                    'La cabecera de %s debe ser exactamente: %s',
                    $path,
                    implode(', ', $expectedHeaders),
                ));
            }

            $rows = [];
            $line = 1;
            while (($values = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                $line++;

                if ($values === [null] || $this->isEmptyCsvRow($values)) {
                    continue;
                }

                if (count($values) !== count($headers)) {
                    throw new RuntimeException("La línea {$line} de {$path} tiene un número de columnas inválido.");
                }

                $row = array_combine($headers, $values);
                if ($row === false) {
                    throw new RuntimeException("No se pudo interpretar la línea {$line} de {$path}.");
                }

                $normalizedRow = array_map(static function (mixed $value): string {
                    return trim((string) $value);
                }, $row);
                $normalizedRow['_linea'] = (string) $line;
                $rows[] = $normalizedRow;
            }

            if ($rows === []) {
                throw new RuntimeException("El CSV no contiene registros: {$path}");
            }

            return $rows;
        } finally {
            fclose($handle);
        }
    }

    /** @param list<string|null> $values */
    private function isEmptyCsvRow(array $values): bool
    {
        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array{
     *     actividades: list<array<string, string>>,
     *     rutas: list<array<string, string>>,
     *     paradas: list<array<string, string>>,
     *     paradas_por_ruta: array<string, list<array<string, string>>>,
     *     imagenes_portada: list<string>
     * } $dataset
     */
    private function validateDataset(array $dataset): void
    {
        $errors = [];

        if (count($dataset['actividades']) !== 6) {
            $errors[] = 'El archivo de actividades debe contener exactamente 6 registros demostrativos.';
        }

        if (count($dataset['rutas']) !== 2) {
            $errors[] = 'El archivo de rutas debe contener exactamente 2 registros demostrativos.';
        }

        $activityAliases = [];
        foreach ($dataset['actividades'] as $activity) {
            $this->validateActivityRow($activity, $activityAliases, $errors);
        }

        $routeAliases = [];
        foreach ($dataset['rutas'] as $route) {
            $this->validateRouteRow($route, $routeAliases, $errors);
        }

        $this->validateStopRows($dataset['paradas'], $routeAliases, $errors);
        $this->throwIfDatasetIsInvalid($errors);
    }

    /**
     * @param  array<string, string>  $activity
     * @param  array<string, true>  $aliases
     * @param  list<string>  $errors
     */
    private function validateActivityRow(array $activity, array &$aliases, array &$errors): void
    {
        $line = $activity['_linea'];
        $this->validateAlias($activity['alias'], "actividad (línea {$line})", $aliases, $errors);
        $this->validateDestinationAlias($activity['destino_alias'], $line, $errors);
        $this->validateDemoName($activity['nombre'], "actividad (línea {$line})", $errors);
        $this->validateRequiredText($activity['descripcion'], "descripcion de actividad (línea {$line})", $errors);
        $this->validateDecimal($activity['costo'], "costo de actividad (línea {$line})", 0, 99999999.99, 2, $errors);
        $this->validatePositiveInteger($activity['duracion_estimada_min'], "duracion_estimada_min de actividad (línea {$line})", $errors);
        $this->validateOptionalDecimal($activity['latitud'], "latitud de actividad (línea {$line})", -90, 90, 7, $errors);
        $this->validateOptionalDecimal($activity['longitud'], "longitud de actividad (línea {$line})", -180, 180, 7, $errors);
        $this->validateState($activity['estado'], "actividad (línea {$line})", $errors);
        $this->validateCoverPath($activity['imagen_portada'], "actividad (línea {$line})", $errors);
    }

    /**
     * @param  array<string, string>  $route
     * @param  array<string, true>  $aliases
     * @param  list<string>  $errors
     */
    private function validateRouteRow(array $route, array &$aliases, array &$errors): void
    {
        $line = $route['_linea'];
        $this->validateAlias($route['alias'], "ruta (línea {$line})", $aliases, $errors);
        $this->validateDestinationAlias($route['destino_alias'], $line, $errors);
        $this->validateDemoName($route['nombre'], "ruta (línea {$line})", $errors);
        $this->validateRequiredText($route['descripcion'], "descripcion de ruta (línea {$line})", $errors);
        $this->validateDecimal($route['distancia_km'], "distancia_km de ruta (línea {$line})", 0, 99999999.99, 2, $errors);
        $this->validatePositiveInteger($route['duracion_estimada_min'], "duracion_estimada_min de ruta (línea {$line})", $errors);
        $this->validateState($route['estado'], "ruta (línea {$line})", $errors);
        $this->validateCoverPath($route['imagen_portada'], "ruta (línea {$line})", $errors);
    }

    /**
     * @param  list<array<string, string>>  $stops
     * @param  array<string, true>  $routeAliases
     * @param  list<string>  $errors
     */
    private function validateStopRows(array $stops, array $routeAliases, array &$errors): void
    {
        /** @var array<string, array<int, true>> $ordersByRoute */
        $ordersByRoute = [];
        /** @var array<string, array<string, true>> $attractionsByRoute */
        $attractionsByRoute = [];

        foreach ($stops as $stop) {
            $line = $stop['_linea'];
            $routeAlias = $stop['ruta_alias'];
            $attractionAlias = $stop['atractivo_alias'];

            if (! isset($routeAliases[$routeAlias])) {
                $errors[] = "La parada de la línea {$line} referencia una ruta inexistente: {$routeAlias}.";
            }

            if (! in_array($attractionAlias, self::AtractivoAliases, true)) {
                $errors[] = "La parada de la línea {$line} usa un alias de atractivo no permitido: {$attractionAlias}.";
            }

            if (! ctype_digit($stop['orden']) || (int) $stop['orden'] < 1) {
                $errors[] = "El orden de la parada en la línea {$line} debe ser un entero positivo.";

                continue;
            }

            $order = (int) $stop['orden'];
            if (isset($ordersByRoute[$routeAlias][$order])) {
                $errors[] = "La ruta {$routeAlias} tiene el orden de parada {$order} repetido.";
            }
            if (isset($attractionsByRoute[$routeAlias][$attractionAlias])) {
                $errors[] = "La ruta {$routeAlias} tiene el atractivo {$attractionAlias} repetido.";
            }

            $ordersByRoute[$routeAlias][$order] = true;
            $attractionsByRoute[$routeAlias][$attractionAlias] = true;
        }

        foreach (array_keys($routeAliases) as $routeAlias) {
            $orders = array_keys($ordersByRoute[$routeAlias] ?? []);
            sort($orders);

            if (count($orders) < 2) {
                $errors[] = "La ruta {$routeAlias} debe tener al menos dos paradas.";

                continue;
            }

            if ($orders !== range(1, count($orders))) {
                $errors[] = "Los órdenes de las paradas de {$routeAlias} deben ser consecutivos desde 1.";
            }
        }
    }

    /**
     * @param  array<string, true>  $aliases
     * @param  list<string>  $errors
     */
    private function validateAlias(string $alias, string $label, array &$aliases, array &$errors): void
    {
        if (! preg_match('/^[a-z0-9_-]+$/', $alias)) {
            $errors[] = "El alias de {$label} debe contener solo minúsculas, números, guiones o guiones bajos.";

            return;
        }

        if (isset($aliases[$alias])) {
            $errors[] = "El alias {$alias} está repetido en {$label}.";

            return;
        }

        $aliases[$alias] = true;
    }

    /** @param list<string> $errors */
    private function validateDestinationAlias(string $destinationAlias, string $line, array &$errors): void
    {
        if ($destinationAlias !== self::DestinoAlias) {
            $errors[] = "La línea {$line} debe usar el destino_alias ".self::DestinoAlias.'.';
        }
    }

    /** @param list<string> $errors */
    private function validateDemoName(string $name, string $label, array &$errors): void
    {
        if ($name === '' || mb_strlen($name) > 150) {
            $errors[] = "El nombre de {$label} es obligatorio y no puede superar 150 caracteres.";
        }

        if (! str_ends_with($name, ' [DEMO]')) {
            $errors[] = "El nombre de {$label} debe finalizar en [DEMO] para evitar confundirlo con datos reales.";
        }
    }

    /** @param list<string> $errors */
    private function validateRequiredText(string $text, string $label, array &$errors): void
    {
        if ($text === '') {
            $errors[] = "La {$label} es obligatoria.";
        }
    }

    /** @param list<string> $errors */
    private function validateDecimal(
        string $value,
        string $label,
        float $minimum,
        float $maximum,
        int $maximumScale,
        array &$errors,
    ): void {
        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            $errors[] = "El valor {$label} debe ser numérico y usar punto decimal.";

            return;
        }

        $parts = explode('.', $value);
        if (isset($parts[1]) && strlen($parts[1]) > $maximumScale) {
            $errors[] = "El valor {$label} admite como máximo {$maximumScale} decimales.";

            return;
        }

        $number = (float) $value;
        if ($number < $minimum || $number > $maximum) {
            $errors[] = "El valor {$label} debe estar entre {$minimum} y {$maximum}.";
        }
    }

    /** @param list<string> $errors */
    private function validateOptionalDecimal(
        string $value,
        string $label,
        float $minimum,
        float $maximum,
        int $maximumScale,
        array &$errors,
    ): void {
        if ($value === '') {
            return;
        }

        $this->validateDecimal($value, $label, $minimum, $maximum, $maximumScale, $errors);
    }

    /** @param list<string> $errors */
    private function validatePositiveInteger(string $value, string $label, array &$errors): void
    {
        if (! ctype_digit($value) || (int) $value < 1) {
            $errors[] = "El valor {$label} debe ser un entero positivo.";
        }
    }

    /** @param list<string> $errors */
    private function validateState(string $state, string $label, array &$errors): void
    {
        if (! in_array($state, ['activo', 'inactivo'], true)) {
            $errors[] = "El estado de {$label} debe ser activo o inactivo.";
        }
    }

    /** @param list<string> $errors */
    private function validateCoverPath(string $path, string $label, array &$errors): void
    {
        if ($path === '') {
            return;
        }

        if (! preg_match('#^actividades-rutas-demo/[A-Za-z0-9][A-Za-z0-9._-]*\.jpg$#', $path)) {
            $errors[] = "La imagen de {$label} debe usar actividades-rutas-demo/*.jpg o quedar vacía.";
        }
    }

    /** @param list<string> $errors */
    private function throwIfDatasetIsInvalid(array $errors): void
    {
        if ($errors === []) {
            return;
        }

        throw new RuntimeException("Los archivos demostrativos no son válidos:\n- ".implode("\n- ", $errors));
    }

    /**
     * @param  array{destinos: array<string, mixed>, atractivos: array<string, mixed>}  $mapping
     * @param  array{
     *     actividades: list<array<string, string>>,
     *     rutas: list<array<string, string>>,
     *     paradas: list<array<string, string>>,
     *     paradas_por_ruta: array<string, list<array<string, string>>>,
     *     imagenes_portada: list<string>
     * }  $dataset
     * @return array{destino_id: int, atractivo_ids: array<string, int>}
     */
    private function normalizeMapping(array $mapping, array $dataset): array
    {
        if (! isset($mapping['destinos']) || ! is_array($mapping['destinos'])) {
            throw new RuntimeException('El mapeo debe contener el objeto destinos; no se importó nada.');
        }

        if (! isset($mapping['atractivos']) || ! is_array($mapping['atractivos'])) {
            throw new RuntimeException('El mapeo debe contener el objeto atractivos; no se importó nada.');
        }

        $destinationId = $this->extractPositiveId($mapping['destinos'][self::DestinoAlias] ?? null, 'destinos.cusco');
        $attractionIds = [];

        foreach (self::AtractivoAliases as $alias) {
            $attractionIds[$alias] = $this->extractPositiveId(
                $mapping['atractivos'][$alias] ?? null,
                "atractivos.{$alias}",
            );
        }

        if (count(array_unique($attractionIds)) !== count($attractionIds)) {
            throw new RuntimeException('Cada alias de atractivo debe mapearse a un ID distinto; no se importó nada.');
        }

        foreach ($dataset['paradas'] as $stop) {
            if (! array_key_exists($stop['atractivo_alias'], $attractionIds)) {
                throw new RuntimeException("Falta el ID para el alias de atractivo {$stop['atractivo_alias']} en el mapeo.");
            }
        }

        return [
            'destino_id' => $destinationId,
            'atractivo_ids' => $attractionIds,
        ];
    }

    private function extractPositiveId(mixed $value, string $path): int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            throw new RuntimeException("El mapeo {$path} debe contener un ID entero positivo; no se importó nada.");
        }

        return $id;
    }

    /**
     * @param  array{destino_id: int, atractivo_ids: array<string, int>}  $context
     */
    private function validateReferences(array $context, bool $lockRows): void
    {
        $destinationQuery = DB::table('destinos')
            ->select(['id', 'nombre', 'estado'])
            ->where('id', $context['destino_id']);

        if ($lockRows && $this->canLockRows()) {
            $destinationQuery->lockForUpdate();
        }

        $destination = $destinationQuery->first();
        if ($destination === null) {
            throw new RuntimeException("No existe el destino mapeado con ID {$context['destino_id']}; no se importó nada.");
        }

        if ($destination->estado !== 'activo') {
            throw new RuntimeException("El destino mapeado {$destination->nombre} no está activo; no se importó nada.");
        }

        if (! Str::contains(Str::lower((string) $destination->nombre), 'cusco')) {
            throw new RuntimeException("El ID {$destination->id} no corresponde a un destino cuyo nombre contiene Cusco; no se importó nada.");
        }

        $attractionIds = array_values($context['atractivo_ids']);
        sort($attractionIds);

        $attractionsQuery = DB::table('atractivos')
            ->select(['id', 'nombre', 'destino_id', 'estado', 'deleted_at'])
            ->whereIn('id', $attractionIds)
            ->orderBy('id');

        if ($lockRows && $this->canLockRows()) {
            $attractionsQuery->lockForUpdate();
        }

        $attractionsById = $attractionsQuery->get()->keyBy('id');
        foreach ($context['atractivo_ids'] as $alias => $id) {
            $attraction = $attractionsById->get($id);

            if ($attraction === null) {
                throw new RuntimeException("No existe el atractivo mapeado para {$alias} (ID {$id}); no se importó nada.");
            }

            if ((int) $attraction->destino_id !== $context['destino_id']) {
                throw new RuntimeException("El atractivo {$attraction->nombre} ({$alias}) no pertenece al destino Cusco mapeado; no se importó nada.");
            }

            if ($attraction->estado !== 'activo' || $attraction->deleted_at !== null) {
                throw new RuntimeException("El atractivo {$attraction->nombre} ({$alias}) debe estar activo y no eliminado; no se importó nada.");
            }
        }
    }

    private function canLockRows(): bool
    {
        return in_array(DB::getDriverName(), ['pgsql', 'mysql'], true);
    }

    private function acquireImportLock(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::select('SELECT pg_advisory_xact_lock(?)', [self::AdvisoryLockId]);
    }

    /**
     * @param  list<array<string, string>>  $activities
     * @param  list<string>  $availableCoverPaths
     * @return array{
     *     actividades_creadas: int,
     *     actividades_ya_existentes: int,
     *     actividades_eliminadas_omitidas: int
     * }
     */
    private function importActivities(array $activities, int $destinationId, bool $supportsCover, array $availableCoverPaths): array
    {
        $names = array_column($activities, 'nombre');
        $existingRows = DB::table('actividades')
            ->select(['id', 'nombre', 'deleted_at'])
            ->where('destino_id', $destinationId)
            ->whereIn('nombre', $names)
            ->orderBy('id')
            ->get()
            ->groupBy('nombre');

        foreach ($existingRows as $name => $rows) {
            if ($rows->count() > 1) {
                throw new RuntimeException("Hay más de una actividad con la identidad demostrativa {$name}; no se importó nada.");
            }
        }

        $rowsToInsert = [];
        $alreadyExisting = 0;
        $softDeletedOmitted = 0;
        $now = now();

        foreach ($activities as $activity) {
            $existing = $existingRows->get($activity['nombre'])?->first();
            if ($existing !== null) {
                if ($existing->deleted_at !== null) {
                    $softDeletedOmitted++;
                } else {
                    $alreadyExisting++;
                }

                continue;
            }

            $row = [
                'destino_id' => $destinationId,
                'nombre' => $activity['nombre'],
                'descripcion' => $activity['descripcion'],
                'costo' => $activity['costo'],
                'duracion_estimada_min' => (int) $activity['duracion_estimada_min'],
                'latitud' => $activity['latitud'] === '' ? null : $activity['latitud'],
                'longitud' => $activity['longitud'] === '' ? null : $activity['longitud'],
                'estado' => $activity['estado'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($supportsCover && in_array($activity['imagen_portada'], $availableCoverPaths, true)) {
                $row['imagen_portada'] = $activity['imagen_portada'];
            }

            $rowsToInsert[] = $row;
        }

        if ($rowsToInsert !== []) {
            DB::table('actividades')->insert($rowsToInsert);
        }

        return [
            'actividades_creadas' => count($rowsToInsert),
            'actividades_ya_existentes' => $alreadyExisting,
            'actividades_eliminadas_omitidas' => $softDeletedOmitted,
        ];
    }

    /**
     * @param  list<array<string, string>>  $routes
     * @param  array<string, list<array<string, string>>>  $stopsByRoute
     * @param  array{destino_id: int, atractivo_ids: array<string, int>}  $context
     * @param  list<string>  $availableCoverPaths
     * @return array{rutas_creadas: int, rutas_ya_existentes: int, paradas_creadas: int}
     */
    private function importRoutes(
        array $routes,
        array $stopsByRoute,
        array $context,
        bool $supportsCover,
        array $availableCoverPaths,
    ): array {
        $names = array_column($routes, 'nombre');
        $existingRows = DB::table('rutas')
            ->select(['id', 'nombre'])
            ->where('destino_id', $context['destino_id'])
            ->whereIn('nombre', $names)
            ->orderBy('id')
            ->get()
            ->groupBy('nombre');

        foreach ($existingRows as $name => $rows) {
            if ($rows->count() > 1) {
                throw new RuntimeException("Hay más de una ruta con la identidad demostrativa {$name}; no se importó nada.");
            }
        }

        $routesCreated = 0;
        $routesAlreadyExisting = 0;
        $stopsCreated = 0;
        $now = now();

        foreach ($routes as $route) {
            if ($existingRows->has($route['nombre'])) {
                $routesAlreadyExisting++;

                continue;
            }

            $routeRow = [
                'destino_id' => $context['destino_id'],
                'nombre' => $route['nombre'],
                'descripcion' => $route['descripcion'],
                'distancia_km' => $route['distancia_km'],
                'duracion_estimada_min' => (int) $route['duracion_estimada_min'],
                'estado' => $route['estado'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($supportsCover && in_array($route['imagen_portada'], $availableCoverPaths, true)) {
                $routeRow['imagen_portada'] = $route['imagen_portada'];
            }

            $routeId = DB::table('rutas')->insertGetId($routeRow);
            $stops = $stopsByRoute[$route['alias']];
            usort($stops, static function (array $left, array $right): int {
                return (int) $left['orden'] <=> (int) $right['orden'];
            });

            $stopRows = [];
            foreach ($stops as $stop) {
                $stopRows[] = [
                    'ruta_id' => $routeId,
                    'atractivo_id' => $context['atractivo_ids'][$stop['atractivo_alias']],
                    'orden' => (int) $stop['orden'],
                ];
            }

            DB::table('ruta_paradas')->insert($stopRows);
            $routesCreated++;
            $stopsCreated += count($stopRows);
        }

        return [
            'rutas_creadas' => $routesCreated,
            'rutas_ya_existentes' => $routesAlreadyExisting,
            'paradas_creadas' => $stopsCreated,
        ];
    }

    /**
     * @param array{
     *     actividades: list<array<string, string>>,
     *     rutas: list<array<string, string>>,
     *     paradas: list<array<string, string>>,
     *     paradas_por_ruta: array<string, list<array<string, string>>>,
     *     imagenes_portada: list<string>
     * } $dataset
     */
    private function countCoverPathsOmittedBySchema(
        array $dataset,
        bool $supportsActivityCover,
        bool $supportsRouteCover,
    ): int {
        $omitted = 0;

        if (! $supportsActivityCover) {
            foreach ($dataset['actividades'] as $activity) {
                if ($activity['imagen_portada'] !== '') {
                    $omitted++;
                }
            }
        }

        if (! $supportsRouteCover) {
            foreach ($dataset['rutas'] as $route) {
                if ($route['imagen_portada'] !== '') {
                    $omitted++;
                }
            }
        }

        return $omitted;
    }

    /**
     * @param  list<string>  $coverPaths
     * @return list<string>
     */
    private function availableCoverPaths(array $coverPaths): array
    {
        $sourceDirectory = base_path('app/Modules/Actividades/Resources/demo/imagenes');
        $disk = Storage::disk('public');
        $available = [];

        foreach ($coverPaths as $coverPath) {
            $sourcePath = $sourceDirectory.DIRECTORY_SEPARATOR.basename($coverPath);
            if (is_file($sourcePath) || is_file($disk->path($coverPath))) {
                $available[] = $coverPath;
            }
        }

        return $available;
    }

    /**
     * Las imágenes son opcionales: se copian tras confirmar la transacción para que una
     * ausencia de archivo no vuelva inconsistente la importación de datos demostrativos.
     *
     * @param  list<string>  $coverPaths
     * @return array{
     *     imagenes_copiadas: int,
     *     imagenes_ya_existentes: int,
     *     imagenes_fuente_ausente: int,
     *     imagenes_con_error: int
     * }
     */
    private function copyDemoImages(array $coverPaths): array
    {
        $counts = [
            'imagenes_copiadas' => 0,
            'imagenes_ya_existentes' => 0,
            'imagenes_fuente_ausente' => 0,
            'imagenes_con_error' => 0,
        ];
        $disk = Storage::disk('public');
        $sourceDirectory = base_path('app/Modules/Actividades/Resources/demo/imagenes');

        foreach ($coverPaths as $coverPath) {
            $targetPath = $disk->path($coverPath);
            if (is_file($targetPath)) {
                $counts['imagenes_ya_existentes']++;

                continue;
            }

            $sourcePath = $sourceDirectory.DIRECTORY_SEPARATOR.basename($coverPath);
            if (! is_file($sourcePath)) {
                $counts['imagenes_fuente_ausente']++;

                continue;
            }

            try {
                $targetDirectory = dirname($targetPath);
                if (! is_dir($targetDirectory) && ! mkdir($targetDirectory, 0775, true) && ! is_dir($targetDirectory)) {
                    throw new RuntimeException("No se pudo crear el directorio público {$targetDirectory}.");
                }

                $source = fopen($sourcePath, 'rb');
                if ($source === false) {
                    $counts['imagenes_fuente_ausente']++;

                    continue;
                }

                $target = @fopen($targetPath, 'x+b');
                if ($target === false) {
                    fclose($source);

                    if (is_file($targetPath)) {
                        $counts['imagenes_ya_existentes']++;
                    } else {
                        $counts['imagenes_con_error']++;
                    }

                    continue;
                }

                try {
                    if (stream_copy_to_stream($source, $target) === false) {
                        throw new RuntimeException("No se pudo copiar {$sourcePath}.");
                    }
                } catch (Throwable) {
                    fclose($source);
                    fclose($target);
                    @unlink($targetPath);
                    $counts['imagenes_con_error']++;

                    continue;
                }

                fclose($source);
                fclose($target);
                $counts['imagenes_copiadas']++;
            } catch (Throwable) {
                $counts['imagenes_con_error']++;
            }
        }

        return $counts;
    }
}
