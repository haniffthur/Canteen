
# Proyek Canteen

Proyek "Canteen" adalah aplikasi web berbasis Laravel 10 yang dirancang untuk mengelola dan memantau proses pengambilan jatah makan karyawan di sebuah perusahaan. Sistem ini menggantikan proses manual dengan sistem tap kartu RFID/NFC yang terintegrasi, menyediakan data real-time dan menyederhanakan proses pelaporan.

## Teknologi yang Digunakan

  - **Backend:** PHP 8.1+, Laravel 10
  - **Frontend:** HTML5, CSS3, JavaScript (ES6+), Bootstrap 5 (via SB Admin 2)
  - **Database:** MySQL / MariaDB
  - **Fitur Frontend Lanjutan:** AJAX, Chart.js, SweetAlert2, Tom Select

## Prasyarat (Prerequisites)

Pastikan perangkat lunak berikut telah terinstal di lingkungan pengembangan Anda:

  - PHP 8.1 atau lebih baru
  - Composer
  - Node.js & NPM
  - Server database (MySQL atau MariaDB)

## Panduan Instalasi

Berikut adalah langkah-langkah untuk menginstal dan menjalankan proyek "Canteen":

1.  **Clone Repository**

    ```bash
    git clone https://github.com/haniffthur/Canteen.git
    cd Canteen
    ```

2.  **Instalasi Dependensi**
    Jalankan perintah berikut untuk menginstal dependensi PHP dan JavaScript.

    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Lingkungan**
    Salin file `.env.example` menjadi `.env`.

    ```bash
    cp .env.example .env
    ```

    Kemudian, buat kunci aplikasi baru.

    ```bash
    php artisan key:generate
    ```

4.  **Konfigurasi Database**
    Buka file `.env` dan atur koneksi database Anda.

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_anda
    DB_USERNAME=username_anda
    DB_PASSWORD=password_anda
    ```

5.  **Migrasi dan Seeding Database**
    Jalankan migrasi untuk membuat tabel database dan seeder untuk mengisi data awal.

    ```bash
    php artisan migrate --seed
    ```

6.  **Menjalankan Aplikasi**
    Jalankan server pengembangan Laravel.

    ```bash
    php artisan serve
    ```

    Aplikasi akan berjalan di `http://127.0.0.1:8000`.

### **Fitur-Fitur Secara Lengkap dan Rinci**

Aplikasi "Canteen" ini dirancang untuk menjadi solusi komprehensif dalam manajemen kantin perusahaan. Berikut adalah detail dari setiap fiturnya:

#### **1. Sistem Otentikasi dan Otorisasi Berbasis Peran (Role-Based)**
Sistem ini memastikan bahwa setiap pengguna hanya dapat mengakses fitur yang sesuai dengan hak aksesnya.

* **Admin:**
    * Memiliki hak akses penuh ke seluruh sistem.
    * Dapat mengelola semua data master (menu, karyawan, kartu, counter).
    * Dapat melihat seluruh laporan dan statistik di dashboard.
    * Mengelola akun pengguna lain (misalnya, membuat akun untuk HR).
* **HR (Human Resources):**
    * Memiliki hak akses yang lebih terbatas dibandingkan Admin.
    * Fokus pada pengelolaan data karyawan dan kartu.
    * Dapat melihat laporan yang relevan dengan data karyawan dan pengambilan makan.

#### **2. Dashboard Interaktif dan Real-time**
Dashboard adalah halaman utama yang memberikan gambaran umum tentang aktivitas kantin secara langsung.

* **Statistik Real-time:** Menampilkan data yang terus diperbarui, seperti:
    * Jumlah karyawan yang sudah mengambil makan hari ini.
    * Sisa porsi menu yang tersedia di setiap counter.
    * Grafik tren pengambilan makan (harian, mingguan, bulanan).
* **Visualisasi Data:** Menggunakan grafik (seperti diagram batang atau lingkaran dari Chart.js) untuk mempermudah pemahaman data.
* **Notifikasi Penting:** Dapat menampilkan informasi penting seperti stok menu yang akan habis.

#### **3. Manajemen Data Master (CRUD - Create, Read, Update, Delete)**
Ini adalah fitur inti untuk mengelola semua data yang dibutuhkan oleh sistem.

* **Manajemen Menu:**
    * Menambah, mengubah, dan menghapus menu makanan/minuman.
    * Mengatur harga dan kuantitas (stok) untuk setiap menu.
    * Menetapkan menu untuk hari atau tanggal tertentu.
* **Manajemen Karyawan:**
    * Mendaftarkan karyawan baru dan menonaktifkan karyawan yang sudah tidak bekerja.
    * Menyimpan data detail karyawan (nama, NIP, departemen, dll.).
    * Menghubungkan data karyawan dengan kartu RFID/NFC.
* **Manajemen Kartu:**
    * Mendaftarkan kartu RFID/NFC baru.
    * Mengaitkan (pairing) kartu dengan seorang karyawan.
    * Menonaktifkan kartu yang hilang atau rusak.
* **Manajemen Counter:**
    * Mendefinisikan lokasi counter pengambilan makan (misal: "Counter A", "Counter B").
    * Mengalokasikan menu dan stok ke masing-masing counter.

