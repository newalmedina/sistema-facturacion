<?php

namespace App\Http\Controllers;

use App\Exports\AdminCentersExport;
use App\Http\Requests\AdminCenterRequest;
use App\Http\Requests\AdminChangeCenterRequest;
use App\Models\Center;
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

class AdminCenterController extends Controller
{
    public $filtProvinceId;
    public $filtMunicipioId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->filtMunicipioId = ($request->session()->has('center_filter_municipio_id')) ? ($request->session()->get('center_filter_municipio_id')) : "";
            $this->filtProvinceId = ($request->session()->has('center_filter_province_id')) ? ($request->session()->get('center_filter_province_id')) : "";
            return $next($request);
        });
    }

    public function index()
    {
        if (!auth()->user()->isAbleTo('admin-centers')) {
            app()->abort(403);
        }

        $pageTitle = trans('centers/admin_lang.centers');
        $title = trans('centers/admin_lang.list');
        $provincesList = Province::active()->get();
        // $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();
        $municipiosList = Municipio::active()->where("province_id", $this->filtProvinceId)->get();

        return view('centers.admin_index', compact('pageTitle', 'title', 'provincesList', 'municipiosList'))
            ->with([
                'filtProvinceId' => $this->filtProvinceId,
                'filtMunicipioId' => $this->filtMunicipioId,
            ]);
    }

    public function create()
    {
        if (!auth()->user()->isAbleTo('admin-centers-create')) {
            app()->abort(403);
        }
        $pageTitle = trans('centers/admin_lang.new');
        $title = trans('centers/admin_lang.list');
        $center = new Center();
        $tab = 'tab_1';

        $provincesList = Province::active()->get();
        $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();

        return view('centers.admin_edit', compact('pageTitle', 'title', "center", "provincesList", 'municipiosList'))
            ->with('tab', $tab);
    }

    public function store(AdminCenterRequest $request)
    {
        if (!auth()->user()->isAbleTo('admin-centers-create')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $center = new Center();

            $this->saveCenter($center, $request);

            DB::commit();

            return redirect()->route('admin.centers.edit', [$center->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/centers/create')
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        if (!auth()->user()->isAbleTo('admin-centers-read')) {
            app()->abort(403);
        }
        $center = Center::find($id);

        if (empty($center)) {
            app()->abort(404);
        }

        $pageTitle = trans('centers/admin_lang.show');
        $title = trans('centers/admin_lang.list');
        $tab = 'tab_1';
        $provincesList = Province::active()->get();
        $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();
        $disabled = "disabled";

        return view('centers.admin_edit', compact('pageTitle', 'title', "center", 'provincesList', 'municipiosList', 'disabled'))
            ->with('tab', $tab);
    }

    public function edit($id)
    {
        if (!auth()->user()->isAbleTo('admin-centers-update')) {
            app()->abort(403);
        }
        $center = Center::find($id);

        if (empty($center)) {
            app()->abort(404);
        }

        $pageTitle = trans('centers/admin_lang.edit');
        $title = trans('centers/admin_lang.list');
        $tab = 'tab_1';
        $provincesList = Province::active()->get();
        $municipiosList = Municipio::active()->where("province_id", $center->province_id)->get();


        return view('centers.admin_edit', compact('pageTitle', 'title', "center", 'provincesList', 'municipiosList'))
            ->with('tab', $tab);
    }

    public function update(AdminCenterRequest $request, $id)
    {
        if (!auth()->user()->isAbleTo('admin-centers-update')) {
            app()->abort(403);
        }

        try {
            DB::beginTransaction();

            $center = Center::find($id);

            $this->saveCenter($center, $request);

            DB::commit();


            return redirect()->route('admin.centers.edit', [$center->id])->with('success', trans('general/admin_lang.save_ok'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect('admin/centers/create/' . $center->id)
                ->with('error', trans('general/admin_lang.save_ko') . ' - ' . $e->getMessage());
        }
    }

    public function saveFilter(Request $request)
    {
        $this->clearSesions($request);
        if (!empty($request->province_id))
            $request->session()->put('center_filter_province_id', $request->province_id);
        if (!empty($request->municipio_id))
            $request->session()->put('center_filter_municipio_id', $request->municipio_id);

        return redirect('admin/centers');
    }
    public function removeFilter(Request $request)
    {
        $this->clearSesions($request);
        return redirect('admin/centers');
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
        $request->session()->forget('center_filter_province_id');
        $request->session()->forget('center_filter_municipio_id');
    }

    public function getData()
    {
        if (!auth()->user()->isAbleTo('admin-centers-list')) {
            app()->abort(403);
        }
        $query = Center::select([
            'centers.active',
            'centers.id',
            'centers.name',
            // 'centers.image',
            'centers.default',
            'centers.phone',
            'centers.email',
            'provinces.name as province',
            'municipios.name as municipio',
        ])
            ->leftJoin("provinces", "centers.province_id", "=", "provinces.id")
            ->leftJoin("municipios", "centers.municipio_id", "=", "municipios.id");
        $this->addFilter($query);

        $table = DataTables::of($query);

        $table->editColumn('active', function ($data) {
            $permision = "";
            if (!auth()->user()->isAbleTo('admin-centers-update')) {
                $permision = "disabled";
            }

            $state = $data->active ? "checked" : "";

            return  '<div class="form-check form-switch ">
                <input class="form-check-input" onclick="changeState(' . $data->id . ')" ' . $state . '  ' . $permision . '  value="1" name="active" type="checkbox" id="active">
            </div>';
        });

        // $table->editColumn('image', function ($data) {
        //     if (empty($data->image)) {
        //         return "";
        //     }

        //     return  '<center><img width="40" class="rounded-circle" src="' . url('admin/centers/get-image/' . $data->image) . '" alt="imagen"> </center>';
        // });
        $table->editColumn('default', function ($data) {

            if ($data->default) {
                return '<center><i class="fa fa-check text-success" aria-hidden="true"></i></center>';
            }
            return '<center><i class="fa fa-times text-danger" aria-hidden="true"></i></center>';
        });

        $table->editColumn('actions', function ($data) {
            $actions = '';
            if (auth()->user()->isAbleTo("admin-centers-read")) {
                $actions .= '<a  class="btn btn-info btn-xs" href="' . route('admin.centers.show', $data->id) . '" ><i
                class="fa fa-eye fa-lg"></i></a> ';
            }
            if (auth()->user()->isAbleTo("admin-centers-update")) {
                $actions .= '<a  class="btn btn-primary btn-xs" href="' . route('admin.centers.edit', $data->id) . '" ><i
                class="fa fa-marker fa-lg"></i></a> ';
            }

            if (auth()->user()->isAbleTo("admin-centers-delete")) {

                $actions .= '<button class="btn btn-danger btn-xs" onclick="javascript:deleteElement(\'' .
                    url('admin/centers/' . $data->id) . '\');" data-content="' .
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
        if (!auth()->user()->isAbleTo('admin-centers-delete')) {
            app()->abort(403);
        }
        $center = Center::find($id);
        if (empty($center)) {
            app()->abort(404);
        }
        $myServiceSPW = new StoragePathWork("centers");

        if (!empty($center->image)) {
            $myServiceSPW->deleteFile($center->image, '');
            $center->image = "";
        }
        $center->delete();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }

    public function changeState($id)
    {
        if (!auth()->user()->isAbleTo('admin-centers-update')) {
            app()->abort(403);
        }

        $center = Center::find($id);

        if (!empty($center)) {
            $center->active = !$center->active;
            return $center->save() ? 1 : 0;
        }

        return 0;
    }

    public function showAditionalInfo($id)
    {
        if (!auth()->user()->isAbleTo('admin-centers-update')) {
            app()->abort(403);
        }

        $center = Center::find($id);
        if (is_null($center)) {
            app()->abort(500);
        }
        $pageTitle = trans('centers/admin_lang.show');
        $title = trans('centers/admin_lang.list');


        $tab = "tab_2";
        $disabled = "disabled";
        return view('centers.admin_edit_aditional_info', compact(
            'pageTitle',
            'title',
            'disabled',
            "center"
        ))
            ->with('tab', $tab);
    }

    public function editAditionalInfo($id)
    {
        if (!auth()->user()->isAbleTo('admin-centers-update')) {
            app()->abort(403);
        }

        $center = Center::find($id);
        if (is_null($center)) {
            app()->abort(500);
        }
        $pageTitle = trans('centers/admin_lang.edit');
        $title = trans('centers/admin_lang.list');


        $tab = "tab_2";
        return view('centers.admin_edit_aditional_info', compact(
            'pageTitle',
            'title',
            "center"
        ))
            ->with('tab', $tab);
    }

    public function updateAditionalInfo(Request $request, $id)
    {

        if (!auth()->user()->isAbleTo('admin-centers-update')) {
            app()->abort(403);
        }

        $center = Center::find($id);

        if (is_null($center)) {
            app()->abort(500);
        }

        try {
            DB::beginTransaction();
            $center->specialities = $request->input('specialities');
            $center->schedule = $request->input('schedule');

            $center->save();
            DB::commit();
            // Y Devolvemos una redirección a la acción show para mostrar el usuario

            return redirect()->to('/admin/centers/aditional-info/' . $center->id)->with('success', trans('general/admin_lang.save_ok'));
        } catch (\PDOException $e) {
            DB::rollBack();
            dd($e);

            return redirect()->to('/admin/centers/aditional-info/' . $center->id);
            // ->with('error', trans('general/admin_lang.save_ko'));
        }
    }

    public function getImage($photo)
    {
        $myServiceSPW = new StoragePathWork("centers");
        return $myServiceSPW->showFile($photo, '/centers');
    }

    public function deleteImage($id)
    {
        $myServiceSPW = new StoragePathWork("centers");
        $center = Center::find($id);

        if (!empty($center->image)) {
            $myServiceSPW->deleteFile($center->image, '');
            $center->image = "";
        }
        $center->save();

        return response()->json(array(
            'success' => true,
            'msg' => trans("general/admin_lang.delete_ok"),
        ));
    }

    public function exportExcel()
    {
        if (!auth()->user()->isAbleTo('admin-centers-list')) {
            app()->abort(403);
        }
        $query = Center::select([
            'centers.active',
            'centers.id',
            'centers.name',
            // 'centers.image',
            'centers.default',
            'centers.phone',
            'centers.email',
            'centers.address',
            'centers.schedule',
            'centers.specialities',
            'provinces.name as province',
            'municipios.name as municipio',
        ])
            ->leftJoin("provinces", "centers.province_id", "=", "provinces.id")
            ->leftJoin("municipios", "centers.municipio_id", "=", "municipios.id");
        return Excel::download(new AdminCentersExport($query), strtolower(trans('centers/admin_lang.centers')) . '_' . Carbon::now()->format("dmYHis") . '.xlsx');
    }

    public function changeCenter(AdminChangeCenterRequest $request)
    {

        $profile = UserProfile::where("user_id", Auth::user()->id)->first();
        $profile->selected_center = $request->center_id;
        $profile->save();
        return redirect()->back()->with('success', trans('centers/admin_lang.changed'));
    }

    private function saveCenter($center, $request)
    {
        $center->name = $request->input('name');
        $center->phone = $request->input('phone');
        $center->email = $request->input('email');
        $center->province_id = $request->input('province_id');
        $center->municipio_id = $request->input('municipio_id');
        $center->address = $request->input('address');
        $center->default = $request->input('default', 0);
        $center->active = $request->input('active', 0);

        // $image = $request->file('image');

        // if (!is_null($image)) {
        //     $myServiceSPW = new StoragePathWork("centers");

        //     if (!empty($center->image)) {
        //         $myServiceSPW->deleteFile($center->image, '');
        //         $center->image = "";
        //     }

        //     $filename = $myServiceSPW->saveFile($image, '');
        //     $center->image = $filename;
        // }

        if ($request->input('default')) {
            DB::table('centers')
                ->update([
                    'default' => 0
                ]);
            $center->default = $request->input('default');
        }

        $center->save();
    }
}
