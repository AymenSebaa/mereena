@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="mobile-padding">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <input type="text" id="orderSearch" class="form-control rounded-pill" placeholder="Search orders...">
        @include('stock::orders.upsert-modal')
    </div>

    <div id="ordersTable">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Items</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr id="order_{{ $order->id }}">
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->status->name ?? '-' }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ number_format($order->total, 2) }}</td>
                        <td>
                            <ul class="mb-0">
                                @foreach($order->items as $item)
                                    <li>{{ $item->product->name }} (x{{ $item->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning"
                                onclick="openOrderModal({{ $order->id }})">Edit</button>
                            <button class="btn btn-sm btn-danger"
                                onclick="confirmDeleteOrder({{ $order->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('stock::orders.delete-modal')
@endsection
