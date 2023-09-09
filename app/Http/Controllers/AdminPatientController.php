<?php

namespace App\Http\Controllers;

use App\Exports\AdminClinicPersonalExport;
use App\Http\Requests\AdminPatientsRequest;
use App\Models\InsuranceCarrier;
use App\Models\Municipio;
use App\Models\PatientProfile;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\StoragePathWork;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class AdminPatientController extends Controller
{
    public $filtProvinceId;
    public $filtMunicipioId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->filtMunicipioId = ($request->session()->has('patient_filter_municipio_id')) ? ($request->session()->get('patient_filter_municipio_id')) : "";
            $this->filtProvinceId = ($request->session()->has('patient_filter_province_id')) ? ($request->session()->get('patient_filter_province_id')) : "";
            return $next($request);
        });
    }

    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-patients')) {
            app()->abort(403);
        }

        $pageTitle = trans('patients/admin_lang.patients');
        $title = trans('patients/admin_lang.list');
        $provincesList = Province::active()->get();
        // $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();
        $municipiosList = Municipio::active()->where("province_id", $this->filtProvinceId)->get();


        return view('patients.admin_index', compact('pageTitle', 'title', 'provincesList', 'municipiosList'))
            ->with([
                'filtProvinceId' => $this->filtProvinceId,
                'filtMunicipioId' => $this->filtMunicipioId,
            ]);
    }
    public function create()
    {
        if (!auth()->user()->isAbleTo('admin-patients-create')) {
            app()->abort(403);
        }
        $patient = new User();

        $pageTitle = trans('patients/admin_lang.patients');
        $title = trans('patients/admin_lang.list');
        $provincesList = Province::active()->get();
        // $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();
        $municipiosList = Municipio::active()->where("province_id", $patient->province_id)->get();

        $tab = 'tab_1';


        $genders = [
            "male" => trans("general/admin_lang.male"),
            "female" => trans("general/admin_lang.female")
        ];

        return view('patients.admin_edit', compact('pageTitle', 'title', 'provincesList', 'municipiosList', 'patient', 'genders'))
            ->with('tab', $tab);
    }

    public function show($id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-read')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        $patient = User::find($id);
        $pageTitle = trans('patients/admin_lang.patients');
        $title = trans('patients/admin_lang.list');
        $provincesList = Province::active()->get();
        // $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();
        $municipiosList = Municipio::active()->where("province_id", $patient->userProfile->province_id)->get();

        $tab = 'tab_1';


        $genders = [
            "male" => trans("general/admin_lang.male"),
            "female" => trans("general/admin_lang.female")
        ];
        $disabled = "disabled";
        return view('patients.admin_edit', compact('pageTitle', 'title', 'provincesList', 'municipiosList', 'patient', 'genders', 'disabled'))
            ->with('tab', $tab);
    }

    public function edit($id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-update')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        $patient = User::find($id);
        $pageTitle = trans('patients/admin_lang.general_info');
        $title = trans('patients/admin_lang.list');
        $provincesList = Province::active()->get();
        // $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();
        $municipiosList = Municipio::active()->where("province_id", $patient->userProfile->province_id)->get();

        $tab = 'tab_1';


        $genders = [
            "male" => trans("general/admin_lang.male"),
            "female" => trans("general/admin_lang.female")
        ];

        $deleteImage = true;

        return view('patients.admin_edit', compact('pageTitle', 'title', 'provincesList', 'municipiosList', 'patient', 'genders', 'deleteImage'))
            ->with('tab', $tab);
    }

    public function store(AdminPatientsRequest $request)
    {
        if (!auth()->user()->isAbleTo('admin-patients-create')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();
            $patient = new User();
            $patient->email = Str::random(8);
            $patient->password =  Hash::make($patient->email);
            $this->savePatients($patient, $request);

            $roles = Role::where("name", "patient")->pluck("id");

            $patient->syncRoles($roles);
            DB::commit();
            return redirect()->route('admin.patients.edit', [$patient->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/patients/create/')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }
    public function update(AdminPatientsRequest $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-update')) {
            app()->abort(403);
        }

        try {
            $patient = User::where("users.id", $id)->patients()->first();

            if (empty($patient)) {
                app()->abort(404);
            }

            $patient = User::find($id);

            $this->savePatients($patient, $request);

            // DB::commit();
            return redirect()->route('admin.patients.edit', [$patient->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/patients/create/' . $patient->id)
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function clinicalRecord($id)
    {
        $disabled = null;
        if (!auth()->user()->isAbleTo('admin-patients-clinic-record-update') && !auth()->user()->isAbleTo('admin-patients-clinic-record-read')) {
            app()->abort(403);
        }
        if (!auth()->user()->isAbleTo('admin-patients-clinic-record-update') && auth()->user()->isAbleTo('admin-patients-clinic-record-read')) {
            $disabled = "disabled";
        }

        $patient = User::where("users.id", $id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        $patient = User::find($id);
        $pageTitle = trans('patients/admin_lang.clinic_record');
        $title = trans('patients/admin_lang.list');
        $provincesList = Province::active()->get();
        // $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();
        $municipiosList = Municipio::active()->where("province_id", $patient->userProfile->province_id)->get();

        $tab = 'tab_2';


        $genders = [
            "male" => trans("general/admin_lang.male"),
            "female" => trans("general/admin_lang.female")
        ];
        return view('patients.admin_clinic_record_edit', compact('pageTitle', 'title', 'provincesList', 'municipiosList', 'patient', 'genders', 'disabled'))
            ->with('tab', $tab);
    }

    public function clinicalRecordUpdate(Request $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-clinic-record-update')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $patient = User::where("users.id", $id)->patients()->first();

            if (empty($patient)) {
                app()->abort(404);
            }

            $patient = User::find($id);
            if (empty($patient->patientProfile)) {
                $patientProfile = new PatientProfile();
                $patientProfile->user_id = $patient->id;
            } else {
                $patientProfile = PatientProfile::where('user_id', $patient->id)->first();
            }

            $patientProfile->allergies = $request->input('patient_profile.allergies');
            $patientProfile->pathological_diseases = $request->input('patient_profile.pathological_diseases');
            $patientProfile->surgical_diseases = $request->input('patient_profile.surgical_diseases');
            $patientProfile->family_history = $request->input('patient_profile.family_history');
            $patientProfile->gynecological_history = $request->input('patient_profile.gynecological_history');
            $patientProfile->others = $request->input('patient_profile.others');
            $patientProfile->save();


            DB::commit();
            return redirect()->route('admin.patients.clinicalRecord', [$patient->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function insuranceCarrier($id)
    {
        $disabled = null;
        if (!auth()->user()->isAbleTo('admin-patients-insurance-carriers-update') && !auth()->user()->isAbleTo('admin-patients-insurance-carriers-read')) {
            app()->abort(403);
        }
        if (!auth()->user()->isAbleTo('admin-patients-insurance-carriers-update') && auth()->user()->isAbleTo('admin-patients-insurance-carriers-read')) {

            $disabled = "disabled";
        }

        $patient = User::where("users.id", $id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }
        $patient = User::find($id);
        $pageTitle = trans('patients/admin_lang.insurance_carriers');
        $title = trans('patients/admin_lang.list');

        $insuranceList = InsuranceCarrier::active()->get();
        $patient = User::find($id);
        $tab = 'tab_3';

        return view('patients.admin_insurance_carrier', compact('pageTitle', 'title', 'insuranceList', 'patient', 'disabled'))
            ->with('tab', $tab);
    }

    public function insuranceCarrierUpdate(Request $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-insurance-carriers-update')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $patient = User::where("users.id", $id)->patients()->first();

            if (empty($patient)) {
                app()->abort(404);
            }

            $patient = User::find($id);
            $patient->insuranceCarriers()->detach();
            if (!empty($request->insurance)) {
                foreach ($request->insurance as $key => $value) {
                    $segurosData = [];
                    $segurosData[$value] = ["poliza" => $request->poliza[$key]];
                    $patient->insuranceCarriers()->attach($segurosData);
                }
            }

            $patient->save();

            DB::commit();
            return redirect()->route('admin.patients.insuranceCarrier', [$patient->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function saveFilter(Request $request)
    {
        $this->clearSesions($request);
        if (!empty($request->province_id))
            $request->session()->put('patient_filter_province_id', $request->province_id);

        if (!empty($request->municipio_id))
            $request->session()->put('patient_filter_municipio_id', $request->municipio_id);


        return redirect('admin/patients');
    }

    public function removeFilter(Request $request)
    {
        $this->clearSesions($request);
        return redirect('admin/patients');
    }

    private function addFilter(&$query)
    {

        if (!empty($this->filtProvinceId)) {
            $query->where("user_profiles.province_id", $this->filtProvinceId);
        }
        if (!empty($this->filtMunicipioId)) {
            $query->where("user_profiles.municipio_id", $this->filtMunicipioId);
        }
    }

    private function clearSesions($request)
    {
        $request->session()->forget('patient_filter_province_id');
        $request->session()->forget('patient_filter_municipio_id');
    }

    public function getData()
    {
        if (!auth()->user()->isAbleTo('admin-patients-list')) {
            app()->abort(403);
        }
        $query = User::select(
            [
                'users.id',
                'users.active',
                'patient_profiles.email',
                'user_profiles.phone',
                'user_profiles.first_name',
                DB::raw('CONCAT(user_profiles.first_name, " ", user_profiles.last_name) as patient'),
                'user_profiles.photo',
                'provinces.name as province',
                'municipios.name as municipio',
            ]
        )
            ->patients()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->leftJoin("patient_profiles", "patient_profiles.user_id", "=", "users.id")
            ->leftJoin("doctor_profiles", "doctor_profiles.user_id", "=", "users.id")
            ->leftJoin("provinces", "user_profiles.province_id", "=", "provinces.id")
            ->leftJoin("municipios", "user_profiles.municipio_id", "=", "municipios.id")
            ->distinct();

        $this->addFilter($query);

        $table = DataTables::of($query);

        $table->filterColumn('patient', function ($query, $keyword) {
            $query->whereRaw("CONCAT(user_profiles.first_name,' ',user_profiles.last_name) like ?", ["%{$keyword}%"]);
        });


        $table->editColumn('photo', function ($data) {
            if (empty($data->photo)) {
                return "";
            }

            return  '<center><img width="40" class="rounded-circle" src="' . url('admin/patients/get-image/' . $data->photo) . '" alt="imagen"> </center>';
        });

        $table->editColumn('active', function ($data) {
            $permision = "";
            if (!auth()->user()->isAbleTo('admin-patients-update')) {
                $permision = "disabled";
            }

            $state = $data->active ? "checked" : "";

            return  '<div class="form-check form-switch ">
                <input class="form-check-input" onclick="changeState(' . $data->id . ')" ' . $state . '  ' . $permision . '  value="1" name="active" type="checkbox" id="active">
            </div>';
        });

        $table->editColumn('actions', function ($data) {
            $actions = '';
            // if (auth()->user()->isAbleTo("admin-patients-read")) {
            //     $actions .= '<a  class="mb-2 btn btn-info btn-xs" href="' . route('admin.patients.show', $data->id) . '" ><i
            //     class="fa fa-eye"></i></a> ';
            // }
            if (auth()->user()->isAbleTo("admin-patients-update")) {
                $actions .= '<a  class="mb-2 btn btn-primary btn-xs" data-bs-content="' .trans('general/admin_lang.edit') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.edit', $data->id) . '" ><i
                class="fa fa-marker"></i></a> ';
            } elseif (auth()->user()->isAbleTo("admin-patients-read")) {
                $actions .= '<a  class="mb-2 btn btn-info btn-xs" data-bs-content="' .trans('general/admin_lang.show') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.show', $data->id) . '" ><i
                    class="fa fa-eye"></i></a> ';
            }

            if (auth()->user()->isAbleTo("admin-patients-clinic-record-update") || auth()->user()->isAbleTo("admin-patients-clinic-record-read")) {
                $actions .= '<a  class="mb-2 btn btn-tertiary btn-xs" data-bs-content="' .trans('patients/admin_lang.clinic_record') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.clinicalRecord', $data->id) . '" ><i
                class="fa fa-notes-medical"></i></a> ';
            }
            
            if (auth()->user()->isAbleTo("admin-patients-insurance-carriers-update") || auth()->user()->isAbleTo("admin-patients-insurance-carriers-read")) {
                $actions .= '<a  class="mb-2 btn btn-secondary btn-xs"  data-bs-content="' .trans('patients/admin_lang.insurance_carriers') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.insuranceCarrier', $data->id) . '" ><i
                class="fa fa-house-damage"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-medicines")) {
                $actions .= '<a  class="mb-2 btn btn-warning btn-xs" data-bs-content="' .trans('patient-medicines/admin_lang.patient-medicines') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.medicines', $data->id) . '" ><i
                class="fa fa-pills"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-medical-studies")) {
                $actions .= '<a  class="mb-2 btn btn-quaternary btn-xs" data-bs-content="' .trans('patients/admin_lang.insurance_carriers') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.medical-studies', $data->id) . '" ><i
                class="fa fa-book-medical"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-monitoring")) {
                $actions .= '<a  class="mb-2 btn btn-default btn-xs" data-bs-content="' .trans('patients/admin_lang.patient_monitorings') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.monitorings', $data->id) . '" ><i
                class="fa fas fa-heartbeat"></i></a> ';
                
            }
            if (auth()->user()->isAbleTo("admin-patients-delete")) {

                $actions .= '<button class="mb-2 btn btn-danger btn-xs" data-bs-content="' .trans('general/admin_lang.delete') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    url('admin/patients/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }


            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'photo', 'active']);
        return $table->make();
    }

    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->isAbleTo('admin-patients-delete')) {
            app()->abort(403);
        }
        $patients = User::find($id);
        if (empty($patients)) {
            app()->abort(404);
        }
        $myServiceSPW = new StoragePathWork("users");

        if (!empty($patients->image)) {
            // $myServiceSPW->deleteFile($patients->image, '');
            // $patients->image = "";
            // $patients->save = "";
        }
        $patients->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }

    public function changeState($id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-update')) {
            app()->abort(403);
        }

        $patients = User::find($id);

        if (!empty($patients)) {
            $patients->active = !$patients->active;
            return $patients->save() ? 1 : 0;
        }

        return 0;
    }


    public function getImage($photo)
    {
        $myServiceSPW = new StoragePathWork("users");
        return $myServiceSPW->showFile($photo, '/users');
    }
    public function deleteImage($id)
    {
        $myServiceSPW = new StoragePathWork("users");
        $patient = User::find($id);

        if (!empty($patient->userProfile->photo)) {
            $myServiceSPW->deleteFile($patient->userProfile->photo, '');
            $patient->userProfile->photo = "";
        }
        $patient->userProfile->save();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }


    public function exportExcel()
    {
        if (!auth()->user()->isAbleTo('admin-patients-list')) {
            app()->abort(403);
        }

        $query = User::select(
            [
                'users.id',
                'users.email',
                'user_profiles.phone',
                'user_profiles.first_name',
                'user_profiles.photo',
                'provinces.name as province',
                'municipios.name as municipio',
            ]
        )
            ->patient()->clinicPersonalSelectedCenter()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->leftJoin("doctor_profiles", "doctor_profiles.user_id", "=", "users.id")
            ->leftJoin("provinces", "user_profiles.province_id", "=", "provinces.id")
            ->leftJoin("municipios", "user_profiles.municipio_id", "=", "municipios.id")
            ->where("roles.name", "patient")
            ->distinct();

        $this->addFilter($query);

        return Excel::download(new AdminClinicPersonalExport($query), strtolower(trans('patients/admin_lang.patients')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function savePatients($patient, $request)
    {

        $patient->active = $request->input('active', 0);
        $patient->save();
        if (empty($patient->userProfile)) {
            $userProfile = new UserProfile();
            $userProfile->user_id = $patient->id;
        } else {
            $userProfile = UserProfile::where('user_id', $patient->id)->first();
        }


        $userProfile->first_name = $request->input('user_profile.first_name');
        $userProfile->last_name = $request->input('user_profile.last_name');
        $userProfile->birthday = !empty($request->input('user_profile.birthday')) ? Carbon::createFromFormat("d/m/Y", $request->input('user_profile.birthday'))->format("Y-m-d") : null;
        $userProfile->identification = $request->input('user_profile.identification');
        $userProfile->phone = $request->input('user_profile.phone');
        $userProfile->mobile = $request->input('user_profile.mobile');
        $userProfile->gender = $request->input('user_profile.gender');
        $userProfile->province_id = $request->input('user_profile.province_id');
        $userProfile->municipio_id = $request->input('user_profile.municipio_id');
        $userProfile->address = $request->input('user_profile.address');

        $image = $request->file('image');

        if (!is_null($image)) {
            $myServiceSPW = new StoragePathWork("users");

            if (!empty($userProfile->photo)) {
                $myServiceSPW->deleteFile($userProfile->photo, '');
                $userProfile->photo  = "";
            }

            $filename = $myServiceSPW->saveFile($image, '');
            $userProfile->photo  = $filename;
        }

        $userProfile->save();

        if (empty($patient->patientProfile)) {
            $patientProfile = new PatientProfile();
            $patientProfile->user_id = $patient->id;
            $patientProfile->created_by = Auth::user()->id;
        } else {
            $patientProfile = PatientProfile::where('user_id', $patient->id)->first();
        }

        $patientProfile->email = $request->input('patient_profile.email');
        $patientProfile->save();
    }
}
