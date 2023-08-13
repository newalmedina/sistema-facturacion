@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')
<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />
    
@stop

@section('breadcrumb')
<li><a href="{{ url('admin/clinic-personal') }}">{{ $title }}</a></li>
@yield('tab_breadcrumb')
@stop

@section('content')
    
<section role="main" class="content-body card-margin">      
    <div class="mt-2">
         @include('layouts.admin.includes.modals')
      
        @include('layouts.admin.includes.errors')   
    </div>
   @if (!empty( $clinicPersonal->id))
        <div class="row">
            <div class="col-12 text-end">
                <h3> <span class="badge badge-primary p-2"><b>{{ $clinicPersonal->UserProfile->fullName }}</b></span></h3>
            </div>
        </div>
    @else
        <div class="col-12 mb-5"></div>
    @endif

    <div class="row mt-2">
        @if (!empty( $clinicPersonal->id))
            <div class="col-12 col-md-3">
                <section class="card">
                
                    <div class="card-body">
                        
                        <div class="thumb-info mb-3">
                            <div id="fileOutput">
                                @if($clinicPersonal->userProfile->photo!='')
                                    <img src='{{ url('admin/clinic-personal/get-image/'.$clinicPersonal->userProfile->photo) }}' id='image_ouptup' class="rounded img-fluid" alt="{{$clinicPersonal->userProfile->photo}}">
                                @else                                
                                    <img src="{{ asset("/assets/front/img/!logged-user.jpg") }}" class="rounded img-fluid" alt="image">
                                @endif
                            </div>   
                            <div class="thumb-info-title">
                                <span class="thumb-info-inner">{{ Auth::user()->userProfile->fullName }}</span>
                                <span class="thumb-info-type"> {{ implode(",",Auth::user()->roles->pluck('display_name')->toArray()) }}</span>
                            </div>                         
                        </div>
                      
                    </div>
                </section>
            </div>
        @endif
        <div class="col-12   @if (!empty( $clinicPersonal->id))col-md-9 @endif">
            <div class="tabs tabs-primary">
                <ul class="nav nav-tabs" id="custom-tabs">
            
                    <li class="nav-item @if ($tab == 'tab_1') active @endif">
                        <a id="tab_1" class="nav-link" data-bs-target="#tab_1-1" data-bs-toggle="tabajax"
                            href="{{ !empty($clinicPersonal->id) ? url('admin/clinic-personal/' . $clinicPersonal->id . '/edit') : '#' }}"
                            data-target="#tab_1-1" aria-controls="tab_1-1" aria-selected="true">
                            {{ trans('clinic-personal/admin_lang.general_info') }}
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
<script>
    $(document).ready(function() {
      
    });


</script>

@yield('tab_foot')
@stop

