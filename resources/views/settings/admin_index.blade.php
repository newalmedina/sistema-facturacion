@extends('settings.admin_settings_layout')


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
@php
$disabled= isset($disabled)?$disabled : null;
@endphp
<div class="row">
   
    <div class="col-12">
        <form id="formData" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}" method="post"  novalidate="false">
            @csrf 
             @method('patch')
          
              
            <div class="card-body">
                <div class="row form-group mb-3">
                    <div class="col-12">
                     
                        <div class="form-group">
                            <label class="text-primary" for="name"> {{ trans('settings/admin_lang.general_info_fields.site_name') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($setting->site_name) ? $setting->site_name :null }}" type="text"  @if ($disabledForm) disabled  @endif      class="form-control"    name="site_name"  placeholder="{{ trans('settings/admin_lang.general_info_fields.site_name_helper') }}">
                        </div>
                    </div>      
                </div>
                 <div class="row form-group mb-3"">                         
                    <div class="col-lg-12>
                        <div class="form-group">
                            <label class="text-primary" for="image"> {{ trans('settings/admin_lang.general_info_fields.image') }}</label>
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
                            <label class="text-primary" for="phone"> {{ trans('settings/admin_lang.general_info_fields.phone') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($setting->phone) ? $setting->phone :null }}" type="text"  @if ($disabledForm) disabled  @endif      class="form-control"   name="phone"  placeholder="{{ trans('settings/admin_lang.general_info_fields.phone_helper') }}">
                        </div>
                    </div>    
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label class="text-primary" for="email"> {{ trans('settings/admin_lang.general_info_fields.email') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($setting->email) ? $setting->email :null }}" type="text"  @if ($disabledForm) disabled  @endif      class="form-control"   name="email"  placeholder="{{ trans('settings/admin_lang.general_info_fields.email_helper') }}">
                        </div>
                    </div>                        
                </div>

            

                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label class="text-primary" for="province_id" class="col-12"> {{ trans('settings/admin_lang.general_info_fields.province_id') }}<span class="text-danger">*</span></label>
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
                            <label class="text-primary" for="municipio_id" class="col-12"> {{ trans('settings/admin_lang.general_info_fields.municipio_id') }}<span class="text-danger">*</span></label>
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
                            <label class="text-primary" for="address"> {{ trans('settings/admin_lang.general_info_fields.address') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($setting->address) ? $setting->address :null }}" type="text"  @if ($disabledForm) disabled  @endif      class="form-control"   name="address"  placeholder="{{ trans('settings/admin_lang.general_info_fields.address_helper') }}">
                        </div>
                    </div>                      
                </div>
                {{-- <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">                     
                        <div class="form-group">
                            <label class="text-primary" for="active"> {{ trans('settings/admin_lang.general_info_fields.active') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-switch" @if($setting->active==1) checked @endif value="1" name="active" type="checkbox" id="active">
                            </div>                           
                        </div>
                    </div>                    
                      
                </div> --}}
                           
            </div>
            <div class=" row">
                <div class="col-12  d-flex justify-content-between">

                    <a href="{{ url('admin/settings') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                    @if ( !  $disabledForm )
                    <button type="submit" class="btn btn-primary">{{ trans('general/admin_lang.save') }}</button>   
                        
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@section("tab_foot")
@stop