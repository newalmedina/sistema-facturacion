<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminCenterController;
use App\Http\Controllers\AdminClinicPersonalController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDiagnosiController;
use App\Http\Controllers\AdminInsuranceCarrierController;
use App\Http\Controllers\AdminMedicalSpecializationController;
use App\Http\Controllers\AdminMunicipioController;
use App\Http\Controllers\AdminPatientController;
use App\Http\Controllers\AdminPatientMedicalStudieController;
use App\Http\Controllers\AdminPatientMedicineController;
use App\Http\Controllers\AdminPatientMonitoringController;
use App\Http\Controllers\AdminProvinceController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminSuplantacionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminUserProfileController;
use App\Http\Controllers\AdminAppointmentController;
use App\Http\Controllers\Auth\FrontRegisterUserController;
use App\Http\Controllers\AdminCalendarController;
use App\Http\Controllers\FrontSettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocalizationController;
use App\Http\Middleware\AvailableSite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** begin -- de autenticacion */
// Rutas de autenticación generadas por Auth::routes()
Route::middleware([AvailableSite::class])->group(function () {
    Auth::routes(['verify' => true]);
});

// Route::post('/register', [FrontRegisterUserController::class, 'create'])
//     ->middleware('guest');
Route::get('/register/verify/{confirmation_code}', [FrontRegisterUserController::class, 'verify'])
    ->middleware('guest');
/** end -- de autenticacion */

// Route::group(array('prefix' => ''), function () {
//     // Route::get('/', [HomeController::class, 'index']);
//     // Route::get('/home', [HomeController::class, 'index'])->name('home');
//     //   Route::resource('alarms', 'FrontAlarmsController');
// });

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
//change language
Route::get('lang/{locale}', [LocalizationController::class, 'index']);

Route::get('home', function () {
    return redirect()->route('admin.dashboard');
});


//General Routes
Route::group(array('prefix' => 'front', 'middleware' => []), function () {

    Route::get('/settings/get-image/{image}', [FrontSettingsController::class, 'getImage'])->name("front.settings-get-image");
});
Route::group(array('prefix' => 'admin', 'middleware' => ['auth', 'verified', 'check.active', 'avaible.site']), function () {

    Route::get('/profile/personal-info', [AdminUserProfileController::class, 'personalInfo']);
    Route::post('/profile/personal-info/update', [AdminUserProfileController::class, 'updatePersonalInfo'])->name("admin.updateProfilePersonalInfo");
    Route::get('/profile/clinic-training', [AdminUserProfileController::class, 'clinicTraining']);
    Route::post('/profile/clinic-training/update', [AdminUserProfileController::class, 'updateClinicTraining'])->name("admin.updateProfileClinicTraining");
    Route::get('/profile/getphoto/{photo}', [AdminUserProfileController::class, 'getPhoto'])->name("admin.getPhoto");

    Route::get('/settings/get-image/{image}', [AdminSettingsController::class, 'getImage'])->name("admin.settings-get-image");

    Route::get('/municipios/municipios-list/{id?}', [AdminMunicipioController::class, 'getMunicipioListByProvince']);
});


