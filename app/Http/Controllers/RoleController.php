<?php

namespace App\Http\Controllers;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_roles');
    }

    /**
     * List all roles.
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Store a new role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        try {
            $role = new Role();
            $role->name = $request->name;
            $role->slug = Str::slug($request->name);
            $role->save();

            return redirect()->route('roles.index')->with('mensaje', 'Rol creado exitosamente.');
        } catch (Exception $ex) {
            return redirect()->route('roles.index')->with('error', 'Error al crear el rol: ' . $ex->getMessage());
        }
    }

    /**
     * Edit role permissions.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent editing permissions of the superadmin role if needed, or allow it but keep it safe.
        // For security, ID = 1 is hardcoded to bypass checks anyway, so editing it in DB is fine.
        
        $permissions = Permission::all();
        
        // Group permissions by category/component for clean UX
        $groupedPermissions = [];
        foreach ($permissions as $p) {
            $parts = explode('_', $p->slug);
            // Default group name
            $groupName = 'Otros';
            
            if (count($parts) > 1) {
                $comp = $parts[1];
                switch ($comp) {
                    case 'categories':
                    case 'categorias':
                        $groupName = 'Categorías';
                        break;
                    case 'clients':
                    case 'clientes':
                        $groupName = 'Clientes';
                        break;
                    case 'materials':
                    case 'materiales':
                        $groupName = 'Materiales';
                        break;
                    case 'suppliers':
                    case 'proveedores':
                        $groupName = 'Proveedores';
                        break;
                    case 'users':
                    case 'usuarios':
                        $groupName = 'Usuarios';
                        break;
                    case 'products':
                    case 'productos':
                    case 'utility':
                        $groupName = 'Productos';
                        break;
                    case 'apartados':
                        $groupName = 'Apartados';
                        break;
                    case 'sales':
                    case 'ventas':
                        $groupName = 'Ventas / Tienda';
                        break;
                    case 'finances':
                    case 'egresos':
                        $groupName = 'Finanzas / Egresos';
                        break;
                    case 'statistics':
                        $groupName = 'Estadísticas';
                        break;
                    case 'roles':
                        $groupName = 'Roles y Permisos';
                        break;
                    case 'homepage':
                        $groupName = 'Página de Inicio';
                        break;
                }
            } else {
                if ($p->slug === 'make_sales') {
                    $groupName = 'Ventas / Tienda';
                }
            }
            
            $groupedPermissions[$groupName][] = $p;
        }

        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissionIds'));
    }

    /**
     * Update role permissions.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        if ($role->slug === 'admin' && $id == 1) {
            // Admin role must retain manage_roles permission for safety
            $manageRolesPermission = Permission::where('slug', 'manage_roles')->first();
            if ($manageRolesPermission && !in_array($manageRolesPermission->id, $request->input('permissions', []))) {
                return redirect()->back()->with('error', 'El Administrador principal debe conservar el permiso para administrar roles.');
            }
        }

        try {
            $permissionIds = $request->input('permissions', []);
            $role->permissions()->sync($permissionIds);

            return redirect()->route('roles.index')->with('mensaje', 'Permisos del rol actualizados exitosamente.');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Error al actualizar permisos: ' . $ex->getMessage());
        }
    }

    /**
     * Delete a role.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->slug === 'admin' || $role->id == 1) {
            return redirect()->route('roles.index')->with('error', 'El rol de Administrador principal no puede ser eliminado.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
        }

        try {
            $role->permissions()->detach();
            $role->delete();

            return redirect()->route('roles.index')->with('mensaje', 'Rol eliminado exitosamente.');
        } catch (Exception $ex) {
            return redirect()->route('roles.index')->with('error', 'Error al eliminar el rol: ' . $ex->getMessage());
        }
    }
}
