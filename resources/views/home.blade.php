@extends('layouts.app')

@section('title', 'Home - Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
    <x-hero />
    <x-about />
    <x-features />
@endsection