<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Banner Header -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #881337 0%, #0f172a 100%);">
                        <div class="card-body p-4 p-md-5">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <span class="badge bg-white text-danger font-size-12 px-3 py-1 mb-2 fw-semibold">Feed Informasi & Edukasi Warga</span>
                                    <h3 class="text-white fw-bold mb-2">Bacaan, Edukasi & Warta Komunitas</h3>
                                    <p class="text-white-50 font-size-14 mb-0">Temukan wawasan keilmuan, video edukasi, informasi kegiatan, dan panduan warga dalam satu feed interaktif terpadu.</p>
                                </div>
                                <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                                    <div class="input-group">
                                        <input type="text" id="feed_search_input" class="form-control" placeholder="Cari artikel, video, topik..." onkeyup="if(event.key==='Enter') load_feed_warga();">
                                        <button class="btn btn-primary" type="button" onclick="load_feed_warga();">
                                            <i class="mdi mdi-magnify text-white"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category & Type Tabs -->
            <div class="row mt-2">
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between pb-3 border-bottom">
                        <div class="btn-group btn-group-sm" role="group" id="btn_type_group">
                            <button type="button" class="btn btn-outline-primary active" onclick="set_feed_type('all', this);">Semua Konten</button>
                            <button type="button" class="btn btn-outline-primary" onclick="set_feed_type('artikel', this);">Artikel Bacaan</button>
                            <button type="button" class="btn btn-outline-primary" onclick="set_feed_type('video', this);">Video Kajian</button>
                        </div>
                        <div class="d-flex gap-2">
                            <select id="feed_kategori_select" class="form-select form-select-sm" style="min-width: 180px;" onchange="load_feed_warga();">
                                <option value="">Semua Kategori</option>
                                <option value="Kajian">Kajian Keagamaan</option>
                                <option value="Edukasi">Edukasi & Wawasan</option>
                                <option value="Fiqih">Fiqih & Ibadah</option>
                                <option value="Pengumuman">Pengumuman & Warta</option>
                                <option value="Kesehatan">Kesehatan</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feed Content Grid -->
            <div class="row mt-4" id="feed_grid_container">
                <div class="col-12 text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-danger me-2"></div> Memuat feed konten warga...
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Baca Artikel Lengkap -->
<div class="modal fade" id="modal_baca_artikel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <div>
                    <span class="badge bg-light text-primary border font-size-11 mb-1" id="read_kategori"></span>
                    <h5 class="modal-title font-size-18 fw-bold text-dark" id="read_judul"></h5>
                    <small class="text-muted font-size-12" id="read_meta"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="line-height: 1.8; font-size: 15px; color: #334155;">
                <div id="read_thumbnail_container" class="mb-3 text-center d-none">
                    <img id="read_thumbnail" src="" class="img-fluid rounded-3 shadow-sm" style="max-height: 350px; width: 100%; object-fit: cover;">
                </div>
                <div id="read_konten" style="white-space: pre-line;"></div>
            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tonton Video -->
<div class="modal fade" id="modal_tonton_video" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow bg-dark text-white">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title font-size-16 text-white" id="watch_judul"></h5>
                    <small class="text-white-50 font-size-12" id="watch_meta"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stop_video_player();"></button>
            </div>
            <div class="modal-body p-3">
                <div class="ratio ratio-16x9 rounded-3 overflow-hidden" id="video_player_container">
                    <!-- iframe embed injected here -->
                </div>
                <div class="mt-3 text-white-50 font-size-13" id="watch_deskripsi"></div>
            </div>
        </div>
    </div>
</div>

<script src="konten/js/j_konten.js?v=<?php echo uniqid(); ?>"></script>
<script>
$(document).ready(function() {
    load_feed_warga();
});
</script>
