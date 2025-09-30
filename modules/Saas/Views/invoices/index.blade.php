@php
    $items = 'invoices';
    $item = 'invoice';
    $routePrefix = "saas.$items";
@endphp

@extends('crud.index')
@extends('saas::invoices.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6><i class="bi bi-receipt"></i> Invoice #${item.id}</h6>
@endsection

@section('item-body')
    <span class="badge bg-${item.status === 'paid' ? 'success' : (item.status === 'pending' ? 'warning' : 'secondary')}">
        ${item.status}
    </span>
    <div class="mt-1">
        <span class="badge bg-secondary">Amount: ${item.amount} DA</span>
        <span class="badge bg-muted">Due: ${item.due_date ?? '-'}</span>
    </div>
@endsection
