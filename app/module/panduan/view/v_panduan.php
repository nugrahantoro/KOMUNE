<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Hero Banner -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #7f1d1d 0%, #0f172a 100%);">
                        <div class="card-body p-4 p-md-5">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <span class="badge bg-white text-danger font-size-12 px-3 py-1 mb-2 fw-semibold">Pusat Dokumentasi & Bantuan</span>
                                    <h3 class="text-white fw-bold mb-2">Panduan Penggunaan Sistem Komune</h3>
                                    <p class="text-white-50 font-size-14 mb-0">
                                        Petunjuk teknis dan panduan operasional lengkap untuk pengurus, bendahara, dan administrator dalam mengelola administrasi, keuangan, dompet digital, dan konten edukasi komunitas.
                                    </p>
                                </div>
                                <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                                    <div class="input-group">
                                        <input type="text" id="guide_search" class="form-control" placeholder="Cari topik panduan (misal: iuran, kas, artikel, warga)..." onkeyup="filter_guide_topics();">
                                        <button class="btn btn-primary" type="button">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Topic Navigation -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card border shadow-sm">
                        <div class="card-body p-2">
                            <ul class="nav nav-pills nav-justified font-size-13" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active py-2" id="tab-quickstart" data-bs-toggle="pill" data-bs-target="#content-quickstart" type="button">
                                        <i class="mdi mdi-rocket-launch-outline me-1"></i> Alur Cepat
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-2" id="tab-warga" data-bs-toggle="pill" data-bs-target="#content-warga" type="button">
                                        <i class="mdi mdi-account-group-outline me-1"></i> Data Warga & RT/RW
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-2" id="tab-iuran" data-bs-toggle="pill" data-bs-target="#content-iuran" type="button">
                                        <i class="mdi mdi-credit-card-outline me-1"></i> Iuran & Kasir
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-2" id="tab-kas" data-bs-toggle="pill" data-bs-target="#content-kas" type="button">
                                        <i class="mdi mdi-cash-multiple me-1"></i> Kas Komunitas
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-2" id="tab-dompet" data-bs-toggle="pill" data-bs-target="#content-dompet" type="button">
                                        <i class="mdi mdi-wallet-outline me-1"></i> Dompet Digital
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-2" id="tab-konten" data-bs-toggle="pill" data-bs-target="#content-konten" type="button">
                                        <i class="mdi mdi-book-open-page-variant-outline me-1"></i> Konten & Edukasi
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link py-2" id="tab-audit" data-bs-toggle="pill" data-bs-target="#content-audit" type="button">
                                        <i class="mdi mdi-shield-check-outline me-1"></i> Keamanan & Audit
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guide Content Panes -->
            <div class="tab-content mt-2" id="pills-tabContent">

                <!-- 1. QUICKSTART -->
                <div class="tab-pane fade show active" id="content-quickstart" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border shadow-sm h-100 guide-card">
                                <div class="card-body p-4">
                                    <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mb-3">1</div>
                                    <h5 class="fw-bold text-dark font-size-15">Langkah 1: Setup Wilayah & Warga</h5>
                                    <p class="text-muted font-size-13">
                                        Mulai dengan mendaftarkan struktur RT/RW pada menu <b>Kelompok & RT/RW</b>, kemudian tambahkan data warga pada menu <b>Data Warga & Anggota</b>.
                                    </p>
                                    <a href="warga" class="btn btn-outline-primary btn-sm">Buka Data Warga &rarr;</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border shadow-sm h-100 guide-card">
                                <div class="card-body p-4">
                                    <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mb-3">2</div>
                                    <h5 class="fw-bold text-dark font-size-15">Langkah 2: Konfigurasi Tarif Iuran</h5>
                                    <p class="text-muted font-size-13">
                                        Tentukan jenis-jenis iuran komunitas (Iuran Keamanan, Kebersihan, Dana Sosial) pada menu <b>Tarif & Pos Iuran</b> dan tetapkan jadwal tagihan.
                                    </p>
                                    <a href="konfig_tu" class="btn btn-outline-primary btn-sm">Buka Tarif Iuran &rarr;</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border shadow-sm h-100 guide-card">
                                <div class="card-body p-4">
                                    <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold mb-3">3</div>
                                    <h5 class="fw-bold text-dark font-size-15">Langkah 3: Publikasi Edukasi & Feed</h5>
                                    <p class="text-muted font-size-13">
                                        Kontributor dapat mengunggah artikel bacaan dan embed video edukasi agar warga dapat langsung mengaksesnya di feed aplikasi komunitas.
                                    </p>
                                    <a href="konten" class="btn btn-outline-primary btn-sm">Buka Pusat Konten &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. WARGA & RT/RW -->
                <div class="tab-pane fade" id="content-warga" role="tabpanel">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title font-size-16 mb-0 fw-semibold text-dark">Panduan Modul Warga, Kelompok & Wilayah</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">1. Menambah & Mengelola Warga</h6>
                                    <ol class="text-muted font-size-13 ps-3" style="line-height: 1.8;">
                                        <li>Buka menu <b>Warga & Wilayah &rarr; Data Warga & Anggota</b>.</li>
                                        <li>Klik tombol <b>+ Tambah Warga</b> di pojok kanan atas.</li>
                                        <li>Isi Nomor Anggota / NIK, Nama Lengkap, Jenis Kelamin, Nama Kepala Keluarga, Kelompok RT/RW, dan No. Telepon/WhatsApp.</li>
                                        <li>Klik <b>Simpan</b>. Data langsung tersinkronisasi ke seluruh sistem iuran dan dompet digital.</li>
                                    </ol>
                                </div>
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">2. Manajemen Kelompok & RT/RW</h6>
                                    <p class="text-muted font-size-13" style="line-height: 1.7;">
                                        Pengurus dapat menambah atau mengedit <b>Wilayah Utama (RW / Zona)</b> serta <b>Kelompok Warga (RT / Unit)</b> secara mandiri melalui menu <b>Kelompok & RT/RW</b> dengan tombol aksi tambah/edit/hapus yang tersedia.
                                    </p>
                                    <div class="alert alert-light border font-size-12 text-muted mb-0">
                                        <i class="mdi mdi-shield-check-outline text-danger me-1"></i> Seluruh penghapusan warga menggunakan mekanisme <b>Soft Delete</b> untuk menjaga riwayat transaksi masa lalu tetap aman.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. IURAN & KASIR -->
                <div class="tab-pane fade" id="content-iuran" role="tabpanel">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title font-size-16 mb-0 fw-semibold text-dark">Panduan Loket Iuran & Pembayaran</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">1. Menerima Pembayaran di Loket / Kasir</h6>
                                    <ol class="text-muted font-size-13 ps-3" style="line-height: 1.8;">
                                        <li>Buka menu <b>Administrasi Iuran &rarr; Loket Pembayaran Iuran</b>.</li>
                                        <li>Cari warga berdasarkan Nama atau Nomor Anggota.</li>
                                        <li>Centang tagihan yang akan dibayar oleh warga.</li>
                                        <li>Pilih metode pembayaran (Tunai, Transfer, atau Dompet Digital).</li>
                                        <li>Klik <b>Bayar Sekarang</b> untuk memproses transaksi.</li>
                                    </ol>
                                </div>
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">2. Bukti Kuitansi & Konfirmasi Otomatis</h6>
                                    <p class="text-muted font-size-13" style="line-height: 1.7;">
                                        Setiap transaksi yang berhasil:
                                    </p>
                                    <ul class="text-muted font-size-13 ps-3" style="line-height: 1.8;">
                                        <li>Menghasilkan format Kuitansi Digital yang siap dicetak.</li>
                                        <li>Mengirimkan notifikasi WhatsApp otomatis kepada warga.</li>
                                        <li>Mencatat mutasi jurnal kas secara real-time.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. KAS KOMUNITAS -->
                <div class="tab-pane fade" id="content-kas" role="tabpanel">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title font-size-16 mb-0 fw-semibold text-dark">Panduan Pengelolaan Buku Kas & Arus Kas</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">1. Pencatatan Kas Masuk & Kas Keluar</h6>
                                    <p class="text-muted font-size-13" style="line-height: 1.7;">
                                        Bendahara dapat mencatat pengeluaran operasional (seperti perbaikan fasilitas, honor kebersihan, bantuan sosial) pada menu <b>Kas Komunitas &rarr; Buku Kas Masuk & Keluar</b>.
                                    </p>
                                </div>
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">2. Laporan Arus Kas & Ekspor Excel</h6>
                                    <p class="text-muted font-size-13" style="line-height: 1.7;">
                                        Laporan arus kas bulanan dapat diakses dan diekspor ke format Excel atau PDF secara transparan untuk rapat pertanggungjawaban warga.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. DOMPET DIGITAL -->
                <div class="tab-pane fade" id="content-dompet" role="tabpanel">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title font-size-16 mb-0 fw-semibold text-dark">Panduan Dompet Digital Warga</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">1. Fungsi Dompet Digital</h6>
                                    <p class="text-muted font-size-13" style="line-height: 1.7;">
                                        Dompet Digital memungkinkan warga menyimpan saldo pra-bayar yang dapat digunakan untuk pembayaran iuran otomatis, santunan warga, atau pembelanjaan di unit usaha komunitas.
                                    </p>
                                </div>
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">2. Top-Up & Monitoring Saldo</h6>
                                    <p class="text-muted font-size-13" style="line-height: 1.7;">
                                        Pengurus dapat memantau perputaran saldo pada menu <b>Dompet Digital &rarr; Saldo Dompet Warga</b> dan memproses transaksi pengisian atau penarikan saldo.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. KONTEN & EDUKASI -->
                <div class="tab-pane fade" id="content-konten" role="tabpanel">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title font-size-16 mb-0 fw-semibold text-dark">Panduan Publikasi Artikel & Video Edukasi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">1. Menulis Artikel & Bacaan</h6>
                                    <ol class="text-muted font-size-13 ps-3" style="line-height: 1.8;">
                                        <li>Buka menu <b>Konten & Edukasi &rarr; Artikel & Bacaan</b>.</li>
                                        <li>Klik tombol <b>Tulis Artikel Baru</b>.</li>
                                        <li>Pilih kategori (Edukasi, Warta Komunitas, Wawasan, Pengumuman).</li>
                                        <li>Tuliskan judul, nama penulis, ringkasan, dan isi lengkap artikel.</li>
                                        <li>Pilih status <b>Diterbitkan (Published)</b> agar artikel langsung tampil di Feed Warga.</li>
                                    </ol>
                                </div>
                                <div class="col-lg-6">
                                    <h6 class="fw-bold text-dark mb-2">2. Embed Video Edukasi (YouTube)</h6>
                                    <ol class="text-muted font-size-13 ps-3" style="line-height: 1.8;">
                                        <li>Buka menu <b>Konten & Edukasi &rarr; Video Edukasi</b>.</li>
                                        <li>Klik tombol <b>Tambah Video Baru</b>.</li>
                                        <li>Tempelkan tautan video YouTube.</li>
                                        <li>Sistem otomatis mengekstrak ID video dan menampilkan preview pemutar video responsif.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 7. AUDIT & KEAMANAN -->
                <div class="tab-pane fade" id="content-audit" role="tabpanel">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title font-size-16 mb-0 fw-semibold text-dark">Panduan Keamanan & Audit Log Aktivitas</h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted font-size-13" style="line-height: 1.7;">
                                Seluruh interaksi sensitif yang terjadi di sistem dicatat secara otomatis pada menu <b>Audit Log Aktivitas</b>:
                            </p>
                            <div class="table-responsive mt-2">
                                <table class="table table-sm table-bordered font-size-13">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Aksi Terlacak</th>
                                            <th>Keterangan yang Direkam</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-light text-success border">CREATE</span></td>
                                            <td>Penambahan warga baru, pembuatan tagihan iuran, posting artikel baru, tambah user.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-light text-primary border">UPDATE</span></td>
                                            <td>Perubahan data profil, edit artikel, perubahan kata sandi, koreksi tarif.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-light text-danger border">SOFT_DELETE</span></td>
                                            <td>Penghapusan/pengarsipan warga, pembatalan transaksi, hapus artikel atau video.</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-light text-info border">LOGIN / LOGOUT</span></td>
                                            <td>Waktu masuk/keluar akun pengurus beserta alamat IP dan peramban yang digunakan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
function filter_guide_topics() {
    let query = document.getElementById('guide_search').value.toLowerCase();
    let cards = document.querySelectorAll('.guide-card, .tab-pane');

    cards.forEach(function(card) {
        let text = card.textContent.toLowerCase();
        if (text.includes(query)) {
            card.style.display = '';
        } else if (query.trim() !== '' && card.classList.contains('guide-card')) {
            card.style.display = 'none';
        }
    });
}
</script>
