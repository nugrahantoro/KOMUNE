let currentFeedType = 'all';

$(document).ready(function() {
    if ($('#tbody_artikel').length) {
        load_data_artikel();
    }
    if ($('#tbody_video').length) {
        load_data_video();
    }
});

/* =========================================================================
   1. ARTIKEL MANAGEMENT
   ========================================================================= */

function load_data_artikel() {
    let kategori = $('#filter_art_kategori').val();
    let status = $('#filter_art_status').val();
    let search = $('#search_artikel').val();

    $('#tbody_artikel').html('<tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat artikel...</td></tr>');

    $.ajax({
        url: 'konten/ex/artikel/get_list',
        type: 'POST',
        dataType: 'json',
        data: {
            kategori: kategori,
            status: status,
            search: search
        },
        success: function(res) {
            if (res.status === 'success') {
                render_table_artikel(res.data);
                $('#info_total_artikel').text('Total: ' + res.total + ' artikel aktif');
            } else {
                $('#tbody_artikel').html('<tr><td colspan="8" class="text-center py-4 text-danger">' + res.message + '</td></tr>');
            }
        },
        error: function() {
            $('#tbody_artikel').html('<tr><td colspan="8" class="text-center py-4 text-danger">Gagal memuat artikel dari server.</td></tr>');
        }
    });
}

