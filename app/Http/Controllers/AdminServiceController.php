<?php

namespace App\Http\Controllers;

use App\Exports\AdminServicesExport;
use App\Http\Requests\AdminServiceRequest;
use App\Models\InsuranceCarrier;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

use Maatwebsite\Excel\Facades\Excel;

class AdminServiceController extends Controller
{


    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-services')) {
            app()->abort(403);
        }

        $pageTitle = trans('services/admin_lang.services');
        $title = trans('services/admin_lang.list');

        return view('services.admin_index', compact('pageTitle', 'title'));
    }

    public function create()
    {
        if (!auth()->user()->isAbleTo('admin-services-create')) {
            app()->abort(403);
        }
        $pageTitle = trans('services/admin_lang.new');
        $title = trans('services/admin_lang.list');
        $service = new Service();
        $tab = 'tab_1';


        return view('services.admin_edit', compact('pageTitle', 'title', "service"))
            ->with('tab', $tab);
    }

    public function store(AdminServiceRequest $request)
    {
        if (!auth()->user()->isAbleTo('admin-services-create')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $service = new Service();

            $this->saveService($service, $request);

            DB::commit();

            return redirect()->route('admin.services.edit', [$service->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/services/create')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->isAbleTo('admin-services-update')) {
            app()->abort(403);
        }
        $service = Service::find($id);

        if (empty($service)) {
            app()->abort(404);
        }

        $pageTitle = trans('services/admin_lang.edit');
        $title = trans('services/admin_lang.list');
        $tab = 'tab_1';
        return view('services.admin_edit', compact('pageTitle', 'title', "service"))
            ->with('tab', $tab);
    }

    public function update(AdminServiceRequest $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-services-update')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $service = Service::find($id);

            $this->saveService($service, $request);

            DB::commit();


            return redirect()->route('admin.services.edit', [$service->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/services/create/' . $service->id)
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }



    public function getData()
    {
        if (!auth()->user()->isAbleTo('admin-services-list')) {
            app()->abort(403);
        }
        $query = Service::select([
            'services.active',
            'services.id',
            'services.name',
            'services.price',

        ]);

        $table = DataTables::of($query);

        $table->editColumn('active', function ($data) {
            $permision = "";
            if (!auth()->user()->isAbleTo('admin-services-update')) {
                $permision = "disabled";
            }

            $state = $data->active ? "checked" : "";

            return  '<div class="form-check form-switch ">
                <input class="form-check-input" onclick="changeState(' . $data->id . ')" ' . $state . '  ' . $permision . '  value="1" name="active" type="checkbox" id="active">
            </div>';
        });



        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->isAbleTo("admin-services-update")) {
                $actions .= '<a  class="btn btn-info btn-sm" href="' . route('admin.services.edit', $data->id) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }

            if (auth()->user()->isAbleTo("admin-services-delete")) {

                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                    url('admin/services/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }

            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'active']);
        return $table->make();
    }

    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->isAbleTo('admin-services-delete')) {
            app()->abort(403);
        }
        $service = Service::find($id);
        if (empty($service)) {
            app()->abort(404);
        }

        $service->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }

    public function changeState($id)
    {
        if (!auth()->user()->isAbleTo('admin-services-update')) {
            app()->abort(403);
        }

        $service = Service::find($id);

        if (!empty($service)) {
            $service->active = !$service->active;
            return $service->save() ? 1 : 0;
        }

        return 0;
    }

    public function editAditionalInfo($id)
    {
        if (!auth()->user()->isAbleTo('admin-services-update')) {
            app()->abort(403);
        }

        $service = Service::find($id);
        if (is_null($service)) {
            app()->abort(500);
        }
        $insuranceList = InsuranceCarrier::active()->get();
        $pageTitle = trans('services/admin_lang.edit');
        $title = trans('services/admin_lang.list');


        $tab = "tab_2";
        return view('services.admin_edit_aditional_info', compact(
            'pageTitle',
            'title',
            "service",
            'insuranceList'
        ))
            ->with('tab', $tab);
    }

    public function updateAditionalInfo(Request $request, $id)
    {

        if (!auth()->user()->isAbleTo('admin-services-update')) {
            app()->abort(403);
        }

        $service = Service::find($id);

        if (is_null($service)) {
            app()->abort(500);
        }

        try {
            DB::beginTransaction();

            $service->insuranceCarriers()->detach();
            if (!empty($request->insurance)) {
                for ($i = 0; $i < count($request->insurance); $i++) {
                    $segurosData = [];
                    $segurosData[$request->insurance[$i]] = ["price" => $request->price[$i]];
                    $service->insuranceCarriers()->attach($segurosData);
                }
            }

            $service->save();
            DB::commit();
            // Y Devolvemos una redirección a la acción show para mostrar el usuario

            return redirect()->to('/admin/services/aditional-info/' . $service->id)->with('success', trans('general/admin_lang.save_ok'));
        } catch (\PDOException $e) {
            DB::rollBack();
            dd($e);

            return redirect()->to('/admin/services/aditional-info/' . $service->id);
            // ->with('error', trans('general/admin_lang.save_ko'));
        }
    }



    public function exportExcel()
    {
        if (!auth()->user()->isAbleTo('admin-services-list')) {
            app()->abort(403);
        }
        $query = Service::select([
            'services.active',
            'services.id',
            'services.name',
            'services.description',
            'services.price',

        ]);
        return Excel::download(new AdminServicesExport($query), strtolower(trans('services/admin_lang.services')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }



    private function saveService($service, $request)
    {
        $service->name = $request->input('name');
        $service->active = $request->input('active', 0);
        $service->price = $request->input('price', 0);
        $service->description = $request->input('description', 0);
        $service->save();
    }
}
