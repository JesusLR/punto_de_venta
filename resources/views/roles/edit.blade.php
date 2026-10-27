@extends("maestra")
@section("titulo", "Editar Permisos de Rol")
@section("contenido")
<link rel="stylesheet" href="{{ asset('css/productos-styles.css') }}">
<style>
    .permission-group-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
        border: 1px solid #eef2f5;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .permission-group-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .permission-group-header {
        background: #f8fafc;
        padding: 1rem 1.25rem;
        font-weight: 700;
        color: #1e293b;
        border-bottom: 2px solid #D4AF37;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .permission-group-body {
        padding: 1.25rem;
    }
    .permission-checkbox-container {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.6rem;
        border-radius: 8px;
        transition: background-color 0.2s ease;
        cursor: pointer;
    }
    .permission-checkbox-container:hover {
        background-color: #f1f5f9;
    }
    .permission-checkbox {
        margin-top: 0.25rem;
        width: 18px;
        height: 18px;
        accent-color: #D4AF37;
        cursor: pointer;
    }
    .permission-details {
        display: flex;
        flex-direction: column;
    }
    .permission-title {
        font-weight: 600;
        color: #334155;
        font-size: 0.9rem;
    }
    .permission-desc {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 0.2rem;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="productos-header">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1><i class="fas fa-key"></i> Permisos del Rol: {{ $role->name }}</h1>
                    <p style="margin-top: 5px; color: rgba(255,255,255,0.8);">Selecciona las secciones y acciones a las que este rol tendrá acceso.</p>
                </div>
                <div>
                    <a class="btn-action btn-secondary" href="{{ route('roles.index') }}" style="margin: 0;"><i class="fas fa-arrow-left"></i> Volver a Roles</a>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                @foreach($groupedPermissions as $groupName => $permissions)
                    <div class="col-md-6 col-lg-4">
                        <div class="permission-group-card">
                            <div class="permission-group-header">
                                <i class="fas fa-folder-open" style="color: #D4AF37;"></i> {{ $groupName }}
                            </div>
                            <div class="permission-group-body">
                                @foreach($permissions as $p)
                                    <label class="permission-checkbox-container">
                                        <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="permission-checkbox"
                                               {{ in_array($p->id, $rolePermissionIds) ? 'checked' : '' }}
                                               {{ ($role->slug === 'admin' && $p->slug === 'manage_roles') ? 'onclick="return false;"' : '' }}
                                        >
                                        <div class="permission-details">
                                            <span class="permission-title">{{ $p->name }}</span>
                                            <span class="permission-desc">{{ $p->description }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="form-container" style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
                <a class="btn-action btn-secondary" href="{{ route('roles.index') }}"><i class="fas fa-times"></i> Cancelar</a>
                <button type="submit" class="btn-action btn-success-modern"><i class="fas fa-save"></i> Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
