@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Tambah Menu Baru</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('menus.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="name">Nama Menu</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="category">Kategori</label>
                <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                    <option value="utama">Utama</option>
                    <option value="spesial">Spesial</option>
                    <option value="opsional">Opsional</option>
                </select>
                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="description">Deskripsi (Opsional)</label>
                <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('menus.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection