<?php

namespace App\Services;

use App\Egreso;
use App\Producto;
use App\Reparacion;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReparacionesService
{
    public function crear(array $data, $userId)
    {
        try {
            return DB::transaction(function () use ($data, $userId) {
                $reparacion = Reparacion::create([
                    'solicitud_id' => $data['solicitud_id'], 'tipo' => 'INTERNA', 'estado' => 'EN_REPARACION',
                    'nombre' => $data['nombre'] ?? null,
                    'total' => $data['total'], 'observaciones' => $data['observaciones'] ?? null, 'id_usuario' => $userId,
                ]);
                $productos = Producto::whereIn('id', array_column($data['productos'], 'producto_id'))
                    ->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                foreach ($data['productos'] as $index => $pieza) {
                    $producto = $productos->get($pieza['producto_id']);
                    if (!$producto || !$producto->lActivo || $producto->existencia < $pieza['cantidad']) {
                        throw ValidationException::withMessages([
                            'productos.'.$index.'.cantidad' => 'Existencia insuficiente o producto no disponible: '.($producto ? $producto->descripcion : $pieza['producto_id']),
                        ]);
                    }
                    $reparacion->piezas()->create([
                        'producto_id' => $producto->id, 'codigo_barras' => $producto->codigo_barras,
                        'descripcion' => $producto->descripcion, 'cantidad' => $pieza['cantidad'],
                    ]);
                    $producto->existencia -= $pieza['cantidad'];
                    $producto->save();
                }
                return $reparacion;
            }, 3);
        } catch (QueryException $exception) {
            // A retried submission must not withdraw the same pieces twice.
            $existente = Reparacion::where('solicitud_id', $data['solicitud_id'])->first();
            if ($existente && (int) $existente->id_usuario === (int) $userId) return $existente;
            if ($existente) throw ValidationException::withMessages(['solicitud_id' => 'Esta solicitud ya fue utilizada. Recarga la página.']);
            throw $exception;
        }
    }

    public function entregar($id, $userId)
    {
        return DB::transaction(function () use ($id, $userId) {
            $reparacion = Reparacion::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($reparacion->estado === 'ENTREGADO') return $reparacion;
            if ($reparacion->estado !== 'EN_REPARACION' || $reparacion->tipo !== 'INTERNA') {
                throw ValidationException::withMessages(['estado' => 'Esta reparación no se puede recibir.']);
            }
            $piezas = $reparacion->piezas()->get();
            $productos = Producto::whereIn('id', $piezas->pluck('producto_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            foreach ($piezas as $pieza) {
                $producto = $productos->get($pieza->producto_id);
                if (!$producto || $producto->existencia + $pieza->cantidad > 9999999.99) {
                    throw ValidationException::withMessages(['productos' => 'No se puede devolver el inventario de '.$pieza->descripcion.'. Revisa su existencia.']);
                }
                $producto->existencia += $pieza->cantidad;
                $producto->save();
            }
            $egreso = Egreso::create([
                'id_usuario' => $userId, 'concepto' => $reparacion->conceptoEgreso(),
                'monto' => $reparacion->total, 'fecha' => now()->toDateString(),
                'observaciones' => 'Recepción de piezas reparadas. '.$reparacion->observaciones,
            ]);
            $reparacion->update([
                'estado' => 'ENTREGADO', 'entregado_at' => now(), 'id_usuario_entrega' => $userId, 'id_egreso' => $egreso->id,
            ]);
            return $reparacion;
        }, 3);
    }

    public function cancelar($id, $userId)
    {
        return DB::transaction(function () use ($id, $userId) {
            $reparacion = Reparacion::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($reparacion->estado === 'CANCELADO') return $reparacion;
            if ($reparacion->estado !== 'EN_REPARACION' || !in_array($reparacion->tipo, ['INTERNA', 'EXTERNA'], true)) {
                throw ValidationException::withMessages(['estado' => 'Solo se puede cancelar una reparación pendiente.']);
            }
            $piezas = $reparacion->piezas()->get();
            $productos = Producto::whereIn('id', $piezas->pluck('producto_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            foreach ($piezas as $pieza) {
                $producto = $productos->get($pieza->producto_id);
                if (!$producto || $producto->existencia + $pieza->cantidad > 9999999.99) {
                    throw ValidationException::withMessages(['productos' => 'No se puede devolver el inventario de '.$pieza->descripcion.'. Revisa su existencia.']);
                }
                $producto->existencia += $pieza->cantidad;
                $producto->save();
            }
            $reparacion->update(['estado' => 'CANCELADO', 'cancelado_at' => now(), 'id_usuario_cancelacion' => $userId]);
            return $reparacion;
        }, 3);
    }
}
