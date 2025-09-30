@php
    $items = 'suppliers';
    $item = 'supplier';
    $routePrefix = 'procurement.suppliers';
@endphp

@extends('crud.index')
@extends('procurement::suppliers.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6>${item.name}</h6>
@endsection

@section('item-body')
    <div class="small mb-2">
        <i class="bi bi-envelope"></i> ${item.email ?? '-'}<br>
        <i class="bi bi-telephone"></i> ${item.profile.phone ?? '-'}<br>
    </div>
@endsection
