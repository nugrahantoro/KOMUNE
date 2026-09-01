$(document).ready(function(){
	tampil_data();
	//tampil_data2();
	$("#col-userlain").hide();
});

controller = 'sistem/ex/data_user/';

function tampil_data()
{
	dt={};
	ajx=$.post(controller+'tampil_data',dt,function(raw)
	{
		$('#div_data_user').html(raw);
	});
	load_ajx(ajx,'#col-userkeu');
}

function tampil_data2()
{
	dt={};
	ajx=$.post(controller+'tampil_data2',dt,function(raw)
	{
		$('#div_data_user2').html(raw);
	});
	load_ajx(ajx,'#col-userlain');
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
		alertify.error('Plih Nama User dan Jenjang');
		return;
	}
	alertify.confirm('Konfirmasi', 'Tambahkan wilayah untuk pengguna dipilih?', function(){
		ajx=$.post(controller+'save_akses_jenjang',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success('Akses wilayah ditambahkan');
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
		alertify.confirm('Konfirmasi','Hapus akses wilayah?', function(){
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

function modal_user(mode,idu='new')
{
	glob_mode=mode;
	glob_kode=idu;
	$('#modal_user input').val('');
	if(mode==1)
	{
		title='Tambah';
		$('#us_kode').prop('disabled',false);
		$('#btn_del_user').hide();
	}
	else
	{
		$('#btn_del_user').show();
		$('#us_kode').prop('disabled',true);
		dt={};
		dt.kode=idu;
		title='Edit';
		ajx=$.post(controller+'get_user',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#us_kode').val(dt.id)
				$('#us_nama').val(dt.nama_us)
				$('#us_user').val(dt.username)
				$('#us_pass').val(dt.password)
				$('#us_alamat').val(dt.alamat)
				$('#us_jabatan').val(dt.jabat)
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_user span').html(title+' Pengguna');
	$('#ket_modal').html(title);
	$('#modal_user').modal('show');
}

// function modal_user2(mode,idu='new')
// {
// 	glob_mode2=mode;
// 	glob_kode=idu;
// 	glob_kode2=idu;
// 	$('#modal_user2 input').val('');
// 	if(mode==1)
// 	{
// 		title='Tambah';
// 		$('#us_kode2').prop('disabled',false);
// 		$('#btn_del_user2').hide();
// 		$('#us_jabatan2').val(0);
// 		init_kelas();
// 	}
// 	else
// 	{
// 		$('#btn_del_user2').show();
// 		$('#us_kode2').prop('disabled',true);
// 		dt={};
// 		dt.kode=idu;
// 		title='Edit';
// 		ajx=$.post(controller+'get_user',dt,function(raw)
// 		{
// 			if(isJson(raw))
// 			{
// 				dt = JSON.parse(raw);
// 				$('#us_kode2').val(dt.id);
// 				$('#us_nama2').val(dt.nama_us);
// 				$('#us_user2').val(dt.username);
// 				$('#us_pass2').val(dt.password);
// 				$('#us_alamat2').val(dt.alamat);
// 				$('#us_jabatan2').val(dt.jenis_user);
// 				$('#us_kelas').val(dt.akses_kelas);
// 				init_kelas();
				
// 			}
// 		});
// 		setTimeout(function(){load_ajx(ajx,'#modal_body2')},225);
// 	}
// 	$('#btn_save_user2 span').html(title+' User');
// 	$('#ket_modal2').html(title);
// 	$('#modal_user2').modal('show');
// }

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

function save_user()
{
	dt={};
	dt.mode=glob_mode;
	dt.kode=$('#us_kode').val();
	dt.nama=$('#us_nama').val();
	dt.user=$('#us_user').val();
	dt.pass=$('#us_pass').val();
	dt.alamat=$('#us_alamat').val();
	dt.jabatan=$('#us_jabatan').val();
	if(dt.kode=='' || dt.nama=='' || dt.user=='' || dt.pass=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Pengguna?', function(){
		$.post(controller+'save_user',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Pengguna berhasil');
					tampil_data();
					$('#modal_user').modal('hide');
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

// function save_user2()
// {
// 	dt={};
// 	dt.mode=glob_mode2;
// 	dt.kode=$('#us_kode2').val();
// 	dt.nama=$('#us_nama2').val();
// 	dt.user=$('#us_user2').val();
// 	dt.pass=$('#us_pass2').val();
// 	dt.alamat=$('#us_alamat2').val();
// 	dt.jabatan= $( "#us_jabatan2 option:selected" ).text();
// 	dt.jenis_user=$('#us_jabatan2').val();
// 	dt.akses_kelas=$('#us_kelas').val();
// 	if(dt.kode=='' || dt.nama=='' || dt.user=='' || dt.pass=='' || dt.jenis_user=='0')
// 	{
// 		alertify.error('Data Belum Lengkap');
// 		return;
// 	}
// 	mode_ket=['','Tambah','Edit'];
// 	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' User Lain?', function(){
// 		$.post(controller+'save_user2',dt,function(raw)
// 		{
// 			if(isJson(raw))
// 			{
// 				rs=JSON.parse(raw);
// 				if(rs.stat == 1)
// 				{
// 					alertify.success(mode_ket[dt.mode]+' User berhasil');
// 					tampil_data2();
// 					$('#modal_user2').modal('hide');
// 				}
// 				else
// 				{
// 					alertify.alert('Alert',rs.ket);
// 				}
// 			}
// 			else
// 			{
// 				alertify.alert('Alert', raw);
// 			}
// 		});
// 	},
// 	function(){});
	
// }

function aktif_user(kode)
{
		dt={};
		dt.kode=kode;
		alertify.confirm('Konfirmasi','Aktifkan Pengguna?', function(){
		$.post(controller+'aktif_user',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Pengguna terverifikasi');
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

function del_user(kode)
{
		dt={};
		dt.kode=glob_kode;
		alertify.confirm('Konfirmasi','Hapus Pengguna?', function(){
		$.post(controller+'del_user',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Pengguna Dihapus');
					tampil_data();
					tampil_data2();
					$('#modal_user').modal('hide');
					$('#modal_user2').modal('hide');
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