<?php

namespace App\Http\Controllers;

use App\Services\OpenWaService;
use Illuminate\Http\Request;

class OpenWaController extends Controller
{
    private function sessionId(): string
    {
        return config('services.openwa.session_id') ?: 'ee45e07b-59fd-4d59-80d5-ab40977c2b2f';
    }

    public function sendText(Request $request, OpenWaService $openwa)
    {


        $data = $request->validate([
            'chatId' => ['required', 'string'],
            'text' => ['required', 'string'],
        ]);

        $response = $openwa->sendText($this->sessionId(), $request->chatId, $request->text);
    // dd($response);
        return response()->json($response->json(), $response->status());
    }

    public function sendDocument(Request $request, OpenWaService $openwa)
    {
        $data = $request->validate([
            'chatId' => ['required', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
            'base64' => ['nullable', 'string'],
            'mimetype' => ['nullable', 'string'],
            'filename' => ['nullable', 'string'],
        ]);

        $base64 = null;
        $mimetype = null;
        $filename = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $mimetype = $file->getMimeType();
            $filename = $file->getClientOriginalName();
        } elseif (!empty($data['base64']) && !empty($data['mimetype']) && !empty($data['filename'])) {
            $base64 = $data['base64'];
            $mimetype = $data['mimetype'];
            $filename = $data['filename'];
        } else {
            return response()->json([
                'message' => 'Debes enviar file o base64+mimetype+filename.',
            ], 422);
        }

        $response = $openwa->sendDocument(
            $this->sessionId(),
            $data['chatId'],
            $base64,
            $mimetype,
            $filename
        );

        return response()->json($response->json(), $response->status());
    }
}
