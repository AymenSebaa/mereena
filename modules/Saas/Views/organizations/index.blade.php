@php
    $items = 'organizations';
    $item = 'organization';
    $routePrefix = "saas.$items";
@endphp

@extends('crud.index')
@extends('saas::organizations.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6><i class="bi bi-building"></i> ${item.name}</h6>
@endsection

@section('item-body')
    <small class="text-muted">${item.slug ?? ''}</small>
    <div class="mt-1">
        <small class="text-muted"><b>Email:</b> ${item.email ?? 'N/A'}</small><br>
        <small class="text-muted"><b>Phone:</b> ${item.phone ?? 'N/A'}</small><br>
        <small class="text-muted"><b>Address:</b> ${item.address ?? 'N/A'}</small>
    </div>
@endsection
