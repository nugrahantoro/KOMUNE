$(document).ready(function(){
    load_data();
});

controller = 'mobileapp/ex/event/';

function ta(){
	load_data()
	load_laporan()
}

function jenjang(){
	load_data()
	load_laporan()
}

function tab_event(){
	load_data()
}

function tab_laporan(){
	load_laporan()
}

/* Tab 1 */
function load_data()
{
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'tampil_data_event',dt,function(raw)
	{
		$('#div_list_event').html(raw);
	});
}

function load_kelas()
{
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'tampil_data_kelas',dt,function(raw)
	{
		$('#list_kelas').html(raw);
	});
}

function cari_event()
{
	txt = $('#cari_event').val();
	cari=cariable('#div_list_event',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.event_no_result').show(); 
	}
	else
	{
		$('.event_no_result').hide();
	}
}

function modal_event(event,mode=1,id_event=0)
{
	load_kelas();
	event.stopPropagation();
	glob_mode=mode;
	glob_id_event=id_event;
	$('#modal_event input').val('');
	$('#modal_event select').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_event').hide();
	}
	else 
	{
		$('#btn_del_event').show();
		dt={};
		dt.id_event=id_event;
		title='Edit';
		ajx=$.post(controller+'get_data_event',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#frm_kelas').val(dt.akses_kelas);
				$('#frm_tid').val(dt.tid_alat);
				$('#frm_nama').val(dt.nama_event);
				$('#frm_keterangan').val(dt.keterangan);
				$('#frm_tgl_mulai').val(dt.tgl_mulai);
				$('#frm_jam_mulai').val(dt.jam_mulai);
				$('#frm_tgl_selesai').val(dt.tgl_selesai);
				$('#frm_jam_selesai').val(dt.jam_selesai);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_event span').html(title+' Event');
	$('#ket_modal_event').html(title);
	$('#modal_event').modal('show');
}

function save_event()
{
	dt={}
	dt.mode=glob_mode;
	dt.id=glob_id_event;
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_kelas=$('#frm_kelas').val();
	dt.tid=$('#frm_tid').val();
	dt.nama_event=$('#frm_nama').val();
	dt.keterangan=$('#frm_keterangan').val();
	dt.tgl_mulai=$('#frm_tgl_mulai').val();
	dt.jam_mulai=$('#frm_jam_mulai').val();
	dt.tgl_selesai=$('#frm_tgl_selesai').val();
	dt.jam_selesai=$('#frm_jam_selesai').val();
	if(dt.tid=='' || dt.nama_event=='' || dt.tgl_mulai=='' || dt.jam_mulai=='' || dt.tgl_selesai=='' || dt.jam_selesai=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi ', mode_ket[dt.mode]+' Event?', function(){
		$.post(controller+'save_event',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' TID');
					load_data();
					$('#modal_event').modal('hide');
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

function modal_cetak(id_event=0)
{
	glob_id_event=id_event;
	dt={};
	dt.id_event=id_event;
	ajx=$.post(controller+'load_qrcode',dt,function(raw)
	{
		$('#div_qrcode').html(raw);
	});
	setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	$('#modal_cetak').modal('show');
}

function download_qrcode(url=null)
{
	
	var a = document.createElement('a');
    a.href = url;
    a.download = url.substring(url.lastIndexOf('/') + 1); // Use the filename from the URL
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}


/* Tab 2 */
function load_laporan(){
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'tampil_data_event_lap',dt,function(raw)
	{
		$('#div_list_lap_event').html(raw);
	});
}

function cari_nm_event()
{
	txt = $('#cari_nm_event').val();
	cari=cariable('#div_list_lap_event',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.lap_event_no_result').show(); 
	}
	else
	{
		$('.lap_event_no_result').hide();
	}
}

function lihat_absensi(id_event=0){
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_event=id_event;
	ajx=$.post(controller+'tampil_data_absensi',dt,function(raw)
	{
		$('#div_list_absensi').html(raw);
	});
}

function cetak_absensi(id_event=0){
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_event=id_event;
	var url = controller+"cetak_absensi/?id_event="+dt.id_event+"&id_ta="+dt.id_ta+"&id_jenjang="+dt.id_jenjang;
	window.open(url);	
}