<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminDashboardPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-dashboard'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de dashboard
        $permissions = [
            [
                'display_name' => 'Dashboard',
                'name' => Str::slug('admin-dashboard'),
                'description' => 'Dashboard - Módulo'
            ],

            [
                'display_name' => 'Dashboard - ver',
                'name' => Str::slug('admin-dashboard-show'),
                'description' => 'Dashboard - ver'
            ],
            //visitas
            [
                'display_name' => 'Dashboard - Visitas Hoy',
                'name' => Str::slug('admin-dashboard-appointment-today'),
                'description' => 'Dashboard - Visitas Hoy'
            ],
            [
                'display_name' => 'Dashboard - Visitas Pendientes Hoy',
                'name' => Str::slug('admin-dashboard-appointment-pending-today'),
                'description' => 'Dashboard - Visitas Pendientes Hoy'
            ],
            [
                'display_name' => 'Dashboard - Visitas Semana Actual',
                'name' => Str::slug('admin-dashboard-appointment-this-week'),
                'description' => 'Dashboard - Visitas Semana Actual'
            ],
            [
                'display_name' => 'Dashboard - Visitas Mes Actual',
                'name' => Str::slug('admin-dashboard-appointment-this-month'),
                'description' => 'Dashboard - Visitas Mes Actual'
            ],

            // pacientes
            [
                'display_name' => 'Dashboard - Número de Doctores en este centro',
                'name' => Str::slug('admin-dashboard-doctor-number-this-center'),
                'description' => 'Dashboard - Número de Doctores en este centro'
            ],
            [
                'display_name' => 'Dashboard - Número Pacientes Consultados en este centro',
                'name' => Str::slug('admin-dashboard-apatient-number-this-center'),
                'description' => 'Dashboard - Número Pacientes Consultados en este centro'
            ],
            [
                'display_name' => 'Dashboard - Recetas Prescritos',
                'name' => Str::slug('admin-dashboard-patient-medicines-number'),
                'description' => 'Dashboard - Recetas Prescritos'
            ],
            [
                'display_name' => 'Dashboard - Estudios Prescritos',
                'name' => Str::slug('admin-dashboard-patient-studies-number'),
                'description' => 'Dashboard - Estudios Prescritos'
            ],

            // pacientes
            [
                'display_name' => 'Dashboard - Resumen Ganancias',
                'name' => Str::slug('admin-dashboard-center-profits'),
                'description' => 'Dashboard - Resumen Ganancias'
            ],

            // visitas de hoy
            [
                'display_name' => 'Dashboard - Visitas Programadas Hoy Todas',
                'name' => Str::slug('admin-dashboard-appointment-programadas-today-all'),
                'description' => 'Dashboard - Visitas Programadas Hoy Todas'
            ],
            [
                'display_name' => 'Dashboard - Visitas Programadas Hoy (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-dashboard-appointment-programadas-today'),
                'description' => 'Dashboard - Visitas Programadas Hoy (Solo las Asignadas a mi como doctor)'
            ],
            [
                'display_name' => 'Dashboard - Visitas Programadas Hoy Facturar Todas',
                'name' => Str::slug('admin-dashboard-appointment-programadas-today-facturar-all'),
                'description' => 'Dashboard - Visitas Programadas Hoy Facturar Todas'
            ],
            [
                'display_name' => 'Dashboard - Visitas Programadas Hoy Facturar (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-dashboard-appointment-programadas-today-facturar'),
                'description' => 'Dashboard - Visitas Programadas Hoy Facturar (Solo las Asignadas a mi como doctor)'
            ],

            [
                'display_name' => 'Dashboard - Visitas Programadas Hoy Finalizar Todas',
                'name' => Str::slug('admin-dashboard-appointment-programadas-today-end-all'),
                'description' => 'Dashboard - Visitas Programadas Hoy Finalizar Todas'
            ],
            [
                'display_name' => 'Dashboard - Visitas Programadas Hoy Finalizar (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-dashboard-appointment-programadas-today-end'),
                'description' => 'Dashboard - Visitas Programadas Hoy Finalizar (Solo las Asignadas a mi como doctor)'
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
