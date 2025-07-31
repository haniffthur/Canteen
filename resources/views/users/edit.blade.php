{{-- File: resources/views/users/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Edit User</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="role">Role</label>
                <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                    <option value="admin" @if(old('role', $user->role) == 'admin') selected @endif>Admin</option>
                    <option value="hr" @if(old('role', $user->role) == 'hr') selected @endif>HR</option>
                    <option value="security_officer" @if(old('role', $user->role) == 'security_officer') selected @endif>Security Officer</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <hr>
            <p class="text-muted">Kosongkan password jika tidak ingin mengubahnya.</p>
            <div class="form-group mb-3">
                <label for="password">Password Baru</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection