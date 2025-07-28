@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Edit Kartu: {{ $card->card_number }}</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('cards.update', $card->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label for="card_number">Nomor Kartu</label>
                <input type="text" name="card_number" id="card_number" class="form-control @error('card_number') is-invalid @enderror" value="{{ old('card_number', $card->card_number) }}" required>
                @error('card_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="employee_id">Tugaskan ke Karyawan</label>
                <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id', $card->employee_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} ({{ $employee->employee_id }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="form-text text-muted">Hanya menampilkan karyawan aktif yang belum memiliki kartu (plus pemegang kartu saat ini).</small>
            </div>

            <div class="form-group mb-3">
                <label for="status">Status Kartu</label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status', $card->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $card->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="lost" {{ old('status', $card->status) == 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('cards.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection