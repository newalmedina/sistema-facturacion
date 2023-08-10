<?php

namespace App\Http\Controllers;

use App\Exports\AdminMedicalSpecializationsExport;
use App\Http\Requests\AdminMedicalSpecializationRequest;
use App\Models\MedicalSpecialization;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AdminMedicalSpecializationController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-medical-specializations')) {
            app()->abort(403);
        }

        $pageTitle = trans('medical-specializations/admin_lang.medical-specializations');
        $title = trans('medical-specializations/admin_lang.list');


        return view('medical-specializations.admin_index', compact('pageTitle', 'title'))
            ->with([]);
    }

    public function create()
    {
        if (!auth()->user()->isAbleTo('admin-medical-specializations-create')) {
            app()->abort(403);
        }
        $pageTitle = trans('medical-specializations/admin_lang.new');
        $title = trans('medical-specializations/admin_lang.list');
        $medicalSpecialization = new MedicalSpecialization();


        return view('medical-specializations.admin_edit', compact('pageTitle', 'title', "medicalSpecialization"));
    }

    public function store(AdminMedicalSpecializationRequest $request)
    {
        if (!auth()->user()->isAbleTo('admin-medical-specializations-create')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $medicalSpecialization = new MedicalSpecialization();

            $this->saveMedicalSpecialization($medicalSpecialization, $request);

            DB::commit();

            return redirect()->route('admin.medical-specializations.edit', [$medicalSpecialization->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/medical-specializations/create')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->isAbleTo('admin-medical-specializations-update')) {
            app()->abort(403);
        }
        $medicalSpecialization = MedicalSpecialization::find($id);

        if (empty($medicalSpecialization)) {
            app()->abort(404);
        }

        $pageTitle = trans('medical-specializations/admin_lang.edit');
        $title = trans('medical-specializations/admin_lang.list');


        return view('medical-specializations.admin_edit', compact('pageTitle', 'title', "medicalSpecialization"));
    }

    public function update(AdminMedicalSpecializationRequest $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-medical-specializations-update')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $medicalSpecialization = MedicalSpecialization::find($id);

            $this->saveMedicalSpecialization($medicalSpecialization, $request);

            DB::commit();


            return redirect()->route('admin.medical-specializations.edit', [$medicalSpecialization->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/medical-specializations/create/' . $medicalSpecialization->id)
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }


    public function getData()
    {
        if (!auth()->user()->isAbleTo('admin-medical-specializations-list')) {
            app()->abort(403);
        }
        $query = MedicalSpecialization::select([
            'medical_specializations.active',
            'medical_specializations.id',
            'medical_specializations.name',

        ]);

        $table = DataTables::of($query);

        $table->editColumn('active', function ($data) {
            $permision = "";
            if (!auth()->user()->isAbleTo('admin-medical-specializations-update')) {
                $permision = "disabled";
            }

            $state = $data->active ? "checked" : "";

            return  '<div class="form-check form-switch ">
                <input class="form-check-input" onclick="changeState(' . $data->id . ')" ' . $state . '  ' . $permision . '  value="1" name="active" type="checkbox" id="active">
            </div>';
        });

        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->isAbleTo("admin-medical-specializations-update")) {
                $actions .= '<a  class="btn btn-info btn-sm" href="' . route('admin.medical-specializations.edit', $data->id) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }

            if (auth()->user()->isAbleTo("admin-medical-specializations-delete")) {

                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                    url('admin/medical-specializations/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }

            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'active',  'default']);
        return $table->make();
    }

    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->isAbleTo('admin-medical-specializations-delete')) {
            app()->abort(403);
        }
        $medicalSpecialization = MedicalSpecialization::find($id);
        if (empty($medicalSpecialization)) {
            app()->abort(404);
        }

        $medicalSpecialization->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }

    public function changeState($id)
    {
        if (!auth()->user()->isAbleTo('admin-medical-specializations-update')) {
            app()->abort(403);
        }

        $medicalSpecialization = MedicalSpecialization::find($id);

        if (!empty($medicalSpecialization)) {
            $medicalSpecialization->active = !$medicalSpecialization->active;
            return $medicalSpecialization->save() ? 1 : 0;
        }

        return 0;
    }


    public function exportExcel()
    {
        if (!auth()->user()->isAbleTo('admin-medical-specializations-list')) {
            app()->abort(403);
        }
        $query = MedicalSpecialization::select([
            'medical_specializations.active',
            'medical_specializations.id',
            'medical_specializations.name',
            'medical_specializations.description',

        ]);
        return Excel::download(new AdminMedicalSpecializationsExport($query), strtolower(trans('medical-specializations/admin_lang.medical-specializations')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function saveMedicalSpecialization($medicalSpecialization, $request)
    {
        $medicalSpecialization->name = $request->input('name');
        $medicalSpecialization->description = $request->input('description');
        $medicalSpecialization->active = $request->input('active', 0);
        $medicalSpecialization->save();
    }
}
