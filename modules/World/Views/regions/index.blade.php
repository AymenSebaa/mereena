@php
    $items = 'regions';
    $item = 'region';
    $routePrefix = "world.$items";
@endphp

@extends('crud.index')
@extends('world::regions.upsert-modal')

@section('title', 'Regions')

@section('item-header')
    <h6>
        <i class="bi bi-geo-alt"></i> ${item.name}
        <small class="text-muted">(${item.continent?.name ?? 'N/A'})</small>
    </h6>
@endsection

@section('item-body')
    <p class="text-muted">M49 Code: ${item.m49_code}</p>
@endsection

@push('scripts')
    <script></script>
@endpush
