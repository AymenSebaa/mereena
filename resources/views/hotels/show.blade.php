@extends('layouts.app')

@section('content')
    <h1>{{ $hotel->name }}</h1>
    <p>Address: {{ $hotel->address }}</p>
    <p>Coordinates: {{ $hotel->lat }}, {{ $hotel->lng }}</p>
    <p>Stars: ⭐ {{ $hotel->stars }}</p>

    <a href="{{ oRoute('hotels.index') }}">Back to hotels</a>
@endsection
