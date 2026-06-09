@extends('layouts.app')

@section('title', 'Daftar Kamar - Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/rooms/shared.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/rooms/index.css') }}">
@endpush

@section('content')
    @include('pages.rooms.partials.index.hero')
    @include('pages.rooms.partials.index.filter')
    @include('pages.rooms.partials.index.recent')
    @include('pages.rooms.partials.index.grid')
    @include('pages.rooms.partials.index.cta')
@endsection

@push('scripts')
<script src="{{ asset('js/pages/rooms/index.js') }}" defer></script>
@endpush
