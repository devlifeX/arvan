@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (Auth::check())
                <domain></domain>
            @endif
        </div>
    </div>
</div>
@endsection
