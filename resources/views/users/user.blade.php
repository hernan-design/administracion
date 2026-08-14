@extends('layout.layout')

@section('content')
    <section id="user-wrapper">
        <section id="user-content" class="user-content">
            {{-- users dinamic content  --}}
            @yield('users.content')
        </section>
    </section>
@stop
