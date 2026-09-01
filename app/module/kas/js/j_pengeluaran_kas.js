$(document).ready(function () {
    tampil_th();
    tampil_jenjang();

    e = $("#e").flatpickr({
        dateFormat: "Y-m-d",
        static: true,
    });
    ee = $("#ee").flatpickr({
        dateFormat: "Y-m-d",
        static: true,
    });
});

ctr_kfg_bayar = 'kas/ex/pengeluaran_kas/';

function tampil_th() {
    $("#tahun").load(ctr_kfg_bayar+'send_th');
}

function tampil_jenjang() {
    $("#jenjang").load(ctr_kfg_bayar+'send_jenjang');
}

function myNewFunction(){
    var tahun=$('#pilih_tahun').val();
    var jenjang=$('#pilih_jenjang').val();
    $("#ambil_dari_select").load(ctr_kfg_bayar+'send_ambil_dari/?tahun=' + tahun + "&jenjang=" + jenjang);
    $("#ambil_jenis_select").load(ctr_kfg_bayar+'send_ambil_jenis/?tahun=' + tahun + "&jenjang=" + jenjang);
}

function tamtag() {
    var tahun=$('#pilih_tahun').val();
    var jenjang=$('#pilih_jenjang').val();
    $("#tag_pem").modal("show");
    $("#tam_jenis_pengeluaran").load(ctr_kfg_bayar+'send_jenis_pengeluaran/?tahun=' + tahun + "&jenjang=" + jenjang);
    $("#edd").hide();
    $("#hdd").hide();
    $("#edd_baru").hide();
    $("#add").show();
    
}

function simpan_kat(){
    dt={};
    dt.tahun = $('#pilih_tahun').val();
    dt.jenjang = $('#pilih_jenjang').val();
    dt.nama_id = $('#nama_jns_pengeluaran').val();
    alertify.confirm('Konfirmasi', 'Simpan?', function(){
        ajx=$.post(ctr_kfg_bayar+'simpan_kat',dt,function(raw)
        {
            tamtag();
            alertify.success('Berhasil Ditambah');
            $("#nama_jns_pengeluaran").val(null);
            $("#id_jenis_pengeluaran").val(null);
        });
        load_show(ajx);
    },
    function(){});
}

function theare_kat(){
    $("#add").hide();
    $("#edd").show();
    $("#hdd").show();
    $("#edd_baru").show();
    var table = document.getElementById('tabse'),
    cells = table.getElementsByTagName('tr');

    for (var i=0,len=cells.length; i<len; i++){
        cells[i].onclick = function(){
            document.getElementById('id_jenis_pengeluaran').value=this.cells[1].innerText;
            document.getElementById('nama_jns_pengeluaran').value=this.cells[4].innerText;
        }
        
    }
}

function edit_kat(){
    dt={};
    dt.tahun = $('#pilih_tahun').val();
    dt.jenjang = $('#pilih_jenjang').val();
    dt.nama_id = $('#nama_jns_pengeluaran').val();
    dt.id_jenis_pengeluaran = $('#id_jenis_pengeluaran').val();
    alertify.confirm('Konfirmasi', 'Edit?', function(){
        ajx=$.post(ctr_kfg_bayar+'edit_kat',dt,function(raw)
        {
            tamtag();
            alertify.success('Berhasil Diedit');
            $("#nama_jns_pengeluaran").val(null);
            $("#id_jenis_pengeluaran").val(null);
        });
        load_show(ajx);
    },
    function(){});
}

function hapus_kat(){
    dt={};
    dt.tahun = $('#pilih_tahun').val();
    dt.jenjang = $('#pilih_jenjang').val();
    dt.nama_id = $('#nama_jns_pengeluaran').val();
    dt.id_jenis_pengeluaran = $('#id_jenis_pengeluaran').val();
    alertify.confirm('Konfirmasi', 'Yakin Hapus?', function(){
        ajx=$.post(ctr_kfg_bayar+'hapus_kat',dt,function(raw)
        {
            tamtag();
            alertify.success('Berhasil Dihapus');
            $("#nama_jns_pengeluaran").val(null);
            $("#id_jenis_pengeluaran").val(null);
        });
        load_show(ajx);
    },
    function(){});
}

function baru_saja(){
    $("#add").show();
    $("#edd").hide();
    $("#hdd").hide();
    $("#edd_baru").hide();
    $("#nama_jns_pengeluaran").val(null);
    $("#id_jenis_pengeluaran").val(null);
}

