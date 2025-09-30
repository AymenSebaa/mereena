@php
    $items = 'inventories';
    $item = 'inventory';
    $routePrefix = 'stock.inventories';
@endphp

@extends('crud.index')
@extends('stock::inventories.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6><i class="bi bi-box"></i> ${item.product?.name ?? '-'}</h6>
@endsection

@section('item-body')
    <div class="small mb-2">
        <i class="bi bi-truck"></i>  ${item.supplier?.name ?? '-'} <br>
        <i class="bi bi-cash"></i> ${item.price} | <i class="bi bi-boxes"></i> ${item.quantity}<br>
        <i class="bi bi-calendar-plus"></i> ${item.made_at ?? '-'} | <i class="bi bi-calendar-x"></i> ${item.expires_at ?? '-'}
    </div>
@endsection
