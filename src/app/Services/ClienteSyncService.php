<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use Illuminate\Support\Facades\Log;

class ClienteSyncService
{
    /**
     * Rutas de búsqueda del archivo CSV de clientes.
     */
    protected array $csvPaths = [
        'database/data/clientes_listado_unico.csv',
        'storage/app/clientes_listado_unico.csv',
        'storage/app/clientes.csv',
    ];

    /**
     * Obtener y parsear el listado único de clientes desde el CSV.
     */
    public function parseCsv(?string $customPath = null): array
    {
        $filePath = null;

        if ($customPath && file_exists($customPath)) {
            $filePath = $customPath;
        } else {
            foreach ($this->csvPaths as $p) {
                $fullPath = base_path($p);
                if (file_exists($fullPath)) {
                    $filePath = $fullPath;
                    break;
                }
            }
        }

        if (!$filePath) {
            throw new \RuntimeException("No se encontró el archivo CSV de clientes en las rutas estándar.");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return [];
        }

        $clients = [];
        $currentId = null;

        foreach ($lines as $line) {
            $parts = str_getcsv($line, ',');
            if (count($parts) < 3) {
                continue;
            }

            $rawId = trim($parts[1] ?? '');
            $rawNombre = trim($parts[2] ?? '');

            if (!empty($rawId) && is_numeric($rawId)) {
                $cid = (int) $rawId;
                $currentId = $cid;
                $cleanedName = $this->cleanName($rawNombre);

                $clients[$cid] = [
                    'id'        => $cid,
                    'name'      => $cleanedName,
                    'locations' => [],
                ];
            } elseif ($currentId !== null && !empty($rawNombre)) {
                $cleanedLoc = $this->cleanName($rawNombre);
                if (!empty($cleanedLoc)) {
                    $clients[$currentId]['locations'][] = $cleanedLoc;
                }
            }
        }

        // Filtrar solo aquellos que tengan nombre válido (no vacíos ni puramente números)
        return array_filter($clients, function ($item) {
            $name = $item['name'];
            return !empty($name) && !is_numeric($name) && mb_strlen($name) >= 2;
        });
    }

    /**
     * Sincronizar clientes en la base de datos según el listado del CSV.
     */
    public function syncFromCsv(?string $customPath = null, int $year = 2026, bool $padThreeDigits = false): array
    {
        $clientsList = $this->parseCsv($customPath);

        $created = 0;
        $updated = 0;
        $cleaned = 0;

        // 1. Limpiar o depurar clientes temporales que tenían como nombre solo un número (ej: "344", "347")
        $corruptClients = Cliente::all()->filter(function (Cliente $c) {
            $trimmed = trim($c->nombre);
            return is_numeric($trimmed) || empty($trimmed);
        });

        foreach ($corruptClients as $bad) {
            try {
                // Si no tiene proyectos ni ventas asociadas, eliminarlo
                $hasProyectos = method_exists($bad, 'proyectos') && $bad->proyectos()->exists();
                if (!$hasProyectos) {
                    $bad->ubicaciones()->delete();
                    $bad->contactos()->delete();
                    $bad->delete();
                    $cleaned++;
                }
            } catch (\Throwable $e) {
                Log::warning("No se pudo depurar cliente corrupto ID {$bad->id}: " . $e->getMessage());
            }
        }

        // 2. Insertar o actualizar cada cliente con su código CLI-año-ID exacto
        foreach ($clientsList as $item) {
            $cid = $item['id'];
            $nombre = $item['name'];
            $locations = $item['locations'];

            // Formato de código: CLI-2026-247 (o con relleno si se solicita)
            $formattedId = $padThreeDigits ? str_pad($cid, 3, '0', STR_PAD_LEFT) : $cid;
            $codigo = "CLI-{$year}-{$formattedId}";

            // Buscar si ya existe por código exacto o por nombre comercial
            $cliente = Cliente::where('codigo', $codigo)
                ->orWhere('nombre', $nombre)
                ->orWhere('nombre_comercial', $nombre)
                ->first();

            if ($cliente) {
                $cliente->codigo = $codigo;
                $cliente->nombre = $nombre;
                $cliente->nombre_comercial = $nombre;
                $cliente->tipo_persona = 'juridica';
                $cliente->activo = true;
                $cliente->save();
                $updated++;
            } else {
                $cliente = Cliente::create([
                    'codigo'           => $codigo,
                    'nombre'           => $nombre,
                    'nombre_comercial' => $nombre,
                    'tipo_persona'     => 'juridica',
                    'activo'           => true,
                    'notas'            => 'Importado desde Listado Único',
                ]);
                $created++;
            }

            // 3. Crear ubicación principal para el cliente
            $tipoPrincipal = $this->detectLocationType($nombre);
            $cliente->ubicaciones()->firstOrCreate(
                ['nombre' => $nombre],
                [
                    'tipo'   => $tipoPrincipal,
                    'activo' => true,
                ]
            );

            // 4. Crear sub-ubicaciones adicionales si el cliente tiene sedes adicionales
            foreach ($locations as $locName) {
                if (empty($locName) || $locName === $nombre) {
                    continue;
                }
                $tipoSub = $this->detectLocationType($locName);
                $cliente->ubicaciones()->firstOrCreate(
                    ['nombre' => $locName],
                    [
                        'tipo'   => $tipoSub,
                        'activo' => true,
                    ]
                );
            }
        }

        return [
            'total_csv' => count($clientsList),
            'created'   => $created,
            'updated'   => $updated,
            'cleaned'   => $cleaned,
        ];
    }

    /**
     * Limpiar formato de nombre.
     */
    protected function cleanName(string $name): string
    {
        $name = str_replace('_', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    /**
     * Detectar si una ubicación es negocio o residencia.
     */
    protected function detectLocationType(string $name): string
    {
        $lower = strtolower($name);
        if (str_contains($lower, 'casa') || str_contains($lower, 'residencia') || str_contains($lower, 'domicilio')) {
            return 'residencial';
        }
        return 'negocio';
    }
}
