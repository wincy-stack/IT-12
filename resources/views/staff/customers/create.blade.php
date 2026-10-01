@extends('layouts.staff')

@section('title', 'Create Customer – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
        <h1><i class="fa-solid fa-user-plus" style="color:#38bdf8"></i> New Customer Account</h1>
        <p>Register a new customer account. They will be able to login immediately.</p>
    </div>
    <a href="{{ route('staff.customers.index') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Customers
    </a>
</div>

<div class="card" style="max-width:560px;">
    <form action="{{ route('staff.customers.store') }}" method="POST" novalidate>
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input id="name" type="text" name="name" class="form-input"
                    placeholder="Juan Dela Cruz" value="{{ old('name') }}" required>
                @error('name')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="c_email">Email Address</label>
                <input id="c_email" type="email" name="email" class="form-input"
                    placeholder="customer@email.com" value="{{ old('email') }}" required>
                @error('email')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row form-row-2">
            <div class="form-group">
                <label class="form-label" for="c_password">Password</label>
                <input id="c_password" type="password" name="password" class="form-input"
                    placeholder="Min. 8 characters" required>
                @error('password')<div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="c_password_confirmation">Confirm Password</label>
                <input id="c_password_confirmation" type="password" name="password_confirmation" class="form-input"
                    placeholder="Re-enter password" required>
            </div>
        </div>

        <div style="display:flex;gap:.85rem;margin-top:.75rem;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Create Account</button>
            <a href="{{ route('staff.customers.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
