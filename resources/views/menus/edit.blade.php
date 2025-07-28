@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Edit Menu</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('menus.update', $menu->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label for="name">Nama Menu</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $menu->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="category">Kategori</label>
                <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                    <option value="utama" {{ $menu->category == 'utama' ? 'selected' : '' }}>Utama</option>
                    <option value="spesial" {{ $menu->category == 'spesial' ? 'selected' : '' }}>Spesial</option>
                    <option value="opsional" {{ $menu->category == 'opsional' ? 'selected' : '' }}>Opsional</option>
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="description">Deskripsi (Opsional)</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $menu->description) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('menus.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection