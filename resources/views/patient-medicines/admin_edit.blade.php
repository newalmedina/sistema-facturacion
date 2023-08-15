@extends('patients.admin_patient_layout')


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
        <form id="formData" enctype="multipart/form-data" action="@if(empty($patient->id)) {{ route("admin.patients.store") }} @else {{ route("admin.patients.update",$patient->id) }} @endif " method="post"  novalidate="false">
            @csrf       
            
            @if(empty($patient->id))  
                @method('post')
            @else   
                @method('patch') 
            @endif
              
            <div class="card-body"> 
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="active"> {{ trans('patients/admin_lang.fields.active') }}</label>
                            <div class="form-check form-switch">
                                <input {{ $disabled }}  class="form-check-input toggle-switch" @if($patient->active==1) checked @endif value="1" name="active" type="checkbox" id="active">
                            </div>                           
                        </div>
                    </div>   
                    @if (!empty($patient->patientProfile))
                        <div class="col-12 col-md-6">                     
                            <div class="form-group">
                                <label for="email"> {{ trans('patients/admin_lang.fields.created_by') }}</label>
                                <input value="{{ !empty($patient->patientProfile)?$patient->patientProfile->createdBy->userProfile->fullname :null}}" type="text" disabled class="form-control" >
                            </div>
                        </div>                       
                    @endif
                </div>

                <div class="row form-group mb-3">                   

                    <div class="col-lg-6">
                     
                        <div class="form-group">
                            <label for="first_name"> {{ trans('patients/admin_lang.fields.first_name') }}<span class="text-danger">*</span></label>
                            <input value="{{ !empty($patient->userProfile)?$patient->userProfile->first_name :null}}" type="text" {{ $disabled }} class="form-control" name="user_profile[first_name]"  placeholder="{{ trans('patients/admin_lang.fields.first_name_helper') }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="last_name"> {{ trans('patients/admin_lang.fields.last_name') }}<span class="text-danger">*</span></label>
                            <input  value="{{ !empty($patient->userProfile)?$patient->userProfile->last_name :null}}"  type="text" {{ $disabled }} class="form-control" name="user_profile[last_name]"  id="last_name" placeholder="{{ trans('patients/admin_lang.fields.last_name_helper') }}">
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">                   

                    <div class="col-lg-6">                     
                        <div class="form-group">
                            <label for="email"> {{ trans('patients/admin_lang.fields.email') }}<span class="text-danger">*</span></label>
                            <input value="{{ !empty($patient->patientProfile)?$patient->patientProfile->email :null}}" type="text" {{ $disabled }} class="form-control" name="patient_profile[email]"  placeholder="{{ trans('patients/admin_lang.fields.email_helper') }}">
                        </div>
                    </div>
                </div>

                <div class="row form-group mb-3">
                    <div class="col-lg-6">
                     
                        <div class="form-group">
                            <label for="birthday"> {{ trans('patients/admin_lang.fields.birthday') }} <span class="text-danger">*</span></label>
                            <input value="{{ !empty($patient->userProfile)?$patient->userProfile->birthdayFormatted :null}}" type="text" {{ $disabled }} class="form-control" autocomplete="off" id="birthday" name="user_profile[birthday]"  placeholder="{{ trans('patients/admin_lang.fields.birthday_helper') }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="identification"> {{ trans('patients/admin_lang.fields.identification') }} <span class="text-danger">*</span></label>
                            <input  value="{{ !empty($patient->userProfile)?$patient->userProfile->identification:null }}" maxlength="15" type="text" {{ $disabled }} class="form-control" name="user_profile[identification]"  id="identification" placeholder="{{ trans('patients/admin_lang.fields.identification_helper') }}">
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">
                    <div class="col-lg-6">
                     
                        <div class="form-group">
                            <label for="phone"> {{ trans('patients/admin_lang.fields.phone') }} <span class="text-danger">*</span></label>
                            <input value="{{ !empty($patient->userProfile)?$patient->userProfile->phone:null }}" type="text"  maxlength="15" {{ $disabled }} class="form-control" name="user_profile[phone]"  placeholder="{{ trans('patients/admin_lang.fields.phone_helper') }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="mobile"> {{ trans('patients/admin_lang.fields.mobile') }}</label>
                            <input  value="{{ !empty($patient->userProfile)?$patient->userProfile->mobile :null}}"  type="text"  maxlength="15" {{ $disabled }} class="form-control" name="user_profile[mobile]"  id="mobile" placeholder="{{ trans('patients/admin_lang.fields.mobile_helper') }}">
                        </div>
                    </div>
                </div>

                <div class="row form-group mb-3">
                    <div class="col-lg-6">
                     
                        <div class="form-group">
                            <label for="gender" class="col-12"> {{ trans('patients/admin_lang.fields.gender') }} <span class="text-danger">*</span></label>
                            <select {{ $disabled }} class="form-control select2" name="user_profile[gender]" id="gender"> 
                                @foreach ($genders as $key=>$value)
                                    <option value="{{ $key }}" @if(!empty($patient->userProfile)?$patient->userProfile->gender:null ==$key) selected @endif>{{ $value }}</option>
                                @endforeach 
                            </select>    
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="province_id" class="col-12"> {{ trans('patients/admin_lang.fields.province_id') }}<span class="text-danger">*</span></label>
                            <select {{ $disabled }} class="form-control select2" name="user_profile[province_id]" id="province_id">
                                <option value="">{{ trans('patients/admin_lang.fields.province_id_helper') }}</option>   
                                @foreach ($provincesList as $province)
                                    <option value="{{ $province->id }}" @if(!empty($patient->userProfile)&& $patient->userProfile->province_id ==$province->id) selected @endif>{{ $province->name }}</option>
                                @endforeach 
                            </select>    
                        
                        </div>
                    </div>    
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="municipio_id" class="col-12"> {{ trans('patients/admin_lang.fields.municipio_id') }} <span class="text-danger">*</span> </label>
                            <select {{ $disabled }} class="form-control select2" name="user_profile[municipio_id]" id="municipio_id">
                                <option value="">{{ trans('patients/admin_lang.fields.municipio_id_helper') }}</option>   
                                @foreach ($municipiosList as $municipio)
                                    <option value="{{ $municipio->id }}" @if(!empty($patient->userProfile)&& $patient->userProfile->municipio_id ==$municipio->id) selected @endif>{{ $municipio->name }}</option>
                                @endforeach 
                            </select>    
                        </div>
                    </div>                        
                </div>
                <div class="row form-group mb-3">
                    <div class="col-lg-12">
                     
                        <div class="form-group">
                            <label for="address"> {{ trans('patients/admin_lang.fields.address') }} <span class="text-danger">*</span></label>
                            <input value="{{ !empty($patient->userProfile)?$patient->userProfile->address :null}}" type="text" {{ $disabled }} class="form-control" name="user_profile[address]"  placeholder="{{ trans('patients/admin_lang.fields.address_helper') }}">
                        </div>
                    </div>
                    
                </div>
                  <div class="row form-group mb-3"">                         
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="image"> {{ trans('patients/admin_lang.fields.photo') }}</label>
                            <input type="file" accept="image/*" class="form-control d-none" name="image" id="patient_image" style="opacity: 0; width: 0;">
                            <div class="input-group">
                                <input type="text"  disabled class="form-control" id="nombrefichero" readonly>
                                <span class="input-group-append">
                                    <button id="btnSelectImage" {{ $disabled }} class="btn btn-primary" type="button">{{ trans('profile/admin_lang.fields.search_image') }}</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">
                    <a href="{{ url('admin/patients') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                    @if (empty( $disabled ))
                        <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>                           
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
        $('#birthday').datepicker(
            {
                language: 'es',
                format: 'dd/mm/yyyy',
                orientation:'bottom',
                autoclose: true
            }
       );
         $("#patient_image").change(function(){
            getFileName();
            readURL(this);
        });
      });

    $("#province_id").change(function(){
        $('#municipio_id').html("<option value='' >{{ trans('centers/admin_lang.fields.municipio_id_helper') }}</option>");
        $.ajax({
            url     : "{{ url('admin/municipios/municipios-list') }}/"+$(this).val(),
            type    : 'GET',
            "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            data: {_method: 'delete'},
            success : function(data) {
                console.log(data)
                $.each(data, function(index, value) {
                    $('#municipio_id').append("<option value='"+value['id']+"' >"+value['name']+"</option>");
                   
                });
            }

        });
    });

    function getFileName() {
            $('#nombrefichero').val($('#patient_image')[0].files[0].name);
            $("#delete_photo").val('1');
            $("#contenedor-remove").css("display","");
    }
   
    $("#btnSelectImage").click(function() {
        $('#patient_image').trigger('click');
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#fileOutput').html("<img src='"+e.target.result+"' id='image_ouptup' width='100%' alt=''>");
                $("#remove").css("display","block");
                //  $('#image_ouptup').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
  

  
  
</script>
{!! JsValidator::formRequest('App\Http\Requests\AdminPatientsRequest')->selector('#formData') !!}
@stop