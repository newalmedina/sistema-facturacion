@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')

  <!-- DataTables -->
  <link href="{{ asset('/assets/admin/vendor/datatables.net/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
  type="text/css" />
<link href="{{ asset('/assets/admin/vendor/datatables.net/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
  type="text/css" />

@stop

@section('breadcrumb')
<li><span>{{ $title }}</span></li>
@stop

@section('content')
<section role="main" class="content-body card-margin">
    <div class="mt-2">
        @include('layouts.admin.includes.modals')
 
        @include('layouts.admin.includes.errors')        
    </div>
    <!-- start: page -->
   
    <div class="row">
        <div class="col">
            <form id="formData" enctype="multipart/form-data" action=" {{ route("admin.appointments_deleted.saveFilter") }}" method="post"  novalidate="false">
                @csrf
                @method("post")
            <section class="card card-featured-top card-featured-primary">
                <header class="card-header">
                    <div class="card-actions">
                        <a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
                        <a href="#" class="card-action card-action-dismiss" data-card-dismiss=""></a>
                    </div>
                    <h2 class="card-title">{!! trans('general/admin_lang.filters_exports') !!}</h2>
                </header>
            
                <div class="card-body py-4">  
                    <div class="row">
                        <div class="col-12 col-md-3">                     
                            <div class="form-group">
                                <label class="text-primary" for="start_at_ini"  class="col-12"> {{ trans('appointments_deleted/admin_lang.fields.start_at_ini') }}</label>
                                <input   type="text"  autocomplete="off" class="form-control datepicker" value="{{ $filtStartAtIni }}"  name="start_at_ini" id="start_at_ini" >
                                            
                            </div>
                        </div>       
                        <div class="col-12 col-md-3">                     
                            <div class="form-group">
                                <label class="text-primary" for="start_at_end"  class="col-12"> {{ trans('appointments_deleted/admin_lang.fields.start_at_end') }}</label>
                                <input   type="text"  autocomplete="off" class="form-control datepicker" value="{{ $filtStartAtEnd }}"  name="start_at_end" id="start_at_end" >
                                            
                            </div>
                        </div>   
                        <div class="col-12 col-md-3">                     
                            <div class="form-group">
                                <label class="text-primary" for="state" class="col-12"> {{ trans('appointments_deleted/admin_lang.fields.state') }} </label>
                                <select class="form-control select2" multiple name="state[]" id="state">
                                    <option value="">{{ trans('appointments_deleted/admin_lang.fields.state_helper') }}</option>   
                                    @foreach ($stateList as $key=>$value)
                                    
                                        <option value="{{ $key }}"  
                                            @if(in_array($key,$filtState))
                                                selected
                                            @endif
                                        >{{ $value }}</option>
                                    @endforeach 
                                </select>   
                                
                            </div>
                        </div>       
                        <div class="col-12 col-md-3">                     
                            <div class="form-group">
                                <label class="text-primary" for="service_id" class="col-12"> {{ trans('appointments_deleted/admin_lang.fields.service_id') }} </label>
                                <select class="form-control select2" multiple name="service_id[]" id="service_id">
                                    <option value="">{{ trans('appointments_deleted/admin_lang.fields.state_helper') }}</option>   
                                    @foreach ($serviceList as $service)
                                       
                                         <option value="{{  $service->id }}"
                                            @if(in_array($service->id,$filtServiceId))
                                                selected
                                            @endif
                                            >{{  $service->name }}</option>
                                    @endforeach 
                                </select>   
                                
                            </div>
                        </div>       
                    </div>                       
                    <div class="row">
                        <div class="col-12 col-md-6">                     
                            <div class="form-group">
                                <label class="text-primary" for="user_id"  class="col-12"> {{ trans('appointments_deleted/admin_lang.fields.user_id') }}</label>
                                <select class="form-control select2" multiple name="user_id[]" id="user_id">
                                    <option value="">{{ trans('appointments_deleted/admin_lang.fields.user_id_helper') }}</option>   
                                    @foreach ($patientList as $patient)
                                        <option value="{{  $patient->id }}"
                                            @if(in_array($patient->id,$filtUserId))
                                            selected
                                        @endif
                                            >{{  $patient->userProfile->fullName }}</option>
                                    @endforeach
                                </select>    
                            
                            </div>
                        </div>   
                        <div class="col-12 col-md-6">                     
                            <div class="form-group">
                                <label class="text-primary" for="doctor_id" class="col-12"> {{ trans('appointments_deleted/admin_lang.fields.doctor_id') }} </label>
                                <select class="form-control select2" multiple name="doctor_id[]" id="doctor_id">
                                    <option value="">{{ trans('appointments_deleted/admin_lang.fields.doctor_id_helper') }}</option>   
                                    @foreach ($doctorList as $doctor)
                                    <option value="{{  $doctor->id }}"
                                        @if(in_array($doctor->id,$filtDoctorId))
                                        selected
                                    @endif
                                        >{{  $doctor->userProfile->fullName }}</option>
                                @endforeach
                                </select>   
                                
                            </div>
                        </div>   
                        
                    </div>                       
                </div>
                <div class="card-footer">  
                    <div class="row ">
                        <div class="col-12 col-md-6 d-flex justify-content-start">
                            <button class="btn btn-primary btn-xs " type="submit"> {!! trans('general/admin_lang.filter') !!}</button>
                            <a href="{{ url('admin/appointments-deleted/remove-filter') }}" class="ms-2 btn btn-danger btn-xs">
                                {!! trans('general/admin_lang.clean_filter') !!}
                            </a>
                        </div>
                        @if ( 
                        Auth::user()->isAbleTo("admin-appointments-deleted-list") 
                         ) 
                        <div class="col-12 col-md-6 d-flex justify-content-end">
                            <a href="{{ url('admin/appointments/export-excel') }}" class="text-success">
                                <i class="far fa-file-excel fa-2x"></i>
                            </a>
                        </div>
                        @endif
                    </div>                       
                </div>
            </section>
            </form>
        </div>
    </div>

        <div class="row">
            <div class="col">
                <section class="card card-featured-top card-featured-primary">
                    <header class="card-header">
                        <div class="card-actions">
                            <a href="#" class="card-action card-action-toggle" data-card-toggle=""></a>
                            <a href="#" class="card-action card-action-dismiss" data-card-dismiss=""></a>
                        </div>

                        <h2 class="card-title">{{ $title }}</h2>
                    </header>
                   

                    <div class="card-body">  
                        <div class="row">
                            <div class="col-12 table-responsive">
                                @if ( 
                                    Auth::user()->isAbleTo("admin-appointments-deleted-list") 
                                    ) 
                                <table id="table_appointments" class="table table-bordered table-striped" style="width: 100%" aria-hidden="true">
                                    <thead>
                                        <tr>
                                            {{-- <th scope="col"> --}}
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            {{-- <th scope="col"> --}}
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                            <th scope="col">
                                        </tr>                               
                                    </tfoot>
                                </table>
                                @else
                                    <h2 class="text-warning">{!! trans('general/admin_lang.not_permission') !!}</h2>
                                @endif
                            </div>
                        </div>                       
                    </div>
                </section>
            </div>
        </div>
       
    <!-- end: page -->
