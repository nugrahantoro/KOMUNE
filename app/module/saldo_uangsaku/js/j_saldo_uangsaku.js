$(document).ready(function(){
    tampil_thn();
    tampil_kls_null();
    tampil_default();

    if ($("#sampai_tgl").length) {
        $("#sampai_tgl").flatpickr({
            dateFormat: "Y-m-d",
            static: true
        });
    }
});

let ctr_saku = 'saldo_uangsaku/ex/saldo_uangsaku/';
let selected_warga_id = '';

function tampil_thn(){
    $("#tampil_thn").load(ctr_saku + 'send_th/');
}

function tampil_kls_null(){
    $('#tam_kelas').load(ctr_saku + 'send_kls_tag/');
}

function tampil_kls(){
    let th = $('#myselects_th').val() || '';
    $('#tam_kelas').load(ctr_saku + 'send_kls_tag/?th=' + encodeURIComponent(th));
}

function tampil_default(){
    $('#tampil_masuk').html('<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-danger me-2"></div>Memuat saldo dompet digital...</div>');
    $('#tampil_masuk').load(ctr_saku + 'laporan_saldo/');
}

function flag(){
    let sampai_tgl = $('#sampai_tgl').val() || '';
    let th = $('#myselects_th').val() || '';
    let id_kls = $('#myselects_kls').val() || '';
    
    $('#tampil_masuk').html('<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-danger me-2"></div>Memuat data...</div>');
    $('#tampil_masuk').load(ctr_saku + 'laporan_saldo/?sampai_tgl=' + encodeURIComponent(sampai_tgl) + '&th=' + encodeURIComponent(th) + '&id_kls=' + encodeURIComponent(id_kls) + '&nis_from_idsiswa=' + encodeURIComponent(selected_warga_id));
}

function flag_cari(){
    $('#tag_cari').modal('show');
    $('#edValue').val('');
    $('#tampil_cari').html('<div class="text-muted text-center py-3 font-size-12">Ketik nama warga untuk mencari...</div>');
    setTimeout(function(){
        document.getElementById("edValue").focus();
    }, 400);
}

function edValueKeyPress(){
    let nama = $('#edValue').val().trim();
    let th = $('#myselects_th').val() || '';
    if (nama.length >= 1) {
        $("#tampil_cari").load(ctr_saku + 'cari_siswa/?nama=' + encodeURIComponent(nama) + '&th=' + encodeURIComponent(th));
    } else {
        $("#tampil_cari").html('<div class="text-muted text-center py-3 font-size-12">Ketik nama warga untuk mencari...</div>');
    }
}

function pilih_warga_cari(id_siswa, nama_warga){
    selected_warga_id = id_siswa;
    $('#tag_cari').modal('hide');
    $('#selected_warga_badge').text('Filter Warga: ' + nama_warga).show();
    flag();
}

function reset_saldo_filter(){
    selected_warga_id = '';
    $('#myselects_th').val('');
    $('#myselects_kls').val('');
    $('#sampai_tgl').val('');
    $('#selected_warga_badge').hide();
    tampil_kls_null();
    tampil_default();
}

function rinci_warga(id_siswa){
    $('#tam_sald').html('<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-danger me-2"></div>Memuat rincian mutasi...</div>');
    $('#tam_saldi').modal('show');
    $('#tam_sald').load(ctr_saku + 'laporan_saldo_persis/?id_siswa=' + encodeURIComponent(id_siswa));
}
