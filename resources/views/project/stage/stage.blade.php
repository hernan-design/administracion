@extends('layout.layout')

@section('content')
    <section id="stage-wrapper">
        <section id="stage-content" class="stage-content">
            {{-- stages dinamic content  --}}
            @yield('stages.content')
        </section>
    </section>
@stop
