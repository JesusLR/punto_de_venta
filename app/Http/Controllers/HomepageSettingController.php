<?php

namespace App\Http\Controllers;

use App\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomepageSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_homepage');
    }

    /**
     * Mostrar formulario de configuración de la portada.
     */
    public function index()
    {
        // Obtener todos los settings como array llave => valor
        $settings = HomepageSetting::pluck('value', 'key')->toArray();

        // Valores por defecto si no existen
        $gallery = json_decode($settings['gallery_images'] ?? '[]', true);
        $gallery = is_array($gallery) ? $gallery : [];
        $team = json_decode($settings['team_members'] ?? '[]', true);
        $team = is_array($team) ? $team : [];
        $pills = isset($settings['hero_pills']) ? json_decode($settings['hero_pills'], true) : [];

        // Rellenar galería hasta 12 elementos si es menor
        if (count($gallery) < 12) {
            $gallery = array_pad($gallery, 12, '');
        }

        // Rellenar equipo hasta 6 elementos si es menor
        if (count($team) < 6) {
            $team = array_pad($team, 6, [
                'name' => '',
                'role' => '',
                'image' => ''
            ]);
        }

        return view('homepage_settings.index', compact('settings', 'gallery', 'team', 'pills'));
    }

    /**
     * Guardar la configuración de la portada.
     */
    public function update(Request $request)
    {
        $rules = [
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string',
            'hero_pills' => 'nullable|string',
            'story_title' => 'required|string|max:255',
            'story_text' => 'required|string',
            'mission_title' => 'required|string|max:255',
            'mission_text' => 'required|string',
            'materials_title' => 'required|string|max:255',
            'materials_text' => 'required|string',
            'store_address' => 'required|string|max:500',
            'store_hours' => 'required|string|max:255',
            'maps_iframe' => 'nullable|string',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:50',
            'gallery_count' => 'required|integer|min:0|max:12',
            'team_count' => 'required|integer|min:0|max:6',
            'team_name' => 'nullable|array|max:6',
            'team_role' => 'nullable|array|max:6',
            'team_name.*' => 'nullable|string|max:255',
            'team_role.*' => 'nullable|string|max:255',
        ];
        foreach (['gallery_image' => 12, 'team_image' => 6] as $prefix => $count) {
            for ($i = 0; $i < $count; $i++) $rules[$prefix.'_'.$i] = 'nullable|image|mimes:jpeg,jpg,png,gif,webp,bmp|max:5120';
        }
        $request->validate($rules);
        $nuevas = []; $anteriores = [];
        try {
            DB::beginTransaction();
            // 1. Guardar textos simples y conteos
            HomepageSetting::setValue('hero_title', $request->hero_title);
            HomepageSetting::setValue('hero_subtitle', $request->hero_subtitle);
            HomepageSetting::setValue('story_title', $request->story_title);
            HomepageSetting::setValue('story_text', $request->story_text);
            HomepageSetting::setValue('mission_title', $request->mission_title);
            HomepageSetting::setValue('mission_text', $request->mission_text);
            HomepageSetting::setValue('materials_title', $request->materials_title);
            HomepageSetting::setValue('materials_text', $request->materials_text);
            HomepageSetting::setValue('store_address', $request->store_address);
            HomepageSetting::setValue('store_hours', $request->store_hours);
            HomepageSetting::setValue('maps_iframe', $request->maps_iframe);
            HomepageSetting::setValue('contact_email', $request->contact_email);
            HomepageSetting::setValue('contact_phone', $request->contact_phone);
            HomepageSetting::setValue('gallery_count', $request->gallery_count);
            HomepageSetting::setValue('team_count', $request->team_count);

            // Procesar tags (pills) del hero
            if ($request->filled('hero_pills')) {
                $pillsArray = array_map('trim', explode(',', $request->hero_pills));
                // Filtrar elementos vacíos
                $pillsArray = array_filter($pillsArray);
                HomepageSetting::setValue('hero_pills', json_encode(array_values($pillsArray)));
            } else {
                HomepageSetting::setValue('hero_pills', json_encode([]));
            }

            // 2. Procesar imágenes de la galería (hasta 12)
            $currentGalleryJson = HomepageSetting::getValue('gallery_images', '[]');
            $galleryImages = json_decode($currentGalleryJson, true);
            if (!is_array($galleryImages)) {
                $galleryImages = array_fill(0, 12, '');
            }
            if (count($galleryImages) < 12) {
                $galleryImages = array_pad($galleryImages, 12, '');
            }

            for ($i = 0; $i < 12; $i++) {
                if ($request->hasFile("gallery_image_{$i}")) {
                    $file = $request->file("gallery_image_{$i}");
                    $name = $this->guardarImagen($file, 'gallery', $nuevas);
                    $oldImage = $galleryImages[$i] ?? '';
                    if ($oldImage && basename($oldImage) === $oldImage && strpos($oldImage, 'gallery_') === 0) $anteriores[] = 'about_gallery/'.$oldImage;

                    $galleryImages[$i] = $name;
                }
            }
            HomepageSetting::setValue('gallery_images', json_encode(array_values($galleryImages)));

            // 3. Procesar equipo de trabajo (hasta 6)
            $currentTeamJson = HomepageSetting::getValue('team_members', '[]');
            $teamMembers = json_decode($currentTeamJson, true);
            if (!is_array($teamMembers)) {
                $teamMembers = [];
            }
            if (count($teamMembers) < 6) {
                $teamMembers = array_pad($teamMembers, 6, [
                    'name' => '',
                    'role' => '',
                    'image' => ''
                ]);
            }

            $newTeam = [];
            $names = $request->input('team_name', []);
            $roles = $request->input('team_role', []);

            for ($i = 0; $i < 6; $i++) {
                $nameVal = $names[$i] ?? '';
                $roleVal = $roles[$i] ?? '';
                $imageVal = $teamMembers[$i]['image'] ?? '';

                if ($request->hasFile("team_image_{$i}")) {
                    $file = $request->file("team_image_{$i}");
                    $imgName = $this->guardarImagen($file, 'team', $nuevas);
                    if ($imageVal && basename($imageVal) === $imageVal && strpos($imageVal, 'team_') === 0) $anteriores[] = 'about_gallery/'.$imageVal;

                    $imageVal = $imgName;
                }

                $newTeam[] = [
                    'name' => $nameVal,
                    'role' => $roleVal,
                    'image' => $imageVal
                ];
            }
            HomepageSetting::setValue('team_members', json_encode($newTeam));

            DB::commit();
            foreach ($anteriores as $ruta) {
                try { Storage::disk('public')->delete($ruta); } catch (\Throwable $cleanup) { report($cleanup); }
            }
            return redirect()->route('homepage.settings.index')->with('mensaje', 'Configuración de la portada guardada correctamente.');

        } catch (\Throwable $ex) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            foreach ($nuevas as $ruta) {
                try { Storage::disk('public')->delete($ruta); } catch (\Throwable $cleanup) { report($cleanup); }
            }
            report($ex);
            return redirect()->route('homepage.settings.index')->with('error', 'Error al guardar la configuración: ' . $ex->getMessage());
        }
    }
    private function guardarImagen($file, $prefix, array &$nuevas)
    {
        $nombre = $prefix.'_'.Str::uuid().'.'.$file->extension();
        $ruta = $file->storeAs('about_gallery', $nombre, 'public');
        if (!$ruta) throw new \RuntimeException('No se pudo guardar la imagen. Revisa los permisos de storage/app/public en el servidor.');
        $nuevas[] = $ruta;
        if (!Storage::disk('public')->exists($ruta)) throw new \RuntimeException('No se pudo verificar la imagen guardada. Revisa el almacenamiento del servidor.');
        return $nombre;
    }

}
