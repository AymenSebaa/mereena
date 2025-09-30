@php
    $items = 'continents';
    $item = 'continent';
    $routePrefix = "world.$items";
@endphp

@extends('crud.index')
@extends('world::continents.upsert-modal')
@section('title', 'Continents')

@section('item-header')
    <h6><i class="bi bi-globe-americas"></i> ${item.name}</h6>
@endsection

@section('item-body')
@endsection


@push('scripts')
    <script></script>
@endpush
