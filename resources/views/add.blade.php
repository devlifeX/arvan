@extends('layouts.app')

@section('content')
<div class="container">
    <div>
    <a href="{{url('home')}}">Back to List</a>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (Auth::check())
                <add-domain></add-domain>
            @endif
        </div>
    </div>
</div>
@endsection
