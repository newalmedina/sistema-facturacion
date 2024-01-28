@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')
<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />
    
@stop

@section('breadcrumb')
<li><span>{{ $title }}</span></li>
@stop

@section('content')
<section role="main" class="content-body card-margin">
      
    <!-- start: page -->
  
    @include('layouts.admin.includes.modals')
    @include('layouts.admin.includes.errors')        
    <div class="row mt-5">
        <div class="col-12 col-md-3">
            <section class="card">
            
                <div class="card-body">
                    
                    <div class="thumb-info mb-3">
                        <div id="fileOutput">
                            @if(!empty($setting->image))
                                <img src='{{ url('admin/settings/get-image/'.$setting->image) }}' id='image_ouptup' class="rounded img-fluid" >
                            @else
                                <img src="{{ asset("/assets/front/img/!logged-user.jpg") }}" class="rounded img-fluid" >
                            @endif
                        </div>
                        @if (!$disabledForm)                            
                            <div id="remove"  onclick="deleteElement()" class="text-danger mt-2" style="@if($setting->image=='') display: none; @endif cursor: pointer; text-align: center;"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('settings/admin_lang.quit_image') }} </div>
                        @endif
    
                    </div>
                    
                </div>
            </section>
        </div>
        <div class="col-12 col-md-9">
            <div class="tabs tabs-primary">
                <ul class="nav nav-tabs" id="custom-tabs">            
                    <li class="nav-item ">
                        <a id="tab_1" class="nav-link" data-bs-target="#tab_1-1" data-bs-toggle="tabajax"
                            href="#"
                            data-target="#tab_1-1" aria-controls="tab_1-1" aria-selected="true">
                            {{ trans('settings/admin_lang.general_info') }}
                        </a>
                    </li>
                  
                </ul>
                <div class="tab-content" id="tab_tabContent">
                    <div id="tab_1-1" class="tab-pane  active ">

                        <form id="formData" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}" method="post"  novalidate="false">
                            @csrf 
                             @method('patch')
                          
                              
                            <div class="card-body">
                                <div class="row form-group mb-3">
                                    <div class="col-12">
                                     
                                        <div class="form-group">
                                            <label for="name"> {{ trans('settings/admin_lang.general_info_fields.site_name') }}<span class="text-danger">*</span></label>
                                            <input value="{{!empty($setting->site_name) ? $setting->site_name :null }}" type="text"  @if ($disabledForm) disabled  @endif      class="form-control"    name="site_name"  placeholder="{{ trans('settings/admin_lang.general_info_fields.site_name_helper') }}">
                                        </div>
                                    </div>      
                                </div>
                                 <div class="row form-group mb-3"">                         
                                    <div class="col-lg-12>
                                        <div class="form-group">
                                            <label for="image"> {{ trans('settings/admin_lang.general_info_fields.image') }}</label>
                                            <input type="file" accept="image/*"  @if ($disabledForm) disabled  @endif      class="form-control d-none" name="image" id="setting_image" style="opacity: 0; width: 0;">
                                            <div class="input-group">
                                                <input type="text"       class="form-control"   id="nombrefichero" readonly>
                                                <span class="input-group-append">
                                                    <button id="btnSelectImage"  @if ($disabledForm) disabled  @endif        class="btn btn-primary" type="button">{{ trans('settings/admin_lang.general_info_fields.search_image') }}</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group mb-3">
                                    <div class="col-12 col-md-6">                     
                                        <div class="form-group">
                                            <label for="phone"> {{ trans('settings/admin_lang.general_info_fields.phone') }}<span class="text-danger">*</span></label>
                                            <input value="{{!empty($setting->phone) ? $setting->phone :null }}" type="text"  @if ($disabledForm) disabled  @endif      class="form-control"   name="phone"  placeholder="{{ trans('settings/admin_lang.general_info_fields.phone_helper') }}">
                                        </div>
                                    </div>    
                                    <div class="col-12 col-md-6">                     
                                        <div class="form-group">
                                            <label for="email"> {{ trans('settings/admin_lang.general_info_fields.email') }}<span class="text-danger">*</span></label>
                                            <input value="{{!empty($setting->email) ? $setting->email :null }}" type="text"  @if ($disabledForm) disabled  @endif      class="form-control"   name="email"  placeholder="{{ trans('settings/admin_lang.general_info_fields.email_helper') }}">
                                        </div>
                                    </div>                        
                                </div>

                            

                                <div class="row form-group mb-3">
                                    <div class="col-12 col-md-6">                     
                                        <div class="form-group">
                                            <label for="province_id" class="col-12"> {{ trans('settings/admin_lang.general_info_fields.province_id') }}<span class="text-danger">*</span></label>
                                            <select  @if ($disabledForm) disabled  @endif      class="form-control select2 col-12"  style="width: 100%"  name="province_id" id="province_id">
                                                <option value="">{{ trans('settings/admin_lang.general_info_fields.province_id_helper') }}</option>   
                                                @foreach ($provincesList as $province)
                                                    <option value="{{ $province->id }}" @if($setting->province_id ==$province->id) selected @endif>{{ $province->name }}</option>
                                                @endforeach 
                                            </select>    
                                        
                                        </div>
                                    </div>    
                                    <div class="col-12 col-md-6">                     
                                        <div class="form-group">
                                            <label for="municipio_id" class="col-12"> {{ trans('settings/admin_lang.general_info_fields.municipio_id') }}<span class="text-danger">*</span></label>
                                            <select  @if ($disabledForm) disabled  @endif      class="form-control select2 col-12" style="width:100%"   name="municipio_id" id="municipio_id">
                                                <option value="">{{ trans('settings/admin_lang.general_info_fields.municipio_id_helper') }}</option>   
                                                @foreach ($municipiosList as $municipio)
                                                    <option value="{{ $municipio->id }}" @if($setting->municipio_id ==$municipio->id) selected @endif>{{ $municipio->name }}</option>
                                                @endforeach 
                                            </select>    
                                        </div>
                                    </div>                        
                                </div>
                                <div class="row form-group mb-3">
                                    <div class="col-12">                     
                                        <div class="form-group">
                                            <label for="address"> {{ trans('settings/admin_lang.general_info_fields.address') }}<span class="text-danger">*</span></label>
                                            <input value="{{!empty($setting->address) ? $setting->address :null }}" type="text"  @if ($disabledForm) disabled  @endif      class="form-control"   name="address"  placeholder="{{ trans('settings/admin_lang.general_info_fields.address_helper') }}">
                                        </div>
                                    </div>                      
                                </div>
                                {{-- <div class="row form-group mb-3">
                                    <div class="col-12 col-md-6">                     
                                        <div class="form-group">
                                            <label for="active"> {{ trans('settings/admin_lang.general_info_fields.active') }}</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-switch" @if($setting->active==1) checked @endif value="1" name="active" type="checkbox" id="active">
                                            </div>                           
                                        </div>
                                    </div>                    
                                      
                                </div> --}}
                                           
                            </div>
                            <div class="card-footer row">
                                <div class="col-12  d-flex justify-content-between">
                
                                    <a href="{{ url('admin/settings') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                                    @if ( !  $disabledForm )
                                    <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>   
                                        
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end: page -->
</section>   
@endsection
@section('foot_page')
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

