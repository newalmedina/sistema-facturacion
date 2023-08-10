@extends('users.admin_users_layout')


@section('tab_head')
<link href="{{ asset('/assets/admin/vendor/jquery-bonsai/css/jquery.bonsai.css')}}" rel="stylesheet" />
<style>

</style>
@stop

@section('tab_breadcrumb')
<li class="breadcrumb-item active"><a href="#">{{ $pageTitle }}</a></li>
@stop

@section('tab_content_3')

<div class="row">
    <div class="col-12">
        <form id="frm_Permission_Role" action="{{ route("admin.users.updateCenters",$user->id) }}" method="post" novalidate="false">
            @csrf
            @method('patch')
            <input type="hidden" name="results" id="results">
            <div class="row form-group mb-3">
                <div class="col-12 ">                     
                    <div class="form-group">
                        <label for="center_id" class="col-12"> {{ trans('users/admin_lang.centers_asigned') }}</label>
                        <select class="col-12 form-control select2" style="width:100%" multiple name="center_id[]" id="center_id">
                            <option value="">{{ trans('users/admin_lang.centers_helper') }}</option>   
                            @foreach ($centers as $center)
                                <option value="{{ $center->id }}" @if(in_array($center->id,$selected_center)) selected @endif >{{ $center->name }} 
                                    @if(!empty($center->province->id) && !empty($center->municipio->id) )
                                    <small>({{ $center->province->name.', '.$center->municipio->name }})</small>
                                    @endif
                                </option>
                            @endforeach 
                        </select>    
                    
                    </div>
                </div>                    
            </div>

            <div class="card-footer text-end mt-2">
                <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section("tab_foot")
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

@stop