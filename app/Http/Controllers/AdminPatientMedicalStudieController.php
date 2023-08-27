<?php

namespace App\Http\Controllers;

use App\Exports\AdminPatientMedicineExport;
use App\Http\Requests\AdminPatientMedicinesRequest;
use App\Models\Center;
use App\Models\InsuranceCarrier;
use App\Models\Municipio;
use App\Models\PatientMedicine;
use App\Models\PatientMedicineDetail;
use App\Models\PatientProfile;
use App\Models\Province;
use App\Models\User;
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
        $tab = "tab_4";
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

        $medicine = new PatientMedicine();

        $tab = 'tab_4';

        $notImage = true;


        return view('patient-medical-studies.admin_edit', compact('pageTitle', 'title', 'patient', 'medicine', 'notImage'))
            ->with('tab', $tab);
    }

    public function show($patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-update')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $medicine = PatientMedicine::where("id", $id)
            ->where("user_id", $patient_id)->first();

        if (empty($patient) || empty($medicine->id)) {
            app()->abort(404);
        }

        $patient = User::find($patient_id);
        $pageTitle = trans('patient-medical-studies/admin_lang.patient-medical-studies');
        $title = trans('patients/admin_lang.patients');

        $tab = 'tab_4';

        $notImage = true;
        $disabled = "disabled";

        return view('patient-medical-studies.admin_edit', compact('pageTitle', 'title', 'patient', 'medicine', 'notImage', "disabled"))
            ->with('tab', $tab);
    }

    public function copy($patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-create')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $medicine = PatientMedicine::where("id", $id)
            ->where("user_id", $patient_id)->first();

        if (empty($patient) || empty($medicine->id)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();
            $newMedicine = $medicine->replicate();
            $newMedicine->created_by = Auth::user()->id;
            $newMedicine->user_id = $patient_id;
            $newMedicine->date = Carbon::now();
            $newMedicine->center_id =  Auth::user()->hasSelectedCenter();
            $newMedicine->save();

            foreach ($medicine->details as $detail) {

                $newDetail = $detail->replicate();
                $newDetail->patient_medical_studies_id = $newMedicine->id;
                $newDetail->save();
            }

            DB::commit();
            return redirect()->route('admin.patients.medicines.edit', ["patient_id" => $patient_id, "id" => $newMedicine->id])->with('success', trans('general/admin_lang.save_ok'));
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
        $medicine = PatientMedicine::where("id", $id)
            ->where("user_id", $patient_id)
            ->where("created_by", Auth::user()->id)
            ->first();

        if (empty($patient) || empty($medicine->id)) {
            app()->abort(404);
        }

        $data = [
            'title' => trans("pdfLayout/admin_lang.doctor_info"),
            'info' => $medicine,
            'date' => Carbon::parse($medicine->date)->format("d/m/Y"),
            'doctorInfo' => $medicine->createdBy
        ];
        $pdf = PDF::loadView('pdf.partials.recetas', $data);

        return $pdf->stream(
            trans('patient-medical-studies/admin_lang.patient-medical-studies-export') . '_' . Carbon::now()->format("dmYHis") . '.pdf'
        );
    }

    public function edit($patient_id, $id)
    {
        $medicine = PatientMedicine::where("id", $id)->where("user_id", $patient_id)->first();
        $patient = User::where("users.id", $patient_id)->patients()->first();

        if (empty($patient) || empty($medicine->id)) {
            app()->abort(404);
        }

        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-update-all') && !auth()->user()->isAbleTo('admin-patients-medical-studies-update')) {
            app()->abort(403);
        }


        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-update-all') && auth()->user()->isAbleTo('admin-patients-medical-studies-update') && $medicine->created_by != Auth::user()->id) {
            app()->abort(403);
        }



        $patient = User::find($patient_id);
        $pageTitle = trans('patient-medical-studies/admin_lang.patient-medical-studies');
        $title = trans('patients/admin_lang.patients');

        $tab = 'tab_4';

        $notImage = true;


        return view('patient-medical-studies.admin_edit', compact('pageTitle', 'title', 'patient', 'medicine', 'notImage'))
            ->with('tab', $tab);
    }

    public function store(AdminPatientMedicinesRequest $request, $patient_id)
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
            $medicine = new PatientMedicine();
            $medicine->created_by = Auth::user()->id;
            $medicine->user_id = $patient_id;
            $medicine->center_id =  Auth::user()->hasSelectedCenter();

            $this->saveMedicine($medicine, $request);


            DB::commit();
            return redirect()->route('admin.patients.medicines.edit', ["patient_id" => $patient_id, "id" => $medicine->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/patients/create/')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function update(AdminPatientMedicinesRequest $request,  $patient_id, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-update')) {
            app()->abort(403);
        }

        $patient = User::where("users.id", $patient_id)->patients()->first();
        $medicine =  PatientMedicine::find($id);

        if (empty($patient) || empty($medicine->id)) {
            app()->abort(404);
        }

        try {
            DB::beginTransaction();

            $this->saveMedicine($medicine, $request);


            DB::commit();
            return redirect()->route('admin.patients.medicines.edit', ["patient_id" => $patient_id, "id" => $medicine->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/patients/create/')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function clinicalRecord($id)
    {
        $disabled = null;
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-clinic-record-update') && !auth()->user()->isAbleTo('admin-patients-medical-studies-clinic-record-read')) {
            app()->abort(403);
        }
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-clinic-record-update') && auth()->user()->isAbleTo('admin-patients-medical-studies-clinic-record-read')) {
            $disabled = "disabled";
        }

        $patient = User::where("users.id", $id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }

        $patient = User::find($id);
        $pageTitle = trans('patient-medical-studies/admin_lang.patient-medical-studies');
        $title = trans('patients/admin_lang.patients');
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
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-clinic-record-update')) {
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
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-insurance-carriers-update') && !auth()->user()->isAbleTo('admin-patients-medical-studies-insurance-carriers-read')) {
            app()->abort(403);
        }
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-insurance-carriers-update') && auth()->user()->isAbleTo('admin-patients-medical-studies-insurance-carriers-read')) {

            $disabled = "disabled";
        }

        $patient = User::where("users.id", $id)->patients()->first();

        if (empty($patient)) {
            app()->abort(404);
        }
        $patient = User::find($id);
        $pageTitle = trans('patient-medical-studies/admin_lang.patient-medical-studies');
        $title = trans('patients/admin_lang.patients');

        $insuranceList = InsuranceCarrier::active()->get();
        $patient = User::find($id);
        $tab = 'tab_3';

        return view('patients.admin_insurance_carrier', compact('pageTitle', 'title', 'insuranceList', 'patient', 'disabled'))
            ->with('tab', $tab);
    }

    public function insuranceCarrierUpdate(Request $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-insurance-carriers-update')) {
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

    public function saveFilter(Request $request, $patient_id)
    {
        $this->clearSesions($request);
        if (!empty($request->center_id))
            $request->session()->put('patient_medical_studies_center_id', $request->center_id);

        if (!empty($request->start_date))
            $request->session()->put('patient_medical_studies_start_date', $request->start_date);

        if (!empty($request->end_date))
            $request->session()->put('patient_medical_studies_end_date', $request->end_date);


        return redirect('admin/patients/' . $patient_id . '/medicines');
    }

    public function removeFilter(Request $request, $patient_id)
    {
        $this->clearSesions($request);
        return redirect('admin/patients/' . $patient_id . '/medicines');
    }

    private function addFilter(&$query)
    {

        if (count($this->filtCenterId) > 0) {
            $query->whereIn("patient_medical_studiess.center_id", [$this->filtCenterId]);
        }

        if (!empty($this->filtStartData)) {
            $date = Carbon::createFromFormat("d/m/Y", $this->filtStartData)->format("Y-m-d");
            $query->where("patient_medical_studiess.date", ">=", $date);
        }
        if (!empty($this->filtEndData)) {
            $date = Carbon::createFromFormat("d/m/Y", $this->filtEndData)->format("Y-m-d");
            $query->where("patient_medical_studiess.date", "<=", $date);
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

        $query = PatientMedicine::select(
            [
                "patient_medical_studiess.user_id",
                "patient_medical_studiess.id",
                "patient_medical_studiess.date",
                "created.id as creador",
                DB::raw('CONCAT(created.first_name, " ", created.last_name) as created_by'),
                "centers.name as center",
            ]
        )
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "patient_medical_studiess.user_id")
            ->leftJoin("user_profiles as created", "created.user_id", "=", "patient_medical_studiess.created_by")
            ->leftJoin("centers", "centers.id", "=", "patient_medical_studiess.center_id")
            ->leftJoin("patient_medical_studies_details", "patient_medical_studies_details.patient_medical_studies_id", "=", "patient_medical_studiess.id")
            ->where("patient_medical_studiess.user_id", $patient_id)
            ->distinct();

        $this->addFilter($query);

        $table = DataTables::of($query);

        $table->filterColumn('date', function ($query, $keyword) {
            $query->whereRaw("DATE_FORMAT(patient_medical_studiess.date, '%d/%m/%Y') like ?", ["%{$keyword}%"]);
        });

        $table->filterColumn('created_by', function ($query, $keyword) {
            $query->whereRaw("CONCAT(created.first_name,' ',created.last_name) like ?", ["%{$keyword}%"]);
        });
        $table->filterColumn('medicine', function ($query, $keyword) {
            $query->whereRaw("patient_medical_studies_details.medicine like ?", ["%{$keyword}%"]);
        });

        $table->editColumn('date', function ($data) {

            $fecha = Carbon::createFromFormat("Y-m-d", $data->date);
            return [
                'display' => e($fecha->format("d/m/Y")),
                'timestamp' => $fecha->timestamp
            ];
        });

        $table->editColumn('medicine', function ($data) {

            $medicines = PatientMedicineDetail::where("patient_medical_studies_id", $data->id)->pluck('medicine');
            return $medicines->implode(", ");
        });




        $table->editColumn('actions', function ($data) {
            $actions = '';

            if (auth()->user()->isAbleTo("admin-patients-medical-studies-read")) {
                $actions .= '<a  class="btn btn-info btn-xs" data-bs-content="' .trans('general/admin_lang.show') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.medicines.show', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                    class="fa fa-eye fa-lg"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-medical-studies-update-all")) {
                $actions .= '<a  class="btn btn-primary btn-xs" data-bs-content="' .trans('general/admin_lang.edit') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.medicines.edit', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            } elseif (auth()->user()->isAbleTo("admin-patients-medical-studies-update") && $data->creador == Auth::user()->id) {
                $actions .= '<a  class="btn btn-primary btn-xs" data-bs-content="' .trans('general/admin_lang.edit') . '" data-bs-placement="left" data-bs-toggle="popover" href="' . route('admin.patients.medicines.edit', ["patient_id" => $data->user_id, "id" => $data->id]) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }
            if ($data->creador == Auth::user()->id) {
                $actions .= '<a  class="btn btn-danger btn-xs"href="' . route('admin.patients.medicines.generatePdf', ["patient_id" => $data->user_id, "id" => $data->id]) . '""><i
                    class="fa fa-file-pdf fa-lg"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-patients-medical-studies-create")) {
                $actions .= '<a  class="btn btn-success btn-xs" href="javascript:void(0);"  onclick="javascript:copyElement(\'' .
                    route('admin.patients.medicines.copy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');"><i
                    class="fa fa-copy fa-lg"></i></a> ';
            }

            if (auth()->user()->isAbleTo("admin-patients-medical-studies-delete-all")) {

                $actions .= '<button class="btn btn-danger btn-xs" data-bs-content="' .trans('general/admin_lang.delete'). '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    route('admin.patients.medicines.destroy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            } elseif (auth()->user()->isAbleTo("admin-patients-medical-studies-delete") && $data->creador == Auth::user()->id) {
                $actions .= '<button class="btn btn-danger btn-xs" data-bs-content="' .trans('general/admin_lang.delete'). '" data-bs-placement="left" data-bs-toggle="popover" onclick="javascript:deleteElement(\'' .
                    route('admin.patients.medicines.destroy', ["patient_id" => $data->user_id, "id" => $data->id])  . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }


            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'medicine']);
        return $table->make();
    }

    public function destroy($patient_id, $id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->isAbleTo('admin-patients-medical-studies-delete')) {
            app()->abort(403);
        }
        $medicina = PatientMedicine::where("id", $id)
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

        $query = PatientMedicine::select(
            [
                "patient_medical_studiess.user_id",
                "patient_medical_studiess.id",
                "patient_medical_studiess.date",
                DB::raw('CONCAT(created.first_name, " ", created.last_name) as created_by'),
                DB::raw('CONCAT(user_profiles.first_name, " ", user_profiles.last_name) as patient'),
                "centers.name as center",
                "patient_medical_studies_details.dosis",
                "patient_medical_studies_details.frecuency",
                "patient_medical_studies_details.amount",
                "patient_medical_studies_details.period",
                "patient_medical_studies_details.medicine",
            ]
        )
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "patient_medical_studiess.user_id")
            ->leftJoin("user_profiles as created", "created.user_id", "=", "patient_medical_studiess.created_by")
            ->leftJoin("centers", "centers.id", "=", "patient_medical_studiess.center_id")
            ->leftJoin("patient_medical_studies_details", "patient_medical_studies_details.patient_medical_studies_id", "=", "patient_medical_studiess.id")
            ->where("patient_medical_studiess.user_id", $patient_id)
            ->distinct();


        $this->addFilter($query);

        return Excel::download(new AdminPatientMedicineExport($query), strtolower(trans('patient-medical-studies/admin_lang.patient-medical-studies-export')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function saveMedicine($medicine, $request)
    {
        $medicine->date = Carbon::createFromFormat("d/m/Y", $request->date);
        $medicine->comment =  $request->comment;
        $medicine->save();

        //eliminando medicamentos antiguos
        if (!empty($medicine->id)) {
            $details = PatientMedicineDetail::where("patient_medical_studies_id", $medicine->id)->delete();
        }

        for ($i = 0; $i < count($request->medicine); $i++) {
            $detail = new PatientMedicineDetail();
            $detail->patient_medical_studies_id = $medicine->id;
            $detail->medicine = $request->medicine[$i];
            $detail->dosis = $request->dosis[$i];
            $detail->frecuency = $request->frecuency[$i];
            $detail->period = $request->period[$i];
            $detail->save();
        }
    }
}
