<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminPatientMedicinesPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-patients-medicines'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de patients
        $permissions = [
            [
                'display_name' => 'Pacientes Medicamentos',
                'name' => Str::slug('admin-patients-medicines'),
                'description' => 'Pacientes Medicamentos - Módulo'
            ],
            [
                'display_name' => 'Pacientes Medicamentos - listado',
                'name' => Str::slug('admin-patients-medicines-list'),
                'description' => 'Pacientes Medicamentos - lista'
            ],
            [
                'display_name' => 'Pacientes Medicamentos - crear',
                'name' => Str::slug('admin-patients-medicines-create'),
                'description' => 'Pacientes Medicamentos - crear'
            ],
            [
                'display_name' => 'Pacientes Medicamentos - actualizar',
                'name' => Str::slug('admin-patients-medicines-update'),
                'description' => 'Pacientes Medicamentos - actualizar'
            ],
            [
                'display_name' => 'Pacientes Medicamentos - actualizar todos',
                'name' => Str::slug('admin-patients-medicines-update-all'),
                'description' => 'Pacientes Medicamentos - actualizar todos'
            ],
            [
                'display_name' => 'Pacientes Medicamentos - ver',
                'name' => Str::slug('admin-patients-medicines-read'),
                'description' => 'Pacientes Medicamentos - ver'
            ],
            [
                'display_name' => 'Pacientes Medicamentos - borrar',
                'name' => Str::slug('admin-patients-medicines-delete'),
                'description' => 'Pacientes Medicamentos - borrar'
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
