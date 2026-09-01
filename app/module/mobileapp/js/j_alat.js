$(document).ready(function(){
    load_data();
});

controller = 'mobileapp/ex/alat/';

function load_data()
{
	dt={};
	ajx=$.post(controller+'tampil_data',dt,function(raw)
	{
		$('#div_data_mode').html(raw);
	});
}

function cari_mode()
{
	txt = $('#cari_mode').val();
	cari=cariable('#div_data_mode',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.mode_no_result').show(); 
	}
	else
	{
		$('.mode_no_result').hide();
	}
}

function modal_mode(event,mode=1,id_mode=0)
{
	event.stopPropagation();
	glob_mode=mode;
	dt={};
	dt.id=id_mode;
	title='Edit';
	ajx=$.post(controller+'get_data_mode',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt = JSON.parse(raw);
			$('#frm_id').val(dt.id);
			$('#frm_tid').val(dt.tid);
		}
	});
	setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);

	$('#btn_save_mode span').html(title+' Mode');
	$('#ket_modal_mode').html(title);
	$('#modal_mode').modal('show');
}

function save_mode()
{
	dt={}
	dt.mode=glob_mode;
	dt.id=$('#frm_id').val();
	dt.tid=$('#frm_tid').val();
	if(dt.id=='' || dt.tid=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	alertify.confirm('Konfirmasi Edit TID?', function(){
		$.post(controller+'save_mode',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' TID');
					load_data();
					$('#modal_mode').modal('hide');
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

function ubah(id='')
{
	dt={};
	dt.id=id;
	alertify.confirm('Konfirmasi','Jadikan default?', function(){
		ajx=$.post(controller+"ubah", dt, function(raw)
		{
			load_data();
		});
		load_ajx(ajx,'#div_data_mode');
	},
	function(){});
}