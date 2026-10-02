<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use Illuminate\Console\Command;

class ImportarClientes extends Command
{
    protected $signature = 'importar:clientes {archivo=storage/app/clientes.csv : Ruta del archivo CSV}';
    protected $description = 'Importa la lista de clientes desde un archivo CSV, limpiando datos y generando códigos únicos';

    public function handle()
    {
        $archivo = $this->argument('archivo');

        if (!file_exists($archivo)) {
            $this->error("❌ El archivo {$archivo} no existe. Asegúrate de subirlo a storage/app/");
            return Command::FAILURE;
        }

        $this->info('🚀 Iniciando importación de clientes...');
        
        $manejador = fopen($archivo, 'r');
        $importados = 0;
        $omitidos = 0;
        
        // Obtenemos el último código para continuar la secuencia de forma segura
        $ultimoCliente = Cliente::orderBy('id', 'desc')->first();
        $contador = $ultimoCliente ? (intval(substr($ultimoCliente->codigo, 4)) + 1) : 1;

        $bar = $this->output->createProgressBar(500);
        $bar->start();

        // Saltar la primera línea (encabezados del CSV)
        fgetcsv($manejador);

        while (($fila = fgetcsv($manejador, 1000, ',')) !== false) {
            // La columna 1 es "DENOMINACION DE NEGOCIO"
            $nombreComercial = trim($fila[1] ?? '');

            // 1. Omitir filas vacías, con solo '|' o sin nombre
            if (empty($nombreComercial) || $nombreComercial === '|' || strlen($nombreComercial) < 3) {
                $omitidos++;
                $bar->advance();
                continue;
            }

            // 2. Limpiar el nombre: quitar guiones bajos y múltiples espacios
            $nombreLimpio = str_replace('_', ' ', $nombreComercial);
            $nombreLimpio = preg_replace('/\s+/', ' ', $nombreLimpio);
            $nombreLimpio = trim($nombreLimpio);

            // 3. Verificar si ya existe para no duplicar
            if (Cliente::where('nombre_comercial', $nombreLimpio)->exists()) {
                $omitidos++;
                $bar->advance();
                continue;
            }

            // 4. Generar código único (ej: CLI-0001)
            $codigo = 'CLI-' . str_pad($contador, 4, '0', STR_PAD_LEFT);
            $contador++;

            // 5. Crear el cliente en la base de datos
            Cliente::create([
                'codigo' => $codigo,
                'tipo_persona' => 'juridica', // Al ser "Denominación de Negocio", asumimos jurídica
                'nombre' => $nombreLimpio,
                'nombre_comercial' => $nombreLimpio,
                'activo' => true,
                'notas' => 'Importado desde listado único',
            ]);

            $importados++;
            $bar->advance();
        }

        fclose($manejador);
        $bar->finish();

        $this->newLine(2);
        $this->info("✅ Importación completada con éxito.");
        $this->info("📥 Clientes importados: {$importados}");
        $this->info("⏭️ Filas omitidas (vacías o duplicadas): {$omitidos}");

        return Command::SUCCESS;
    }
}
