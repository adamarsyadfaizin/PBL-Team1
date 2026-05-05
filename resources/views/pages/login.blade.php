@extends('layouts.auth')

@section('title', 'Home - Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/signup.css') }}">
@endpush

@section('content')
    <x-login.login />
@endsection