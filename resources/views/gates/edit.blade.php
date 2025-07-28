@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Edit Counter</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('gates.update', $gate->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="name">Nama Counter</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $gate->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group mb-3">
                    <label for="location">Lokasi (Opsional)</label>
                    <input type="text" name="location" id="location"
                        class="form-control @error('location') is-invalid @enderror"
                        value="{{ old('location', $gate->location) }}">
                    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group mb-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $gate->status) == 'active' ? 'selected' : '' }}>Active
                        </option>
                        <option value="maintenance" {{ old('status', $gate->status) == 'maintenance' ? 'selected' : '' }}>
                            Maintenance</option>
                        <option value="inactive" {{ old('status', $gate->status) == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="start_time">Jam Mulai Aktif (Opsional)</label>
                            <input type="time" name="start_time" id="start_time" class="form-control"
                                value="{{ old('start_time', $gate->start_time ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="stop_time">Jam Selesai Aktif (Opsional)</label>
                            <input type="time" name="stop_time" id="stop_time" class="form-control"
                                value="{{ old('stop_time', $gate->stop_time ?? '') }}">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('gates.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection