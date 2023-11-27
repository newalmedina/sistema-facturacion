<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminCentersPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-centers'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de centers
        $permissions = [
            [
                'display_name' => 'Centros',
                'name' => Str::slug('admin-centers'),
                'description' => 'Centros - Módulo'
            ],
            [
                'display_name' => 'Centros - listado',
                'name' => Str::slug('admin-centers-list'),
                'description' => 'Centros - lista'
            ],
            [
                'display_name' => 'Centros - crear',
                'name' => Str::slug('admin-centers-create'),
                'description' => 'Centros - crear'
            ],
            [
                'display_name' => 'Centros - actualizar',
                'name' => Str::slug('admin-centers-update'),
                'description' => 'Centros - actualizar'
            ],
            [
                'display_name' => 'Centros - borrar',
                'name' => Str::slug('admin-centers-delete'),
                'description' => 'Centros - borrar'
            ],
            [
                'display_name' => 'Centros - ver',
                'name' => Str::slug('admin-centers-read'),
                'description' => 'Centros - ver'
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
