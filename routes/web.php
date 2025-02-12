<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route("home");
});
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
        Route::resource("ventas", "VentasController");
        Route::get("/vender", "VenderController@index")->name("vender.index");
        Route::post("/productoDeVenta", "VenderController@agregarProductoVenta")->name("agregarProductoVenta");
        Route::delete("/productoDeVenta", "VenderController@quitarProductoDeVenta")->name("quitarProductoDeVenta");
        Route::post("/terminarOCancelarVenta", "VenderController@terminarOCancelarVenta")->name("terminarOCancelarVenta");

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
    });
