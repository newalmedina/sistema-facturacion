<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminMedicalSpecializationsPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-medical-specializations'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de medical-specializations
        $permissions = [
            [
                'display_name' => 'Especializaciones médicas',
                'name' => Str::slug('admin-medical-specializations'),
                'description' => 'Especializaciones médicas - Módulo'
            ],
            [
                'display_name' => 'Especializaciones médicas - listado',
                'name' => Str::slug('admin-medical-specializations-list'),
                'description' => 'Especializaciones médicas - lista'
            ],
            [
                'display_name' => 'Especializaciones médicas - crear',
                'name' => Str::slug('admin-medical-specializations-create'),
                'description' => 'Especializaciones médicas - crear'
            ],
            [
                'display_name' => 'Especializaciones médicas - actualizar',
                'name' => Str::slug('admin-medical-specializations-update'),
                'description' => 'Especializaciones médicas - actualizar'
            ],
            [
                'display_name' => 'Especializaciones médicas - borrar',
                'name' => Str::slug('admin-medical-specializations-delete'),
                'description' => 'Especializaciones médicas - borrar'
            ],
            [
                'display_name' => 'Especializaciones médicas - ver',
                'name' => Str::slug('admin-medical-specializations-read'),
                'description' => 'Especializaciones médicas - ver'
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
