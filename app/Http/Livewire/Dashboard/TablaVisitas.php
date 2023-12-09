<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use Livewire\WithPagination;

class TablaVisitas extends Component
{
    protected $listeners = [
        'facturarCita' => 'facturarCita',
        'finalizarCita' => 'finalizarCita'
    ];
    public $actualDate;

    public $estado;
    public $spanishMonths;
    public $filtros = [];
    public $with = [
        "patient.userProfile",
        "doctor.userProfile",
        "service",
        "createdBy"
    ];

    public function mount()
    {
        $this->getConsulta();
        $this->actualDate = Carbon::now()->format("d/m/Y");
    }


    public function facturarCita($id)
    {
        $apointment = Appointment::find($id);
        $apointment->paid_at = Carbon::now();
        $apointment->color = "#ffc107";
        $apointment->save();
    }

    public function finalizarCita($id)
    {
        $apointment = Appointment::find($id);
        $apointment->finish_at = Carbon::now();
        $apointment->color = "#28a745";
        $apointment->save();
    }

    public function getConsulta()
    {
        $datos = Appointment::with($this->with)
            ->when(data_get($this->filtros, 'paciente'), fn ($q, $keyword) => $q->whereHas('patient.userProfile', fn ($q) => $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $keyword . '%')))
            ->when(data_get($this->filtros, 'doctor'), fn ($q, $keyword) => $q->whereHas('doctor.userProfile', fn ($q) => $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $keyword . '%')))
            ->when(data_get($this->filtros, 'created_by'), fn ($q, $keyword) => $q->whereHas('createdBy.userProfile', fn ($q) => $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $keyword . '%')))
            ->when(data_get($this->filtros, 'service'), fn ($q, $info) => $q->whereHas('service', fn ($q) => $q->where('name', 'like', '%' . $info . '%')))
            ->when(data_get($this->filtros, 'precio_min'), fn ($q, $info) => $q->where('total', '>=', $info))
            ->when(data_get($this->filtros, 'precio_max'), fn ($q, $info) => $q->where('total', '<=', $info))
            // ->when(data_get($this->filtros, 'paciente'), fn ($q, $info) => $q->whereHas('patient.userProfile', fn ($q) => $q->where('first_name', $info)))
            ->where("appointments.center_id", auth()->user()->userProfile->center->id)
            ->where("appointments.start_at", ">=", Carbon::now()->startOfDay()->format("Y-m-d H:i:s"))
            ->where("appointments.start_at", "<=", Carbon::now()->endOfDay()->format("Y-m-d H:i:s"));

        if (!auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-all') && !auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today')) {

            return  Appointment::whereNull("id");
        } else if (!auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today-all') && auth()->user()->isAbleTo('admin-dashboard-appointment-programadas-today')) {

            $datos->where("appointments.doctor_id", Auth::user()->id);
        }
        if (!empty($this->estado)) {
            switch ($this->estado) {
                case "pend":
                    $datos
                        ->whereNull("appointments.finish_at")
                        ->whereNull("appointments.paid_at");
                    break;
                case "fact":
                    $datos
                        ->whereNull("appointments.finish_at")
                        ->whereNotNull("appointments.paid_at");
                    break;
                case "fin":
                    $datos
                        ->whereNotNull("appointments.finish_at");
                    # code...
                    break;

                default:
                    # codsadsadsadsasadsade...
                    break;
            }
        }

        return $datos->orderBy("start_at", "asc");
    }


    public function render()
    {
        return view(
            'livewire.dashboard.tabla-visitas',
            [
                'appointments' => $this->getConsulta()->paginate(10)
            ]
        );
    }
}
