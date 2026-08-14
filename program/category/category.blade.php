@extends('layout.layout')

@section('content')
    <section id="category-wrapper">
        <section id="category-content" class="category-content">
            {{-- categories dinamic content  --}}
            @yield('categories.content')
        </section>
    </section>
@stop
