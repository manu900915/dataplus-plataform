<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Registra las categorías oficiales del inventario de DataPlus
     * acordes a su objeto social: Seguridad Electrónica (CCTV y SACI),
     * Redes & Gestión Remota, Control de Acceso, Energía y Cableado Estructurado.
     */
    public function up(): void
    {
        // 1. Agregar columna 'descripcion' si no existe
        if (Schema::hasTable('categorias_item') && !Schema::hasColumn('categorias_item', 'descripcion')) {
            Schema::table('categorias_item', function (Blueprint $table) {
                $table->text('descripcion')->nullable()->after('nombre');
            });
        }

        // 2. Limpiar categorías de plantillas genéricas (solo si no tienen ítems vinculados)
        $categoriasObsoletas = [
            'Insecticidas y plaguicidas',
            'Cebos y trampas',
            'Envases y consumibles',
            'Equipos de aspersión',
        ];

        foreach ($categoriasObsoletas as $nombreObsoleto) {
            $cat = DB::table('categorias_item')->where('nombre', $nombreObsoleto)->first();
            if ($cat) {
                $tieneItems = DB::table('items')->where('categoria_id', $cat->id)->exists();
                if (!$tieneItems) {
                    DB::table('categorias_item')->where('id', $cat->id)->delete();
                } else {
                    DB::table('categorias_item')->where('id', $cat->id)->update(['activo' => false]);
                }
            }
        }

        // 3. Catálogo profesional oficial de DataPlus
        $categoriasDataPlus = [
            [
                'nombre' => 'CCTV - Cámaras y Dispositivos de Captura',
                'tipo' => 'equipamiento',
                'descripcion' => 'Cámaras IP, domos, tubulares/bullet, cámaras PTZ, cámaras térmicas y minidomos con audio.',
                'activo' => true,
            ],
            [
                'nombre' => 'CCTV - Grabadores y Almacenamiento (NVR / DVR)',
                'tipo' => 'equipamiento',
                'descripcion' => 'Grabadores NVR, DVR/XVR, discos duros para videovigilancia (WD Purple, SkyHawk) y tarjetas industriales.',
                'activo' => true,
            ],
            [
                'nombre' => 'SACI - Paneles de Alarma y Centrales de Control',
                'tipo' => 'equipamiento',
                'descripcion' => 'Centrales de alarma cableadas e inalámbricas, paneles de control, expansores de zonas y comunicadores GSM/IP.',
                'activo' => true,
            ],
            [
                'nombre' => 'SACI - Sensores y Detección de Intrusión',
                'tipo' => 'equipamiento',
                'descripcion' => 'Sensores de movimiento PIR interiores/exteriores, contactos magnéticos, detectores de impacto y barreras perimetrales.',
                'activo' => true,
            ],
            [
                'nombre' => 'SACI - Notificación Sonora y Señalización',
                'tipo' => 'equipamiento',
                'descripcion' => 'Sirenas exteriores con flash/estroboscopio, sirenas piezoeléctricas de interior, campanas industriales y módulos de voz.',
                'activo' => true,
            ],
            [
                'nombre' => 'Redes, Conectividad y Gestión Remota',
                'tipo' => 'equipamiento',
                'descripcion' => 'Routers 4G/LTE industriales, módems, switches PoE/Gigabit gestionables, puntos de acceso WiFi, antenas de alta ganancia y SIMs M2M.',
                'activo' => true,
            ],
            [
                'nombre' => 'Control de Acceso e Intercomunicación',
                'tipo' => 'equipamiento',
                'descripcion' => 'Controladores de acceso IP, lectores biométricos (huella/facial), lectores RFID, electroimanes (280kg/500kg), cantoneras y pulsadores No Touch.',
                'activo' => true,
            ],
            [
                'nombre' => 'Energía, Respaldo y Protección Eléctrica',
                'tipo' => 'ambos',
                'descripcion' => 'Sistemas UPS ininterrumpidos, fuentes conmutadas centralizadas (12V/24V), baterías AGM de ciclo profundo (12V 4Ah/7Ah) y supresores de sobretensión.',
                'activo' => true,
            ],
            [
                'nombre' => 'Cableado Estructurado y Conectividad Pasiva',
                'tipo' => 'material',
                'descripcion' => 'Bobinas de cable UTP/FTP Cat 6/6A para interior y exterior, conectores RJ45 apantallados, patch panels, jacks modulares y patch cords.',
                'activo' => true,
            ],
            [
                'nombre' => 'Canalizaciones, Tuberías y Cajas de Conexión',
                'tipo' => 'material',
                'descripcion' => 'Tuberías conduit metálicas EMT y PVC, canaletas ranuradas/decorativas, uniones, curvas herméticas y cajas de paso estancas IP65/IP66.',
                'activo' => true,
            ],
            [
                'nombre' => 'Fijación, Tornillería y Consumibles de Montaje',
                'tipo' => 'material',
                'descripcion' => 'Tarugos/tacos plásticos y metálicos, tornillos autoperforantes, amarres/bridas UV, cinta aislante y vulcanizada, y selladores de silicona.',
                'activo' => true,
            ],
            [
                'nombre' => 'Herramientas de Trabajo e Instrumental de Medición',
                'tipo' => 'equipamiento',
                'descripcion' => 'Crimpeadoras RJ45, herramientas de impacto punch-down, probadores/testers de red, multímetros digitales, taladros rotomartillo y escaleras.',
                'activo' => true,
            ],
            [
                'nombre' => 'Equipos de Protección Individual (EPI / EPP)',
                'tipo' => 'material',
                'descripcion' => 'Cascos dieléctricos, arneses anticaídas de cuerpo entero para trabajo en altura, guantes técnicos, gafas de seguridad y chalecos reflectivos.',
                'activo' => true,
            ],
        ];

        $now = now();
        foreach ($categoriasDataPlus as $cat) {
            DB::table('categorias_item')->updateOrInsert(
                ['nombre' => $cat['nombre']],
                [
                    'tipo' => $cat['tipo'],
                    'descripcion' => $cat['descripcion'],
                    'activo' => $cat['activo'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('categorias_item') && Schema::hasColumn('categorias_item', 'descripcion')) {
            Schema::table('categorias_item', function (Blueprint $table) {
                $table->dropColumn('descripcion');
            });
        }
    }
};