#### **4. Aturan Bisnis yang Kompleks (Complex Business Rules)**
Fitur ini memungkinkan sistem untuk menangani skenario operasional yang spesifik.

* **Validasi Menu Harian/Khusus:** Sistem dapat diatur agar menu tertentu hanya valid pada hari-hari tertentu (misalnya, "Menu Spesial Jumat" hanya bisa diambil pada hari Jumat).
* **Pembatasan Pengambilan:** Mengatur agar setiap karyawan hanya bisa mengambil jatah makan satu kali per hari.

#### **5. Fitur Operasional Real-time**
Fitur ini dirancang untuk menangani dinamika operasional kantin sehari-hari.

* **Pemindahan Stok Antar Counter:** Jika satu counter kehabisan stok menu tertentu sementara counter lain masih memiliki stok, admin dapat memindahkan stok tersebut melalui sistem. Perubahan ini akan langsung terlihat di antarmuka tapping.

#### **6. Endpoint API Publik**
API (Application Programming Interface) berfungsi sebagai jembatan antara perangkat tapping (misalnya, komputer dengan RFID reader di counter) dengan server utama.

* **`GET /api/menu/{counter_id}`:** Endpoint ini digunakan untuk mengambil daftar menu yang aktif dan tersedia di counter tertentu. Perangkat tapping akan memanggil API ini untuk menampilkan menu kepada petugas counter.
* **`POST /api/tap`:** Endpoint ini digunakan untuk memproses transaksi. Ketika seorang karyawan melakukan tap kartu, perangkat akan mengirimkan ID kartu ke API ini. Server kemudian akan memvalidasi kartu, memeriksa hak makan karyawan, mencatat transaksi, dan mengurangi stok menu.

---

### **Alur Kerja (Workflow) Secara Rinci dan Jelas**

Berikut adalah alur kerja sistem "Canteen" dari berbagai sudut pandang:

#### **A. Alur Kerja Admin/HR (Persiapan dan Manajemen)**

1.  **Login:** Admin atau HR masuk ke dalam sistem menggunakan email dan password mereka.
2.  **Manajemen Karyawan:**
    * HR mendaftarkan karyawan baru ke dalam sistem.
    * Setiap karyawan yang terdaftar akan dihubungkan dengan sebuah kartu RFID/NFC yang unik.
3.  **Manajemen Menu:**
    * Admin menyiapkan daftar menu yang akan disajikan untuk periode tertentu (misalnya, seminggu ke depan).
    * Admin memasukkan jumlah porsi (stok awal) untuk setiap menu.
4.  **Manajemen Counter:**
    * Admin mengalokasikan menu-menu yang sudah disiapkan ke counter-counter yang tersedia. Misalnya, "Nasi Goreng" dialokasikan ke "Counter A" sebanyak 100 porsi.

#### **B. Alur Kerja Karyawan (Pengambilan Makan)**

1.  **Datang ke Counter:** Karyawan datang ke salah satu counter kantin pada jam makan.
2.  **Memilih Menu:** Karyawan memilih menu yang diinginkan (jika ada beberapa pilihan di counter tersebut).
3.  **Tap Kartu:** Karyawan melakukan tap kartu RFID/NFC miliknya pada alat pembaca (RFID reader) yang tersedia di counter.
4.  **Validasi Sistem:**
    * Alat pembaca mengirimkan ID kartu ke server melalui API.
    * Server menerima ID kartu dan melakukan serangkaian validasi:
        * Apakah kartu ini terdaftar dan aktif?
        * Apakah kartu ini sudah terhubung dengan seorang karyawan?
        * Apakah karyawan ini sudah mengambil jatah makan hari ini?
        * Apakah menu yang dipilih masih tersedia (stok > 0)?
5.  **Hasil Transaksi:**
    * **Jika Berhasil:** Sistem akan memberikan notifikasi sukses (misalnya, lampu hijau atau pesan di layar). Transaksi dicatat, dan stok menu otomatis berkurang satu.
    * **Jika Gagal:** Sistem akan memberikan notifikasi gagal beserta alasannya (misalnya, "Jatah makan sudah diambil" atau "Kartu tidak terdaftar").

#### **C. Alur Kerja Sistem (Monitoring dan Pelaporan)**

1.  **Pencatatan Transaksi:** Setiap kali terjadi tap yang berhasil, sistem akan menyimpan data transaksi yang mencakup: ID karyawan, nama, menu yang diambil, counter, serta tanggal dan waktu.
2.  **Pembaruan Dashboard:** Data di dashboard Admin/HR akan diperbarui secara real-time. Mereka bisa langsung melihat berapa banyak karyawan yang sudah makan dan sisa porsi setiap menu.
3.  **Pembuatan Laporan:**
    * Di akhir hari atau periode tertentu, Admin/HR dapat menghasilkan laporan.
    * Laporan bisa berupa:
        * Laporan harian pengambilan makan.
        * Laporan rekapitulasi per karyawan atau per departemen.
        * Laporan popularitas menu (menu mana yang paling sering diambil).
    * Laporan ini dapat digunakan untuk analisis, perencanaan menu selanjutnya, atau keperluan audit.