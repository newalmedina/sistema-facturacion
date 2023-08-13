<?php

namespace App\Http\Controllers;

use App\Exports\AdminClinicPersonalExport;
use App\Http\Requests\AdminClinicPersonalRequest;
use App\Models\DoctorProfile;
use App\Models\InsuranceCarrier;
use App\Models\MedicalSpecialization;
use App\Models\Municipio;
use App\Models\Province;
use App\Models\User;
use App\Services\StoragePathWork;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

use Maatwebsite\Excel\Facades\Excel;

class AdminPatientController extends Controller
{
    public $filtSpecializationId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->filtSpecializationId = ($request->session()->has('clinic-personal_filter_specialization_id')) ? ($request->session()->get('clinic-personal_filter_specialization_id')) : [];
            return $next($request);
        });
    }

    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-patients')) {
            app()->abort(403);
        }

        $pageTitle = trans('clinic-personal/admin_lang.clinic-personal');
        $title = trans('clinic-personal/admin_lang.list');

        $specializations = MedicalSpecialization::active()->get();

        return view('clinic-personal.admin_index', compact('pageTitle', 'title', 'specializations'))
            ->with([
                'filtSpecializationId' => $this->filtSpecializationId,
            ]);
    }

    public function show($id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-read')) {
            app()->abort(403);
        }

        $clinicPersonal = User::where("users.id", $id)->clinicPersonal()->clinicPersonalSelectedCenter()->first();

        if (empty($clinicPersonal)) {
            app()->abort(404);
        }
        $pageTitle = trans('clinic-personal/admin_lang.show');
        $title = trans('clinic-personal/admin_lang.list');
        $tab = 'tab_1';
        $disabled = "disabled";

        $clinicPersonal = User::find($id);
        $specializations = MedicalSpecialization::active()->get();
        $specializationsSeledted = [];

        if (!empty($clinicPersonal->doctorProfile)) {
            foreach ($clinicPersonal->specializations as $specialization) {
                $specializationsSeledted[] = $specialization->id;
            }
        }

        return view('clinic-personal.admin_edit', compact('pageTitle', 'title', "clinicPersonal", 'specializations', 'specializationsSeledted', 'disabled'))
            ->with('tab', $tab);
    }

    public function edit($id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-update')) {
            app()->abort(403);
        }

        $clinicPersonal = User::where("users.id", $id)->clinicPersonal()->clinicPersonalSelectedCenter()->first();

        if (empty($clinicPersonal)) {
            app()->abort(404);
        }
        $pageTitle = trans('clinic-personal/admin_lang.show');
        $title = trans('clinic-personal/admin_lang.list');
        $tab = 'tab_1';

        $clinicPersonal = User::find($id);
        $specializations = MedicalSpecialization::active()->get();
        $specializationsSeledted = [];

        if (!empty($clinicPersonal->doctorProfile)) {
            foreach ($clinicPersonal->specializations as $specialization) {
                $specializationsSeledted[] = $specialization->id;
            }
        }

        return view('clinic-personal.admin_edit', compact('pageTitle', 'title', "clinicPersonal", 'specializations', 'specializationsSeledted'))
            ->with('tab', $tab);
    }

    public function update(AdminClinicPersonalRequest $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-update')) {
            app()->abort(403);
        }

        try {
            // DB::beginTransaction();
            $clinicPersonal = User::with('doctorProfile')->find($id);

            if (empty($clinicPersonal->doctorProfile)) {
                $newDoctorProfile = new DoctorProfile();
                $newDoctorProfile->user_id = $id;
                $newDoctorProfile->save();
                $clinicPersonal = User::with('doctorProfile')->find($id);
            }

            $this->saveclinicPersonal($clinicPersonal, $request);

            // DB::commit();
            return redirect()->route('admin.clinic-personal.edit', [$clinicPersonal->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/clinic-personal/create/' . $clinicPersonal->id)
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function saveFilter(Request $request)
    {
        $this->clearSesions($request);
        if (!empty($request->specialization_id))
            $request->session()->put('clinic-personal_filter_specialization_id', $request->specialization_id);


        return redirect('admin/clinic-personal');
    }

    public function removeFilter(Request $request)
    {
        $this->clearSesions($request);
        return redirect('admin/clinic-personal');
    }

    private function addFilter(&$query)
    {

        if (!empty($this->filtSpecializationId)) {
            $query->whereIn("doctor_specializations.specialization_id", $this->filtSpecializationId);
        }
    }

    private function clearSesions($request)
    {
        $request->session()->forget('clinic-personal_filter_specialization_id');
    }

    public function getData()
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
            ]
        )
            ->clinicPersonal()->clinicPersonalSelectedCenter()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->leftJoin("doctor_profiles", "doctor_profiles.user_id", "=", "users.id")
            ->leftJoin("doctor_specializations", "doctor_specializations.user_id", "=", "users.id")
            ->distinct();


        $this->addFilter($query);

        $table = DataTables::of($query);



        $table->editColumn('photo', function ($data) {
            if (empty($data->photo)) {
                return "";
            }

            return  '<center><img width="40" class="rounded-circle" src="' . url('admin/clinic-personal/get-image/' . $data->photo) . '" alt="imagen"> </center>';
        });
        $table->editColumn('specializations', function ($data) {

            $specializations = DB::table('doctor_specializations')
                ->join("medical_specializations", "medical_specializations.id", "=", "doctor_specializations.specialization_id")
                ->where("doctor_specializations.user_id", $data->id)
                ->pluck("medical_specializations.name");

            return $specializations->implode(", ");
        });


        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->isAbleTo("admin-patients-read")) {
                $actions .= '<a  class="btn btn-info btn-xs" href="' . route('admin.clinic-personal.show', $data->id) . '" ><i
                class="fa fa-eye fa-lg"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-update")) {
                $actions .= '<a  class="btn btn-primary btn-xs" href="' . route('admin.clinic-personal.edit', $data->id) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }


            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'photo', 'specializations']);
        return $table->make();
    }

    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->isAbleTo('admin-patients-delete')) {
            app()->abort(403);
        }
        $insuranceCarrier = InsuranceCarrier::find($id);
        if (empty($insuranceCarrier)) {
            app()->abort(404);
        }
        $myServiceSPW = new StoragePathWork("clinic-personal");

        if (!empty($insuranceCarrier->image)) {
            // $myServiceSPW->deleteFile($insuranceCarrier->image, '');
            // $insuranceCarrier->image = "";
            // $insuranceCarrier->save = "";
        }
        $insuranceCarrier->delete();

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

        $insuranceCarrier = InsuranceCarrier::find($id);

        if (!empty($insuranceCarrier)) {
            $insuranceCarrier->active = !$insuranceCarrier->active;
            return $insuranceCarrier->save() ? 1 : 0;
        }

        return 0;
    }


    public function getImage($photo)
    {
        $myServiceSPW = new StoragePathWork("users");
        return $myServiceSPW->showFile($photo, '/users');
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
                'user_profiles.last_name',
                'user_profiles.photo',
                'doctor_profiles.exequatur',
            ]
        )
            ->clinicPersonal()->clinicPersonalSelectedCenter()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->leftJoin("doctor_profiles", "doctor_profiles.user_id", "=", "users.id")

            ->leftJoin("doctor_specializations", "doctor_specializations.user_id", "=", "users.id")
            ->distinct();

        $this->addFilter($query);

        return Excel::download(new AdminClinicPersonalExport($query), strtolower(trans('clinic-personal/admin_lang.clinic-personal')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function saveclinicPersonal($clinicPersonal, $request)
    {
        $clinicPersonal->doctorProfile->exequatur = $request->input('doctor_profile.exequatur');
        $clinicPersonal->doctorProfile->save();

        $specializations = $request->input('doctor_profile.specialization_id');
        $clinicPersonal->specializations()->sync($specializations);
    }
}
