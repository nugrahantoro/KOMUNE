    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <!-- Page Header Title & Period Filter -->
                <div class="row align-items-center mb-3">
                    <div class="col-md-6">
                        <h4 class="mb-1 font-size-20 text-dark fw-bold" style="letter-spacing: -0.02em;">Dashboard Eksekutif Komune</h4>
                        <p class="text-muted font-size-13 mb-0">Ringkasan kesehatan finansial, penerimaan iuran, arus kas, dan aktivitas warga secara real-time.</p>
                    </div>

                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-2">
                            <div class="d-flex align-items-center bg-white px-3 py-1 rounded-3 border shadow-sm">
                                <label class="font-size-12 text-muted me-2 mb-0 fw-medium text-uppercase">Periode:</label>
                                <select id='sel_th' class='form-select form-select-sm border-0 py-0 ps-1 pe-4 fw-semibold text-danger' style="background-position: right 0 center;">
                                    <?php
                                    $id_user = $_SESSION['id_user'] ?? $_SESSION['id'] ?? '';
                                    $querykls = "SELECT * FROM data_tahunajar ORDER BY id_tahunajar DESC";
                                    $result = db_query($querykls);
                                    if ($result) {
                                        while($dt = mysqli_fetch_array($result)){
                                            $sel = ($dt['beranda'] == '1') ? 'selected' : '';
                                            echo '<option value="'.Security::e($dt['id_tahunajar']).'" '.$sel.'>'.Security::e($dt['nama_tahunajar']).'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="d-flex align-items-center bg-white px-3 py-1 rounded-3 border shadow-sm">
                                <label class="font-size-12 text-muted me-2 mb-0 fw-medium text-uppercase">Wilayah:</label>
                                <select id='sel_jenjang' class='form-select form-select-sm border-0 py-0 ps-1 pe-4 fw-semibold text-danger' style="background-position: right 0 center;">
                                    <?php
                                        $queryJenjang = "SELECT DISTINCT dj.* FROM data_jenjang dj ORDER BY dj.id_jenjang ASC";
                                        $resultJenjang = db_query($queryJenjang);
                                        if ($resultJenjang) {
                                            while($dt = mysqli_fetch_array($resultJenjang)){
                                                echo '<option value="'.Security::e($dt['id_jenjang']).'">'.Security::e($dt['nama_jenjang']).'</option>';
                                            }
                                        }
                                    ?>	
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4 Executive KPI Metric Cards -->
                <div class="row g-3 mb-4">
                    <!-- Total Warga -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card border shadow-sm h-100 kpi-card">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted font-size-12 fw-semibold text-uppercase mb-1" style="letter-spacing: 0.05em;">Total Warga Aktif</p>
                                        <?php
                                        $cntWarga = db_assoc("SELECT COUNT(*) as total FROM data_siswa");
                                        $totalWarga = $cntWarga['total'] ?? 0;
                                        ?>
                                        <h3 class="fw-bold text-dark mb-0 font-size-22"><?php echo number_format((int)$totalWarga, 0, ',', '.'); ?> <span class="font-size-13 text-muted fw-normal">Jiwa</span></h3>
                                    </div>
                                    <div class="avatar-sm bg-light text-danger rounded-3 d-flex align-items-center justify-content-center border">
                                        <i class="mdi mdi-account-group font-size-22"></i>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between font-size-12">
                                    <span class="text-muted">Terdaftar di komunitas</span>
                                    <a href="warga" class="text-danger fw-medium text-decoration-none">Kelola Warga &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Penerimaan Iuran Bulan Ini -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card border shadow-sm h-100 kpi-card">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted font-size-12 fw-semibold text-uppercase mb-1" style="letter-spacing: 0.05em;">Penerimaan Iuran Bulan Ini</p>
                                        <?php
                                        $curBulan = date('Y-m');
                                        $cntIuran = db_assoc("SELECT SUM(nominal_dibayarkan) as total FROM transaksi_siswa WHERE LEFT(tgl, 7) = '$curBulan'");
                                        $totalIuran = $cntIuran['total'] ?? 0;
                                        ?>
                                        <h3 class="fw-bold text-success mb-0 font-size-22">Rp <?php echo number_format((float)$totalIuran, 0, ',', '.'); ?></h3>
                                    </div>
                                    <div class="avatar-sm bg-light text-success rounded-3 d-flex align-items-center justify-content-center border">
                                        <i class="mdi mdi-credit-card-check-outline font-size-22"></i>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between font-size-12">
                                    <span class="text-muted">Periode <?php echo date('M Y'); ?></span>
                                    <a href="konfig_tu/transaksi_bayar" class="text-success fw-medium text-decoration-none">Loket Kasir &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kas Masuk & Kas Komunitas -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card border shadow-sm h-100 kpi-card">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted font-size-12 fw-semibold text-uppercase mb-1" style="letter-spacing: 0.05em;">Saldo Kas Komunitas</p>
                                        <?php
                                        $saldoKas = 0;
                                        $cntKas = db_assoc("SELECT SUM(debit) - SUM(kredit) as saldo FROM akuntan_tbl_jurnal_transaksi");
                                        if ($cntKas && isset($cntKas['saldo']) && $cntKas['saldo'] !== null) {
                                            $saldoKas = (float)$cntKas['saldo'];
                                        } else {
                                            $cntKas2 = db_assoc("SELECT SUM(nominal) as saldo FROM tbl_kas");
                                            if ($cntKas2 && isset($cntKas2['saldo'])) {
                                                $saldoKas = (float)$cntKas2['saldo'];
                                            }
                                        }
                                        ?>
                                        <h3 class="fw-bold text-dark mb-0 font-size-22">Rp <?php echo number_format($saldoKas, 0, ',', '.'); ?></h3>
                                    </div>
                                    <div class="avatar-sm bg-light text-info rounded-3 d-flex align-items-center justify-content-center border">
                                        <i class="mdi mdi-cash-multiple font-size-22"></i>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between font-size-12">
                                    <span class="text-muted">Saldo buku kas aktif</span>
                                    <a href="kasv2" class="text-info fw-medium text-decoration-none">Buku Kas &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dompet Saku Warga / Konten Edukasi -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card border shadow-sm h-100 kpi-card">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <p class="text-muted font-size-12 fw-semibold text-uppercase mb-1" style="letter-spacing: 0.05em;">Konten Edukasi & Media</p>
                                        <?php
                                        $totArt = 0;
                                        $totVid = 0;
                                        $cntArt = db_assoc("SELECT COUNT(*) as total FROM tbl_artikel");
                                        if ($cntArt && isset($cntArt['total'])) {
                                            $totArt = (int)$cntArt['total'];
                                        }
                                        $cntVid = db_assoc("SELECT COUNT(*) as total FROM tbl_video");
                                        if ($cntVid && isset($cntVid['total'])) {
                                            $totVid = (int)$cntVid['total'];
                                        }
                                        ?>
                                        <h3 class="fw-bold text-dark mb-0 font-size-22"><?php echo $totArt; ?> <span class="font-size-13 text-muted fw-normal">Artikel /</span> <?php echo $totVid; ?> <span class="font-size-13 text-muted fw-normal">Video</span></h3>
                                    </div>
                                    <div class="avatar-sm bg-light text-warning rounded-3 d-flex align-items-center justify-content-center border">
                                        <i class="mdi mdi-book-open-page-variant font-size-22"></i>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between font-size-12">
                                    <span class="text-muted">Konten edukasi aktif</span>
                                    <a href="konten/feed" class="text-warning fw-medium text-decoration-none">Buka Feed &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Bar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border shadow-sm bg-white">
                            <div class="card-body p-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <span class="fw-semibold text-dark font-size-13"><i class="mdi mdi-flash-outline text-warning me-1"></i> Aksi Cepat Pengurus:</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="konfig_tu/transaksi_bayar" class="btn btn-primary btn-sm px-3 shadow-sm">
                                            <i class="mdi mdi-plus me-1"></i> Terima Pembayaran Iuran
                                        </a>
                                        <a href="kasv2" class="btn btn-light btn-sm px-3 border">
                                            <i class="mdi mdi-cash-plus me-1"></i> Catat Kas Operasional
                                        </a>
                                        <a href="warga" class="btn btn-light btn-sm px-3 border">
                                            <i class="mdi mdi-account-plus-outline me-1"></i> Daftarkan Warga Baru
                                        </a>
                                        <a href="konten/artikel" class="btn btn-light btn-sm px-3 border">
                                            <i class="mdi mdi-pencil-outline me-1"></i> Tulis Artikel
                                        </a>
                                        <a href="konten/video" class="btn btn-light btn-sm px-3 border">
                                            <i class="mdi mdi-video-outline me-1"></i> Tambah Video
                                        </a>
                                        <a href="panduan" class="btn btn-outline-secondary btn-sm px-3">
                                            <i class="mdi mdi-help-circle-outline me-1"></i> Panduan Sistem
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Transaction Table & Real-time Feeds -->
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title font-size-15 mb-0 fw-semibold text-dark">Ringkasan Pembayaran Online & Virtual Account</h5>
                                    <small class="text-muted font-size-12">Transaksi iuran yang terverifikasi otomatis melalui BNI e-Collection dan QRIS.</small>
                                </div>
                                <div class="mt-2 mt-sm-0">
                                    <span id='data_va_tgl' for='div_data_va' class='select_ta'></span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row align-items-center" id='par_div_data_va'>
                                    <div id='div_data_va' class="col-sm align-self-center elem_load"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                            
                <div class="row mt-2">
                    <div class="col-xl-12">
                        <div class="card border shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title font-size-15 mb-0 fw-semibold text-dark">Ringkasan Dompet Digital & Uang Saku Warga</h5>
                                    <small class="text-muted font-size-12">Monitoring pengisian saldo dan perputaran uang saku warga di lingkungan komunitas.</small>
                                </div>
                                <div class="mt-2 mt-sm-0">
                                    <span id='data_saku_tgl' for='div_data_saku' class='select_ta'></span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row align-items-center" id='par_div_data_saku'>
                                    <div id='div_data_saku' class="col-sm align-self-center elem_load"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                        
            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->
    </div>

    <script src="main/js/main.js?v=<?php echo uniqid() ?>"></script>