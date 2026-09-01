function load_audit_logs() {
    let aksi = $('#filter_log_aksi').val();
    let modul = $('#filter_log_modul').val();
    let search = $('#search_log').val();

    $('#tbody_audit_logs').html('<tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-danger me-2"></div>Memuat log aktivitas...</td></tr>');

    $.ajax({
        url: 'audit_log/get_logs',
        type: 'POST',
        dataType: 'json',
        data: {
            aksi: aksi,
            modul: modul,
            search: search,
            limit: 100
        },
        success: function(res) {
            if (res && res.status === 'success') {
                render_table_logs(res.data);
                $('#info_total_logs').text('Menampilkan ' + res.total + ' log aktivitas terbaru');
            } else {
                $('#tbody_audit_logs').html('<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat aktivitas yang tercatat.</td></tr>');
            }
        },
        error: function(xhr, status, err) {
            console.error("Audit log load error:", status, err, xhr.responseText);
            $('#tbody_audit_logs').html('<tr><td colspan="7" class="text-center py-4 text-danger"><i class="mdi mdi-alert-circle-outline me-1"></i> Gagal memuat log aktivitas dari server.</td></tr>');
        }
    });
}

function render_table_logs(list) {
    if (!list || list.length === 0) {
        $('#tbody_audit_logs').html('<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat aktivitas yang tercatat.</td></tr>');
        return;
    }

    let html = '';
    $.each(list, function(idx, item) {
        let badgeColor = 'bg-light text-secondary';
        if (item.aksi === 'CREATE') badgeColor = 'bg-light text-success border';
        else if (item.aksi === 'UPDATE') badgeColor = 'bg-light text-primary border';
        else if (item.aksi === 'SOFT_DELETE' || item.aksi === 'HARD_DELETE') badgeColor = 'bg-light text-danger border';
        else if (item.aksi === 'LOGIN') badgeColor = 'bg-light text-info border';
        else if (item.aksi === 'PAYMENT') badgeColor = 'bg-light text-warning border';

        let ipInfo = (item.ip_address ? item.ip_address : '-') + (item.user_agent ? `<br><small class="text-muted" style="max-width: 150px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${escapeHtml(item.user_agent)}">${escapeHtml(item.user_agent)}</small>` : '');

        html += `
            <tr>
                <td class="text-center text-muted font-size-12">#${item.id}</td>
                <td><span class="badge ${badgeColor} font-size-11 fw-semibold">${escapeHtml(item.aksi)}</span></td>
                <td><span class="badge bg-light text-dark border font-size-11">${escapeHtml(item.modul)}</span></td>
                <td>
                    <div class="fw-semibold text-dark">${escapeHtml(item.nama_user || 'System')}</div>
                    <small class="text-muted">ID: ${escapeHtml(item.user_id || '-')}</small>
                </td>
                <td class="text-dark">${escapeHtml(item.keterangan || '-')}</td>
                <td class="font-size-12">${ipInfo}</td>
                <td class="text-muted font-size-12">${item.created_at}</td>
            </tr>
        `;
    });

    $('#tbody_audit_logs').html(html);
}

function reset_filter_logs() {
    $('#filter_log_aksi').val('');
    $('#filter_log_modul').val('');
    $('#search_log').val('');
    load_audit_logs();
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
