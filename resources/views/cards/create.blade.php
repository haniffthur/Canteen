@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Daftarkan Kartu Baru</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('cards.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="card_number">Nomor Kartu</label>
                    <input type="text" name="card_number" id="card_number"
                        class="form-control @error('card_number') is-invalid @enderror" value="{{ old('card_number') }}"
                        required placeholder="Scan atau ketik nomor kartu...">
                    @error('card_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group mb-3">
                    <label for="employee_id">Tugaskan ke Karyawan</label>
                    <select name="employee_id" id="employee_id"
                        class="form-control @error('employee_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($employees as $employee)
                            {{-- Gunakan $selectedEmployeeId untuk memilih opsi secara otomatis --}}
                            <option value="{{ $employee->id }}" {{ old('employee_id', $selectedEmployeeId) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="form-text text-muted">Hanya menampilkan karyawan aktif yang belum memiliki kartu.</small>
                </div>

                <div class="form-group mb-3">
                    <label for="status">Status Kartu</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="lost">Lost</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('cards.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection