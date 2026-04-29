@extends('layouts.app')

@section('title', 'Home - Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
    <x-home.hero />
    <x-home.about />
    <x-home.features />
@endsection