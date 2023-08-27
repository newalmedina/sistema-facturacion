<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminPatientMedicalStudiesPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-patients-medical-studies'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de patients
        $permissions = [
            [
                'display_name' => 'Pacientes Estudios Médicos',
                'name' => Str::slug('admin-patients-medical-studies'),
                'description' => 'Pacientes Estudios Médicos - Módulo'
            ],
            [
                'display_name' => 'Pacientes Estudios Médicos - listado',
                'name' => Str::slug('admin-patients-medical-studies-list'),
                'description' => 'Pacientes Estudios Médicos - lista'
            ],
            [
                'display_name' => 'Pacientes Estudios Médicos - crear',
                'name' => Str::slug('admin-patients-medical-studies-create'),
                'description' => 'Pacientes Estudios Médicos - crear'
            ],
            [
                'display_name' => 'Pacientes Estudios Médicos - actualizar',
                'name' => Str::slug('admin-patients-medical-studies-update'),
                'description' => 'Pacientes Estudios Médicos - actualizar'
            ],
            [
                'display_name' => 'Pacientes Estudios Médicos - actualizar todos',
                'name' => Str::slug('admin-patients-medical-studies-update-all'),
                'description' => 'Pacientes Estudios Médicos - actualizar todos'
            ],
            [
                'display_name' => 'Pacientes Estudios Médicos - ver',
                'name' => Str::slug('admin-patients-medical-studies-read'),
                'description' => 'Pacientes Estudios Médicos - ver'
            ],
            [
                'display_name' => 'Pacientes Estudios Médicos - borrar',
                'name' => Str::slug('admin-patients-medical-studies-delete'),
                'description' => 'Pacientes Estudios Médicos - borrar'
            ],
            [
                'display_name' => 'Pacientes Estudios Médicos - borrar todos',
                'name' => Str::slug('admin-patients-medical-studies-delete-all'),
                'description' => 'Pacientes Estudios Médicos - borrar Todos'
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
