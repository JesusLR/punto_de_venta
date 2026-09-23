<?php

namespace App\Http\Controllers;

use App\Services\OpenWaService;
use Illuminate\Http\Request;

class OpenWaController extends Controller
{
    private function sessionId(OpenWaService $openwa): string
    {
        return $openwa->getActiveSessionId();
    }

    public function sendText(Request $request, OpenWaService $openwa)
    {
        $data = $request->validate([
            'chatId' => ['required', 'string'],
            'text' => ['required', 'string'],
        ]);

        $activeSessionId = $this->sessionId($openwa);
        if (empty($activeSessionId)) {
            return response()->json([
                'message' => 'No hay ninguna sesión activa de WhatsApp seleccionada en la configuración.',
            ], 400);
        }

        $response = $openwa->sendText($activeSessionId, $request->chatId, $request->text);

        if (!$response) {
            return response()->json([
                'message' => 'No se pudo comunicar con el servicio de WhatsApp OpenWA.',
            ], 500);
        }

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

        $activeSessionId = $this->sessionId($openwa);
        if (empty($activeSessionId)) {
            return response()->json([
                'message' => 'No hay ninguna sesión activa de WhatsApp seleccionada en la configuración.',
            ], 400);
        }

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
            $activeSessionId,
            $data['chatId'],
            $base64,
            $mimetype,
            $filename
        );

        if (!$response) {
            return response()->json([
                'message' => 'No se pudo comunicar con el servicio de WhatsApp OpenWA.',
            ], 500);
        }

        return response()->json($response->json(), $response->status());
    }
}
