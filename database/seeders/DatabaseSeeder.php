<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call(UserSeeder::class);
        $this->call(RolesSeeders::class);
        $this->call(PermissionSeeder::class);
        $this->call(AdminDashboardPremissionSeeder::class);
        $this->call(AdminSettingsPremissionSeeder::class);
        $this->call(AdminUsersPremissionSeeder::class);
        $this->call(AdminRolesPremissionSeeder::class);
        $this->call(AdminMunicipiosPremissionSeeder::class);
        $this->call(AdminProvincesPremissionSeeder::class);
        $this->call(AdminServicesPremissionSeeder::class);
        $this->call(AdminDiagnosisPremissionSeeder::class);
        $this->call(AdminInsuranceCarriersPremissionSeeder::class);
        $this->call(AdminClinicPersonalPremissionSeeder::class);
        $this->call(AdminCentersPremissionSeeder::class);
        $this->call(AdminMedicalSpecializationsPremissionSeeder::class);


        $this->call(InsertProvinceMunicipDataSeeder::class);
        $this->call(InsertCenterDataSeeder::class);
        $this->call(GeneralSettingSeeders::class);
        $this->call(MedicalSpecializationDataSeeder::class);
        $this->call(DiagnosisDataSeeder::class);
        $this->call(InsuranceCarriersDataSeeder::class);
        $this->call(AdminPatientsPremissionSeeder::class);
        $this->call(AdminPatientsInsurancesPremissionSeeder::class);
        $this->call(AdminPatientMedicinesPremissionSeeder::class);
        $this->call(AdminPatientMedicalStudiesPremissionSeeder::class);
        $this->call(AdminPatientMonitoringPremissionSeeder::class);
        $this->call(AdminAppointmentPremissionSeeder::class);
    }
}
