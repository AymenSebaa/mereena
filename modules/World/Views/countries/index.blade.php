@php
    $items = 'countries';
    $item = 'country';
    $routePrefix = "world.$items";
@endphp

@extends('crud.index')
@extends('world::countries.upsert-modal')

@section('title', 'Countries')

@section('item-header')
    <h6 class="d-flex align-items-center">
        <i class="bi bi-flag"></i>
        <span class="ms-2">${item.name}</span>
        <span class="display-6 ms-2">${item.emoji || ''}</span>
    </h6>
@endsection

@section('item-body')
    <small class="text-muted">
        Region: ${item.region?.name ?? 'N/A'} | Continent: ${item.region?.continent?.name ?? 'N/A'}
    </small>
    <div class="mt-1">
        <span class="badge bg-secondary">ISO2: ${item.iso2}</span>
        <span class="badge bg-secondary">ISO3: ${item.iso3}</span>
        <span class="badge bg-info text-dark">Phone: ${item.phone_code || 'N/A'}</span>
        <span class="badge bg-success">Currency: ${item.currency || 'N/A'}</span>
    </div>
    <div class="mt-1 text-muted">
        Lat: ${item.lat ?? 'N/A'}, Lng: ${item.lng ?? 'N/A'}
    </div>
@endsection
