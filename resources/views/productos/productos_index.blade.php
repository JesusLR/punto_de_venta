{{-- filepath: c:\laragon\www\punto_de_venta\resources\views\productos\productos_index.blade.php --}}
@extends("maestra")
@section("titulo", "Productos")
@section("contenido")

<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="productos-header">
            <h1>
                <i class="fas fa-box-open"></i>
                Gestión de Productos
            </h1>
            <div class="header-actions">
                <a href="{{route("productos.create")}}" class="btn-modern btn-success-modern">
                    <i class="fas fa-plus-circle"></i>
                    Nuevo Producto
                </a>
                <button type="button" class="btn-modern btn-excel" id="btnExportExcel">
                    <i class="fas fa-file-excel"></i>
                    Exportar Excel
                </button>
                <button type="button" class="btn-modern btn-pdf" id="btnExportPDF">
                    <i class="fas fa-file-pdf"></i>
                    Exportar PDF
                </button>
                <button type="button" class="btn-modern btn-excel" id="btnCargaExcell">
                    <i class="fas fa-upload"></i>
                    Importar Excel
                </button>
            </div>
        </div>

        @include("notificacion")

        <!-- Filtros -->
        <div class="filters-container">
            <div class="filters-title">
                <i class="fas fa-filter"></i>
                Filtros de Búsqueda
            </div>
            <div class="row">
                @if(Auth::user()->id == 1)
                <div class="col-md-3">
                    <div class="form-group-modern">
                        <label><i class="fas fa-search"></i>Buscar por:</label>
                        <select required class="form-control" name="cTipoBusquedaProductos" id="cTipoBusquedaProductos">
                                <option value="T">Todos</option>
                                <option value="F">Sin foto</option>
                                <option value="B">Blancos</option>
                                <option value="R">Rojo</option>
                                <option value="N">Naranja</option>
                            </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group-modern">
                        <label><i class="fas fa-truck"></i>Proveedor:</label>
                        <select class="form-control-modern" id="cTipoBusquedaProveedor">
                            <option value="T">Todos</option>
                            <option value="0">N/A</option>
                            @foreach($lstProveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">{{ $proveedor->cNombreProveedor }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group-modern">
                        <label><i class="fas fa-gem"></i>Material:</label>
                        <select class="form-control-modern" id="cTipoBusquedaMaterial">
                            <option value="T">Todos</option>
                            <option value="0">N/A</option>
                            @foreach($lstMateriales as $material)
                                <option value="{{ $material->id }}">{{ $material->cNombreMaterial }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group-modern">
                        <label><i class="fas fa-folder"></i>Categoría:</label>
                        <select class="form-control-modern" id="cTipoBusquedaCategoria">
                            <option value="T">Todos</option>
                            <option value="0">N/A</option>
                            @foreach($lstCategorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->cNombreCategoria }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla -->
            <div class="table-container">
                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="table table-hover" id="gridProductos"
                        data-show-export="true"
                        data-show-footer="true"
                        data-url="https://examples.wenzhixin.net.cn/examples/bootstrap_table/data">
                        </table>
                    </div>
                </div>
            </div>
    </div>
</div>

<!-- Modal Carga Excel -->
<div class="modal fade" id="cargaExcellModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-excel"></i> Importar Productos desde Excel
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group-modern">
                    <label><i class="fas fa-upload"></i>Seleccionar Archivo Excel</label>
                    <input type="file" class="form-control-modern" id="fileExcellProductos" accept=".xlsx,.xls">
                    <small class="form-text text-muted">Formato aceptado: .xlsx, .xls</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn-modern btn-success-modern" id="btnGuardarInfoExcell">
                    <i class="fas fa-check"></i> Importar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Imagen -->
<div class="modal fade" id="imagenProductoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloImagenModal"></h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="divImagenProductoModal">
                <!-- Imagen aquí -->
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="userID" value="{{Auth::user()->id}}">

<script src="{{ asset('js/productos.js') }}"></script>

@endsection