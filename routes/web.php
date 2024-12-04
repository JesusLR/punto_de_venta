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
        Route::get("/ventas/ticket", "VentasController@ticket")->name("ventas.ticket");
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

        //Estadisticas
        Route::resource("estadisticas", "EstadisticasController");
        Route::post("/gridEstadisticas", "EstadisticasController@gridEstadisticas")->name("gridEstadisticas");
        Route::post("/graficaVentas", "EstadisticasController@graficaVentas")->name("graficaVentas");
    });
