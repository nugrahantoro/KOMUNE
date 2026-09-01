$(document).ready(function () {
    load_kategori_laporan();
});

ctr_mst_trans = "konfig_tu/ex/mst_transaksi/";

function tab_mst_transaksi() {
    list_mst_transaksi();
}

function list_mst_transaksi() {
    dt = {};
    dt.id_jenjang = $("#id_jenjang").val();
    dt.id_ta = $("#id_ta").val();
    ajx = $.post(ctr_mst_trans + "list_mst_transaksi", dt, function (raw) {
        $("#tbl_mst_transaksi").html(raw);
    });
    load_ajx(ajx, "#tbl_mst_transaksi");
}

function cari_mst_transaksi() {
    txt = $("#cari_mst_transaksi").val();
    cari = cariable("#tbl_mst_transaksi", txt);
    if (cari.stat == "no_result" || cari.count == 0) {
        $(".trans_no_result").show();
    } else {
        $(".trans_no_result").hide();
    }
}

function modal_master_kategori(mode = 1, id_siswa = 0) {
    $("#modal_master_kategori").modal("show");
    load_mst_kat_transaksi();
    load_mst_kat_laporan();
    load_mst_kat_pengeluaran();
}

function load_mst_kat_transaksi() {
    dt = {};
    dt.id_jenjang = $("#id_jenjang").val();
    dt.id_ta = $("#id_ta").val();
    ajx = $.post(ctr_mst_trans + "load_mst_kat_transaksi", dt, function (raw) {
        $("#mst_kat_tbl_transaksi").html(raw);
        $("#inp_mst_kat_transaksi").val("");
    });
    load_ajx(ajx, "#div_mst_kat_transaksi");
}

function save_mst_kat_transaksi(mode, id_master = 0) {
    dt = {};
    dt.mode = mode;
    dt.id_master = id_master;
    dt.nama_master = $("#inp_mst_kat_transaksi").val();
    dt.id_ta = $("#id_ta").val();
    dt.id_jenjang = $("#id_jenjang").val();
    if (dt.nama_master == "") {
        alertify.error("Data Belum Lengkap");
        return;
    }
    mode_ket = ["", "Tambah", "Edit"];
    alertify.confirm(
        "Konfirmasi",
        mode_ket[dt.mode] + " Kelompok Transaksi?",
        function () {
            $.post(ctr_mst_trans + "save_mst_kat_transaksi", dt, function (raw) {
                if (isJson(raw)) {
                    rs = JSON.parse(raw);
                    if (rs.stat == 1) {
                        alertify.success(mode_ket[dt.mode] + " Kelompok Transaksi berhasil");
                        load_mst_kat_transaksi();
                    } else {
                        alertify.alert("Alert", rs.ket);
                    }
                } else {
                    alertify.alert("Alert", raw);
                }
            });
        },
        function () {}
    );
}

$("#mst_kat_tbl_transaksi").on("click", "tbody tr", function (event) {
    if ($(event.target).closest(".td_last").length) {
        return;
    }
    val = $(this).find(".nama_kat").html();
    $("#inp_mst_kat_transaksi").val(val);
});

function del_mst_kat_transaksi(id_master) {
    dt = {};
    dt.id_master = id_master;
    alertify.confirm(
        "Konfirmasi",
        "Hapus Kategori Transaksi?",
        function () {
            $.post(ctr_mst_trans + "del_mst_kat_transaksi", dt, function (raw) {
                if (isJson(raw)) {
                    rs = JSON.parse(raw);
                    if (rs.stat == 1) {
                        alertify.success(" Data Berhasil Dihapus");
                        load_mst_kat_transaksi();
                    } else {
                        alertify.alert("Alert", rs.ket);
                    }
                } else {
                    alertify.alert("Alert", raw);
                }
            });
        },
        function () {}
    );
}

function load_mst_kat_laporan() {
    dt = {};
    dt.id_jenjang = $("#id_jenjang").val();
    dt.id_ta = $("#id_ta").val();
    ajx = $.post(ctr_mst_trans + "load_mst_kat_laporan", dt, function (raw) {
        $("#mst_kat_tbl_laporan").html(raw);
        $("#inp_mst_kat_laporan").val("");
    });
    load_ajx(ajx, "#div_mst_kat_laporan");
}

