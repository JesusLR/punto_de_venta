<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class DocumentoPdf
{
    public const ABONOS = 'Historial de abonos';
    public const VENTA = 'Comprobante de pago';

    public static function nombre(string $tipo, Model $operacion): string
    {
        $operacion->loadMissing(['cliente', 'productos']);
        $cliente = self::limpiar(optional($operacion->cliente)->nombre ?: 'Sin cliente');
        $cliente = rtrim(mb_strcut($cliente, 0, 70, 'UTF-8'));
        $productos = $operacion->productos->map(function ($producto) {
            return self::limpiar($producto->descripcion ?: $producto->codigo_barras ?: '');
        })->filter()->unique()->implode(', ');
        $nombre = $tipo.' _ '.$cliente.' _ '.($productos ?: 'Sin productos');

        // OpenWA acepta hasta 200 caracteres; limitar bytes también evita nombres
        // demasiado largos en sistemas de archivos con clientes/productos acentuados.
        return rtrim(mb_strcut($nombre, 0, 196, 'UTF-8'), " .").'.pdf';
    }

    public static function mensaje(string $tipo): string
    {
        $negocio = trim((string) config('app.name')) ?: 'Joyería Colibrí Progreso';
        return mb_substr('¡Hola! Aquí tienes tu '.mb_strtolower($tipo, 'UTF-8').'. Gracias por confiar en '.$negocio.'.', 0, 1024, 'UTF-8');
    }

    private static function limpiar(string $texto): string
    {
        $texto = preg_replace('~[\\\\/:*?"<>|\x00-\x1F\x7F]~u', '-', $texto);
        return trim(preg_replace('/\s+/u', ' ', $texto), " .");
    }
}
