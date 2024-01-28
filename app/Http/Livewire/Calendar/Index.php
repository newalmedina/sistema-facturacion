<?php

namespace App\Http\Livewire\Calendar;

use App\Models\Appointment;
use App\Models\InsuranceCarrier;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Index extends Component
{
    public $events = '';
    public $modalTitle;
    public $selectedService;
    public $servicesList = [];
    public $doctorList = [];
    public $doctorListFilter = [];
    public $patientListFilter = [];
    public $insuranceList = [];
    public $patientList = [];
    public $successSaved = null;
    public $disabledForm = null;
    public $estado;

    public $filtersForm = [
        "doctor_id" => "",
        "estado" => "",
        "patient_id" => ""
    ];
    public $appointmentForm;
    public Appointment $appointment;

    protected $listeners = [
        'clickCalendar' => 'clickCalendar',
        'reloadCalendar' => 'reloadCalendar',
        'changePatient' => 'changePatient',
        'calculatePrices' => 'calculatePrices'
    ];


    public function mount()
    {

        $this->resetFields();
        $this->reloadCalendar();
        $this->updateSelects();
    }

    public function render()
    {
        $pageTitle =  trans('calendar/admin_lang.calendar');
        $title =  trans('calendar/admin_lang.calendar');

        return view('livewire.calendar.index',  compact(
            'pageTitle',
            'title'
        ));
    }

    public function rules()
    {
        return [
            'appointmentForm.start_at' => 'required',
            'appointmentForm.hour' => 'required',
            'appointmentForm.user_id' => 'required',
            'appointmentForm.service_id' => 'required',
            'appointmentForm.doctor_id' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'appointmentForm.start_at.required' =>  trans('appointments/admin_lang.fields.start_at_required'),
            'appointmentForm.hour.required' =>  trans('appointments/admin_lang.fields.hour_required'),
            'appointmentForm.user_id.required' =>  trans('appointments/admin_lang.fields.user_id_required'),
            'appointmentForm.service_id.required' =>  trans('appointments/admin_lang.fields.service_id_required'),
            'appointmentForm.doctor_id.required' =>  trans('appointments/admin_lang.fields.doctor_id_required'),
        ];
    }

    public function clickCalendar($data)
    {
        $this->resetValidation();
        $time = $data[0];
        $id = $data[1];
        $this->resetFields();
        $this->disabledForm = "";

        if (empty($id)) {
            if (!auth()->user()->isAbleTo("admin-appointments-create")) {
                $this->emit('sinPermisos', "No tienes permisos para crear citas");
                return false;
            }

            $this->appointment = new Appointment();
            $this->appointmentForm["start_at"] = $time;
            $this->modalTitle = "Nueva cita";
        } else {
            $this->appointment =  Appointment::find($id);

            $this->disabledForm = "disabled";
            if (
                !$this->appointment->canEdit()
                && !$this->appointment->canShow()
            ) {
                $this->emit('sinPermisos', "No tienes permisos para acceder a esta cita");
                return false;
            }

            if ($this->appointment->canEdit() && $this->appointment->state == "pendiente") {
                $this->disabledForm = "";
            }


            $this->modalTitle = "Editar cita";
            $this->appointmentForm = [
                "start_at" =>  Carbon::parse($this->appointment->start_at)->format("Y-m-d"),
                "service_id" => $this->appointment->service_id,
                "hour" => Carbon::parse($this->appointment->start_at)->format("H:i"),
                "user_id" => $this->appointment->user_id,
                "doctor_id" => $this->appointment->doctor_id,
                "insurance_carrier_id" => $this->appointment->insurance_carrier_id,
                "applicated_insurance" => $this->appointment->applicated_insurance,
                "comment" => $this->appointment->comment,
                "price_with_insurance" => 0,
                "price" => 0,
                "total" => 0,
                "poliza" => "",
                "delete_coment" => $this->appointment->delete_coment
            ];
        }

        $this->getInsurance();
        $this->calculatePrices();
        $this->emit('toggleModal');
    }

    public function reloadCalendar()
    {
        $this->getEvents();

        $this->emit('reloadEvents');
    }

    public function getevent()
    {

        $events = Appointment::select('id', 'title', 'start_at', "end_at")->get();

        return  json_encode($events);
    }

    public function changePatient()
    {

        $this->appointmentForm["insurance_carrier_id"] = "";
        $this->appointmentForm["poliza"] = "";
        $this->appointmentForm["applicated_insurance"] = 0;
        $this->getInsurance();
    }

    public function getInsurance()
    {

        $this->appointmentForm["price_with_insurance"] = 0;
        $this->appointmentForm["poliza"] = "";
        $this->appointmentForm["total"] = !empty($this->selectedService->id) ? $this->selectedService->price : 0;
        // dd($this->appointmentForm["user_id"]);
        if (!empty($this->appointmentForm["insurance_carrier_id"])) {
            $this->insuranceList = InsuranceCarrier::active()
                ->select("insurance_carriers.id", "insurance_carriers.name")
                ->join("patient_insurance_carriers", "insurance_carriers.id", "patient_insurance_carriers.insurance_carrier_id")
                ->where("patient_insurance_carriers.user_id", $this->appointmentForm["user_id"])
                ->where("patient_insurance_carriers.insurance_carrier_id", $this->appointmentForm["insurance_carrier_id"])
                ->get();
        } else {
            $this->insuranceList = InsuranceCarrier::active()
                ->select("insurance_carriers.id", "insurance_carriers.name")
                ->join("patient_insurance_carriers", "insurance_carriers.id", "patient_insurance_carriers.insurance_carrier_id")
                ->where("patient_insurance_carriers.user_id", $this->appointmentForm["user_id"])
                ->get();
        }
    }

    public function filters()
    {

        $this->reloadCalendar();
    }

    public function calculatePrices()
    {
        if (!empty($this->appointment->paid_at)) {
            //si ya se ha facturado no se puede calcular nada

            $this->appointmentForm["price_with_insurance"] = $this->appointment->price_with_insurance;
            $this->appointmentForm["total"] = $this->appointment->total;
            $this->appointmentForm["price"] = $this->appointment->price;
        } else {
            $this->appointmentForm["price_with_insurance"] = 0;
            $this->appointmentForm["total"] = 0;

            $this->selectedService = Service::find($this->appointmentForm["service_id"]);

            $this->appointmentForm["price"] = !empty($this->selectedService->id) ? $this->selectedService->price : 0;

            $seguroPrecio = DB::table("service_insurance_carriers")
                ->select("service_insurance_carriers.id", "service_insurance_carriers.price", "patient_insurance_carriers.poliza")
                ->leftJoin("patient_insurance_carriers", "patient_insurance_carriers.insurance_carrier_id", "service_insurance_carriers.insurance_carrier_id")
                ->where("service_insurance_carriers.insurance_carrier_id",  $this->appointmentForm["insurance_carrier_id"])
                ->where("service_insurance_carriers.service_id",  $this->appointmentForm["service_id"])
                ->where("patient_insurance_carriers.user_id",  $this->appointmentForm["user_id"])
                ->first();

            // dd($this->appointmentForm["insurance_carrier_id"]);

            $miPoliza = !empty($seguroPrecio->id) ? $seguroPrecio->poliza : "";

            if (!empty($this->appointmentForm["insurance_carrier_id"])) {
                $query = DB::table("patient_insurance_carriers")
                    ->where("patient_insurance_carriers.insurance_carrier_id", $this->appointmentForm["insurance_carrier_id"])
                    ->where("patient_insurance_carriers.user_id", $this->appointmentForm["user_id"])
                    ->first();

                if (!empty($query->poliza)) {

                    $miPoliza = $query->poliza;
                }
            }

            $this->appointmentForm["poliza"] = $miPoliza;
            $this->appointmentForm["total"] = !empty($this->selectedService->id) ? $this->selectedService->price : 0;
            // dd($seguroPrecio->id);
            if (!empty($seguroPrecio->id)) {
                $this->appointmentForm["price_with_insurance"] = $seguroPrecio->price;

                if (!empty($this->appointmentForm["applicated_insurance"])) {
                    $this->appointmentForm["total"] = $seguroPrecio->price;
                }
            }
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function addevent()
    {

        $this->validate();

        if (empty($this->appointment->id)) {
            if (!auth()->user()->isAbleTo('admin-appointments-create')) {
                app()->abort(403);
            }

            $this->appointment = new Appointment();
            $this->appointment->created_by = Auth::user()->id;
            $this->appointment->state = "pendiente";
            $this->appointment->center_id = Auth::user()->hasSelectedCenter();
        } else {
            if (!empty($this->disabledForm)) {
                app()->abort(403);
            }
        }

        $this->appointment->title = "Cita medica";

        $this->appointment->user_id = $this->appointmentForm["user_id"];
        $this->appointment->doctor_id = $this->appointmentForm["doctor_id"];
        $start = Carbon::parse($this->appointmentForm["start_at"] . " " . $this->appointmentForm["hour"]);
        $end = $start;
        $this->appointment->start_at = $start->format("Y-m-d H:i");
        $this->appointment->end_at = $end->addMinutes(15)->format("Y-m-d H:i");
        $this->appointment->service_id = $this->appointmentForm["service_id"];
        $this->appointment->price = $this->appointmentForm["price"];
        $this->appointment->insurance_carrier_id = !empty($this->appointmentForm["insurance_carrier_id"]) ? $this->appointmentForm["insurance_carrier_id"] : null;
        $this->appointment->applicated_insurance = !empty($this->appointmentForm["applicated_insurance"]) &&  !empty($this->appointment->insurance_carrier_id) ? $this->appointmentForm["applicated_insurance"] : 0;
        $this->appointment->price_with_insurance = $this->appointmentForm["price_with_insurance"];
        $this->appointment->total = $this->appointmentForm["total"];
        $this->appointment->comment = $this->appointmentForm["comment"];
        $this->appointment->color = "#6c757d";
        $this->appointment->save();
        $this->reloadCalendar();
        // $this->resetFields();
        // $this->emit('toggleModal');
        $this->emit('eventoAgregado');


        $this->successSaved = "Registro guardado correctamente";
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function eventDrop($event, $oldEvent)
    {
        $eventdata = Appointment::find($event['id']);
        $eventdata->start = $event['start'];
        $eventdata->save();
    }

    public function getEvents()
    {

        $events = Appointment::canList()
            ->select(
                'appointments.id',
                DB::raw("CONCAT(user_profiles.first_name, ' ', user_profiles.last_name,' / ',doctor.first_name, ' ', doctor.last_name ) as title"),
                'start_at as start',
                "end_at as end",
                'color'
            )
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "appointments.user_id")
            ->leftJoin("user_profiles as doctor", "doctor.user_id", "=", "appointments.doctor_id")
            ->selectedCenter();

        if (!empty($this->filtersForm["doctor_id"])) {
            $events->where("appointments.doctor_id", $this->filtersForm["doctor_id"]);
        }
        if (!empty($this->filtersForm["patient_id"])) {
            $events->where("appointments.user_id", $this->filtersForm["patient_id"]);
        }

        if (!empty($this->filtersForm["estado"])) {
            switch ($this->filtersForm["estado"]) {
                case "pend":
                    $events
                        ->where("appointments.state", "pendiente");
                    break;
                case "fact":
                    $events
                        ->where("appointments.state", "facturado");
                    break;
                case "fin":
                    $events
                        ->where("appointments.state", "finalizado");
                    # code...
                    break;

                default:
                    # codsadsadsadsasadsade...
                    break;
            }
        }


        $this->events = json_encode($events->get());
        // dd($this->events);
    }

    public function openFacturarModal()
    {
        $this->emit('facturarModal');
    }
    public function openFinalizarModal()
    {
        $this->emit('finalizarModal');
    }
    public function openDeleteModal()
    {
        $this->emit('deteleModal');
    }

    public function deleteItem()
    {
        if (
            !$this->appointment->canDelete()
        ) {
            $this->emit('sinPermisos', "No tienes permisos para eliminar esta cita");
            return false;
        }

        if ($this->appointment->state != "pendiente") {
            $this->validate(
                [
                    'appointmentForm.delete_coment' => 'required', // Puedes personalizar estas reglas según tus necesidades
                ],
                [
                    'appointmentForm.delete_coment' => 'El comentario o motivo de la eliminación es obligatorio',
                ]
            );
            $this->appointment->delete_coment = $this->appointmentForm["delete_coment"];
        }
        $this->appointment->deleted_at = Carbon::now();
        $this->appointment->save();

        $this->appointment->delete();
        $this->emit('deteleModal');
        $this->emit('toggleModal');
        $this->emit('eventoEliminado');
        $this->getEvents();
        $this->reloadCalendar();
    }

    public function facturarItem()
    {
        if (
            !$this->appointment->canFacturar()
        ) {
            $this->emit('sinPermisos', "No tienes permisos para facturar esta cita");
            return false;
        }

        $this->appointment->paid_at = Carbon::now();
        $this->appointment->paid_by = Auth::user()->id;

        $this->appointment->color = "#ffc107";
        $this->appointment->state = "facturado";
        $this->appointment->save();

        $this->emit('facturarModal');
        $this->emit('eventoFacturado');
        $this->disabledForm = "disabled";
        $this->reloadCalendar();
    }
    public function finalizarItem()
    {
        if (
            !$this->appointment->canFinalizar()
        ) {
            $this->emit('sinPermisos', "No tienes permisos para finalizar esta cita");
            return false;
        }
        $this->appointment->finish_at = Carbon::now();
        $this->appointment->finish_by = Auth::user()->id;
        $this->appointment->color = "#28a745";

        $this->appointment->state = "finalizado";
        $this->appointment->save();
        $this->emit('finalizarModal');
        $this->emit('eventoFinalizado');
        $this->disabledForm = "disabled";
        $this->reloadCalendar();
    }

    public function updateSelects()
    {
        $this->servicesList = Service::active()->get();
        $this->patientList = User::select("users.*")
            ->active()
            ->patients()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->get();
        $this->patientListFilter = User::select("users.*")
            ->active()
            ->patients()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->get();
        $this->doctorList = User::select("users.*")
            ->active()
            ->clinicPersonal()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->leftJoin("user_centers", "user_centers.user_id", "=", "users.id")
            ->where("user_centers.center_id", auth()->user()->userProfile->center->id)
            ->get();
        $this->doctorListFilter = User::select("users.*")
            ->active()
            ->clinicPersonal()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->leftJoin("user_centers", "user_centers.user_id", "=", "users.id")
            ->where("user_centers.center_id", auth()->user()->userProfile->center->id)
            ->get();
    }


    public function resetFields()
    {
        $this->appointmentForm = [
            "start_at" => null,
            "service_id" => null,
            "hour" => null,
            "user_id" => null,
            "doctor_id" => null,
            "insurance_carrier_id" => null,
            "applicated_insurance" => null,
            "comment" => null,
            "price_with_insurance" => 0,
            "price" => 0,
            "total" => 0,
            "poliza" => "",
            "delete_coment" => ""
        ];
    }
}
