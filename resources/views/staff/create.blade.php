@extends('layouts.app')

@section('title', 'Add Staff')

@section('content')
    <div class="mobile-padding">
        <h4>Add New Staff</h4>
        <form action="{{ oRoute('staff.store') }}" method="POST" class="mt-3">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select name="role_id" class="form-control" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Country</label>
                <select name="country_id" class="form-control">
                    <option value="">--</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Hotel</label>
                <select name="hotel_id" class="form-control">
                    <option value="">--</option>
                    @foreach ($hotels as $hotel)
                        <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
