@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-gray-800">Atur Jadwal Makan</h1>
    <a href="{{ route('schedules.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('schedules.store') }}" method="POST">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-header">Detail Jadwal</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="meal_date" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="meal_date" name="meal_date" value="{{ old('meal_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="meal_type" class="form-label">Sesi Makan</label>
                    <select class="form-select form-control" id="meal_type" name="meal_type" required>
                        <option value="lunch" {{ old('meal_type') == 'lunch' ? 'selected' : '' }}>Makan Siang (Lunch)</option>
                        <option value="dinner" {{ old('meal_type') == 'dinner' ? 'selected' : '' }}>Makan Malam (Dinner)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="day_type" class="form-label">Tipe Hari</label>
                    <select class="form-select form-control" id="day_type" name="day_type" required>
                        <option value="normal" {{ old('day_type') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="special" {{ old('day_type') == 'special' ? 'selected' : '' }}>Spesial (Stok Terbatas)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">Penugasan Menu ke Counter</div>
        <div class="card-body">
            <p>Pilih menu yang akan disajikan di setiap counter. Untuk hari **Normal**, biarkan stok kosong. Untuk hari **Spesial**, isi jumlah stok.</p>
            <div id="assignments-container">
                </div>
            <button type="button" id="add-row-btn" class="btn btn-success mt-2"><i class="fas fa-plus"></i> Tambah Penugasan Menu</button>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let assignmentIndex = 0;
    const container = document.getElementById('assignments-container');
    const addBtn = document.getElementById('add-row-btn');

    const addRow = () => {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'align-items-end', 'assignment-row', 'mb-3');
        
        // Template HTML untuk satu baris baru, pastikan semua 'name' sudah benar
        newRow.innerHTML = `
            <div class="col-md-3">
                <label class="form-label">Counter/Gate</label>
                <select name="assignments[${assignmentIndex}][gate_id]" class="form-select form-control" required>
                    @foreach($gates as $gate)
                    <option value="{{ $gate->id }}">{{ $gate->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Menu</label>
                <select name="assignments[${assignmentIndex}][menu_id]" class="form-select form-control" required>
                    <option value="">-- Pilih Menu --</option>
                    @foreach($menus as $menu)
                    <option value="{{ $menu->id }}">{{ $menu->name }} ({{$menu->category}})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tipe Opsi</label>
                <select name="assignments[${assignmentIndex}][meal_option_type]" class="form-select form-control" required>
                    <option value="default">Default</option>
                    <option value="optional">Optional</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Stok (Hari Spesial)</label>
                <input type="number" name="assignments[${assignmentIndex}][supply_qty]" class="form-control" placeholder="cth: 300">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger remove-row-btn d-block w-100">Hapus</button>
            </div>
        `;
        
        container.appendChild(newRow);
        assignmentIndex++;
    };

    // Tambah baris pertama saat halaman dimuat
    addRow(); 

    // Event listener untuk tombol tambah
    addBtn.addEventListener('click', addRow);

    // Event listener untuk tombol hapus
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row-btn')) {
            e.target.closest('.assignment-row').remove();
        }
    });
});
</script>
@endpush
@endsection
