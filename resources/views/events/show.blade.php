@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $event['name'] }}</h1>
    <p><strong>Location:</strong> {{ $event['location'] }}</p>
    <p><strong>Start:</strong> {{ $event['start_time'] }}</p>
    <p><strong>End:</strong> {{ $event['end_time'] }}</p>
    <p><strong>Description:</strong> {{ $event['description'] ?? '—' }}</p>

    {{-- Association to Hotel for pickup/delivery --}}
    @if(!empty($event['hotel']))
        <p><strong>Hotel:</strong> {{ $event['hotel']['name'] }} ({{ $event['hotel']['address'] }})</p>
    @endif

    <a href="{{ oRoute('events.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
