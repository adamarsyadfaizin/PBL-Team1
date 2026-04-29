@extends('layouts.app')

@section('title', 'Gallery - Berlima Guest House')

@section('body-class', 'page-gallery')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/gallery.css') }}">
@endpush

@section('content')
    <x-gallery.gallery />
@endsection
