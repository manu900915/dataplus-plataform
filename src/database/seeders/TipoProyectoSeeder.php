<?php

namespace Database\Seeders;

use App\Models\TipoProyecto;
use Illuminate\Database\Seeder;

class TipoProyectoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Instalación de CCTV & Videovigilancia',
                'descripcion' => 'Montaje y configuración de cámaras de seguridad IP/HD, grabadores NVR/XVR, cableado y visualización remota.',
                'activo' => true,
            ],
            [
                'nombre' => 'Sistemas de Alarma Contra Intrusión (SACI)',
                'descripcion' => 'Instalación de centrales de alarma, sensores de movimiento, contactos magnéticos, sirenas y teclados de armado.',
                'activo' => true,
            ],
            [
                'nombre' => 'Control de Acceso & Cerraduras Electrónicas',
                'descripcion' => 'Cerraduras digitales biométricas, electroimanes, lectores RFID, pulsadores y control de asistencia.',
                'activo' => true,
            ],
            [
                'nombre' => 'Redes de Datos & Cableado Estructurado',
                'descripcion' => 'Tendido y conectorización Cat6/Cat6A, gabinetes rack, patch panels, switches y certificación de puntos de red.',
                'activo' => true,
            ],
            [
                'nombre' => 'Wi-Fi Empresarial & Enlaces Inalámbricos',
                'descripcion' => 'Puntos de acceso de alta densidad, controladores centralizados, antenas y enlaces de radio punto a punto.',
                'activo' => true,
            ],
            [
                'nombre' => 'Video Porteros & Intercomunicación IP',
                'descripcion' => 'Sistemas de timbre inteligente, monitores interiores de videoportero y apertura remota de accesos.',
                'activo' => true,
            ],
            [
                'nombre' => 'Detección de Incendio & Seguridad Perimetral',
                'descripcion' => 'Detectores de humo fotoeléctricos, estaciones manuales, sirenas estroboscópicas y sensores perimetrales.',
                'activo' => true,
            ],
            [
                'nombre' => 'Respaldo Energético & UPS para Sistemas Críticos',
                'descripcion' => 'Bancos de baterías, inversores y UPS para alimentación ininterrumpida de cámaras y servidores.',
                'activo' => true,
            ],
            [
                'nombre' => 'Mantenimiento Preventivo & Pólizas de Soporte',
                'descripcion' => 'Revisión periódica de equipos, limpieza de ópticas, ajuste de cableado y soporte técnico prioritario.',
                'activo' => true,
            ],
            [
                'nombre' => 'Investigación & Desarrollo (I+D) / Obras Especiales',
                'descripcion' => 'Proyectos de innovación tecnológica, prototipado de hardware, automatización e integraciones personalizadas.',
                'activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoProyecto::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                [
                    'descripcion' => $tipo['descripcion'],
                    'activo' => $tipo['activo'],
                ]
            );
        }
    }
}
