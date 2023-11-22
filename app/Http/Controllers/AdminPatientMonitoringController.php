<?php

namespace App\Http\Controllers;

use App\Exports\AdminPatientMonitoringExport;
use App\Http\Requests\AdminPatientMonitoringsRequest;
use App\Models\Center;
use App\Models\Diagnosi;
use App\Models\InsuranceCarrier;
use App\Models\Municipio;
use App\Models\PatientMonitoring;
use App\Models\PatientMonitoringDetail;
use App\Models\PatientProfile;
use App\Models\Province;
use App\Models\User;
use App\Services\UtilsServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class AdminPatientMonitoringController extends Controller
{
    public $filtCenterId;
    public $filtStartData;
    public $filtEndData;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->filtStartData = ($request->session()->has('patient_monitoring_start_date')) ? ($request->session()->get('patient_monitoring_start_date')) : "";
            $this->filtEndData = ($request->session()->has('patient_monitoring_end_date')) ? ($request->session()->get('patient_monitoring_end_date')) : "";
            $this->filtCenterId = ($request->session()->has('patient_monitoring_center_id')) ? ($request->session()->get('patient_monitoring_center_id')) : [];
            return $next($request);
        });
    }

    public function index($patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring')) {
            app()->abort(403);
        }


        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        $patient = User::find($patient_id);

        $pageTitle = trans('patient-monitorings/admin_lang.list');
        $title = trans('patients/admin_lang.patients');
        $centerList = Center::active()->get();
        $tab = "tab_6";
        $notImage = true;
        return view('patient-monitorings.admin_index', compact('pageTitle', 'title', 'centerList', "patient", "tab", "notImage"))
            ->with([
                'filtCenterId' => $this->filtCenterId,
                'filtStartData' => $this->filtStartData,
                'filtEndData' => $this->filtEndData,
            ]);
    }

    public function create($patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-create')) {
            app()->abort(403);
        }
        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        $patient = User::find($patient_id);

        $pageTitle = trans('patient-monitorings/admin_lang.patient-monitorings');
        $title = trans('patients/admin_lang.patients');

        $patientMonitoring = new PatientMonitoring();
        $diagnosisList = Diagnosi::active()->orderBy("name", "asc")->get();
        $tab = 'tab_6';

        $notImage = true;
        $diagnosisSelected = $patientMonitoring->diagnosisIdArrayFormatted;

        return view('patient-monitorings.admin_edit', compact('pageTitle', 'title', 'patient', 'patientMonitoring', 'notImage', 'diagnosisList', 'diagnosisSelected'))
            ->with('tab', $tab);
    }

    public function show($patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-update')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $patientMonitoring = PatientMonitoring::where("id", $id)
            ->where("user_id", $patient_id)->first();

        if (empty($patient) || empty($patientMonitoring->id)) {
            app()->abort(404);
        }

        $patient = User::find($patient_id);
        $pageTitle = trans('patient-monitorings/admin_lang.patient-monitorings');
        $title = trans('patients/admin_lang.patients');

        $tab = 'tab_6';

        $notImage = true;
        $disabled = "disabled";
        $diagnosisList = Diagnosi::active()->orderBy("name", "asc")->get();
        $diagnosisSelected = $patientMonitoring->diagnosisIdArrayFormatted;

        return view('patient-monitorings.admin_edit', compact('pageTitle', 'title', 'patient', 'patientMonitoring', 'notImage', "disabled", 'diagnosisList', 'diagnosisSelected'))
            ->with('tab', $tab);
    }

    public function copy($patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-create')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $patientMonitoring = PatientMonitoring::where("id", $id)
            ->where("user_id", $patient_id)->first();

        if (empty($patient) || empty($patientMonitoring->id)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();
            $newMedicine = $patientMonitoring->replicate();
            $newMedicine->created_by = Auth::user()->id;
            $newMedicine->user_id = $patient_id;
            $newMedicine->date = Carbon::now();
            $newMedicine->center_id =  Auth::user()->hasSelectedCenter();
            $newMedicine->save();

            foreach ($patientMonitoring->details as $detail) {

                $newDetail = $detail->replicate();
                $newDetail->patient_monitoring_id = $newMedicine->id;
                $newDetail->save();
            }

            DB::commit();
            return redirect()->route('admin.patients.monitorings.edit', ["patient_id" => $patient_id, "id" => $newMedicine->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect('admin/patients/create/')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function generatePdf($patient_id, $id)
    {
        $patient = User::where("users.id", $patient_id)->patients()->first();
        $patientMonitoring = PatientMonitoring::where("id", $id)
            ->where("user_id", $patient_id)
            ->where("created_by", Auth::user()->id)
            ->first();

        if (empty($patient) || empty($patientMonitoring->id)) {
            app()->abort(404);
        }

        $data = [
            'doctor_info' => trans("pdfLayout/admin_lang.doctor_info"),
            'title' => trans("pdfLayout/admin_lang.receta_medica"),
            'info' => $patientMonitoring,
            'date' => Carbon::parse($patientMonitoring->date)->format("d/m/Y"),
            'doctorInfo' => $patientMonitoring->createdBy
        ];
        $pdf = PDF::loadView('pdf.partials.recetas', $data);

        return $pdf->download(
            trans('patient-monitorings/admin_lang.patient-monitorings-export') . '_' . Carbon::now()->format("dmYHis") . '.pdf'
        );
    }

    public function edit($patient_id, $id)
    {
        $patientMonitoring = PatientMonitoring::where("id", $id)->where("user_id", $patient_id)->first();
        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient) || empty($patientMonitoring->id)) {
            app()->abort(404);
        }

        if (!auth()->user()->isAbleTo('admin-patients-monitoring-update-all') && !auth()->user()->isAbleTo('admin-patients-monitoring-update')) {
            app()->abort(403);
        }


        if (!auth()->user()->isAbleTo('admin-patients-monitoring-update-all') && auth()->user()->isAbleTo('admin-patients-monitoring-update') && $patientMonitoring->created_by != Auth::user()->id) {
            app()->abort(403);
        }



        $patient = User::find($patient_id);
        $pageTitle = trans('patient-monitorings/admin_lang.patient-monitorings');
        $title = trans('patients/admin_lang.patients');

        $tab = 'tab_6';

        $notImage = true;

        $diagnosisList = Diagnosi::active()->orderBy("name", "asc")->get();
        $diagnosisSelected = $patientMonitoring->diagnosisIdArrayFormatted;


        return view('patient-monitorings.admin_edit', compact('pageTitle', 'title', 'patient', 'patientMonitoring', 'notImage', 'diagnosisList', 'diagnosisSelected'))
            ->with('tab', $tab);
    }

    public function store(AdminPatientMonitoringsRequest $request, $patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-create')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();
            $patientMonitoring = new PatientMonitoring();
            $patientMonitoring->created_by = Auth::user()->id;
            $patientMonitoring->user_id = $patient_id;
            $patientMonitoring->center_id =  Auth::user()->hasSelectedCenter();

            $this->saveMonitoring($patientMonitoring, $request);


            DB::commit();
            return redirect()->route('admin.patients.monitorings.edit', ["patient_id" => $patient_id, "id" => $patientMonitoring->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/patients/create/')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function update(AdminPatientMonitoringsRequest $request,  $patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-update')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $patientMonitoring =  PatientMonitoring::find($id);

        if (empty($patient) || empty($patientMonitoring->id)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();

            $this->saveMonitoring($patientMonitoring, $request);


            DB::commit();
            return redirect()->route('admin.patients.monitorings.edit', ["patient_id" => $patient_id, "id" => $patientMonitoring->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/patients/create/')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }



    public function saveFilter(Request $request, $patient_id)
    {
        $this->clearSesions($request);
        if (!empty($request->center_id))
            $request->session()->put('patient_monitoring_center_id', $request->center_id);

        if (!empty($request->start_date))
            $request->session()->put('patient_monitoring_start_date', $request->start_date);

        if (!empty($request->end_date))
            $request->session()->put('patient_monitoring_end_date', $request->end_date);


        return redirect('admin/patients/' . $patient_id . '/monitorings');
    }

    public function removeFilter(Request $request, $patient_id)
    {
        $this->clearSesions($request);
        return redirect('admin/patients/' . $patient_id . '/monitorings');
    }

    private function addFilter(&$query)
    {

        if (count($this->filtCenterId) > 0) {
            $query->whereIn("patient_monitorings.center_id", $this->filtCenterId);
        }

        if (!empty($this->filtStartData)) {
            $date = Carbon::createFromFormat("d/m/Y", $this->filtStartData)->format("Y-m-d");
            $query->where("patient_monitorings.date", ">=", $date);
        }
        if (!empty($this->filtEndData)) {
            $date = Carbon::createFromFormat("d/m/Y", $this->filtEndData)->format("Y-m-d");
            $query->where("patient_monitorings.date", "<=", $date);
        }
    }

    private function clearSesions($request)
    {
        $request->session()->forget('patient_monitoring_center_id');
        $request->session()->forget('patient_monitoring_start_date');
        $request->session()->forget('patient_monitoring_end_date');
    }

    public function getData($patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-list')) {
            app()->abort(403);
        }

        $query = PatientMonitoring::select(
            [
                "patient_monitorings.user_id",
                "patient_monitorings.id",
                "patient_monitorings.date",
                "patient_monitorings.motive",
                "created.id as creador",
                DB::raw('CONCAT(created.first_name, " ", created.last_name) as created_by'),
                "centers.name as center",
            ]
        )
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "patient_monitorings.user_id")
            ->leftJoin("user_profiles as created", "created.user_id", "=", "patient_monitorings.created_by")
            ->leftJoin("centers", "centers.id", "=", "patient_monitorings.center_id")
            ->where("patient_monitorings.user_id", $patient_id)
            ->distinct();

        $this->addFilter($query);

        $table = DataTables::of($query);

        $table->filterColumn('date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(patient_monitorings.date, '%d/%m/%Y') like ?", ["%{$keyword}%"]);
        });

        $table->filterColumn('created_by', function ($query, $keyword) {
            $query->whereRaw("CONCAT(created.first_name,' ',created.last_name) like ?", ["%{$keyword}%"]);
        });

        $table->editColumn('motive', function ($data) {
            return  UtilsServices::makeTextShort(strip_tags($data->motive), 100);
        });

        $table->editColumn('date', function ($data) {

            $fecha = Carbon::createFromFormat("Y-m-d", $data->date);
            return [
                'display' => e($fecha->format("d/m/Y")),
                'timestamp' => $fecha->timestamp
            ];
        });






        $table->editColumn('actions', function ($data) {
            $actions = '';

            if (auth()->user()->isAbleTo("admin-patients-monitoring-read")) {
                $actions .= '<a  class="btn btn-info btn-xs" data-bs-content="' . trans('general/admin_lang.show') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.monitorings.show', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                    class="fa fa-eye fa-lg"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-monitoring-update-all")) {
                $actions .= '<a  class="btn btn-primary btn-xs" data-bs-content="' . trans('general/admin_lang.edit') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.monitorings.edit', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            } elseif (auth()->user()->isAbleTo("admin-patients-monitoring-update") && $data->creador == Auth::user()->id) {
                $actions .= '<a  class="btn btn-primary btn-xs" data-bs-content="' . trans('general/admin_lang.edit') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.monitorings.edit', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }
            // if ($data->creador == Auth::user()->id) {
            //     $actions .= '<a  class="btn btn-danger btn-xs"href="' . route('admin.patients.monitorings.generatePdf', ["patient_id" => $data->user_id, "id" => $data->id]) . '""><i
            //         class="fa fa-file-pdf fa-lg"></i></a> ';
            // }
            // if (auth()->user()->isAbleTo("admin-patients-monitoring-create")) {
            //     $actions .= '<a  class="btn btn-success btn-xs" href="javascript:void(0);"  onclick="javascript:copyElement(\'' .
            //         route('admin.patients.monitorings.copy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');"><i
            //         class="fa fa-copy fa-lg"></i></a> ';
            // }

            if (auth()->user()->isAbleTo("admin-patients-monitoring-delete-all")) {

                $actions .= '<button class="btn btn-danger btn-xs" data-bs-content="' . trans('general/admin_lang.delete') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    route('admin.patients.monitorings.destroy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            } elseif (auth()->user()->isAbleTo("admin-patients-monitoring-delete") && $data->creador == Auth::user()->id) {
                $actions .= '<button class="btn btn-danger btn-xs" data-bs-content="' . trans('general/admin_lang.delete') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    route('admin.patients.monitorings.destroy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }


            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'motive']);
        return $table->make();
    }

    public function destroy($patient_id, $id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-delete')) {
            app()->abort(403);
        }
        $medicina = PatientMonitoring::where("id", $id)
            ->where("user_id", $patient_id)->first();
        if (empty($medicina)) {
            app()->abort(404);
        }

        $medicina->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }

    public function exportExcel($patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-monitoring-list')) {
            app()->abort(403);
        }

        $query = PatientMonitoring::select(
            [
                "patient_monitorings.user_id",
                "patient_monitorings.id",
                "patient_monitorings.date",
                DB::raw('CONCAT(created.first_name, " ", created.last_name) as created_by'),
                DB::raw('CONCAT(user_profiles.first_name, " ", user_profiles.last_name) as patient'),
                "centers.name as center",

                'patient_monitorings.height',
                'patient_monitorings.weight',
                'patient_monitorings.temperature',
                'patient_monitorings.heart_rate',
                'patient_monitorings.blood_presure',
                'patient_monitorings.rheumatoid_factor',
                'patient_monitorings.motive',
                'patient_monitorings.physical_exploration',
                'patient_monitorings.symptoms',
                'patient_monitorings.comment',

            ]
        )
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "patient_monitorings.user_id")
            ->leftJoin("user_profiles as created", "created.user_id", "=", "patient_monitorings.created_by")
            ->leftJoin("centers", "centers.id", "=", "patient_monitorings.center_id")
            ->where("patient_monitorings.user_id", $patient_id)
            ->distinct();


        $this->addFilter($query);

        return Excel::download(new AdminPatientMonitoringExport($query), strtolower(trans('patient-monitorings/admin_lang.patient-monitorings-export')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function saveMonitoring($patientMonitoring, $request)
    {
        // dd($request->all());
        $patientMonitoring->date = Carbon::createFromFormat("d/m/Y", $request->date);
        $patientMonitoring->height =  $request->height;
        $patientMonitoring->weight =  $request->weight;
        $patientMonitoring->temperature =  $request->temperature;
        $patientMonitoring->heart_rate =  $request->heart_rate;
        $patientMonitoring->blood_presure =  $request->blood_presure;
        $patientMonitoring->rheumatoid_factor =  $request->rheumatoid_factor;
        $patientMonitoring->motive =  $request->motive;
        $patientMonitoring->physical_exploration =  $request->physical_exploration;
        $patientMonitoring->symptoms =  $request->symptoms;
        $patientMonitoring->comment =  $request->comment;
        $patientMonitoring->save();
        $patientMonitoring->diagnosis()->sync($request->diagnosis_id);
    }
}
