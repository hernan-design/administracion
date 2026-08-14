@extends('layout.layout')

@section('content')
    <section id="occupation-wrapper">
        <section id="occupation-content" class="occupation-content">
            {{-- occupations dinamic content  --}}
            @yield('occupations.content')
        </section>
    </section>
@stop
