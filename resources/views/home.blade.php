@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="justify-content-center">
            <flash-message transitionIn="animated swing" class=""></flash-message>
            <router-view></router-view>
        </div>
    </div>
@endsection
