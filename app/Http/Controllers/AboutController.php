<?php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    // ...existing code...

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120'
        ]);

        $file = $request->file('image');
        $name = 'about_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/about_gallery', $name);

        $url = asset('storage/about_gallery/' . $name);

        return response()->json(['url' => $url, 'name' => $name]);
    }

    // ... code...
}