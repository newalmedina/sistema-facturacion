<?php

namespace App\Http\Controllers;

use App\Exports\AdminCentersExport;
use App\Exports\AdminInsuranceCarrierExport;
use App\Http\Requests\AdminInsuranceCarrierRequest;
use App\Http\Requests\AdminChangeCenterRequest;
use App\Models\Center;
use App\Models\InsuranceCarrier;
use App\Models\Municipio;
use App\Models\PermissionsTree;
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

use Maatwebsite\Excel\Facades\Excel;

class AdminInsuranceCarrierController extends Controller
{
    public $filtProvinceId;
    public $filtMunicipioId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->filtMunicipioId = ($request->session()->has('insurance-carrier_filter_municipio_id')) ? ($request->session()->get('insurance-carrier_filter_municipio_id')) : "";
            $this->filtProvinceId = ($request->session()->has('insurance-carrier_filter_province_id')) ? ($request->session()->get('insurance-carrier_filter_province_id')) : "";
            return $next($request);
        });
    }

    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-insurance-carriers')) {
            app()->abort(403);
        }

        $pageTitle = trans('insurance-carriers/admin_lang.insurance-carriers');
        $title = trans('insurance-carriers/admin_lang.list');
        $provincesList = Province::active()->get();
        // $municipiosList = Municipio::active()->where("province_id", $insuranceCarrier->province_id)->get();
        $municipiosList = Municipio::active()->where("province_id", $this->filtProvinceId)->get();

        return view('insurance-carriers.admin_index', compact('pageTitle', 'title', 'provincesList', 'municipiosList'))
            ->with([
                'filtProvinceId' => $this->filtProvinceId,
                'filtMunicipioId' => $this->filtMunicipioId,
            ]);
    }

    public function create()
    {
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-create')) {
            app()->abort(403);
        }
        $pageTitle = trans('insurance-carriers/admin_lang.new');
        $title = trans('insurance-carriers/admin_lang.list');
        $insuranceCarrier = new InsuranceCarrier();
        $tab = 'tab_1';

        $provincesList = Province::active()->get();
        $municipiosList = Municipio::active()->where("province_id", $insuranceCarrier->province_id)->get();

        return view('insurance-carriers.admin_edit', compact('pageTitle', 'title', "insuranceCarrier", "provincesList", 'municipiosList'))
            ->with('tab', $tab);
    }

    public function store(AdminInsuranceCarrierRequest $request)
    {
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-create')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $insuranceCarrier = new InsuranceCarrier();

            $this->saveInsuranceCarrier($insuranceCarrier, $request);

            DB::commit();

            return redirect()->route('admin.insurance-carriers.edit', [$insuranceCarrier->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/insurance-carriers/create')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-update')) {
            app()->abort(403);
        }
        $insuranceCarrier = InsuranceCarrier::find($id);

        if (empty($insuranceCarrier)) {
            app()->abort(404);
        }

        $pageTitle = trans('insurance-carriers/admin_lang.edit');
        $title = trans('insurance-carriers/admin_lang.list');
        $tab = 'tab_1';
        $provincesList = Province::active()->get();
        $municipiosList = Municipio::active()->where("province_id", $insuranceCarrier->province_id)->get();


        return view('insurance-carriers.admin_edit', compact('pageTitle', 'title', "insuranceCarrier", 'provincesList', 'municipiosList'))
            ->with('tab', $tab);
    }

    public function update(AdminInsuranceCarrierRequest $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-update')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $insuranceCarrier = InsuranceCarrier::find($id);

            $this->saveInsuranceCarrier($insuranceCarrier, $request);

            DB::commit();


            return redirect()->route('admin.insurance-carriers.edit', [$insuranceCarrier->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/insurance-carriers/create/' . $insuranceCarrier->id)
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function saveFilter(Request $request)
    {
        $this->clearSesions($request);
        if (!empty($request->province_id))
            $request->session()->put('insurance-carrier_filter_province_id', $request->province_id);
        if (!empty($request->municipio_id))
            $request->session()->put('insurance-carrier_filter_municipio_id', $request->municipio_id);

        return redirect('admin/insurance-carriers');
    }
    public function removeFilter(Request $request)
    {
        $this->clearSesions($request);
        return redirect('admin/insurance-carriers');
    }

    private function addFilter(&$query)
    {

        if (!empty($this->filtProvinceId)) {
            $query->where("provinces.id", $this->filtProvinceId);
        }
        if (!empty($this->filtMunicipioId)) {
            $query->where("municipios.id", $this->filtMunicipioId);
        }
    }

    private function clearSesions($request)
    {
        $request->session()->forget('insurance-carrier_filter_province_id');
        $request->session()->forget('insurance-carrier_filter_municipio_id');
    }

    public function getData()
    {
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-list')) {
            app()->abort(403);
        }
        $query = InsuranceCarrier::select([
            'insurance_carriers.active',
            'insurance_carriers.id',
            'insurance_carriers.name',
            'insurance_carriers.image',
            'insurance_carriers.phone',
            'insurance_carriers.email',
            'provinces.name as province',
            'municipios.name as municipio',
        ])
            ->leftJoin("provinces", "insurance_carriers.province_id", "=", "provinces.id")
            ->leftJoin("municipios", "insurance_carriers.municipio_id", "=", "municipios.id");
        $this->addFilter($query);

        $table = DataTables::of($query);

        $table->editColumn('active', function ($data) {
            $permision = "";
            if (!auth()->user()->isAbleTo('admin-insurance-carriers-update')) {
                $permision = "disabled";
            }

            $state = $data->active ? "checked" : "";

            return  '<div class="form-check form-switch ">
                <input class="form-check-input" onclick="changeState(' . $data->id . ')" ' . $state . '  ' . $permision . '  value="1" name="active" type="checkbox" id="active">
            </div>';
        });

        $table->editColumn('image', function ($data) {
            if (empty($data->image)) {
                return "";
            }

            return  '<center><img width="40" class="rounded-circle" src="' . url('admin/insurance-carriers/get-image/' . $data->image) . '" alt="imagen"> </center>';
        });


        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->isAbleTo("admin-insurance-carriers-update")) {
                $actions .= '<a  class="btn btn-info btn-sm" href="' . route('admin.insurance-carriers.edit', $data->id) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }

            if (auth()->user()->isAbleTo("admin-insurance-carriers-delete")) {

                $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                    url('admin/insurance-carriers/' . $data->id) . '\');" data-content="' .
                    trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
            }

            return $actions;
        });

        $table->removeColumn('id');
        $table->rawColumns(['actions', 'active', 'image']);
        return $table->make();
    }

    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-delete')) {
            app()->abort(403);
        }
        $insuranceCarrier = InsuranceCarrier::find($id);
        if (empty($insuranceCarrier)) {
            app()->abort(404);
        }
        $myServiceSPW = new StoragePathWork("insurance-carriers");

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
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-update')) {
            app()->abort(403);
        }

        $insuranceCarrier = InsuranceCarrier::find($id);

        if (!empty($insuranceCarrier)) {
            $insuranceCarrier->active = !$insuranceCarrier->active;
            return $insuranceCarrier->save() ? 1 : 0;
        }

        return 0;
    }

    public function editAditionalInfo($id)
    {
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-update')) {
            app()->abort(403);
        }

        $insuranceCarrier = InsuranceCarrier::find($id);
        if (is_null($insuranceCarrier)) {
            app()->abort(500);
        }
        $pageTitle = trans('insurance-carriers/admin_lang.edit');
        $title = trans('insurance-carriers/admin_lang.list');


        $tab = "tab_2";
        return view('insurance-carriers.admin_edit_aditional_info', compact(
            'pageTitle',
            'title',
            "center"
        ))
            ->with('tab', $tab);
    }

    public function updateAditionalInfo(Request $request, $id)
    {

        if (!auth()->user()->isAbleTo('admin-insurance-carriers-update')) {
            app()->abort(403);
        }

        $insuranceCarrier = InsuranceCarrier::find($id);

        if (is_null($insuranceCarrier)) {
            app()->abort(500);
        }

        try {
            DB::beginTransaction();
            $insuranceCarrier->specialities = $request->input('specialities');
            $insuranceCarrier->schedule = $request->input('schedule');

            $insuranceCarrier->save();
            DB::commit();
            // Y Devolvemos una redirección a la acción show para mostrar el usuario

            return redirect()->to('/admin/insurance-carriers/aditional-info/' . $insuranceCarrier->id)->with('success', trans('general/admin_lang.save_ok'));
        } catch (\PDOException $e) {
            DB::rollBack();
            dd($e);

            return redirect()->to('/admin/insurance-carriers/aditional-info/' . $insuranceCarrier->id);
            // ->with('error', trans('general/admin_lang.save_ko'));
        }
    }

    public function getImage($photo)
    {
        $myServiceSPW = new StoragePathWork("insurance-carriers");
        return $myServiceSPW->showFile($photo, '/insurance-carriers');
    }

    public function deleteImage($id)
    {
        $myServiceSPW = new StoragePathWork("insurance-carriers");
        $insuranceCarrier = InsuranceCarrier::find($id);

        if (!empty($insuranceCarrier->image)) {
            $myServiceSPW->deleteFile($insuranceCarrier->image, '');
            $insuranceCarrier->image = "";
        }
        $insuranceCarrier->save();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }

    public function exportExcel()
    {
        if (!auth()->user()->isAbleTo('admin-insurance-carriers-list')) {
            app()->abort(403);
        }

        $query = InsuranceCarrier::select([
            'insurance_carriers.active',
            'insurance_carriers.id',
            'insurance_carriers.name',
            // 'insurance_carriers.image',
            'insurance_carriers.phone',
            'insurance_carriers.email',
            'insurance_carriers.address',
            'provinces.name as province',
            'municipios.name as municipio',
        ])
            ->leftJoin("provinces", "insurance_carriers.province_id", "=", "provinces.id")
            ->leftJoin("municipios", "insurance_carriers.municipio_id", "=", "municipios.id");
        return Excel::download(new AdminInsuranceCarrierExport($query), strtolower(trans('insurance-carriers/admin_lang.insurance-carriers')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }


    private function saveInsuranceCarrier($insuranceCarrier, $request)
    {
        $insuranceCarrier->name = $request->input('name');
        $insuranceCarrier->phone = $request->input('phone');
        $insuranceCarrier->email = $request->input('email');
        $insuranceCarrier->province_id = $request->input('province_id');
        $insuranceCarrier->municipio_id = $request->input('municipio_id');
        $insuranceCarrier->address = $request->input('address');
        $insuranceCarrier->active = $request->input('active', 0);

        $image = $request->file('image');

        if (!is_null($image)) {
            $myServiceSPW = new StoragePathWork("insurance-carriers");

            if (!empty($insuranceCarrier->image)) {
                $myServiceSPW->deleteFile($insuranceCarrier->image, '');
                $insuranceCarrier->image = "";
            }

            $filename = $myServiceSPW->saveFile($image, '');
            $insuranceCarrier->image = $filename;
        }



        $insuranceCarrier->save();
    }
}
