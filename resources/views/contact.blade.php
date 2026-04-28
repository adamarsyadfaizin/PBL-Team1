@extends('layouts.app')

@section('title', 'Contact - Berlima Guest House')

@section('body-class', 'contact-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/contact.css') }}">
@endpush

@section('content')
    <x-contact.hero />
    <x-contact.info />
    <x-contact.form />
    <x-contact.map />
@endsection