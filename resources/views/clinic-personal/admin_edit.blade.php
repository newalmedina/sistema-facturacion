@extends('clinic-personal.admin_clinic_personal_layout')


@section('tab_head')

@stop

@section('tab_breadcrumb')
    <li class="breadcrumb-item active">
        <span>
            {{ $pageTitle }} 
          </span>
    </li>
@stop

@section('tab_content_1')
@php
    $disabled= isset($disabled)?$disabled : null;
@endphp

<div class="row">
    
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action="{{ route("admin.clinic-personal.update",$clinicPersonal->id) }} " method="post"  novalidate="false">
            @csrf       
            
                @method('patch') 
           
              
            <div class="card-body"> 
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label class='text-primary' for="phone"> {{ trans('clinic-personal/admin_lang.fields.phone') }}</label>
                            <input value="{{!empty($clinicPersonal->userProfile) ? $clinicPersonal->userProfile->phone.' / '. $clinicPersonal->userProfile->mobile :null }}" type="text" disabled class="form-control" name="phone"  placeholder="{{ trans('clinic-personal/admin_lang.fields.phone_helper') }}">
                        </div>
                    </div>    
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label class='text-primary' for="email"> {{ trans('clinic-personal/admin_lang.fields.email') }}</label>
                            <input value="{{!empty($clinicPersonal->email) ? $clinicPersonal->email :null }}" type="text" disabled class="form-control" name="email"  placeholder="{{ trans('clinic-personal/admin_lang.fields.email_helper') }}">
                        </div>
                    </div>                        
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label class='text-primary' for="exequatur"> {{ trans('clinic-personal/admin_lang.fields.exequatur') }}</label>
                            <input value="{{!empty($clinicPersonal->doctorProfile) ? $clinicPersonal->doctorProfile->exequatur :null }}" maxlength="20" type="text" {{ $disabled }} class="form-control" name="doctor_profile[exequatur]"  placeholder="{{ trans('clinic-personal/admin_lang.fields.exequatur_helper') }}">
                        </div>
                    </div>            
                </div> 
                <div class="row form-group mb-3">
                    <div class="col-12 ">                     
                        <div class="form-group">
                            <label class='text-primary' for="specialization_id" class="col-12"> {{ trans('clinic-personal/admin_lang.fields.specialization_id') }}</label>
                            <select {{ $disabled }} class="col-12 form-control select2" style="width:100%"  multiple name="doctor_profile[specialization_id][]" id="specialization_id">
                                <option value="">{{ trans('clinic-personal/admin_lang.fields.specialization_id_helper') }}</option>   
                                @foreach ($specializations as $specialization)
                                    <option value="{{ $specialization->id }}" @if(in_array($specialization->id,$specializationsSeledted)) selected @endif >{{ $specialization->name }} </option>
                                @endforeach 
                            </select>    
                        
                        </div>
                    </div>                    
                </div>
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">
                    <a href="{{ url('admin/clinic-personal') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                    @if (empty( $disabled ))
                        <button type="submit" class="btn btn-primary">{{ trans('general/admin_lang.save') }}</button>                           
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section("tab_foot")

    
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script>

     $(document).ready(function() {
        $('.select2').select2();
       
   
      });
     
  
  
</script>
{!! JsValidator::formRequest('App\Http\Requests\AdminClinicPersonalRequest')->selector('#formData') !!}
@stop