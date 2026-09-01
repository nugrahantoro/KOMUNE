$(document).ready(function () {
    tampil_tahun();
    tampil_jenjang();
    tampil_kelas_awal();
    proses_tagihan_awal();

    tanggal_awal = $("#tanggal_awal").flatpickr({
        dateFormat: "Y-m-d",
        static: true,
    });
    tanggal_akhir = $("#tanggal_akhir").flatpickr({
        dateFormat: "Y-m-d",
        static: true,
    });
});

ctr_kfg_bayar = 'laporan_rekap_tagihan/ex/laporan_rekap_tagihan/';

function tampil_tahun() {
    $("#tampil_tahun").load(ctr_kfg_bayar+'tampil_tahun');
}

function tampil_jenjang() {
    $("#tampil_jenjang").load(ctr_kfg_bayar+'tampil_jenjang');
}
function tampil_kelas_awal() {
    $("#tampil_kelas").load(ctr_kfg_bayar+'tampil_kelas_awal');
}
function tampil_kelas() {
    $("#tampil_kelas").load(ctr_kfg_bayar+'tampil_kelas');
}

// TAMPIL KELAS DENGAN TAHUN
function tampil_kelas_tahun(sel) {
    var th = document.querySelector('[id="pilih_tahun"]').value;
    var id_jenjang = document.querySelector('[id="pilih_jenjang"]').value;
    pages = sel.options[sel.selectedIndex].value;
    $("#tampil_kelas").load(ctr_kfg_bayar+'tampil_kelas/?th=' + th + "&id_jenjang=" + id_jenjang);
}
// TAMPIL KELAS DENGAN JENJANG
function tampil_kelas_jenjang(sel) {
    var th = document.querySelector('[id="pilih_tahun"]').value;
    var id_jenjang = document.querySelector('[id="pilih_jenjang"]').value;
    pages = sel.options[sel.selectedIndex].value;
    $("#tampil_kelas").load(ctr_kfg_bayar+'tampil_kelas/?th=' + th + "&id_jenjang=" + id_jenjang);
}

function proses_tagihan_awal() {
    $("#hasil_rekap").load(ctr_kfg_bayar+'hasil_rekap_awal');
}
function proses_tagihan() {
    var tanggal_awal = document.querySelector('[id="tanggal_awal"]').value;
    var tanggal_akhir = document.querySelector('[id="tanggal_akhir"]').value;
    var pilih_tahun = document.querySelector('[id="pilih_tahun"]').value;
    var pilih_jenjang = document.querySelector('[id="pilih_jenjang"]').value;
    var pilih_kelas = document.querySelector('[id="pilih_kelas"]').value;
    $("#hasil_rekap").load(ctr_kfg_bayar+'hasil_rekap/?th=' + pilih_tahun + "&id_kls=" + pilih_kelas + "&a=" + tanggal_awal + "&b=" + tanggal_akhir + "&id_jenjang=" + pilih_jenjang);
}

function send_tagihan_wa(id_siswa)
{
	dt={};
	dt.id_siswa=id_siswa;
	dt.tanggal_awal=$('#tanggal_awal').val();
	dt.tanggal_akhir=$('#tanggal_akhir').val();
	dt.id_ta=$('#pilih_tahun').val();
	dt.id_kelas=$('#pilih_kelas').val();
	if(!confirm("Kirim Surat Tagihan ke nomor WA?"))
	{
		return;
	}
	$.post(ctr_kfg_bayar+'send_tagihan_wa',dt,function(raw)
	{
		//alertify.success(raw);
		alert(raw);
	})
}

function cetak_surat() {
    var a = document.querySelector('[id="tanggal_awal"]').value;
    var b = document.querySelector('[id="tanggal_akhir"]').value;
    var th = document.querySelector('[id="pilih_tahun"]').value;
    var id_kls = document.querySelector('[id="pilih_kelas"]').value;
    var table = document.getElementById("tabsesur"),
        cells = table.getElementsByTagName("tr");
    var id = "";

    for (var i = 0, len = cells.length; i < len; i++) {
        cells[i].onclick = function () {
            id = this.cells[1].innerText;
        };
    }

    $.ajax({
        type: "GET",
        url: ctr_kfg_bayar+'htmltables/?th=' + th + "&id_kls=" + id_kls + "&id=" + id + "&a=" + a + "&b=" + b,
        success: function () {
            window.open(ctr_kfg_bayar+'htmltables/?th=' + th + "&id_kls=" + id_kls + "&id=" + id + "&a=" + a + "&b=" + b);
        },
    });
}

//
////
////// JS CODE FOR FORMAT SURAT
////
//

$(document).ready(function () {
	teditor=ClassicEditor
            .create( document.querySelector( '#srt_paragraf_3' ) )
             .then( newEditor => {
				teditor = newEditor;
			} )
			.catch( error => {
                console.error( error );
            } );
});

function modal_format_surat()
{
	$('#modal_format_surat').modal('show');
	ajx=$.get(ctr_kfg_bayar+"get_form_surat",function(raw)
	{
		dt=JSON.parse(raw);
		$('#srt_no_surat').val(dt.no);
		$('#srt_tempat').val(dt.tempat);
		$('#srt_tanggal').val(dt.tanggal);
		$('#srt_perihal').val(dt.hal);
		$('#srt_lampiran').val(dt.lamp);
		$('#srt_paragraf_1').val(dt.isi);
		$('#srt_paragraf_2').val(dt.bawah);
		teditor.setData(dt.isi_a);
	});
	load_show(ajx);
}

function save_format_surat()
{
	dt={};
	dt.no_surat = $('#srt_no_surat').val();
	dt.tempat = $('#srt_tempat').val();
	dt.tanggal = $('#srt_tanggal').val();
	dt.perihal = $('#srt_perihal').val();
	dt.lampiran = $('#srt_lampiran').val();
	dt.paragraf_1 = $('#srt_paragraf_1').val();
	dt.paragraf_2 = $('#srt_paragraf_2').val();
	dt.paragraf_3 = teditor.getData();
	alertify.confirm('Konfirmasi', 'Simpan Perubahan?', function(){
		ajx=$.post(ctr_kfg_bayar+'save_format_surat',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success('Perubahan Disimpan');
					$('#modal_format_surat').modal('hide');
				}
				else
				{
					alertify.alert('Alert','Gagal');
					//alertify.alert('Alert',rs.ket);
				}
			}
			else
			{
				alertify.alert('Alert','Gagal');
				//alertify.alert('Alert', raw);
			}
		});
		load_show(ajx);
	},
	function(){});
}

//
//// CODE END
