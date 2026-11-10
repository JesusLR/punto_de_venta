<?php

namespace App\Services;

use App\Egreso;
use App\Ingreso;
use App\Reparacion;
use App\Cliente;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReparacionesExternasService
{
    private function existente(array $data, $actor)
    {
        $reparacion = Reparacion::where('solicitud_id', $data['solicitud_id'])->first();
        if ($reparacion && ((int) $reparacion->id_usuario !== (int) $actor || $reparacion->tipo !== 'EXTERNA')) {
            throw ValidationException::withMessages(['solicitud_id' => 'Esta solicitud ya fue utilizada. Recarga la página.']);
        }
        return $reparacion;
    }

    public function crear(array $data, array $imagenes, $actor)
    {
        if ($existente = $this->existente($data, $actor)) return $existente;
        $rutas = [];
        try {
            return DB::transaction(function () use ($data, $imagenes, $actor, &$rutas) {
                $cliente = $data['contacto_origen'] === 'catalogo' ? Cliente::findOrFail($data['contacto_cliente_id']) : null;
                $reparacion = Reparacion::create([
                    'solicitud_id' => $data['solicitud_id'], 'tipo' => 'EXTERNA', 'estado' => 'EN_REPARACION', 'total' => 0,
                    'nombre' => $data['nombre'] ?? null, 'id_usuario' => $actor,
                    'contacto_cliente_id' => $cliente ? $cliente->id : null,
                    'contacto_nombre' => $cliente ? $cliente->nombre : $data['contacto_nombre'],
                    'contacto_telefono' => $data['contacto_telefono'] ?? ($cliente ? preg_replace('/\D/', '', $cliente->telefono) : null), 'observaciones' => $data['observaciones'],
                ]);
                $this->guardarImagenes($reparacion, $imagenes, $rutas);
                return $reparacion;
            });
        } catch (\Throwable $exception) {
            foreach ($rutas as $ruta) Storage::disk('local')->delete($ruta);
            if ($exception instanceof QueryException && ($existente = $this->existente($data, $actor))) return $existente;
            throw $exception;
        }
    }

    private function guardarImagenes(Reparacion $reparacion, array $grupos, array &$rutas)
    {
        foreach (['ANTES', 'DESPUES'] as $etapa) {
            $archivos = $grupos[$etapa] ?? [];
            if ($reparacion->imagenes()->where('etapa', $etapa)->count() + count($archivos) > 10) {
                throw ValidationException::withMessages(['imagenes' => 'Solo se permiten 10 imágenes de antes y 10 de después.']);
            }
            foreach ($archivos as $imagen) {
                $ruta = $imagen->store('reparaciones', 'local');
                if (!$ruta) throw new \RuntimeException('No se pudo guardar la imagen de la reparación.');
                $rutas[] = $ruta;
                $reparacion->imagenes()->create(['ruta' => $ruta, 'etapa' => $etapa]);
            }
        }
    }

    public function actualizar($id, array $data, array $imagenes)
    {
        $nuevas = []; $eliminadas = [];
        try {
            $reparacion = DB::transaction(function () use ($id, $data, $imagenes, &$nuevas, &$eliminadas) {
                $reparacion = Reparacion::whereKey($id)->lockForUpdate()->firstOrFail();
                if ($reparacion->tipo !== 'EXTERNA') throw ValidationException::withMessages(['tipo' => 'Solo se puede editar una reparación externa.']);
                $ids = array_unique($data['eliminar_imagenes'] ?? []);
                $imagenesQuitar = $reparacion->imagenes()->whereIn('id', $ids)->get();
                if ($imagenesQuitar->count() !== count($ids)) throw ValidationException::withMessages(['eliminar_imagenes' => 'Una imagen no pertenece a esta reparación. Recarga la página.']);
                foreach ($imagenesQuitar as $imagen) { $eliminadas[] = $imagen->ruta; $imagen->delete(); }
                $this->guardarImagenes($reparacion, $imagenes, $nuevas);
                $cambios = ['observaciones' => $data['observaciones']];
                if (array_key_exists('nombre', $data)) $cambios['nombre'] = $data['nombre'];
                $reparacion->update($cambios);
                if ($reparacion->id_egreso) Egreso::whereKey($reparacion->id_egreso)->update(['concepto' => $reparacion->conceptoEgreso()]);
                if ($reparacion->id_ingreso) Ingreso::whereKey($reparacion->id_ingreso)->update(['concepto' => $reparacion->conceptoEgreso()]);
                return $reparacion;
            });
        } catch (\Throwable $exception) {
            foreach ($nuevas as $ruta) Storage::disk('local')->delete($ruta);
            throw $exception;
        }
        foreach ($eliminadas as $ruta) Storage::disk('local')->delete($ruta);
        return $reparacion;
    }

    public function entregar($id, array $precios, $actor)
    {
        return DB::transaction(function () use ($id, $precios, $actor) {
            $reparacion = Reparacion::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($reparacion->tipo !== 'EXTERNA') throw ValidationException::withMessages(['tipo' => 'No es una reparación externa.']);
            if ($reparacion->estado === 'ENTREGADO') return $reparacion;
            if ($reparacion->estado !== 'EN_REPARACION') throw ValidationException::withMessages(['estado' => 'Solo se pueden entregar reparaciones pendientes.']);
            $movimiento = ['id_usuario' => $actor, 'concepto' => $reparacion->conceptoEgreso(), 'fecha' => now()->toDateString(),
                'observaciones' => 'Reparación externa #'.$reparacion->id.' · '.$reparacion->contacto_nombre];
            $egreso = Egreso::create($movimiento + ['monto' => $precios['precio_compra']]);
            $ingreso = Ingreso::create($movimiento + ['monto' => $precios['precio_venta']]);
            $reparacion->update(['estado' => 'ENTREGADO', 'total' => $precios['precio_compra'],
                'precio_compra' => $precios['precio_compra'], 'precio_venta' => $precios['precio_venta'],
                'id_egreso' => $egreso->id, 'id_ingreso' => $ingreso->id,
                'id_usuario_entrega' => $actor, 'entregado_at' => now()]);
            return $reparacion;
        }, 3);
    }
}