function save_mst_kat_laporan(mode, id_master = 0) {
    dt = {};
    dt.mode = mode;
    dt.id_master = id_master;
    dt.nama_master = $("#inp_mst_kat_laporan").val();
    dt.id_ta = $("#id_ta").val();
    dt.id_jenjang = $("#id_jenjang").val();
    if (dt.nama_master == "") {
        alertify.error("Data Belum Lengkap");
        return;
    }
    mode_ket = ["", "Tambah", "Edit"];
    alertify.confirm(
        "Konfirmasi",
        mode_ket[dt.mode] + " Kelompok Laporan?",
        function () {
            $.post(ctr_mst_trans + "save_mst_kat_laporan", dt, function (raw) {
                if (isJson(raw)) {
                    rs = JSON.parse(raw);
                    if (rs.stat == 1) {
                        alertify.success(mode_ket[dt.mode] + " Kelompok Laporan berhasil");
                        load_mst_kat_laporan();
                    } else {
                        alertify.alert("Alert", rs.ket);
                    }
                } else {
                    alertify.alert("Alert", raw);
                }
            });
        },
        function () {}
    );
}

$("#mst_kat_tbl_laporan").on("click", "tbody tr", function (event) {
    if ($(event.target).closest(".td_last").length) {
        return;
    }
    val = $(this).find(".nama_kat").html();
    $("#inp_mst_kat_laporan").val(val);
});

function del_mst_kat_laporan(id_master) {
    dt = {};
    dt.id_master = id_master;
    alertify.confirm(
        "Konfirmasi",
        "Hapus Kategori Laporan?",
        function () {
            $.post(ctr_mst_trans + "del_mst_kat_laporan", dt, function (raw) {
                if (isJson(raw)) {
                    rs = JSON.parse(raw);
                    if (rs.stat == 1) {
                        alertify.success(" Data Berhasil Dihapus");
                        load_mst_kat_laporan();
                    } else {
                        alertify.alert("Alert", rs.ket);
                    }
                } else {
                    alertify.alert("Alert", raw);
                }
            });
        },
        function () {}
    );
}

function load_mst_kat_pengeluaran() {
    dt = {};
    dt.id_jenjang = $("#id_jenjang").val();
    dt.id_ta = $("#id_ta").val();
    ajx = $.post(ctr_mst_trans + "load_mst_kat_pengeluaran", dt, function (raw) {
        $("#mst_kat_tbl_pengeluaran").html(raw);
        $("#inp_mst_kat_pengeluaran").val("");
    });
    load_ajx(ajx, "#div_mst_kat_pengeluaran");
}

function save_mst_kat_pengeluaran(mode, id_master = 0) {
    dt = {};
    dt.mode = mode;
    dt.id_master = id_master;
    dt.nama_master = $("#inp_mst_kat_pengeluaran").val();
    dt.id_ta = $("#id_ta").val();
    dt.id_jenjang = $("#id_jenjang").val();
    if (dt.nama_master == "") {
        alertify.error("Data Belum Lengkap");
        return;
    }
    mode_ket = ["", "Tambah", "Edit"];
    alertify.confirm(
        "Konfirmasi",
        mode_ket[dt.mode] + " Kelompok Pengeluaran?",
        function () {
            $.post(ctr_mst_trans + "save_mst_kat_pengeluaran", dt, function (raw) {
                if (isJson(raw)) {
                    rs = JSON.parse(raw);
                    if (rs.stat == 1) {
                        alertify.success(mode_ket[dt.mode] + " Kelompok Pengeluaran berhasil");
                        load_mst_kat_pengeluaran();
                    } else {
                        alertify.alert("Alert", rs.ket);
                    }
                } else {
                    alertify.alert("Alert", raw);
                }
            });
        },
        function () {}
    );
}

