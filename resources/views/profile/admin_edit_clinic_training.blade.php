@extends('profile.admin_profile_layout')


@section('tab_head')

@stop

@section('tab_breadcrumb')
<li class="breadcrumb-item active">
    <span>
        {{ $pageTitle }}
    </span>
</li>
@stop

@section('tab_content_3')

<div class="row">

    <div class="col-12">
        <form id="formData" action="{{ route("admin.updateProfileClinicTraining") }}" method="Post" enctype="multipart/form-data" novalidate="false">
            @csrf
            <input type="hidden" name="delete_photo" id="delete_photo">
            <div class="card-body">
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="exequatur"> {{ trans('clinic-personal/admin_lang.fields.exequatur') }}</label>
                            <input value="{{!empty($user->doctorProfile) ? $user->doctorProfile->exequatur :null }}" maxlength="20" type="text" class="form-control" name="doctor_profile[exequatur]"  placeholder="{{ trans('clinic-personal/admin_lang.fields.exequatur_helper') }}">
                        </div>
                    </div>            
                </div> 
                <div class="row form-group mb-3">
                    <div class="col-12 ">                     
                        <div class="form-group">
                            <label for="specialization_id" class="col-12"> {{ trans('clinic-personal/admin_lang.fields.specialization_id') }}</label>
                            <select class="col-12 form-control select2" style="width:100%"  multiple name="doctor_profile[specialization_id][]" id="specialization_id">
                                <option value="">{{ trans('clinic-personal/admin_lang.fields.specialization_id_helper') }}</option>   
                                @foreach ($specializations as $specialization)
                                    <option value="{{ $specialization->id }}" @if(in_array($specialization->id,$specializationsSeledted)) selected @endif >{{ $specialization->name }} </option>
                                @endforeach 
                            </select>    
                        
                        </div>
                    </div>                    
                </div>
            
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>
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
{{-- {!! JsValidator::formRequest('App\Http\Requests\AdminProfilePersonalInfoRequest')->selector('#formData') !!} --}}
@stop