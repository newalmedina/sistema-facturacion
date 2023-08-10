@extends('services.admin_services_layout')


@section('tab_head')
   <style>
    .role-selected{
        cursor: pointer;
    }
   </style>
@stop

@section('tab_breadcrumb')
    <li class="breadcrumb-item active"><a href="#">{{ $pageTitle }}</a></li>
@stop

@section('tab_content_2')

<div class="row">
    
    <div class="col-12">
       
              
        <form id="formData" enctype="multipart/form-data" action="{{ route("admin.services.updateAditionalInfo",$service->id) }}" method="post"  novalidate="false">
            @csrf 
            @method('patch') 
            
            <div class="card-body">
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-2">
                     
                        <div class="form-group">
                            <label for="price"> {{ trans('services/admin_lang.fields.general_price') }}</label>
                            <input value="{{!empty($service->price) ? $service->price :null }}" disabled type="text" class="form-control" name="realPrice"  placeholder="{{ trans('services/admin_lang.fields.price') }}">
                        </div>
                    </div>                                       
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12">
                     
                        <div class="form-group">
                            <button type="button" id="addInsurance" class="btn btn-xs btn-primary">{{ trans('services/admin_lang.add_insurance') }}</button>
                        </div>
                    </div>                                       
                </div> 
                <div id="insuranceContainer">
                    @php
                        $cont=0;
                    @endphp
                    @foreach ($service->insuranceCarriers as $insurance)
                    
                        <div class="row form-group mb-3">
                            <div class="col-12 col-md-8 mt-2">                             
                                <div class="form-group">
                                    <select class="form-control select2 required-field" name="insurance[{{ $cont }}]][]" id="">
                                        <option value="">{{ trans('services/admin_lang.fields.insurance_id_helper') }}</option>   
                                        @foreach ($insuranceList as $item)
                                            <option value="{{ $item->id }}" @if( $item->id==$insurance->id)  selected @endif >{{ $item->name }}</option>
                                        @endforeach 
                                    </select>    
                                </div>
                            </div>                                       
                            <div class="col-12 col-md-2 mt-2">                             
                                <div class="form-group">
                                    <input value="{{ $insurance->pivot->price  }}"  type="number" class="form-control required-field" name="price[{{ $cont }}]][]"  placeholder="{{ trans('services/admin_lang.fields.price') }}">
                                </div>
                            </div>                                       
                            <div class="col-12 col-md-2 mt-2">                             
                                <div class="form-group">
                                    <button type="button"  class="btn btn-danger remove"> <i class="fa fa-trash" aria-hidden="true"></i></button>
                                </div>
                            </div>                                       
                        </div>
                        @php
                            $cont++;
                        @endphp
                    @endforeach
                </div>                       
               
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">

                    <a href="{{ url('admin/services') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                    <button type="button" id="saveData" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>   
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section("tab_foot")
<script>
    $(document).ready(function(){
        $(".select2").select2();
        var cont = "{{ $cont }}"
        $(document).on('click', '#addInsurance', function() {
            var containerInsurance = $('#insuranceContainer');
            
            var html=`
            <div class="row form-group mb-3">
                <div class="col-12 col-md-8 mt-2">                             
                    <div class="form-group">
                        <select class="form-control select2 required-field" name="insurance[${cont }]" id="">
                            <option value="">{{ trans('services/admin_lang.fields.insurance_id_helper') }}</option>   
                            @foreach ($insuranceList as $item)
                                <option value="{{ $item->id }}"  >{{ $item->name }}</option>
                            @endforeach 
                        </select>   
                    </div>
                </div>                                       
                <div class="col-12 col-md-2 mt-2">                             
                    <div class="form-group">
                        <input value=""  type="number" class="form-control required-field" name="price[${cont }]"  placeholder="{{ trans('services/admin_lang.fields.price') }}">
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
    }); 
    


    
</script>
@stop