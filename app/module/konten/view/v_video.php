<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0 font-size-18 text-dark fw-semibold">Kelola Video Edukasi</h4>
                            <p class="text-muted font-size-13 mb-0 mt-1">Embed dan bagikan rekaman video edukasi, seminar, tutorial, dan dokumentasi warga.</p>
                        </div>
                        <div class="page-title-right mt-3 mt-sm-0">
                            <button type="button" class="btn btn-primary btn-sm px-3" onclick="tambah_video_modal();">
                                <i class="mdi mdi-video-plus-outline me-1"></i> Tambah Video Baru
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
                                    <label class="form-label font-size-12 text-muted mb-1">Kategori Video</label>
                                    <select id="filter_vid_kategori" class="form-select form-select-sm" onchange="load_data_video();">
                                        <option value="">Semua Kategori</option>
                                        <option value="Kajian">Kajian & Ceramah</option>
                                        <option value="Tafsir">Tafsir & Hadits</option>
                                        <option value="Fiqih">Fiqih Praktis</option>
                                        <option value="Edukasi">Edukasi & Tutorial</option>
                                        <option value="Dokumentasi">Dokumentasi Kegiatan</option>
                                        <option value="Podcast">Podcast & Obrolan Warga</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-size-12 text-muted mb-1">Pencarian (Judul / Pemateri)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="search_video" class="form-control" placeholder="Cari judul video atau nama ustadz..." onkeyup="if(event.key === 'Enter') load_data_video();">
                                        <button class="btn btn-outline-secondary" type="button" onclick="load_data_video();">
                                            <i class="mdi mdi-magnify"></i> Cari
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end pt-3">
                                    <button class="btn btn-light btn-sm text-secondary w-100" onclick="reset_filter_video();">
                                        Reset Filter
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
                                            <th style="width: 120px;">Thumbnail</th>
                                            <th>Judul & Pemateri</th>
                                            <th>Kategori</th>
                                            <th>Platform</th>
                                            <th class="text-center">Ditonton</th>
                                            <th>Tanggal</th>
                                            <th style="width: 120px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_video" class="font-size-13">
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">Memuat data video...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-2 px-3 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted font-size-12" id="info_total_video">Menampilkan video aktif</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Form Video -->
<div class="modal fade" id="modal_video" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-size-16 fw-semibold" id="modal_video_title">Tambah Video Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form_video">
                    <input type="hidden" id="vid_id" name="id_video">
                    <?php echo Security::csrf_field(); ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label font-size-13 fw-medium">Judul Video Kajian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="vid_judul" name="judul" placeholder="Contoh: Kajian Rutin Malam Ahad - Keutamaan Sedekah" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label font-size-13 fw-medium">Link URL Video (YouTube / Direct) <span class="text-danger">*</span></label>
                            <input type="url" class="form-control form-control-sm" id="vid_url" name="url_video" placeholder="https://www.youtube.com/watch?v=..." required oninput="preview_youtube_thumbnail();">
                            <small class="text-muted font-size-11">Sistem otomatis mengenali link YouTube reguler, live, dan Shorts.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-size-13 fw-medium">Kategori Video</label>
                            <select class="form-select form-select-sm" id="vid_kategori" name="kategori">
                                <option value="Kajian">Kajian & Ceramah</option>
                                <option value="Tafsir">Tafsir & Hadits</option>
                                <option value="Fiqih">Fiqih Praktis</option>
                                <option value="Edukasi">Edukasi & Tutorial</option>
                                <option value="Dokumentasi">Dokumentasi Kegiatan</option>
                                <option value="Podcast">Podcast & Obrolan Warga</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-size-13 fw-medium">Nama Pemateri / Ustadz</label>
                            <input type="text" class="form-control form-control-sm" id="vid_pemateri" name="pemateri" placeholder="Nama Ustadz / Pengisi Acara" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label font-size-13 fw-medium">Deskripsi Ringkas Video</label>
                            <textarea class="form-control form-control-sm" id="vid_deskripsi" name="deskripsi" rows="3" placeholder="Poin-poin pembahasan kajian atau catatan penting..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm px-4" onclick="submit_simpan_video();" id="btn_simpan_vid">Simpan Video</button>
            </div>
        </div>
    </div>
</div>

<script src="konten/js/j_konten.js?v=<?php echo uniqid(); ?>"></script>
