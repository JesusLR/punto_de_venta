# Joyería Colibrí Progreso

Sistema de punto de venta y administración para una joyería, con página pública, catálogo de piezas y gestión de ventas, inventario y apartados.

Desarrollado sobre Laravel 7 y PHP 7.4. El proyecto adapta y amplía el punto de venta original de Parzibyte.

## Funcionalidades

- Página de inicio y catálogo público con búsqueda, imágenes de las piezas y contacto por WhatsApp.
- Inventario con códigos, categorías, materiales, proveedores, precios y existencias; edición rápida e importación desde Excel.
- Ventas, clientes y tickets para impresora térmica.
- Apartados con historial de abonos, fechas de pago y saldo pendiente.
- Reparaciones internas de varias piezas, retiro temporal de existencias y recepción con egreso automático.
- Reparaciones externas para clientes o contactos manuales, imágenes opcionales, PDF y entrega con ingreso y egreso.
- Comprobantes de pago e historiales de abonos en PDF, con numeración de páginas, descarga y envío por WhatsApp.
- Finanzas con ingresos, egresos y egresos automáticos; estadísticas y exportación a Excel.
- Usuarios, roles y permisos.
- Configuración de portada, datos de contacto y parámetros generales.
- Integración con OpenWA para iniciar sesión mediante QR y seleccionar la sesión de envío del sistema.
- Interfaz adaptable a móviles y sidebar plegable en escritorio.

## Requisitos

- PHP 7.4, de acuerdo con la restricción `^7.4` de `composer.json`.
- Composer y MySQL/MariaDB.
- Extensiones de PHP requeridas por Laravel y las dependencias de PDF/Excel: PDO MySQL, Mbstring, OpenSSL, Tokenizer, XML/DOM, Ctype, JSON, Fileinfo, GD y ZIP.
- Servidor web con su raíz pública apuntando a la carpeta `public/`.
- Servicio OpenWA accesible desde el servidor para las funciones de WhatsApp.
- Node.js y npm solo si se van a recompilar los recursos de Laravel Mix.

## Instalación local

1. Instalar las dependencias:

   ```bash
   composer install
   ```

2. Crear un archivo `.env` en la raíz. Este repositorio no incluye `.env.example`. Configuración mínima de referencia:

   ```dotenv
   APP_NAME="Joyeria Colibri Progreso"
   APP_ENV=local
   APP_KEY=
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   LOG_CHANNEL=stack
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sistema_ventas
   DB_USERNAME=usuario_local
   DB_PASSWORD=tu_contrasena

   CACHE_DRIVER=file
   QUEUE_CONNECTION=sync
   SESSION_DRIVER=file
   SESSION_LIFETIME=120
   ```

3. Crear una base de datos vacía y generar la clave de la aplicación:

   ```bash
   php artisan key:generate
   ```

