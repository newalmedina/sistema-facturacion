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

@section('tab_content_4')
@php
    $disabled= isset($disabled)?$disabled : null;
@endphp

@if(!empty($medicine->id) && auth()->user()->id ==$medicine->created_by)  
<div class="row">
    <div class="col-12 d-flex justify-content-end">
        <a href="{{ route('admin.patients.medicines.generatePdf', ["patient_id" => $medicine->user_id, "id" => $medicine->id]) }}" class="btn btn-danger btn-xs"> <i
            class="fa fa-file-pdf mr-1"></i> {{ trans("general/admin_lang.generate_document") }}</a>
    </div>
</div>
@endif

<div class="row">
    
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action="@if(empty($medicine->id)) {{ route("admin.patients.medicines.store",['patient_id'=>$patient->id]) }} @else {{ route("admin.patients.medicines.update", ['patient_id'=>$patient->id,"id"=>$medicine->id]) }} @endif " method="post"  novalidate="false">
            @csrf       
            
            @if(empty($medicine->id))  
                @method('post')
            @else   
                @method('patch') 
            @endif
              
            <div class="card-body"> 
            

                <div class="row form-group mb-3">                   

                    <div class="col-md-2">
                     
                        <div class="form-group">
                            <label class='text-primary' for="date"> {{ trans('patient-medicines/admin_lang.fields.date') }}<span class="text-danger">*</span></label>
                            <input value="{{ $medicine->dateFormatted }}" name="date" autocomplete="off" type="text" {{ $disabled }} autocomplete="off" class="form-control datepicker"   placeholder="{{ trans('patient-medicines/admin_lang.fields.date_helper') }}">
                        </div>
                    </div>
                   <div class="col-md-10">
                    <div class="row">
                        @if(!empty($medicine->created_by)) 
                        <div class=" offset-md-4 col-md-4">
                         
                            <div class="form-group">
                                <label class='text-primary' for="created_by"> {{ trans('patient-medicines/admin_lang.fields.created_by') }}</label>
                                <input value="{{ $medicine->createdBy->userProfile->fullName }}" type="text" disabled class="form-control "   placeholder="{{ trans('patient-medicines/admin_lang.fields.created_by_helper') }}">
                            </div>
                        </div>
                        @endif
                        @if(!empty($medicine->center_id)) 
                        <div class="col-md-4">
                         
                            <div class="form-group">
                                <label class='text-primary' for="center"> {{ trans('patient-medicines/admin_lang.fields.center') }}</label>
                                <input value="{{ $medicine->center->name }}" type="text" disabled class="form-control "   placeholder="{{ trans('patient-medicines/admin_lang.fields.center_helper') }}">
                            </div>
                        </div>
                        @endif
                    </div>
                   </div>
                </div>
                
                <div class="row form-group mb-3">
                    @if (empty( $disabled ))
                    <div class="col-12">
                     
                        <div class="form-group">
                            <button type="button" id="addInsurance" class="btn btn-xs btn-primary">{{ trans('patient-medicines/admin_lang.add_medicine') }}</button>
                        </div>
                    </div>                                       
                    @endif
                </div> 
                <div id="insuranceContainer" class="mb-3">
                    @php
                        $cont=0;
                    @endphp
                    @foreach ($medicine->details as $detail)
                       <div class="row">
                        <div class="col-12 col-md-3 mt-2">                             
                            <div class="form-group">
                                <input value="{{ $detail->medicine }}"  type="text" {{ $disabled }} class="form-control required-field" name="medicine[{{ $cont }}]"  placeholder="{{ trans('patient-medicines/admin_lang.fields.medicine') }}">
                            </div>
                        </div>                                       
                        <div class="col-12 col-md-2 mt-2">                             
                            <div class="form-group">
                                <input value="{{ $detail->dosis }}"  type="text" {{ $disabled }} class="form-control required-field" name="dosis[{{ $cont }}]"  placeholder="{{ trans('patient-medicines/admin_lang.fields.dosis') }}">
                            </div>
                        </div>                                       
                        <div class="col-12 col-md-2 mt-2">                             
                            <div class="form-group">
                               
                                <input value="{{ $detail->frecuency }}"  type="text" {{ $disabled }} class="form-control required-field" name="frecuency[{{ $cont }}]"  placeholder="{{ trans('patient-medicines/admin_lang.fields.frecuency') }}">
                            </div>
                        </div>                                       
                        <div class="col-12 col-md-3 mt-2">                             
                            <div class="form-group">
                               
                                <input value="{{ $detail->period }}"  type="text" {{ $disabled }} class="form-control required-field" name="period[{{ $cont }}]"  placeholder="{{ trans('patient-medicines/admin_lang.fields.period') }}">
                            </div>
                        </div>   
                            @if (empty( $disabled ))                                   
                                <div class="col-12 col-md-2 mt-2">                             
                                    <div class="form-group">
                                        <button type="button"  class="btn btn-danger btn-xs remove "> <i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                </div>         
                            @endif 
                        @php
                            $cont++;
                        @endphp
                       </div>
                    @endforeach
                </div> 
                <hr>
                 <div class="row form-group mb-3">                   

                    <div class="col-12">
                     
                        <div class="form-group">
                            <label class='text-primary' for="comment"> {{ trans('patient-medicines/admin_lang.fields.comment') }}</label> 
                            <textarea name="comment" class="form-control textarea" id="" cols="30" rows="10">{{ $medicine->comment }}</textarea>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">
                    <a href="{{ route('admin.patients.medicines',["patient_id"=>$patient->id]) }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                    @if (empty( $disabled ))
                        <button type="button" id="saveData" class="btn btn-primary">{{ trans('general/admin_lang.save') }}</button>                           
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

    var cont = "{{ $cont }}"
    $(document).on('click', '#addInsurance', function() {
        var containerInsurance = $('#insuranceContainer');
        
        var html=`
        <div class="row form-group s">
            <div class="col-12 col-md-3 mt-2">                             
                <div class="form-group">
                    <input value=""  type="text" {{ $disabled }} class="form-control required-field" name="medicine[${cont }]"  placeholder="{{ trans('patient-medicines/admin_lang.fields.medicine') }}">
                </div>
            </div>                                       
            <div class="col-12 col-md-2 mt-2">                             
                <div class="form-group">
                    <input value=""  type="text" {{ $disabled }} class="form-control required-field" name="dosis[${cont }]"  placeholder="{{ trans('patient-medicines/admin_lang.fields.dosis') }}">
                </div>
            </div>                                       
            <div class="col-12 col-md-2 mt-2">                             
                <div class="form-group">
                    <input value=""  type="text" {{ $disabled }} class="form-control required-field" name="frecuency[${cont }]"  placeholder="{{ trans('patient-medicines/admin_lang.fields.frecuency') }}">
                </div>
            </div>                                       
            <div class="col-12 col-md-3 mt-2">                             
                <div class="form-group">
                    <input value=""  type="text" {{ $disabled }} class="form-control required-field" name="period[${cont }]"  placeholder="{{ trans('patient-medicines/admin_lang.fields.period') }}">
                </div>
            </div>                                       
            <div class="col-12 col-md-2 mt-2">                             
                <div class="form-group">
                    <button type="button"  class="btn btn-danger btn-xs remove"> <i class="fa fa-trash" aria-hidden="true"></i></button>
                </div>
            </div>                                       
        </div>
        `;
        containerInsurance.append(html);
        cont ++;
        $(".select2").select2();
    });

    $(document).on('click', '.remove', function() {
    // Code to execute when the element is clicked
        $(this).parent().parent().parent().remove();
    });
    $(document).on('click', '#saveData', function(event) {
    // Code to execute when the element is clicked
        event.preventDefault();
        var  hayCamposNulos=false;
    
        $(".required-field").each(function() {
            const valor = $(this).val().trim(); // Obtenemos el valor del campo y eliminamos espacios en blanco
            if (valor == "") {
                hayCamposNulos = true;
                // Agregamos una clase o estilo para resaltar campos nulos (opcional)
                $(this).addClass("is-invalid");
            } else {
                // Quitamos la clase de resaltado en caso de que el campo tenga valor
                $(this).removeClass("is-invalid");
            }
        });

        // Si hay campos nulos, mostramos un mensaje de error (opcional)
        if (hayCamposNulos) {
            toastr.error(" {{ trans('general/admin_lang.required_files') }}")
        }else{
            $("#formData").submit();
        }
    });
        // Delegar evento de validación a los campos con la clase "mi-campo"
    $(document).on("blur", ".required-field", function() {
        const valor = $(this).val().trim();
        if (valor == "") {
        $(this).addClass("is-invalid");
        } else {
        $(this).removeClass("is-invalid");
        }
    });
    $(document).on("focusout", ".required-field", function() {
        const valor = $(this).val().trim();
        if (valor == "") {
        $(this).addClass("is-invalid");
        } else {
        $(this).removeClass("is-invalid");
        }
    });

    
</script>
{!! JsValidator::formRequest('App\Http\Requests\AdminPatientMedicinesRequest')->selector('#formData') !!}
@stop