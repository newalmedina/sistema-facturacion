<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminClinicPersonalPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-clinic-personal'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de clinic-personal
        $permissions = [
            [
                'display_name' => 'Personal clínico',
                'name' => Str::slug('admin-clinic-personal'),
                'description' => 'Personal clínico - Módulo'
            ],
            [
                'display_name' => 'Personal clínico - listado',
                'name' => Str::slug('admin-clinic-personal-list'),
                'description' => 'Personal clínico - lista'
            ],
            [
                'display_name' => 'Personal clínico - actualizar',
                'name' => Str::slug('admin-clinic-personal-update'),
                'description' => 'Personal clínico - actualizar'
            ],
            [
                'display_name' => 'Personal clínico - ver',
                'name' => Str::slug('admin-clinic-personal-read'),
                'description' => 'Personal clínico - ver'
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
