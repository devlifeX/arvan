@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-2 offset-md-10">
            <a href="{{url('add')}}">Add new Domain</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2>All Domains</h2>
            <show-domains></show-domains>
        </div>
    </div>
</div>
@endsection
