@extends('layouts.guest')

@section('title', 'Home')

@include('home.shared-styles')

@section('content')
    <div id="scroll-progress"
        class="fixed top-0 left-0 h-1 bg-gradient-to-r from-gray-600 to-gray-400 z-[9999] w-0 shadow-lg transition-all duration-100">
    </div>

    @include('home.hero')
    @include('home.stats')
    @include('home.services')
    @include('home.why')
    @include('home.faq')
    @include('home.cta')

    @include('components.waves')
    @include('home.footer')

@endsection