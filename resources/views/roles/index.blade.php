@extends("maestra")
@section("titulo", "Roles y Permisos")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">

<div class="row">
    <div class="col-md-8">
        <div class="productos-header">
            <h1><i class="fas fa-user-shield"></i> Roles del Sistema</h1>
            <p style="margin-top: 5px; color: rgba(255,255,255,0.8);">Administra los roles disponibles en el sistema y configura los accesos correspondientes a cada uno.</p>
        </div>
        
        @include("notificacion")
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-trianglemr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="table-container">
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead style="background: #1f1f1f; color: #f7d777;">
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Rol</th>
                                <th>Slug</th>
                                <th class="text-center">Usuarios Asignados</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td><strong>{{ $role->name }}</strong></td>
                                    <td><code>{{ $role->slug }}</code></td>
                                    <td class="text-center">
                                        <span class="badge badge-info" style="font-size: 0.9rem; padding: 0.4rem 0.8rem; border-radius: 20px;">
                                            <i class="fas fa-users mr-1"></i> {{ $role->users_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-table-action" title="Editar Permisos">
                                            <i class="fa fa-key"></i> Configurar Permisos
                                        </a>
                                        @if($role->slug !== 'admin' && $role->id != 1)
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Está seguro de que desea eliminar este rol? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-table-action" title="Eliminar Rol">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-container">
            <div class="section-title">
                <i class="fas fa-plus"></i> Crear Nuevo Rol
            </div>
            
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="form-group-modern">
                    <label for="name"><i class="fas fa-tag"></i> Nombre del Rol</label>
                    <input type="text" id="name" name="name" class="form-control form-control-modern" placeholder="Ej: Supervisor, Vendedor" required autocomplete="off">
                    <small class="form-text text-muted" style="margin-top: 8px;">
                        <i class="fas fa-info-circle mr-1"></i> El nombre identificará al rol en las pantallas de administración de usuarios.
                    </small>
                </div>
                
                <button type="submit" class="btn-action btn-success-modern w-100" style="margin-top: 1.5rem; justify-content: center;">
                    <i class="fas fa-save"></i> Guardar Rol
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
