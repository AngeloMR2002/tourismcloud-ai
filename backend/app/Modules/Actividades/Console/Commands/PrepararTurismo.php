<?php

namespace App\Modules\Actividades\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class PrepararTurismo extends Command
{
    protected $signature = 'tourism:preparar {--sin-datos : Ejecutar solo las migraciones}';
    protected $description = 'Conectar PostgreSQL, preparar el esquema privado y cargar el catálogo verificado';

    public function handle(): int
    {
        try {
            if (DB::connection()->getDriverName() !== 'pgsql') {
                $this->error('Configura DB_CONNECTION=pgsql para conectar con Supabase.');
                return self::FAILURE;
            }
            $schema = config('database.connections.pgsql.search_path');
            if (blank(config('database.connections.pgsql.password')) && blank(config('database.connections.pgsql.url'))) {
                $this->error('Guarda la contraseña de PostgreSQL en DB_PASSWORD de .env. No la publiques en el chat ni en Git.');
                return self::FAILURE;
            }
            if (! is_string($schema) || ! preg_match('/\\A[a-z_][a-z0-9_]{0,62}\\z/', $schema)
                || in_array($schema, ['public', 'auth', 'storage', 'realtime', 'extensions'], true)) {
                $this->error('DB_SCHEMA debe ser un esquema privado, por ejemplo tourismcloud.');
                return self::FAILURE;
            }
            DB::statement('CREATE SCHEMA IF NOT EXISTS "'.$schema.'"');
            if ($this->call('migrate', ['--force' => true, '--no-interaction' => true]) !== self::SUCCESS) {
                return self::FAILURE;
            }
            if (! $this->option('sin-datos')) {
                if ($this->call('db:seed', ['--force' => true, '--no-interaction' => true]) !== self::SUCCESS) {
                    return self::FAILURE;
                }
            }
            $connection = DB::selectOne("select current_database() as database, current_schema() as schema,
                (select ssl from pg_stat_ssl where pid = pg_backend_pid()) as ssl");
            $this->info('Conexión verificada. Esquema: '.$connection->schema.'; SSL: '.($connection->ssl ? 'activo' : 'inactivo').'.');
            return self::SUCCESS;
        } catch (Throwable $error) {
            report($error);
            $this->error('No fue posible preparar PostgreSQL. Revisa DB_HOST, DB_USERNAME y DB_PASSWORD en .env, SSL y la extensión pdo_pgsql.');
            return self::FAILURE;
        }
    }
}
