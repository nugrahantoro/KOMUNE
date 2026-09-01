<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0 font-size-18 text-dark fw-semibold">Audit Log & Jejak Aktivitas</h4>
                            <p class="text-muted font-size-13 mb-0 mt-1">Audit trail seluruh operasi manipulasi data, perubahan iuran, posting konten, dan login pengurus.</p>
                        </div>
                        <div class="page-title-right mt-3 mt-sm-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="load_audit_logs();">
                                <i class="mdi mdi-refresh me-1"></i> Refresh Log
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card border shadow-sm">
                        <div class="card-body p-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label font-size-12 text-muted mb-1">Jenis Tindakan / Aksi</label>
                                    <select id="filter_log_aksi" class="form-select form-select-sm" onchange="load_audit_logs();">
                                        <option value="">Semua Tindakan</option>
                                        <option value="CREATE">CREATE (Tambah Data)</option>
                                        <option value="UPDATE">UPDATE (Ubah Data)</option>
                                        <option value="SOFT_DELETE">SOFT_DELETE (Hapus Arsip)</option>
                                        <option value="LOGIN">LOGIN</option>
                                        <option value="LOGOUT">LOGOUT</option>
                                        <option value="PAYMENT">PAYMENT (Transaksi)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-size-12 text-muted mb-1">Modul Terkait</label>
                                    <select id="filter_log_modul" class="form-select form-select-sm" onchange="load_audit_logs();">
                                        <option value="">Semua Modul</option>
                                        <option value="warga">Warga & Anggota</option>
                                        <option value="administrasi">Administrasi Iuran</option>
                                        <option value="kas">Kas Komunitas</option>
                                        <option value="artikel">Artikel Edukasi</option>
                                        <option value="video">Video Kajian</option>
                                        <option value="auth">Autentikasi & Akun</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-size-12 text-muted mb-1">Pencarian (User / Keterangan / IP)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="search_log" class="form-control" placeholder="Kata kunci pencarian..." onkeyup="if(event.key === 'Enter') load_audit_logs();">
                                        <button class="btn btn-outline-secondary" type="button" onclick="load_audit_logs();">
                                            <i class="mdi mdi-magnify"></i> Cari
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end pt-3">
                                    <button class="btn btn-light btn-sm text-secondary w-100" onclick="reset_filter_logs();">
                                        Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted font-size-12 text-uppercase">
                                        <tr>
                                            <th style="width: 60px;" class="text-center">ID</th>
                                            <th style="width: 130px;">Aksi</th>
                                            <th style="width: 110px;">Modul</th>
                                            <th>Pengguna</th>
                                            <th>Keterangan Aktivitas</th>
                                            <th>Alamat IP & Perangkat</th>
                                            <th style="width: 150px;">Waktu Kejadian</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_audit_logs" class="font-size-13">
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Memuat log aktivitas...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-2 px-3 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted font-size-12" id="info_total_logs">Menampilkan log terbaru</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="audit_log/js/j_audit_log.js?v=<?php echo uniqid(); ?>"></script>
<script>
$(document).ready(function() {
    load_audit_logs();
});
</script>
