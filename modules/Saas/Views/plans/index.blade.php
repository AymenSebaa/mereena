@php
    $items = 'plans';
    $item = 'plan';
    $routePrefix = "saas.$items";
@endphp

@extends('crud.index')
@extends('saas::plans.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6><i class="bi bi-tags"></i> ${item.name}</h6>
@endsection

@section('item-body')
    <small class="text-muted">${item.slug ?? ''}</small>
    <div class="small text-muted"> ${item.interval} — $${item.price} </div>
    <div class="mt-1">
        <span class="badge bg-secondary">Interval: ${item.interval}</span>
        <span class="badge bg-muted">Price: $${item.price}</span>
    </div>
@endsection
