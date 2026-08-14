@extends('layout.layout')

@section('content')
    <section id="profession-wrapper">
        <section id="profession-content" class="profession-content">
            {{-- professions dinamic content  --}}
            @yield('professions.content')
        </section>
    </section>
@stop
