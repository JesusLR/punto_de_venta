<?php
/*

  ____          _____               _ _           _       
 |  _ \        |  __ \             (_) |         | |      
 | |_) |_   _  | |__) |_ _ _ __ _____| |__  _   _| |_ ___ 
 |  _ <| | | | |  ___/ _` | '__|_  / | '_ \| | | | __/ _ \
 | |_) | |_| | | |  | (_| | |   / /| | |_) | |_| | ||  __/
 |____/ \__, | |_|   \__,_|_|  /___|_|_.__/ \__, |\__\___|
         __/ |                               __/ |        
        |___/                               |___/         
    
    Blog:       https://parzibyte.me/blog
    Ayuda:      https://parzibyte.me/blog/contrataciones-ayuda/
    Contacto:   https://parzibyte.me/blog/contacto/
    
    Copyright (c) 2020 Luis Cabrera Benito
    Licenciado bajo la licencia MIT
    
    El texto de arriba debe ser incluido en cualquier redistribucion
*/ ?>
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        "codigo_barras", 
        "descripcion", 
        "precio_compra", 
        "precio_venta", "existencia", 
        "id_proveedor", 
        "id_material", 
        "id_categoria"
    ];

    public function Proveedores() {
        return $this->belongsTo(Proveedores::class, 'id_proveedor', 'id');
    }
    public function Materiales() {
        return $this->belongsTo(Materiales::class, 'id_material', 'id');
    }
    public function Categorias() {
        return $this->belongsTo(Categorias::class, 'id_categoria', 'id');
    }

}
