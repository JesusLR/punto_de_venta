# WhatsApp de Colibrí / punto_de_venta

La integración utiliza `/api/integrations/sessions` de OpenWA y una credencial
Bearer exclusiva registrada bajo `punto_de_venta` en `INTEGRATION_SYSTEMS`.
No se usa la clave general `OPENWA_API_KEY` ni la URL antigua de `homepage_settings`.
Las sesiones anteriores sin `sourceSystem=punto_de_venta` no se muestran ni se usan;
se debe crear y vincular una sesión nueva desde Inicio de sesión.

## Local

En `.env` de este proyecto:

```dotenv
OPENWA_URL=http://127.0.0.1:2785/api
OPENWA_SYSTEM_KEY=credencial-exclusiva-de-punto_de_venta
```

La API local escucha en 2785; el panel de desarrollo puede usar 2886.
La credencial local ya se generó y registró en ambos `.env` sin publicarla.
Se reinició únicamente la API local para cargarla.

## Producción

1. Respaldar los archivos de configuración y la base antes del despliegue.
2. Generar una credencial aleatoria de al menos 32 caracteres y agregarla como
   `punto_de_venta` al JSON de `INTEGRATION_SYSTEMS` en OpenWA. Conservar las
   entradas existentes, incluida `de_viaje_en_viaje`; cada sistema usa una clave distinta.
3. En el `.env` de Colibrí configurar exactamente la misma clave para ese entorno:

```dotenv
OPENWA_URL=http://74.208.53.13:2886/api
OPENWA_SYSTEM_KEY=misma-credencial-registrada-para-punto_de_venta
```

4. Reiniciar OpenWA mediante el administrador de procesos usado en el servidor.
5. Ejecutar `php artisan config:clear` en Colibrí (o regenerar `config:cache` si
   el despliegue utiliza caché de configuración).
6. Revisar `php artisan migrate:status` y conciliar el historial histórico si es
   necesario, según `database/README.md`. La migración nueva
   `2026_11_04_000002_disable_whatsapp_automatic_replies` únicamente establece
   `wa_bot_enabled=0`; no borra tablas ni registros. Puede ejecutarse por separado:

```sh
php artisan migrate --path=database/migrations/2026_11_04_000002_disable_whatsapp_automatic_replies.php --force
```

7. Abrir Inicio de sesión, crear la sesión, escanear el QR y seleccionarla para
   envíos. Después usar los botones existentes en ventas y apartados, confirmando
   el teléfono antes de enviar documentos reales.

No copiar una clave al repositorio ni sustituir todo el JSON por un único sistema.
El registro y reinicio de producción no se realizaron desde este equipo.

## Comportamiento

- El servidor valida `sourceSystem` en las sesiones y usa las rutas de integración,
  que también restringen el acceso a las sesiones de cada credencial.
- Los PDF se envían directamente al servicio; no hay llamadas HTTP del POS a sí mismo.
- Se requiere una sesión `ready` con motor activo. Los números mexicanos se envían
  con prefijo `52`, sin el antiguo `1` adicional.
- Solo se confirma éxito cuando OpenWA devuelve `status=sent`. No hay reintentos
  automáticos; si un envío queda sin confirmación, revisar el chat antes de repetirlo.
- Centro de Mensajes, Enlaces/QR y los parámetros automáticos se ocultaron. Las
  respuestas automáticas quedan desactivadas también en el servidor. Las rutas
  de envío genéricas anteriores requieren autenticación API y permiso de configuración.
- Las pruebas usan SQLite en memoria y HTTP simulado; no envían mensajes reales.
