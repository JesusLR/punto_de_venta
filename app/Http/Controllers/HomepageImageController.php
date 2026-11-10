<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class HomepageImageController extends Controller
{
    public function show($archivo)
    {
        abort_unless(preg_match('/^(gallery|team)_[a-z0-9_.-]+\.(jpe?g|png|gif|webp|bmp)$/iD', $archivo), 404);
        $ruta = 'about_gallery/'.$archivo;
        abort_unless(Storage::disk('public')->exists($ruta), 404);
        return response()->file(Storage::disk('public')->path($ruta), [
            'Cache-Control' => 'public, max-age=86400', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
