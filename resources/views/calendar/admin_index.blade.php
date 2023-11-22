@extends('layouts.admin.default')
@section('title')
    @parent {{ $pageTitle }}
@stop
@section('head_page')


@stop

@section('breadcrumb')
<li><span>{{ $title }}</span></li>
@stop

@section('content')
<section role="main" class="content-body card-margin">  

    <livewire:calendar.index />

</section>   
@endsection
@section('foot_page')
<script>
      $(document).ready(function() {
        $('.select2').select2();
      
    });
</script>
@stop