function render_table_artikel(list) {
    if (!list || list.length === 0) {
        $('#tbody_artikel').html('<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada artikel yang tersedia.</td></tr>');
        return;
    }

    let html = '';
    $.each(list, function(idx, item) {
        let no = idx + 1;
        let statusBadge = item.status === 'published' ? '<span class="badge bg-light text-success border">Diterbitkan</span>' : '<span class="badge bg-light text-warning border">Draft</span>';
        let ringkasan = item.ringkasan ? item.ringkasan : '-';
        let tgl = item.created_at ? item.created_at.substring(0, 10) : '-';

        html += `
            <tr>
                <td class="text-center text-muted">${no}</td>
                <td>
                    <div class="fw-semibold text-dark font-size-14">${escapeHtml(item.judul)}</div>
                    <small class="text-muted" style="max-width: 300px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(ringkasan)}</small>
                </td>
                <td><span class="badge bg-light text-primary border">${escapeHtml(item.kategori)}</span></td>
                <td>
                    <div class="fw-medium text-dark">${escapeHtml(item.penulis)}</div>
                    <small class="text-muted">${escapeHtml(item.role_penulis || 'Ustadz')}</small>
                </td>
                <td>${statusBadge}</td>
                <td class="text-center text-muted">${item.views || 0}</td>
                <td class="text-muted font-size-12">${tgl}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="edit_artikel(${item.id_artikel});" title="Edit">
                            <i class="mdi mdi-pencil font-size-14"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapus_artikel(${item.id_artikel}, '${escapeHtml(item.judul)}');" title="Hapus (Soft Delete)">
                            <i class="mdi mdi-trash-can-outline font-size-14"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#tbody_artikel').html(html);
}

function tambah_artikel_modal() {
    $('#form_artikel')[0].reset();
    $('#art_id').val('');
    $('#modal_artikel_title').text('Tulis Artikel Baru');
    $('#btn_simpan_art').text('Simpan Artikel');
    $('#modal_artikel').modal('show');
}

function edit_artikel(id) {
    $.ajax({
        url: 'konten/ex/artikel/get_detail',
        type: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function(res) {
            if (res.status === 'success') {
                let d = res.data;
                $('#art_id').val(d.id_artikel);
                $('#art_judul').val(d.judul);
                $('#art_kategori').val(d.kategori);
                $('#art_penulis').val(d.penulis);
                $('#art_role_penulis').val(d.role_penulis);
                $('#art_thumbnail').val(d.thumbnail || '');
                $('#art_status').val(d.status);
                $('#art_ringkasan').val(d.ringkasan || '');
                $('#art_konten').val(d.konten || '');

                $('#modal_artikel_title').text('Edit Artikel: ' + d.judul);
                $('#btn_simpan_art').text('Perbarui Artikel');
                $('#modal_artikel').modal('show');
            } else {
                alertify.error(res.message);
            }
        },
        error: function() {
            alertify.error('Gagal mengambil data artikel');
        }
    });
}

function submit_simpan_artikel() {
    let id = $('#art_id').val();
    let judul = $('#art_judul').val().trim();
    let konten = $('#art_konten').val().trim();

    if (!judul || !konten) {
        alertify.error('Judul dan Konten bacaan wajib diisi!');
        return;
    }

    let url = id ? 'konten/ex/artikel/update' : 'konten/ex/artikel/simpan';
    let formData = $('#form_artikel').serialize();

    $('#btn_simpan_art').prop('disabled', true).text('Menyimpan...');

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function(res) {
            $('#btn_simpan_art').prop('disabled', false).text('Simpan');
            if (res.status === 'success') {
                $('#modal_artikel').modal('hide');
                alertify.success(res.message);
                load_data_artikel();
            } else {
                alertify.error(res.message);
            }
        },
        error: function() {
            $('#btn_simpan_art').prop('disabled', false).text('Simpan');
            alertify.error('Terjadi kesalahan pada server');
        }
    });
}

function hapus_artikel(id, judul) {
    alertify.confirm('Konfirmasi Hapus', 'Hapus artikel <b>' + judul + '</b>? <br><small class="text-muted">Artikel akan diarsipkan (Soft Delete).</small>',
        function() {
            $.ajax({
                url: 'konten/ex/artikel/hapus',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(res) {
                    if (res.status === 'success') {
                        alertify.success(res.message);
                        load_data_artikel();
                    } else {
                        alertify.error(res.message);
                    }
                },
                error: function() {
                    alertify.error('Gagal menghapus artikel');
                }
            });
        },
        function() {}
    ).set('labels', {ok:'Hapus', cancel:'Batal'});
}

function reset_filter_artikel() {
    $('#filter_art_kategori').val('');
    $('#filter_art_status').val('');
    $('#search_artikel').val('');
    load_data_artikel();
}

/* =========================================================================
   2. VIDEO MANAGEMENT
   ========================================================================= */

function load_data_video() {
    let kategori = $('#filter_vid_kategori').val();
    let search = $('#search_video').val();

    $('#tbody_video').html('<tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-success me-2"></div>Memuat video...</td></tr>');

    $.ajax({
        url: 'konten/ex/video/get_list',
        type: 'POST',
        dataType: 'json',
        data: {
            kategori: kategori,
            search: search
        },
        success: function(res) {
            if (res.status === 'success') {
                render_table_video(res.data);
                $('#info_total_video').text('Total: ' + res.total + ' video kajian');
            } else {
                $('#tbody_video').html('<tr><td colspan="8" class="text-center py-4 text-danger">' + res.message + '</td></tr>');
            }
        },
        error: function() {
            $('#tbody_video').html('<tr><td colspan="8" class="text-center py-4 text-danger">Gagal memuat video dari server.</td></tr>');
        }
    });
}

function render_table_video(list) {
    if (!list || list.length === 0) {
        $('#tbody_video').html('<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada video kajian yang tersedia.</td></tr>');
        return;
    }

    let html = '';
    $.each(list, function(idx, item) {
        let no = idx + 1;
        let thumb = item.thumbnail ? `<img src="${item.thumbnail}" class="rounded border" style="width: 100px; height: 58px; object-fit: cover;">` : '<div class="bg-light text-center py-3 rounded border text-muted font-size-11">No Thumb</div>';
        let platformBadge = item.platform === 'youtube' ? '<span class="badge bg-light text-danger border"><i class="mdi mdi-youtube me-1"></i>YouTube</span>' : '<span class="badge bg-light text-secondary border">Direct</span>';
        let tgl = item.created_at ? item.created_at.substring(0, 10) : '-';

        html += `
            <tr>
                <td class="text-center text-muted">${no}</td>
                <td>${thumb}</td>
                <td>
                    <div class="fw-semibold text-dark font-size-14">${escapeHtml(item.judul)}</div>
                    <small class="text-muted"><i class="mdi mdi-account-voice me-1"></i>${escapeHtml(item.pemateri)} (${escapeHtml(item.role_pemateri || 'Ustadz')})</small>
                </td>
                <td><span class="badge bg-light text-success border">${escapeHtml(item.kategori)}</span></td>
                <td>${platformBadge}</td>
                <td class="text-center text-muted">${item.views || 0}</td>
                <td class="text-muted font-size-12">${tgl}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="edit_video(${item.id_video});" title="Edit">
                            <i class="mdi mdi-pencil font-size-14"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="hapus_video(${item.id_video}, '${escapeHtml(item.judul)}');" title="Hapus (Soft Delete)">
                            <i class="mdi mdi-trash-can-outline font-size-14"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    $('#tbody_video').html(html);
}

function tambah_video_modal() {
    $('#form_video')[0].reset();
    $('#vid_id').val('');
    $('#modal_video_title').text('Tambah Video Baru');
    $('#btn_simpan_vid').text('Simpan Video');
    $('#modal_video').modal('show');
}

function edit_video(id) {
    $.ajax({
        url: 'konten/ex/video/get_detail',
        type: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function(res) {
            if (res.status === 'success') {
                let d = res.data;
                $('#vid_id').val(d.id_video);
                $('#vid_judul').val(d.judul);
                $('#vid_url').val(d.url_video);
                $('#vid_kategori').val(d.kategori);
                $('#vid_pemateri').val(d.pemateri);
                $('#vid_deskripsi').val(d.deskripsi || '');

                $('#modal_video_title').text('Edit Video: ' + d.judul);
                $('#btn_simpan_vid').text('Perbarui Video');
                $('#modal_video').modal('show');
            } else {
                alertify.error(res.message);
            }
        },
        error: function() {
            alertify.error('Gagal mengambil data video');
        }
    });
}

function submit_simpan_video() {
    let id = $('#vid_id').val();
    let judul = $('#vid_judul').val().trim();
    let urlVideo = $('#vid_url').val().trim();

    if (!judul || !urlVideo) {
        alertify.error('Judul dan URL link video wajib diisi!');
        return;
    }

    let url = id ? 'konten/ex/video/update' : 'konten/ex/video/simpan';
    let formData = $('#form_video').serialize();

    $('#btn_simpan_vid').prop('disabled', true).text('Menyimpan...');

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function(res) {
            $('#btn_simpan_vid').prop('disabled', false).text('Simpan');
            if (res.status === 'success') {
                $('#modal_video').modal('hide');
                alertify.success(res.message);
                load_data_video();
            } else {
                alertify.error(res.message);
            }
        },
        error: function() {
            $('#btn_simpan_vid').prop('disabled', false).text('Simpan');
            alertify.error('Terjadi kesalahan pada server');
        }
    });
}

