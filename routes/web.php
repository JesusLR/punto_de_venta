<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return redirect()->route("inicio");
});
// Route::get('/', function () {
//     return redirect()->route("home");
// });
Route::get("/acerca-de", function () {
    return view("misc.acerca_de");
})->name("acerca_de.index");
Route::get("/soporte", function(){
    return redirect("https://parzibyte.me/blog/contrataciones-ayuda/");
})->name("soporte.index");

Auth::routes([
    "reset" => false,// no pueden olvidar contraseña
]);

        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/inicio', 'HomeController@inicio')->name('inicio');
        // Permitir logout con petición get
        Route::get("/logout", function () {
            Auth::logout();
            return redirect()->route("home");
        })->name("logout");


        Route::middleware("auth")
            ->group(function () {
                Route::resource("clientes", "ClientesController");
                Route::resource("usuarios", "UserController")->parameters(["usuarios" => "user"]);
                Route::resource("productos", "ProductosController");
                Route::get("/ventas/ticket/{id}", "VentasController@ticket")->name("ventas.ticket");
                Route::get("/ventas/pdf/{id}", "VentasController@pdf")->name("ventas.pdf");
                Route::resource("ventas", "VentasController");
                Route::get("/vender", "VenderController@index")->name("vender.index");
                Route::post("/vender/cliente-rapido", "VenderController@crearClienteRapido")->name("vender.crearClienteRapido");
                Route::post("/productoDeVenta", "VenderController@agregarProductoVenta")->name("agregarProductoVenta");
                Route::delete("/productoDeVenta", "VenderController@quitarProductoDeVenta")->name("quitarProductoDeVenta");
                Route::post("/precioProductoVenta", "VenderController@actualizarPrecioProductoVenta")->name("actualizarPrecioProductoVenta");
                Route::post("/terminarOCancelarVenta", "VenderController@terminarOCancelarVenta")->name("terminarOCancelarVenta");

                // Finanzas
                Route::get('/finanzas', 'FinanzasController@index')->name('finanzas.index');
                Route::get('/finanzas/excel', 'FinanzasController@exportExcel')->name('finanzas.excel');
                Route::post('/finanzas/egresos', 'FinanzasController@store')->name('finanzas.store');
                Route::delete('/finanzas/egresos/{id}', 'FinanzasController@destroy')->name('finanzas.destroy');

                // Apartados
                Route::get("/apartados", "ApartadosController@index")->name("apartados.index");
                Route::post("/apartados/abonar", "ApartadosController@abonar")->name("apartados.abonar");
                Route::post("/apartados/eliminar-abono", "ApartadosController@eliminarAbono")->name("apartados.eliminarAbono");
                Route::post("/apartados/editar-fecha-abono", "ApartadosController@editarFechaAbono")->name("apartados.editarFechaAbono");
                Route::post("/apartados/agregar-producto", "ApartadosController@agregarProducto")->name("apartados.agregarProducto");
                Route::post("/apartados/cancelar", "ApartadosController@cancelar")->name("apartados.cancelar");
                Route::post("/apartados/ejecutar", "ApartadosController@ejecutar")->name("apartados.ejecutar");
                Route::get("/apartados/pdf/{id}", "ApartadosController@pdf")->name("apartados.pdf");
                Route::get("/apartados/ver-productos/{id}", "ApartadosController@verProductos")->name("apartados.verProductos");
                Route::get("/apartados/ver-abonos/{id}", "ApartadosController@verAbonos")->name("apartados.verAbonos");
                Route::get("/apartados/detalle/{id}", "ApartadosController@detalle")->name("apartados.detalle");
                Route::post("/gridApartados/gridApartados", "ApartadosController@gridApartados")->name("gridApartados");
                Route::post("/apartados/cambiarNombre", "ApartadosController@cambiarNombre")->name("apartados.cambiarNombre");

                //Productos
                Route::post("/gridProductos", "ProductosController@gridProductos")->name("gridProductos");
                Route::get("/editarProducto/{id}", "ProductosController@editarProducto")->name("editarProducto");
                Route::post("/deleteProducto", "ProductosController@deleteProducto")->name("deleteProducto");
                Route::get("/getProductos", "ProductosController@getProductos")->name("getProductos");
                Route::post("/cargarProductosExcell", "ProductosController@cargarProductosExcell")->name("cargarProductosExcell");

                //Ventas
                Route::post("/gridVentas", "VentasController@gridVentas")->name("gridVentas");
                Route::post("/saveNombreVenta", "VentasController@saveNombreVenta")->name("saveNombreVenta");
                

                //Estadisticas
                Route::resource("estadisticas", "EstadisticasController");
                Route::post("/gridEstadisticas", "EstadisticasController@gridEstadisticas")->name("gridEstadisticas");
                Route::post("/graficaVentas", "EstadisticasController@graficaVentas")->name("graficaVentas");
                Route::post("/estadisticas/productos-filtro", "EstadisticasController@productosVendidosFiltro")->name("estadisticas.productosFiltro");

                //Proveedores
                Route::resource("proveedores", "ProveedoresController");
                Route::post("/gridProveedores", "ProveedoresController@gridProveedores")->name("gridProveedores");
                Route::post("/saveProveedores", "ProveedoresController@saveProveedores")->name("saveProveedores");
                Route::post("/deleteProveedor", "ProveedoresController@deleteProveedor")->name("deleteProveedor");
                Route::get("/getProveedor/{id}", "ProveedoresController@getProveedor")->name("getProveedor");

                //Categorias
                Route::resource("categorias", "CategoriasController");
                Route::post("/gridCategorias", "CategoriasController@gridCategorias")->name("gridCategorias");
                Route::post("/saveCategorias", "CategoriasController@saveCategorias")->name("saveCategorias");
                Route::post("/deleteCategoria", "CategoriasController@deleteCategoria")->name("deleteCategoria");
                Route::get("/getCategoria/{id}", "CategoriasController@getCategoria")->name("getCategoria");

                //Materiales
                Route::resource("materiales", "MaterialesController");
                Route::post("/gridMateriales", "MaterialesController@gridMateriales")->name("gridMateriales");
                Route::post("/saveMateriales", "MaterialesController@saveMateriales")->name("saveMateriales");
                Route::post("/deleteMaterial", "MaterialesController@deleteMaterial")->name("deleteMaterial");
                Route::get("/getMaterial/{id}", "MaterialesController@getMaterial")->name("getMaterial");

                Route::post('/about/upload-image', [AboutController::class, 'uploadImage'])->name('about.upload.image');
                Route::get('/gold-price', [HomeController::class, 'goldPrice'])->name('gold.price');
            });