</section>   
@endsection
@section('foot_page')
<!-- DataTables -->
<script src="{{ asset('/assets/admin/vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/admin/vendor/datatables.net/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/assets/admin/vendor/datatables.net/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/assets/admin/vendor/datatables.net/js/responsive.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('.datepicker').datepicker(
            {
                language: 'es',
                format: 'dd/mm/yyyy',
                orientation:'bottom',
                autoclose: true
            }
        );     
    });
    var oTable = '';
    @if ( 
        Auth::user()->isAbleTo("admin-appointments-deleted-list") 
       
            ) 
        $(function() {
            oTable = $('#table_appointments').DataTable({
                "stateSave": true,
                "stateDuration": 60,
                "processing": true,
                "serverSide": true,
                "pageLength": 50,
                "responsive": true,
                ajax: {
                    "headers": {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    url: "{{ url('admin/appointments-deleted/list') }}",
                    type: "POST"
                },
            /* order: [
                    [2, "asc"]
                ],*/
                columns: [
                   
                    // {
                    //     "title": "{!! trans('appointments_deleted/admin_lang.fields.image2') !!}",
                    //     orderable: false,
                    //     searchable: false,
                    //     data: 'image',
                    //     name: 'image',
                    //     sWidth: '80px'
                    // },
                    {
                        "title": "{!! trans('appointments_deleted/admin_lang.fields.start_at') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'start_at',
                        name            : 'start_at',
                        type: 'num',
                            render: {
                                _: 'display',
                                sort: 'timestamp'
                            },
                            sWidth          : '100px'
                    },
                    {
                        "title": "{!! trans('appointments_deleted/admin_lang.fields.user_id') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'patient',
                        name: 'patient.first_name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('appointments_deleted/admin_lang.fields.doctor_id') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'doctor',
                        name: 'doctor.first_name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('appointments_deleted/admin_lang.fields.service_id') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'service',
                        name: 'services.name',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('appointments_deleted/admin_lang.fields.total') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'total',
                        name: 'appointments.total',
                        sWidth: '100px',
                    },
                   
                    {
                        "title": "{!! trans('appointments_deleted/admin_lang.fields.state') !!}",
                        orderable: false,
                        searchable: false,
                        sWidth: '100px',
                        data: 'state'
                    },
                    {
                        "title": "{!! trans('appointments_deleted/admin_lang.fields.deleted_at') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'deleted_at',
                        name            : 'deleted_at',
                        type: 'num',
                            render: {
                                _: 'display',
                                sort: 'timestamp'
                            },
                            sWidth          : '100px'
                    },
                    {
                        "title": "{!! trans('general/admin_lang.actions') !!}",
                        orderable: false,
                        searchable: false,
                        // sWidth: '100px',
                        data: 'actions'
                    }

                ],
                "fnDrawCallback": function(oSettings) {
                    $('[data-bs-toggle="popover"]').mouseover(function() {
                        $(this).popover("show");
                    });

                    $('[data-bs-toggle="popover"]').mouseout(function() {
                        $(this).popover("hide");
                    });
                },
                oLanguage: {!! json_encode(trans('datatable/lang')) !!}

            });

            var state = oTable.state.loaded();
            $('tfoot th', $('#table_appointments')).each(function(colIdx) {
                var title = $('tfoot th', $('#table_appointments')).eq($(this).index()).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if (state) defecto = state.columns[colIdx].search.search;

                    $(this).html(
                        '<input style="width: 99.9%" type="text" class="form-control input-small input-inline" placeholder="' +
                        oTable.context[0].aoColumns[colIdx].title + ' ' + title + '" value="' +
                        defecto + '" />');
                }
            });

            $('#table_appointments').on('keyup change', 'tfoot input', function(e) {
                oTable
                    .column($(this).parent().index() + ':visible')
                    .search(this.value)
                    .draw();
            });

        });

   
    @endif 
    // function deleteElement(url) {
    //     var strBtn = "";

    //     $("#confirmModalLabel").html("{{ trans('general/admin_lang.delete') }}");
    //     $("#confirmModalBody").html("<div class='d-flex align-items-center'><i class='fas fa-question-circle text-success' style='font-size: 64px; float: left; margin-right:15px;'></i><label class="text-primary" style='font-size: 18px'>{{ trans('general/admin_lang.delete_question') }}</label></div>");
    //     strBtn+= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
    //     strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:deleteinfo(\''+url+'\');">{{ trans('general/admin_lang.yes_delete') }}</button>';
    //     $("#confirmModalFooter").html(strBtn);
    //     $('#modal_confirm').modal('toggle');
    // }


    function deleteElement(url) {
        Swal.fire({
        title: "{{ trans('general/admin_lang.delete') }}",
        text: 'Esta acción no se puede deshacer',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "{{ trans('general/admin_lang.yes_delete') }}",
        cancelButtonText: "{{ trans('general/admin_lang.close') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                deleteinfo(url);
            }
        });
    }

    function deleteinfo(url) {
        $.ajax({
            url     : url,
            type    : 'POST',
            "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
            data: {_method: 'delete'},
            success : function(data) {
                $('#modal_confirm').modal('hide');
                if(data) {
                   
                    toastr.success( data.msg)
                    oTable.ajax.reload(null, false);
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

    function restaurarElement(url) {
            Swal.fire({
                title: "{{ trans('appointments_deleted/admin_lang.restaurar') }}",
            text: "{{ trans('appointments_deleted/admin_lang.restaurar_question') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('general/admin_lang.yes') }}",
            cancelButtonText: "{{ trans('general/admin_lang.no') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    facturarInfo(url);
                    
                }
            });
        }

        function facturarInfo(url) {
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'PATCH'},
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                    
                        toastr.success( data.msg);
                        oTable.ajax.reload(null, false);
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

        function finalizarElement(url) {
            Swal.fire({
                title: "{{ trans('appointments_deleted/admin_lang.finalizar') }}",
            text: "{{ trans('appointments_deleted/admin_lang.finalizar_question') }}",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('general/admin_lang.yes') }}",
            cancelButtonText: "{{ trans('general/admin_lang.no') }}"
            }).then((result) => {
                if (result.isConfirmed) {               
                    finalizarInfo(url);
                }
            });
        }

        function finalizarInfo(url) {
            $.ajax({
                url     : url,
                type    : 'POST',
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                data: {_method: 'PATCH'},
                success : function(data) {
                    $('#modal_confirm').modal('hide');
                    if(data) {
                        toastr.success( data.msg);
                        oTable.ajax.reload(null, false);
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

@stop