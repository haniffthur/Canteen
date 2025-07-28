<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tapping Interface - {{ $gate->name }}</title>

    <!-- Bootstrap Admin Template -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4e73df, #224abe);
            color: #343a40;
        }

        .card {
            border: none;
            border-radius: 1rem;
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }

        .list-group-item {
            border: none;
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: #f1f5ff;
        }

        .menu-item.selected {
            background-color: #e0e7ff !important;
            border-left: 5px solid #4e73df;
            font-weight: 600;
            transform: scale(1.02);
        }

        .form-control-lg {
            border-radius: 0.75rem;
            font-size: 1.25rem;
            padding: 1rem;
            border: 2px solid #d1d3e2;
        }

        #clock {
            font-size: 1rem;
        }

        .swal2-popup {
            font-size: 1.1rem !important;
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tapping Interface - {{ $gate->name }}</h6>
                    <div id="clock" class="font-weight-bold text-secondary"></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Menu Utama / Spesial -->
                        <div class="col-md-6 mb-3">
                            <h5 class="text-center font-weight-bold text-gray-800">Menu Utama / Spesial</h5>
                            <ul class="list-group">
                                @php $mainMenuFound = false; @endphp
                                @foreach ($activeMenus as $item)
                                    @if(in_array($item->menu->category, ['utama', 'spesial']))
                                        <li class="list-group-item list-group-item-primary">{{ $item->menu->name }}</li>
                                        @php $mainMenuFound = true; @endphp
                                    @endif
                                @endforeach
                                @if(!$mainMenuFound)
                                    <li class="list-group-item list-group-item-danger">Tidak Ada Menu Utama</li>
                                @endif
                            </ul>
                        </div>
                        <!-- Menu Opsional -->
                        <div class="col-md-6 mb-3">
                            <h5 class="text-center font-weight-bold text-gray-800">Pilih Menu Opsional</h5>
                            <ul id="optional-menu-list" class="list-group">
                                @php $optionalMenuFound = false; @endphp
                                @foreach ($activeMenus as $item)
                                    @if($item->menu->category === 'opsional')
                                        <li class="list-group-item list-group-item-action menu-item" style="cursor: pointer;" data-id="{{ $item->id }}">
                                            {{ $item->menu->name }}
                                        </li>
                                        @php $optionalMenuFound = true; @endphp
                                    @endif
                                @endforeach
                                @if(!$optionalMenuFound)
                                    <li class="list-group-item">Tidak Ada Menu Opsional</li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <!-- Input Card -->
                    <form id="tapping-form" onsubmit="return false;">
                        <input type="text" id="card-number-input" class="form-control form-control-lg text-center" placeholder="TAP KARTU KARYAWAN DI SINI..." autofocus>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tappingForm = document.getElementById('tapping-form');
    const cardInput = document.getElementById('card-number-input');
    const optionalMenuList = document.getElementById('optional-menu-list');
    const gateId = "{{ $gate->id }}";
    let selectedOptionalIds = [];

    // Update jam setiap detik
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID', {
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Event pilih menu opsional
    optionalMenuList.addEventListener('click', function(e) {
        if (e.target.classList.contains('menu-item')) {
            e.target.classList.toggle('selected');
            const menuId = e.target.dataset.id;
            if (e.target.classList.contains('selected')) {
                selectedOptionalIds.push(menuId);
            } else {
                selectedOptionalIds = selectedOptionalIds.filter(id => id !== menuId);
            }
        }
    });

    // Menampilkan status dengan SweetAlert
    const showStatus = (success, message, details = {}) => {
        Swal.fire({
            icon: success ? 'success' : 'error',
            title: success ? 'Berhasil' : 'Gagal',
            html: details.employee_name ? `<b>${details.employee_name}</b><br>${message}` : message,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            customClass: {
                popup: 'shadow rounded'
            }
        });
    };

    const resetInterface = () => {
        cardInput.value = '';
        cardInput.focus();
        selectedOptionalIds = [];
        document.querySelectorAll('.menu-item.selected').forEach(item => item.classList.remove('selected'));
    };

    // Saat tap kartu
    tappingForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const cardNumber = cardInput.value.trim();
        if (!cardNumber) return;

        try {
            const response = await fetch("{{ route('api.tap.process') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    card_number: cardNumber,
                    gate_id: gateId,
                    optional_ids: selectedOptionalIds
                })
            });

            const result = await response.json();
            showStatus(response.ok, result.message, result);

        } catch (error) {
            showStatus(false, 'Terjadi masalah koneksi.');
        } finally {
            setTimeout(resetInterface, 4000);
        }
    });
});
</script>
</body>
</html>
