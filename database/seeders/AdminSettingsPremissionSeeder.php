<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminSettingsPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-settings'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de settings
        $permissions = [
            [
                'display_name' => 'Configuración',
                'name' => Str::slug('admin-settings'),
                'description' => 'Configuración - Módulo'
            ],

            [
                'display_name' => 'Configuración - ver',
                'name' => Str::slug('admin-settings-show'),
                'description' => 'Configuración - ver'
            ],
            [
                'display_name' => 'Configuración - actualizar',
                'name' => Str::slug('admin-settings-update'),
                'description' => 'Configuración - actualizar'
            ]

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
