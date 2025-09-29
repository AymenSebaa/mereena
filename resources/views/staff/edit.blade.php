@extends('layouts.app')

@section('title', 'Edit Staff')

@section('content')
<div class="mobile-padding">
    <h4>Edit Staff</h4>
    <form action="{{ oRoute('staff.update', $staff) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ $staff->name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ $staff->email }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div class="mb-3">
            <label>Role</label>
            <select name="role_id" class="form-control" required>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $staff->profile && $staff->profile->role_id == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Country</label>
            <select name="country_id" class="form-control">
                <option value="">--</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ $staff->profile && $staff->profile->country_id == $country->id ? 'selected' : '' }}>
                        {{ $country->name_en }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Hotel</label>
            <select name="hotel_id" class="form-control">
                <option value="">--</option>
                @foreach($hotels as $hotel)
                    <option value="{{ $hotel->id }}" {{ $staff->profile && $staff->profile->hotel_id == $hotel->id ? 'selected' : '' }}>
                        {{ $hotel->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
