@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10">
            <h2>All Domains</h2>
            <show-domains></show-domains>
        </div>
         <div class="col-md-2">
             <a href="{{url('add')}}">Add new Domain</a>
        </div>
    </div>
</div>
@endsection
