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

@section('tab_content_5')
@php
    $disabled= isset($disabled)?$disabled : null;
@endphp

@if(!empty($medicalStudies->id) && auth()->user()->id ==$medicalStudies->created_by)  
<div class="row">
    <div class="col-12 d-flex justify-content-end">
        <a href="{{ route('admin.patients.medical-studies.generatePdf', ["patient_id" => $medicalStudies->user_id, "id" => $medicalStudies->id]) }}" class="btn btn-danger btn-xs"> <i
            class="fa fa-file-pdf mr-1"></i> {{ trans("general/admin_lang.generate_document") }}</a>
    </div>
</div>
@endif

<div class="row">
    
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action="@if(empty($medicalStudies->id)) {{ route("admin.patients.medical-studies.store",['patient_id'=>$patient->id]) }} @else {{ route("admin.patients.medical-studies.update", ['patient_id'=>$patient->id,"id"=>$medicalStudies->id]) }} @endif " method="post"  novalidate="false">
            @csrf       
            
            @if(empty($medicalStudies->id))  
                @method('post')
            @else   
                @method('patch') 
            @endif
              
            <div class="card-body"> 
            

                <div class="row form-group mb-3">                   

                    <div class="col-md-2">
                     
                        <div class="form-group">
                            <label for="date"> {{ trans('patient-medical-studies/admin_lang.fields.date') }}<span class="text-danger">*</span></label>
                            <input value="{{ $medicalStudies->dateFormatted }}" name="date" autocomplete="off" type="text" {{ $disabled }} class="form-control datepicker"   placeholder="{{ trans('patient-medical-studies/admin_lang.fields.date_helper') }}">
                        </div>
                    </div>
                   <div class="col-md-9">
                    <div class="row">
                        @if(!empty($medicalStudies->created_by)) 
                        <div class=" offset-md-4 col-md-4">
                         
                            <div class="form-group">
                                <label for="created_by"> {{ trans('patient-medical-studies/admin_lang.fields.created_by') }}</label>
                                <input value="{{ $medicalStudies->createdBy->userProfile->fullName }}" type="text" disabled class="form-control "   placeholder="{{ trans('patient-medical-studies/admin_lang.fields.created_by_helper') }}">
                            </div>
                        </div>
                        @endif
                        @if(!empty($medicalStudies->center_id)) 
                        <div class="col-md-4">
                         
                            <div class="form-group">
                                <label for="center"> {{ trans('patient-medical-studies/admin_lang.fields.center') }}</label>
                                <input value="{{ $medicalStudies->center->name }}" type="text" disabled class="form-control "   placeholder="{{ trans('patient-medical-studies/admin_lang.fields.center_helper') }}">
                            </div>
                        </div>
                        @endif
                    </div>
                   </div>
                </div>
                          
               
                 <div class="row form-group mb-3">                   

                    <div class="col-12">
                     
                        <div class="form-group">
                            <label for="description"> {{ trans('patient-medical-studies/admin_lang.fields.description') }}</label> 
                            <textarea name="description" class="form-control textarea" id="description" cols="30" rows="10">{{ $medicalStudies->description }}</textarea>
                            <textarea name="hideDescription" style="visibility: hidden; position: absolute; top: -9999px; left: -9999px;" id="hideDescription">{{ $medicalStudies->description }}</textarea>
                            {{--   --}}
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">
                    <a href="{{ route('admin.patients.medical-studies',["patient_id"=>$patient->id]) }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
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
            height: 300,
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
{!! JsValidator::formRequest('App\Http\Requests\AdminPatientMedicalStudiesRequest')->selector('#formData') !!}
@stop