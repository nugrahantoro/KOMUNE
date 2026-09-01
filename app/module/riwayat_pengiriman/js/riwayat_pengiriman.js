controller = 'riwayat_pengiriman/ex/riwayat_pengiriman/';
var modale = document.getElementById("myModale");


$(document).ready(function(){
    tampil_tahun();

    var dateObj = new Date();
    var month = dateObj.getUTCMonth() + 1; //months from 1-12
    var day = dateObj.getUTCDate();
    var year = dateObj.getUTCFullYear();

    $("#data_riwayat_pengiriman").load(ctr_kfg_bayar+'data_histori_pengiriman/?page=1&periode='+year+"-"+month);

    $("#total_harga_perbulan").load(ctr_kfg_bayar+'total_harga_perbulan/?periode='+year+"-"+month);

    $("#total_harga_perhari").load(ctr_kfg_bayar+'total_harga_perhari');

    $("#total_berhasil_perbulan").load(ctr_kfg_bayar+'total_berhasil_perbulan/?periode='+year+"-"+month);

    $("#total_berhasil_perhari").load(ctr_kfg_bayar+'total_berhasil_perhari');

    $("#total_gagal_perbulan").load(ctr_kfg_bayar+'total_gagal_perbulan/?periode='+year+"-"+month);

    $("#total_gagal_perhari").load(ctr_kfg_bayar+'total_gagal_perhari');
});


ctr_kfg_bayar = 'riwayat_pengiriman/ex/riwayat_pengiriman/';
function tampil_tahun(){
    $("#data_tahun").load(ctr_kfg_bayar+'tahun');
}

function myNewFunction(){
    var periode = $('#myselects_th').val();

    $("#data_riwayat_pengiriman").load(ctr_kfg_bayar+'data_histori_pengiriman/?page=1&periode='+periode);

    $("#total_harga_perbulan").load(ctr_kfg_bayar+'total_harga_perbulan/?periode='+periode);

    $("#total_berhasil_perbulan").load(ctr_kfg_bayar+'total_berhasil_perbulan/?periode='+periode);

    $("#total_gagal_perbulan").load(ctr_kfg_bayar+'total_gagal_perbulan/?periode='+periode);
}

function myNewFunctionPage(sel) {
    pages = sel.options[sel.selectedIndex].value;
    var dateObj = new Date();
    var month = dateObj.getUTCMonth() + 1; //months from 1-12
    var day = dateObj.getUTCDate();
    var year = dateObj.getUTCFullYear();

    var periode = $('#myselects_th').val();

    $("#data_riwayat_pengiriman").load(ctr_kfg_bayar+'data_histori_pengiriman/?page='+pages+"&periode="+periode);
    return false;
}

function cetak_histori_pengiriman_excel(){
    var periode = $('#myselects_th').val();

    window.open(ctr_kfg_bayar+'cetak_histori_pengiriman_excel/?periode='+periode);
}