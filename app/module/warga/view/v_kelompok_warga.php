<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <!-- Header Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-sm-0 font-size-18 text-dark fw-semibold">Kelompok Warga & Wilayah</h4>
                            <p class="text-muted font-size-13 mb-0 mt-1">Struktur pengelompokan RT/RW, lingkungan, dan kategori zona komunitas warga.</p>
                        </div>
                        <div class="page-title-right mt-3 mt-sm-0 d-flex gap-2">
                            <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="tambah_wilayah_modal();">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> + Tambah Wilayah (RW)
                            </button>
                            <button type="button" class="btn btn-primary btn-sm px-3" onclick="tambah_kelompok_modal();">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> + Tambah Kelompok (RT)
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Cards -->
            <div class="row mt-2">
                <!-- Kolom Wilayah Utama (Zona / RW) -->
                <div class="col-lg-5">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-size-14 mb-0 fw-semibold text-dark">Daftar Wilayah Utama (RW / Zona)</h5>
                            <button class="btn btn-sm btn-light border" onclick="tambah_wilayah_modal();"><i class="mdi mdi-plus"></i></button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted font-size-12 text-uppercase">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">No</th>
                                            <th>Nama Wilayah</th>
                                            <th class="text-center">Total Kelompok</th>
                                            <th style="width: 80px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-size-13">
                                        <?php
                                        $qw = "SELECT dj.*, (SELECT COUNT(*) FROM data_kelas dk WHERE dk.id_jenjang = dj.id_jenjang) AS total_kelompok 
                                               FROM data_jenjang dj 
                                               ORDER BY dj.nama_jenjang ASC";
                                        $resW = db_query($qw);
                                        $no = 1;
                                        if ($resW && mysqli_num_rows($resW) > 0) {
                                            while ($dw = mysqli_fetch_assoc($resW)) {
                                                $idJ = (int)$dw['id_jenjang'];
                                                $nmJ = Security::e($dw['nama_jenjang']);
                                                echo '<tr>
                                                    <td class="text-center text-muted">' . $no++ . '</td>
                                                    <td class="fw-semibold text-dark">' . $nmJ . '</td>
                                                    <td class="text-center"><span class="badge bg-light text-primary border">' . Security::e($dw['total_kelompok']) . ' Kelompok</span></td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-light btn-sm text-primary" onclick="edit_wilayah_modal(' . $idJ . ', \'' . addslashes($dw['nama_jenjang']) . '\');" title="Edit"><i class="mdi mdi-pencil"></i></button>
                                                            <button class="btn btn-light btn-sm text-danger" onclick="hapus_wilayah(' . $idJ . ');" title="Hapus"><i class="mdi mdi-trash-can-outline"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="4" class="text-center py-4 text-muted">Belum ada data wilayah.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kelompok Warga (RT / Unit) -->
                <div class="col-lg-7">
                    <div class="card border shadow-sm">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title font-size-14 mb-0 fw-semibold text-dark">Daftar Kelompok Warga (RT / Unit)</h5>
                            <button class="btn btn-sm btn-light border" onclick="tambah_kelompok_modal();"><i class="mdi mdi-plus"></i></button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-muted font-size-12 text-uppercase">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">No</th>
                                            <th>Nama Kelompok / RT</th>
                                            <th>Wilayah Induk</th>
                                            <th class="text-center">Jumlah Warga</th>
                                            <th style="width: 80px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-size-13">
                                        <?php
                                        $qk = "SELECT dk.*, dj.nama_jenjang, 
                                               (SELECT COUNT(*) FROM penempatan_siswa ps 
                                                JOIN data_siswa ds ON ds.id_siswa = ps.nis_from_idsiswa 
                                                WHERE ps.id_kelas = dk.id_kelas) AS total_warga
                                               FROM data_kelas dk
                                               LEFT JOIN data_jenjang dj ON dj.id_jenjang = dk.id_jenjang
                                               ORDER BY dj.nama_jenjang ASC, dk.nama_kelas ASC";
                                        $resK = db_query($qk);
                                        $no2 = 1;
                                        if ($resK && mysqli_num_rows($resK) > 0) {
                                            while ($dk = mysqli_fetch_assoc($resK)) {
                                                $idK = (int)$dk['id_kelas'];
                                                $nmK = Security::e($dk['nama_kelas']);
                                                $idJk = (int)$dk['id_jenjang'];
                                                echo '<tr>
                                                    <td class="text-center text-muted">' . $no2++ . '</td>
                                                    <td class="fw-semibold text-dark">' . $nmK . '</td>
                                                    <td class="text-muted">' . Security::e($dk['nama_jenjang'] ?? '-') . '</td>
                                                    <td class="text-center"><span class="badge bg-light text-success border">' . Security::e($dk['total_warga']) . ' Warga</span></td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-light btn-sm text-primary" onclick="edit_kelompok_modal(' . $idK . ', \'' . addslashes($dk['nama_kelas']) . '\', ' . $idJk . ');" title="Edit"><i class="mdi mdi-pencil"></i></button>
                                                            <button class="btn btn-light btn-sm text-danger" onclick="hapus_kelompok(' . $idK . ');" title="Hapus"><i class="mdi mdi-trash-can-outline"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data kelompok.</td></tr>';
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

<!-- Modal Tambah/Edit Wilayah (RW) -->
<div class="modal fade" id="modal_wilayah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-size-16 fw-semibold text-dark" id="modal_wilayah_title">Tambah Wilayah (RW / Zona)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_wilayah">
                <div class="modal-body p-4">
                    <input type="hidden" id="modal_id_wilayah" name="id_wilayah" value="">
                    <div class="mb-3">
                        <label class="form-label font-size-13 text-muted">Nama Wilayah (RW / Zona) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_nama_wilayah" name="nama_wilayah" placeholder="Contoh: RW 01 - Dusun Barat" required>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Wilayah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Kelompok (RT) -->
<div class="modal fade" id="modal_kelompok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-size-16 fw-semibold text-dark" id="modal_kelompok_title">Tambah Kelompok (RT / Unit)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_kelompok">
                <div class="modal-body p-4">
                    <input type="hidden" id="modal_id_kelompok" name="id_kelompok" value="">
                    <div class="mb-3">
                        <label class="form-label font-size-13 text-muted">Wilayah Induk (RW) <span class="text-danger">*</span></label>
                        <select class="form-select" id="modal_id_jenjang_kelompok" name="id_jenjang" required>
                            <option value="">Pilih Wilayah Induk</option>
                            <?php
                            $qjw = "SELECT id_jenjang, nama_jenjang FROM data_jenjang ORDER BY nama_jenjang ASC";
                            $rwj = db_query($qjw);
                            if ($rwj) {
                                while ($w = mysqli_fetch_assoc($rwj)) {
                                    echo '<option value="' . Security::e($w['id_jenjang']) . '">' . Security::e($w['nama_jenjang']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-size-13 text-muted">Nama Kelompok / RT <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_nama_kelompok" name="nama_kelompok" placeholder="Contoh: RT 01 / Blok A" required>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Kelompok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function tambah_wilayah_modal() {
    $('#modal_id_wilayah').val('');
    $('#modal_nama_wilayah').val('');
    $('#modal_wilayah_title').text('Tambah Wilayah (RW / Zona)');
    $('#modal_wilayah').modal('show');
}

function edit_wilayah_modal(id, nama) {
    $('#modal_id_wilayah').val(id);
    $('#modal_nama_wilayah').val(nama);
    $('#modal_wilayah_title').text('Edit Wilayah (RW / Zona)');
    $('#modal_wilayah').modal('show');
}

function hapus_wilayah(id) {
    alertify.confirm('Konfirmasi Hapus', 'Yakin ingin menghapus wilayah ini?', function() {
        $.post('warga/ex/data_warga/hapus_wilayah', { id: id }, function(res) {
            if (res.status === 'success') {
                alertify.success(res.message);
                setTimeout(function() { location.reload(); }, 500);
            } else {
                alertify.error(res.message);
            }
        }, 'json');
    }, function() {});
}

function tambah_kelompok_modal() {
    $('#modal_id_kelompok').val('');
    $('#modal_nama_kelompok').val('');
    $('#modal_id_jenjang_kelompok').val('');
    $('#modal_kelompok_title').text('Tambah Kelompok (RT / Unit)');
    $('#modal_kelompok').modal('show');
}

function edit_kelompok_modal(id, nama, idJenjang) {
    $('#modal_id_kelompok').val(id);
    $('#modal_nama_kelompok').val(nama);
    $('#modal_id_jenjang_kelompok').val(idJenjang);
    $('#modal_kelompok_title').text('Edit Kelompok (RT / Unit)');
    $('#modal_kelompok').modal('show');
}

function hapus_kelompok(id) {
    alertify.confirm('Konfirmasi Hapus', 'Yakin ingin menghapus kelompok/RT ini?', function() {
        $.post('warga/ex/data_warga/hapus_kelompok', { id: id }, function(res) {
            if (res.status === 'success') {
                alertify.success(res.message);
                setTimeout(function() { location.reload(); }, 500);
            } else {
                alertify.error(res.message);
            }
        }, 'json');
    }, function() {});
}

$(document).ready(function() {
    $('#form_wilayah').on('submit', function(e) {
        e.preventDefault();
        $.post('warga/ex/data_warga/simpan_wilayah', $(this).serialize(), function(res) {
            if (res.status === 'success') {
                alertify.success(res.message);
                $('#modal_wilayah').modal('hide');
                setTimeout(function() { location.reload(); }, 500);
            } else {
                alertify.error(res.message);
            }
        }, 'json');
    });

    $('#form_kelompok').on('submit', function(e) {
        e.preventDefault();
        $.post('warga/ex/data_warga/simpan_kelompok', $(this).serialize(), function(res) {
            if (res.status === 'success') {
                alertify.success(res.message);
                $('#modal_kelompok').modal('hide');
                setTimeout(function() { location.reload(); }, 500);
            } else {
                alertify.error(res.message);
            }
        }, 'json');
    });
});
</script>
