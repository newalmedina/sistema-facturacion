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

@section('tab_content_2')
@php
    $disabled= isset($disabled)?$disabled : null;
@endphp

<div class="row">
    
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action=" {{ route("admin.patients.clinicalRecordUpdate",$patient->id) }}  " method="post"  novalidate="false">
            @csrf       
            
            @method('patch') 
        
              
            <div class="card-body"> 
              

                <div class="row form-group mb-3">   
                    <div class="col-lg-12">                     
                        <div class="form-group">
                            <label class="text-primary" for="allergies"> {{ trans('patients/admin_lang.fields.allergies') }}</label>
                            <textarea name="patient_profile[allergies]"  {{ $disabled }}  class="form-control" id="" cols="30" rows="4" placeholder="{{ trans('patients/admin_lang.fields.allergies_helper') }}">{{ !empty($patient->patientProfile)?$patient->patientProfile->allergies :null}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">     
                    <div class="col-lg-12">                     
                        <div class="form-group">
                            <label class="text-primary" for="pathological_diseases"> {{ trans('patients/admin_lang.fields.pathological_diseases') }}</label>
                            <textarea name="patient_profile[pathological_diseases]"  {{ $disabled }}  class="form-control" id="" cols="30" rows="4" placeholder="{{ trans('patients/admin_lang.fields.pathological_diseases_helper') }}">{{ !empty($patient->patientProfile)?$patient->patientProfile->pathological_diseases :null}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">     
                    <div class="col-lg-12">                     
                        <div class="form-group">
                            <label class="text-primary" for="surgical_diseases"> {{ trans('patients/admin_lang.fields.surgical_diseases') }}</label>
                            <textarea name="patient_profile[surgical_diseases]"  {{ $disabled }}  class="form-control" id="" cols="30" rows="4" placeholder="{{ trans('patients/admin_lang.fields.surgical_diseases_helper') }}">{{ !empty($patient->patientProfile)?$patient->patientProfile->surgical_diseases :null}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">     
                    <div class="col-lg-12">                     
                        <div class="form-group">
                            <label class="text-primary" for="family_history"> {{ trans('patients/admin_lang.fields.family_history') }}</label>
                            <textarea name="patient_profile[family_history]"  {{ $disabled }}  class="form-control" id="" cols="30" rows="4" placeholder="{{ trans('patients/admin_lang.fields.family_history_helper') }}">{{ !empty($patient->patientProfile)?$patient->patientProfile->family_history :null}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">     
                    <div class="col-lg-12">                     
                        <div class="form-group">
                            <label class="text-primary" for="gynecological_history"> {{ trans('patients/admin_lang.fields.gynecological_history') }}</label>
                            <textarea name="patient_profile[gynecological_history]"  {{ $disabled }}  class="form-control" id="" cols="30" rows="4" placeholder="{{ trans('patients/admin_lang.fields.gynecological_history_helper') }}">{{ !empty($patient->patientProfile)?$patient->patientProfile->gynecological_history :null}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">     
                    <div class="col-lg-12">                     
                        <div class="form-group">
                            <label class="text-primary" for="others"> {{ trans('patients/admin_lang.fields.others') }}</label>
                            <textarea name="patient_profile[others]"  {{ $disabled }}  class="form-control" id="" cols="30" rows="4" placeholder="{{ trans('patients/admin_lang.fields.others_helper') }}">{{ !empty($patient->patientProfile)?$patient->patientProfile->others :null}}</textarea>
                        </div>
                    </div>
                </div>
                
                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">
                    <a href="{{ url('admin/patients') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
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
{{-- {!! JsValidator::formRequest('App\Http\Requests\AdminPatientsRequest')->selector('#formData') !!} --}}
@stop