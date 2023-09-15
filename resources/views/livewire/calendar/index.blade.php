@section('head_page')
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
    </style>
@stop

<div class="row">
    <div class="modal fade" id="modal_appointment"tabindex="-1"  aria-labelledby="miModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
    
                    <h5 class="modal-title" id="miModalLabel" wire:model="modalTitle">{{ $modalTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    
                </div>
                <div  class="modal-body" style="min-height: 100px">
                    <form id="formDataAppointment" wire:submit.prevent="addevent">
                     
                       
                        <div class="row form-group mb-3">
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group ">
                                            <label for="start_at"> {{ trans('appointments/admin_lang.fields.start_at') }}<span class="text-danger">*</span></label>
                                            <input  type="text" wire:model.defer="appointmentForm.start_at" id="start_at"  class="form-control datepicker"   placeholder="{{ trans('appointments/admin_lang.fields.start_at_helper') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group ">
                                            <label for="hour"> {{ trans('appointments/admin_lang.fields.hour') }}<span class="text-danger">*</span></label>
                                            <input  type="text" wire:model.defer="appointmentForm.hour" id="hour"  class="form-control datepicker"   placeholder="{{ trans('appointments/admin_lang.fields.hour_helper') }}">
                                        </div>
                                    </div>
                                </div>
                                
                            </div> 
                            <div class="col-12 col-md-6">
                                <div class="form-group ">
                                    <label for="user_id"> {{ trans('appointments/admin_lang.fields.user_id') }}<span class="text-danger">*</span></label>
                                    <select  class="form-control " wire:model.defer="appointmentForm.user_id" wire:change="getInsurance"  name="" id="user_id">
                                        <option value=""> {{ trans('appointments/admin_lang.fields.user_id_helper') }}</option>
                                        @foreach ($patientList as $patient)
                                            <option value="{{  $patient->id }}">{{  $patient->userProfile->fullName }}</option>
                                            
                                        @endforeach
                                    </select>
                                </div>
                               
                            </div> 
                            
                        </div>                                
                        <div class="row form-group mb-3">                                
                            <div class="col-12 col-md-6">
                                
                                <div class="form-group ">
                                    <label for="service_id"> {{ trans('appointments/admin_lang.fields.service_id') }}<span class="text-danger">*</span></label>
                                    <select  class="form-control select2" wire:model.defer="appointmentForm.service_id"  name="" id="service_id">
                                        <option value=""> {{ trans('appointments/admin_lang.fields.service_id_helper') }}</option>
                                        @foreach ($servicesList as $service)
                                        <option value="{{  $service->id }}">{{  $service->name }} ({{  $service->price }} RD$)</option>
                                            
                                        @endforeach
                                    </select>
                                </div>
                            </div>      
                        </div>    
                        <div class="row form-group mb-3">                                
                            <div class="col-12 col-md-6">
                                
                                <div class="form-group ">
                                    <label for="insurance_carrier_id"> {{ trans('appointments/admin_lang.fields.insurance_carrier_id') }}</label>
                                    <select  class="form-control select2" wire:model.defer="appointmentForm.insurance_carrier_id"  name="" id="insurance_carrier_id">
                                        <option value=""> {{ trans('appointments/admin_lang.fields.insurance_carrier_id_helper') }}</option>
                                        @foreach ($servicesList as $service)
                                        <option value="{{  $service->id }}">{{  $service->name }} ({{  $service->price }} RD$)</option>
                                            
                                        @endforeach
                                    </select>
                                </div>
                            </div>      
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="applicated_insurance"> {{ trans('appointments/admin_lang.fields.applicated_insurance') }}</label>
                                            <div class="form-check form-switch">
                                                <input  class="form-check-input toggle-switch"  wire:model.defer="appointmentForm.applicated_insurance"  value="1" name="applicated_insurance" type="checkbox" id="applicated_insurance">
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="price_with_insurance"> {{ trans('appointments/admin_lang.fields.price_with_insurance') }}</label>
                                            <input  type="text" id="price_with_insurance"   wire:model.defer="appointmentForm.price_with_insurance"   disabled class="form-control "   placeholder="">
                                        </div>
                                    </div>
                                </div>
                              
                            </div>      
                        </div>    
                        <div class="row form-group mb-3">                                
                            <div class="col-12 col-md-3">
                                
                                <div class="form-group ">
                                    <label for="total"> {{ trans('appointments/admin_lang.fields.total') }}</label>
                                    <input  type="text" id="total" disabled class="form-control "  wire:model.defer="appointmentForm.total"     placeholder="">
                                       
                                </div>
                            </div>     
                        </div>  
                    </form>      
                </div>
                <div  class="modal-footer">
                    <button type="button" class=" btn  btn-default pull-right" data-bs-dismiss="modal" aria-label="Close">{{ trans('general/admin_lang.close') }}</button>
                    <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>   
                                    
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
  $(document).ready(function() {
       
      
    });
    document.addEventListener('livewire:load', function() {
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
        var Calendar = FullCalendar.Calendar;
        var Draggable = FullCalendar.Draggable;
        var calendarEl = document.getElementById('calendar');
        var checkbox = document.getElementById('drop-remove');
        var data =   @this.events;
        var calendar = new Calendar(calendarEl, {
        
            events: JSON.parse(data),        
            dateClick(info)  {
                let id="";
                let time=info.dateStr;
                Livewire.emit('clickCalendar',[time,id]);
                            
             
                // var title = prompt('Enter Event Title');
                // var date = new Date(info.dateStr + 'T00:00:00');
                // if(title != null && title != ''){
                //     calendar.addEvent({
                //     title: title,
                //     start: date,
                //     allDay: true
                //     });
                //     var eventAdd = {title: title,start: date};
                //     @this.addevent(eventAdd);
                //     alert('Great. Now, update your database...');
                // }else{
                // alert('Event Title Is Required');
                // }
            },

            editable: true,
            selectable: true,
            displayEventTime: false,
            droppable: true, // this allows things to be dropped onto the calendar
            drop: function(info) {
                // is the "remove after drop" checkbox checked?
                if (checkbox.checked) {
                // if so, remove the element from the "Draggable Events" list
                info.draggedEl.parentNode.removeChild(info.draggedEl);
                }
            },
            
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
 
        @this.on(`refreshCalendar`, () => {
            calendar.refetchEvents()
        });
        
        window.addEventListener('toggleModal', () => {
             $('#modal_appointment').modal('show'); 
         
        })
      
        

      
    });
</script>

<link href="{{ asset('/assets/admin/vendor/fullcalendar/fullcalendar.min.css')}}" rel="stylesheet" />
@stop