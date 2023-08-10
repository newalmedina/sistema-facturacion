<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminCenterController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDiagnosiController;
use App\Http\Controllers\AdminInsuranceCarrierController;
use App\Http\Controllers\AdminMedicalSpecializationController;
use App\Http\Controllers\AdminMunicipioController;
use App\Http\Controllers\AdminProvinceController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminSuplantacionController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminUserProfileController;
use App\Http\Controllers\Auth\FrontRegisterUserController;
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


//General Routes
Route::group(array('prefix' => 'front', 'middleware' => []), function () {

    Route::get('/settings/get-image/{image}', [FrontSettingsController::class, 'getImage'])->name("front.settings-get-image");
});
Route::group(array('prefix' => 'admin', 'middleware' => ['auth', 'verified', 'check.active', 'avaible.site']), function () {

    Route::get('/profile/personal-info', [AdminUserProfileController::class, 'personalInfo']);
    Route::post('/profile/personal-info/store', [AdminUserProfileController::class, 'updatePersonalInfo'])->name("admin.updateProfilePersonalInfo");
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
    Route::get('/users/change-state/{id}', [AdminUserController::class, 'changeState'])->name('admin.users.changeState');
    Route::patch('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::post('/users/list', [AdminUserController::class, 'getData'])->name('admin.users.getData');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/users/roles/{id}', [AdminUserController::class, 'editRoles'])->name('admin.users.editRoles');
    Route::patch('/users/roles/{id}', [AdminUserController::class, 'updateRoles'])->name('admin.users.updateRoles');
    Route::get('/users/centers/{id}', [AdminUserController::class, 'editCenters'])->name('admin.users.editCenters');
    Route::patch('/users/centers/{id}', [AdminUserController::class, 'updateCenters'])->name('admin.users.updateCenters');

    Route::get('/users/personal-info/{id}', [AdminUserController::class, 'personalInfo']);
    Route::post('/users/personal-info/store/{id}', [AdminUserController::class, 'updatePersonalInfo'])->name("admin.users.updatePersonalInfo");
    //admin centers
    Route::get('/centers', [AdminCenterController::class, 'index']);
    Route::get('/centers/create', [AdminCenterController::class, 'create'])->name('admin.centers.create');
    Route::get('/centers/{id}/edit', [AdminCenterController::class, 'edit'])->name('admin.centers.edit');
    Route::get('/centers/change-state/{id}', [AdminCenterController::class, 'changeState'])->name('admin.centers.changeState');
    Route::get('/centers/remove-filter', [AdminCenterController::class, 'removeFilter'])->name('admin.centers.removeFilter');
    Route::patch('/centers/{id}', [AdminCenterController::class, 'update'])->name('admin.centers.update');
    Route::post('/centers/change-center', [AdminCenterController::class, 'changeCenter'])->name('admin.centers.changeCenterUpdate');
    Route::post('/centers', [AdminCenterController::class, 'store'])->name('admin.centers.store');
    Route::post('/centers/save-filter', [AdminCenterController::class, 'saveFilter'])->name('admin.centers.saveFilter');
    Route::post('/centers/list', [AdminCenterController::class, 'getData'])->name('admin.centers.getData');
    Route::delete('/centers/{id}', [AdminCenterController::class, 'destroy'])->name('admin.centers.destroy');

    Route::get('/centers/aditional-info/{id}', [AdminCenterController::class, 'editAditionalInfo'])->name('admin.centers.editAditionalInfo');
    Route::patch('/centers/aditional-info/{id}', [AdminCenterController::class, 'updateAditionalInfo'])->name('admin.centers.updateAditionalInfo');
    Route::get('/centers/get-image/{photo}', [AdminCenterController::class, 'getimage'])->name("admin.centers.getimage");
    Route::get('/centers/export-excel', [AdminCenterController::class, 'exportExcel'])->name("admin.centers.exportExcel");
    Route::delete('/centers/delete-image/{photo}', [AdminCenterController::class, 'deleteImage'])->name("admin.centers.deleteImage");

    //admin municipios

    Route::get('/municipios', [AdminMunicipioController::class, 'index']);
    Route::get('/municipios/create', [AdminMunicipioController::class, 'create'])->name('admin.municipios.create');
    Route::get('/municipios/{id}/edit', [AdminMunicipioController::class, 'edit'])->name('admin.municipios.edit');
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
    Route::get('/insurance-carriers/change-state/{id}', [AdminInsuranceCarrierController::class, 'changeState'])->name('admin.insurance-carriers.changeState');
    Route::get('/insurance-carriers/remove-filter', [AdminInsuranceCarrierController::class, 'removeFilter'])->name('admin.insurance-carriers.removeFilter');
    Route::patch('/insurance-carriers/{id}', [AdminInsuranceCarrierController::class, 'update'])->name('admin.insurance-carriers.update');
    Route::post('/insurance-carriers/change-center', [AdminInsuranceCarrierController::class, 'changeCenter'])->name('admin.insurance-carriers.changeCenterUpdate');
    Route::post('/insurance-carriers', [AdminInsuranceCarrierController::class, 'store'])->name('admin.insurance-carriers.store');
    Route::post('/insurance-carriers/save-filter', [AdminInsuranceCarrierController::class, 'saveFilter'])->name('admin.insurance-carriers.saveFilter');
    Route::post('/insurance-carriers/list', [AdminInsuranceCarrierController::class, 'getData'])->name('admin.insurance-carriers.getData');
    Route::delete('/insurance-carriers/{id}', [AdminInsuranceCarrierController::class, 'destroy'])->name('admin.insurance-carriers.destroy');

    Route::get('/insurance-carriers/aditional-info/{id}', [AdminInsuranceCarrierController::class, 'editAditionalInfo'])->name('admin.insurance-carriers.editAditionalInfo');
    Route::patch('/insurance-carriers/aditional-info/{id}', [AdminInsuranceCarrierController::class, 'updateAditionalInfo'])->name('admin.insurance-carriers.updateAditionalInfo');
    Route::get('/insurance-carriers/get-image/{photo}', [AdminInsuranceCarrierController::class, 'getimage'])->name("admin.insurance-carriers.getimage");
    Route::get('/insurance-carriers/export-excel', [AdminInsuranceCarrierController::class, 'exportExcel'])->name("admin.insurance-carriers.exportExcel");
    Route::delete('/insurance-carriers/delete-image/{photo}', [AdminInsuranceCarrierController::class, 'deleteImage'])->name("admin.insurance-carriers.deleteImage");


    //admin services
    Route::get('/services', [AdminServiceController::class, 'index']);
    Route::get('/services/create', [AdminServiceController::class, 'create'])->name('admin.services.create');
    Route::get('/services/{id}/edit', [AdminServiceController::class, 'edit'])->name('admin.services.edit');
    Route::get('/services/change-state/{id}', [AdminServiceController::class, 'changeState'])->name('admin.services.changeState');
    Route::patch('/services/{id}', [AdminServiceController::class, 'update'])->name('admin.services.update');
    Route::post('/services', [AdminServiceController::class, 'store'])->name('admin.services.store');
    Route::post('/services/list', [AdminServiceController::class, 'getData'])->name('admin.services.getData');
    Route::delete('/services/{id}', [AdminServiceController::class, 'destroy'])->name('admin.services.destroy');

    Route::get('/services/aditional-info/{id}', [AdminServiceController::class, 'editAditionalInfo'])->name('admin.services.editAditionalInfo');
    Route::patch('/services/aditional-info/{id}', [AdminServiceController::class, 'updateAditionalInfo'])->name('admin.services.updateAditionalInfo');
    Route::get('/services/export-excel', [AdminServiceController::class, 'exportExcel'])->name("admin.services.exportExcel");
});
