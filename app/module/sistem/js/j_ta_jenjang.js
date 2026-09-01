$(document).ready(function(){
	tampil_data();
	tampil_data2();
	
	tgl_mulai_ta = $("#tgl_mulai_ta").flatpickr({
		dateFormat: "d-m-Y",
		static:true
	});
	tgl_selesai_ta = $("#tgl_selesai_ta").flatpickr({
		dateFormat: "d-m-Y",
		static:true
	});
});

controller = 'sistem/ex/ta_jenjang/';
function tampil_data()
{
	dt={};
	ajx=$.post(controller+'tampil_data',dt,function(raw)
	{
		$('#div_data_ta').html(raw);
	});
	load_ajx(ajx,'#col-userkeu');
}

function set_aktif_ta(id)
{
	alertify.confirm('Konfirmasi', 'Aktifkan periode ini sebagai periode aktif sistem?', function(){
		$.post(controller+'set_aktif_ta', { kode: id }, function(res){
			if(isJson(res)) {
				let rs = JSON.parse(res);
				if(rs.stat == 1) {
					alertify.success(rs.ket);
					tampil_data();
				} else {
					alertify.error(rs.ket);
				}
			} else {
				alertify.success("Periode berhasil diaktifkan");
				tampil_data();
			}
		});
	}, function(){});
}

function tampil_data2()
{
	dt={};
	ajx=$.post(controller+'tampil_data2',dt,function(raw)
	{
		$('#div_data_jenjang').html(raw);
	});
	load_ajx(ajx,'#col-userlain');
}

function set_aktif_jenjang(id)
{
	alertify.confirm('Konfirmasi', 'Aktifkan wilayah ini sebagai wilayah aktif utama?', function(){
		$.post(controller+'set_aktif_jenjang', { kode: id }, function(res){
			if(isJson(res)) {
				let rs = JSON.parse(res);
				if(rs.stat == 1) {
					alertify.success(rs.ket);
					tampil_data2();
				} else {
					alertify.error(rs.ket);
				}
			} else {
				alertify.success("Wilayah berhasil diaktifkan");
				tampil_data2();
			}
		});
	}, function(){});
}

function modal_akses_jenjang(mode,idu='new')
{
	$('#modal_akses_jenjang').modal('show');
	form_akses_jenjang();
}

function form_akses_jenjang()
{
	ajx = $.post(controller+'form_akses_jenjang',function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#aj_kode').html(dt.aj_kode);
			$('#aj_jenjang').html(dt.aj_jenjang);
			data_akses_jenjang();
		}
	});
	setTimeout(function(){load_ajx(ajx,'#modal_akses_jenjang .modal-body')},225);
	
}

function data_akses_jenjang()
{
	dt={};
	dt.kode=$('#aj_kode').val();
	ajx = $.post(controller+'data_akses_jenjang',dt,function(raw)
	{
		$('#div_data_jenjang').html(raw);
	});
	load_ajx(ajx,'#modal_akses_jenjang .modal-body');
}

function save_akses_jenjang()
{
	dt={};
	dt.kode=$('#aj_kode').val();
	dt.jenjang=$('#aj_jenjang').val();
	if(dt.kode=='all' || dt.jenjang=='all')
	{
		alertify.error('Plih Periode dan Wilayah');
		return;
	}
	alertify.confirm('Konfirmasi', 'Tambahkan akses untuk Periode Operasional dipilih?', function(){
		ajx=$.post(controller+'save_akses_jenjang',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success('Akses Wilayah ditambahkan');
					data_akses_jenjang();
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

function del_akses_jenjang(id_akses)
{
		dt={};
		dt.id_akses=id_akses;
		alertify.confirm('Konfirmasi','Hapus akses Wilayah?', function(){
		$.post(controller+'del_akses_jenjang',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Akses Dihapus');
					data_akses_jenjang();
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

function modal_ta(mode,idu='new')
{
	glob_mode=mode;
	glob_kode=idu;
	$('#modal_ta input').val('');
	tgl_mulai_ta.clear();
	tgl_selesai_ta.clear();
	if(mode==1)
	{
		title='Tambah';
		$('#us_kode').prop('disabled',false);
		$('#btn_del_ta').hide();
	}
	else
	{
		$('#btn_del_ta').show();
		$('#us_kode').prop('disabled',true);
		dt={};
		dt.kode=idu;
		title='Edit';
		ajx=$.post(controller+'get_ta',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#nm_ta').val(dt.nama_tahunajar);
				tgl_mulai_ta.setDate(tgl_indo(dt.tgl_mulai));
				tgl_selesai_ta.setDate(tgl_indo(dt.tgl_selesai));
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_ta span').html(title+' Periode');
	$('#ket_modal').html(title);
	$('#modal_ta').modal('show');
}

function modal_jenjang(mode,idu='new')
{
	glob_mode2=mode;
	glob_kode2=idu;
	$('#modal_jenjang input').val('');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_jenjang').hide();
	}
	else
	{
		$('#btn_del_jenjang').show();
		dt={};
		dt.kode=idu;
		title='Edit';
		ajx=$.post(controller+'get_jenjang',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#nama_jenjang').val(dt.nama_jenjang);
				
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body2')},225);
	}
	$('#btn_save_jenjang span').html(title+' Wilayah');
	$('#ket_modal2').html(title);
	$('#modal_jenjang').modal('show');
}

function init_kelas()
{
	jabatan = $('#us_jabatan2').val();
	if(jabatan ==2)
	{
		$('#us_kelas').prop('disabled',false);
	}
	else
	{
		$('#us_kelas').prop('disabled',true);
		$('#us_kelas').val(0);
	}
}

function save_ta()
{
	dt={};
	dt.mode=glob_mode;
	dt.kode=glob_kode;
	dt.nama=$('#nm_ta').val();
	dt.tgl_mulai=tgl_indo($('#tgl_mulai_ta').val());
	dt.tgl_selesai=tgl_indo($('#tgl_selesai_ta').val());
	if(dt.kode=='' || dt.nama=='' || dt.tgl_mulai=='' || dt.tgl_selesai=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Periode?', function(){
		$.post(controller+'save_ta',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Periode berhasil');
					tampil_data();
					$('#modal_ta').modal('hide');
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

glob_kode2='';
function save_jenjang()
{
	dt={};
	dt.mode=glob_mode2;
	dt.kode=glob_kode2;
	dt.nama_jenjang=$('#nama_jenjang').val();
	if(dt.kode=='' || dt.nama_jenjang=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Wilayah?', function(){
		$.post(controller+'save_jenjang',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Wilayah berhasil');
					tampil_data2();
					$('#modal_jenjang').modal('hide');
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

function aktif_ta(kode)
{
		dt={};
		dt.kode=kode;
		alertify.confirm('Konfirmasi','Aktifkan Periode Operasional?', function(){
		$.post(controller+'aktif_ta',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Periode terverifikasi');
					tampil_data();
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

function del_ta(kode)
{
		dt={};
		dt.kode=glob_kode;
		alertify.confirm('Konfirmasi','Hapus Tahun Ajaran?', function(){
		$.post(controller+'del_ta',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Periode Dihapus');
					tampil_data();
					$('#modal_ta').modal('hide');
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

function del_jenjang()
{
		dt={};
		dt.kode=glob_kode2;
		alertify.confirm('Konfirmasi','Hapus Wilayah?', function(){
		$.post(controller+'del_jenjang',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Wilayah Dihapus');
					tampil_data2();
					$('#modal_jenjang').modal('hide');
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

function load_over(elem,ajax)
{
	//nothing
}