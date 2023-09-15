<?php

namespace App\Http\Livewire\Calendar;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public $events = '';
    public $modalTitle;
    public $servicesList = [];
    public $patientList = [];

    public $appointmentForm;
    public Appointment $appointment;

    protected $listeners = ['clickCalendar' => 'clickCalendar'];

    public function mount()
    {

        // $this->resetFields();
        $this->getEvents();
        //$this->updateSelects();
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

    public function clickCalendar($data)
    {
        $time = $data[0];
        $id = $data[1];
        if (empty($id)) {
            $this->appointment = new Appointment();
            $this->modalTitle = "Nueva cita";
        } else {
            $this->modalTitle = "Editar cita";
        }

        $this->updateSelects();
        $this->resetFields();
        // $this->dispatchBrowserEvent('toggleModal');
    }

    public function getevent()
    {
        $events = Appointment::select('id', 'title', 'start_at', "end_at")->get();

        return  json_encode($events);
    }
    public function getInsurance()
    {
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function addevent()
    {
        dd($this->appointmentForm);
        // $input['title'] = $event['title'];
        // $input['start'] =  Carbon::parse($event['start']);
        // $input['end'] =  Carbon::parse($event['start'])->addMinutes(15);
        // $newAppointment = new Appointment();
        // $newAppointment->title = $input['title'];
        // $newAppointment->start_at = $input['start'];
        // $newAppointment->end_at =   $input['end'];
        // $newAppointment->save();
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
        $events = Appointment::select('id', 'title', 'start_at as start')->get();
        $this->events = json_encode($events);
    }

    public function updateSelects()
    {
        $this->servicesList = Service::active()->get();
        $this->patientList = User::active()
            ->patients()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->get();
    }
    public function resetFields()
    {
        $this->appointmentForm = [
            "start_at" => "",
            "service_id" => "",
            "hour" => "",
            "user_id" => "",
            "service_id" => "",
            "insurance_carrier_id" => "",
            "applicated_insurance" => "",
            "price_with_insurance" => "",
            "total" => ""
        ];
    }
}
