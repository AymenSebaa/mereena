@php
    $items = 'products';
    $item = 'product';
    $routePrefix = 'stock.products';
@endphp

@extends('crud.index')
@extends('stock::products.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6>${item.name}</h6>
@endsection

@section('item-body')

    <div class="carousel slide mb-2" id="carousel_${item.id}" data-bs-ride="carousel">
        <div class="carousel-indicators">
            ${item.images.map((img,i) => `<button type="button" data-bs-target="#carousel_${item.id}" data-bs-slide-to="${i}" ${i===0?'class="active"':''}></button>`).join('')}
        </div>
        <div class="carousel-inner">
            ${item.images.map((img,i) => `<div class="carousel-item ${i===0?'active':''}">
                <img src="{{ env('APP_URL') }}/${img}" class="d-block w-100" style="height:150px;object-fit:cover;">
            </div>`).join('')}
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carousel_${item.id}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carousel_${item.id}" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <div class="small mb-2">
        <i class="bi bi-upc"></i> ${item.sku ?? '-'}<br>
        <i class="bi bi-tags"></i> ${item.category?.name ?? '-'}<br>
        <i class="bi bi-briefcase"></i> ${item.brand ?? '-'}
    </div>
@endsection
