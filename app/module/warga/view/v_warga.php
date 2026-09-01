<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Header Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0 font-size-18 text-dark fw-semibold">Data Warga & Anggota</h4>
                            <p class="text-muted font-size-13 mb-0 mt-1">Manajemen informasi identitas warga, domisili, dan pengelompokan wilayah komunitas.</p>
                        </div>
                        <div class="page-title-right mt-3 mt-sm-0">
                            <button type="button" class="btn btn-primary btn-sm px-3" onclick="tambah_warga_modal();">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Warga
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card border shadow-sm">
                        <div class="card-body p-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label font-size-12 text-muted mb-1">Wilayah / Kategori</label>
                                    <select id="filter_wilayah" class="form-select form-select-sm" onchange="load_data_warga();">
                                        <option value="">Semua Wilayah</option>
                                        <?php
                                        $qj = "SELECT id_jenjang, nama_jenjang FROM data_jenjang WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY nama_jenjang ASC";
                                        $resJ = db_query($qj);
                                        while ($dj = mysqli_fetch_assoc($resJ)) {
                                            echo '<option value="' . Security::e($dj['id_jenjang']) . '">' . Security::e($dj['nama_jenjang']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label font-size-12 text-muted mb-1">Kelompok / RT-RW</label>
                                    <select id="filter_kelompok" class="form-select form-select-sm" onchange="load_data_warga();">
                                        <option value="">Semua Kelompok</option>
                                        <?php
                                        $qk = "SELECT id_kelas, nama_kelas FROM data_kelas WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY nama_kelas ASC";
                                        $resK = db_query($qk);
                                        while ($dk = mysqli_fetch_assoc($resK)) {
                                            echo '<option value="' . Security::e($dk['id_kelas']) . '">' . Security::e($dk['nama_kelas']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label font-size-12 text-muted mb-1">Pencarian (Nama / NIK / No. HP)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="search_warga" class="form-control" placeholder="Ketik kata kunci..." onkeyup="if(event.key === 'Enter') load_data_warga();">
                                        <button class="btn btn-outline-secondary" type="button" onclick="load_data_warga();">
                                            <i class="mdi mdi-magnify"></i> Cari
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end pt-3">
                                    <button class="btn btn-light btn-sm text-secondary w-100" onclick="reset_filter();">
                                        Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card border shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="tbl_data_warga">
                                    <thead class="table-light text-muted font-size-12 text-uppercase">
                                        <tr>
                                            <th style="width: 50px;" class="text-center">No</th>
                                            <th>No. Anggota / NIK</th>
                                            <th>Nama Warga</th>
                                            <th>Kepala Keluarga</th>
                                            <th>Wilayah & Kelompok</th>
                                            <th>No. Telepon / WA</th>
                                            <th>Alamat</th>
                                            <th style="width: 120px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_warga" class="font-size-13">
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">Memuat data warga...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-2 px-3 border-top d-flex justify-content-between align-items-center">
                            <small class="text-muted font-size-12" id="info_total_warga">Menampilkan data aktif</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Form Tambah / Edit Warga -->
<div class="modal fade" id="modal_warga" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-size-16 fw-semibold" id="modal_warga_title">Tambah Data Warga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form_warga">
                    <input type="hidden" id="warga_id" name="id_siswa">
                    <?php echo Security::csrf_field(); ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-size-13 fw-medium">Nomor Anggota / NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="warga_nomor" name="nomor_anggota" placeholder="Contoh: 351501230001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-size-13 fw-medium">Nama Lengkap Warga <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="warga_nama" name="nama_warga" placeholder="Nama lengkap sesuai KTP" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-size-13 fw-medium">Jenis Kelamin</label>
                            <select class="form-select form-select-sm" id="warga_jk" name="jenis_kelamin">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-size-13 fw-medium">Nama Kepala Keluarga (Opsional)</label>
                            <input type="text" class="form-control form-control-sm" id="warga_kk" name="nama_kepala_keluarga" placeholder="Nama Kepala Keluarga">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-size-13 fw-medium">Kelompok Warga / RT-RW</label>
                            <select class="form-select form-select-sm" id="warga_kelompok" name="id_kelas">
                                <option value="">-- Pilih Kelompok / RT-RW --</option>
                                <?php
                                $qk2 = "SELECT id_kelas, nama_kelas FROM data_kelas WHERE (is_deleted = 0 OR is_deleted IS NULL) ORDER BY nama_kelas ASC";
                                $resK2 = db_query($qk2);
                                while ($dk2 = mysqli_fetch_assoc($resK2)) {
                                    echo '<option value="' . Security::e($dk2['id_kelas']) . '">' . Security::e($dk2['nama_kelas']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-size-13 fw-medium">Nomor WhatsApp / Telepon</label>
                            <input type="text" class="form-control form-control-sm" id="warga_telepon" name="no_telepon" placeholder="08123456789">
                        </div>

                        <div class="col-12">
                            <label class="form-label font-size-13 fw-medium">Alamat Domisili</label>
                            <textarea class="form-control form-control-sm" id="warga_alamat" name="alamat" rows="2" placeholder="Jalan, Blok, No. Rumah"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm px-4" onclick="submit_simpan_warga();" id="btn_simpan_warga">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script src="warga/js/j_warga.js?v=<?php echo uniqid(); ?>"></script>
