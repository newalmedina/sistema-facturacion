@extends('patients.admin_patient_layout')

@section('tab_head')
  <!-- DataTables -->
  <link href="{{ asset('/assets/admin/vendor/datatables.net/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
  type="text/css" />
    <link href="{{ asset('/assets/admin/vendor/datatables.net/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"
  type="text/css" />
 

@stop

@section('tab_breadcrumb')
<li class="breadcrumb-item active">
    <span>
        {{ $pageTitle }} 
      </span>
</li>
@stop

@section('tab_content_4')
<div class="card-body mb-4">  
    <div class="row">
        <div class="col">
            <form id="formData" enctype="multipart/form-data" action=" {{ route("admin.patients.medicines.saveFilter",[$patient->id]) }}" method="post"  novalidate="false">
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
                  <div class="row form-group mb-3">
                    <div class="col-md-4">                     
                        <div class="form-group">
                            <label for="start_date"> {{ trans('patient-medicines/admin_lang.fields.start_date') }}</label>
                            <input value="{{ $filtStartData }}" name="start_date" type="text"  class="form-control datepicker"   placeholder="{{ trans('patient-medicines/admin_lang.fields.start_date_helper') }}">
                        </div>
                    </div>
                    <div class="col-md-4">                     
                        <div class="form-group">
                            <label for="end_date"> {{ trans('patient-medicines/admin_lang.fields.end_date') }}</label>
                            <input value="{{ $filtEndData }}" name="end_date" type="text"  class="form-control datepicker"   placeholder="{{ trans('patient-medicines/admin_lang.fields.end_date_helper') }}">
                        </div>
                    </div>
                  </div>
                    <div class="row form-group mb-3">
                        <div class="col-12 col-md-8">                     
                            <div class="form-group">
                                <label for="center_id"  class="col-12"> {{ trans('patient-medicines/admin_lang.fields.centers') }}</label>
                                <select class="form-control select2" multiple name="center_id[]" id="center_id">
                                    <option value="">{{ trans('patient-medicines/admin_lang.fields.centers_helper') }}</option>   
                                    @foreach ($centerList as $center)
                                        <option value="{{ $center->id }}" @if( $center->id==in_array($center->id,$filtCenterId))  selected @endif >{{ $center->name }}</option>
                                    @endforeach 
                                </select>                            
                            </div>
                        </div>             
                    </div>       
                </div>
                <div class="card-footer">  
                    <div class="row ">
                        <div class="col-12 col-md-6 d-flex justify-content-start">
                            <button class="btn btn-success btn-xs " type="submit"> {!! trans('general/admin_lang.filter') !!}</button>
                            <a href="{{ url('admin/patients/'.$patient->id.'/medicines/remove-filter') }}" class="ms-2 btn btn-danger btn-xs">
                                {!! trans('general/admin_lang.clean_filter') !!}
                            </a>
                        </div>
                        @if ( Auth::user()->isAbleTo("admin-patients-medicines-list") ) 
                        <div class="col-12 col-md-6 d-flex justify-content-end">
                            <a href="{{ url('admin/patients/'.$patient->id.'/medicines/export-excel') }}" class="text-success">
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
</div>
<div class="card-body">  
    <div class="text-end">
        @if(Auth::user()->isAbleTo("admin-patients-medicines-create"))
            <a href="{{ route('admin.patients.medicines.create',["patient_id"=>$patient->id]) }}" class="btn btn-outline-success">
            {{ trans('patient-medicines/admin_lang.new') }}
            </a>
        @endif
    </div>
</div>

<div class="card-body">
    <div class="row">
        <div class="col-12 table-responsive">
            @if ( Auth::user()->isAbleTo("admin-patients-medicines-list") ) 
            <table id="table_patient-medicines" class="table table-bordered table-striped" style="width: 100%" aria-hidden="true">
                <thead>
                    <tr>
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
<div class="card-footer row">
    <div class="col-12  d-flex justify-content-between">
        <a href="{{ route('admin.patients') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
        
    </div>
