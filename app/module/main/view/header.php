<!DOCTYPE html>
<html lang="id">
<head>
	<base href="<?php global $basepath; echo $basepath; ?>" target="_self">
    <meta charset="utf-8" />
    <title><?php echo Security::e($data['title'] ?? 'Beranda'); ?> - Komune</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Platform Digital Administrasi & Keuangan Komunitas" name="description" />
    <meta content="Komune" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="images/favicon.ico">

    <!-- Preloader css -->
    <link rel="stylesheet" href="css/preloader.min.css" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" /> 
	<!-- Alertifyjs Css -->
    <link href="libs/alertifyjs/build/css/alertify.min.css" rel="stylesheet" type="text/css" />
    <link href="libs/alertifyjs/build/css/themes/default.min.css" rel="stylesheet" type="text/css" />
	
	<link rel="stylesheet" href="libs/flatpickr/flatpickr.min.css">
	
    <!-- Icons Css -->
    <link href="css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="css/app.css" id="app-style" rel="stylesheet" type="text/css" />
    <link href="css/custom.css?v=<?php echo uniqid() ?>" rel="stylesheet" type="text/css" />

    <!-- Choices css -->
    <link href="libs/choices.js/public/assets/styles/choices.min.css" rel="stylesheet" type="text/css" />
    <link href="libs/choices.js/public/assets/styles/chol.min.css" rel="stylesheet" type="text/css" />
    
    <!-- Select2 Css -->
    <link href="css/select2.min.css" rel="stylesheet" type="text/css" /> 
	
    <script src="libs/jquery/jquery.min.js"></script>
</head>
	
