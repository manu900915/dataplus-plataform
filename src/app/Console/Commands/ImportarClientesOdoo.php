<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\ClienteUbicacion;
use Illuminate\Console\Command;

class ImportarClientesOdoo extends Command
{
    protected $signature = 'clientes:importar-odoo {archivo}';
    protected $description = 'Importa clientes desde CSV de Odoo';

    public function handle(): int
    {
        $archivo = $this->argument('archivo');

        if (!file_exists($archivo)) {
            $this->error("Archivo no encontrado: {$archivo}");
            return 1;
        }

        $contenido = file_get_contents($archivo);
        
        // Quitar BOM si existe
        $contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido);
        
        $lineas = explode("\n", $contenido);
        $totalLineas = count($lineas);
        
        $this->info("Total de líneas en archivo: {$totalLineas}");

        if ($totalLineas < 2) {
            $this->error('El archivo está vacío o no tiene datos');
            return 1;
        }

        // Leer cabeceras
        $cabeceras = str_getcsv(trim($lineas[0]), ',');
        $cabeceras = array_map('trim', $cabeceras);
        
        $this->info("Cabeceras encontradas: " . implode(' | ', $cabeceras));

        $filas = [];
        for ($i = 1; $i < $totalLineas; $i++) {
            $linea = trim($lineas[$i]);
            if (empty($linea)) continue;
            
            $fila = str_getcsv($linea, ',');
            
            // Asegurar misma cantidad de columnas
            if (count($fila) !== count($cabeceras)) {
                continue;
            }
            
            $filas[] = array_combine($cabeceras, $fila);
        }

        $this->info("Filas válidas leídas: " . count($filas));

        // Agrupar por cliente base
        $grupos = [];
        foreach ($filas as $fila) {
            $nombreCompleto = $fila['Translated Display Name'] ?? '';
            if (empty($nombreCompleto)) continue;
            
            $clienteBase = $this->extraerClienteBase($nombreCompleto);
            if (empty($clienteBase)) continue;
            
            $grupos[$clienteBase][] = $fila;
        }

        $this->info("Grupos de clientes detectados: " . count($grupos));

        $creados = 0;
        $ubicacionesCreadas = 0;

        foreach ($grupos as $clienteBase => $registros) {
            if (empty($clienteBase)) continue;

            $principal = $registros[0];
            $esEmpresa = $this->esEmpresa($clienteBase, $registros);

            // Buscar email y teléfono en cualquier registro del grupo
            $email = null;
            $telefono = null;
            foreach ($registros as $r) {
                if (empty($email) && !empty($r['Correo electrónico'])) {
                    $email = trim($r['Correo electrónico']);
                }
                if (empty($telefono) && !empty($r['Teléfono'])) {
                    $telefono = trim($r['Teléfono']);
                }
            }

            $cliente = Cliente::create([
                //'codigo' => $this->generarCodigo(),
                'codigo' => !empty($datos['codigo']) ? trim($datos['codigo']) : $this->generarCodigo(),
                'tipo_persona' => $esEmpresa ? 'juridica' : 'natural',
                'documento' => null,
                'nombre' => $this->limpiarNombre($clienteBase),
                'nombre_comercial' => $esEmpresa ? $this->limpiarNombre($clienteBase) : null,
                'email' => $this->limpiarEmail($email),
                'telefono' => $this->limpiarTelefono($telefono),
                'direccion' => null,
                'provincia' => $this->mapearProvincia($principal['Ciudad'] ?? null),
                'municipio' => $this->mapearMunicipio($principal['Ciudad'] ?? null),
                'notas' => 'Importado desde Odoo',
                'activo' => true,
            ]);

            $creados++;

            foreach ($registros as $reg) {
                $nombreUbicacion = $this->extraerNombreUbicacion(
                    $reg['Translated Display Name'] ?? '', 
                    $clienteBase
                );

                if (empty($nombreUbicacion)) {
                    $nombreUbicacion = 'Principal';
                }

                $telUbicacion = !empty($reg['Teléfono']) ? trim($reg['Teléfono']) : null;

                ClienteUbicacion::create([
                    'cliente_id' => $cliente->id,
                    'nombre' => $nombreUbicacion,
                    'tipo' => $this->detectarTipoUbicacion($nombreUbicacion),
                    'tipo_negocio_id' => null,
                    'direccion' => null,
                    'provincia' => $this->mapearProvincia($reg['Ciudad'] ?? null),
                    'municipio' => $this->mapearMunicipio($reg['Ciudad'] ?? null),
                    'contacto_nombre' => $this->extraerContacto($reg['Translated Display Name'] ?? ''),
                    'contacto_telefono' => $telUbicacion,
                    'notas' => !empty($reg['Actividades']) ? trim($reg['Actividades']) : null,
                    'activo' => true,
                ]);

                $ubicacionesCreadas++;
            }

            $this->info("  ✓ {$clienteBase} ({$esEmpresa}) - " . count($registros) . " ubicaciones");
        }

