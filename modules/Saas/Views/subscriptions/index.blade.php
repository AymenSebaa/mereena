@php
    $items = 'subscriptions';
    $item = 'subscription';
    $routePrefix = "saas.$items";
@endphp

@extends('crud.index')
@extends('saas::subscriptions.upsert-modal')

@section('title', ucfirst($items))

@section('item-header')
    <h6><i class="bi bi-file-earmark-text"></i> ${item.organization?.name ?? 'N/A'}</h6>
@endsection

@section('item-body')
    <small class="text-muted">${item.plan?.name ?? ''}</small>
    <div class="small text-muted"> ${item.status} — ${formatDateTime(item.starts_at) ?? 'N/A'} to ${formatDateTime(item.ends_at) ?? 'N/A'} </div>
    <div class="mt-1">
        <span class="badge bg-secondary">Plan: ${item.plan?.name ?? 'N/A'}</span>
        <span class="badge bg-muted">Status: ${item.status}</span>
    </div>
@endsection
