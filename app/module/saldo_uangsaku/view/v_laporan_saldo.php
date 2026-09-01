<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0 font-size-18 text-dark fw-semibold">Saldo Dompet Digital Warga</h4>
                            <p class="text-muted font-size-13 mb-0 mt-1">Monitoring posisi saldo pra-bayar dan mutasi dompet digital seluruh anggota komunitas.</p>
                        </div>
                        <div class="page-title-right mt-3 mt-sm-0">
                            <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="flag_cari();">
                                <i class="bx bx-search-alt me-1"></i> Cari Berdasarkan Warga
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Row -->
            <div class="row mt-2">
                <!-- Filter Panel -->
                <div class="col-xl-3 col-lg-4">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title font-size-14 mb-0 fw-semibold text-dark">
                                <i class="mdi mdi-filter-outline me-1 text-danger"></i> Filter Data Saldo
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <div id="tampil_thn"></div>
                            <div id="tam_kelas"></div>

                            <div class="mb-3">
                                <label class="form-label font-size-13 text-muted mb-1">Sampai Tanggal</label>
                                <input type="text" class="form-control form-control-sm" id="sampai_tgl" placeholder="Pilih tanggal">
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-primary btn-sm" onclick="flag();" type="button">
                                    <i class="bx bx-check-circle me-1"></i> Tampilkan Data
                                </button>
                                <button class="btn btn-light btn-sm text-secondary" onclick="reset_saldo_filter();" type="button">
                                    Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Panel -->
                <div class="col-xl-9 col-lg-8">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-size-14 mb-0 fw-semibold text-dark">
                                <i class="bx bx-wallet text-danger me-1"></i> Rekapitulasi Saldo Dompet Digital Warga
                            </h5>
                            <span id="selected_warga_badge" class="badge bg-light text-danger border" style="display:none;"></span>
                        </div>
                        <div class="card-body p-0">
                            <div id="tampil_masuk"></div>
                            <div id="tampil_default"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Pencarian Warga -->
<div class="modal fade" id="tag_cari" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-size-15 fw-semibold text-dark"><i class="bx bx-search me-1 text-danger"></i> Cari Data Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-3">
                    <input id="edValue" type="text" class="form-control form-control-sm" placeholder="Ketik nama lengkap warga..." oninput="edValueKeyPress();">
                </div>
                <div id="tampil_cari" style="max-height: 250px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rincian Mutasi Warga -->
<div class="modal fade" id="tam_saldi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-size-15 fw-semibold text-dark"><i class="bx bx-history text-danger me-1"></i> Rincian Mutasi Dompet Digital</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div id="tam_sald"></div>
            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="saldo_uangsaku/js/j_saldo_uangsaku.js?v=<?php echo uniqid() ?>"></script>