        $this->newLine();
        $this->info("✅ Importación completada:");
        $this->info("   Clientes creados: {$creados}");
        $this->info("   Ubicaciones creadas: {$ubicacionesCreadas}");

        return 0;
    }

    private function extraerClienteBase(string $nombre): string
    {
        // Quitar código entre corchetes: [043]
        $nombre = preg_replace('/^\[\d+\]\s*/', '', $nombre);
        
        // Quitar "(→ Cliente)" al final
        $nombre = preg_replace('/\s*\(→.*?\)\s*$/', '', $nombre);
        
        // Si tiene coma, la primera parte es el cliente
        if (str_contains($nombre, ',')) {
            $partes = explode(',', $nombre, 2);
            $base = trim($partes[0]);
            
            // Quitar número inicial si existe: "452 Selma..." → "Selma..."
            if (preg_match('/^\d+\s+(.+)$/', $base, $matches)) {
                $base = $matches[1];
            }
            
            return $base;
        }

        // Si tiene "→", es referencia
        if (str_contains($nombre, '→')) {
            $partes = explode('→', $nombre);
            return trim($partes[1]);
        }

        // Quitar número inicial si existe
        return preg_replace('/^\d+\s+/', '', trim($nombre));
    }

    private function extraerNombreUbicacion(string $nombreCompleto, string $clienteBase): string
    {
        $nombre = preg_replace('/^\[\d+\]\s*/', '', $nombreCompleto);
        $nombre = preg_replace('/\s*\(→.*?\)\s*$/', '', $nombre);

        if (str_contains($nombre, ',')) {
            $partes = explode(',', $nombre, 2);
            return trim($partes[1]);
        }

        return 'Principal';
    }

    private function extraerContacto(string $nombre): ?string
    {
        if (preg_match('/\(([^)]+)\)/', $nombre, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function esEmpresa(string $clienteBase, array $registros): bool
    {
        $palabras = ['Restaurante', 'Cafe', 'Bar', 'Tienda', 'Almacen', 'Mercado', 'Salon', 
                     'Pizzeria', 'Bodega', 'Heladeria', 'Pan', 'Fregadora', 'Cafeteria',
                     'Hostal', 'Fabrica', 'Taller', 'Dulceria', 'Charcuteria', 'Chocolatera',
                     'Kiosko', 'Mercadito', 'Finca', 'Estudio', 'Club', 'AutoPartes'];
        
        $texto = strtolower($clienteBase);
        foreach ($palabras as $p) {
            if (str_contains($texto, strtolower($p))) return true;
        }
        
        return count($registros) > 1;
    }

    private function detectarTipoUbicacion(string $nombre): string
    {
        $palabras = ['Restaurante', 'Cafe', 'Bar', 'Tienda', 'Almacen', 'Mercado', 'Salon',
                     'Pizzeria', 'Bodega', 'Heladeria', 'Pan', 'Fregadora', 'Cafeteria',
                     'Hostal', 'Fabrica', 'Taller', 'Dulceria', 'Charcuteria', 'Chocolatera',
                     'Kiosko', 'Mercadito', 'Finca', 'Estudio', 'Club'];
        
        foreach ($palabras as $p) {
            if (str_contains(strtolower($nombre), strtolower($p))) return 'negocio';
        }
        return 'residencial';
    }

    private function limpiarNombre(string $nombre): string
    {
        return trim(preg_replace('/^\d+\s+/', '', $nombre));
    }

    private function limpiarEmail(?string $email): ?string
    {
        if (empty($email)) return null;
        $email = trim($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    private function limpiarTelefono(?string $telefono): ?string
    {
        return empty($telefono) ? null : trim($telefono);
    }

    private function mapearProvincia(?string $ciudad): ?string
    {
        if (empty($ciudad)) return null;
        
        $mapa = [
            'la habana' => 'La_Habana', 'habana' => 'La_Habana', 'centro habana' => 'La_Habana',
            'plaza' => 'La_Habana', 'playa' => 'La_Habana', 'vedado' => 'La_Habana',
            'cerro' => 'La_Habana', 'sevillano' => 'La_Habana', 'casino deportivo' => 'La_Habana',
            '10 de octubre' => 'La_Habana', 'boyeros' => 'La_Habana', 'arroyo naranjo' => 'La_Habana',
            'san miguel' => 'La_Habana', 'santo suarez' => 'La_Habana', 'guanabacoa' => 'La_Habana',
            'alamar' => 'La_Habana', 'lisa' => 'La_Habana', 'la lisa' => 'La_Habana',
            'guanabo' => 'La_Habana', 'guinera' => 'La_Habana', 'bauta' => 'Artemisa',
            'pinar del rio' => 'Pinar_del_Rio', 'artemisa' => 'Artemisa', 'mayabeque' => 'Mayabeque',
            'matanzas' => 'Matanzas', 'cienfuegos' => 'Cienfuegos', 'villa clara' => 'Villa_Clara',
            'sancti spiritus' => 'Sancti_Spiritus', 'ciego de avila' => 'Ciego_de_Avila',
            'camaguey' => 'Camaguey', 'las tunas' => 'Las_Tunas', 'holguin' => 'Holguin',
            'granma' => 'Granma', 'santiago de cuba' => 'Santiago_de_Cuba',
            'guantanamo' => 'Guantanamo', 'isla de la juventud' => 'Isla_de_la_Juventud',
        ];
        
        return $mapa[strtolower(trim($ciudad))] ?? null;
    }

    private function mapearMunicipio(?string $ciudad): ?string
    {
        if (empty($ciudad)) return null;
        
        $mapa = [
            'Vedado' => 'Vedado', 'Plaza' => 'Plaza', 'Playa' => 'Playa', 'Cerro' => 'Cerro',
            'Centro Habana' => 'Centro_Habana', 'La Habana' => 'La_Habana',
            'Sevillano' => 'Diez_de_Octubre', 'Casino Deportivo' => 'Cerro',
            '10 de octubre' => 'Diez_de_Octubre', 'Boyeros' => 'Boyeros',
            'Arroyo Naranjo' => 'Arroyo_Naranjo', 'San Miguel' => 'San_Miguel_del_Padron',
            'Santo Suarez' => 'Diez_de_Octubre', 'Guanabacoa' => 'Guanabacoa',
            'Alamar' => 'Guanabacoa', 'Lisa' => 'La_Lisa', 'La Lisa' => 'La_Lisa',
            'Guanabo' => 'Guanabacoa', 'Güinera' => 'Arroyo_Naranjo', 'Bauta' => 'Bauta',
        ];
        
        return $mapa[trim($ciudad)] ?? trim($ciudad);
    }

    private function generarCodigo(): string
    {
        $year = now()->year;
        $count = Cliente::whereYear('created_at', $year)->count() + 1;
        return "CLI-{$year}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}