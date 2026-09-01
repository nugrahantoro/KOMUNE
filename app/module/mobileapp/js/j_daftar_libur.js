$(document).ready(function(){
	load_data();
});

controller = 'mobileapp/ex/daftar_libur/';

function ta(){
	load_data();
}

function jenjang(){
	load_data();
}

function load_data()
{
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'load_data',dt,function(raw)
	{
		$('#div_list_libur').html(raw);
	});
	load_ajx(ajx);
}

function cari_libur()
{
	txt = $('#cari_libur').val();
	cari=cariable('#div_list_libur',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.libur_no_result').show(); 
	}
	else
	{
		$('.libur_no_result').hide();
	}
}


function modal_libur(event,mode=1, id_libur=0)
{
	event.stopPropagation();
	glob_mode=mode;
	glob_id_libur=id_libur;
	$('#modal_libur input').val('');
	$('#modal_libur textarea').val('');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_libur').hide();
	}
	else 
	{
		$('#btn_del_libur').show();
		dt={};
		dt.id_libur=id_libur;
		title='Edit';
		ajx=$.post(controller+'get_data_libur',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#frm_tanggal').val(dt.tanggal);
				$('#frm_keterangan').val(dt.keterangan);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_libur span').html(title+' hari libur');
	$('#ket_modal_libur').html(title);
	$('#modal_libur').modal('show');
}

function save_libur()
{
	dt={}
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	dt.mode=glob_mode;
	dt.id_libur=glob_id_libur;
	dt.tanggal=$('#frm_tanggal').val();
	dt.keterangan=$('#frm_keterangan').val();

	if (dt.tanggal == '' || dt.keterangan == '') {
        alertify.error('Data Belum Lengkap');
		return;
    }
    mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' libur?', function(){
		ajx=$.post(controller+'save_libur',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' hari libur berhasil');
					load_data();
					$('#modal_libur').modal('hide');
				}
				else
				{
					alertify.alert('Alert',rs.ket);
					load_data();
				}
			}
			else
			{
				alertify.alert('Alert', raw);
			}
		});
		load_ajx(ajx);
	},
	function(){});	
}

function del_libur(id_libur)
{
	dt={};
	dt.id_libur=glob_id_libur;
	alertify.confirm('Konfirmasi','Hapus libur?', function(){
		$.post(controller+'del_libur',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' hari libur Berhasil Dihapus');
					load_data();
					$('#modal_libur').modal('hide');
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