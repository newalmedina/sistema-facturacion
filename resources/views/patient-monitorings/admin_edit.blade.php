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

@section('tab_content_6')
@php
    $disabled= isset($disabled)?$disabled : null;
@endphp

{{-- @if(!empty($patientMonitoring->id) && auth()->user()->id ==$patientMonitoring->created_by)  
<div class="row">
    <div class="col-12 d-flex justify-content-end">
        <a href="{{ route('admin.patients.monitorings.generatePdf', ["patient_id" => $patientMonitoring->user_id, "id" => $patientMonitoring->id]) }}" class="btn btn-danger btn-xs"> <i
            class="fa fa-file-pdf mr-1"></i> {{ trans("general/admin_lang.generate_document") }}</a>
    </div>
</div>
@endif --}}

<div class="row">
    
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action="@if(empty($patientMonitoring->id)) {{ route("admin.patients.monitorings.store",['patient_id'=>$patient->id]) }} @else {{ route("admin.patients.monitorings.update", ['patient_id'=>$patient->id,"id"=>$patientMonitoring->id]) }} @endif " method="post"  novalidate="false">
            @csrf       
            
            @if(empty($patientMonitoring->id))  
                @method('post')
            @else   
                @method('patch') 
            @endif
              
            <div class="card-body"> 
            

                <div class="row form-group mb-3">  
                    <div class="col-md-2">                     
                        <div class="form-group">
                            <label for="date"> {{ trans('patient-monitorings/admin_lang.fields.date') }}<span class="text-danger">*</span></label>
                            <input value="{{ $patientMonitoring->dateFormatted }}" name="date" autocomplete="off" type="text" {{ $disabled }} class="form-control datepicker"   placeholder="{{ trans('patient-monitorings/admin_lang.fields.date_helper') }}">
                        </div>
                    </div>
                   <div class="col-md-9">
                    <div class="row">
                        @if(!empty($patientMonitoring->created_by)) 
                        <div class=" offset-md-4 col-md-4">
                         
                            <div class="form-group">
                                <label for="created_by"> {{ trans('patient-monitorings/admin_lang.fields.created_by') }}</label>
                                <input value="{{ $patientMonitoring->createdBy->userProfile->fullName }}" type="text" disabled class="form-control "   placeholder="{{ trans('patient-monitorings/admin_lang.fields.created_by_helper') }}">
                            </div>
                        </div>
                        @endif
                        @if(!empty($patientMonitoring->center_id)) 
                        <div class="col-md-4">
                         
                            <div class="form-group">
                                <label for="center"> {{ trans('patient-monitorings/admin_lang.fields.center') }}</label>
                                <input value="{{ $patientMonitoring->center->name }}" type="text" disabled class="form-control "   placeholder="{{ trans('patient-monitorings/admin_lang.fields.center_helper') }}">
                            </div>
                        </div>
                        @endif
                    </div>
                   </div>
                </div>
                          
               <div class="row">
                <div class="col-12 col-md-3 border border-1 p-2">
                    <div class="row form-group mb-3">  
                        <div class="col-12">
                            <div class="form-group">
                                <label for="height"> {{ trans('patient-monitorings/admin_lang.fields.height') }}</label>
                                <input name="height" value="{{ $patientMonitoring->height }}" type="number" {{ $disabled }} class="form-control "   placeholder="">
                            </div>
                        </div>      
                    </div>               
                    <div class="row form-group mb-3">    
                        <div class="col-12">
                            <div class="form-group">
                                <label for="weight"> {{ trans('patient-monitorings/admin_lang.fields.weight') }}</label>
                                <input name="weight" value="{{ $patientMonitoring->weight }}" type="number" {{ $disabled }} class="form-control "   placeholder="">
                            </div>
                        </div>  
                    </div>               
                    <div class="row form-group mb-3">   
                        <div class="col-12">
                            <div class="form-group">
                                <label for="temperature"> {{ trans('patient-monitorings/admin_lang.fields.temperature') }}</label>
                                <input name="temperature" value="{{ $patientMonitoring->temperature }}" type="number" {{ $disabled }} class="form-control "   placeholder="">
                            </div>
                        </div>    
                    </div>               
                    <div class="row form-group mb-3">    
                        <div class="col-12">
                            <div class="form-group">
                                <label for="heart_rate"> {{ trans('patient-monitorings/admin_lang.fields.heart_rate') }}</label>
                                <input name="heart_rate" value="{{ $patientMonitoring->heart_rate }}" type="number" {{ $disabled }} class="form-control "   placeholder="">
                            </div>
                        </div>    
                    </div>               
                    <div class="row form-group mb-3">   
                        <div class="col-12">
                            <div class="form-group">
                                <label for="blood_presure"> {{ trans('patient-monitorings/admin_lang.fields.blood_presure') }}</label>
                                <input name="blood_presure" value="{{ $patientMonitoring->blood_presure }}" type="number" {{ $disabled }} class="form-control "   placeholder="">
                            </div>
                        </div>      
                    </div>               
                    <div class="row form-group mb-3">  
                        <div class="col-12">
                            <div class="form-group">
                                <label for="rheumatoid_factor"> {{ trans('patient-monitorings/admin_lang.fields.rheumatoid_factor') }}</label>
                                <input name="rheumatoid_factor" value="{{ $patientMonitoring->rheumatoid_factor }}" type="number" {{ $disabled }} class="form-control "   placeholder="">
                            </div>
                        </div>   
                    </div>  
                </div>
                <div class="col-12 col-md-9 border border-1 p-2">
                    <div class="row form-group mb-3">                   
                        <div class="accordion" id="accordion4">
                            <div class="card card-default">
                                <div class="card-header">
                                    <h4 class="card-title m-0">
                                        <a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion4" data-bs-target="#motiveCollapse">
                                            <label for="motive"> {{ trans('patient-monitorings/admin_lang.fields.motive') }}</label> 
                                        </a>
                                    </h4>
                                </div>
                                <div id="motiveCollapse" class="collapse show" data-bs-parent="#accordion4">
                                    <div class="card-body">
                                        <textarea name="motive" class="form-control textarea" id="motive" cols="30" rows="10">{{ $patientMonitoring->motive }}</textarea>                           
                                    </div>
                                </div>
                            </div>
                            <div class="card card-default">
                                <div class="card-header">
                                    <h4 class="card-title m-0">
                                        <a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion4" data-bs-target="#physical_explorationCollapse">
                                            <label for="physical_exploration"> {{ trans('patient-monitorings/admin_lang.fields.physical_exploration') }}</label> 
                                        </a>
                                    </h4>
                                </div>
                                <div id="physical_explorationCollapse" class="collapse" data-bs-parent="#accordion4">
                                    <div class="card-body">
                                        <textarea name="physical_exploration" class="form-control textarea" id="physical_exploration" cols="30" rows="10">{{ $patientMonitoring->physical_exploration }}</textarea>                           
                                    </div>
                                </div>
                            </div>
                            <div class="card card-default">
                                <div class="card-header">
                                    <h4 class="card-title m-0">
                                        <a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion4" data-bs-target="#symptomsCollapse">
                                            <label for="symptoms"> {{ trans('patient-monitorings/admin_lang.fields.symptoms') }}</label> 
                                        </a>
                                    </h4>
                                </div>
                                <div id="symptomsCollapse" class="collapse" data-bs-parent="#accordion4">
                                    <div class="card-body">
                                        <textarea name="symptoms" class="form-control textarea" id="symptoms" cols="30" rows="10">{{ $patientMonitoring->symptoms }}</textarea>                           
                                    </div>
                                </div>
                            </div>
                            <div class="card card-default">
                                <div class="card-header">
                                    <h4 class="card-title m-0">
                                        <a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion4" data-bs-target="#diagnosisCollapse">
                                            <label for="diagnosis"> {{ trans('patient-monitorings/admin_lang.fields.diagnosis_id') }}</label> 
                                        </a>
                                    </h4>
                                </div>
                                <div id="diagnosisCollapse" class="collapse" data-bs-parent="#accordion4">
                                    <div class="card-body">
                                        <select class="form-control select2" style="width: 100%" multiple name="diagnosis_id[]" id="diagnosis_id">   
                                            @foreach ($diagnosisList as $diagnosis)
                                                <option value="{{ $diagnosis->id }}" @if( $diagnosis->id==in_array($diagnosis->id,$diagnosisSelected))  selected @endif >{{ $diagnosis->name }}</option>
                                            @endforeach 
                                        </select>    
                                    </div>
                                </div>
                            </div>
                            <div class="card card-default">
                                <div class="card-header">
                                    <h4 class="card-title m-0">
                                        <a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion4" data-bs-target="#commentCollapse">
                                            <label for="comment"> {{ trans('patient-monitorings/admin_lang.fields.comment') }}</label> 
                                        </a>
                                    </h4>
                                </div>
                                <div id="commentCollapse" class="collapse" data-bs-parent="#accordion4">
                                    <div class="card-body">
                                        <textarea name="comment" class="form-control textarea" id="comment" cols="30" rows="10">{{ $patientMonitoring->comment }}</textarea>                           
                                    </div>
                                </div>
                            </div>
                        </div>                       
                    </div>
                </div>
               </div>
              
                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">
                    <a href="{{ route('admin.patients.monitorings',["patient_id"=>$patient->id]) }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                    @if (empty( $disabled ))
                        <button type="submit" id="saveData" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>                           
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section("tab_foot")

    
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script type="text/javascript" src="{{ asset("assets/admin/vendor/tinymce/tinymce.min.js") }}">       </script>
<script>

    $(document).ready(function() {
        $('.select2').select2();
        $('.datepicker').datepicker(
            {
                language: 'es',
                format: 'dd/mm/yyyy',
                orientation:'bottom',
                autoclose: true
            }
        );    
        tinymce.init({
            selector: "textarea.textarea",
            setup: function (editor) {
                editor.on('keyup', function () {
                    var content = editor.getContent();
                    document.getElementById('hideDescription').value = content;
                });
            },
            
            menubar: false,
            height: 200,
            resize:false,
            convert_urls: false,
            @isset($disabled)
                        readonly : 1,
                    @endisset
            // extended_valid_elements : "a[class|name|href|target|title|onclick|rel],script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]",
            plugins: [
                "textcolor",
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table paste hr",
                "wordcount fullscreen nonbreaking visualblocks",
            ],
            content_css: [
                {{--
                // Ponemos aquí los css de front
                '{{ url('assets/front/vendor/bootstrap/css/bootstrap.min.css') }}',
                '{{ url('assets/front/vendor/fontawesome/css/font-awesome.min.css') }}',
                '{{ url('assets/front/css/front.min.css') }}',
                '{{ url('assets/front/css/theme.css') }}',
                '{{ url('assets/front/css/theme-element.css') }}',
                '{{ url('assets/front/vendor/fontawesome/css/font-awesome.min.css') }}'
                --}}
                ],
            toolbar: "forecolor backcolor | insertfile undo redo | styleselect | fontsizeselect | bold italic forecolor, backcolor | hr nonbreaking visualblocks | table |  alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link media image | code fullscreen",
            file_picker_callback: function(callback, value, meta) {
                openImageControllerExt(callback, '0');
            }
        });
    });
   
    
    
</script>
{!! JsValidator::formRequest('App\Http\Requests\AdminPatientMonitoringsRequest')->selector('#formData') !!}
@stop