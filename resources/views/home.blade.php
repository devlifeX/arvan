@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2>All Doamins</h2>
            <show-domains></show-domains>
        </div>
         <div class="col-md-4">
             <a href="{{url('add')}}">Add new Domain</a>
        </div>
    </div>
</div>
@endsection
