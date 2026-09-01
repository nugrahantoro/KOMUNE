<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0 font-size-18 text-dark fw-semibold">Kelola Artikel & Bacaan</h4>
                            <p class="text-muted font-size-13 mb-0 mt-1">Daftar artikel bacaan, warta informasi, edukasi, dan wawasan warga dari penulis/kontributor.</p>
                        </div>
                        <div class="page-title-right mt-3 mt-sm-0">
                            <button type="button" class="btn btn-primary btn-sm px-3" onclick="tambah_artikel_modal();">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tulis Artikel Baru
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
                                    <label class="form-label font-size-12 text-muted mb-1">Kategori Konten</label>
                                    <select id="filter_art_kategori" class="form-select form-select-sm" onchange="load_data_artikel();">
                                        <option value="">Semua Kategori</option>
                                        <option value="Kajian">Kajian Keagamaan</option>
                                        <option value="Edukasi">Edukasi & Wawasan</option>
                                        <option value="Pengumuman">Pengumuman & Warta</option>
                                        <option value="Fiqih">Fiqih & Ibadah</option>
                                        <option value="Kesehatan">Kesehatan & Keluarga</option>
                                        <option value="Umum">Umum</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-size-12 text-muted mb-1">Status Publikasi</label>
                                    <select id="filter_art_status" class="form-select form-select-sm" onchange="load_data_artikel();">
                                        <option value="">Semua Status</option>
                                        <option value="published">Diterbitkan (Published)</option>
                                        <option value="draft">Draft / Konsep</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-size-12 text-muted mb-1">Pencarian (Judul / Penulis)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="search_artikel" class="form-control" placeholder="Cari judul artikel atau ustadz..." onkeyup="if(event.key === 'Enter') load_data_artikel();">
                                        <button class="btn btn-outline-secondary" type="button" onclick="load_data_artikel();">
                                            <i class="mdi mdi-magnify"></i> Cari
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end pt-3">
                                    <button class="btn btn-light btn-sm text-secondary w-100" onclick="reset_filter_artikel();">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted font-size-12 text-uppercase">
                                        <tr>
                                            <th style="width: 50px;" class="text-center">No</th>
                                            <th>Judul & Ringkasan</th>
                                            <th>Kategori</th>
                                            <th>Penulis / Ustadz</th>
                                            <th>Status</th>
                                            <th class="text-center">Dibaca</th>
                                            <th>Tanggal</th>
                                            <th style="width: 120px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_artikel" class="font-size-13">
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">Memuat data artikel...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-2 px-3 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted font-size-12" id="info_total_artikel">Menampilkan artikel aktif</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Form Artikel -->
<div class="modal fade" id="modal_artikel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-size-16 fw-semibold" id="modal_artikel_title">Tulis Artikel Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form_artikel">
                    <input type="hidden" id="art_id" name="id_artikel">
                    <?php echo Security::csrf_field(); ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label font-size-13 fw-medium">Judul Artikel / Bacaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="art_judul" name="judul" placeholder="Masukkan judul artikel yang menarik" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label font-size-13 fw-medium">Kategori</label>
                            <select class="form-select form-select-sm" id="art_kategori" name="kategori">
                                <option value="Kajian">Kajian Keagamaan</option>
                                <option value="Edukasi">Edukasi & Wawasan</option>
                                <option value="Pengumuman">Pengumuman & Warta</option>
                                <option value="Fiqih">Fiqih & Ibadah</option>
                                <option value="Kesehatan">Kesehatan & Keluarga</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-size-13 fw-medium">Penulis / Ustadz</label>
                            <input type="text" class="form-control form-control-sm" id="art_penulis" name="penulis" value="<?php echo Security::e($_SESSION['nama_us'] ?? 'Ustadz / Pengurus'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-size-13 fw-medium">Peran / Gelar Penulis</label>
                            <select class="form-select form-select-sm" id="art_role_penulis" name="role_penulis">
                                <option value="Ustadz / Pemateri">Ustadz / Pemateri</option>
                                <option value="Pengajar / Ustadzah">Pengajar / Ustadzah</option>
                                <option value="Pengurus Komunitas">Pengurus Komunitas</option>
                                <option value="Kontributor Warga">Kontributor Warga</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label font-size-13 fw-medium">URL Gambar Cover / Thumbnail (Opsional)</label>
                            <input type="text" class="form-control form-control-sm" id="art_thumbnail" name="thumbnail" placeholder="https://domain.com/gambar.jpg">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-size-13 fw-medium">Status Publikasi</label>
                            <select class="form-select form-select-sm" id="art_status" name="status">
                                <option value="published">Langsung Terbitkan (Published)</option>
                                <option value="draft">Simpan Sebagai Draft</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label font-size-13 fw-medium">Ringkasan Singkat (Opsional)</label>
                            <textarea class="form-control form-control-sm" id="art_ringkasan" name="ringkasan" rows="2" placeholder="1-2 kalimat ringkasan yang muncul di kartu feed"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label font-size-13 fw-medium">Isi Lengkap Bacaan / Artikel <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="art_konten" name="konten" rows="8" placeholder="Tuliskan isi artikel lengkap di sini..." required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="submit_simpan_artikel();" id="btn_simpan_art">Simpan Artikel</button>
            </div>
        </div>
    </div>
</div>

<script src="konten/js/j_konten.js?v=<?php echo uniqid(); ?>"></script>