Route::group(array('prefix' => 'admin', 'middleware' => ['auth', 'verified', 'check.active', 'profile.complete', 'avaible.site'/* , 'selected.center' */]), function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name("admin.dashboard");

    Route::get('/settings', [AdminSettingsController::class, 'index'])->name("admin.settings");
    Route::patch('/settings', [AdminSettingsController::class, 'update'])->name("admin.settings.update");
    Route::delete('/settings/delete-image/{image}', [AdminSettingsController::class, 'deleteImage'])->name("admin.settings.deleteImage");

    //Admin Profile
    Route::get('/profile', [AdminUserProfileController::class, 'edit']);

    Route::delete('/profile/delete-image/{id}', [AdminUserProfileController::class, 'deleteImage'])->name("admin.profile.deleteImage");
    Route::post('/profile/store', [AdminUserProfileController::class, 'store'])->name("admin.updateProfile");


    //Admin Roles
    Route::get('/roles', [AdminRoleController::class, 'index']);
    Route::post('/roles/list', [AdminRoleController::class, 'getData'])->name('admin.roles.getData');
    Route::get('/roles/create', [AdminRoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [AdminRoleController::class, 'store'])->name('admin.roles.store');
    Route::delete('/roles/{id}', [AdminRoleController::class, 'destroy'])->name('admin.roles.destroy');
    Route::get('/roles/{id}/edit', [AdminRoleController::class, 'edit'])->name('admin.roles.edit');
    Route::patch('/roles/{id}', [AdminRoleController::class, 'update'])->name('admin.roles.update');
    Route::get('/roles/permissions/{id}', [AdminRoleController::class, 'editPermissions'])->name('admin.roles.editPermissions');
    Route::patch('/roles/permissions/{id}', [AdminRoleController::class, 'updatePermissions'])->name('admin.permissions.update');
    Route::get('/roles/change-state/{id}', [AdminRoleController::class, 'changeState'])->name('admin.roles.changeState');
    //suplanta identidad
    Route::get('/suplantar/{id}', [AdminSuplantacionController::class, 'suplantar'])->name('admin.suplantar');
    Route::get('/suplantar', [AdminSuplantacionController::class, 'revertir'])->name('admin.revertirSuplnatar');
    //admin users

    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::get('/users/{id}/show', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('/users/change-state/{id}', [AdminUserController::class, 'changeState'])->name('admin.users.changeState');
    Route::patch('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::post('/users/list', [AdminUserController::class, 'getData'])->name('admin.users.getData');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');


    Route::post('/users/save-filter', [AdminUserController::class, 'saveFilter'])->name('admin.users.saveFilter');
    Route::get('/users/remove-filter', [AdminUserController::class, 'removeFilter'])->name('admin.users.removeFilter');

    Route::get('/users/roles/{id}', [AdminUserController::class, 'editRoles'])->name('admin.users.editRoles');
    Route::get('/users/roles/{id}/show', [AdminUserController::class, 'showRoles'])->name('admin.users.showRoles');
    Route::patch('/users/roles/{id}', [AdminUserController::class, 'updateRoles'])->name('admin.users.updateRoles');
    Route::get('/users/centers/{id}', [AdminUserController::class, 'editCenters'])->name('admin.users.editCenters');
    Route::get('/users/centers/{id}/show', [AdminUserController::class, 'showCenters'])->name('admin.users.showCenters');
    Route::patch('/users/centers/{id}', [AdminUserController::class, 'updateCenters'])->name('admin.users.updateCenters');

    Route::get('/users/personal-info/{id}', [AdminUserController::class, 'personalInfo']);
    Route::get('/users/personal-info/{id}/show', [AdminUserController::class, 'ShowPersonalInfo']);
    Route::post('/users/personal-info/store/{id}', [AdminUserController::class, 'updatePersonalInfo'])->name("admin.users.updatePersonalInfo");
    //admin centers
    Route::get('/centers', [AdminCenterController::class, 'index']);
    Route::get('/centers/create', [AdminCenterController::class, 'create'])->name('admin.centers.create');
    Route::get('/centers/{id}/edit', [AdminCenterController::class, 'edit'])->name('admin.centers.edit');
    Route::get('/centers/{id}/show', [AdminCenterController::class, 'show'])->name('admin.centers.show');
    Route::get('/centers/change-state/{id}', [AdminCenterController::class, 'changeState'])->name('admin.centers.changeState');
    Route::patch('/centers/{id}', [AdminCenterController::class, 'update'])->name('admin.centers.update');
    Route::get('/centers/remove-filter', [AdminCenterController::class, 'removeFilter'])->name('admin.centers.removeFilter');
    Route::post('/centers/change-center', [AdminCenterController::class, 'changeCenter'])->name('admin.centers.changeCenterUpdate');
    Route::post('/centers', [AdminCenterController::class, 'store'])->name('admin.centers.store');
    Route::post('/centers/save-filter', [AdminCenterController::class, 'saveFilter'])->name('admin.centers.saveFilter');
    Route::post('/centers/list', [AdminCenterController::class, 'getData'])->name('admin.centers.getData');
    Route::delete('/centers/{id}', [AdminCenterController::class, 'destroy'])->name('admin.centers.destroy');

    Route::get('/centers/aditional-info/{id}', [AdminCenterController::class, 'editAditionalInfo'])->name('admin.centers.editAditionalInfo');
    Route::get('/centers/aditional-info/{id}/show', [AdminCenterController::class, 'showAditionalInfo'])->name('admin.centers.showAditionalInfo');
    Route::patch('/centers/aditional-info/{id}', [AdminCenterController::class, 'updateAditionalInfo'])->name('admin.centers.updateAditionalInfo');
    Route::get('/centers/get-image/{photo}', [AdminCenterController::class, 'getimage'])->name("admin.centers.getimage");
    Route::get('/centers/export-excel', [AdminCenterController::class, 'exportExcel'])->name("admin.centers.exportExcel");
    Route::delete('/centers/delete-image/{photo}', [AdminCenterController::class, 'deleteImage'])->name("admin.centers.deleteImage");

    //admin municipios

    Route::get('/municipios', [AdminMunicipioController::class, 'index']);
    Route::get('/municipios/create', [AdminMunicipioController::class, 'create'])->name('admin.municipios.create');
    Route::get('/municipios/{id}/edit', [AdminMunicipioController::class, 'edit'])->name('admin.municipios.edit');
    Route::get('/municipios/{id}/show', [AdminMunicipioController::class, 'show'])->name('admin.municipios.show');
    Route::get('/municipios/change-state/{id}', [AdminMunicipioController::class, 'changeState'])->name('admin.municipios.changeState');
    Route::get('/municipios/export-excel', [AdminMunicipioController::class, 'exportExcel'])->name("admin.municipios.exportExcel");
    Route::get('/municipios/remove-filter', [AdminMunicipioController::class, 'removeFilter'])->name('admin.municipios.removeFilter');
    Route::patch('/municipios/{id}', [AdminMunicipioController::class, 'update'])->name('admin.municipios.update');
    Route::post('/municipios/save-filter', [AdminMunicipioController::class, 'saveFilter'])->name('admin.municipios.saveFilter');
    Route::post('/municipios', [AdminMunicipioController::class, 'store'])->name('admin.municipios.store');
    Route::post('/municipios/save-filter', [AdminMunicipioController::class, 'saveFilter'])->name('admin.municipios.saveFilter');
    Route::post('/municipios/list', [AdminMunicipioController::class, 'getData'])->name('admin.municipios.getData');
    Route::delete('/municipios/{id}', [AdminMunicipioController::class, 'destroy'])->name('admin.municipios.destroy');

    //admin provincias
    Route::get('/provinces', [AdminProvinceController::class, 'index']);
    Route::get('/provinces/create', [AdminProvinceController::class, 'create'])->name('admin.provinces.create');
    Route::get('/provinces/{id}/edit', [AdminProvinceController::class, 'edit'])->name('admin.provinces.edit');
    Route::get('/provinces/{id}/show', [AdminProvinceController::class, 'show'])->name('admin.provinces.show');
    Route::get('/provinces/change-state/{id}', [AdminProvinceController::class, 'changeState'])->name('admin.provinces.changeState');
    Route::get('/provinces/export-excel', [AdminProvinceController::class, 'exportExcel'])->name("admin.provinces.exportExcel");
    Route::patch('/provinces/{id}', [AdminProvinceController::class, 'update'])->name('admin.provinces.update');
    Route::post('/provinces', [AdminProvinceController::class, 'store'])->name('admin.provinces.store');
    Route::post('/provinces/save-filter', [AdminProvinceController::class, 'saveFilter'])->name('admin.provinces.saveFilter');
    Route::post('/provinces/list', [AdminProvinceController::class, 'getData'])->name('admin.provinces.getData');
    Route::delete('/provinces/{id}', [AdminProvinceController::class, 'destroy'])->name('admin.provinces.destroy');

    //admin especializaciones medicas
    Route::get('/medical-specializations', [AdminMedicalSpecializationController::class, 'index']);
    Route::get('/medical-specializations/create', [AdminMedicalSpecializationController::class, 'create'])->name('admin.medical-specializations.create');
    Route::get('/medical-specializations/{id}/edit', [AdminMedicalSpecializationController::class, 'edit'])->name('admin.medical-specializations.edit');
    Route::get('/medical-specializations/{id}/show', [AdminMedicalSpecializationController::class, 'show'])->name('admin.medical-specializations.show');
    Route::get('/medical-specializations/change-state/{id}', [AdminMedicalSpecializationController::class, 'changeState'])->name('admin.medical-specializations.changeState');
    Route::get('/medical-specializations/export-excel', [AdminMedicalSpecializationController::class, 'exportExcel'])->name("admin.medical-specializations.exportExcel");
    Route::patch('/medical-specializations/{id}', [AdminMedicalSpecializationController::class, 'update'])->name('admin.medical-specializations.update');
    Route::post('/medical-specializations', [AdminMedicalSpecializationController::class, 'store'])->name('admin.medical-specializations.store');
    Route::post('/medical-specializations/save-filter', [AdminMedicalSpecializationController::class, 'saveFilter'])->name('admin.medical-specializations.saveFilter');
    Route::post('/medical-specializations/list', [AdminMedicalSpecializationController::class, 'getData'])->name('admin.medical-specializations.getData');
    Route::delete('/medical-specializations/{id}', [AdminMedicalSpecializationController::class, 'destroy'])->name('admin.medical-specializations.destroy');

    //admin diagnosis
    Route::get('/diagnosis', [AdminDiagnosiController::class, 'index']);
    Route::get('/diagnosis/create', [AdminDiagnosiController::class, 'create'])->name('admin.diagnosis.create');
    Route::get('/diagnosis/{id}/edit', [AdminDiagnosiController::class, 'edit'])->name('admin.diagnosis.edit');
    Route::get('/diagnosis/{id}/show', [AdminDiagnosiController::class, 'show'])->name('admin.diagnosis.show');
    Route::get('/diagnosis/change-state/{id}', [AdminDiagnosiController::class, 'changeState'])->name('admin.diagnosis.changeState');
    Route::get('/diagnosis/export-excel', [AdminDiagnosiController::class, 'exportExcel'])->name("admin.diagnosis.exportExcel");
    Route::patch('/diagnosis/{id}', [AdminDiagnosiController::class, 'update'])->name('admin.diagnosis.update');
    Route::post('/diagnosis', [AdminDiagnosiController::class, 'store'])->name('admin.diagnosis.store');
    Route::post('/diagnosis/save-filter', [AdminDiagnosiController::class, 'saveFilter'])->name('admin.diagnosis.saveFilter');
    Route::post('/diagnosis/list', [AdminDiagnosiController::class, 'getData'])->name('admin.diagnosis.getData');
    Route::delete('/diagnosis/{id}', [AdminDiagnosiController::class, 'destroy'])->name('admin.diagnosis.destroy');

    //admin insurance-carriers
    Route::get('/insurance-carriers', [AdminInsuranceCarrierController::class, 'index']);
    Route::get('/insurance-carriers/create', [AdminInsuranceCarrierController::class, 'create'])->name('admin.insurance-carriers.create');
    Route::get('/insurance-carriers/{id}/edit', [AdminInsuranceCarrierController::class, 'edit'])->name('admin.insurance-carriers.edit');
    Route::get('/insurance-carriers/{id}/show', [AdminInsuranceCarrierController::class, 'show'])->name('admin.insurance-carriers.show');
    Route::get('/insurance-carriers/change-state/{id}', [AdminInsuranceCarrierController::class, 'changeState'])->name('admin.insurance-carriers.changeState');
    Route::get('/insurance-carriers/remove-filter', [AdminInsuranceCarrierController::class, 'removeFilter'])->name('admin.insurance-carriers.removeFilter');
    Route::patch('/insurance-carriers/{id}', [AdminInsuranceCarrierController::class, 'update'])->name('admin.insurance-carriers.update');
    Route::post('/insurance-carriers', [AdminInsuranceCarrierController::class, 'store'])->name('admin.insurance-carriers.store');
    Route::post('/insurance-carriers/save-filter', [AdminInsuranceCarrierController::class, 'saveFilter'])->name('admin.insurance-carriers.saveFilter');
    Route::post('/insurance-carriers/list', [AdminInsuranceCarrierController::class, 'getData'])->name('admin.insurance-carriers.getData');
    Route::delete('/insurance-carriers/{id}', [AdminInsuranceCarrierController::class, 'destroy'])->name('admin.insurance-carriers.destroy');
    Route::get('/insurance-carriers/get-image/{photo}', [AdminInsuranceCarrierController::class, 'getimage'])->name("admin.insurance-carriers.getimage");
    Route::get('/insurance-carriers/export-excel', [AdminInsuranceCarrierController::class, 'exportExcel'])->name("admin.insurance-carriers.exportExcel");
    Route::delete('/insurance-carriers/delete-image/{photo}', [AdminInsuranceCarrierController::class, 'deleteImage'])->name("admin.insurance-carriers.deleteImage");



    //admin services
    Route::get('/services', [AdminServiceController::class, 'index']);
    Route::get('/services/create', [AdminServiceController::class, 'create'])->name('admin.services.create');
    Route::get('/services/{id}/edit', [AdminServiceController::class, 'edit'])->name('admin.services.edit');
    Route::get('/services/{id}/show', [AdminServiceController::class, 'show'])->name('admin.services.show');
    Route::get('/services/change-state/{id}', [AdminServiceController::class, 'changeState'])->name('admin.services.changeState');
    Route::patch('/services/{id}', [AdminServiceController::class, 'update'])->name('admin.services.update');
    Route::post('/services', [AdminServiceController::class, 'store'])->name('admin.services.store');
    Route::post('/services/list', [AdminServiceController::class, 'getData'])->name('admin.services.getData');
    Route::delete('/services/{id}', [AdminServiceController::class, 'destroy'])->name('admin.services.destroy');

    Route::get('/services/aditional-info/{id}', [AdminServiceController::class, 'editAditionalInfo'])->name('admin.services.editAditionalInfo');
    Route::get('/services/aditional-info/{id}/show', [AdminServiceController::class, 'showAditionalInfo'])->name('admin.services.showAditionalInfo');
    Route::patch('/services/aditional-info/{id}', [AdminServiceController::class, 'updateAditionalInfo'])->name('admin.services.updateAditionalInfo');
    Route::get('/services/export-excel', [AdminServiceController::class, 'exportExcel'])->name("admin.services.exportExcel");
});

Route::group(array('prefix' => 'admin', 'middleware' => ['auth', 'verified', 'check.active', 'profile.complete', 'avaible.site', 'selected.center']), function () {
    //admin clinic-personal
    Route::get('/clinic-personal', [AdminClinicPersonalController::class, 'index']);
    Route::get('/clinic-personal/create', [AdminClinicPersonalController::class, 'create'])->name('admin.clinic-personal.create');
    Route::get('/clinic-personal/{id}/edit', [AdminClinicPersonalController::class, 'edit'])->name('admin.clinic-personal.edit');
    Route::get('/clinic-personal/{id}/show', [AdminClinicPersonalController::class, 'show'])->name('admin.clinic-personal.show');
    Route::get('/clinic-personal/remove-filter', [AdminClinicPersonalController::class, 'removeFilter'])->name('admin.clinic-personal.removeFilter');
    Route::patch('/clinic-personal/{id}', [AdminClinicPersonalController::class, 'update'])->name('admin.clinic-personal.update');
    Route::post('/clinic-personal/save-filter', [AdminClinicPersonalController::class, 'saveFilter'])->name('admin.clinic-personal.saveFilter');
    Route::post('/clinic-personal/list', [AdminClinicPersonalController::class, 'getData'])->name('admin.clinic-personal.getData');

    Route::get('/clinic-personal/get-image/{photo}', [AdminClinicPersonalController::class, 'getimage'])->name("admin.clinic-personal.getimage");
    Route::get('/clinic-personal/export-excel', [AdminClinicPersonalController::class, 'exportExcel'])->name("admin.clinic-personal.exportExcel");

    //admin patients
    Route::get('/patients', [AdminPatientController::class, 'index'])->name("admin.patients");
    Route::get('/patients/create', [AdminPatientController::class, 'create'])->name('admin.patients.create');
    Route::get('/patients/{id}/edit', [AdminPatientController::class, 'edit'])->name('admin.patients.edit');
    Route::get('/patients/{id}/show', [AdminPatientController::class, 'show'])->name('admin.patients.show');
    Route::get('/patients/remove-filter', [AdminPatientController::class, 'removeFilter'])->name('admin.patients.removeFilter');
    Route::get('/patients/change-state/{id}', [AdminPatientController::class, 'changeState'])->name('admin.patients.changeState');

    Route::post('/patients', [AdminPatientController::class, 'store'])->name('admin.patients.store');
    Route::patch('/patients/{id}', [AdminPatientController::class, 'update'])->name('admin.patients.update');
    Route::post('/patients/save-filter', [AdminPatientController::class, 'saveFilter'])->name('admin.patients.saveFilter');
    Route::post('/patients/list', [AdminPatientController::class, 'getData'])->name('admin.patients.getData');

    Route::get('/patients/get-image/{photo}', [AdminPatientController::class, 'getimage'])->name("admin.patients.getimage");
    Route::get('/patients/export-excel', [AdminPatientController::class, 'exportExcel'])->name("admin.patients.exportExcel");
    Route::delete('/patients/delete-image/{photo}', [AdminPatientController::class, 'deleteImage'])->name("admin.patients.deleteImage");
    Route::delete('/patients/{id}', [AdminPatientController::class, 'destroy'])->name('admin.patients.destroy');

    Route::get('/patients/clinical-record/{id}/edit', [AdminPatientController::class, 'clinicalRecord'])->name('admin.patients.clinicalRecord');
    Route::patch('/patients/clinical-record/{id}', [AdminPatientController::class, 'clinicalRecordUpdate'])->name('admin.patients.clinicalRecordUpdate');

    Route::get('/patients/insurance-carriers/{id}/edit', [AdminPatientController::class, 'insuranceCarrier'])->name('admin.patients.insuranceCarrier');
    Route::patch('/patients/insurance-carriers/{id}', [AdminPatientController::class, 'insuranceCarrierUpdate'])->name('admin.patients.insuranceCarrierUpdate');

    //medicines patients
    Route::get('/patients/{patient_id}/medicines', [AdminPatientMedicineController::class, 'index'])->name('admin.patients.medicines');
    Route::get('/patients/{patient_id}/medicines/create', [AdminPatientMedicineController::class, 'create'])->name('admin.patients.medicines.create');
    Route::get('/patients/{patient_id}/medicines/{id}/edit', [AdminPatientMedicineController::class, 'edit'])->name('admin.patients.medicines.edit');
    Route::get('/patients/{patient_id}/medicines/{id}/show', [AdminPatientMedicineController::class, 'show'])->name('admin.patients.medicines.show');
    Route::get('/patients/{patient_id}/medicines/{id}/copy', [AdminPatientMedicineController::class, 'copy'])->name('admin.patients.medicines.copy');
    Route::get('/patients/{patient_id}/medicines/{id}/generate-pdf', [AdminPatientMedicineController::class, 'generatePdf'])->name('admin.patients.medicines.generatePdf');
    Route::get('/patients/{patient_id}/medicines/remove-filter', [AdminPatientMedicineController::class, 'removeFilter'])->name('admin.patients.medicines.removeFilter');
    Route::get('/patients/{patient_id}/medicines/change-state/{id}', [AdminPatientMedicineController::class, 'changeState'])->name('admin.patients.medicines.changeState');

    Route::get('/patients/{patient_id}/medicines/export-excel', [AdminPatientMedicineController::class, 'exportExcel'])->name("admin.patients.medicines.exportExcel");

    Route::post('/patients/{patient_id}/medicines', [AdminPatientMedicineController::class, 'store'])->name('admin.patients.medicines.store');
    Route::patch('/patients/{patient_id}/medicines/{id}', [AdminPatientMedicineController::class, 'update'])->name('admin.patients.medicines.update');
    Route::post('/patients/{patient_id}/medicines/save-filter', [AdminPatientMedicineController::class, 'saveFilter'])->name('admin.patients.medicines.saveFilter');
    Route::post('/patients/{patient_id}/medicines/list', [AdminPatientMedicineController::class, 'getData'])->name('admin.patients.medicines.getData');
    Route::delete('/patients/{patient_id}/medicines/{id}', [AdminPatientMedicineController::class, 'destroy'])->name('admin.patients.medicines.destroy');

    //estudios patients
    Route::get('/patients/{patient_id}/medical-studies', [AdminPatientMedicalStudieController::class, 'index'])->name('admin.patients.medical-studies');
    Route::get('/patients/{patient_id}/medical-studies/create', [AdminPatientMedicalStudieController::class, 'create'])->name('admin.patients.medical-studies.create');
    Route::get('/patients/{patient_id}/medical-studies/{id}/edit', [AdminPatientMedicalStudieController::class, 'edit'])->name('admin.patients.medical-studies.edit');
    Route::get('/patients/{patient_id}/medical-studies/{id}/show', [AdminPatientMedicalStudieController::class, 'show'])->name('admin.patients.medical-studies.show');
    Route::get('/patients/{patient_id}/medical-studies/{id}/copy', [AdminPatientMedicalStudieController::class, 'copy'])->name('admin.patients.medical-studies.copy');
    Route::get('/patients/{patient_id}/medical-studies/{id}/generate-pdf', [AdminPatientMedicalStudieController::class, 'generatePdf'])->name('admin.patients.medical-studies.generatePdf');
    Route::get('/patients/{patient_id}/medical-studies/remove-filter', [AdminPatientMedicalStudieController::class, 'removeFilter'])->name('admin.patients.medical-studies.removeFilter');
    Route::get('/patients/{patient_id}/medical-studies/change-state/{id}', [AdminPatientMedicalStudieController::class, 'changeState'])->name('admin.patients.medical-studies.changeState');

    Route::get('/patients/{patient_id}/medical-studies/export-excel', [AdminPatientMedicalStudieController::class, 'exportExcel'])->name("admin.patients.medical-studies.exportExcel");

    Route::post('/patients/{patient_id}/medical-studies', [AdminPatientMedicalStudieController::class, 'store'])->name('admin.patients.medical-studies.store');
    Route::patch('/patients/{patient_id}/medical-studies/{id}', [AdminPatientMedicalStudieController::class, 'update'])->name('admin.patients.medical-studies.update');
    Route::post('/patients/{patient_id}/medical-studies/save-filter', [AdminPatientMedicalStudieController::class, 'saveFilter'])->name('admin.patients.medical-studies.saveFilter');
    Route::post('/patients/{patient_id}/medical-studies/list', [AdminPatientMedicalStudieController::class, 'getData'])->name('admin.patients.medical-studies.getData');
    Route::delete('/patients/{patient_id}/medical-studies/{id}', [AdminPatientMedicalStudieController::class, 'destroy'])->name('admin.patients.medical-studies.destroy');

    //seguimientos patients
    Route::get('/patients/{patient_id}/monitorings', [AdminPatientMonitoringController::class, 'index'])->name('admin.patients.monitorings');
    Route::get('/patients/{patient_id}/monitorings/create', [AdminPatientMonitoringController::class, 'create'])->name('admin.patients.monitorings.create');
    Route::get('/patients/{patient_id}/monitorings/{id}/edit', [AdminPatientMonitoringController::class, 'edit'])->name('admin.patients.monitorings.edit');
    Route::get('/patients/{patient_id}/monitorings/{id}/show', [AdminPatientMonitoringController::class, 'show'])->name('admin.patients.monitorings.show');
    Route::get('/patients/{patient_id}/monitorings/{id}/copy', [AdminPatientMonitoringController::class, 'copy'])->name('admin.patients.monitorings.copy');
    Route::get('/patients/{patient_id}/monitorings/{id}/generate-pdf', [AdminPatientMonitoringController::class, 'generatePdf'])->name('admin.patients.monitorings.generatePdf');
    Route::get('/patients/{patient_id}/monitorings/remove-filter', [AdminPatientMonitoringController::class, 'removeFilter'])->name('admin.patients.monitorings.removeFilter');
    Route::get('/patients/{patient_id}/monitorings/change-state/{id}', [AdminPatientMonitoringController::class, 'changeState'])->name('admin.patients.monitorings.changeState');

    Route::get('/patients/{patient_id}/monitorings/export-excel', [AdminPatientMonitoringController::class, 'exportExcel'])->name("admin.patients.monitorings.exportExcel");

    Route::post('/patients/{patient_id}/monitorings', [AdminPatientMonitoringController::class, 'store'])->name('admin.patients.monitorings.store');
    Route::patch('/patients/{patient_id}/monitorings/{id}', [AdminPatientMonitoringController::class, 'update'])->name('admin.patients.monitorings.update');
    Route::post('/patients/{patient_id}/monitorings/save-filter', [AdminPatientMonitoringController::class, 'saveFilter'])->name('admin.patients.monitorings.saveFilter');
    Route::post('/patients/{patient_id}/monitorings/list', [AdminPatientMonitoringController::class, 'getData'])->name('admin.patients.monitorings.getData');
    Route::delete('/patients/{patient_id}/monitorings/{id}', [AdminPatientMonitoringController::class, 'destroy'])->name('admin.patients.monitorings.destroy');

    ///appointments
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('admin.appointment');
    Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('admin.calendar');
});
