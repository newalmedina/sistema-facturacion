<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Appointment;
use App\Models\PatientMedicalStudies;
use App\Models\PatientMedicine;
use App\Models\PatientMonitoring;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DoctorPatient extends Component
{
    public $doctors = 0;
    public $patients = 0;
    public $recetas = 0;
    public $estudios = 0;
    public $patientFilter = null;
    public $recetaFilter = null;
    public $estudiosFilter = null;

    protected $listeners = ['actualizarFechaDoctorPatient'];

    public function mount()
    {
        $this->patientFilter = Carbon::now()->format("m/Y");
        $this->recetaFilter = Carbon::now()->format("m/Y");
        $this->estudiosFilter = Carbon::now()->format("m/Y");
        $this->getDatos();
    }
    public function actualizarFechaDoctorPatient($campo, $fecha)
    {
        $this->{$campo} = $fecha;

        $this->getDatos();
    }

    private function getDatos()
    {
        // Inicializar la variable $visitCount
        $this->doctors =  User::select("users.id")
            ->distinct()
            ->active()
            ->clinicPersonal()
            ->leftJoin("user_profiles", "user_profiles.user_id", "=", "users.id")
            ->join("user_centers", "user_centers.user_id", "=", "users.id")
            ->where("user_centers.center_id", auth()->user()->userProfile->center->id)
            ->count();

        $patientQuery = PatientMonitoring::select("patient_monitorings.id")
            ->where("center_id", auth()->user()->hasSelectedCenter());

        if (!empty($this->patientFilter)) {
            // dd(Carbon::createFromFormat("m/Y", $this->patientFilter)->endOfMonth()->format("Y-m-d"));
            $patientQuery->where("patient_monitorings.date", ">=", Carbon::createFromFormat("m/Y", $this->patientFilter)->startOfMonth()->format("Y-m-d"))
                ->where("patient_monitorings.date", "<=", Carbon::createFromFormat("m/Y", $this->patientFilter)->endOfMonth()->format("Y-m-d"));
        }

        $this->patients = count($patientQuery->get());

        $recetaQuery = PatientMedicine::select("patient_medicines.id")
            ->where("center_id", auth()->user()->hasSelectedCenter());

        if (!empty($this->recetaFilter)) {
            // dd(Carbon::createFromFormat("m/Y", $this->patientFilter)->endOfMonth()->format("Y-m-d"));
            $recetaQuery->where("patient_medicines.date", ">=", Carbon::createFromFormat("m/Y", $this->recetaFilter)->startOfMonth()->format("Y-m-d"))
                ->where("patient_medicines.date", "<=", Carbon::createFromFormat("m/Y", $this->recetaFilter)->endOfMonth()->format("Y-m-d"));
        }
        $this->recetas = count($recetaQuery->get());

        $estudioQuery = PatientMedicalStudies::select("patient_medical_studies.id")

            ->where("center_id", auth()->user()->hasSelectedCenter());

        if (!empty($this->estudiosFilter)) {
            // dd(Carbon::createFromFormat("m/Y", $this->patientFilter)->endOfMonth()->format("Y-m-d"));
            $estudioQuery->where("patient_medical_studies.date", ">=", Carbon::createFromFormat("m/Y", $this->estudiosFilter)->startOfMonth()->format("Y-m-d"))
                ->where("patient_medical_studies.date", "<=", Carbon::createFromFormat("m/Y", $this->estudiosFilter)->endOfMonth()->format("Y-m-d"));
        }
        $this->estudios = count($estudioQuery->get());
    }

    public function render()
    {
        return view('livewire.dashboard.doctor-patient');
    }
}
