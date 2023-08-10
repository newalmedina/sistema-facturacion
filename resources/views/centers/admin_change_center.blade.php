  <!-- Modal -->
  @php
      $centers = \App\Models\Center::select("centers.id","centers.name")->active()->join("user_centers","user_centers.center_id","=","centers.id")->where("user_centers.user_id",auth()->user()->id)->get();
  @endphp
  <div class="modal fade " id="modalChangeCenter" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="true"  aria-labelledby="staticBackdropLabel">
    <div class="modal-dialog ">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="staticBackdropLabel">{{ trans('centers/admin_lang.change_centers') }}</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formChangeCenter" enctype="multipart/form-data" action=" {{ route("admin.centers.changeCenterUpdate") }}" method="post"  novalidate="false">
          @csrf
          @method('post')
          <div class="modal-body" id="modalChangeCenterBody">     
            <div class="row form-group mb-3">
              
              <div class="col-12 ">                     
                  <div class="form-group">
                      <label for="center_id"> {{ trans('centers/admin_lang.centers') }}</label>
                      <select class="form-control select2Center"  name="center_id" id="center_id">
                          <option value="">{{ trans('centers/admin_lang.fields.center_id_helper') }}</option>   
                          @foreach ($centers as $center)
                              <option value="{{ $center->id }}"
                                @if (auth()->user()->userProfile->selected_center ==$center->id)
                                    selected
                                @endif
                               >
                                {{ $center->name}} 
                              </option>
                          @endforeach 
                      </select>    
                  
                  </div>
              </div>                    
          </div>
          </div>

          <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ trans('general/admin_lang.close') }}</button>
            <button class="btn btn-success" type="submit"  >{{ trans('general/admin_lang.save') }}</button>        
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
  <script>
     $(document).ready(function() {
        // $('.select2Center').select2();
       
        
       
      });
  </script>
  {!! JsValidator::formRequest('App\Http\Requests\AdminChangeCenterRequest')->selector('#formChangeCenter') !!}
  