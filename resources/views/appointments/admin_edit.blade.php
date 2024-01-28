@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')
<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />
    
@stop

@section('breadcrumb')
<li><a href="{{ url('admin/appointments') }}">{{ $title }}</a></li>
@stop

@section('content')
@php
$disabled= isset($disabled)?$disabled : null;
@endphp


<section role="main" class="content-body card-margin">      
    <div class="mt-2">
         @include('layouts.admin.includes.modals')
      
        @include('layouts.admin.includes.errors')   
    </div>
    <div class="row">
        <div class="col">
            <section class="card card-featured-top card-featured-primary">
                <header class="card-header">
                    <div class="card-actions">
                        <a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
                        <a href="#" class="card-action card-action-dismiss" data-card-dismiss=""></a>
                    </div>

                    <h2 class="card-title">{{ trans("general/admin_lang.general_info") }}</h2>
                </header>
                

                <div class="card-body">  
                    <form id="formData" enctype="multipart/form-data" action="@if(empty($diagnosi->id)) {{ route("admin.appointments.store") }} @else {{ route("admin.appointments.update",$diagnosi->id) }} @endif" method="post"  novalidate="false">
                        @csrf       
                       
                        @if(empty($diagnosi->id))  
                            @method('post')
                        @else   
                            @method('patch') 
                        @endif
                          
                        <div class="card-body">
                            @if (!empty($appointment->id) )      
                                <div class="col-12 d-flex justify-content-end mb-3">
                                
                                    <span class=" badge p-2" style="background-color: {{$appointment->getStateColor()}};">{{$appointment->getState()}}</span>
                                    
                                </div>
                            @endif
                            @if (!isset($showMode) &&!empty($appointment->id))
                                <div class="row mb-3">
                                    <div class="col-12 d-flex justify-content-between">                                        
                                        @if($appointment->canFacturar())
                                            <button type="button" onclick="facturarElement()"  class="btn btn-warning btn-sm"><i class="fas fa-dollar-sign me-2"></i>Facturar</button>
                                        @endif
                                        @if($appointment->canFinalizar())
                                            <button type="button" onclick="finalizarElement()" class="btn btn-success btn-sm"><i class="fas fa-dollar-sign me-2"></i>Finalizar</button>
                                        @endif
                                    </div>   
                                </div>
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
                                                <input {{ $disabledForm}}  value="{{ !empty($appointment->start_at) ? \Carbon\Carbon::parse($appointment->start_at)->format("Y-m-d") : null }}"  type="date" name="start_at" id="start_at"  class="form-control " placeholder="{{ trans('appointments/admin_lang.fields.start_at_helper') }}">
                                                
                                               
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group ">
                                                <label for="hour"> {{ trans('appointments/admin_lang.fields.hour') }}<span class="text-danger">*</span></label>
                                                <input {{ $disabledForm}} value="{{ !empty($appointment->start_at) ? \Carbon\Carbon::parse($appointment->start_at)->format("H:i") : null }}"  type="time"   name="hour" id="hour"  class="form-control timepicker"   placeholder="{{ trans('appointments/admin_lang.fields.hour_helper') }}">
                                   
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div> 
                                <div class="col-12 col-md-6">
                                    <div class="form-group ">
                                        <label for="user_id"> {{ trans('appointments/admin_lang.fields.user_id') }}<span class="text-danger">*</span></label>                                  
                                    
                                            <select {{ $disabledForm}}  class="form-control  select2"  name="user_id" id="user_id" >
                                                <option value=""> {{ trans('appointments/admin_lang.fields.user_id_helper') }}</option>
                                                @foreach ($patientList as $patient)
                                                    <option 
                                                    @if($patient->id==$appointment->user_id)
                                                    selected
                                                    @endif
                                                    value="{{  $patient->id }}">{{  $patient->userProfile->fullName }}</option>
                                                @endforeach
                                            </select>
                                        
                                    </div>
                                
                                </div> 
                                
                            </div>                                
                            <div class="row form-group mb-3">                                
                                <div class="col-12 col-md-6">
                                    
                                    <div class="form-group ">
                                        <label for="doctor_id"> {{ trans('appointments/admin_lang.fields.doctor_id') }}<span class="text-danger">*</span></label>
                                        
                                        <select  {{ $disabledForm }}   class="form-control   select2" name="doctor_id"  >
                                            <option value=""> {{ trans('appointments/admin_lang.fields.doctor_id_helper') }}</option>
                                            @foreach ($doctorList as $doctor)
                                                <option 
                                                @if($doctor->id==$appointment->doctor_id)
                                                selected
                                                @endif
                                                value="{{  $doctor->id }}">{{  $doctor->userProfile->fullName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>      
                                <div class="col-12 col-md-6">
                                    
                                    <div  class="form-group ">
                                        <label for="service_id"> {{ trans('appointments/admin_lang.fields.service_id') }}<span class="text-danger">*</span></label>
                                        
                                        <select {{ $disabledForm }}    class="form-control  select2" name="service_id"  id="service_id"  >
                                            <option  data-id="0" value=""> {{ trans('appointments/admin_lang.fields.service_id_helper') }}</option>
                                            @foreach ($serviceList as $service)
                                            <option 
                                            @if($service->id==$appointment->service_id)
                                            selected
                                            @endif
                                            data-id="{{  $service->price }}" value="{{  $service->id }}">{{  $service->name }} ({{  $service->price }} RD$)</option>
                                                
                                            @endforeach
                                        </select>
                                    </div>
                                </div>      
                            </div>    
                            <div class="row form-group mb-3">                                
                                <div class="col-12 col-md-6">
                                    
                                    <div   class="form-group ">
                                        <label for="insurance_carrier_id"> {{ trans('appointments/admin_lang.fields.insurance_carrier_id') }}</label>
                                    
                                        <select {{ $disabledForm }}    class="form-control select2"  name="insurance_carrier_id"  id="insurance_carrier_id"    >
                                            <option data-id="" value=""> {{ trans('appointments/admin_lang.fields.insurance_carrier_id_helper') }}</option>
                                            @foreach ($insuranceList as $insurance)
                                                <option data-id='{{  $insurance->poliza }}' @if($insurance->id==$appointment->insurance_carrier_id) selected @endif value="{{  $insurance->id }}">{{  $insurance->name }} </option>                                            
                                            @endforeach
                                        </select>
                                        <span id="insuranceServiceApplied" class="text-danger"></span>
                                    
                                    </div>
                                </div>      
                                <div class="col-12 col-md-6">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group ">
                                                <label for="poliza"> {{ trans('appointments/admin_lang.fields.poliza') }}</label>
                                                <input {{ $disabledForm}}  type="text"  name="poliza" id="poliza"    disabled class="form-control "   placeholder="">
                                            </div>
                                        </div>                                    
                                    </div>                              
                                </div>      
                            </div>    
                            <div class="row form-group mb-3">       
                                <div class="col-12 col-md-4">
                                    <div class="form-group ">
                                        <label for="price_with_insurance"> {{ trans('appointments/admin_lang.fields.price_with_insurance') }}</label>
                                        <input {{ $disabledForm}} value="{{ $appointment->price_with_insurance }}"  type="text"  id="price_with_insurance"   name="price_with_insurance"     disabled class="form-control "   placeholder="">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group ">
                                        <label for="applicated_insurance"> {{ trans('appointments/admin_lang.fields.applicated_insurance') }}</label>
                                        <div class="form-check form-switch">
                                          
                                                <input {{ $disabledForm}}  @if($appointment->applicated_insurance==1) checked @endif  class="form-check-input toggle-switch"  name="applicated_insurance"  value="1"   type="checkbox" id="applicated_insurance">
                                         
                                        </div>    
                                    </div>
                                </div>                         
                                <div class="col-12 col-md-4">
                                    
                                    <div class="form-group ">
                                        <label for="total"> {{ trans('appointments/admin_lang.fields.total') }}</label>
                                        <input {{ $disabledForm}} value="{{ $appointment->total}}"  type="text"  id="total" disabled class="form-control "  name="total"     placeholder="">
                                        
                                    </div>
                                </div>     
                            </div>  
                            <div class="row form-group mb-3">                                
                                <div class="col-12 ">
                                    
                                    <div class="form-group ">
                                        <label for="comment"> {{ trans('appointments/admin_lang.fields.comment') }}</label>
                                        <textarea  {{ $disabledForm }}   id="comment"  class="form-control "  name="comment"     placeholder="{{ trans('appointments/admin_lang.fields.comment_helper') }}">{{ $appointment->comment}}</textarea>
                                    </div>
                                </div>     
                            </div>          
                        </div>
                        <div class="card-footer row">
                            <div class="col-12  d-flex justify-content-between">
            
                                <a href="{{ url('admin/appointments') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                                @if (empty( $disabledForm ))
                                    <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>                                       
                                @endif
                            </div>
                        </div>
                    </form>                     
                </div>
            </section>
        </div>
    </div>
</section>
@endsection
@section('foot_page')

<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#poliza').val($('#insurance_carrier_id').find(':selected').data('id'));
        let insurance= $("#insurance_carrier_id").val()==""?0:$("#insurance_carrier_id").val();
        let service= $("#service_id").val()==""?0: $("#service_id").val();
        let user_id=$("#user_id").val()==""?0: $("#user_id").val();
        // insurancePrice(user_id,service, insurance);
        calculateTotal();
    });
    var allowInsurance=false;

    $("#user_id").change(function(){
        $('#insurance_carrier_id').html(" <option data-id='' value=''> {{ trans('appointments/admin_lang.fields.insurance_carrier_id_helper') }}</option>");
        $("#poliza").val("")   ;
        $.ajax({
            url     : "{{ url('admin/appointments/get-insurances') }}/"+$(this).val(),
            type    : 'GET',
            "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            success : function(data) {
                $.each(data, function(index, value) {
                    $('#insurance_carrier_id').append("<option data-id='"+value['poliza']+"' value='"+value['id']+"' >"+value['name']+"</option>");
                    
                });
            }
            
        });
        let insurance= $("#insurance_carrier_id").val()==""?0:$("#insurance_carrier_id").val();
        let service= $("#service_id").val()==""?0: $("#service_id").val();
        let user_id=$(this).val()==""?0: $(this).val();
        insurancePrice(user_id,service, insurance);
    
    });
    $(document).on('change', '#insurance_carrier_id', function(e){
        let poliza= "";
        let insurance= $(this).val()==""?0: $(this).val();
        let service= $("#service_id").val()==""?0: $("#service_id").val();
        let user_id= $("#user_id").val()==""?0: $("#user_id").val();
       
        if($(this).val()==""){
            $(this).val("");
        }else{
            poliza= $(this).find(':selected').data('id')
        }
        insurancePrice(user_id,service, insurance);

        $("#poliza").val(poliza);
    });
    
    
    $(document).on('change', '#service_id', function(e){
        let insurance= $("#insurance_carrier_id").val()==""?0:$("#insurance_carrier_id").val();
        let service= $(this).val()==""?0: $(this).val();
        let user_id= $("#user_id").val()==""?0: $("#user_id").val();       
        
        insurancePrice(user_id,service, insurance);
        
    });
    
    $(document).on('change', '#applicated_insurance', function(e){      
        
    calculateTotal();
        
    });
    
    function insurancePrice(user_id,service_id,insurance_id){
        // $('#insurance_carrier_id').val("");
        $('#price_with_insurance').val("");
        $('#insuranceServiceApplied').html("");
        $('#applicated_insurance').prop("disabled",true);
        $('#applicated_insurance').prop("checked",false);
        
        if(user_id>0 && service_id>0 && insurance_id>0){
            $.ajax({ 
                url     : "{{ url('admin/appointments/get-insurances-price') }}/"+user_id+"/"+service_id+"/"+insurance_id,
                type    : 'GET',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                success : function(data) {
                    $('#price_with_insurance').val(data.price_insurance);

                    if(!data.validServiceInsurance){
                        $('#insuranceServiceApplied').html("Este seguro no aplica para este servicio.");
                        $('#applicated_insurance').prop("disabled",true);
                        $('#applicated_insurance').prop("checked",false);
                        
                    }              else{
                        $('#applicated_insurance').prop("disabled",false);
                        @if($appointment->applicated_insurance)
                        $('#applicated_insurance').prop("checked",true);
                        @else
                        $('#applicated_insurance').prop("checked",false);

                        @endif
                        
                    }        
                }
                
            });
        }
        calculateTotal();
    }

    function calculateTotal(){
        $("#total").val($("#service_id").find(':selected').data('id'));
        
        if($("#service_id").val()!=""){
         
            $("#total").val($("#service_id").find(':selected').data('id'));

            if ($('#applicated_insurance').prop('checked')) {
            
                 $("#total").val($("#price_with_insurance").val());
                
            } 
        }

    }

    @if (!empty($appointment->id)) 
        function facturarElement() {
            Swal.fire({
                title: "{{ trans('appointments/admin_lang.facturar') }}",
            text: "{{ trans('appointments/admin_lang.facturar_question') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('general/admin_lang.yes') }}",
            cancelButtonText: "{{ trans('general/admin_lang.no') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    facturarInfo();
                    
                }
            });
        }

        function facturarInfo() {
            url="{{ route('admin.appointments.facturar',[$appointment->id]) }}";
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'PATCH'},
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                    
                        toastr.success( data.msg)
                       window.location.reload();
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fas fa-bug text-danger' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }

            });
            return false;
        }

        function finalizarElement() {
            Swal.fire({
                title: "{{ trans('appointments/admin_lang.finalizar') }}",
            text: "{{ trans('appointments/admin_lang.finalizar_question') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('general/admin_lang.yes') }}",
            cancelButtonText: "{{ trans('general/admin_lang.no') }}"
            }).then((result) => {
                if (result.isConfirmed) {               
                    finalizarInfo();
                }
            });
        }

        function finalizarInfo() {
            url="{{ route('admin.appointments.finalizar',[$appointment->id]) }}";
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'PATCH'},
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                    
                        toastr.success( data.msg)
                       window.location.reload();
                    } else {
                        $("#modal_alert").addClass('modal-danger');
                        $("#alertModalBody").html("<i class='fas fa-bug text-danger' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                        $("#modal_alert").modal('toggle');
                    }
                    return false;
                }

            });
            return false;
        }     
    @endif

   
    
    
</script>

{!! JsValidator::formRequest('App\Http\Requests\AdminAppointmentRequest')->selector('#formData') !!}
@stop

