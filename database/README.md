# Migraciones y administrador

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
