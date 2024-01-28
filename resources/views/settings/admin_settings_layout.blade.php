@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')
<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />
    
@stop

@section('breadcrumb')
<li><a href="{{ url('admin/users') }}">{{ $title }}</a></li>
@yield('tab_breadcrumb')
@stop

@section('content')
@php
$disabled= isset($disabled)?$disabled : null;
@endphp
<section role="main" class="content-body card-margin">      
    <div class="mt-2">
       
        @include('layouts.admin.includes.modals')
        @include('layouts.admin.includes.errors')     
    </div>


    <div class="row mt-2">
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
                            @if(!empty($setting->image))                           
                                <div id="remove"  onclick="deleteElement()" class="text-danger mt-2" style="@if($setting->image=='') display: none; @endif cursor: pointer; text-align: center;"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('settings/admin_lang.quit_image') }} </div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        <div class="col-12    col-md-9 ">
            <div class="tabs tabs-primary">
                <ul class="nav nav-tabs" id="custom-tabs">
            
                    <li class="nav-item @if ($tab == 'tab_1') active @endif">
                        <a id="tab_1" class="nav-link" data-bs-target="#tab_1-1" data-bs-toggle="tabajax"
                            href="#"
                            data-target="#tab_1-1" aria-controls="tab_1-1" aria-selected="true">
                            {{ trans('settings/admin_lang.general_info') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="tab_tabContent">
                    <div id="tab_1-1" class="tab-pane @if ($tab == 'tab_1') active @endif">            
                        @yield('tab_content_1')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('foot_page')

@yield('tab_foot')
@stop

