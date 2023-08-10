@extends('centers.admin_centers_layout')


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
    @php
    $disabled= isset($disabled)?$disabled : null;
@endphp
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action="@if(empty($center->id)) {{ route("admin.centers.store") }} @else {{ route("admin.centers.update",$center->id) }} @endif" method="post"  novalidate="false">
            @csrf       
           
            @if(empty($center->id))  
                @method('post')
            @else   
                @method('patch') 
            @endif
              
            <div class="card-body">
                <div class="row form-group mb-3">
                    <div class="col-12">
                     
                        <div class="form-group">
                            <label for="name"> {{ trans('centers/admin_lang.fields.name') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($center->name) ? $center->name :null }}" type="text"  {{ $disabled }} class="form-control" name="name"  placeholder="{{ trans('centers/admin_lang.fields.name_helper') }}">
                        </div>
                    </div>      
                </div>
                {{-- <div class="row form-group mb-3"">                         
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="image"> {{ trans('centers/admin_lang.fields.image') }}</label>
                            <input type="file" accept="image/*" class="form-control d-none" name="image" id="center_image" style="opacity: 0; width: 0;">
                            <div class="input-group">
                                <input type="text"  {{ $disabled }} class="form-control" id="nombrefichero" readonly>
                                <span class="input-group-append">
                                    <button id="btnSelectImage" class="btn btn-primary" type="button">{{ trans('profile/admin_lang.fields.search_image') }}</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="phone"> {{ trans('centers/admin_lang.fields.phone') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($center->phone) ? $center->phone :null }}" type="text"  {{ $disabled }} class="form-control" name="phone"  placeholder="{{ trans('centers/admin_lang.fields.phone_helper') }}">
                        </div>
                    </div>    
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="email"> {{ trans('centers/admin_lang.fields.email') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($center->email) ? $center->email :null }}" type="text"  {{ $disabled }} class="form-control" name="email"  placeholder="{{ trans('centers/admin_lang.fields.email_helper') }}">
                        </div>
                    </div>                        
                </div>

             

                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="province_id" class="col-12"> {{ trans('centers/admin_lang.fields.province_id') }}<span class="text-danger">*</span></label>
                            <select  {{ $disabled }}  class="form-control select2" name="province_id" id="province_id">
                                <option value="">{{ trans('centers/admin_lang.fields.province_id_helper') }}</option>   
                                @foreach ($provincesList as $province)
                                    <option value="{{ $province->id }}" @if($center->province_id ==$province->id) selected @endif>{{ $province->name }}</option>
                                @endforeach 
                            </select>    
                        
                        </div>
                    </div>    
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="municipio_id" class="col-12"> {{ trans('centers/admin_lang.fields.municipio_id') }} <span class="text-danger">*</span> </label>
                            <select  {{ $disabled }}  class="form-control select2" name="municipio_id" id="municipio_id">
                                <option value="">{{ trans('centers/admin_lang.fields.municipio_id_helper') }}</option>   
                                @foreach ($municipiosList as $municipio)
                                    <option value="{{ $municipio->id }}" @if($center->municipio_id ==$municipio->id) selected @endif>{{ $municipio->name }}</option>
                                @endforeach 
                            </select>    
                        </div>
                    </div>                        
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12">                     
                        <div class="form-group">
                            <label for="address"> {{ trans('centers/admin_lang.fields.address') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($center->address) ? $center->address :null }}" type="text"  {{ $disabled }} class="form-control" name="address"  placeholder="{{ trans('centers/admin_lang.fields.address_helper') }}">
                        </div>
                    </div>                      
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="active"> {{ trans('centers/admin_lang.fields.active') }}</label>
                            <div class="form-check form-switch">
                                <input {{ $disabled }}  class="form-check-input toggle-switch" @if($center->active==1) checked @endif value="1" name="active" type="checkbox" id="active">
                            </div>                           
                        </div>
                    </div>                    
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="default"> {{ trans('centers/admin_lang.fields.default') }}</label>
                            <div class="form-check form-switch">
                                <input {{ $disabled }}  class="form-check-input toggle-switch" @if($center->default==1) checked @endif value="1" name="default" type="checkbox" id="default">
                            </div>                           
                        </div>
                    </div>                    
                </div>                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">

                    <a href="{{ url('admin/centers') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
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
{!! JsValidator::formRequest('App\Http\Requests\AdminCenterRequest')->selector('#formData') !!}
@stop