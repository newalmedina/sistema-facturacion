<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminInsuranceCarriersPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-insurance-carriers'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de insurance-carriers
        $permissions = [
            [
                'display_name' => 'Aseguradoras',
                'name' => Str::slug('admin-insurance-carriers'),
                'description' => 'Aseguradoras - Módulo'
            ],
            [
                'display_name' => 'Aseguradoras - listado',
                'name' => Str::slug('admin-insurance-carriers-list'),
                'description' => 'Aseguradoras - lista'
            ],
            [
                'display_name' => 'Aseguradoras - crear',
                'name' => Str::slug('admin-insurance-carriers-create'),
                'description' => 'Aseguradoras - crear'
            ],
            [
                'display_name' => 'Aseguradoras - actualizar',
                'name' => Str::slug('admin-insurance-carriers-update'),
                'description' => 'Aseguradoras - actualizar'
            ],
            [
                'display_name' => 'Aseguradoras - borrar',
                'name' => Str::slug('admin-insurance-carriers-delete'),
                'description' => 'Aseguradoras - borrar'
            ],
            [
                'display_name' => 'Aseguradoras - ver',
                'name' => Str::slug('admin-insurance-carriers-read'),
                'description' => 'Aseguradoras - ver'
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
