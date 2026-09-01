$(document).ready(function() {
    load_data_warga();
});

function load_data_warga() {
    let id_jenjang = $('#filter_wilayah').val();
    let id_kelas = $('#filter_kelompok').val();
    let search = $('#search_warga').val();

    $('#tbody_warga').html('<tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat data warga...</td></tr>');

    $.ajax({
        url: 'warga/ex/data_warga/get_list_warga',
        type: 'POST',
        dataType: 'json',
        data: {
            id_jenjang: id_jenjang,
            id_kelas: id_kelas,
            search: search
        },
        success: function(res) {
            if (res.status === 'success') {
                render_table_warga(res.data);
                $('#info_total_warga').text('Total: ' + res.total + ' warga aktif');
            } else {
                $('#tbody_warga').html('<tr><td colspan="8" class="text-center py-4 text-danger">' + res.message + '</td></tr>');
            }
        },
        error: function() {
            $('#tbody_warga').html('<tr><td colspan="8" class="text-center py-4 text-danger">Gagal memuat data warga dari server.</td></tr>');
        }
    });
}

function render_table_warga(list) {
    if (!list || list.length === 0) {
        $('#tbody_warga').html('<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data warga yang sesuai.</td></tr>');
        return;
    }

    let html = '';
    $.each(list, function(idx, item) {
        let no = idx + 1;
        let jkBadge = item.jenis_kelamin === 'L' ? '<span class="badge bg-light text-primary border">Laki-laki</span>' : '<span class="badge bg-light text-danger border">Perempuan</span>';
        let wilayah = (item.wilayah ? item.wilayah : '-') + ' &bull; ' + (item.kelompok_warga ? item.kelompok_warga : '-');
        let kk = item.nama_kepala_keluarga ? item.nama_kepala_keluarga : '-';
        let hp = item.no_telepon ? '<a href="https://wa.me/' + item.no_telepon + '" target="_blank" class="text-success text-decoration-none"><i class="mdi mdi-whatsapp me-1"></i>' + item.no_telepon + '</a>' : '-';
        let alamat = item.alamat ? item.alamat : '-';

        html += `
            <tr>
                <td class="text-center text-muted">${no}</td>
                <td><span class="fw-semibold text-dark">${item.nomor_anggota}</span></td>
                <td>
                    <div class="fw-semibold text-dark">${item.nama_warga}</div>
                    <div class="mt-1">${jkBadge}</div>
                </td>
                <td class="text-secondary">${kk}</td>
                <td><span class="badge bg-light text-dark border font-size-12">${wilayah}</span></td>
                <td>${hp}</td>
                <td class="text-muted" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${alamat}">${alamat}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="edit_warga(${item.id_siswa});" title="Edit">
                            <i class="mdi mdi-pencil font-size-14"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapus_warga(${item.id_siswa}, '${escapeHtml(item.nama_warga)}');" title="Hapus (Soft Delete)">
                            <i class="mdi mdi-trash-can-outline font-size-14"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#tbody_warga').html(html);
}

function tambah_warga_modal() {
    $('#form_warga')[0].reset();
    $('#warga_id').val('');
    $('#modal_warga_title').text('Tambah Data Warga');
    $('#btn_simpan_warga').text('Simpan Data');
    $('#modal_warga').modal('show');
}

function edit_warga(id) {
    $.ajax({
        url: 'warga/ex/data_warga/get_detail',
        type: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function(res) {
            if (res.status === 'success') {
                let d = res.data;
                $('#warga_id').val(d.id_siswa);
                $('#warga_nomor').val(d.nis);
                $('#warga_nama').val(d.nama_siswa);
                $('#warga_jk').val(d.jenis_kelamin || 'L');
                $('#warga_kk').val(d.nama_ortu || '');
                $('#warga_kelompok').val(d.id_kelas || '');
                $('#warga_telepon').val(d.no_hp_ortu || '');
                $('#warga_alamat').val(d.alamat || '');

                $('#modal_warga_title').text('Edit Data Warga: ' + d.nama_siswa);
                $('#btn_simpan_warga').text('Perbarui Data');
                $('#modal_warga').modal('show');
            } else {
                alertify.error(res.message);
            }
        },
        error: function() {
            alertify.error('Gagal mengambil data detail warga');
        }
    });
}

function submit_simpan_warga() {
    let id = $('#warga_id').val();
    let nomor = $('#warga_nomor').val().trim();
    let nama = $('#warga_nama').val().trim();

    if (!nomor || !nama) {
        alertify.error('Nomor Anggota/NIK dan Nama Warga wajib diisi!');
        return;
    }

    let url = id ? 'warga/ex/data_warga/update_warga' : 'warga/ex/data_warga/simpan_warga';
    let formData = $('#form_warga').serialize();

    $('#btn_simpan_warga').prop('disabled', true).text('Menyimpan...');

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function(res) {
            $('#btn_simpan_warga').prop('disabled', false).text('Simpan');
            if (res.status === 'success') {
                $('#modal_warga').modal('hide');
                alertify.success(res.message);
                load_data_warga();
            } else {
                alertify.error(res.message);
            }
        },
        error: function() {
            $('#btn_simpan_warga').prop('disabled', false).text('Simpan');
            alertify.error('Terjadi kesalahan pada server');
        }
    });
}

function hapus_warga(id, nama) {
    alertify.confirm('Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus data warga <b>' + nama + '</b>? <br><small class="text-muted">Data ini akan diarsipkan (Soft Delete) dan dapat dipulihkan oleh administrator.</small>', 
        function() {
            $.ajax({
                url: 'warga/ex/data_warga/hapus_warga',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(res) {
                    if (res.status === 'success') {
                        alertify.success(res.message);
                        load_data_warga();
                    } else {
                        alertify.error(res.message);
                    }
                },
                error: function() {
                    alertify.error('Gagal menghapus data warga');
                }
            });
        }, 
        function() {
            // Cancel action
        }
    ).set('labels', {ok:'Ya, Hapus Data', cancel:'Batal'});
}

function reset_filter() {
    $('#filter_wilayah').val('');
    $('#filter_kelompok').val('');
    $('#search_warga').val('');
    load_data_warga();
}

function escapeHtml(string) {
    if (!string) return '';
    return String(string).replace(/[&<>"'`=\/]/g, function (s) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        }[s];
    });
}
