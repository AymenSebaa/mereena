@php
    $items = 'organization_users';
    $item = 'orgUser';
    $routePrefix = "saas.$items";
@endphp

@extends('crud.index')
@extends('saas::organization_users.upsert-modal')

@section('title', 'Organization Users')

@section('item-header')
    <h6><i class="bi bi-person-badge"></i> ${ item.user.name ?? 'Unknown User' }</h6>
@endsection

@section('item-body')
    <small class="text-muted">${ item.organization.name ?? 'Unknown Org' }</small>
    <div class="text-muted mt-1">Role: ${ item.role }</div>
@endsection
