$(document).ready(function(){
	get_form();
	var x = document.getElementById("identitas");
	  if (x.style.display === "none") {
	    x.style.display = "block";
	  } else {
	    x.style.display = "none";
	  }
});

controller = 'siswa/ex/data_rekap_absensi/';

function ta(){
	get_form();
}

function jenjang(){
	get_form();
}

function get_form(){
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'get_data_kelas',dt,function(raw)
	{
		$('#div_kelas').html(raw);
	});
	ajx=$.post(controller+'get_data_periode',dt,function(raw)
	{
		$('#div_periode').html(raw);
	});
	rekap();
}

function rekap(){
	tab_sekolah();
}

/* TAB 1 */
function tab_sekolah(){
	dt={};
	dt.id_kelas=$('#frm_kelas').val();
	dt.periode=$('#frm_periode').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'tampil_data_absensi',dt,function(raw)
	{
		$('#div_list_absensi').html(raw);
	});
	load_ajx(ajx,'#div_absensi');
}

function modal_approval(event,id_ku=1,rfid_ku=0){
	event.stopPropagation();
	glob_id_ku=id_ku;
	glob_rfid_ku=rfid_ku;
	$('#modal_absensi input').val('');
	$('#modal_absensi select').val('0');
	$('#btn_del_absensi').show();
	dt={};
	dt.rfid=rfid_ku;
	title='Apakah yakin ingin menghapus data ini?';
	$('#ket_modal_absensi').html(title);
	$('#modal_absensi').modal('show');
}

function modal_detail(event,id_ku=1,rfid_ku=0){
	event.stopPropagation();
	glob_id_ku=id_ku;
	glob_rfid_ku=rfid_ku;
	dt={};
	dt.rfid=rfid_ku;
	myTanggal = 'i_tgl-'+glob_id_ku;
	myNama = 'i_nama-'+glob_id_ku;
	myStatus = 'i_status-'+glob_id_ku;
	myMasuk = 'i_masuk-'+glob_id_ku;
	myPulang = 'i_pulang-'+glob_id_ku;
	myKetMas = 'i_ket_masuk-'+glob_id_ku;
	myKetIz = 'i_ket_izin-'+glob_id_ku;
	myLokasi = 'i_lokasi-'+glob_id_ku;
	var tanggal = document.getElementById(myTanggal).value;
	var nama = document.getElementById(myNama).value;
	var status = document.getElementById(myStatus).value;
	var masuk = document.getElementById(myMasuk).value;
	var pulang = document.getElementById(myPulang).value;
	var ket_masuk = document.getElementById(myKetMas).value;
	var ket_izin = document.getElementById(myKetIz).value;
	var lokasi = document.getElementById(myLokasi).value;
	title='Detail Absensi';
	$('#ket_modal_detail_absensi').html(title);
	$('#kh_tgl').html(tanggal);
	$('#kh_status').html(status);
	$('#kh_nama').html(nama);
	$('#kh_masuk').html(masuk);
	$('#kh_pulang').html(pulang);
	$('#kh_ket').html(ket_masuk);
	$('#kh_izin').html(ket_izin);
	$('#kh_lokasi').html(lokasi);
	$('#modal_detail_absensi').modal('show');
}

function cetak_absensi(){
	dt={};
	dt.id_kelas=$('#frm_kelas').val();
	dt.periode=$('#frm_periode').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	var url = controller+"cetak_laporan_absensi/?id_kelas="+dt.id_kelas+"&periode="+dt.periode+"&id_ta="+dt.id_ta+"&id_jenjang="+dt.id_jenjang;
	window.open(url);
}

function hapus_absensi(id_ku,rfid_ku){
	dt={};
	dt.id_ku=glob_id_ku;
	dt.rfid=glob_rfid_ku;
	alertify.confirm('Konfirmasi','Hapus Absensi?', function(){
	$.post(controller+'hapus_absensi',dt,function(raw)
	{
		if(isJson(raw))
		{
			rs=JSON.parse(raw);
			if(rs.stat == 1)
			{
				alertify.success(' Absensi berhasil dihapus');
				rekap();
				$('#modal_absensi').modal('hide');
			}
			else
			{
				alertify.alert('Alert',rs.ket);
			}
		}
		else
		{
			alertify.alert('Alert', raw);
		}
	});
	},
	function(){});
}

function modal_hapus(id_ku,rfid_ku){
	dt={};
	dt.id_ku=id_ku;
	dt.rfid=rfid_ku;
	alertify.confirm('Konfirmasi','Hapus Absensi?', function(){
	$.post(controller+'modal_hapus',dt,function(raw)
	{
		if(isJson(raw))
		{
			rs=JSON.parse(raw);
			if(rs.stat == 1)
			{
				alertify.success(' Absensi berhasil dihapus');
				rekap();
			}
			else
			{
				alertify.alert('Alert',rs.ket);
			}
		}
		else
		{
			alertify.alert('Alert', raw);
		}
	});
	},
	function(){});
}
