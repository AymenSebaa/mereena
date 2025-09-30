@php
    $items = 'states';
    $item = 'state';
    $routePrefix = "world.$items";
@endphp

@extends('crud.index')
@extends('world::states.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6><i class="bi bi-geo-alt"></i> ${item.name}</h6>
@endsection

@section('item-body')
    <small class="text-muted">
        Country: ${item.country?.name ?? 'N/A'} <br>
        Region: ${item.country?.region?.name ?? 'N/A'} <br>
        Continent: ${item.country?.region?.continent?.name ?? 'N/A'}
    </small>
    <div class="mt-1">
        <span class="badge bg-secondary">ISO2: ${item.iso2 || 'N/A'}</span>
        <span class="badge bg-muted">Lat: ${item.lat ?? 'N/A'} | Lng: ${item.lng ?? 'N/A'}</span>
    </div>
@endsection
