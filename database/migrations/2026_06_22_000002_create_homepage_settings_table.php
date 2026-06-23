<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateHomepageSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Crear la tabla de configuraciones
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // 2. Sembrar datos por defecto de la joyería
        $defaultSettings = [
            'hero_title' => 'Casa de Joyería Colibrí',
            'hero_subtitle' => 'Descubre piezas de alta joyería diseñadas con pasión y excelencia artesanal. Fusionamos metales nobles y gemas selectas para crear legados atemporales.',
            'hero_pills' => json_encode(['Alta Joyería', 'Diseños Exclusivos', 'Artesanía Tradicional']),
            'story_title' => 'Nuestra Historia',
            'story_text' => 'Desde nuestra fundación, nos hemos dedicado a perfeccionar el arte de la orfebrería. Cada una de nuestras piezas es concebida y modelada por maestros artesanos, asegurando que cada detalle refleje exclusividad, sofisticación y la belleza inigualable de los metales más puros.',
            'mission_title' => 'Misión & Visión',
            'mission_text' => 'Misión: Crear joyas excepcionales que trasciendan generaciones, utilizando materiales sostenibles y procesos de manufactura artesanal con los más altos estándares de calidad. Visión: Consolidarnos como la casa de joyería fina referente de la región, reconocida por la excelencia de nuestros de diseños y un servicio al cliente personalizado y cálido.',
            'materials_title' => 'Metales Nobles & Gemas Certificadas',
            'materials_text' => 'En nuestra casa de joyería, la calidad no es una opción, sino nuestro pilar fundamental. Trabajamos exclusivamente con oro certificado de 10k, 14k y 24k, plata ley .925 y gemas seleccionadas meticulosamente por gemólogos expertos. Cada pieza se somete a rigurosos controles de calidad antes de llegar a sus manos.',
            'store_address' => 'Av. Paseo de la Reforma 450, Juárez, Ciudad de México',
            'store_hours' => 'Lunes a Viernes: 10:00 AM - 8:00 PM | Sábados: 11:00 AM - 6:00 PM',
            'maps_iframe' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3762.6615822363193!2d-99.16744832569502!3d19.427024440866205!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff35f5555555%3A0x5555555555555555!2sPaseo%20de%20la%20Reforma!5e0!3m2!1ses-419!2smx!4v1719000000000!5m2!1ses-419!2smx',
            'contact_email' => 'contacto@joyeriacolibri.com',
            'contact_phone' => '5512345678',
            'gallery_images' => json_encode([
                'producto_1729.jpg',
                'producto_BB02.jpg',
                'producto_CARTIER-60CM.jpg',
                'producto_DIJE-Y.jpg',
                'producto_DJ1123.jpg',
                'producto_BR1516AM.jpg'
            ]),
            'team_members' => json_encode([
                [
                    'name' => 'Sofía Mendoza',
                    'role' => 'Directora Creativa & Gemóloga',
                    'image' => ''
                ],
                [
                    'name' => 'Alejandro Ruiz',
                    'role' => 'Maestro Orfebre',
                    'image' => ''
                ],
                [
                    'name' => 'Camila Ortega',
                    'role' => 'Asesora de Alta Joyería',
                    'image' => ''
                ]
            ])
        ];

        foreach ($defaultSettings as $key => $value) {
            DB::table('homepage_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Crear el nuevo permiso 'manage_homepage'
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => 'Configurar Portada',
            'slug' => 'manage_homepage',
            'description' => 'Permite modificar la página de inicio (imágenes, textos, contacto, etc.)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Asociar el permiso al rol 'admin'
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        if ($adminRole) {
            DB::table('permission_role')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => $permissionId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 1. Eliminar asociación de permisos
        $permission = DB::table('permissions')->where('slug', 'manage_homepage')->first();
        if ($permission) {
            DB::table('permission_role')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }

        // 2. Dropear la tabla
        Schema::dropIfExists('homepage_settings');
    }
}
