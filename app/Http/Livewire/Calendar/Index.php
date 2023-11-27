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
    public $servicesList = [];
    public $doctorList = [];
    public $doctorListFilter = [];
    public $patientListFilter = [];
    public $insuranceList = [];
    public $patientList = [];
    public $successSaved = null;
    public $disabledForm = null;

    public $filtersForm = [
        "doctor_id" => "",
        "paid" => "",
        "patient_id" => ""
    ];
    public $appointmentForm;
    public Appointment $appointment;

    protected $listeners = ['clickCalendar' => 'clickCalendar'];

    public function mount()
    {

        $this->resetFields();
        $this->getEvents();
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
        $time = $data[0];
        $id = $data[1];
        $this->resetFields();
        $this->disabledForm = "";

        if (empty($id)) {
            if (!auth()->user()->isAbleTo("admin-appointments-create")) {
                abort(403);
            }
            $this->appointment = new Appointment();
            $this->appointmentForm["start_at"] = $time;
            $this->modalTitle = "Nueva cita";
        } else {
            $this->appointment =  Appointment::find($id);
            $this->disabledForm = "disabled";
            if (
                !auth()->user()->isAbleTo("admin-appointments-update-all") &&
                !auth()->user()->isAbleTo("admin-appointments-read") &&
                !auth()->user()->isAbleTo("admin-appointments-update")
            ) {
                abort(403);
            }
            if (auth()->user()->isAbleTo("admin-appointments-update-all") && !$this->appointment->paid) {
                $this->disabledForm = "";
            } else if (auth()->user()->isAbleTo("admin-appointments-update") && !$this->appointment->paid && $this->appointment->created_by == Auth::user()->id) {
                $this->disabledForm = "";
            }



            if (auth()->user()->isAbleTo("admin-appointments-update") &&  $this->appointment->created_by != auth()->user()->id && auth()->user()->isAbleTo("admin-appointments-read")) {
                $this->disabledForm = "disabled";
            }
            if (
                !auth()->user()->isAbleTo("admin-appointments-update-all") &&
                auth()->user()->isAbleTo("admin-appointments-read") &&
                !auth()->user()->isAbleTo("admin-appointments-update")
            ) {
                $this->disabledForm = "disabled";
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
                "poliza" => ""
            ];
        }

        $this->getInsurance();
        $this->calculatePrices();
        $this->emit('toggleModal');
    }

    public function getevent()
    {

        $events = Appointment::select('id', 'title', 'start_at', "end_at")->get();

        return  json_encode($events);
    }

    public function getInsurance()
    {
        // dd($this->appointmentForm["user_id"]);
        if (!empty($this->appointmentForm["insurance_carrier_id"])) {
            $this->insuranceList = InsuranceCarrier::active()
                ->join("patient_insurance_carriers", "insurance_carriers.id", "patient_insurance_carriers.insurance_carrier_id")
                ->where("patient_insurance_carriers.user_id", $this->appointmentForm["user_id"])
                ->where("patient_insurance_carriers.insurance_carrier_id", $this->appointmentForm["insurance_carrier_id"])
                ->get();
        } else {
            $this->insuranceList = InsuranceCarrier::active()
                ->join("patient_insurance_carriers", "insurance_carriers.id", "patient_insurance_carriers.insurance_carrier_id")
                ->where("patient_insurance_carriers.user_id", $this->appointmentForm["user_id"])
                ->get();
            $this->appointmentForm["insurance_carrier_id"] = "";
            $this->appointmentForm["price_with_insurance"] = 0;
            $this->appointmentForm["total"] = 0;
        }
    }

    public function filters()
    {

        $this->getEvents();

        $this->emit('reloadEvents');
    }

    public function calculatePrices()
    {
        $this->appointmentForm["price_with_insurance"] = 0;
        $this->appointmentForm["total"] = 0;

        $service = Service::find($this->appointmentForm["service_id"]);
        $this->appointmentForm["price"] = !empty($service->id) ? $service->price : 0;

        $seguroPrecio = DB::table("service_insurance_carriers")
            ->select("service_insurance_carriers.id", "service_insurance_carriers.price", "patient_insurance_carriers.poliza")
            ->join("patient_insurance_carriers", "patient_insurance_carriers.insurance_carrier_id", "service_insurance_carriers.insurance_carrier_id")
            ->where("service_insurance_carriers.insurance_carrier_id",  $this->appointmentForm["insurance_carrier_id"])
            ->where("service_insurance_carriers.service_id",  $this->appointmentForm["service_id"])
            ->where("patient_insurance_carriers.user_id",  $this->appointmentForm["user_id"])
            ->first();

        $this->appointmentForm["poliza"] = !empty($seguroPrecio->id) ? $seguroPrecio->poliza : "";
        $this->appointmentForm["total"] = !empty($service->id) ? $service->price : 0;

        if (!empty($seguroPrecio->id)) {
            $this->appointmentForm["price_with_insurance"] = $seguroPrecio->price;

            if (!empty($this->appointmentForm["applicated_insurance"])) {
                $this->appointmentForm["total"] = $seguroPrecio->price;
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
        $this->appointment->applicated_insurance = !empty($this->appointmentForm["applicated_insurance"]) ? $this->appointmentForm["applicated_insurance"] : 0;
        $this->appointment->price_with_insurance = $this->appointmentForm["price_with_insurance"];
        $this->appointment->total = $this->appointmentForm["total"];
        $this->appointment->comment = $this->appointmentForm["comment"];
        $this->appointment->save();
        $this->getEvents();
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
        if (auth()->user()->isAbleTo('admin-appointments-list')) {
            $events = Appointment::select('id', 'title', 'start_at as start', "end_at as end", 'color');

            if (!empty($this->filtersForm["doctor_id"])) {
                $events->where("doctor_id", $this->filtersForm["doctor_id"]);
            }
            if (!empty($this->filtersForm["patient_id"])) {
                $events->where("user_id", $this->filtersForm["patient_id"]);
            }
            if ($this->filtersForm["paid"] || $this->filtersForm["paid"] == 0) {
                $events->where("paid", $this->filtersForm["paid"]);
            }
        } else {
            $events = Appointment::select('id', 'title', 'start_at as start', "end_at as end", 'color')->whereNull("id");
        }


        $this->events = json_encode($events->get());
        // dd($this->events);
    }

    public function openFacturarModal()
    {
        $this->emit('facturarModal');
    }
    public function openDeleteModal()
    {
        $this->emit('deteleModal');
    }

    public function deleteItem()
    {
        $this->appointment->delete();
        $this->emit('deteleModal');
        $this->emit('toggleModal');
        $this->emit('eventoEliminado');
        $this->getEvents();
    }

    public function facturarItem()
    {
        $this->appointment->paid = 1;
        $this->appointment->color = "#47a447";
        $this->appointment->save();
        $this->emit('facturarModal');
        $this->emit('eventoFacturado');
    }

    public function updateSelects()
    {
        $this->servicesList = Service::active()->get();
        $this->patientList = User::active()
            ->patients()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->get();
        $this->patientListFilter = User::active()
            ->patients()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->get();
        $this->doctorList = User::active()
            ->clinicPersonal()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->get();
        $this->doctorListFilter = User::active()
            ->clinicPersonal()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
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
            "poliza" => ""
        ];
    }
}
