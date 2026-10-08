<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tipos = [
            [
                'nombre' => 'Instalación de CCTV & Videovigilancia',
                'descripcion' => 'Montaje y configuración de cámaras de seguridad IP/HD, grabadores NVR/XVR, cableado y visualización remota.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sistemas de Alarma Contra Intrusión (SACI)',
                'descripcion' => 'Instalación de centrales de alarma, sensores de movimiento, contactos magnéticos, sirenas y teclados de armado.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Control de Acceso & Cerraduras Electrónicas',
                'descripcion' => 'Cerraduras digitales biométricas, electroimanes, lectores RFID, pulsadores y control de asistencia.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Redes de Datos & Cableado Estructurado',
                'descripcion' => 'Tendido y conectorización Cat6/Cat6A, gabinetes rack, patch panels, switches y certificación de puntos de red.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Wi-Fi Empresarial & Enlaces Inalámbricos',
                'descripcion' => 'Puntos de acceso de alta densidad, controladores centralizados, antenas y enlaces de radio punto a punto.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Video Porteros & Intercomunicación IP',
                'descripcion' => 'Sistemas de timbre inteligente, monitores interiores de videoportero y apertura remota de accesos.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Detección de Incendio & Seguridad Perimetral',
                'descripcion' => 'Detectores de humo fotoeléctricos, estaciones manuales, sirenas estroboscópicas y sensores perimetrales.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Respaldo Energético & UPS para Sistemas Críticos',
                'descripcion' => 'Bancos de baterías, inversores y UPS para alimentación ininterrumpida de cámaras y servidores.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Mantenimiento Preventivo & Pólizas de Soporte',
                'descripcion' => 'Revisión periódica de equipos, limpieza de ópticas, ajuste de cableado y soporte técnico prioritario.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Investigación & Desarrollo (I+D) / Obras Especiales',
                'descripcion' => 'Proyectos de innovación tecnológica, prototipado de hardware, automatización e integraciones personalizadas.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tipos as $tipo) {
            $exists = DB::table('tipos_proyecto')->where('nombre', $tipo['nombre'])->exists();
            if (!$exists) {
                DB::table('tipos_proyecto')->insert($tipo);
            }
        }
    }

    public function down(): void
    {
        // No destructivo
    }
};
