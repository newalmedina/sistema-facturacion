@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')
<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />
    
@stop

@section('breadcrumb')
<li><a href="{{ url('admin/medical-specializations') }}">{{ $title }}</a></li>
@stop

@section('content')
    
<section role="main" class="content-body card-margin">      
    <div class="mt-2">
         @include('layouts.admin.includes.modals')
      
        @include('layouts.admin.includes.errors')   
    </div>
    <div class="row">
        <div class="col">
            <section class="card card-featured-top card-featured-primary">
                <header class="card-header">
                    <div class="card-actions">
                        <a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
                        <a href="#" class="card-action card-action-dismiss" data-card-dismiss=""></a>
                    </div>

                    <h2 class="card-title">{{ trans("general/admin_lang.general_info") }}</h2>
                </header>
                

                <div class="card-body">  
                    <form id="formData" enctype="multipart/form-data" action="@if(empty($medicalSpecialization->id)) {{ route("admin.medical-specializations.store") }} @else {{ route("admin.medical-specializations.update",$medicalSpecialization->id) }} @endif" method="post"  novalidate="false">
                        @csrf       
                       
                        @if(empty($medicalSpecialization->id))  
                            @method('post')
                        @else   
                            @method('patch') 
                        @endif
                          
                        <div class="card-body">
                            <div class="row form-group mb-3">
                                <div class="col-12">
                                 
                                    <div class="form-group">
                                        <label for="name"> {{ trans('medical-specializations/admin_lang.fields.name') }}<span class="text-danger">*</span></label>
                                        <input value="{{!empty($medicalSpecialization->name) ? $medicalSpecialization->name :null }}" type="text" class="form-control" name="name"  placeholder="{{ trans('medical-specializations/admin_lang.fields.name_helper') }}">
                                    </div>
                                </div>      
                            </div>    
                            <div class="row form-group mb-3">
                                <div class="col-12">                     
                                    <div class="form-group">
                                        <label for="description"> {{ trans('medical-specializations/admin_lang.fields.description') }}</label>
                                        <textarea name="description" class="form-control" id="" cols="30" rows="10">{{ $medicalSpecialization->description }}</textarea>
                                        
                                    </div>
                                </div>                                       
                            </div>    
                            <div class="row form-group mb-3">
                                <div class="col-12 col-md-6">                     
                                    <div class="form-group">
                                        <label for="active"> {{ trans('medical-specializations/admin_lang.fields.active') }}</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-switch" @if($medicalSpecialization->active==1) checked @endif value="1" name="active" type="checkbox" id="active">
                                        </div>                           
                                    </div>
                                </div>                    
                                                 
                            </div>            
                        </div>
                        <div class="card-footer row">
                            <div class="col-12  d-flex justify-content-between">
            
                                <a href="{{ url('admin/medical-specializations') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                                <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>   
                            </div>
                        </div>
                    </form>                     
                </div>
            </section>
        </div>
    </div>
</section>
@endsection
@section('foot_page')

<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script>
    $(document).ready(function() {
      
    });

</script>

{!! JsValidator::formRequest('App\Http\Requests\AdminMedicalSpecializationRequest')->selector('#formData') !!}
@stop

