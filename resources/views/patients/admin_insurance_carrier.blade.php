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

@section('tab_content_3')
@php
    $disabled= isset($disabled)?$disabled : null;
    $cont=0;
@endphp

<div class="row">
    
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action=" {{ route("admin.patients.insuranceCarrierUpdate",$patient->id) }}  " method="post"  novalidate="false">
            @csrf       
            
            @method('patch') 
        
              
            <div class="card-body"> 
                <div class="row form-group mb-3">
                    @if (empty( $disabled ))
                    <div class="col-12">
                     
                        <div class="form-group">
                            <button type="button" id="addInsurance" class="btn btn-xs btn-primary">{{ trans('patients/admin_lang.add_insurance') }}</button>
                        </div>
                    </div>                                       
                    @endif
                </div> 
                <div id="insuranceContainer">
                    @php
                        $cont=0;
                    @endphp
                    @foreach ($patient->insuranceCarriers as $insurance)
                       <div class="row">
                        <div class="col-12 col-md-7 mt-2">                             
                            <div class="form-group">
                                <select {{ $disabled }} class="form-control select2 required-field" name="insurance[{{ $cont }}]" id="">
                                    <option value="">{{ trans('patients/admin_lang.fields.insurance_id_helper') }}</option>   
                                    @foreach ($insuranceList as $item)
                                        <option value="{{ $item->id }}"@if($insurance->id ==$item->id) selected @endif  >{{ $item->name }}</option>
                                    @endforeach 
                                </select>   
                            </div>
                        </div>                                       
                        <div class="col-12 col-md-3 mt-2">                             
                            <div class="form-group">
                                <input value="{{ $insurance->pivot->poliza }}"  type="text" {{ $disabled }} class="form-control required-field" name="poliza[{{ $cont }}]]"  placeholder="{{ trans('patients/admin_lang.fields.poliza') }}">
                            </div>
                        </div>     
                            @if (empty( $disabled ))                                   
                                <div class="col-12 col-md-2 mt-2">                             
                                    <div class="form-group">
                                        <button type="button"  class="btn btn-danger remove"> <i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </div>
                                </div>         
                            @endif 
                        @php
                            $cont++;
                        @endphp
                       </div>
                    @endforeach
                </div> 
                
                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">
                    <a href="{{ url('admin/patients') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
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
<script>

    $(".select2").select2();
        var cont = "{{ $cont }}"
        $(document).on('click', '#addInsurance', function() {
            var containerInsurance = $('#insuranceContainer');
            
            var html=`
            <div class="row form-group mb-3">
                <div class="col-12 col-md-7 mt-2">                             
                    <div class="form-group">
                        <select {{ $disabled }} class="form-control select2 required-field" name="insurance[${cont }]" id="">
                            <option value="">{{ trans('patients/admin_lang.fields.insurance_id_helper') }}</option>   
                            @foreach ($insuranceList as $item)
                                <option value="{{ $item->id }}"  >{{ $item->name }}</option>
                            @endforeach 
                        </select>   
                    </div>
                </div>                                       
                <div class="col-12 col-md-3 mt-2">                             
                    <div class="form-group">
                        <input value=""  type="text" {{ $disabled }} class="form-control required-field" name="poliza[${cont }]"  placeholder="{{ trans('patients/admin_lang.fields.poliza') }}">
                    </div>
                </div>                                       
                <div class="col-12 col-md-2 mt-2">                             
                    <div class="form-group">
                        <button type="button"  class="btn btn-danger remove"> <i class="fa fa-trash" aria-hidden="true"></i></button>
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
{{-- {!! JsValidator::formRequest('App\Http\Requests\AdminPatientsRequest')->selector('#formData') !!} --}}
@stop