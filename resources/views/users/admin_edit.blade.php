@extends('users.admin_users_layout')


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
        <form id="formData" action="@if(empty($user->id)) {{ route("admin.users.store") }} @else {{ route("admin.users.update",$user->id) }} @endif" method="post"  novalidate="false">
            @csrf       
           
            @if(empty($user->id))  
                @method('post')
            @else   
                @method('patch') 
            @endif
              
            <div class="card-body">
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">
                     
                        <div class="form-group">
                            <label for="first_name"> {{ trans('users/admin_lang.fields.first_name') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($user->userProfile->first_name) ? $user->userProfile->first_name :null }}" type="text"  {{ $disabled }} class="form-control" name="user_profile[first_name]"  placeholder="{{ trans('users/admin_lang.fields.first_name_helper') }}">
                        </div>
                    </div>    
                    
                    <div class="col-12 col-md-6">
                     
                        <div class="form-group">
                            <label for="last_name"> {{ trans('users/admin_lang.fields.last_name') }}<span class="text-danger">*</span></label>
                            <input value="{{!empty($user->userProfile->last_name) ? $user->userProfile->last_name :null }}" type="text"  {{ $disabled }} class="form-control" name="user_profile[last_name]"  placeholder="{{ trans('users/admin_lang.fields.last_name_helper') }}">
                        </div>
                    </div>     
                </div>

                <div class="row form-group mb-3">
                    <div class="col-12">
                     
                        <div class="form-group">
                            <label for="email"> {{ trans('users/admin_lang.fields.email') }}<span class="text-danger">*</span></label>
                            <input value="{{ $user->email }}" type="text"  {{ $disabled }} class="form-control" name="email"  placeholder="{{ trans('users/admin_lang.fields.email_helper') }}">
                        </div>
                    </div>                    
                </div>
                <div class="row form-group mb-3">
                    <div class="col-12 col-md-6">
                     
                        <div class="form-group">
                            <label for="password"> {{ trans('users/admin_lang.fields.password') }}<span class="text-danger">*</span></label>
                            <input value="" type="text"  {{ $disabled }} class="form-control" id="password" name="password"  placeholder="{{ trans('users/admin_lang.fields.password_helper') }}">
                        </div>
                    </div>  
                    <div class="col-12 col-md-6">
                     
                        <div class="form-group">
                            <label for="password_confirm"> {{ trans('users/admin_lang.fields.password_confirm') }}<span class="text-danger">*</span></label>
                            <input value="" type="text"  {{ $disabled }} class="form-control" id="password_confirm" name="password_confirm"  placeholder="{{ trans('users/admin_lang.fields.password_confirm_helper') }}">
                        </div>
                    </div>                    
                </div>
                @if (empty($disabled))
                    <div class="row form-group mb-3">
                        <div class="col-12 col-md-6">
                            <button onclick="generatePassword(8)" type="button" class="btn btn-info">{{ trans('users/admin_lang.generate_password') }}</button>                        
                        </div>                    
                    </div>
                                      
                @endif           
               

                <div class="row form-group mb-3">
                    <div class="col-12">
                     
                        <div class="form-group">
                            <label for="active"> {{ trans('users/admin_lang.fields.active') }}</label>
                            <div class="form-check form-switch">
                                <input  {{ $disabled }} class="form-check-input toggle-switch" @if($user->active==1) checked @endif value="1" name="active" type="checkbox" id="active">
                            </div>                           
                        </div>
                    </div>                    
                </div>
                

                
            </div>
            <div class="card-footer row">
                <div class="col-12  d-flex justify-content-between">

                    <a href="{{ url('admin/users') }}" class="btn btn-default">{{ trans('general/admin_lang.back') }}</a>
                    @if (empty($disabled))
                     <button type="submit" class="btn btn-success">{{ trans('general/admin_lang.save') }}</button>  
                     @endif    
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section("tab_foot")

<script src="{{ asset('assets/admin/vendor/ios7-switch/ios7-switch.js')}}"></script>
    
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
<script>
    
    function generatePassword(lenght) {
        var pass = '';
        var str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' + 
                'abcdefghijklmnopqrstuvwxyz0123456789@#$';
            
        for (let i = 1; i <= lenght; i++) {
            var char = Math.floor(Math.random()
                        * str.length + 1);                
            pass += str.charAt(char)
        }
        $("#password").val(pass);
        $("#password_confirm").val(pass);
      //  return pass;
    }
</script>
{!! JsValidator::formRequest('App\Http\Requests\AdminUserRequest')->selector('#formData') !!}
@stop