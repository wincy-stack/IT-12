@extends('layouts.app')

@section('title', 'Create Customer – Aqua De Smiley')

@section('content')
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1><i class="fa-solid fa-user-plus" style="color:#38bdf8;margin-right:.6rem"></i>Create Customer Account</h1>
        <p>Fill in the details below to register a new customer.</p>
    </div>
    <a href="{{ route('staff.dashboard') }}" class="btn btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<div class="card" style="max-width:560px;">
    <form action="{{ route('staff.customers.store') }}" method="POST" novalidate>
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input
                id="name"
                type="text"
                name="name"
                class="form-input"
                placeholder="Juan Dela Cruz"
                value="{{ old('name') }}"
                required
            >
            @error('name')
                <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="c_email">Email Address</label>
            <input
                id="c_email"
                type="email"
                name="email"
                class="form-input"
                placeholder="customer@email.com"
                value="{{ old('email') }}"
                required
            >
            @error('email')
                <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="c_password">Password</label>
            <input
                id="c_password"
                type="password"
                name="password"
                class="form-input"
                placeholder="Minimum 8 characters"
                required
            >
            @error('password')
                <div class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom:1.75rem;">
            <label class="form-label" for="c_password_confirmation">Confirm Password</label>
            <input
                id="c_password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input"
                placeholder="Re-enter password"
                required
            >
        </div>

        <div style="display:flex;gap:1rem;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> Create Account
            </button>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
