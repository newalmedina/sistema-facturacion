@extends('services.admin_services_layout')


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
        <form id="formData" enctype="multipart/form-data" action="@if(empty($service->id)) {{ route("admin.services.store") }} @else {{ route("admin.services.update",$service->id) }} @endif" method="post"  novalidate="false">
            @csrf       
           
            @if(empty($service->id))  
                @method('post')
            @else   
                @method('patch') 
            @endif
              
            <div class="card-body">
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="name"> {{ trans('services/admin_lang.fields.name') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($service->name) ? $service->name :null }}" type="text" class="form-control" name="name"  placeholder="{{ trans('services/admin_lang.fields.name_helper') }}">
                        </div>
                    </div>      
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="price"> {{ trans('services/admin_lang.fields.price') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($service->price) ? $service->price :null }}" type="number" min = "0" class="form-control" name="price"  placeholder="{{ trans('services/admin_lang.fields.price_helper') }}">
                        </div>
                    </div>      
                </div>
               
                <div class="row form-group mb-3">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="description"> {{ trans('services/admin_lang.fields.description') }}</label>
                            <textarea placeholder="{{ trans('services/admin_lang.fields.description_helper') }}" class="form-control" name="description" id="description"  cols="30" rows="10">{{ !empty($service->description)?$service->description:null }}</textarea>
                         </div>
                    </div>               
                </div>
               
             
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label for="active"> {{ trans('services/admin_lang.fields.active') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-switch" @if($service->active==1) checked @endif value="1" name="active" type="checkbox" id="active">
                            </div>                           
                        </div>
                    </div>                    
                                 
                </div>                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">

                    <a href="{{ url('admin/services') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
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
        $('#municipio_id').html("<option value='' >{{ trans('services/admin_lang.fields.municipio_id_helper') }}</option>");
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
{!! JsValidator::formRequest('App\Http\Requests\AdminServiceRequest')->selector('#formData') !!}
@stop