</div>
@endsection
@section('tab_foot')
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
    @if ( Auth::user()->isAbleTo("admin-patients-medicines-list") )    
        $(function() {
            oTable = $('#table_patient-medicines').DataTable({
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
                    url: "{{ url('admin/patients/'.$patient->id.'/medicines/list') }}",
                    type: "POST"
                },
            /* order: [
                    [2, "asc"]
                ],*/
                columns: [
                  
                  
                    {
                        "title": "{!! trans('patient-medicines/admin_lang.fields.date') !!}",
                        orderable       : true,
                        searchable      : true,
                        data            : 'date',
                        name            : 'date',
                        type: 'num',
                            render: {
                                _: 'display',
                                sort: 'timestamp'
                            },
                            sWidth          : '100px'
                    },
                    {
                        "title": "{!! trans('patient-medicines/admin_lang.fields.created_by') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'created_by',
                        name: 'created_by',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('patient-medicines/admin_lang.fields.medicine') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'medicine',
                        name: 'medicine',
                        sWidth: ''
                    },
                    {
                        "title": "{!! trans('patient-medicines/admin_lang.fields.center') !!}",
                        orderable: true,
                        searchable: true,
                        data: 'center',
                        name: 'centers.name',
                        sWidth: ''
                    },
                    
                    {
                        "title": "{!! trans('general/admin_lang.actions') !!}",
                        orderable: false,
                        searchable: false,
                        sWidth: '180px',
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
            $('tfoot th', $('#table_patient-medicines')).each(function(colIdx) {
                var title = $('tfoot th', $('#table_patient-medicines')).eq($(this).index()).text();
                if (oTable.settings()[0]['aoColumns'][$(this).index()]['bSearchable']) {
                    var defecto = "";
                    if (state) defecto = state.columns[colIdx].search.search;

                    $(this).html(
                        '<input style="width: 99.9%" type="text" class="form-control input-small input-inline" placeholder="' +
                        oTable.context[0].aoColumns[colIdx].title + ' ' + title + '" value="' +
                        defecto + '" />');
                }
            });

            $('#table_patient-medicines').on('keyup change', 'tfoot input', function(e) {
                oTable
                    .column($(this).parent().index() + ':visible')
                    .search(this.value)
                    .draw();
            });

        });    
        function changeState(id){
            $.ajax({
                url     : "{{ url('admin/patient-medicines/change-state/') }}/"+id,
                type    : 'GET',
                success : function(data) {
                    console.log("estado actalizado");           
                },
                error : function(data) {
                    console.log("Error al actualizar "+error);           
                }
            });
        }
    @endif 
   
   
    function copyElement(url) {
        var strBtn = "";

        $("#confirmModalLabel").html("{{ trans('general/admin_lang.copy') }}");
        $("#confirmModalBody").html("<div class='d-flex align-items-center'><i class='fas fa-question-circle text-success' style='font-size: 64px; float: left; margin-right:15px;'></i><label style='font-size: 18px'>{{ trans('general/admin_lang.copy_question') }}</label></div>");
        strBtn+= '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>';
        strBtn+= '<button type="button" class="btn btn-primary" onclick="javascript:copyInfo(\''+url+'\');">{{ trans('general/admin_lang.yes_copy') }}</button>';
        $("#confirmModalFooter").html(strBtn);
        $('#modal_confirm').modal('toggle');
    }

    function copyInfo(url) {
        window.location.href = url;
    }

    // function deleteElement(url) {
    //     var strBtn = "";

    //     $("#confirmModalLabel").html("{{ trans('general/admin_lang.delete') }}");
    //     $("#confirmModalBody").html("<div class='d-flex align-items-center'><i class='fas fa-question-circle text-success' style='font-size: 64px; float: left; margin-right:15px;'></i><label style='font-size: 18px'>{{ trans('general/admin_lang.delete_question') }}</label></div>");
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
                    // $("#modal_alert").addClass('modal-success');
                    // $("#alertModalHeader").html("{{ trans('general/admin_lang.warning') }}");
                    // $("#alertModalBody").html("<div class='d-flex align-items-center'><i class='fas fa-check-circle text-success' style='font-size: 64px; float: left; margin-right:15px;'></i> <label style='font-size: 18px'>" + data.msg+"</label></div>");
                    // $("#modal_alert").modal('toggle');
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
</script>

@stop