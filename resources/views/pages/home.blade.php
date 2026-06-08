@extends('layouts.app')

@section('title', 'Home - Berlima Guest House')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
    <x-home.hero 
        :settings="$homeSettings"
        :stats="$homeStats"
        :search-rooms="$searchRooms"
        :guest-profile="$guestProfile"
    />
    <x-home.about 
        :settings="$homeSettings"
        :recommended-room="$highlightRooms->first()"
    />
    <x-home.features 
        :settings="$homeSettings"
        :rooms="$highlightRooms"
        :guest-profile="$guestProfile"
    />
@endsection