$("#mst_kat_tbl_pengeluaran").on("click", "tbody tr", function (event) {
    if ($(event.target).closest(".td_last").length) {
        return;
    }
    val = $(this).find(".nama_kat").html();
    $("#inp_mst_kat_pengeluaran").val(val);
});

function del_mst_kat_pengeluaran(id_master) {
    dt = {};
    dt.id_master = id_master;
    alertify.confirm(
        "Konfirmasi",
        "Hapus Kategori Pengeluaran?",
        function () {
            $.post(ctr_mst_trans + "del_mst_kat_pengeluaran", dt, function (raw) {
                if (isJson(raw)) {
                    rs = JSON.parse(raw);
                    if (rs.stat == 1) {
                        alertify.success(" Data Berhasil Dihapus");
                        load_mst_kat_pengeluaran();
                    } else {
                        alertify.alert("Alert", rs.ket);
                    }
                } else {
                    alertify.alert("Alert", raw);
                }
            });
        },
        function () {}
    );
}

function modal_mst_trans(mode = 1, id_mst = 0) {
    glob_mode = mode;
    glob_id_master = id_mst;
    $("#modal_mst_trans").modal("show");
    dt = {};
    dt.id_mst = id_mst;
    dt.id_ta = $("#id_ta").val();
    dt.id_jenjang = $("#id_jenjang").val();
    mode_ket = ["", "Tambah", "Edit"];
    $("#titel_modal_mst_trans").html(mode_ket[mode]);
    $.post(ctr_mst_trans + "load_mst_jns_transaksi", dt, function (raw) {
        dt = JSON.parse(raw);
        $("#msjns_jatuh_tempo").html(dt.jatuh_tempo);
        $("#msjns_kelompok_trans").html(dt.kelompok_transaksi);
        $("#msjns_kelompok_laporan").html(dt.kelompok_laporan);
        $("#msjns_kelompok_pengeluaran").html(dt.kelompok_pengeluaran);

        $("#msjns_jatuh_tempo").val(dt.tgl);
        $("#msjns_kelompok_trans").val(dt.id_kat_trans);
        $("#msjns_kelompok_laporan").val(dt.id_kategori_laporan);
        $("#msjns_nama_transaksi").val(dt.nama_transaksi);
        $("#msjns_status").val(dt.status_kelompok);
        $("#msjns_cicilan").val(dt.cicil);
        $("#msjns_cicilan_khusus").val(dt.kondisi_khusus);
        $("#msjns_kategori_pemasukan").val(dt.id_kat_pemasukan);
        if (id_mst != 0) {
            $("#msjns_kelompok_pengeluaran").val(dt.id_kategori_pengeluaran);
        }

        list_subkat();
        $("#msjns_subkategori_pemasukan").val(dt.id_subkat_pemasukan);
        change_mst_status_trans();
    });
}

function change_mst_status_trans() {
    val = $("#msjns_status").val();
    if (val == 0) {
        $("#div_msjns_cicilan").show();
        $("#div_msjns_cicilan_khusus").hide();
        $("#div_msjns_kelompok_trans").hide();
    } else if (val == 1) {
        $("#div_msjns_cicilan").hide();
        $("#div_msjns_cicilan_khusus").show();
        $("#div_msjns_kelompok_trans").show();
    } else {
        $("#div_msjns_cicilan").show();
        $("#div_msjns_cicilan_khusus").hide();
        $("#div_msjns_kelompok_trans").hide();
    }
}

function load_mst_kat_pengeluaran() {
    dt = {};
    dt.id_jenjang = $("#id_jenjang").val();
    dt.id_ta = $("#id_ta").val();
    ajx = $.post(ctr_mst_trans + "load_mst_kat_pengeluaran", dt, function (raw) {
        $("#mst_kat_tbl_pengeluaran").html(raw);
        $("#inp_mst_kat_pengeluaran").val("");
    });
    load_ajx(ajx, "#div_mst_kat_pengeluaran");
}

