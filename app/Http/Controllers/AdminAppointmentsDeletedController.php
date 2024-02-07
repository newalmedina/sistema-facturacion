<?php

namespace App\Http\Controllers;

use App\Exports\AdminappointmentsExport;
use App\Http\Requests\AdminAppointmentRequest;
use App\Http\Requests\AdminChangeCenterRequest;
use App\Models\Appointment;
use App\Models\Center;
use App\Models\InsuranceCarrier;
use App\Models\Municipio;
use App\Models\PermissionsTree;
use App\Models\Province;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\StoragePathWork;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class AdminAppointmentsDeletedController extends Controller
{
    public $filtStartAtIni;
    public $filtStartAtEnd;
    public $filtDoctorId;
    public $filtUserId;
    public $filtState;
    public $filtServiceId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->filtStartAtIni = ($request->session()->has('appointment_deleted_filter_start_at_ini')) ? ($request->session()->get('appointment_deleted_filter_start_at_ini')) : "";
            $this->filtStartAtEnd = ($request->session()->has('appointment_deleted_filter_start_at_end')) ? ($request->session()->get('appointment_deleted_filter_start_at_end')) : "";
            $this->filtDoctorId = ($request->session()->has('appointment_deleted_filter_doctor_id')) ? ($request->session()->get('appointment_deleted_filter_doctor_id')) : [];
            $this->filtUserId = ($request->session()->has('appointment_deleted_filter_user_id')) ? ($request->session()->get('appointment_deleted_filter_user_id')) : [];
            $this->filtState = ($request->session()->has('appointment_deleted_filter_state')) ? ($request->session()->get('appointment_deleted_filter_state')) : [];
            $this->filtServiceId = ($request->session()->has('appointment_deleted_filter_service_id')) ? ($request->session()->get('appointment_deleted_filter_service_id')) : [];
            return $next($request);
        });
    }

    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-appointments-deleted-list')) {
            app()->abort(403);
        }

        $pageTitle = trans('appointments_deleted/admin_lang.appointments');
        $title = trans('appointments_deleted/admin_lang.list');
        $patientList = User::select("users.*")
            ->active()
            ->patients()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->get();
        $doctorList =  User::select("users.*")
            ->active()
            ->clinicPersonal()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->leftJoin("user_centers", "user_centers.user_id", "=", "users.id")
            ->where("user_centers.center_id", auth()->user()->userProfile->center->id)
            ->get();
        $serviceList = Service::active()->get();
        $stateList =  [
            "pendiente" => "Pendiente",
            "facturado" => "Facturado",
            "finalizado" => "Finalizado",
        ];

        return view('appointments.admin_deleted_appointments', compact('pageTitle', 'title', 'patientList', 'doctorList', "stateList", "serviceList"))
            ->with([
                "filtStartAtIni" => $this->filtStartAtIni,
                "filtStartAtEnd" => $this->filtStartAtEnd,
                "filtDoctorId" => $this->filtDoctorId,
                "filtUserId" => $this->filtUserId,
                "filtState" => $this->filtState,
                "filtServiceId" => $this->filtServiceId,
            ]);
    }


    public function saveFilter(Request $request)
    {
        $this->clearSesions($request);

        if (!empty($request->start_at_ini))
            $request->session()->put('appointment_deleted_filter_start_at_ini', $request->start_at_ini);

        if (!empty($request->start_at_end))
            $request->session()->put('appointment_deleted_filter_start_at_end', $request->start_at_end);

        if (!empty($request->service_id))
            $request->session()->put('appointment_deleted_filter_service_id', $request->service_id);

        if (!empty($request->state))
            $request->session()->put('appointment_deleted_filter_state', $request->state);
        if (!empty($request->user_id))
            $request->session()->put('appointment_deleted_filter_user_id', $request->user_id);

        if (!empty($request->doctor_id))
            $request->session()->put('appointment_deleted_filter_doctor_id', $request->doctor_id);

        return redirect('admin/appointments-deleted');
    }

    public function removeFilter(Request $request)
    {
        $this->clearSesions($request);
        return redirect('admin/appointments-deleted');
    }

    private function addFilter(&$query)
    {


        if (!empty($this->filtStartAtIni)) {
            $date = Carbon::createFromFormat("d/m/Y", $this->filtStartAtIni)->startOfDay()->format("Y-m-d H:i:s");
            $query->where("appointments.start_at", ">=", $date);
        }
        if (!empty($this->filtStartAtEnd)) {
            $date = Carbon::createFromFormat("d/m/Y", $this->filtStartAtEnd)->endOfDay()->format("Y-m-d H:i:s");
            $query->where("appointments.start_at", "<=", $date);
        }
        if (count($this->filtDoctorId) > 0) {
            $query->whereIn("appointments.doctor_id", $this->filtDoctorId);
        }
        if (count($this->filtUserId) > 0) {
            $query->whereIn("appointments.user_id", $this->filtUserId);
        }
        if (count($this->filtState) > 0) {
            $query->whereIn("appointments.state", $this->filtState);
        }
        if (count($this->filtServiceId) > 0) {
            $query->whereIn("appointments.service_id", $this->filtServiceId);
        }
    }

    private function clearSesions($request)
    {
        $request->session()->forget('appointment_deleted_filter_start_at_ini');
        $request->session()->forget('appointment_deleted_filter_start_at_end');
        $request->session()->forget('appointment_deleted_filter_user_id');
        $request->session()->forget('appointment_deleted_filter_doctor_id');
        $request->session()->forget('appointment_deleted_filter_service_id');
        $request->session()->forget('appointment_deleted_filter_state');
    }

    public function getData()
    {
        if (
            !auth()->user()->isAbleTo('admin-appointments-deleted-list')
        ) {

            app()->abort(403);
        }
        $query = Appointment::select([
            'appointments.id',
            'appointments.start_at',
            'appointments.deleted_at',
            'appointments.total',
            'appointments.state',
            'appointments.color',
            // 'appointments.image',
            DB::raw('CONCAT(patient.first_name, " ", patient.last_name) as patient'),
            DB::raw('CONCAT(doctor.first_name, " ", doctor.last_name) as doctor'),
            'services.name as service',
        ])
            ->onlyTrashed()
            ->selectedCenter()
            ->canList()
            ->distinct()
            ->join("user_profiles as patient", "appointments.user_id", "=", "patient.user_id")
            ->join("services", "services.id", "=", "appointments.service_id")
            ->join("user_profiles as doctor", "appointments.doctor_id", "=", "doctor.user_id");
        $this->addFilter($query);


        $table = DataTables::of($query);

        $table->filterColumn('start_at', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(appointments.start_at, '%d/%m/%Y') like ?", ["%{$keyword}%"]);
        });
        $table->filterColumn('deleted_at', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(appointments.deleted_at, '%d/%m/%Y') like ?", ["%{$keyword}%"]);
        });

        $table->filterColumn('patient', function ($query, $keyword) {
            $query->whereRaw("CONCAT(patient.first_name, ' ', patient.last_name)  like ?", ["%{$keyword}%"]);
        });

        $table->filterColumn('doctor', function ($query, $keyword) {
            $query->whereRaw("CONCAT(doctor.first_name, ' ', doctor.last_name) like ?", ["%{$keyword}%"]);
        });


        $table->editColumn('start_at', function ($data) {

            if (!empty($data->start_at)) {
                $fecha = Carbon::createFromFormat("Y-m-d H:i:s", $data->start_at);
                return [
                    'display' => e($fecha->format("d/m/Y H:i")),
                    'timestamp' => $fecha->timestamp
                ];
            }
            return [
                'display' => null,
                'timestamp' => null
            ];
        });
        $table->editColumn('deleted_at', function ($data) {

            if (!empty($data->deleted_at)) {
                $fecha = Carbon::createFromFormat("Y-m-d H:i:s", $data->deleted_at);
                return [
                    'display' => e($fecha->format("d/m/Y H:i")),
                    'timestamp' => $fecha->timestamp
                ];
            }
            return [
                'display' => null,
                'timestamp' => null
            ];
        });
        $table->editColumn('state', function ($data) {



            return  '<span class="badge" style="background-color:' . $data->color . '" >' . ucfirst($data->state) . '</span>';
        });


        $table->editColumn('actions', function ($data) {
            $actions = '';
            $appointment = Appointment::withTrashed()->find($data->id);

            if ($appointment->canRestaurarTrash()) {
                $actions .= '<button class="btn btn-primary ms-1 mt-1  btn-xs" data-bs-content="' . trans('appointments_deleted/admin_lang.restaurar') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:restaurarElement(\'' .
                    url('admin/appointments-deleted/restaurar/' . $data->id) . '\');" data-content="' .
                    trans('appointments_deleted/admin_lang.resturar') . '" data-placement="left" data-toggle="popover">
                    <i class="fas fa-check" aria-hidden="true"></i>';
            }

            if ($appointment->canDeleteTrash()) {
                $actions .= '<button class="btn btn-danger ms-1 mt-1  btn-xs" data-bs-content="' . trans('general/admin_lang.delete') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    url('admin/appointments-deleted/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }

            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'state']);
        return $table->make();
    }

    public function restaurar($id)
    {

        try {
            // Si no tiene permisos para modificar lo echamos
            $appointment = Appointment::withTrashed()->find($id);

            if (empty($appointment->id)) {
                app()->abort(404);
            }

            if ($appointment->canRestaurarTrash()) {
                $appointment->restore();

                return response()->json(array(
                    'success' => true,
                    'msg' => trans("general/admin_lang.delete_ok"),
                ));
            }
            return response()->json(array(
                'success' => false,
                'msg' => trans("general/admin_lang.delete_ko"),
            ));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(array(
                'success' => true,
                'msg' => trans("general/admin_lang.save_ko"),
            ));
        }
        // Si no tiene permisos para modificar lo echamos

    }

    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        $appointment = Appointment::withTrashed()->find($id);

        if (empty($appointment->id)) {
            app()->abort(404);
        }

        if ($appointment->canDeleteTrash()) {
            $appointment->forceDelete();

            return response()->json(array(
                'success' => true,
                'msg' => trans("general/admin_lang.delete_ok"),
            ));
        }
        return response()->json(array(
            'success' => false,
            'msg' => trans("general/admin_lang.delete_ko"),
        ));
    }

    public function exportExcel()
    {
        if (
            !auth()->user()->isAbleTo('admin-appointments-deleted-list')
        ) {

            app()->abort(403);
        }
        $query = Appointment::select([
            'appointments.*',

        ])
            ->onlyTrashed()
            ->selectedCenter()
            ->canList()
            ->distinct()
            ->join("user_profiles as patient", "appointments.user_id", "=", "patient.user_id")
            ->join("services", "services.id", "=", "appointments.service_id")
            ->join("user_profiles as doctor", "appointments.doctor_id", "=", "doctor.user_id");
        $this->addFilter($query);
        return Excel::download(new AdminappointmentsExport($query), strtolower(trans('appointments_deleted/admin_lang.appointments')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }
}