<body>
	<div id="div_load_full" style="background-color: rgba(0, 0, 0, 0.2); position: fixed; z-index: 9999; text-align: center; width: 100vw; height: 100vh; padding-top: 45vh; display: none;">
		<div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">Memuat...</span>
        </div>		
	</div>

    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex align-items-center">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="" class="logo logo-dark d-flex align-items-center">
                            <span class="logo-sm">
                                <span class="fw-bold font-size-18" style="color: #e52c3b;">K</span>
                            </span>
                            <span class="logo-lg d-flex align-items-center">
                                <img src="images/komune.png" alt="Komune Logo" style="height: 28px; object-fit: contain; margin-right: 8px;" onerror="this.style.display='none';">
                                <div>
                                    <span class="fw-bold font-size-18" style="color: #e52c3b;"><?php echo KOMUNE_NAME; ?></span>
                                    <small class="text-muted font-size-11 d-block" style="line-height: 1;"><?php echo KOMUNE_NAME_SHORT; ?></small>
                                </div>
                            </span>
                        </a>

                        <a href="" class="logo logo-light">
                            <span class="logo-sm">
                                <span class="fw-bold font-size-18 text-white">K</span>
                            </span>
                            <span class="logo-lg">
                                <span class="fw-bold font-size-18 text-white"><?php echo KOMUNE_NAME; ?></span>
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                </div>

                <div class="d-flex align-items-center">
                    <a href="panduan" class="btn btn-sm btn-outline-danger d-none d-md-inline-flex align-items-center me-3" title="Panduan Penggunaan Sistem">
                        <i class="mdi mdi-help-circle-outline me-1"></i> Panduan Sistem
                    </a>

                    <div class="dropdown d-none d-sm-inline-block me-2">
                        <button type="button" class="btn header-item" id="mode-setting-btn">
                            <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                            <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="images/users/avatar-1.jpg" alt="Avatar">
							<?php
								$nama = $_SESSION['nama_us'] ?? 'Pengurus';
								$arrNama = explode(' ', $nama);
								$nama_us = $arrNama[0];
								if(count($arrNama) > 1) {
									$nama_us .= ' ' . substr($arrNama[1], 0, 1) . '.';
								}
								$nama_us = ucwords($nama_us);
							?>
                            <span class="d-none d-xl-inline-block ms-1 fw-medium"><?php echo Security::e($nama_us); ?></span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0">
                            <a class="dropdown-item" href="javascript:void(0);" onclick="edit_user();"><i class="mdi mdi-account-cog-outline font-size-16 align-middle me-1"></i> Edit Profil</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="login/logout"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Keluar (Logout)</a>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">Utama</li>

                        <li>
                            <a href="">
                                <i data-feather="home"></i>
                                <span>Beranda</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="users"></i>
                                <span>Warga & Wilayah</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="warga">Data Warga & Anggota</a></li>
                                <li><a href="warga/kelompok">Kelompok & RT/RW</a></li>
                            </ul>
                        </li>

                        <li class="menu-title">Keuangan & Iuran</li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="credit-card"></i>
                                <span>Administrasi Iuran</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="konfig_tu/transaksi_bayar">Loket Pembayaran Iuran</a></li>
                                <li><a href="konfig_tu">Tarif & Pos Iuran</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="dollar-sign"></i>
                                <span>Kas Komunitas</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="kasv2">Buku Kas Masuk & Keluar</a></li>
                                <li><a href="kasv2/lap_kas">Laporan Arus Kas</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="pocket"></i>
                                <span>Dompet Digital</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="konfig_tu/transaksi_emoney">Transaksi Dompet Digital</a></li>
                                <li><a href="saldo_uangsaku">Saldo Dompet Digital</a></li>
                            </ul>
                        </li>

                        <li class="menu-title">Komunitas Digital</li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="globe"></i>
                                <span>Komunitas Digital</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="mobileapp/agenda">Agenda Warga</a></li>
                                <li><a href="mobileapp">Informasi & Pengumuman</a></li>
                                <li><a href="konten/artikel">Artikel & Bacaan</a></li>
                                <li><a href="konten/video">Video Edukasi</a></li>
                                <li><a href="konten/feed">Feed Informasi Warga</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="file-text"></i>
                                <span>Laporan & Rekap</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="laporan_rekap_tagihan">Rekap Tagihan & Tunggakan</a></li>
                            </ul>
                        </li>

                        <li class="menu-title">Sistem & Bantuan</li>

                        <li>
                            <a href="audit_log">
                                <i data-feather="activity"></i>
                                <span>Audit Log Aktivitas</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow">
                                <i data-feather="settings"></i>
                                <span>Pengaturan Sistem</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="sistem/manajemen_user">Manajemen User Pengurus</a></li>
                                <li><a href="sistem/ta_jenjang">Periode & Kategori Wilayah</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="panduan">
                                <i data-feather="help-circle"></i>
                                <span>Panduan Penggunaan</span>
                            </a>
                        </li>

                        <li>
                            <a href="login/logout">
                                <i data-feather="log-out"></i>
                                <span>Keluar (Logout)</span>
                            </a>
                        </li>

                    </ul>

                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

<div class="modal fade" id="modal_edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-size-16 fw-semibold" id="exampleModalLabel">Ubah Kata Sandi Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label font-size-13 text-muted">Nama Pengguna</label>
                    <input type="text" class="form-control form-control-sm" value="<?php echo Security::e($nama_us); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label font-size-13 text-muted">Kata Sandi Lama</label>
                    <input type="password" class="form-control form-control-sm" id="pass_lama" placeholder="Tuliskan kata sandi lama">
                </div>
                <div class="mb-3">
                    <label class="form-label font-size-13 text-muted">Kata Sandi Baru</label>
                    <input type="password" class="form-control form-control-sm" id="pass_baru" placeholder="Tuliskan kata sandi baru (minimal 6 karakter)">
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="edit_user_baru();">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<script>
var href = '<?php echo Security::e($data['href'] ?? ''); ?>';
</script>
<script src="main/js/j_edit.js?v=<?php echo uniqid(); ?>"></script>