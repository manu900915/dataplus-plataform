<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoNegocioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            // Gastronomía y Hospitalidad
            ['nombre' => 'Restaurante', 'descripcion' => 'Restaurantes a la carta, cocina nacional e internacional.'],
            ['nombre' => 'Bar / Lounge', 'descripcion' => 'Bares, coctelerías, tabernas y centros nocturnos.'],
            ['nombre' => 'Cafetería / Panadería', 'descripcion' => 'Cafeterías, dulcerías, reposterías y panaderías artesanales.'],
            ['nombre' => 'Hotel / Hostal', 'descripcion' => 'Hoteles, casas de renta turística, hostales y villas.'],
            ['nombre' => 'Pizzería / Comida Rápida', 'descripcion' => 'Pizzerías, locales de fast food, hamburgueserías y delivery.'],
            ['nombre' => 'Heladería / Cremería', 'descripcion' => 'Heladerías, cremerías y venta de postres fríos.'],
            ['nombre' => 'Centro Recreativo / Eventos', 'descripcion' => 'Salones de fiestas, discotecas, centros de recreación y eventos.'],

            // Comercio y Tiendas
            ['nombre' => 'Tienda de Ropa / Boutique', 'descripcion' => 'Comercio de prendas de vestir, calzado y accesorios de moda.'],
            ['nombre' => 'Minimarket / Bodegón', 'descripcion' => 'Mercados de proximidad, bodegones de víveres y productos importados.'],
            ['nombre' => 'Ferretería / Construcción', 'descripcion' => 'Venta de materiales de construcción, herramientas y plomería.'],
            ['nombre' => 'Farmacia / Droguería', 'descripcion' => 'Venta de medicamentos, productos farmacéuticos e insumos médicos.'],
            ['nombre' => 'Tienda de Electrónica / Telefonía', 'descripcion' => 'Venta y reparación de móviles, cómputo y electrodomésticos.'],
            ['nombre' => 'Joyería / Relojería', 'descripcion' => 'Comercio de joyas, relojes, orfebrería y artículos de lujo.'],
            ['nombre' => 'Floristería / Vivero', 'descripcion' => 'Flores naturales, plantas ornamentales y diseño paisajístico.'],
            ['nombre' => 'Óptica', 'descripcion' => 'Venta de lentes graduados, monturas y servicios optométricos.'],
            ['nombre' => 'Mueblería / Decoración', 'descripcion' => 'Muebles de hogar y oficina, iluminación y artículos de decoración.'],
            ['nombre' => 'Tienda de Mascotas / Veterinaria', 'descripcion' => 'Alimentos para animales, accesorios y consultas veterinarias.'],
            ['nombre' => 'Librería / Papelería', 'descripcion' => 'Libros, artículos de oficina, útiles escolares e impresiones.'],

            // Salud y Cuidado Personal
            ['nombre' => 'Clínica / Policlínico', 'descripcion' => 'Centros de salud, consultas médicas privadas y policlínicos.'],
            ['nombre' => 'Laboratorio Clínico', 'descripcion' => 'Análisis clínicos, biología molecular y diagnóstico por imágenes.'],
            ['nombre' => 'Clínica Dental / Odontología', 'descripcion' => 'Consultorios dentales, ortodoncia y prótesis.'],
            ['nombre' => 'Gimnasio / Centro Fitness', 'descripcion' => 'Gimnasios, crossfit, yoga y academias de artes marciales.'],
            ['nombre' => 'Peluquería / Barbería / Estética', 'descripcion' => 'Salones de belleza, barberías modernas, spa de uñas y estética.'],
            ['nombre' => 'Spa / Centro de Bienestar', 'descripcion' => 'Masajes terapéuticos, saunas y centros de relajación.'],

            // Servicios Profesionales y Corporativos
            ['nombre' => 'Oficinas Corporativas / Empresa', 'descripcion' => 'Sedes empresariales, centros administrativos y filiales.'],
            ['nombre' => 'Firma Legal / Bufete de Abogados', 'descripcion' => 'Servicios jurídicos, notariales y asesoría de empresas.'],
            ['nombre' => 'Agencia de Publicidad / Software', 'descripcion' => 'Desarrollo de software, marketing digital y estudios de diseño.'],
            ['nombre' => 'Agencia de Viajes / Turismo', 'descripcion' => 'Tour operadores, venta de boletos y excursiones turísticas.'],
            ['nombre' => 'Consultoría / Asesoría Contable', 'descripcion' => 'Servicios contables, fiscales, auditoría y consultoría estratégica.'],
            ['nombre' => 'Espacio de Coworking', 'descripcion' => 'Oficinas compartidas, puestos flexibles y salas de reuniones.'],

            // Industria, Almacenamiento y Logística
            ['nombre' => 'Almacén / Centro de Distribución', 'descripcion' => 'Naves industriales, almacenamiento de mercancías y paquetería.'],
            ['nombre' => 'Fábrica / Planta Industrial', 'descripcion' => 'Instalaciones de manufactura, producción y ensamblaje.'],
            ['nombre' => 'Taller Mecánico / Automotriz', 'descripcion' => 'Mecánica ligera y pesada, chapa y pintura, electromecánica.'],
            ['nombre' => 'Lavadero de Autos / Car Wash', 'descripcion' => 'Lavado, desinfección y estética automotriz (detailing).'],
            ['nombre' => 'Imprenta / Artes Gráficas', 'descripcion' => 'Impresión digital, serigrafía, gigantografías y encuadernación.'],
            ['nombre' => 'Cámara Fría / Frigorífico', 'descripcion' => 'Conservación y congelación de alimentos y productos perecederos.'],
            ['nombre' => 'Empresa de Transporte / Logística', 'descripcion' => 'Flotas de transporte de carga, mensajería y mudanzas.'],

            // Educación y Formación
            ['nombre' => 'Colegio / Escuela', 'descripcion' => 'Instituciones educativas, preescolares, primarias y secundarias.'],
            ['nombre' => 'Academia de Idiomas / Cursos', 'descripcion' => 'Centros de capacitación técnica y formación profesional.'],
            ['nombre' => 'Guardería / Centro Infantil', 'descripcion' => 'Cuidado diurno y estimulación temprana de infantes.'],

            // Finanzas y Bienes Raíces
            ['nombre' => 'Sucursal Bancaria / Finanzas', 'descripcion' => 'Bancos, casas de cambio, cajas de ahorro y cajeros automáticos.'],
            ['nombre' => 'Inmobiliaria / Bienes Raíces', 'descripcion' => 'Agencias de compraventa, permutas y alquiler de propiedades.'],

            // Sector Público y Organizaciones
            ['nombre' => 'Institución Pública / Gobierno', 'descripcion' => 'Ministerios, delegaciones, dependencias estatales y gubernamentales.'],
            ['nombre' => 'Embajada / Consulado', 'descripcion' => 'Misiones diplomáticas, oficinas consulares y residencias oficiales.'],
            ['nombre' => 'ONG / Organización Social', 'descripcion' => 'Fundaciones, asociaciones benéficas y entidades socioculturales.'],
            ['nombre' => 'Centro Religioso / Templo', 'descripcion' => 'Iglesias, congregaciones, templos y casas de oración.'],

            // Inmuebles Residenciales y Privados
            ['nombre' => 'Residencia Privada', 'descripcion' => 'Viviendas particulares, casas familiares y chalets.'],
            ['nombre' => 'Edificio Residencial / Condominio', 'descripcion' => 'Comunidades de propietarios, torres de apartamentos y garajes comunitarios.'],

            // Agropecuario y Construcción
            ['nombre' => 'Finca / Explotación Agropecuaria', 'descripcion' => 'Fincas agrícolas, ganaderas, avícolas o acuícolas.'],
            ['nombre' => 'Constructora / Contratista', 'descripcion' => 'Empresas de construcción, reformas civiles y montaje industrial.'],
            ['nombre' => 'Estación de Servicio / Gasolinera', 'descripcion' => 'Venta de combustibles, lubricantes y tiendas de servicio.'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipo_negocios')->updateOrInsert(
                ['nombre' => $tipo['nombre']],
                [
                    'descripcion' => $tipo['descripcion'],
                    'activo'      => true,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
            );
        }
    }
}
