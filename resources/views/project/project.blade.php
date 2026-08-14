@extends('layout.layout')

@section('content')
    <section id="project-wrapper">
        <section id="entity-content" class="project-content">
            {{-- projects dinamic content  --}}
            @yield('projects.content')
        </section>
    </section>
@stop
