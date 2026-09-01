<!DOCTYPE html>
<html lang="id">
<head>
    <base href="<?php global $basepath; echo $basepath; ?>" target="_self">
    <meta charset="utf-8" />
    <title>Masuk | Komune - Platform Digital Komunitas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Platform Digital Administrasi, Keuangan, dan Edukasi Komunitas Terpadu" name="description" />
    <meta content="Komune" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- Alertifyjs Css -->
    <link href="libs/alertifyjs/build/css/alertify.min.css" rel="stylesheet" type="text/css" />
    <link href="libs/alertifyjs/build/css/themes/default.min.css" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --brand-primary: #e52c3b;
            --brand-primary-hover: #cc1f2e;
            --brand-dark: #0f172a;
            --brand-surface: #ffffff;
            --brand-bg: #f8fafc;
            --brand-border: #e2e8f0;
            --brand-muted: #64748b;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: var(--brand-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 1040px;
            background: var(--brand-surface);
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.06), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            border: 1px solid var(--brand-border);
            overflow: hidden;
        }

        .auth-sidebar {
            background: linear-gradient(145deg, #18090b 0%, #1e293b 100%);
            color: #ffffff;
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .auth-sidebar::after {
            content: "";
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(229, 44, 59, 0.22) 0%, rgba(229, 44, 59, 0) 70%);
            border-radius: 50%;
        }

        .auth-form-side {
            padding: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            background: rgba(229, 44, 59, 0.15);
            border: 1px solid rgba(229, 44, 59, 0.3);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: #fca5a5;
            margin-bottom: 20px;
            width: fit-content;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .feature-icon {
            width: 36px;
            height: 36px;
            background: rgba(229, 44, 59, 0.12);
            border: 1px solid rgba(229, 44, 59, 0.25);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 14px;
            flex-shrink: 0;
            color: #f87171;
            font-size: 18px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1px solid var(--brand-border);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #f87171;
            box-shadow: 0 0 0 3px rgba(229, 44, 59, 0.15);
        }

        .input-group-text {
            background-color: transparent;
            border-color: var(--brand-border);
            border-radius: 0 8px 8px 0;
            cursor: pointer;
        }

        .btn-brand {
            background-color: var(--brand-primary);
            color: #ffffff;
            font-weight: 600;
            font-size: 14px;
            padding: 11px 20px;
            border-radius: 8px;
            border: none;
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-brand:hover {
            background-color: var(--brand-primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(229, 44, 59, 0.28);
        }

        .btn-brand:active {
            transform: translateY(0);
        }

        @media (max-width: 991px) {
            .auth-sidebar {
                padding: 32px;
            }
            .auth-form-side {
                padding: 32px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="row g-0">
            <!-- Sidebar / Left Brand Section -->
            <div class="col-lg-5 auth-sidebar d-none d-lg-flex">
                <div>
                    <div class="brand-badge">
                        <i class="mdi mdi-shield-check-outline me-1"></i> Sistem Administrasi Komunitas
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <img src="images/komune.png" alt="Logo Komune" style="height: 48px; object-fit: contain; margin-right: 12px;" onerror="this.style.display='none';">
                        <h2 class="text-white fw-bold mb-0" style="letter-spacing: -0.02em;">Komune.</h2>
                    </div>
                    <p class="text-white-50 font-size-14" style="line-height: 1.6;">
                        Platform terintegrasi untuk tata kelola iuran warga, transparansi kas komunitas, tabungan, serta pusat edukasi & kajian keagamaan.
                    </p>
                </div>

                <div class="my-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="mdi mdi-credit-card-check-outline"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-1 font-size-14 font-weight-600">Iuran Digital & Otomatis</h6>
                            <p class="text-white-50 font-size-12 mb-0">Pembayaran iuran via Virtual Account dan QRIS tercatat real-time.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="mdi mdi-book-open-page-variant-outline"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-1 font-size-14 font-weight-600">Pusat Edukasi & Kajian</h6>
                            <p class="text-white-50 font-size-12 mb-0">Artikel bacaan dan video kajian ustadz terintegrasi untuk seluruh warga.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="mdi mdi-history"></i>
                        </div>
                        <div>
                            <h6 class="text-white mb-1 font-size-14 font-weight-600">Transparansi & Audit Trail</h6>
                            <p class="text-white-50 font-size-12 mb-0">Seluruh mutasi dana dan aktivitas pengguna tercatat aman.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-top border-secondary border-opacity-25">
                    <small class="text-white-50 font-size-12">© <?php echo date('Y'); ?> Komune Platform. Versi 2.0</small>
                </div>
            </div>

            <!-- Form / Right Section -->
            <div class="col-lg-7 auth-form-side">
                <div class="w-100" style="max-width: 440px; margin: 0 auto;">
                    <!-- Brand for mobile -->
                    <div class="d-lg-none mb-4 text-center">
                        <img src="images/komune.png" alt="Logo Komune" style="height: 52px; object-fit: contain; margin-bottom: 8px;">
                        <h3 class="fw-bold text-dark mb-1">Komune.</h3>
                        <p class="text-muted font-size-13">Platform Digital Administrasi Komunitas</p>
                    </div>

                    <div class="mb-4">
                        <h4 class="fw-bold text-dark mb-1">Selamat Datang</h4>
                        <p class="text-muted font-size-13">Masuk dengan akun pengurus Anda untuk melanjutkan.</p>
                    </div>

                    <form id="form_login" autocomplete="on">
                        <?php echo Security::csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label" for="username">Username / Akun Pengurus</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autocomplete="username" autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0" for="password">Kata Sandi</label>
                            </div>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                                <span class="input-group-text" id="toggle_password_btn" onclick="toggle_password_view();" title="Lihat/Sembunyikan Kata Sandi">
                                    <i class="mdi mdi-eye-outline text-muted" id="toggle_password_icon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" id="btn_submit_login" class="btn-brand">
                                <span id="btn_text">Masuk ke Sistem</span>
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="text-muted font-size-12 mb-0">
                            Membutuhkan bantuan teknis? <a href="javascript:void(0);" onclick="alertify.alert('Bantuan Akun', 'Silakan hubungi administrator utama komunitas atau pengurus RT/RW terkait untuk penerbitan akun.');" style="color: #e52c3b; font-weight: 500; text-decoration: none;">Hubungi Pengurus</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="libs/jquery/jquery.min.js"></script>
    <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="libs/alertifyjs/build/alertify.min.js"></script>

    <script>
        function toggle_password_view() {
            let passInput = document.getElementById('password');
            let icon = document.getElementById('toggle_password_icon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.remove('mdi-eye-outline');
                icon.classList.add('mdi-eye-off-outline');
            } else {
                passInput.type = 'password';
                icon.classList.remove('mdi-eye-off-outline');
                icon.classList.add('mdi-eye-outline');
            }
        }

        $(document).ready(function() {
            $('#form_login').on('submit', function(e) {
                e.preventDefault();

                let username = $('#username').val().trim();
                let password = $('#password').val().trim();

                if (!username || !password) {
                    alertify.error('Harap lengkapi username dan kata sandi.');
                    return;
                }

                $('#btn_submit_login').prop('disabled', true);
                $('#btn_text').html('<div class="spinner-border spinner-border-sm text-white me-2"></div> Memverifikasi...');

                $.ajax({
                    url: 'login/proses',
                    type: 'POST',
                    data: {
                        username: username,
                        password: password
                    },
                    success: function(res) {
                        let status = parseInt(res, 10);
                        if (status === 1) {
                            alertify.success('Login berhasil! Mengalihkan...');
                            setTimeout(function() {
                                window.location.href = 'main';
                            }, 400);
                        } else {
                            $('#btn_submit_login').prop('disabled', false);
                            $('#btn_text').text('Masuk ke Sistem');
                            alertify.error('Username atau kata sandi tidak sesuai.');
                        }
                    },
                    error: function() {
                        $('#btn_submit_login').prop('disabled', false);
                        $('#btn_text').text('Masuk ke Sistem');
                        alertify.error('Gagal terhubung ke server.');
                    }
                });
            });
        });
    </script>
</body>
</html>