<script>

    $(document).ready(function() {
        $('.select2').select2();
            $("#btnSelectImage").click(function() {
                $('#setting_image').trigger('click');
            });

            $("#setting_image").change(function(){
                getFileName();
                readURL(this);
            });

           
    });
      function getFileName() {
        $('#nombrefichero').val($('#setting_image')[0].files[0].name);
        $("#delete_photo").val('1');
        $("#contenedor-remove").css("display","");
    }
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
    function deleteElement() {
        @if(empty($setting->image))
            $('#fileOutput').html('<img src="{{ asset("/assets/front/img/!logged-user.jpg") }}" class="rounded img-fluid" alt="">');
            $("#remove").css("display","none");
            $('#nombrefichero').val("");
            $('#setting_image').val("");
            return false;
        @else    
            var strBtn = "";
            $("#confirmModalLabel").html("{{ trans('general/admin_lang.delete') }}");
            $("#confirmModalBody").html("<div class='d-flex align-items-center'><i class='fas fa-question-circle text-success' style='font-size: 64px; float: left; margin-right:15px;'></i><label style='font-size: 18px'>{{ trans('general/admin_lang.delete_question_image') }}</label></div>");
            strBtn+= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
            strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo();">{{ trans('general/admin_lang.yes_delete') }}</button>';
            $("#confirmModalFooter").html(strBtn);
            $('#modal_confirm').modal('toggle');
        @endif

    }

    function deleteinfo() {

        $.ajax({
            url     : "{{ url('admin/settings/delete-image/') }}/{{ $setting->image }}",
            type    : 'POST',
            "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            data: {_method: 'delete'},
            success : function(data) {
                $('#modal_confirm').modal('hide');
                if(data) {
                    // $("#modal_alert").addClass('modal-success');
                    // $("#alertModalHeader").html("{{ trans('general/admin_lang.warning') }}");
                    // $("#alertModalBody").html("<div class='d-flex align-items-center'><i class='fas fa-check-circle text-success' style='font-size: 64px; float: left; margin-right:15px;'></i> <label style='font-size: 18px'>" + data.msg+"</label></div>");
                    // $("#modal_alert").modal('toggle');
                    toastr.success( data.msg)
                      $('#fileOutput').html('<img src="{{ asset("/assets/front/img/!logged-user.jpg") }}" class="rounded img-fluid" alt="{{ Auth::user()->userProfile->fullName }}">');
                         $("#remove").css("display","none");
                } else {
                    $("#modal_alert").addClass('modal-danger');
                    $("#alertModalBody").html("<i class='fas fa-bug text-danger' style='font-size: 64px; float: left; margin-right:15px;'></i> {{ trans('general/admin_lang.errorajax') }}");
                    $("#modal_alert").modal('toggle');
                }
                return false;
            }

        });
        return false;
    }

</script>
     

{!! JsValidator::formRequest('App\Http\Requests\AdminSettingRequest')->selector('#formData') !!}
@stop