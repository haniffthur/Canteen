@extends('layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Daftar Jadwal Makan</h1>
    <p class="mb-4">Halaman ini menampilkan semua jadwal makan yang telah dibuat. Anda dapat membuat, melihat detail, mengubah, dan menghapus jadwal dari sini.</p>

    <div class="card shadow mb-4">
       <div class="card-header py-3">
        {{-- Tombol untuk membuka modal 'Create' --}}
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createScheduleModal">
            <i class="fas fa-plus"></i> Buat Jadwal Baru
        </button>
    </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Sesi Makan</th>
                            <th>Tipe Hari</th>
                            <th>Jumlah Menu Ditugaskan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schedules as $schedule)
                            <tr>
                                <td>{{ $loop->iteration + $schedules->firstItem() - 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->meal_date)->isoFormat('dddd, D MMMM Y') }}</td>
                                <td>{{ ucfirst($schedule->meal_type) }}</td>
                                <td>
                                    <span
                                        class="badge {{ $schedule->day_type == 'special' ? 'bg-success' : 'bg-info' }} text-white">
                                        {{ ucfirst($schedule->day_type) }}
                                    </span>
                                </td>
                                <td>{{ $schedule->counter_menus_count }} Menu</td>
                                <td>
                                    <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-info btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                                    
                                    <button type="button" class="btn btn-warning btn-sm edit-schedule-btn" 
                                            data-url="{{ route('schedules.edit', $schedule->id) }}"
                                            data-update-url="{{ route('schedules.update', $schedule->id) }}"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <!-- <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus jadwal ini? Semua data penugasan menu di dalamnya juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> -->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada jadwal yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $schedules->links() }}
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="createScheduleModal" tabindex="-1" role="dialog" aria-labelledby="createScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('schedules.store') }}" method="POST" id="scheduleForm">
                @csrf
                <div id="method-field"></div> {{-- Placeholder untuk @method('PUT') saat edit --}}
                
                <div class="modal-header">
                    <h5 class="modal-title" id="createScheduleModalLabel">Buat Jadwal Makan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">Harap periksa kembali input Anda.</div>
                    @endif
                    
                    {{-- Detail Jadwal --}}
                    <div class="row">
                        <div class="col-md-4 mb-3"><label>Tanggal</label><input type="date" class="form-control" name="meal_date" value="{{ old('meal_date', date('Y-m-d')) }}" required></div>
                        <div class="col-md-4 mb-3"><label>Sesi Makan</label><select class="form-control" name="meal_type" required><option value="lunch">Makan Siang</option><option value="dinner">Makan Malam</option></select></div>
                        <div class="col-md-4 mb-3"><label>Tipe Hari</label><select class="form-control" name="day_type" required><option value="normal">Normal</option><option value="special">Spesial</option></select></div>
                    </div>
                    <hr>

                    {{-- Penugasan Menu Dinamis --}}
                    <h6>Penugasan Menu</h6>
                    <div id="assignments-container"></div>
                    <button type="button" id="add-row-btn" class="btn btn-success btn-sm mt-2"><i class="fas fa-plus"></i> Tambah Menu</button>
                    <hr>

                    {{-- Pemilihan Gate/Counter --}}
                    <h6>Terapkan ke Counter</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="apply_to_gates" id="applyToAll" value="all" checked>
                        <label class="form-check-label" for="applyToAll">Terapkan ke Semua Counter Aktif</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="apply_to_gates" id="applyToSelected" value="selected">
                        <label class="form-check-label" for="applyToSelected">Pilih Beberapa Counter</label>
                    </div>
                    <div id="gate-selection-container" class="mt-2 d-none" style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="checkAllGates">
                            <label class="custom-control-label" for="checkAllGates">Pilih Semua</label>
                        </div>
                        @foreach($gates as $gate)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input gate-checkbox" name="gate_ids[]" value="{{ $gate->id }}" id="gate_{{ $gate->id }}">
                                <label class="custom-control-label" for="gate_{{ $gate->id }}">{{ $gate->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Referensi elemen DOM ---
    const modal = document.getElementById('createScheduleModal');
    const modalTitle = document.getElementById('createScheduleModalLabel');
    const scheduleForm = document.getElementById('scheduleForm');
    const methodField = document.getElementById('method-field');
    const menuContainer = document.getElementById('assignments-container');
    const addMenuBtn = document.getElementById('add-row-btn');
    const gateSelectionContainer = document.getElementById('gate-selection-container');
    const checkAllGates = document.getElementById('checkAllGates');
    const gateCheckboxes = document.querySelectorAll('.gate-checkbox');

    let assignmentIndex = 0;
    const menusData = @json($menus->map(fn($m) => ['id' => $m->id, 'name' => "{$m->name} ({$m->category})"]));

    // --- Logika form Create/Edit ---
    const setupCreateForm = () => {
        scheduleForm.action = "{{ route('schedules.store') }}";
        methodField.innerHTML = '';
        modalTitle.textContent = 'Buat Jadwal Makan Baru';
        scheduleForm.reset();
        menuContainer.innerHTML = '';
        assignmentIndex = 0;
        addRow();
        gateSelectionContainer.classList.add('d-none');
        document.getElementById('applyToAll').checked = true;
    };

    const setupEditForm = async (url, updateUrl) => {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            
            // Set form action & method
            scheduleForm.action = updateUrl;
            methodField.innerHTML = `@method('PUT')`;
            modalTitle.textContent = `Edit Jadwal`;

            // Isi field utama
            scheduleForm.querySelector('[name="meal_date"]').value = data.schedule.meal_date;
            scheduleForm.querySelector('[name="meal_type"]').value = data.schedule.meal_type;
            scheduleForm.querySelector('[name="day_type"]').value = data.schedule.day_type;
            
            // Isi menu assignments
            menuContainer.innerHTML = '';
            assignmentIndex = 0;
            if (data.assignments.length > 0) {
                data.assignments.forEach(assign => {
                    addRow(assign);
                });
            } else {
                addRow(); // Tambah satu baris kosong jika tidak ada assignment
            }

            // Isi gate selections
            const totalActiveGates = {{ $gates->count() }};
            const selectedGateCount = data.gate_ids.length;

            if (selectedGateCount > 0 && selectedGateCount === totalActiveGates) {
                scheduleForm.querySelector('#applyToAll').checked = true;
                gateSelectionContainer.classList.add('d-none');
            } else {
                scheduleForm.querySelector('#applyToSelected').checked = true;
                gateSelectionContainer.classList.remove('d-none');
                gateCheckboxes.forEach(cb => {
                    cb.checked = data.gate_ids.includes(parseInt(cb.value));
                });
            }

            var myModal = new bootstrap.Modal(modal);
            myModal.show();

        } catch (error) {
            console.error('Gagal mengambil data jadwal:', error);
            alert('Gagal memuat data untuk diedit.');
        }
    };

    // --- Fungsi Bantuan ---
    const addRow = (data = null) => {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'align-items-end', 'assignment-row', 'mb-2');
        
        let menuOptions = '<option value="">-- Pilih --</option>';
        menusData.forEach(menu => {
            const isSelected = data && data.menu_id == menu.id ? 'selected' : '';
            menuOptions += `<option value="${menu.id}" ${isSelected}>${menu.name}</option>`;
        });

        // [DIUBAH] Tambahkan class 'meal-option-type-select' dan 'supply-qty-input'
        newRow.innerHTML = `
            <div class="col-md-5"><label>Menu</label><select name="assignments[${assignmentIndex}][menu_id]" class="form-control" required>${menuOptions}</select></div>
            <div class="col-md-3"><label>Tipe Opsi</label><select name="assignments[${assignmentIndex}][meal_option_type]" class="form-control meal-option-type-select" required><option value="default" ${data && data.meal_option_type == 'default' ? 'selected' : ''}>Default</option><option value="optional" ${data && data.meal_option_type == 'optional' ? 'selected' : ''}>Optional</option></select></div>
            <div class="col-md-3"><label>Stok</label><input type="number" name="assignments[${assignmentIndex}][supply_qty]" class="form-control supply-qty-input" value="${data && data.supply_qty ? data.supply_qty : ''}" placeholder="Kosong = ∞"></div>
            <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-row-btn">&times;</button></div>
        `;
        
        // [BARU] Cek kondisi awal saat baris dibuat
        const optionSelect = newRow.querySelector('.meal-option-type-select');
        const stockInput = newRow.querySelector('.supply-qty-input');
        if (optionSelect.value === 'default') {
            stockInput.disabled = true;
            stockInput.value = ''; // Pastikan kosong jika default
        }

        menuContainer.appendChild(newRow);
        assignmentIndex++;
    };
    
    // --- Event Listeners ---
    document.querySelector('button[data-target="#createScheduleModal"]').addEventListener('click', setupCreateForm);
    
    document.body.addEventListener('click', function(e) {
        if (e.target.closest('.edit-schedule-btn')) {
            const btn = e.target.closest('.edit-schedule-btn');
            setupEditForm(btn.dataset.url, btn.dataset.updateUrl);
        }
    });

    document.querySelectorAll('input[name="apply_to_gates"]').forEach(radio => {
        radio.addEventListener('change', function() {
            gateSelectionContainer.classList.toggle('d-none', this.value === 'all');
        });
    });

    checkAllGates.addEventListener('change', function() {
        gateCheckboxes.forEach(checkbox => checkbox.checked = this.checked);
    });

    addMenuBtn.addEventListener('click', () => addRow());

    menuContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row-btn')) {
            // Jangan hapus baris terakhir
            if (menuContainer.querySelectorAll('.assignment-row').length > 1) {
                e.target.closest('.assignment-row').remove();
            }
        }
    });
    menuContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('meal-option-type-select')) {
            const row = e.target.closest('.assignment-row');
            const stockInput = row.querySelector('.supply-qty-input');

            if (e.target.value === 'default') {
                stockInput.disabled = true;
                stockInput.value = ''; // Otomatis kosongkan field stok
            } else { // Jika 'optional'
                stockInput.disabled = false;
            }
        }
    });

    // Jika ada error validasi dari server, buka kembali modal
    @if($errors->any())
        var myModal = new bootstrap.Modal(modal);
        myModal.show();
    @endif
});
</script>
@endpush