function hapus_video(id, judul) {
    alertify.confirm('Konfirmasi Hapus', 'Hapus video kajian <b>' + judul + '</b>? <br><small class="text-muted">Video akan diarsipkan (Soft Delete).</small>',
        function() {
            $.ajax({
                url: 'konten/ex/video/hapus',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(res) {
                    if (res.status === 'success') {
                        alertify.success(res.message);
                        load_data_video();
                    } else {
                        alertify.error(res.message);
                    }
                },
                error: function() {
                    alertify.error('Gagal menghapus video');
                }
            });
        },
        function() {}
    ).set('labels', {ok:'Hapus', cancel:'Batal'});
}

function reset_filter_video() {
    $('#filter_vid_kategori').val('');
    $('#search_video').val('');
    load_data_video();
}

/* =========================================================================
   3. INTEGRATED CITIZEN FEED
   ========================================================================= */

function set_feed_type(type, btn) {
    currentFeedType = type;
    $('#btn_type_group .btn').removeClass('active');
    $(btn).addClass('active');
    load_feed_warga();
}

function load_feed_warga() {
    let kategori = $('#feed_kategori_select').val();
    let search = $('#feed_search_input').val();

    $('#feed_grid_container').html('<div class="col-12 text-center py-5 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat feed konten warga...</div>');

    $.ajax({
        url: 'konten/ex/feed/get_feed_json',
        type: 'GET',
        dataType: 'json',
        data: {
            kategori: kategori,
            search: search
        },
        success: function(res) {
            if (res.status === '00') {
                render_feed_cards(res.data.articles, res.data.videos);
            } else {
                $('#feed_grid_container').html('<div class="col-12 text-center py-5 text-danger">' + res.message + '</div>');
            }
        },
        error: function() {
            $('#feed_grid_container').html('<div class="col-12 text-center py-5 text-danger">Gagal memuat feed konten dari server.</div>');
        }
    });
}

