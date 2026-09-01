$(document).ready(function () {
    tampil_tahun();
    jenjang_null();
    // bulan();
    tampil_kelas_null();
    tampil_user();
    tampil_trans_null();
    tampil_metode();
    tampil_default();

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var yyyy = today.getFullYear();

    today = yyyy + '-' + mm + '-' + dd;
    $('#e').val(today);
    $('#ee').val(today);

    e = $("#e").flatpickr({
        dateFormat: "Y-m-d",
        static: true,
    });
    ee = $("#ee").flatpickr({
        dateFormat: "Y-m-d",
        static: true,
    });

       $("#bulan").load(ctr_kfg_bayar+'bulan');

});



ctr_kfg_bayar = 'laporan_tabungan_bulan/';

var modalimport = document.getElementById("myModaldata");
var modalinputtrans = document.getElementById("myModalinputtrans");
var modalkelompok = document.getElementById("myModalkelompok");

function tampil_tahun() {
    $("#tam_tahun").load(ctr_kfg_bayar+'send_tahun');
}
function jenjang_null() {
    $("#jenjang").load(ctr_kfg_bayar+'jenjang_null');
}
// function bulan() {
//     $("#bulan").load(ctr_kfg_bayar+'bulan');
// }

function tampil_kelas_null() {
    $("#tampil_kelas").load(ctr_kfg_bayar+'send_kelas_null', function () {
        const select = new Choices("#myselects_thdd", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
    });
}

function flag_laporan(){
    // loading.show('Menunggu');
    var th = document.querySelector('[id="myselects_th"]').value;
    var kls = document.querySelector('[id="myselects_thj"]').value;
    var bln = document.querySelector('[id="myselects_tho"]').value;
    //$("#tampil_laporan").load("../controller/tabungan.php/send_laporan_setoran/?th="+th+"&kls="+kls+"&bln="+bln);
    $("#tampil_laporan").load(ctr_kfg_bayar+'send_laporan_setoran/?th='+th+"&kls="+kls+"&bln="+bln, function(){
            loading.hide();
            
            });
}

function tampil_user() {
    $("#tampil_user").load(ctr_kfg_bayar+'send_user_null', function () {
        const select = new Choices("#myselects_thc", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
    });
}
function tampil_trans_null() {
    $("#tampil_trans").load(ctr_kfg_bayar+'send_trans_null', function () {
        const select = new Choices("#myselects_thd", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
    });
}

function tampil_metode() {
    $("#tampil_metode").load(ctr_kfg_bayar+'send_metode_null', function () {
        const select = new Choices("#myselects_thm", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
    });
}

function myNewFunctionth(sel) {
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    pages = sel.options[sel.selectedIndex].value;
    // if (th == "0") {
    //     tampil_null();
    //     jenjang_null();
    //     bulan();
    //     tampil_user();
    //     tampil_kelas_null();
    //     tampil_trans_null();
    //     return false;
    // } else {
    //     // $("#tampil_kelas").load("/portingsekolahkuapp/laporan/send_kelas_null");
    //     // $("#jenjang").load(ctr_kfg_bayar+'send_jenjang');
    //     // $("#tampil_user").load("/portingsekolahkuapp/laporan/send_user_null/?id_jenjang=" + id_jenjang, function () {
    //     //     const select = new Choices("#myselects_thc", {
    //     //         removeItems: true,
    //     //         removeItemButton: true,
    //     //         maxItemCount: 5,
    //     //     });
    //     // });
    //     // $("#tampil_trans").load(ctr_kfg_bayar+'send_trans_null/?id=' + th + "&id_jenjang=" + id_jenjang, function () {
    //     //     const select = new Choices("#myselects_thd", {
    //     //         removeItems: true,
    //     //         removeItemButton: true,
    //     //         maxItemCount: 5,
    //     //     });
    //     // });
    //     // $("#tampil_metode").load(ctr_kfg_bayar+'send_metode_null', function () {
    //     //     const select = new Choices("#myselects_thm", {
    //     //         removeItems: true,
    //     //         removeItemButton: true,
    //     //         maxItemCount: 5,
    //     //     });
    //     // });
    //     return false;
    // }
}

// function myNewFunctionjang(sel) {
//     var th = document.querySelector('[id="myselects_th"]').value;
//     var id_jenjang = encodeURI($("#myselects_thj").val());
//     var a = document.querySelector('[id="e"]').value;
//     var b = document.querySelector('[id="ee"]').value;
//     var c = encodeURI($("#myselects_thc").val());
//     var d = encodeURI($("#myselects_thd").val());
//     var id_kelas = encodeURI($("#myselects_thdd").val());
//     pages = sel.options[sel.selectedIndex].value;
//     if (th == "0") {
//         tampil_user();
//         return false;
//     }
// }

function flag() {
    $("#tampil_default").hide();
    var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;
    var c = encodeURI($("#myselects_thc").val());
    var d = encodeURI($("#myselects_thd").val());
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = encodeURI($("#myselects_thj").val());
    var id_kelas = encodeURI($("#myselects_thdd").val());
    var metode = encodeURI($("#myselects_thm").val());
    $("#tampil_data").load(ctr_kfg_bayar+'laporan_harian/?a=' + a + "&b=" + b + "&th=" + th + "&c=" + c + "&d=" + d + "&id_jenjang=" + id_jenjang + "&id_kelas=" + id_kelas + "&metode=" + metode);
}

function cethar() {
    var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;
    var c = encodeURI($("#myselects_thc").val());
    var d = encodeURI($("#myselects_thd").val());
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = encodeURI($("#myselects_thj").val());
    var id_kelas = encodeURI($("#myselects_thdd").val());
    window.open(ctr_kfg_bayar+'cetak_laporan_harian/?a=' + a + "&b=" + b + "&th=" + th + "&c=" + c + "&d=" + d + "&id_jenjang=" + id_jenjang + "&id_kelas=" + id_kelas);
}

function flag_kategori() {
    $("#tampil_default").hide();
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;
    $("#tampil_data").load(ctr_kfg_bayar+'total_detil/?a=' + a + "&b=" + b + "&th=" + th + "&id_jenjang=" + id_jenjang);
}


function laporan_harian_list() {
    var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;
    var c = encodeURI($("#myselects_thc").val());
    var d = encodeURI($("#myselects_thd").val());
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = encodeURI($("#myselects_thj").val());
    var id_kelas = encodeURI($("#myselects_thdd").val());
    $("#tampil_data").load(ctr_kfg_bayar+'laporan_harian/?a=' + a + "&b=" + b + "&th=" + th + "&c=" + c + "&d=" + d + "&id_jenjang=" + id_jenjang + "&id_kelas=" + id_kelas);
}

function laporan_harian_table() {
    var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;
    var c = encodeURI($("#myselects_thc").val());
    var d = encodeURI($("#myselects_thd").val());
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = encodeURI($("#myselects_thj").val());
    var id_kelas = encodeURI($("#myselects_thdd").val());
    if (th == "0") {
        alert("Fitur ini bisa dilihat per tahun ajaran, silahkan pilih tahun ajaran terlebih dahulu");
        return false;
    } else {
        $("#tampil_data").load(ctr_kfg_bayar+'laporan_harian_table/?a=' + a + "&b=" + b + "&th=" + th + "&c=" + c + "&d=" + d + "&id_jenjang=" + id_jenjang + "&id_kelas=" + id_kelas);
    }
}
function download_excel_perkelas(id_kel) {
    var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;
    var c = encodeURI($("#myselects_thc").val());
    var d = encodeURI($("#myselects_thd").val());
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = encodeURI($("#myselects_thj").val());
    window.open(ctr_kfg_bayar+'cetak_perkelas_aja/?a=' + a + "&b=" + b + "&th=" + th + "&c=" + c + "&d=" + d + "&id_jenjang=" + id_jenjang + "&id_kel=" + id_kel);
}

function konpig() {
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    $("#section_kategori_laporan").load(ctr_kfg_bayar+'send_transaksi/?th=' + th + "&id_jenjang=" + id_jenjang + "&page=" + 1);
    $("#konpig").modal("show");
    // form_akses_kategori();
}

// function form_akses_kategori()
// {
// 	ajx = $.post(controller+'form_akses_kategori',function(raw)
// 	{
// 		if(isJson(raw))
// 		{
// 			dt=JSON.parse(raw);
// 			$('#aj_kode').html(dt.aj_kode);
// 			$('#aj_jenjang').html(dt.aj_jenjang);
// 			data_akses_jenjang();
// 		}
// 	});
// 	setTimeout(function(){load_ajx(ajx,'#modal_akses_jenjang .modal-body')},225);

// }

function aksitrans() {
    $("#konpig").modal("hide");
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    var table = document.getElementById("tablizz"),
        cells = table.getElementsByTagName("tr");
    for (var i = 1, len = cells.length; i < len; i++) {
        cells[i].onclick = function () {
            var id_jenis_transaksi = this.cells[1].innerText;
            var id_kat_laporan = this.cells[2].innerText;
            document.getElementById("id_jenis_transaksi").value = this.cells[1].innerText;
            $("#aksitrans").load(ctr_kfg_bayar+'send_kategori_select_lap/?id=' + id_jenis_transaksi + "&th=" + th + "&id_jenjang=" + id_jenjang + "&id_kat_laporan=" + id_kat_laporan);
            $("#aksitransi").modal("show");
        };
    }
}

function simlapo() {
    $("#aksitransi").modal("hide");
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    var id_jenis_transaksi = document.querySelector('[id="id_jenis_transaksi"]').value;
    var id_kat_laporan = document.querySelector('[id="myselects_ktg_lap"]').value;
    $.ajax({
        type: "GET",
        url: ctr_kfg_bayar+'simlapoup/?id_jenis_transaksi=' + id_jenis_transaksi + "&id_kat_laporan=" + id_kat_laporan,
        success: function () {
            $("#konpig_data").load("/portingsekolahkuapp/laporan/send_transaksi/?page=" + 1 + "&th=" + th + "&id_jenjang=" + id_jenjang);
        },
    });
}

function popkelompok() {
    $("#konpig").modal("hide");
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    $("#kelompok_lap").load(ctr_kfg_bayar+'send_kelompok_lap/?th=' + th + "&id_jenjang=" + id_jenjang);
    $("#kelompok_lapi").modal("show");
}

function simpan_kel_lap() {
    var th = document.querySelector('[id="myselects_th"]').value;
    var nama = $("#nama_kelompok_lap").val();
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    $.ajax({
        type: "GET",
        url: ctr_kfg_bayar+'simpan_kel_lap/?nama=' + nama + "&th=" + th + "&id_jenjang=" + id_jenjang,
        success: function (response) {
            alert(response);
            $("#nama_kelompok_lap").val(null);
            $("#kelompok_lap").load(ctr_kfg_bayar+'send_kelompok_lap/?th=' + th + "&id_jenjang=" + id_jenjang);
        },
    });
}

function hapus_kelompok_lap() {
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    var table = document.getElementById("tabmens"),
        cells = table.getElementsByTagName("tr");

    for (var i = 0, len = cells.length; i < len; i++) {
        cells[i].onclick = function () {
            var id = this.cells[1].innerText;
            if (confirm("Apakah anda yakin menghapus data?")) {
                $.ajax({
                    type: "GET",
                    url: "/portingsekolahkuapp/laporan/hapus_kelompok_lap/?id=" + id,
                    success: function (response) {
                        alert(response);
                        $("#kelompok_lap").load(ctr_kfg_bayar+'send_kelompok_lap/?th=' + th + "&id_jenjang=" + id_jenjang);
                    },
                });
            }
        };
    }
}

function edit_kelompok_lap() {
    var table = document.getElementById("tabmens"),
        cells = table.getElementsByTagName("tr");

    for (var i = 1, len = cells.length; i < len; i++) {
        cells[i].onclick = function () {
            var id = this.cells[1].innerText;
            var nama = this.cells[2].inerText;
            $("#keldua").show();
            document.getElementById("id_kelompok_laporan").value = id;
            document.getElementById("nama_kelompok_lap").value = nama;
            document.getElementById("btnaddkelompoklap").style.visibility = "hidden";
            document.getElementById("btneditkelompoklap").style.visibility = "visible";
        };
    }
}

function simpan_edit_kel_lap() {
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj"]').value;
    var id = $("#id_kelompok_laporan").val();
    var nama = $("#nama_kelompok_lap").val();
    $.ajax({
        type: "GET",
        url: ctr_kfg_bayar+'edit_kelompok_lap/?id=' + id + "&nama=" + nama,
        success: function (response) {
            alert(response);
            $("#nama_kelompok_lap").val(null);
            $("#kelompok_lap").load(ctr_kfg_bayar+'send_kelompok_lap/?th=' + th + "&id_jenjang=" + id_jenjang);
        },
    });
}

function flag_estimasi() {
    $("#tampil_default").hide();
    var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;
    var d = encodeURI($("#myselects_thd").val());
    var th = document.querySelector('[id="myselects_th"]').value;
    var id_jenjang = encodeURI($("#myselects_thj").val());
    var id_kelas = encodeURI($("#myselects_thdd").val());
    // $("#e").val(null);
    // $("#ee").val(null);
    
    $("#tampil_data").load(ctr_kfg_bayar+'laporan_harian_estimasi/?th=' + th + "&d=" + d + "&id_jenjang=" + id_jenjang + "&id_kelas=" + id_kelas);
}

function tamtag() {
    $("#tam_tahun_tag").load(ctr_kfg_bayar+'send_tahun_tag');
    $("#jenjang_tag").load(ctr_kfg_bayar+'send_jenjang_tag');
    $("#tag_pem").modal("show");
}

function lap_tampil() {
    var th = document.querySelector('[id="myselects_th_tag"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj_tag"]').value;
    var kls = encodeURI($("#myselects_kls").val());
    var trans = encodeURI($("#myselects_thdb").val());
    if (kls == "" || kls == "null") {
        kls = 0;
    }
    if (trans == "" || trans == "null") {
        trans = 0;
    }

    $("#tag_pem").modal("hide");
    $("#per_tahun").load(ctr_kfg_bayar+'send_laporan_tag_new/?th=' + th + "&id_jenjang=" + id_jenjang + "&kls=" + kls + "&trans=" + trans, function () {
        const select = new Choices("#myselects_ran", {
            removeItems: true,
            removeItemButton: true,
            width: 100,
            maxItemCount: 5,
        });
    });
    $("#tag_lap").modal("show");
}

function myNewFunctionjangtag(sel) {
    var th = document.querySelector('[id="myselects_th_tag"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj_tag"]').value;
    $("#tam_kelas_tag").load(ctr_kfg_bayar+'send_kls_tag/?th=' + th + "&id_jenjang=" + id_jenjang, function () {
        const select = new Chol("#myselects_kls", {
            removeItems: true,
            removeItemButton: true,
            width: 100,
            maxItemCount: 5,
        });
    });
    $("#tampil_data_tag").load(ctr_kfg_bayar+'nulls');
}

function tampil_kls() {
    var th = $("#myselects_th_tag").val();
    var id_jenjang = $("#myselects_thj_tag").val();
    $("#tam_kelas_tag").load(ctr_kfg_bayar+'send_kls_tag/?th=' + th + "&id_jenjang=" + id_jenjang, function () {
        const select = new Choices("#myselects_kls", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
    });
    $("#tam_trans").load(ctr_kfg_bayar+'send_trans_tag/?id=' + th + "&id_jenjang=" + id_jenjang, function () {
        const select = new Choices("#myselects_jenis_pem", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
    });
}

function cetak_send_laporan_tag_newb() {
    var th = document.querySelector('[id="myselects_th_tag"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj_tag"]').value;
    var id_bulan = encodeURI($("#myselects_thj_bulan").val());
    var id_jenis_transaksi = encodeURI($("#myselects_thk").val());
    var kls = encodeURI($("#myselects_kls").val());
    window.open(ctr_kfg_bayar+'cetak_send_laporan_tag_new/?th=' + th + "&id_jenjang=" + id_jenjang + "&id_bulan=" + id_bulan + "&id_jenis_transaksi=" + id_jenis_transaksi + "&kls=" + kls);
}

function ambil_perkelas(id_kelas) {
    var th = document.querySelector('[id="myselects_th_tag"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj_tag"]').value;
    var trans = encodeURI($("#myselects_thdb").val());
    window.open(ctr_kfg_bayar+'ambil_perkelas_saja/?th=' + th + "&id_jenjang=" + id_jenjang + "&trans=" + trans + "&id_kelas=" + id_kelas);
}
function tampil_laporan_alls() {
    $("#tag_pem").modal("hide");
    var th = document.querySelector('[id="myselects_th_tag"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj_tag"]').value;
    $("#tam_trans").load(ctr_kfg_bayar+'nulls');
    $("#tampil_data_tag").load(ctr_kfg_bayar+'nulls');
    $("#kat_jatuh_tempo").modal("show");
    $("#bul").load(ctr_kfg_bayar+'send_trans_bul', function () {
        const select = new Choices("#myselects_thj_bulan", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
    });
    $("#kecuali").load(ctr_kfg_bayar+'send_trans_kecuali/?th=' + th + "&id_jenjang=" + id_jenjang, function () {
        const select = new Choices("#myselects_thk", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
    });
}

function tampil_laporan_bul() {
    var th = document.querySelector('[id="myselects_th_tag"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj_tag"]').value;
    var id_bulan = encodeURI($("#myselects_thj_bulan").val());
    var id_jenis_transaksi = encodeURI($("#myselects_thk").val());
    var kls = encodeURI($("#myselects_kls").val());
    $("#tam_trans").load(ctr_kfg_bayar+'send_trans_tag/?id=' + th + "&id_jenjang=" + id_jenjang);
    $("#laporan_bul").modal("show");
    $("#tampil_data_tag").load(ctr_kfg_bayar+'send_laporan_tag_alls/?th=' + th + "&id_jenjang=" + id_jenjang + "&id_bulan=" + id_bulan + "&id_jenis_transaksi=" + id_jenis_transaksi + "&kls=" + kls, function () {
        const select = new Choices("#lapor_pak", {
            removeItems: true,
            removeItemButton: true,
            maxItemCount: 5,
        });
        // modals.style.display = "none";
    });
}

function cetak_send_laporan_tag_alls() {
    var th = document.querySelector('[id="myselects_th_tag"]').value;
    var id_jenjang = document.querySelector('[id="myselects_thj_tag"]').value;
    var id_bulan = encodeURI($("#myselects_thj_bulan").val());
    window.open(ctr_kfg_bayar+'cetak_send_laporan_tag_alls/?th=' + th + "&id_jenjang=" + id_jenjang + "&id_bulan=" + id_bulan);
}

function tampil_default(){
    $("#tampil_default").load(ctr_kfg_bayar+'tampilkan_default');
}
