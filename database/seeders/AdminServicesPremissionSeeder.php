<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminServicesPremissionSeeder  extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();

        // Si los permisos los hemos creados volvemos
        $permExists = Permission::where('name', Str::slug('admin-services'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de services
        $permissions = [
            [
                'display_name' => 'Servicios',
                'name' => Str::slug('admin-services'),
                'description' => 'Servicios - Módulo'
            ],
            [
                'display_name' => 'Servicios - listado',
                'name' => Str::slug('admin-services-list'),
                'description' => 'Servicios - lista'
            ],
            [
                'display_name' => 'Servicios - crear',
                'name' => Str::slug('admin-services-create'),
                'description' => 'Servicios - crear'
            ],
            [
                'display_name' => 'Servicios - actualizar',
                'name' => Str::slug('admin-services-update'),
                'description' => 'Servicios - actualizar'
            ],
            [
                'display_name' => 'Servicios - borrar',
                'name' => Str::slug('admin-services-delete'),
                'description' => 'Servicios - borrar'
            ],
            [
                'display_name' => 'Servicios - ver',
                'name' => Str::slug('admin-services-read'),
                'description' => 'Servicios - ver'
            ],

        ];
        $MenuChild = $this->insertPermissions($permissions, $this->childAdmin, $this->a_permission_admin);

        // Rol de administrador
        $roleAdmin = Role::where("name", "=", Str::slug('admin'))->first();
        if (!empty($this->a_permission_admin)) {
            $roleAdmin->attachPermissions($this->a_permission_admin);
        }
        $roleUser = Role::where("name", "=", Str::slug('usuario-front'))->first();
        if (!empty($this->a_permission_front)) {
            $roleUser->attachPermissions($this->a_permission_front);
        }
    }
}
