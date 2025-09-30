@php
    $items = 'cities';
    $item = 'city';
    $routePrefix = "world.$items";
@endphp

@extends('crud.index')
@extends('world::cities.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6><i class="bi bi-pin-map"></i> ${item.name}</h6>
@endsection

@section('item-body')
    <small class="text-muted">
        State: ${item.state?.name ?? 'N/A'} <br>
        Country: ${item.state?.country?.name ?? 'N/A'} <br>
        Region: ${item.state?.country?.region?.name ?? 'N/A'}
    </small>
    <div class="mt-1">
        <span class="badge bg-secondary">Zip: ${item.zip_code || 'N/A'}</span>
        <span class="badge bg-muted">Lat: ${item.lat ?? 'N/A'} | Lng: ${item.lng ?? 'N/A'}</span>
    </div>
@endsection
