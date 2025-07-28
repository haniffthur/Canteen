@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Tambah Karyawan Baru</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="employee_id">ID Karyawan (NIK)</label>
                <input type="text" name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" value="{{ old('employee_id') }}" required>
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="email">Email (Opsional)</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-3">
                <label for="department">Departemen (Opsional)</label>
                <input type="text" name="department" id="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department') }}">
                @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
             <hr>
            <h6 class="font-weight-bold">Informasi Kartu (Opsional)</h6>
            <p class="text-muted small">Isi jika karyawan langsung diberikan kartu saat pendaftaran.</p>

            {{-- FIELD BARU UNTUK NOMOR KARTU --}}
            <div class="form-group mb-3">
                <label for="card_number">Nomor Kartu</label>
                <input type="text" name="card_number" id="card_number" class="form-control @error('card_number') is-invalid @enderror" value="{{ old('card_number') }}" placeholder="Kosongkan jika belum ada kartu">
                @error('card_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
             <div class="form-group mb-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="resigned" {{ old('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection