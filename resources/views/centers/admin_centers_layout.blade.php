@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')
<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />
    
@stop

@section('breadcrumb')
<li><a href="{{ url('admin/centers') }}">{{ $title }}</a></li>
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
   @if (!empty( $center->id))
        <div class="row">
            <div class="col-12 text-end">
                <h3> <span class="badge badge-primary p-2"><b>{{ $center->name }}</b></span></h3>
            </div>
        </div>
    @else
        <div class="col-12 mb-5"></div>
    @endif

    <div class="row mt-2">
        {{-- @if (!empty( $center->id))
            <div class="col-12 col-md-3">
                <section class="card">
                
                    <div class="card-body">
                        
                        <div class="thumb-info mb-3">
                            <div id="fileOutput">
                                @if($center->image!='')
                                    <img src='{{ url('admin/centers/get-image/'.$center->image) }}' id='image_ouptup' class="rounded img-fluid" alt="{{$center->image}}">
                                @else                                
                                    <img src="{{ asset("/assets/front/img/!logged-user.jpg") }}" class="rounded img-fluid" alt="image">
                                @endif
                            </div>                            
                        </div>
                        <div id="remove" onclick="deleteElement()" class="text-danger" style="@if($center->image=='') display: none; @endif cursor: pointer; text-align: center;"><i class="fa fa-times" aria-hidden="true"></i> {{ trans('centers/admin_lang.quit_image') }} </div>
    
                        <hr class="dotted short">
                        <p class="text-2">
                            {{ trans('centers/admin_lang.created_at') }} <strong>{{ Auth::user()->createdAtFormatted }}</strong>
                        </p>
                    </div>
                </section>
            </div>
        @endif --}}
        <div class="col-12   ">
            <div class="tabs tabs-primary">
                <ul class="nav nav-tabs" id="custom-tabs">
            
                    <li class="nav-item @if ($tab == 'tab_1') active @endif">
                        @if (empty($disabled))
                            <a id="tab_1" class="nav-link" data-bs-target="#tab_1-1" data-bs-toggle="tabajax"
                            href="{{ !empty($center->id) ? url('admin/centers/' . $center->id . '/edit') : '#' }}"
                            data-target="#tab_1-1" aria-controls="tab_1-1" aria-selected="true">
                            {{ trans('centers/admin_lang.general_info') }}
                             </a>                         
                        @else
                        <a id="tab_1" class="nav-link" data-bs-target="#tab_1-1" data-bs-toggle="tabajax"
                            href="{{ !empty($center->id) ? url('admin/centers/' . $center->id . '/show') : '#' }}"
                            data-target="#tab_1-1" aria-controls="tab_1-1" aria-selected="true">
                            {{ trans('centers/admin_lang.general_info') }}
                        </a>
                    @endif
                        
                    </li>
            
                
                    @if (!empty($center->id) &&  Auth::user()->isAbleTo("admin-centers-update") )
                        <li class="nav-item @if ($tab == 'tab_2') active @endif">
                            @if (empty($disabled))
                                <a id="tab_2" class="nav-link" data-bs-target="#tab_2-2"
                                data-bs-toggle="tabajax" href="{{ url('admin/centers/aditional-info/'.$center->id) }}" data-target="#tab_2-2"
                                aria-controls="tab_2-2" aria-selected="true" >
                                    {{ trans('centers/admin_lang.additional_info') }}
                                </a>                     
                            @else
                                <a id="tab_2" class="nav-link" data-bs-target="#tab_2-2"
                                data-bs-toggle="tabajax" href="{{ url('admin/centers/aditional-info/'.$center->id.'/show') }}" data-target="#tab_2-2"
                                aria-controls="tab_2-2" aria-selected="true" >
                                    {{ trans('centers/admin_lang.additional_info') }}
                                </a>
                            @endif
                           
                        </li>
                    
                    @endif
                </ul>
                <div class="tab-content" id="tab_tabContent">
                    <div id="tab_1-1" class="tab-pane @if ($tab == 'tab_1') active @endif">
            
                        @yield('tab_content_1')
                    </div>
            
                    @if (!empty($center->id) &&  Auth::user()->isAbleTo("admin-centers-update") )
                        <div id="tab_2-2" class="tab-pane  @if ($tab == 'tab_2') active @endif">
                            @yield('tab_content_2')
                        </div>
                    @endif
            
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
    function deleteElement() {
        // @if(empty($center->image))
        // $('#fileOutput').html('<img src="{{ asset("/assets/front/img/!logged-user.jpg") }}" class="rounded img-fluid" alt="{{ Auth::user()->userProfile->fullName }}">');
        // $("#remove").css("display","none");
        // $('#nombrefichero').val("");
        // $('#center_image').val("");
        // return false;
        // @endif
        var strBtn = "";
        $("#confirmModalLabel").html("{{ trans('general/admin_lang.delete') }}");
        $("#confirmModalBody").html("<div class='d-flex align-items-center'><i class='fas fa-question-circle text-success' style='font-size: 64px; float: left; margin-right:15px;'></i><label style='font-size: 18px'>{{ trans('general/admin_lang.delete_question_image') }}</label></div>");
        strBtn+= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
        strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo();">{{ trans('general/admin_lang.yes_delete') }}</button>';
        $("#confirmModalFooter").html(strBtn);
        $('#modal_confirm').modal('toggle');
    }

    function deleteinfo() {

        $.ajax({
            url     : "{{ url('admin/centers/delete-image/'.$center->id) }}",
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

@yield('tab_foot')
@stop

