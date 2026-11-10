# Migraciones y administrador

## Reparaciones internas

La migración `2026_11_09_000001_create_reparaciones_internas` agrega las tablas
`reparaciones` y `reparacion_productos`, y los permisos `ver_reparaciones` y
`manage_reparaciones`, asignados inicialmente al rol administrador. Se aplica con
`php artisan migrate --force` después de respaldar la base y revisar los pendientes.

En **Tienda → Reparaciones**, registrar una reparación interna descuenta las
unidades seleccionadas de la existencia disponible. Productos muestra por separado
cuántas unidades están en reparación; el catálogo público conserva su filtro de
existencia mayor que cero. Al marcar **Entregado** se devuelven las unidades y se
crea un único egreso por el total capturado, fechado el día de la recepción.
Ambos movimientos se realizan en una transacción. Los egresos vinculados no se
pueden eliminar desde Finanzas. La reparación conserva código y descripción de
las piezas al momento del envío para su historial.

El filtro inicial muestra las reparaciones pendientes. Se pueden cancelar mientras
estén en reparación: las unidades regresan al inventario sin generar un egreso.
El nombre se puede cambiar y se sincroniza con el concepto del egreso si ya se recibió.
La recepción de reparaciones internas es completa. No se
incluyen entregas parciales ni cambios al
total después de registrar. La migración inicial impide su reversión si existen registros,
para evitar perder el seguimiento del inventario y del gasto.

## Reparaciones externas

El permiso **Ver reparaciones de todos los usuarios** (`ver_todas_reparaciones`)
se agrega con `2026_11_10_000004_add_view_all_reparaciones_permission` y se asigna
al administrador. Sin él, cada usuario solo consulta y administra las reparaciones
que registró, siempre sujeto a sus permisos de consulta y administración. La
restricción también protege PDF, imágenes y acciones accedidas por URL.
Con ese permiso se habilita el filtro **Registrado por**, con un usuario específico
o todos. Los filtros de estado y usuario y la paginación actualizan solo el grid
por AJAX y conservan la selección en la URL.

Las migraciones `2026_11_10_000001_add_reparaciones_externas` y
`2026_11_10_000002_link_external_repairs_to_clientes` habilitan el flujo externo.
Se utiliza el catálogo de **Clientes** (con copia del nombre y teléfono), o se
captura un contacto manual dentro de la reparación sin crear cuentas de acceso.
La columna anterior `contacto_usuario_id` se conserva por compatibilidad con la
primera migración, pero las altas actuales utilizan `contacto_cliente_id`.

El alta requiere observaciones y admite hasta 10 imágenes opcionales de **Antes**
y 10 de **Después**, en JPG/PNG, de hasta 2 MB y 4000 × 4000 píxeles cada una.
La migración `2026_11_10_000003_add_etapa_to_reparacion_imagenes` conserva las
fotos anteriores clasificándolas como Antes. En edición se pueden agregar fotos,
quitar las existentes y modificar las observaciones, sin alterar importes ni estado.
Se almacenan en el disco privado
`local`, bajo `storage/app/reparaciones`, y se consultan mediante rutas con permiso
`ver_reparaciones`. Es necesario que `storage/` sea escribible; los límites de
carga de PHP y del servidor web también se aplican.

Al marcar **Entregado**, se capturan precio de compra y precio de venta: se crean
un egreso y un ingreso con el nombre de la reparación, dentro de una transacción.
Repetir la entrega no duplica movimientos. Una reparación externa no modifica
existencias del inventario y se puede cancelar mientras esté pendiente, sin gastos
ni ingresos. Renombrarla actualiza ambos conceptos; los movimientos vinculados
no se pueden borrar desde Finanzas.

El PDF está disponible desde las acciones del listado e incluye contacto,
observaciones y el precio de venta si ya fue entregada. Antes de abrirlo se elige
si incluir imágenes; se muestra directamente en una pestaña como en Apartados.
Las fotografías van en un anexo organizado por etapa. Las imágenes del detalle se
amplían en un modal y permiten regresar al detalle sin abrir otra pestaña.
El costo interno no aparece en el PDF. Se conserva la numeración de páginas.

## Esquema histórico

Las migraciones incluyen los catálogos `categorias`, `materiales`, `proveedores`
y `precios_materiales`, además de las columnas manuales encontradas en la base local.
Las tablas OAuth se crean mediante las migraciones incluidas en Laravel Passport.
Se conservan los tipos, índices y relaciones definidos por las migraciones existentes;
no se convierten los tipos de las tablas históricas ni se renombran sus claves.

## Base nueva

1. Configurar la conexión de la base vacía en `.env`.
2. Opcionalmente definir `ADMIN_PASSWORD` con la contraseña inicial deseada.
3. Ejecutar `composer dump-autoload` y `php artisan migrate --seed`.

El usuario es `administrador@colibri.com` y el rol es `admin` (Administrador).
Si no se configura `ADMIN_PASSWORD`, el seeder genera una contraseña aleatoria
que muestra en la consola al crear la cuenta. Las ejecuciones posteriores conservan
la contraseña existente y vuelven a asignar el rol administrador sin duplicar registros.

## Base existente

Para crear o asignar únicamente el administrador:

```sh
php artisan db:seed --class=AdministratorUserSeeder
```

Si las tablas historicas ya existen, primero hacer un respaldo y verificar:

```sh
php artisan migrate:baseline-legacy
```

Este comando usa el inventario fijo `database/legacy-schema.json`. Verifica la
presencia de tablas, columnas y los roles/permisos indicados; no exige igualdad
de tipos, claves o indices porque la estructura historica difiere de las migraciones.
Si falta algo, falla sin registrar ninguna migracion. No resuelve estructuras parciales.
Las migraciones nuevas que no esten en ese inventario no se registran automaticamente.

Solo despues de revisar el resultado y la estructura del servidor:

```sh
php artisan migrate:baseline-legacy --apply
php artisan migrate --force
```

La conciliacion solo inserta filas en `migrations` con lote 0 y conserva los datos,
roles y configuraciones existentes. No ejecuta los metodos `up()` historicos.
No se deben revertir esas migraciones adoptadas: sus metodos `down()` eliminan
tablas/columnas. No usar `migrate:fresh`, `migrate:refresh` ni `migrate:reset` con
datos que se quieran conservar. Revisar el alcance antes de cualquier rollback.

La conciliacion ya se aplico a la base local y se verifico que las otras 29 tablas
conservaran exactamente sus datos y estructura. Produccion se revisa por separado.
