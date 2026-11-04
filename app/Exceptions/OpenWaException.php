<?php

namespace App\Exceptions;

class OpenWaException extends \RuntimeException
{
    public function render($request)
    {
        return response()->json([
            'success' => false, 'lSuccess' => false,
            'message' => $this->getMessage(), 'cMensaje' => $this->getMessage(),
        ], $this->getCode() ?: 503)->header('Cache-Control', 'private, no-store');
    }
}
