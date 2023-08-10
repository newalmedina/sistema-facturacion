@extends('insurance-carriers.admin_insurance_carriers_layout')


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

<div class="row">
    
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action="@if(empty($insuranceCarrier->id)) {{ route("admin.insurance-carriers.store") }} @else {{ route("admin.insurance-carriers.update",$insuranceCarrier->id) }} @endif" method="post"  novalidate="false">
            @csrf       
           
            @if(empty($insuranceCarrier->id))  
                @method('post')
            @else   
                @method('patch') 
            @endif
              
            <div class="card-body">
                <div class="row form-group mb-3">
                    <div class="col-12">
                     
                        <div class="form-group">
                            <label for="name"> {{ trans('insurance-carriers/admin_lang.fields.name') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($insuranceCarrier->name) ? $insuranceCarrier->name :null }}" type="text" class="form-control" name="name"  placeholder="{{ trans('insurance-carriers/admin_lang.fields.name_helper') }}">
                        </div>
                    </div>      
                </div>
                <div class="row form-group mb-3"">                         
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="image"> {{ trans('insurance-carriers/admin_lang.fields.image') }}</label>
                            <input type="file" accept="image/*" class="form-control d-none" name="image" id="center_image" style="opacity: 0; width: 0;">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nombrefichero" readonly>
                                <span class="input-group-append">
                                    <button id="btnSelectImage" class="btn btn-primary" type="button">{{ trans('profile/admin_lang.fields.search_image') }}</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="phone"> {{ trans('insurance-carriers/admin_lang.fields.phone') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($insuranceCarrier->phone) ? $insuranceCarrier->phone :null }}" type="text" class="form-control" name="phone"  placeholder="{{ trans('insurance-carriers/admin_lang.fields.phone_helper') }}">
                        </div>
                    </div>    
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="email"> {{ trans('insurance-carriers/admin_lang.fields.email') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($insuranceCarrier->email) ? $insuranceCarrier->email :null }}" type="text" class="form-control" name="email"  placeholder="{{ trans('insurance-carriers/admin_lang.fields.email_helper') }}">
                        </div>
                    </div>                        
                </div>

             

                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="province_id" class="col-12"> {{ trans('insurance-carriers/admin_lang.fields.province_id') }}<span class="text-danger">*</span></label>
                            <select class="form-control select2 col-12" style="width: 100%" name="province_id" id="province_id">
                                <option value="">{{ trans('insurance-carriers/admin_lang.fields.province_id_helper') }}</option>   
                                @foreach ($provincesList as $province)
                                    <option value="{{ $province->id }}" @if($insuranceCarrier->province_id ==$province->id) selected @endif>{{ $province->name }}</option>
                                @endforeach 
                            </select>    
                        
                        </div>
                    </div>    
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="municipio_id" class="col-12"> {{ trans('insurance-carriers/admin_lang.fields.municipio_id') }}<span class="text-danger">*</span></label>
                            <select class="form-control select2 col-12" style="width: 100%" name="municipio_id" id="municipio_id">
                                <option value="">{{ trans('insurance-carriers/admin_lang.fields.municipio_id_helper') }}</option>   
                                @foreach ($municipiosList as $municipio)
                                    <option value="{{ $municipio->id }}" @if($insuranceCarrier->municipio_id ==$municipio->id) selected @endif>{{ $municipio->name }}</option>
                                @endforeach 
                            </select>    
                        </div>
                    </div>                        
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12">                     
                        <div class="form-group">
                            <label for="address"> {{ trans('insurance-carriers/admin_lang.fields.address') }}</label>
                            <input value="{{!empty($insuranceCarrier->address) ? $insuranceCarrier->address :null }}" type="text" class="form-control" name="address"  placeholder="{{ trans('insurance-carriers/admin_lang.fields.address_helper') }}">
                        </div>
                    </div>                      
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="active"> {{ trans('insurance-carriers/admin_lang.fields.active') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-switch" @if($insuranceCarrier->active==1) checked @endif value="1" name="active" type="checkbox" id="active">
                            </div>                           
                        </div>
                    </div>                    
                                   
                </div>                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">

                    <a href="{{ url('admin/insurance-carriers') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                    <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>   
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
       
        $("#center_image").change(function(){
                getFileName();
                readURL(this);
            });
       
      });
     
    function getFileName() {
            $('#nombrefichero').val($('#center_image')[0].files[0].name);
            $("#delete_photo").val('1');
            $("#contenedor-remove").css("display","");
    }
    $("#btnSelectImage").click(function() {
        $('#center_image').trigger('click');
    });

    $("#province_id").change(function(){
        $('#municipio_id').html("<option value='' >{{ trans('insurance-carriers/admin_lang.fields.municipio_id_helper') }}</option>");
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
{!! JsValidator::formRequest('App\Http\Requests\AdminInsuranceCarrierRequest')->selector('#formData') !!}
@stop