<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminPatientsPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-patients'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de patients
        $permissions = [
            [
                'display_name' => 'Pacientes',
                'name' => Str::slug('admin-patients'),
                'description' => 'Pacientes - Módulo'
            ],
            [
                'display_name' => 'Pacientes - listado',
                'name' => Str::slug('admin-patients-list'),
                'description' => 'Pacientes - lista'
            ],
            [
                'display_name' => 'Pacientes - crear',
                'name' => Str::slug('admin-patients-create'),
                'description' => 'Pacientes - crear'
            ],
            [
                'display_name' => 'Pacientes - actualizar',
                'name' => Str::slug('admin-patients-update'),
                'description' => 'Pacientes - actualizar'
            ],
            [
                'display_name' => 'Pacientes - actualizar todos',
                'name' => Str::slug('admin-patients-update-all'),
                'description' => 'Pacientes - actualizar todos'
            ],
            [
                'display_name' => 'Pacientes - ver',
                'name' => Str::slug('admin-patients-read'),
                'description' => 'Pacientes - ver'
            ],
            [
                'display_name' => 'Pacientes - borrar',
                'name' => Str::slug('admin-patients-delete'),
                'description' => 'Pacientes - borrar'
            ],
            [
                'display_name' => 'Pacientes historial médico- actualizar',
                'name' => Str::slug('admin-patients-clinic-record-update'),
                'description' => 'Pacientes - actualizar'
            ],
            [
                'display_name' => 'Pacientes historial médico- ver',
                'name' => Str::slug('admin-patients-clinic-record-read'),
                'description' => 'Pacientes - actualizar'
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