function render_feed_cards(articles, videos) {
    let items = [];

    if (currentFeedType === 'all' || currentFeedType === 'artikel') {
        $.each(articles || [], function(i, a) {
            items.push(a);
        });
    }

    if (currentFeedType === 'all' || currentFeedType === 'video') {
        $.each(videos || [], function(i, v) {
            items.push(v);
        });
    }

    if (items.length === 0) {
        $('#feed_grid_container').html('<div class="col-12 text-center py-5 text-muted font-size-15">Belum ada konten artikel atau video yang sesuai.</div>');
        return;
    }

    // Urutkan berdasarkan tanggal terbaru
    items.sort(function(a, b) {
        return new Date(b.created_at) - new Date(a.created_at);
    });

    let html = '';
    $.each(items, function(idx, item) {
        let isVideo = item.tipe === 'video';
        let badgeColor = isVideo ? 'text-success' : 'text-primary';
        let typeIcon = isVideo ? '<i class="mdi mdi-play-circle text-danger me-1"></i>Video Kajian' : '<i class="mdi mdi-book-open-outline text-primary me-1"></i>Artikel Bacaan';
        let thumbUrl = item.thumbnail ? item.thumbnail : (isVideo ? 'images/video-placeholder.png' : 'images/article-placeholder.png');
        let author = isVideo ? item.pemateri : item.penulis;
        let role = isVideo ? (item.role_pemateri || 'Ustadz') : (item.role_penulis || 'Pengajar');
        let tgl = item.created_at ? item.created_at.substring(0, 10) : '';

        let clickHandler = isVideo ? `open_watch_modal(${item.id_video});` : `open_read_modal(${item.id_artikel});`;

        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border shadow-sm hover-shadow transition-all" style="cursor: pointer;" onclick="${clickHandler}">
                    <div class="position-relative overflow-hidden rounded-top" style="height: 180px; background-color: #f1f5f9;">
                        <img src="${thumbUrl}" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='images/komune.png';">
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-white ${badgeColor} shadow-sm border font-size-11">${escapeHtml(item.kategori)}</span>
                        </div>
                        ${isVideo ? '<div class="position-absolute top-50 start-50 translate-middle"><i class="mdi mdi-play-circle text-white font-size-48" style="text-shadow: 0 2px 10px rgba(0,0,0,0.5);"></i></div>' : ''}
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="font-size-12 text-muted mb-1">${typeIcon} &bull; ${tgl}</div>
                        <h5 class="card-title font-size-15 text-dark fw-bold mb-2 line-clamp-2" style="line-height: 1.4;">${escapeHtml(item.judul)}</h5>
                        <p class="card-text text-muted font-size-13 flex-grow-1 line-clamp-2">${escapeHtml(item.ringkasan || item.deskripsi || 'Klik untuk membuka konten selengkapnya...')}</p>
                        
                        <div class="d-flex align-items-center pt-2 border-top mt-2">
                            <div class="avatar-xs bg-light rounded-circle text-dark fw-bold d-flex align-items-center justify-content-center me-2 border font-size-11">
                                ${escapeHtml(author.charAt(0))}
                            </div>
                            <div class="font-size-12">
                                <div class="fw-semibold text-dark">${escapeHtml(author)}</div>
                                <small class="text-muted">${escapeHtml(role)}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    $('#feed_grid_container').html(html);
}

function open_read_modal(id) {
    $.ajax({
        url: 'konten/ex/artikel/get_detail',
        type: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function(res) {
            if (res.status === 'success') {
                let d = res.data;
                $('#read_judul').text(d.judul);
                $('#read_kategori').text(d.kategori);
                $('#read_meta').text('Ditulis oleh ' + d.penulis + ' (' + (d.role_penulis || 'Ustadz') + ') • ' + (d.created_at ? d.created_at.substring(0, 10) : ''));
                $('#read_konten').text(d.konten);

                if (d.thumbnail) {
                    $('#read_thumbnail').attr('src', d.thumbnail);
                    $('#read_thumbnail_container').removeClass('d-none');
                } else {
                    $('#read_thumbnail_container').addClass('d-none');
                }

                $('#modal_baca_artikel').modal('show');

                // Increment view
                $.post('konten/ex/feed/view_counter', { id: id, tipe: 'artikel' });
            }
        }
    });
}

function open_watch_modal(id) {
    $.ajax({
        url: 'konten/ex/video/get_detail',
        type: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function(res) {
            if (res.status === 'success') {
                let d = res.data;
                $('#watch_judul').text(d.judul);
                $('#watch_meta').text('Pemateri: ' + d.pemateri + ' (' + (d.role_pemateri || 'Ustadz') + ') • Kategori: ' + d.kategori);
                $('#watch_deskripsi').text(d.deskripsi || '');

                if (d.embed_id) {
                    $('#video_player_container').html(`<iframe src="https://www.youtube.com/embed/${d.embed_id}?autoplay=1" title="${escapeHtml(d.judul)}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`);
                } else {
                    $('#video_player_container').html(`<div class="p-5 text-center text-white"><p>Link video:</p><a href="${d.url_video}" target="_blank" class="btn btn-danger btn-sm"><i class="mdi mdi-open-in-new me-1"></i>Tonton di Sumber Asli</a></div>`);
                }

                $('#modal_tonton_video').modal('show');

                // Increment view
                $.post('konten/ex/feed/view_counter', { id: id, tipe: 'video' });
            }
        }
    });
}

function stop_video_player() {
    $('#video_player_container').html('');
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