function myNewFunctionran(sel){
    var tahun=$('#pilih_tahun').val();
    var jenjang=$('#pilih_jenjang').val();
    var awal=$('#e').val();
    var akhir=$('#ee').val();
    var id = sel.options[sel.selectedIndex].value;
    var ambil_jenis=$('#myselects_thj_b').val();
    $.get(ctr_kfg_bayar+'pemasukan/?id=' + id + "&jenjang=" + jenjang + "&tahun=" + tahun,function(jumlah_masuk)
        {
             $("#pemasukan").val(jumlah_masuk);
        });
    $.get(ctr_kfg_bayar+'pengeluaran/?id=' + id + "&jenjang=" + jenjang + "&tahun=" + tahun,function(jumlah_keluar)
        {
             $("#pengeluaran").val(jumlah_keluar);
        });
    $.get(ctr_kfg_bayar+'saldo/?id=' + id + "&jenjang=" + jenjang + "&tahun=" + tahun,function(saldo_semua)
        {
             $("#saldo").val(saldo_semua);
        });
    $("#tampilkan_data").load(ctr_kfg_bayar+'tampilkan_data/?tahun=' + tahun + "&jenjang=" + jenjang + "&page=" + 1 + "&awal=" + awal + "&akhir=" + akhir + "&id=" + id + "&ambil_jenis=" + ambil_jenis);
    $("#id_pengeluaran").val(null);
    $("#nominal").val(null);
    $("#nama_pengeluaran").val(null);
    $("#deskripsi").val(null);
}

function myNewFunctionhal(sel)
{
    var tahun=$('#pilih_tahun').val();
    var jenjang=$('#pilih_jenjang').val();
    var awal=$('#e').val();
    var akhir=$('#ee').val();
    var ambil_jenis=$('#myselects_thj_b').val();
    var id=$('#myselects_thj_a').val();
    pages = sel.options[sel.selectedIndex].value;
    $("#tampilkan_data").load(ctr_kfg_bayar+"tampilkan_data/?page=" + pages+"&tahun="+tahun+"&jenjang="+jenjang + "&awal=" + awal + "&akhir=" + akhir + "&id=" + id + "&ambil_jenis=" + ambil_jenis);
    return false;
}

function tampil_tanggal(){
    var tahun=$('#pilih_tahun').val();
    var jenjang=$('#pilih_jenjang').val();
    var awal=$('#e').val();
    var akhir=$('#ee').val();
    var ambil_jenis=$('#myselects_thj_b').val();
    var id=$('#myselects_thj_a').val();
    $("#tampilkan_data").load(ctr_kfg_bayar+"tampilkan_data/?page=" + 1+"&tahun="+tahun+"&jenjang="+jenjang + "&awal=" + awal + "&akhir=" + akhir+ "&id=" + id + "&ambil_jenis=" + ambil_jenis);
}

function tampil_semua(){
    var tahun=$('#pilih_tahun').val();
    var jenjang=$('#pilih_jenjang').val();
    var awal = '';
    var akhir = '';
    $("#e").val(null);
    $("#ee").val(null);
    var ambil_jenis=$('#myselects_thj_b').val();
    var id=$('#myselects_thj_a').val();
    $("#tampilkan_data").load(ctr_kfg_bayar+"tampilkan_data/?page=" + 1+"&tahun="+tahun+"&jenjang="+jenjang + "&awal=" + awal + "&akhir=" + akhir+ "&id=" + id + "&ambil_jenis=" + ambil_jenis);
}

function simpan_pengeluaran(){
    dt={};
    dt.tahun = $('#pilih_tahun').val();
    dt.jenjang = $('#pilih_jenjang').val();
    dt.nama_pengeluaran = $('#nama_pengeluaran').val();
    dt.deskripsi = $('#deskripsi').val();
    dt.nominal = $('#nominal').val();
    dt.kat = $('#myselects_thj_a').val();
    dt.jns = $('#myselects_thj_b').val();
    dt.saldo = $('#saldo').val();
    dt.saldoin = parseInt(dt.saldo);
    dt.nominalin = parseInt(dt.nominal);
    if(dt.kat==""){
        alertify.error('Pilih pengambilan dari');
        return false;   
    }else if(dt.tahun==""){
        alertify.error('Pilih tahun ajaran');
        return false;   
    }else if(dt.nominalin > dt.saldoin){
        alertify.error('Saldo Tidak Cukup');
        return false;
    }else if(dt.nominalin < dt.saldoin){
        alertify.confirm('Konfirmasi', 'Yakin Disimpan?', function(){
            ajx=$.post(ctr_kfg_bayar+'simpan_pengeluaran',dt,function(raw)
            {
                tampil_semua();
                alertify.success('Berhasil Ditambah');
                $("#nama_pengeluaran").val(null);
                $("#deskripsi").val(null);
                $("#nominal").val(null);
            });
            load_show(ajx);
        },
        function(){});
    }

}

