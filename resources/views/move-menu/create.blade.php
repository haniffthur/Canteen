@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">Pindah Menu Antar Counter</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
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

@if($activeSchedule)
<form action="{{ route('move-menu.store') }}" method="POST">
    @csrf
    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                Jadwal Aktif: {{ ucfirst($activeSchedule->meal_type) }} - {{ \Carbon\Carbon::parse($activeSchedule->meal_date)->isoFormat('D MMM Y') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 mb-3">
                    <label for="origin_counter_menu_id" class="form-label">Pindahkan Menu dari Counter</label>
                    <select name="origin_counter_menu_id" id="origin_counter_menu_id" class="form-control" required>
                        <option value="">-- Pilih Menu & Counter Asal --</option>
                        @foreach($movableMenus as $item)
                            <option value="{{ $item->id }}" data-max="{{ $item->balance_qty }}">
                                {{ $item->menu->name }} - (di {{ $item->gate->name }}) - Sisa: {{ $item->balance_qty }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 mb-3">
                    <label for="destination_gate_id" class="form-label">Ke Counter Tujuan</label>
                    <select name="destination_gate_id" id="destination_gate_id" class="form-control" required>
                        <option value="">-- Pilih Counter Tujuan --</option>
                        @foreach($gates as $gate)
                            <option value="{{ $gate->id }}">{{ $gate->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="quantity" class="form-label">Jumlah Porsi</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Pindahkan Menu</button>
        </div>
    </div>
</form>
@else
<div class="card shadow mb-4">
    <div class="card-body text-center">
        <h5 class="text-warning">Tidak Ada Jadwal Aktif</h5>
        <p>Fitur pindah menu hanya dapat digunakan saat ada jadwal makan yang sedang berlangsung.</p>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const originSelect = document.getElementById('origin_counter_menu_id');
    const quantityInput = document.getElementById('quantity');

    originSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const maxQuantity = selectedOption.dataset.max;
        if (maxQuantity) {
            quantityInput.max = maxQuantity;
            quantityInput.placeholder = `Maks: ${maxQuantity}`;
        } else {
            quantityInput.max = null;
            quantityInput.placeholder = '';
        }
    });
});
</script>
@endpush
