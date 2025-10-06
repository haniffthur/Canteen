<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Display - {{ $gate->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Variabel Warna dari Tapping Interface */
        :root {
            --text-dark: #2d3748;
            --text-secondary: #6B7280;
            --accent-main: #667eea;
            --panel-bg: #FFFFFF;
            --panel-bg-light: #f7fafc;
            --border-color: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--text-dark);
            height: 100vh;
            overflow: hidden;
            padding: 1.5rem;
        }

        .kds-container {
            display: flex;
            height: 100%;
            gap: 1.5rem;
        }

        .main-order, .order-history {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            background: var(--panel-bg);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .main-order {
            flex-grow: 1;
        }

        .order-history {
            width: 380px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .header h1 { font-size: 1.5rem; font-weight: 700; }
        .header h1 i { color: var(--accent-main); }

        .clock {
            font-size: 1.1rem;
            font-weight: 600;
            background: var(--panel-bg-light);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 2px solid var(--border-color);
        }
        
        #main-order-display {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .waiting-state { text-align: center; color: var(--text-secondary); }
        .waiting-state i { font-size: 4rem; color: var(--border-color); margin-bottom: 1rem; }
        .waiting-state h2 { font-size: 1.75rem; color: var(--text-dark);}

        .order-ticket {
            background: var(--panel-bg);
            color: var(--text-dark);
            width: 100%;
            max-width: 500px;
        }
        
        .ticket-header { text-align: center; padding-bottom: 1rem; margin-bottom: 1.5rem; border-bottom: 2px dashed #D1D5DB; }
        .employee-name { font-size: 2.25rem; font-weight: 700; }
        .order-time { font-size: 1rem; color: var(--text-secondary); margin-top: 0.25rem; }

        .menu-list { list-style: none; }
        .menu-item { display: flex; align-items: center; gap: 1rem; font-size: 1.2rem; padding: 0.75rem 0; }
        .menu-item:not(:last-child) { border-bottom: 1px solid var(--border-color); }
        .menu-item.main-menu .menu-text { font-weight: 600; color: var(--text-dark); }
        .menu-item .menu-icon { color: var(--accent-main); }

        .order-history h2 { margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.75rem; }
        .order-history h2 i { color: var(--accent-main); }
        #order-history-list { display: flex; flex-direction: column; gap: 1rem; overflow-y: auto; padding-right: 0.5rem; }
        
        .history-ticket {
            background: var(--panel-bg-light);
            border: 2px solid var(--border-color);
            padding: 1rem;
            border-radius: 8px;
        }
        .history-ticket-header {
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid #d1d5db;
        }
        .history-ticket .employee-name-history { font-weight: 600; }
        .history-ticket .order-time-history { font-size: 0.85rem; color: var(--text-secondary); }
        .history-menu-list {
            list-style: none;
            padding-left: 0.25rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }
        .history-menu-list li {
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .history-menu-list li.main {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .progress-bar {
            position: fixed;
            bottom: 0; left: 0;
            height: 5px;
            background-color: var(--accent-main);
            width: 100%;
        }

        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
        
        @keyframes progress { from { width: 100%; } to { width: 0%; } }
        .progress-bar.active { animation: progress 3s linear forwards; }
    </style>
</head>
<body>
    <div class="kds-container">
        <main class="main-order">
            <div class="header">
                <h1><i class="fas fa-concierge-bell"></i> <span>Kitchen Display - {{ $gate->name }}</span></h1>
                <div class="clock"><i class="far fa-clock"></i> <span id="clock">--:--:--</span></div>
            </div>
            <div id="main-order-display">
                </div>
        </main>

        <aside class="order-history">
            <h2><i class="fas fa-history"></i> Riwayat Pesanan</h2>
            <div id="order-history-list">
                </div>
        </aside>
    </div>
    
    <div id="progress-bar" class="progress-bar"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainOrderDisplay = document.getElementById('main-order-display');
        const historyListDisplay = document.getElementById('order-history-list');
        const progressBar = document.getElementById('progress-bar');
        const logsApiUrl = `{{ route('api.waiter-view.logs', $gate->id) }}`;
        
        let lastOrderId = null;
        let orderHistory = [];

        const fetchAndUpdateDisplay = async () => {
            progressBar.classList.remove('active');
            void progressBar.offsetWidth;
            progressBar.classList.add('active');

            try {
                const response = await fetch(logsApiUrl);
                const order = await response.json();

                if (order && order.id !== lastOrderId) {
                    lastOrderId = order.id;
                    mainOrderDisplay.innerHTML = createOrderTicketElement(order);
                    
                    orderHistory.unshift(order);
                    if (orderHistory.length > 4) {
                        orderHistory.pop();
                    }
                    renderOrderHistory();
                }

            } catch (error) {
                console.error("Gagal mengambil data pesanan:", error);
            }
        };
        
        const showWaitingState = () => {
            mainOrderDisplay.innerHTML = `
                <div class="waiting-state fade-in">
                    <i class="fas fa-receipt"></i>
                    <h2>Menunggu Transaksi</h2>
                </div>
            `;
        };

        const renderOrderHistory = () => {
        historyListDisplay.innerHTML = orderHistory.map(order => {
            // BARU: Urutkan menu di riwayat juga
            order.menus.sort((a, b) => {
                const isMainA = ['utama', 'spesial'].includes(a.category);
                const isMainB = ['utama', 'spesial'].includes(b.category);
                if (isMainA && !isMainB) return -1;
                if (!isMainA && isMainB) return 1;
                return 0;
            });

            const historyMenuHtml = order.menus.map(menu => {
                const isMain = ['utama', 'spesial'].includes(menu.category);
                return `<li class="${isMain ? 'main' : ''}"><i class="fas fa-xs fa-circle"></i> ${menu.name}</li>`;
            }).join('');

            return `
                <div class="history-ticket fade-in">
                    <div class="history-ticket-header">
                        <div class="employee-name-history">${order.employee_name}</div>
                        <div class="order-time-history">${order.tapped_at}</div>
                    </div>
                    <ul class="history-menu-list">
                        ${historyMenuHtml}
                    </ul>
                </div>
            `;
        }).join('');
    };

        const createOrderTicketElement = (order) => {
        // BARU: Urutkan menu sebelum di-render
        order.menus.sort((a, b) => {
            const isMainA = ['utama', 'spesial'].includes(a.category);
            const isMainB = ['utama', 'spesial'].includes(b.category);
            if (isMainA && !isMainB) return -1; // a (menu utama) ditaruh sebelum b
            if (!isMainA && isMainB) return 1;  // b (menu utama) ditaruh sebelum a
            return 0; // Jika sama-sama utama atau opsional, biarkan urutan aslinya
        });

        const menuHtml = order.menus.map(menu => {
            const isMain = ['utama', 'spesial'].includes(menu.category);
            return `
                <li class="menu-item ${isMain ? 'main-menu' : ''}">
                    <i class="menu-icon ${isMain ? 'fas fa-star' : 'fas fa-plus'}"></i>
                    <span class="menu-text">${menu.name}</span>
                </li>
            `;
        }).join('');

        return `
            <div class="order-ticket fade-in">
                <div class="ticket-header">
                    <div class="employee-name">${order.employee_name}</div>
                    <div class="order-time">${order.tapped_at}</div>
                </div>
                <ul class="menu-list">${menuHtml}</ul>
            </div>
        `;
    };

        function updateClock() {
            const timeString = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('clock').textContent = timeString.replace(/\./g, ':');
        }

        showWaitingState();
        setInterval(fetchAndUpdateDisplay, 3000);
        setInterval(updateClock, 1000);
        updateClock();
    });
    </script>
</body>
</html>