function edit_pengeluaran(){
    dt={};
    dt.tahun = $('#pilih_tahun').val();
    dt.jenjang = $('#pilih_jenjang').val();
    dt.nama_pengeluaran = $('#nama_pengeluaran').val();
    dt.deskripsi = $('#deskripsi').val();
    dt.nominal = $('#nominal').val();
    dt.kat = $('#myselects_thj_a').val();
    dt.jns = $('#myselects_thj_b').val();
    dt.saldo = $('#saldo').val();
    dt.pengeluaran = $('#pengeluaran').val();
    dt.pemasukan = $('#pemasukan').val();
    dt.nominal_hitung = $('#nominal_hitung').val();
    dt.jumlah = parseInt(dt.nominal_hitung) + parseInt(dt.nominal);
    dt.saldo_real = parseInt(dt.pemasukan) - parseInt(dt.jumlah);
    dt.id_pengeluaran = $('#id_pengeluaran').val();
    
    if(dt.kat==""){
        alertify.error('Pilih pengambilan dari');
        return false;   
    }else if(dt.tahun==""){
        alertify.error('Pilih tahun ajaran');
        return false;   
    }else if(dt.jumlah > dt.pemasukan){
        alertify.error('Saldo Tidak Cukup');
        return false;
    }else if(dt.jumlah < dt.pemasukan){
        alertify.confirm('Konfirmasi', 'Yakin Dirubah?', function(){
            ajx=$.post(ctr_kfg_bayar+'edit_pengeluaran',dt,function(raw)
            {
                tampil_semua();
                alertify.success('Berhasil Dirubah');
                $("#nama_pengeluaran").val(null);
                $("#deskripsi").val(null);
                $("#nominal").val(null);
                $("#pengeluaran").val(dt.jumlah);
                $("#saldo").val(dt.saldo_real);
            });
            load_show(ajx);
        },
        function(){});
    }
}

function hapus_pengeluaran(){
    dt={};
    dt.tahun = $('#pilih_tahun').val();
    dt.jenjang = $('#pilih_jenjang').val();
    dt.nama_pengeluaran = $('#nama_pengeluaran').val();
    dt.deskripsi = $('#deskripsi').val();
    dt.nominal = $('#nominal').val();
    dt.kat = $('#myselects_thj_a').val();
    dt.jns = $('#myselects_thj_b').val();
    dt.saldo = $('#saldo').val();
    dt.pengeluaran = $('#pengeluaran').val();
    dt.pemasukan = $('#pemasukan').val();
    dt.nominal_hitung = $('#nominal_hitung').val();
    dt.jumlah = parseInt(dt.nominal_hitung) + parseInt(dt.nominal);
    dt.saldo_real = parseInt(dt.pemasukan) - parseInt(dt.jumlah);
    dt.id_pengeluaran = $('#id_pengeluaran').val();
    
    alertify.confirm('Konfirmasi', 'Yakin Dihapus?', function(){
        ajx=$.post(ctr_kfg_bayar+'hapus_pengeluaran',dt,function(raw)
        {
            tampil_semua();
            alertify.success('Berhasil Dihapus');
            $("#nama_pengeluaran").val(null);
            $("#deskripsi").val(null);
            $("#nominal").val(null);
            $("#pengeluaran").val(null);
            $("#saldo").val(null);
            $("#pemasukan").val(null);
        });
        load_show(ajx);
    },
    function(){});
}

function theare(){
    $("#simpan").hide();
    $("#baru").show();
    $("#edit").show();
    $("#hapus").show();
    var table = document.getElementById('tab'),
    cells = table.getElementsByTagName('tr');

    for (var i=0,len=cells.length; i<len; i++){
        cells[i].onclick = function(){
            document.getElementById('id_pengeluaran').value=this.cells[1].innerText;
            document.getElementById('nominal').value=this.cells[8].innerText;
            document.getElementById('nama_pengeluaran').value=this.cells[6].innerText;
            document.getElementById('deskripsi').value=this.cells[7].innerText;
            var pengeluaran = $('#pengeluaran').val();
            var nominal = $('#nominal').val();
            var hitung = parseInt(pengeluaran) - parseInt(nominal);
            $("#nominal_hitung").val(hitung);
        }
        
    }
}

function baru_pengeluaran(){
    $("#simpan").show();
    $("#baru").hide();
    $("#edit").hide();
    $("#hapus").hide();
    $("#id_pengeluaran").val(null);
    $("#nominal").val(null);
}