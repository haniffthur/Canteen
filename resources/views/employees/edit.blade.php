@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Edit Data Karyawan</h1>

<div class="row">
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Personal</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('employees.update', $employee->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- Form input untuk nama, NIK, email, dll. --}}
                    {{-- ... (kode form yang sudah ada sebelumnya) ... --}}
                    <div class="form-group mb-3">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="employee_id">ID Karyawan (NIK)</label>
                        <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id', $employee->employee_id) }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email (Opsional)</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="department">Departemen (Opsional)</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department', $employee->department) }}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="resigned" {{ old('status', $employee->status) == 'resigned' ? 'selected' : '' }}>Resigned</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Data Karyawan</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Kartu</h6>
            </div>
            <div class="card-body">
                @if($employee->card)
                    {{-- Jika karyawan SUDAH punya kartu --}}
                    <div class="mb-3">
                        <label class="form-label">Nomor Kartu</label>
                        <input type="text" class="form-control" value="{{ $employee->card->card_number }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Kartu</label>
                        <input type="text" class="form-control" value="{{ ucfirst($employee->card->status) }}" readonly>
                    </div>
                    <a href="{{ route('cards.edit', $employee->card->id) }}" class="btn btn-warning w-100">
                        <i class="fas fa-edit"></i> Ubah Data Kartu
                    </a>
                @else
                    {{-- Jika karyawan BELUM punya kartu --}}
                    <div class="alert alert-info text-center">
                        <p class="mb-2">Karyawan ini belum memiliki kartu.</p>
                        <a href="{{ route('cards.create', ['employee_id' => $employee->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Daftarkan & Tugaskan Kartu
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection