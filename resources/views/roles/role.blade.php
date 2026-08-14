@extends('layout.layout')

@section('content')
    <section id="role-wrapper">
        <section id="role-content" class="role-content">
            {{-- roles dinamic content  --}}
            @yield('roles.content')
        </section>
    </section>
@stop