4. Para una **base nueva**, ejecutar:

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```

   Para una base con información existente, seguir primero el procedimiento de conciliación descrito más abajo.

### Usuario administrador

El seeder crea `administrador@colibri.com` y le asigna el rol Administrador (`admin`). Puedes definir `ADMIN_PASSWORD` en `.env` antes de ejecutarlo; si no está definido, genera una contraseña aleatoria y la muestra en consola al crear la cuenta.

Para crear o asignar únicamente ese usuario:

```bash
php artisan db:seed --class=AdministratorUserSeeder
```

Si el usuario ya existe, conserva su contraseña y le vuelve a asignar el rol administrador. El seeder del rol agrega los permisos existentes sin quitar los ya asignados.

## Migraciones y actualización del servidor

Antes de actualizar, respaldar la base de datos y revisar las migraciones pendientes:

```bash
php artisan migrate:status
```

### Bases históricas con tablas creadas manualmente

Si las tablas ya existen pero las migraciones aparecen pendientes, revisar el inventario con:

```bash
php artisan migrate:baseline-legacy
```

Este comando verifica las tablas, columnas y registros históricos contemplados en `database/legacy-schema.json`. Por defecto solo verifica. No garantiza que todos los tipos o índices coincidan ni corrige estructuras parciales.

Después de revisar el resultado y confirmar que corresponde a la base del servidor:

```bash
php artisan migrate:baseline-legacy --apply
php artisan migrate --force
```

La conciliación registra las migraciones históricas reconocidas en el lote 0 sin ejecutar sus métodos `up()`. Las migraciones nuevas se ejecutan con `migrate`.

No usar `migrate:fresh`, `migrate:refresh` ni `migrate:reset` en bases cuyos datos se deban conservar. Un rollback de migraciones históricas puede eliminar tablas o columnas. Revisar cada migración antes de aplicarla en producción.

Más detalles en [Migraciones y administrador](database/README.md).

### Despliegue habitual

Una vez conciliado el historial, después de actualizar el código:

```bash
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan migrate --force
php artisan view:clear
php artisan config:cache
```

En producción, configurar `APP_ENV=production`, `APP_DEBUG=false` y la URL real en `APP_URL`. Conservar el `APP_KEY` existente y permitir escritura al usuario del servidor web en `storage/` y `bootstrap/cache/`.

## WhatsApp / OpenWA

Configurar en `.env`:

```dotenv
OPENWA_URL=http://127.0.0.1:2785/api
OPENWA_SYSTEM_KEY=clave_exclusiva_del_sistema
```

`OPENWA_URL` debe apuntar al servicio disponible en el entorno e incluir `/api`. El identificador del sistema en esta aplicación es `punto_de_venta`.

En OpenWA debe existir una entrada para `punto_de_venta` en `INTEGRATION_SYSTEMS`. `OPENWA_SYSTEM_KEY` contiene la **clave** de esa entrada, no el nombre del sistema. La variable debe estar presente en el proceso o contenedor de OpenWA que realmente atiende las solicitudes.

Después de cambiar el entorno de Laravel:

```bash
php artisan config:clear
```

En producción, regenerar también la caché con `php artisan config:cache`.

En **Configuraciones → Inicio de sesión**, crear o iniciar una sesión, escanear el QR y seleccionarla para los envíos. El acceso requiere **Administrar WhatsApp** (`manage_whatsapp`), asignable desde Roles y Permisos. La migración de ese permiso lo asigna al rol administrador.

Las respuestas automáticas están deshabilitadas. Centro de Mensajes y WhatsApp Enlaces / QR permanecen ocultos del menú. Los envíos de PDF desde Ventas y Apartados conservan los permisos de sus respectivos módulos.

El teléfono que usa el catálogo público se configura en **Configuración Portada → Datos de Ubicación, Showroom & Contacto**. Cambiar el nombre de la aplicación no cambia el nombre del perfil de WhatsApp Business.

## Impresión y recursos

La impresión térmica utiliza `mike42/escpos-php`. Configurar `NOMBRE_IMPRESORA` según la impresora instalada; la implementación actual del ticket utiliza `WindowsPrintConnector` y requiere un entorno de impresión compatible.

Los estilos y scripts propios se encuentran en `public/css/` y `public/js/`. Para recompilar los recursos administrados por Laravel Mix:

```bash
npm install
npm run dev
# Compilación de producción:
npm run prod
```

## Pruebas

Las pruebas están configuradas con SQLite en memoria. Habilitar `pdo_sqlite` en el PHP de consola y ejecutar:

```bash
php vendor/phpunit/phpunit/phpunit
```

Pruebas específicas de integración y permisos de WhatsApp:

```bash
php vendor/phpunit/phpunit/phpunit tests/Feature/OpenWaIntegrationTest.php
php vendor/phpunit/phpunit/phpunit tests/Feature/WhatsAppPermissionTest.php
```

## Créditos

Proyecto basado en el [punto de venta de Parzibyte](https://parzibyte.me/blog/2020/03/06/sistema-ventas-laravel-bootstrap-mysql-open-source/) y su [ampliación de clientes y usuarios](https://parzibyte.me/blog/2020/03/10/laravel-pos-clientes-usuarios/).

Créditos de las imágenes originales de Flaticon:

- Productos: [Icongeek26](https://www.flaticon.com/authors/icongeek26).
- Vender, usuarios y clientes: [Freepik](https://www.flaticon.com/authors/freepik).
- Ventas: [Eucalyp](https://www.flaticon.com/authors/eucalyp).
- Soporte: [dDara](https://www.flaticon.com/authors/ddara).
- Acerca de: [inipagistudio](https://www.flaticon.com/authors/inipagistudio).
