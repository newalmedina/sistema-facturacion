@section('head_page')
    <!-- Incluir Moment.js para Bootstrap Datetimepicker -->

    <style>
        #calendar-container{
            width: 100%;
        }
        #calendar{
            padding: 10px;
            margin: 10px;
            width: 1340px;
            height: 610px;
            border:2px solid black;
        }
        .clockpicker-popover {
            z-index: 1060; /* Ajusta el valor según sea necesario para que esté por encima del modal */
        }
      
        .modal {
            z-index: 1050; /* Ajusta el valor según sea necesario para evitar conflictos con el reloj */
        }

    </style>
    
@stop

<div class="row">
    <div wire:ignore.self  class="modal fade" id="modal_facturar" tabindex="-1" style="z-index:99999"  aria-labelledby="miModalLabel " role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
    
                    <h3 class="modal-title" id="staticBackdropLabel">Facturar cita</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    
                </div>
                <div  class="modal-body" style="min-height: 100px">
                    <h4 class="d-flex align-items-center"><span style="font-size: 50px"><i class="fas fa-question-circle me-2 text-primary" ></i></span> <span>¿Seguro que qieres facturar esta cita?</span></h4>
                </div>
                <div  class="modal-footer">
                    <button type="button" class=" btn  btn-default pull-right" data-bs-dismiss="modal" aria-label="Close">{{ trans('general/admin_lang.close') }}</button>
                    <button type="button" wire:click='facturarItem' form="facturar" class="btn btn-success">Si, facturar</button>   
                                    
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    
    <div wire:ignore.self  class="modal fade" id="modal_delete" tabindex="-1" style="z-index:99999"  aria-labelledby="miModalLabel " role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
    
                    <h3 class="modal-title" id="staticBackdropLabel">{{ trans('general/admin_lang.delete') }}</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    
                </div>
                <div  class="modal-body" style="min-height: 100px">
                    <h4 class="d-flex align-items-center"><span style="font-size: 50px"><i class="fas fa-question-circle me-2 text-primary" ></i></span> <span>{{ trans('general/admin_lang.delete_question') }}</span></h4>
                </div>
                <div  class="modal-footer">
                    <button type="button" class=" btn  btn-default pull-right" data-bs-dismiss="modal" aria-label="Close">{{ trans('general/admin_lang.close') }}</button>
                    <button type="button" wire:click='deleteItem' form="eliminar" class="btn btn-success">{{ trans('general/admin_lang.yes_delete') }}</button>   
                                    
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    
    <div wire:ignore.self  class="modal fade" id="modal_appointment" tabindex="-1"  aria-labelledby="miModalLabel " role="dialog">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
    
                    <h5 class="modal-title" id="miModalLabel" wire:model="modalTitle">{{ $modalTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    
                </div>
                <div  class="modal-body" style="min-height: 100px">
                    <form id="formDataAppointment" wire:submit.prevent="addevent">
                        @if (!empty($appointment->id) && empty($disabledForm))                      
                            <div class="row mb-3">
                                @if (!$appointment->paid)
                                <div class="col-12 d-flex justify-content-between">
                                    @if (auth()->user()->isAbleTo("admin-appointments-delete-all") && empty($disabledForm) || auth()->user()->isAbleTo("admin-appointments-delete") && empty($disabledForm))
                                        <button type="button" wire:click='openDeleteModal' class="btn btn-danger btn-sm"><i class="fas fa-trash me-2"></i>Eliminar</button>
                                    @endif
                                        <button type="button" wire:click='openFacturarModal' class="btn btn-warning btn-sm"><i class="fas fa-dollar-sign me-2"></i>Facturar</button>
                                </div>
                                @else
                                
                                <div class="col-12 d-flex justify-content-end">
                                    <span class="badge badge-success"><b>Facturado</b></span>
                                </div>
                                @endif
                                
                            </div>
                            {{-- --}}
                        @elseif(!empty($appointment->id))
                            @if (auth()->user()->isAbleTo("admin-appointments-delete-all") || auth()->user()->isAbleTo("admin-appointments-delete") && $appointment->created_by==auth()->user()->id )
                                <button type="button" wire:click='openDeleteModal' class="btn btn-danger btn-sm"><i class="fas fa-trash me-2"></i>Eliminar</button>
                            @else

                            @endif
                        @endif                 
                         
                        @if (!empty($appointment->created_by))              
                            <div class="row mb-3">
                                <div class="col-12 offset-md-8 col-md-4 ">
                                    <label for="createdBy"> {{ trans('appointments/admin_lang.fields.created_by') }}</label>
                                    <input disabled  type="text"  value="{{ $appointment->createdBy->userProfile->fullName }}" id="createdBy"  class="form-control"   placeholder="">
                                </div>
                                
                                    
                            </div>
                        @endif                        
                       
                        <div class="row form-group mb-3">
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group ">
                                            <label for="start_at"> {{ trans('appointments/admin_lang.fields.start_at') }}<span class="text-danger">*</span></label>
                                            <input {{ $disabledForm}}  type="date" wire:model.defer="appointmentForm.start_at" id="start_at"  class="form-control " placeholder="{{ trans('appointments/admin_lang.fields.start_at_helper') }}">

                                            @error('appointmentForm.start_at') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group ">
                                            <label for="hour"> {{ trans('appointments/admin_lang.fields.hour') }}<span class="text-danger">*</span></label>
                                            <input {{ $disabledForm}}  type="time"   wire:model.defer="appointmentForm.hour" id="hour"  class="form-control timepicker"   placeholder="{{ trans('appointments/admin_lang.fields.hour_helper') }}">
                                            @error('appointmentForm.hour') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                                
                            </div> 
                            <div class="col-12 col-md-6">
                                <div class="form-group ">
                                    <label for="user_id"> {{ trans('appointments/admin_lang.fields.user_id') }}<span class="text-danger">*</span></label>
                                    <select {{ $disabledForm }}  class="form-control " wire:model="appointmentForm.user_id" wire:change="getInsurance"  name="" id="user_id">
                                        <option value=""> {{ trans('appointments/admin_lang.fields.user_id_helper') }}</option>
                                        @foreach ($patientList as $patient)
                                            <option value="{{  $patient->id }}">{{  $patient->userProfile->fullName }}</option>
                                        @endforeach
                                    </select>
                                    @error('appointmentForm.user_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                               
                            </div> 
                            
                        </div>                                
                        <div class="row form-group mb-3">                                
                            <div class="col-12 col-md-6">
                                
                                <div class="form-group ">
                                    <label for="doctor_id"> {{ trans('appointments/admin_lang.fields.doctor_id') }}<span class="text-danger">*</span></label>
                                    <select {{ $disabledForm }}   class="form-control select2" wire:model="appointmentForm.doctor_id"   name="" id="doctor_id">
                                        <option value=""> {{ trans('appointments/admin_lang.fields.doctor_id_helper') }}</option>
                                        @foreach ($doctorList as $doctor)
                                            <option value="{{  $doctor->id }}">{{  $doctor->userProfile->fullName }}</option>
                                        @endforeach
                                    </select>
                                    @error('appointmentForm.doctor_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>      
                            <div class="col-12 col-md-6">
                                
                                <div class="form-group ">
                                    <label for="service_id"> {{ trans('appointments/admin_lang.fields.service_id') }}<span class="text-danger">*</span></label>
                                    <select {{ $disabledForm }}    class="form-control select2" wire:model="appointmentForm.service_id"  wire:change='calculatePrices'   name="" id="service_id">
                                        <option value=""> {{ trans('appointments/admin_lang.fields.service_id_helper') }}</option>
                                        @foreach ($servicesList as $service)
                                        <option value="{{  $service->id }}">{{  $service->name }} ({{  $service->price }} RD$)</option>
                                            
                                        @endforeach
                                    </select>
                                    @error('appointmentForm.service_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>      
                        </div>    
                        <div class="row form-group mb-3">                                
                            <div class="col-12 col-md-6">
                                
                                <div class="form-group ">
                                    <label for="insurance_carrier_id"> {{ trans('appointments/admin_lang.fields.insurance_carrier_id') }}</label>
                                    <select {{ $disabledForm }}    class="form-control select2" wire:model="appointmentForm.insurance_carrier_id"  wire:change='calculatePrices'   name="" id="insurance_carrier_id" >
                                        <option value=""> {{ trans('appointments/admin_lang.fields.insurance_carrier_id_helper') }}</option>
                                        @foreach ($insuranceList as $insurance)
                                        <option value="{{  $insurance->id }}">{{  $insurance->name }} </option>
                                            
                                        @endforeach
                                    </select>
                                   
                                </div>
                            </div>      
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group ">
                                            <label for="poliza"> {{ trans('appointments/admin_lang.fields.poliza') }}</label>
                                            <input {{ $disabledForm}}  type="text"  wire:model="appointmentForm.poliza"     disabled class="form-control "   placeholder="">
                                        </div>
                                    </div>                                    
                                </div>                              
                            </div>      
                        </div>    
                        <div class="row form-group mb-3">       
                            <div class="col-12 col-md-4">
                                <div class="form-group ">
                                    <label for="price_with_insurance"> {{ trans('appointments/admin_lang.fields.price_with_insurance') }}</label>
                                    <input {{ $disabledForm}}  type="text"  id="price_with_insurance"   wire:model="appointmentForm.price_with_insurance"     disabled class="form-control "   placeholder="">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group ">
                                    <label for="applicated_insurance"> {{ trans('appointments/admin_lang.fields.applicated_insurance') }}</label>
                                    <div class="form-check form-switch">
                                        <input {{ $disabledForm}}  class="form-check-input toggle-switch"  wire:model="appointmentForm.applicated_insurance"  value="1"  wire:change='calculatePrices' name="applicated_insurance" type="checkbox" id="applicated_insurance">
                                    </div>    
                                </div>
                            </div>                         
                            <div class="col-12 col-md-4">
                                
                                <div class="form-group ">
                                    <label for="total"> {{ trans('appointments/admin_lang.fields.total') }}</label>
                                    <input {{ $disabledForm}}  type="text"  id="total" disabled class="form-control "  wire:model="appointmentForm.total"     placeholder="">
                                       
                                </div>
                            </div>     
                        </div>  
                        <div class="row form-group mb-3">                                
                            <div class="col-12 ">
                                
                                <div class="form-group ">
                                    <label for="comment"> {{ trans('appointments/admin_lang.fields.comment') }}</label>
                                    <textarea  {{ $disabledForm }}   id="comment"  class="form-control "  wire:model.defer="appointmentForm.comment"     placeholder="{{ trans('appointments/admin_lang.fields.comment_helper') }}"></textarea>
                                </div>
                            </div>     
                        </div>  
                    </form>      
                </div>
                <div  class="modal-footer">
                  
                    <button type="button" class=" btn  btn-default pull-right" data-bs-dismiss="modal" aria-label="Close">{{ trans('general/admin_lang.close') }}</button>                   
                    @if (empty($disabledForm))
                         <button type="submit" form="formDataAppointment" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>  
                        
                    @endif                 
                                    
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="col">
        <section class="card card-featured-top card-featured-primary">
            <header class="card-header">
                <div class="card-actions">
                    <a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
                    <a href="#" class="card-action card-action-dismiss" data-card-dismiss=""></a>
                </div>

                <h2 class="card-title">{{ $title }}</h2>
            </header>          

            <div class="card-body">  
                <div class="row mb-4">
                    <div class="col-4">
                        <div class="form-group ">
                            <label for="doctorfilter_id"> {{ trans('appointments/admin_lang.fields.doctor_id') }}</label>
                            <select   class="form-control select2" wire:model="filtersForm.doctor_id" wire:change="filters"   name="" id="doctorfilter_id">
                                <option value=""> {{ trans('appointments/admin_lang.fields.doctor_id_all') }}</option>
                                @foreach ($doctorListFilter as $doctor)
                                    <option value="{{  $doctor->id }}">{{  $doctor->userProfile->fullName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group ">
                            <label for="doctorfilter_id">Paciente</label>
                            <select   class="form-control select2" wire:model="filtersForm.patient_id" wire:change="filters"   name="" id="doctorfilter_id">
                                <option value=""> Todos</option>
                                @foreach ($patientListFilter as $patiet)
                                    <option value="{{  $patiet->id }}">{{  $patiet->userProfile->fullName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group ">
                            <label for="doctorfilter_id"> Facturado</label>
                            <select   class="form-control select2" wire:model="filtersForm.paid" wire:change="filters"   name="" id="doctorfilter_id">
                                <option value=""> Todos</option>
                                <option value="1"> Si</option>
                                <option value="0"> No</option>
                               
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id='calendar-container' wire:ignore>
                        <div id='calendar'></div>
                      </div>
                </div>                       
            </div>
        </section>
    </div>
</div>
@section('foot_page')
<!-- DataTables -->

{{-- <script>
    $(document).ready(function() {
        $('.select2').select2(); 
    });

</script> --}}
<script src="{{ asset('/assets/admin/vendor/fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ asset('/assets/admin/vendor/fullcalendar/locales-all.min.js') }}"></script>
<script>  
    document.addEventListener('livewire:load', function() {
        @if (!empty($successSaved))             
            toastr.success(" {{ $successSaved }}")
        @endif
   
        $('#modal_appointment').on('shown.bs.modal', function () {        
            // $('.select2').select2();
        });

     
        $('.datepicker').datepicker(
            {
                language: 'es',
                format: 'dd/mm/yyyy',
                orientation:'bottom',
                autoclose: true
            }
        );   

        $('.datepicker').on('change', function () {        
          
        });

        var Calendar = FullCalendar.Calendar;
        var Draggable = FullCalendar.Draggable;
        var calendarEl = document.getElementById('calendar');
        var checkbox = document.getElementById('drop-remove');
        loadCalendar();
        
        function loadCalendar(){
            var data =   @this.events;
            var calendar = new Calendar(calendarEl, {
        
            events: JSON.parse(data),     
          
           
            eventDisplay: 'block',
                dateClick(info)  {
                    @if (auth()->user()->isAbleTo("admin-appointments-create"))
                        let id="";
                        let time=info.dateStr;
                        Livewire.emit('clickCalendar',[time,id]);          
                    @endif                       
                },
                eventClick: function(info) {
                    @if (auth()->user()->isAbleTo("admin-appointments-update")|| auth()->user()->isAbleTo("admin-appointments-update-all")|| auth()->user()->isAbleTo("admin-appointments-read"))
                    var id = info.event.id;
                    var time= info.event.start;                   
                    Livewire.emit('clickCalendar',[time,id]);       
                    @endif
                },
                editable: true,
                selectable: true,
                displayEventTime: false,
                droppable: true, // this allows things to be dropped onto the calendar
                /*drop: function(info) {
                    // is the "remove after drop" checkbox checked?
                    if (checkbox.checked) {
                    // if so, remove the element from the "Draggable Events" list
                    info.draggedEl.parentNode.removeChild(info.draggedEl);
                    }
                },*/
                
                eventDrop: info => @this.eventDrop(info.event, info.oldEvent),
                loading: function(isLoading) {
                        if (!isLoading) {
                            // Reset custom events
                            this.getEvents().forEach(function(e){
                                if (e.source === null) {
                                    e.remove();
                                }
                            });
                        }
                    }
            });

            calendar.render();
        }
 
        // @this.on(`refreshCalendar`, () => {
        //     calendar.refetchEvents()
        // });
        
       
              
        Livewire.on('toggleModal', function () {
            $('#modal_appointment').modal('toggle'); 
        });
        Livewire.on('deteleModal', function () {
            $('#modal_delete').modal('toggle'); 
        });
        Livewire.on('facturarModal', function () {
            $('#modal_facturar').modal('toggle'); 
        });
        Livewire.on('eventoAgregado', function () {
            toastr.success("Evento guardado Correctamente")
            loadCalendar();// Recarga los eventos del calendario
        });
        Livewire.on('eventoFacturado', function () {
            toastr.success("Evento facturado Correctamente")
          
        });
        Livewire.on('eventoEliminado', function () {
            toastr.success("Evento eliminado Correctamente")
            loadCalendar();// Recarga los eventos del calendario
        });
        Livewire.on('reloadEvents', function () {
            loadCalendar();// Recarga los eventos del calendario
        });

      
        

      
    });
</script>

<link href="{{ asset('/assets/admin/vendor/fullcalendar/fullcalendar.min.css')}}" rel="stylesheet" />
@stop