@extends('layout.layout')

@section('content')
    <section id="industry-wrapper">
        <section id="industry-content" class="industry-content">
            {{-- industries dinamic content  --}}
            @yield('industries.content')
        </section>
    </section>
@stop
