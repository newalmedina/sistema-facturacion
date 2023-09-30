<?php

namespace App\Http\Controllers;

use App\Exports\AdminPatientMedicalStudiesExport;
use App\Http\Requests\AdminPatientMedicalStudiesRequest;
use App\Models\Center;
use App\Models\InsuranceCarrier;
use App\Models\Municipio;
use App\Models\PatientMedicalStudies;
use App\Models\PatientMedicalStudiesDetail;
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

class AdminPatientMedicalStudieController extends Controller
{
    public $filtCenterId;
    public $filtStartData;
    public $filtEndData;
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->filtStartData = ($request->session()->has('patient_medical_studies_start_date')) ? ($request->session()->get('patient_medical_studies_start_date')) : "";
            $this->filtEndData = ($request->session()->has('patient_medical_studies_end_date')) ? ($request->session()->get('patient_medical_studies_end_date')) : "";
            $this->filtCenterId = ($request->session()->has('patient_medical_studies_center_id')) ? ($request->session()->get('patient_medical_studies_center_id')) : [];
            return $next($request);
        });
    }

    public function index($patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies')) {
            app()->abort(403);
        }


        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        $patient = User::find($patient_id);

        $pageTitle = trans('patient-medical-studies/admin_lang.list');
        $title = trans('patients/admin_lang.patients');
        $centerList = Center::active()->get();
        $tab = "tab_5";
        $notImage = true;
        return view('patient-medical-studies.admin_index', compact('pageTitle', 'title', 'centerList', "patient", "tab", "notImage"))
            ->with([
                'filtCenterId' => $this->filtCenterId,
                'filtStartData' => $this->filtStartData,
                'filtEndData' => $this->filtEndData,
            ]);
    }

    public function create($patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-create')) {
            app()->abort(403);
        }
        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        $patient = User::find($patient_id);

        $pageTitle = trans('patient-medical-studies/admin_lang.patient-medical-studies');
        $title = trans('patients/admin_lang.patients');

        $medicalStudies = new PatientMedicalStudies();

        $tab = 'tab_5';

        $notImage = true;


        return view('patient-medical-studies.admin_edit', compact('pageTitle', 'title', 'patient', 'medicalStudies', 'notImage'))
            ->with('tab', $tab);
    }

    public function show($patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-update')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $medicalStudies = PatientMedicalStudies::where("id", $id)
            ->where("user_id", $patient_id)->first();

        if (empty($patient) || empty($medicalStudies->id)) {
            app()->abort(404);
        }

        $patient = User::find($patient_id);
        $pageTitle = trans('patient-medical-studies/admin_lang.patient-medical-studies');
        $title = trans('patients/admin_lang.patients');

        $tab = 'tab_5';

        $notImage = true;
        $disabled = "disabled";

        return view('patient-medical-studies.admin_edit', compact('pageTitle', 'title', 'patient', 'medicalStudies', 'notImage', "disabled"))
            ->with('tab', $tab);
    }

    public function copy($patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-create')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $medicalStudies = PatientMedicalStudies::where("id", $id)
            ->where("user_id", $patient_id)->first();

        if (empty($patient) || empty($medicalStudies->id)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();
            $newMedicine = $medicalStudies->replicate();
            $newMedicine->created_by = Auth::user()->id;
            $newMedicine->user_id = $patient_id;
            $newMedicine->date = Carbon::now();
            $newMedicine->center_id =  Auth::user()->hasSelectedCenter();
            $newMedicine->save();


            DB::commit();
            return redirect()->route('admin.patients.medical-studies.edit', ["patient_id" => $patient_id, "id" => $newMedicine->id])->with('success', trans('general/admin_lang.save_ok'));
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
        $medicalStudies = PatientMedicalStudies::where("id", $id)
            ->where("user_id", $patient_id)
            ->where("created_by", Auth::user()->id)
            ->first();

        if (empty($patient) || empty($medicalStudies->id)) {
            app()->abort(404);
        }

        $data = [
            'doctor_info' => trans("pdfLayout/admin_lang.doctor_info"),
            'title' => trans("pdfLayout/admin_lang.medical_studies"),
            'info' => $medicalStudies,
            'date' => Carbon::parse($medicalStudies->date)->format("d/m/Y"),
            'doctorInfo' => $medicalStudies->createdBy
        ];

        $pdf = PDF::loadView('pdf.partials.studies', $data);

        return $pdf->download(
            trans('patient-medical-studies/admin_lang.patient-medical-studies-export') . '_' . Carbon::now()->format("dmYHis") . '.pdf'
        );
    }

    public function edit($patient_id, $id)
    {
        $medicalStudies = PatientMedicalStudies::where("id", $id)->where("user_id", $patient_id)->first();
        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient) || empty($medicalStudies->id)) {
            app()->abort(404);
        }

        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-update-all') && !auth()->user()->isAbleTo('admin-patients-medical-studies-update')) {
            app()->abort(403);
        }


        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-update-all') && auth()->user()->isAbleTo('admin-patients-medical-studies-update') && $medicalStudies->created_by != Auth::user()->id) {
            app()->abort(403);
        }



        $patient = User::find($patient_id);
        $pageTitle = trans('patient-medical-studies/admin_lang.patient-medical-studies');
        $title = trans('patients/admin_lang.patients');

        $tab = 'tab_5';

        $notImage = true;


        return view('patient-medical-studies.admin_edit', compact('pageTitle', 'title', 'patient', 'medicalStudies', 'notImage'))
            ->with('tab', $tab);
    }

    public function store(AdminPatientMedicalStudiesRequest $request, $patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-create')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();
            $medicalStudies = new PatientMedicalStudies();
            $medicalStudies->created_by = Auth::user()->id;
            $medicalStudies->user_id = $patient_id;
            $medicalStudies->center_id =  Auth::user()->hasSelectedCenter();

            $this->saveMedicalStudies($medicalStudies, $request);


            DB::commit();
            return redirect()->route('admin.patients.medical-studies.edit', ["patient_id" => $patient_id, "id" => $medicalStudies->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/patients/create/')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function update(AdminPatientMedicalStudiesRequest $request,  $patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-update')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $medicalStudies =  PatientMedicalStudies::find($id);

        if (empty($patient) || empty($medicalStudies->id)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();

            $this->saveMedicalStudies($medicalStudies, $request);


            DB::commit();
            return redirect()->route('admin.patients.medical-studies.edit', ["patient_id" => $patient_id, "id" => $medicalStudies->id])->with('success', trans('general/admin_lang.save_ok'));
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
            $request->session()->put('patient_medical_studies_center_id', $request->center_id);

        if (!empty($request->start_date))
            $request->session()->put('patient_medical_studies_start_date', $request->start_date);

        if (!empty($request->end_date))
            $request->session()->put('patient_medical_studies_end_date', $request->end_date);


        return redirect('admin/patients/' . $patient_id . '/medical-studies');
    }

    public function removeFilter(Request $request, $patient_id)
    {
        $this->clearSesions($request);
        return redirect('admin/patients/' . $patient_id . '/medical-studies');
    }

    private function addFilter(&$query)
    {

        if (count($this->filtCenterId) > 0) {
            $query->whereIn("patient_medical_studies.center_id", $this->filtCenterId);
        }

        if (!empty($this->filtStartData)) {
            $date = Carbon::createFromFormat("d/m/Y", $this->filtStartData)->format("Y-m-d");
            $query->where("patient_medical_studies.date", ">=", $date);
        }
        if (!empty($this->filtEndData)) {
            $date = Carbon::createFromFormat("d/m/Y", $this->filtEndData)->format("Y-m-d");
            $query->where("patient_medical_studies.date", "<=", $date);
        }
    }

    private function clearSesions($request)
    {
        $request->session()->forget('patient_medical_studies_center_id');
        $request->session()->forget('patient_medical_studies_start_date');
        $request->session()->forget('patient_medical_studies_end_date');
    }

    public function getData($patient_id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-list')) {
            app()->abort(403);
        }

        $query = PatientMedicalStudies::select(
            [
                "patient_medical_studies.user_id",
                "patient_medical_studies.id",
                "patient_medical_studies.description",
                "patient_medical_studies.date",
                "created.id as creador",
                DB::raw('CONCAT(created.first_name, " ", created.last_name) as created_by'),
                "centers.name as center",
                "patient_medical_studies.center_id",
            ]
        )
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "patient_medical_studies.user_id")
            ->leftJoin("user_profiles as created", "created.user_id", "=", "patient_medical_studies.created_by")
            ->leftJoin("centers", "centers.id", "=", "patient_medical_studies.center_id")
            ->where("patient_medical_studies.user_id", $patient_id)
            ->distinct();

        $this->addFilter($query);

        $table = DataTables::of($query);

        $table->filterColumn('date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(patient_medical_studies.date, '%d/%m/%Y') like ?", ["%{$keyword}%"]);
        });

        $table->filterColumn('created_by', function ($query, $keyword) {
            $query->whereRaw("CONCAT(created.first_name,' ',created.last_name) like ?", ["%{$keyword}%"]);
        });


        $table->editColumn('date', function ($data) {

            $fecha = Carbon::createFromFormat("Y-m-d", $data->date);
            return [
                'display' => e($fecha->format("d/m/Y")),
                'timestamp' => $fecha->timestamp
            ];
        });

        $table->editColumn('description', function ($data) {
            return  UtilsServices::makeTextShort(strip_tags($data->description), 100);
        });


        $table->editColumn('actions', function ($data) {
            $actions = '';

            if (auth()->user()->isAbleTo("admin-patients-medical-studies-read")) {
                $actions .= '<a  class="btn btn-info btn-xs" data-bs-content="' . trans('general/admin_lang.show') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.medical-studies.show', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                    class="fa fa-eye fa-lg"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-medical-studies-update-all")) {
                $actions .= '<a  class="btn btn-primary btn-xs" data-bs-content="' . trans('general/admin_lang.edit') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.medical-studies.edit', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            } elseif (auth()->user()->isAbleTo("admin-patients-medical-studies-update") && $data->creador == Auth::user()->id) {
                $actions .= '<a  class="btn btn-primary btn-xs" data-bs-content="' . trans('general/admin_lang.edit') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.medical-studies.edit', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }
            if ($data->creador == Auth::user()->id) {
                $actions .= '<a  class="btn btn-danger btn-xs"href="' . route('admin.patients.medical-studies.generatePdf', ["patient_id" => $data->user_id, "id" => $data->id]) . '""><i
                    class="fa fa-file-pdf fa-lg"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-medical-studies-create")) {
                $actions .= '<a  class="btn btn-success btn-xs" href="javascript:void(0);"  onclick="javascript:copyElement(\'' .
                    route('admin.patients.medical-studies.copy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');"><i
                    class="fa fa-copy fa-lg"></i></a> ';
            }

            if (auth()->user()->isAbleTo("admin-patients-medical-studies-delete-all")) {

                $actions .= '<button class="btn btn-danger btn-xs" data-bs-content="' . trans('general/admin_lang.delete') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    route('admin.patients.medical-studies.destroy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            } elseif (auth()->user()->isAbleTo("admin-patients-medical-studies-delete") && $data->creador == Auth::user()->id) {
                $actions .= '<button class="btn btn-danger btn-xs" data-bs-content="' . trans('general/admin_lang.delete') . '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    route('admin.patients.medical-studies.destroy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }


            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'description']);
        return $table->make();
    }

    public function destroy($patient_id, $id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-delete')) {
            app()->abort(403);
        }
        $medicina = PatientMedicalStudies::where("id", $id)
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
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-list')) {
            app()->abort(403);
        }

        $query = PatientMedicalStudies::select(
            [
                "patient_medical_studies.user_id",
                "patient_medical_studies.id",
                "patient_medical_studies.description",
                "patient_medical_studies.date",
                DB::raw('CONCAT(created.first_name, " ", created.last_name) as created_by'),
                DB::raw('CONCAT(user_profiles.first_name, " ", user_profiles.last_name) as patient'),
                "centers.name as center",
            ]
        )
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "patient_medical_studies.user_id")
            ->leftJoin("user_profiles as created", "created.user_id", "=", "patient_medical_studies.created_by")
            ->leftJoin("centers", "centers.id", "=", "patient_medical_studies.center_id")
            ->where("patient_medical_studies.user_id", $patient_id)
            ->distinct();


        $this->addFilter($query);

        return Excel::download(new AdminPatientMedicalStudiesExport($query), strtolower(trans('patient-medical-studies/admin_lang.patient-medical-studies-export')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function saveMedicalStudies($medicalStudies, $request)
    {
        $medicalStudies->date = Carbon::createFromFormat("d/m/Y", $request->date);
        $medicalStudies->description =  $request->description;
        $medicalStudies->save();
    }
}
