<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tapping Interface | {{ $gate->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            padding: 1.5rem;
            color: #1a202c;
            overflow: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: white;
            border-radius: 16px;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h1 i {
            color: #667eea;
        }

        .clock {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.125rem;
            font-weight: 600;
            color: #4a5568;
        }

        .clock i {
            color: #667eea;
        }

        .main-content {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            flex-grow: 1;
            overflow: hidden;
        }
        
        .menu-section {
            background: #f7fafc;
            border-radius: 12px;
            padding: 1.5rem;
            border: 2px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }

        .menu-section h2 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .menu-section h2 i {
            color: #667eea;
            font-size: 1rem;
        }

        .menu-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            overflow-y: auto;
            padding-right: 0.5rem;
        }

        .menu-item {
            background: white;
            padding: 1rem 1.25rem;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
            cursor: default;
            flex-shrink: 0;
        }

        .menu-item.clickable {
            cursor: pointer;
        }

        .menu-item.clickable:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        }

        .menu-item-name {
            font-weight: 500;
            font-size: 0.95rem;
        }

        .menu-item.main-menu {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-color: #f5576c;
        }

        .menu-item.main-menu .stock-badge {
            background: rgba(255, 255, 255, 0.25);
            color: white;
        }

        .stock-badge {
            background: #fed7d7;
            color: #c53030;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .stock-badge.warning {
            background: #fef5e7;
            color: #d97706;
        }

        .loading, .empty-state {
            text-align: center;
            padding: 2rem;
            color: #718096;
            font-size: 0.95rem;
        }

        .loading i {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 1.5rem 0;
            flex-shrink: 0;
        }

        .input-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            flex-shrink: 0;
        }

        .input-section label {
            display: block;
            color: white;
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-input {
            width: 100%;
            padding: 1rem;
            font-size: 1.25rem;
            text-align: center;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            font-weight: 600;
            color: #2d3748;
            transition: all 0.3s ease;
            letter-spacing: 2px;
        }

        .card-input:focus {
            outline: none;
            border-color: white;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.3);
            transform: scale(1.02);
        }

        .card-input::placeholder {
            color: #a0aec0;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .swal2-popup {
            font-family: 'Inter', sans-serif !important;
            border-radius: 16px !important;
        }

        /* --- STYLE BARU UNTUK CENTANG --- */
        .menu-details {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .check-icon {
            color: white;
            font-size: 1.2rem;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            width: 0;
        }

        .menu-item.selected {
            background: #667eea;
            border-color: #667eea;
            color: white;
            transform: none;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }
        
        .menu-item.selected .stock-badge {
            background: rgba(255, 255, 255, 0.25);
            color: white;
        }

        .menu-item.selected .check-icon {
            opacity: 1;
            width: auto;
        }
        /* --- AKHIR STYLE BARU --- */

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-utensils"></i>
                Tapping Interface - {{ $gate->name }}
            </h1>
            <div class="clock">
                <i class="far fa-clock"></i>
                <span id="clock">00:00:00</span>
            </div>
        </div>

        <div class="main-content">
            <div class="menu-grid">
                <div class="menu-section">
                    <h2>
                        <i class="fas fa-star"></i>
                        Menu Utama / Spesial
                    </h2>
                    <div id="main-menu-list" class="menu-list">
                    </div>
                </div>

                <div class="menu-section">
                    <h2>
                        <i class="fas fa-plus-circle"></i>
                        Menu Opsional
                    </h2>
                    <div id="optional-menu-list" class="menu-list">
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="input-section">
                <label for="card-number-input">
                    <i class="fas fa-id-card"></i> Tap Kartu Karyawan
                </label>
                <form id="tapping-form" onsubmit="return false;">
                    <input 
                        type="text" 
                        id="card-number-input" 
                        class="card-input" 
                        placeholder="TAP KARTU DI SINI..." 
                        autofocus
                        autocomplete="off"
                    >
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainMenuList = document.getElementById('main-menu-list');
        const optionalMenuList = document.getElementById('optional-menu-list');
        const tappingForm = document.getElementById('tapping-form');
        const cardInput = document.getElementById('card-number-input');
        const gateId = "{{ $gate->id }}";
        const menuApiUrl = `{{ route('api.tapping.menu', $gate->id) }}`;
        let selectedOptionalIds = [];

        const fetchAndUpdateMenus = async () => {
            try {
                const response = await fetch(menuApiUrl);
                if (!response.ok) throw new Error('Network response was not ok');
                const menus = await response.json();

                mainMenuList.innerHTML = '';
                optionalMenuList.innerHTML = '';

                let mainMenuFound = false;
                let optionalMenuFound = false;

                menus.forEach(item => {
                    const stockBadge = item.balance_qty !== null 
                        ? `<span class="stock-badge ${item.menu.category === 'opsional' ? 'warning' : ''}">Sisa: ${item.balance_qty}</span>` 
                        : '';

                    if (['utama', 'spesial'].includes(item.menu.category)) {
                        const div = document.createElement('div');
                        div.className = 'menu-item main-menu';
                        div.innerHTML = `<span class="menu-item-name">${item.menu.name}</span> ${stockBadge}`;
                        mainMenuList.appendChild(div);
                        mainMenuFound = true;
                    } else if (item.menu.category === 'opsional') {
                        const div = document.createElement('div');
                        div.className = 'menu-item clickable';
                        div.dataset.id = item.id;
                        div.innerHTML = `
                            <div class="menu-details">
                                <i class="fas fa-check-circle check-icon"></i>
                                <span class="menu-item-name">${item.menu.name}</span>
                            </div>
                            ${stockBadge}
                        `;
                        optionalMenuList.appendChild(div);
                        optionalMenuFound = true;
                    }
                });

                if (!mainMenuFound) {
                    mainMenuList.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><p>Tidak ada menu utama tersedia</p></div>';
                }
                if (!optionalMenuFound) {
                    optionalMenuList.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><p>Tidak ada menu opsional tersedia</p></div>';
                }

            } catch (error) {
                console.error("Gagal memuat menu:", error);
                mainMenuList.innerHTML = '<div class="empty-state" style="color: #e53e3e;"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat menu</p></div>';
                optionalMenuList.innerHTML = '<div class="empty-state" style="color: #e53e3e;"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat menu</p></div>';
            }
        };

        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
            document.getElementById('clock').textContent = timeString.replace(/\./g, ':');
        }

        optionalMenuList.addEventListener('click', function(e) {
            const menuItem = e.target.closest('.menu-item.clickable');
            if (menuItem) {
                menuItem.classList.toggle('selected');
                const menuId = menuItem.dataset.id;
                if (menuItem.classList.contains('selected')) {
                    selectedOptionalIds.push(menuId);
                } else {
                    selectedOptionalIds = selectedOptionalIds.filter(id => id !== menuId);
                }
                setTimeout(() => {
                    cardInput.focus();
                }, 10);
            }
        });

        const showStatus = (success, message, details = {}) => {
            Swal.fire({
                icon: success ? 'success' : 'error',
                title: success ? 'Berhasil' : 'Gagal',
                html: details.employee_name ? `<b style="font-size: 1.25rem;">${details.employee_name}</b><br><span style="color: #4a5568;">${message}</span>` : message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                didOpen: () => {
                    setTimeout(() => cardInput.focus(), 100);
                },
                didClose: () => {
                    setTimeout(() => cardInput.focus(), 100);
                }
            });
        };

        const resetInterface = () => {
            cardInput.value = '';
            cardInput.focus();
            selectedOptionalIds = [];
            document.querySelectorAll('.menu-item.selected').forEach(item => item.classList.remove('selected'));
        };

        tappingForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const cardNumber = cardInput.value.trim();
            if (!cardNumber) return;
            
            cardInput.disabled = true;

            try {
                const response = await fetch("{{ route('api.tap.process') }}", {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({
                        card_number: cardNumber,
                        gate_id: gateId,
                        optional_ids: selectedOptionalIds
                    })
                });
                const result = await response.json();
                
                resetInterface();
                cardInput.disabled = false;
                
                showStatus(response.ok, result.message, result);

                if (response.ok) {
                    fetchAndUpdateMenus();
                }

            } catch (error) {
                resetInterface();
                cardInput.disabled = false;
                showStatus(false, 'Terjadi masalah koneksi.');
            }
        });

        const refreshCsrfToken = async () => {
            try {
                const response = await fetch("{{ route('refresh.csrf') }}");
                const data = await response.json();
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                console.log('CSRF token has been refreshed.');
            } catch (error) {
                console.error('Failed to refresh CSRF token:', error);
            }
        };
        
        // Panggilan awal dan interval
        fetchAndUpdateMenus();
        setInterval(fetchAndUpdateMenus, 30000); // Refresh menu setiap 30 detik
        setInterval(updateClock, 1000);
        setInterval(refreshCsrfToken, 3600000); // Refresh CSRF token setiap 1 jam
        updateClock();
    });
    </script>
</body>
</html>