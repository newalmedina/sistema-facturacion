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

class AdminAppointmentsController extends Controller
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
            $this->filtStartAtIni = ($request->session()->has('appointment_filter_start_at_ini')) ? ($request->session()->get('appointment_filter_start_at_ini')) : "";
            $this->filtStartAtEnd = ($request->session()->has('appointment_filter_start_at_end')) ? ($request->session()->get('appointment_filter_start_at_end')) : "";
            $this->filtDoctorId = ($request->session()->has('appointment_filter_doctor_id')) ? ($request->session()->get('appointment_filter_doctor_id')) : [];
            $this->filtUserId = ($request->session()->has('appointment_filter_user_id')) ? ($request->session()->get('appointment_filter_user_id')) : [];
            $this->filtState = ($request->session()->has('appointment_filter_state')) ? ($request->session()->get('appointment_filter_state')) : [];
            $this->filtServiceId = ($request->session()->has('appointment_filter_service_id')) ? ($request->session()->get('appointment_filter_service_id')) : [];
            return $next($request);
        });
    }

    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-appointments')) {
            app()->abort(403);
        }

        $pageTitle = trans('appointments/admin_lang.appointments');
        $title = trans('appointments/admin_lang.list');
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

        return view('appointments.admin_index', compact('pageTitle', 'title', 'patientList', 'doctorList', "stateList", "serviceList"))
            ->with([
                "filtStartAtIni" => $this->filtStartAtIni,
                "filtStartAtEnd" => $this->filtStartAtEnd,
                "filtDoctorId" => $this->filtDoctorId,
                "filtUserId" => $this->filtUserId,
                "filtState" => $this->filtState,
                "filtServiceId" => $this->filtServiceId,
            ]);
    }

    public function create()
    {
        if (!auth()->user()->isAbleTo('admin-appointments-create')) {
            app()->abort(403);
        }
        $pageTitle = trans('appointments/admin_lang.new');
        $title = trans('appointments/admin_lang.list');
        $appointment = new Appointment();


        $disabledForm = "";
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

        $insuranceList = $this->getInsuraces(0);
        return view('appointments.admin_edit', compact('pageTitle', 'title', "appointment", "patientList", 'doctorList', 'serviceList', 'disabledForm', 'insuranceList'));
    }

    public function store(AdminAppointmentRequest $request)
    {
        if (!auth()->user()->isAbleTo('admin-appointments-create')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $appointment = new Appointment();

            $appointment = new Appointment();
            $appointment->created_by = Auth::user()->id;
            $appointment->state = "pendiente";
            $appointment->center_id = Auth::user()->hasSelectedCenter();
            $appointment->title = "Cita medica";
            $this->saveAppointment($appointment, $request);

            DB::commit();

            return redirect()->route('admin.appointments.edit', [$appointment->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/appointments/create')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $appointment = Appointment::selectedCenter()
            ->where("id", $id)->first();

        if (empty($appointment->id)) {
            app()->abort(404);
        }

        if (!$appointment->canShow()) {
            app()->abort(403);
        }



        $pageTitle = trans('appointments/admin_lang.edit');
        $title = trans('appointments/admin_lang.list');

        $disabledForm = "disabled";
        $insuranceList = $this->getInsuraces($appointment->user_id);

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
        $showMode = true;

        return view('appointments.admin_edit', compact('pageTitle', 'title', "appointment", "patientList", 'doctorList', 'serviceList', 'disabledForm', 'insuranceList', 'showMode'));
    }

    public function edit($id)
    {
        $appointment = Appointment::selectedCenter()
            ->where("id", $id)->first();

        if (empty($appointment->id)) {
            app()->abort(404);
        }

        if (!$appointment->canEdit()) {
            app()->abort(403);
        }



        $pageTitle = trans('appointments/admin_lang.edit');
        $title = trans('appointments/admin_lang.list');

        $disabledForm = "";
        if ($appointment->state != "pendiente") {
            $disabledForm = "disabled";
        }

        $insuranceList = $this->getInsuraces($appointment->user_id);

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

        return view('appointments.admin_edit', compact('pageTitle', 'title', "appointment", "patientList", 'doctorList', 'serviceList', 'disabledForm', 'insuranceList'));
    }

    public function update(AdminAppointmentRequest $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment->canEdit()) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();


            $this->saveAppointment($appointment, $request);

            DB::commit();


            return redirect()->route('admin.appointments.edit', [$appointment->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/appointments/create/' . $appointment->id)
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function saveFilter(Request $request)
    {
        $this->clearSesions($request);

        if (!empty($request->start_at_ini))
            $request->session()->put('appointment_filter_start_at_ini', $request->start_at_ini);

        if (!empty($request->start_at_end))
            $request->session()->put('appointment_filter_start_at_end', $request->start_at_end);

        if (!empty($request->service_id))
            $request->session()->put('appointment_filter_service_id', $request->service_id);

        if (!empty($request->state))
            $request->session()->put('appointment_filter_state', $request->state);
        if (!empty($request->user_id))
            $request->session()->put('appointment_filter_user_id', $request->user_id);

        if (!empty($request->doctor_id))
            $request->session()->put('appointment_filter_doctor_id', $request->doctor_id);

        return redirect('admin/appointments');
    }
    public function removeFilter(Request $request)
    {
        $this->clearSesions($request);
        return redirect('admin/appointments');
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
        $request->session()->forget('appointment_filter_start_at_ini');
        $request->session()->forget('appointment_filter_start_at_end');
        $request->session()->forget('appointment_filter_user_id');
        $request->session()->forget('appointment_filter_doctor_id');
        $request->session()->forget('appointment_filter_service_id');
        $request->session()->forget('appointment_filter_state');
    }

    public function getData()
    {
        if (
            !auth()->user()->isAbleTo('admin-appointments-list-all') &&
            !auth()->user()->isAbleTo('admin-appointments-list-created-by-user') &&
            !auth()->user()->isAbleTo('admin-appointments-list-doctor')
        ) {

            app()->abort(403);
        }
        $query = Appointment::select([
            'appointments.id',
            'appointments.start_at',
            'appointments.total',
            'appointments.state',
            'appointments.color',
            // 'appointments.image',
            DB::raw('CONCAT(patient.first_name, " ", patient.last_name) as patient'),
            DB::raw('CONCAT(doctor.first_name, " ", doctor.last_name) as doctor'),
            'services.name as service',
        ])
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
        $table->editColumn('state', function ($data) {



            return  '<span class="badge" style="background-color:' . $data->color . '" >' . ucfirst($data->state) . '</span>';
        });


        $table->editColumn('actions', function ($data) {
            $actions = '';
            $appointment = Appointment::find($data->id);

            if ($appointment->canShow()) {
                $actions .= '<a  class="btn btn-info btn-xs  mt-1" data-bs-content="' . trans('general/admin_lang.show') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.appointments.show', $data->id) . '" ><i
                class="fa fa-eye fa-lg"></i></a> ';
            }
            if ($appointment->canEdit()) {
                $actions .= '<a  class="btn btn-primary btn-xs  mt-1" data-bs-content="' . trans('general/admin_lang.edit') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.appointments.edit', $data->id) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }
            if ($appointment->canFacturar()) {
                $actions .= '<button class="btn btn-warning ms-1 mt-1  btn-xs" data-bs-content="' . trans('appointments/admin_lang.facturar') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:facturarElement(\'' .
                    url('admin/appointments/facturar/' . $data->id) . '\');" data-content="' .
                    trans('appointments/admin_lang.finalizar') . '" data-placement="left" data-toggle="popover">
                    <i class="fas fa-dollar-sign" aria-hidden="true"></i></button>';
            }
            if ($appointment->canFinalizar()) {
                $actions .= '<button class="btn btn-primary ms-1 mt-1  btn-xs" data-bs-content="' . trans('appointments/admin_lang.finalizar') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:finalizarElement(\'' .
                    url('admin/appointments/finalizar/' . $data->id) . '\');" data-content="' .
                    trans('appointments/admin_lang.facturar') . '" data-placement="left" data-toggle="popover">
                    <i class="fas fa-check" aria-hidden="true"></i>';
            }
            if ($appointment->canDelete()) {
                $actions .= '<button class="btn btn-danger ms-1 mt-1  btn-xs" data-bs-content="' . trans('general/admin_lang.delete') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    url('admin/appointments/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }

            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'state']);
        return $table->make();
    }

    public function facturar($id)
    {

        try {
            DB::beginTransaction();

            $appointment = Appointment::find($id);

            if (empty($appointment->id)) {
                app()->abort(404);
            }

            if (!$appointment->canFacturar()) {
                app()->abort(403);
            }
            $appointment->paid_at = Carbon::now();
            $appointment->paid_by = Auth::user()->id;

            $appointment->color = "#ffc107";
            $appointment->state = "facturado";
            $appointment->save();

            DB::commit();
            return response()->json(array(
                'success' => true,
                'msg' => trans("general/admin_lang.save_ok"),
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
    public function finalizar($id)
    {
        try {
            DB::beginTransaction();

            $appointment = Appointment::find($id);

            if (empty($appointment->id)) {
                app()->abort(404);
            }

            if (!$appointment->canFinalizar()) {
                app()->abort(403);
            }
            $appointment->finish_at = Carbon::now();
            $appointment->finish_by = Auth::user()->id;
            $appointment->color = "#28a745";

            $appointment->state = "finalizado";
            $appointment->save();

            DB::commit();


            return response()->json(array(
                'success' => true,
                'msg' => trans("general/admin_lang.save_ok"),
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
        $appointment = Appointment::find($id);

        if (empty($appointment->id)) {
            app()->abort(404);
        }

        if ($appointment->canDelete()) {

            $appointment->delete();

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

    public function getInsuraces($user_id)
    {
        return  InsuranceCarrier::active()
            ->select("insurance_carriers.id", "insurance_carriers.name", "patient_insurance_carriers.poliza")
            ->join("patient_insurance_carriers", "insurance_carriers.id", "patient_insurance_carriers.insurance_carrier_id")
            ->where("patient_insurance_carriers.user_id", $user_id)
            ->get();
    }
    public function getInsurancesPrice($user_id, $service_id, $insurance_id)
    {
        $service = DB::table("service_insurance_carriers")
            ->select("service_insurance_carriers.id", "service_insurance_carriers.price as price_insurance",)
            ->leftJoin("patient_insurance_carriers", "patient_insurance_carriers.insurance_carrier_id", "service_insurance_carriers.insurance_carrier_id")
            ->where("service_insurance_carriers.insurance_carrier_id", $insurance_id)
            ->where("service_insurance_carriers.service_id",  $service_id)
            ->where("patient_insurance_carriers.user_id",  $user_id)
            ->first();

        $object = new stdClass();
        $object->price_insurance = !empty($service->price_insurance) ? $service->price_insurance : 0;
        $object->validServiceInsurance = !empty($service->price_insurance) ? true : false;

        return $object;
    }



    public function exportExcel()
    {
        if (
            !auth()->user()->isAbleTo('admin-appointments-list-all') &&
            !auth()->user()->isAbleTo('admin-appointments-list-created-by-user') &&
            !auth()->user()->isAbleTo('admin-appointments-list-doctor')
        ) {

            app()->abort(403);
        }
        $query = Appointment::select([
            'appointments.*',

        ])
            ->selectedCenter()
            ->canList()
            ->distinct()
            ->join("user_profiles as patient", "appointments.user_id", "=", "patient.user_id")
            ->join("services", "services.id", "=", "appointments.service_id")
            ->join("user_profiles as doctor", "appointments.doctor_id", "=", "doctor.user_id");
        $this->addFilter($query);
        return Excel::download(new AdminappointmentsExport($query), strtolower(trans('appointments/admin_lang.appointments')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function saveAppointment($appointment, $request)
    {
        $appointment->user_id = $request->user_id;
        $appointment->doctor_id = $request->doctor_id;
        $start = Carbon::parse($request->start_at . " " . $request->hour);
        $end = $start;
        $appointment->start_at = $start->format("Y-m-d H:i");
        $appointment->end_at = $end->addMinutes(15)->format("Y-m-d H:i");
        $appointment->service_id = $request->service_id;
        $appointment->insurance_carrier_id = !empty($request->insurance_carrier_id) ? $request->insurance_carrier_id : null;
        $appointment->applicated_insurance = !empty($request->applicated_insurance) &&  !empty($request->insurance_carrier_id) ? $request->applicated_insurance : 0;
        $appointment->comment = $request->comment;
        $appointment->color = "#6c757d";

        $service = Service::find($request->service_id);
        $appointment->price = $service->price;

        $price_with_insurance = 0;
        $total = $service->price;
        if (!empty($appointment->user_id) && !empty($appointment->service_id) && !empty($appointment->insurance_carrier_id)) {
            $priceInsuranceData = $this->getInsurancesPrice($appointment->user_id, $appointment->service_id, $appointment->insurance_carrier_id);
            $price_with_insurance = $priceInsuranceData->price_insurance;
            if ($appointment->applicated_insurance) {
                $total = $priceInsuranceData->price_insurance;
            }
        }

        $appointment->price_with_insurance = $price_with_insurance;

        $appointment->total = $total;

        $appointment->save();
    }
}
