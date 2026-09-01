<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0 font-size-18 text-dark fw-semibold">Pusat Konten & Edukasi</h4>
                            <p class="text-muted font-size-13 mb-0 mt-1">Platform publikasi artikel bacaan, warta informasi, dan video edukasi komunitas.</p>
                        </div>
                        <div class="page-title-right mt-3 mt-sm-0 d-flex gap-2">
                            <a href="konten/artikel" class="btn btn-outline-primary btn-sm px-3">
                                <i class="mdi mdi-book-open-page-variant-outline me-1"></i> Kelola Artikel
                            </a>
                            <a href="konten/video" class="btn btn-outline-danger btn-sm px-3">
                                <i class="mdi mdi-video-outline me-1"></i> Kelola Video
                            </a>
                            <a href="konten/feed" class="btn btn-primary btn-sm px-3">
                                <i class="mdi mdi-view-dashboard-outline me-1"></i> Buka Feed Warga
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metric Cards -->
            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="card border shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-light text-primary rounded-3 d-flex align-items-center justify-content-center me-3 border">
                                    <i class="mdi mdi-book-open-outline font-size-24"></i>
                                </div>
                                <div>
                                    <p class="text-muted font-size-13 mb-1">Total Artikel Aktif</p>
                                    <?php
                                    $cntArt = db_assoc("SELECT COUNT(*) as total FROM tbl_artikel");
                                    $totalArt = $cntArt['total'] ?? 0;
                                    ?>
                                    <h3 class="mb-0 text-dark fw-bold"><?php echo $totalArt; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-light text-danger rounded-3 d-flex align-items-center justify-content-center me-3 border">
                                    <i class="mdi mdi-youtube font-size-24"></i>
                                </div>
                                <div>
                                    <p class="text-muted font-size-13 mb-1">Total Video Edukasi</p>
                                    <?php
                                    $cntVid = db_assoc("SELECT COUNT(*) as total FROM tbl_video");
                                    $totalVid = $cntVid['total'] ?? 0;
                                    ?>
                                    <h3 class="mb-0 text-dark fw-bold"><?php echo $totalVid; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-light text-info rounded-3 d-flex align-items-center justify-content-center me-3 border">
                                    <i class="mdi mdi-account-voice font-size-24"></i>
                                </div>
                                <div>
                                    <p class="text-muted font-size-13 mb-1">Penulis & Kontributor</p>
                                    <?php
                                    $cntAuthor = db_assoc("SELECT COUNT(DISTINCT penulis) as total FROM tbl_artikel");
                                    $totalAuthor = $cntAuthor['total'] ?? 0;
                                    ?>
                                    <h3 class="mb-0 text-dark fw-bold"><?php echo $totalAuthor; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation Cards -->
            <div class="row mt-3">
                <div class="col-lg-6">
                    <div class="card border shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-size-15 mb-0 fw-semibold text-dark">Artikel Terbaru</h5>
                            <a href="konten/artikel" class="text-danger font-size-12 text-decoration-none">Lihat Semua &rarr;</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 font-size-13">
                                    <tbody id="tbody_preview_artikel">
                                        <?php
                                        $qArt = "SELECT * FROM tbl_artikel ORDER BY id_artikel DESC LIMIT 5";
                                        $resArt = db_query($qArt);
                                        if (!$resArt || mysqli_num_rows($resArt) == 0) {
                                            echo '<tr><td class="text-center py-4 text-muted">Belum ada artikel yang diterbitkan.</td></tr>';
                                        } else {
                                            while ($ra = mysqli_fetch_assoc($resArt)) {
                                                echo '<tr>
                                                    <td class="ps-3 py-3">
                                                        <span class="badge bg-light text-primary border mb-1 font-size-11">' . Security::e($ra['kategori']) . '</span>
                                                        <div class="fw-semibold text-dark">' . Security::e($ra['judul']) . '</div>
                                                        <small class="text-muted">' . Security::e($ra['penulis']) . ' &bull; ' . date('d M Y', strtotime($ra['created_at'])) . '</small>
                                                    </td>
                                                </tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border shadow-sm h-100">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-size-15 mb-0 fw-semibold text-dark">Video Edukasi Terbaru</h5>
                            <a href="konten/video" class="text-danger font-size-12 text-decoration-none">Lihat Semua &rarr;</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 font-size-13">
                                    <tbody id="tbody_preview_video">
                                        <?php
                                        $qVid = "SELECT * FROM tbl_video ORDER BY id_video DESC LIMIT 5";
                                        $resVid = db_query($qVid);
                                        if (!$resVid || mysqli_num_rows($resVid) == 0) {
                                            echo '<tr><td class="text-center py-4 text-muted">Belum ada video edukasi yang ditambahkan.</td></tr>';
                                        } else {
                                            while ($rv = mysqli_fetch_assoc($resVid)) {
                                                echo '<tr>
                                                    <td class="ps-3 py-3">
                                                        <span class="badge bg-light text-danger border mb-1 font-size-11">' . Security::e($rv['kategori']) . '</span>
                                                        <div class="fw-semibold text-dark">' . Security::e($rv['judul']) . '</div>
                                                        <small class="text-muted">' . Security::e($rv['pemateri']) . ' (' . Security::e($rv['role_pemateri']) . ')</small>
                                                    </td>
                                                </tr>';
                                            }
                                        }
                                        ?>
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
