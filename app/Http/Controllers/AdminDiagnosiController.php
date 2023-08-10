<?php

namespace App\Http\Controllers;

use App\Exports\AdminDiagnosisExport;
use App\Exports\AdminProvincesExport;
use App\Http\Requests\AdminDiagnosiRequest;
use App\Models\Diagnosi;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AdminDiagnosiController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-diagnosis')) {
            app()->abort(403);
        }

        $pageTitle = trans('diagnosis/admin_lang.diagnosis');
        $title = trans('diagnosis/admin_lang.list');


        return view('diagnosis.admin_index', compact('pageTitle', 'title'))
            ->with([]);
    }

    public function create()
    {
        if (!auth()->user()->isAbleTo('admin-diagnosis-create')) {
            app()->abort(403);
        }
        $pageTitle = trans('diagnosis/admin_lang.new');
        $title = trans('diagnosis/admin_lang.list');
        $diagnosi = new Diagnosi();


        return view('diagnosis.admin_edit', compact('pageTitle', 'title', "diagnosi"));
    }

    public function store(AdminDiagnosiRequest $request)
    {
        if (!auth()->user()->isAbleTo('admin-diagnosis-create')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $diagnosis = new Diagnosi();

            $this->saveDiagnosis($diagnosis, $request);

            DB::commit();

            return redirect()->route('admin.diagnosis.edit', [$diagnosis->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/diagnosis/create')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->isAbleTo('admin-diagnosis-update')) {
            app()->abort(403);
        }
        $diagnosi = Diagnosi::find($id);

        if (empty($diagnosi)) {
            app()->abort(404);
        }

        $pageTitle = trans('diagnosis/admin_lang.edit');
        $title = trans('diagnosis/admin_lang.list');


        return view('diagnosis.admin_edit', compact('pageTitle', 'title', "diagnosi"));
    }

    public function update(AdminDiagnosiRequest $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-diagnosis-update')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $diagnosis = Diagnosi::find($id);

            $this->saveDiagnosis($diagnosis, $request);

            DB::commit();


            return redirect()->route('admin.diagnosis.edit', [$diagnosis->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/diagnosis/create/' . $diagnosis->id)
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }


    public function getData()
    {
        if (!auth()->user()->isAbleTo('admin-diagnosis-list')) {
            app()->abort(403);
        }
        $query = Diagnosi::select([
            'diagnosis.active',
            'diagnosis.id',
            'diagnosis.name',

        ]);

        $table = DataTables::of($query);

        $table->editColumn('active', function ($data) {
            $permision = "";
            if (!auth()->user()->isAbleTo('admin-diagnosis-update')) {
                $permision = "disabled";
            }

            $state = $data->active ? "checked" : "";

            return  '<div class="form-check form-switch ">
                <input class="form-check-input" onclick="changeState(' . $data->id . ')" ' . $state . '  ' . $permision . '  value="1" name="active" type="checkbox" id="active">
            </div>';
        });

        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->isAbleTo("admin-diagnosis-update")) {
                $actions .= '<a  class="btn btn-info btn-sm" href="' . route('admin.diagnosis.edit', $data->id) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }

            if (auth()->user()->isAbleTo("admin-diagnosis-delete")) {

                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                    url('admin/diagnosis/' . $data->id) . '\');" data-content="' .
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
        if (!auth()->user()->isAbleTo('admin-diagnosis-delete')) {
            app()->abort(403);
        }
        $diagnosis = Diagnosi::find($id);
        if (empty($diagnosis)) {
            app()->abort(404);
        }

        $diagnosis->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }

    public function changeState($id)
    {
        if (!auth()->user()->isAbleTo('admin-diagnosis-update')) {
            app()->abort(403);
        }

        $diagnosis = Diagnosi::find($id);

        if (!empty($diagnosis)) {
            $diagnosis->active = !$diagnosis->active;
            return $diagnosis->save() ? 1 : 0;
        }

        return 0;
    }


    public function exportExcel()
    {
        if (!auth()->user()->isAbleTo('admin-diagnosis-list')) {
            app()->abort(403);
        }
        $query = Diagnosi::select([
            'diagnosis.active',
            'diagnosis.id',
            'diagnosis.name',

        ]);
        return Excel::download(new AdminDiagnosisExport($query), strtolower(trans('diagnosis/admin_lang.diagnosis')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function saveDiagnosis($diagnosis, $request)
    {
        $diagnosis->name = $request->input('name');
        $diagnosis->active = $request->input('active', 0);
        $diagnosis->save();
    }
}
