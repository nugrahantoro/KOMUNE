$(document).ready(function () {
    tampil_th();
    tampil_jenjang();
    // var th = document.querySelector('[id="pilih_tahun"]').value;
    // var id_jenjang = document.querySelector('[id="pilih_jenjang"]').value;
    $("#tampil_data").load(ctr_kfg_bayar+'send_laporan_default');
});

ctr_kfg_bayar = 'kas/ex/kas/';
function langsung() {
    var th = encodeURI(document.querySelector('[id="pilih_tahun"]').value);
    var id_jenjang = encodeURI(document.querySelector('[id="pilih_jenjang"]').value);
    $("#tampil_data").load(ctr_kfg_bayar+'send_laporan_default/?th=' + th + "&id_jenjang=" + id_jenjang);
}

function tampil_th() {
    $("#tahun").load(ctr_kfg_bayar+'send_th');
}
function tampil_jenjang() {
    $("#jenjang").load(ctr_kfg_bayar+'send_jenjang');
}
function myNewFunction(sel) {
    var th = document.querySelector('[id="pilih_tahun"]').value;
    var id_jenjang = document.querySelector('[id="pilih_jenjang"]').value;
    $("#tampil_data").load(ctr_kfg_bayar+'send_laporan/?th=' + th + "&id_jenjang=" + id_jenjang);
}

function myNewFunctionjang(sel) {
    var th = document.querySelector('[id="pilih_tahun"]').value;
    var id_jenjang = document.querySelector('[id="pilih_jenjang"]').value;
    $("#tampil_data").load(ctr_kfg_bayar+'send_laporan/?th=' + th + "&id_jenjang=" + id_jenjang);
}

function cetak_estimasi() {
    var th = document.querySelector('[id="pilih_tahun"]').value;
    window.open(ctr_kfg_bayar+'cetak_estimasi/?th='+ th);
}

function cetak_pendapatan() {
    var th = document.querySelector('[id="pilih_tahun"]').value;
    window.open(ctr_kfg_bayar+'cetak_pendapatan/?th=' + th);
}

function cetak_pengeluaran() {
    var th = document.querySelector('[id="pilih_tahun"]').value;
    window.open(ctr_kfg_bayar+'cetak_pengeluaran/?th=' + th);
}

function cetak_saldo() {
    var th = document.querySelector('[id="pilih_tahun"]').value;
    window.open(ctr_kfg_bayar+'cetak_saldo/?th=' + th);
}