function save_mst_trans() {
    dt = {};
    dt.mode = glob_mode;
    dt.id_master = glob_id_master;
    dt.nama_trans = $("#msjns_nama_transaksi").val();
    dt.status_trans = $("#msjns_status").val();
    dt.id_kelompok_trans = $("#msjns_kelompok_trans").val();
    dt.id_kelompok_laporan = $("#msjns_kelompok_laporan").val();
    dt.id_kelompok_pengeluaran = $("#msjns_kelompok_pengeluaran").val();
    dt.jatuh_tempo = $("#msjns_jatuh_tempo").val();
    dt.cicilan = $("#msjns_cicilan").val();
    dt.cicilan_khusus = $("#msjns_cicilan_khusus").val();
    dt.id_ta = $("#id_ta").val();
    dt.id_jenjang = $("#id_jenjang").val();
    dt.id_kat_pemasukan = $("#msjns_kategori_pemasukan").val();
    dt.id_subkat_pemasukan = $("#msjns_subkategori_pemasukan").val();
    if (dt.nama_trans == "" || dt.status_trans == "-" || dt.id_kelompok_laporan == "-" || dt.id_kelompok_pengeluaran == "-") {
        alertify.error("Data Belum Lengkap");
        return;
    } else if (dt.status_trans == 0 && dt.cicilan == "-") {
        alertify.error("Data Belum Lengkap");
        return;
    } else if (dt.status_trans == 1 && (dt.cicilan_khusus == "-" || dt.id_kelompok_trans == "-")) {
        alertify.error("Data Belum Lengkap");
        return;
    }
    if (dt.kat_pemasukan == 0) {
        alertify.error("Pilih Kategori Pemasukan Kas");
        return;
    }
    mode_ket = ["", "Tambah", "Edit"];
    alertify.confirm(
        "Konfirmasi",
        mode_ket[dt.mode] + " Master Jenis Transaksi?",
        function () {
            ajx = $.post(ctr_mst_trans + "save_mst_trans", dt, function (raw) {
                if (isJson(raw)) {
                    rs = JSON.parse(raw);
                    if (rs.stat == 1) {
                        alertify.success(mode_ket[dt.mode] + " Master Jenis Transaksi berhasil");
                        list_mst_transaksi();
                        $("#modal_mst_trans").modal("hide");
                    } else {
                        alertify.alert("Alert", rs.ket);
                    }
                } else {
                    alertify.alert("Alert", raw);
                }
            });
            load_show(ajx);
        },
        function () {}
    );
}

function del_mst_trans(id_master) {
    dt = {};
    dt.id_master = id_master;
    alertify.confirm(
        "Konfirmasi",
        "Hapus Jenis Transaksi?",
        function () {
            $.post(ctr_mst_trans + "del_mst_trans", dt, function (raw) {
                if (isJson(raw)) {
                    rs = JSON.parse(raw);
                    if (rs.stat == 1) {
                        alertify.success(" Data Berhasil Dihapus");
                        list_mst_transaksi();
                    } else {
                        alertify.alert("Alert", rs.ket);
                    }
                } else {
                    alertify.alert("Alert", raw);
                }
            });
        },
        function () {}
    );
}

function load_kategori_laporan() {
    $.post(ctr_mst_trans + "load_kategori_laporan", function (raw) {
        if (isJson(raw)) {
            dt = JSON.parse(raw);
            $("#msjns_kategori_pemasukan").html(dt.in_kat);
            dt_subkat = dt.subkat;
        } else {
            alertify.error("Error");
        }
    });

    $(".row_subkat").hide();
}

function list_subkat() {
    id_kat = $("#msjns_kategori_pemasukan").val();
    opt = "<option value='0'>-pilih-</option>";
    $(".row_subkat").hide();
    if (id_kat != 0) {
        if (typeof dt_subkat[id_kat] !== "undefined") {
            $(".row_subkat").show();
            subkat = dt_subkat[id_kat];
            for (i = 0; i < subkat.length; i++) {
                opt += "<option value='" + subkat[i]["id_subkat_pemasukan"] + "'>" + subkat[i]["ref"] + " " + subkat[i]["nama_subkat_pemasukan"] + "</option>";
            }
        }
    }
    $("#msjns_subkategori_pemasukan").html(opt);
}
