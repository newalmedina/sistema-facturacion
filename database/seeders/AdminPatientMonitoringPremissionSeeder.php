<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminPatientMonitoringPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-patients-monitoring'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de patients
        $permissions = [
            [
                'display_name' => 'Pacientes Estudios Seguimientos',
                'name' => Str::slug('admin-patients-monitoring'),
                'description' => 'Pacientes Estudios Seguimientos - Módulo'
            ],
            [
                'display_name' => 'Pacientes Estudios Seguimientos - listado',
                'name' => Str::slug('admin-patients-monitoring-list'),
                'description' => 'Pacientes Estudios Seguimientos - lista'
            ],
            [
                'display_name' => 'Pacientes Estudios Seguimientos - crear',
                'name' => Str::slug('admin-patients-monitoring-create'),
                'description' => 'Pacientes Estudios Seguimientos - crear'
            ],
            [
                'display_name' => 'Pacientes Estudios Seguimientos - actualizar',
                'name' => Str::slug('admin-patients-monitoring-update'),
                'description' => 'Pacientes Estudios Seguimientos - actualizar'
            ],
            [
                'display_name' => 'Pacientes Estudios Seguimientos - actualizar todos',
                'name' => Str::slug('admin-patients-monitoring-update-all'),
                'description' => 'Pacientes Estudios Seguimientos - actualizar todos'
            ],
            [
                'display_name' => 'Pacientes Estudios Seguimientos - ver',
                'name' => Str::slug('admin-patients-monitoring-read'),
                'description' => 'Pacientes Estudios Seguimientos - ver'
            ],
            [
                'display_name' => 'Pacientes Estudios Seguimientos - borrar',
                'name' => Str::slug('admin-patients-monitoring-delete'),
                'description' => 'Pacientes Estudios Seguimientos - borrar'
            ],
            [
                'display_name' => 'Pacientes Estudios Seguimientos - borrar todos',
                'name' => Str::slug('admin-patients-monitoring-delete-all'),
                'description' => 'Pacientes Estudios Seguimientos - borrar Todos'
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
