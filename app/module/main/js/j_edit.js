controller = 'main/ex/edit/';

function edit_user(){
	$('#modal_edit').modal('show');
}

function edit_user_baru(){
	dt={};
	dt.lama=$('#pass_lama').val();
	dt.baru=$('#pass_baru').val();
	ajx = $.post(controller+'/rubah_edit',dt,function(raw)
	{
		if(isJson(raw))
			{
			rs=JSON.parse(raw);
			if(rs.stat == 1)
			{
				alertify.success(' Berhasil');
				$('#modal_edit').modal('hide');
				window.location.reload()
			}
			else
			{
				alertify.error('Password lama tidak sama');
				$('#modal_edit').modal('hide');
				window.location.reload()
			}
		}
		else
		{
			alertify.alert('Alert', raw);
		}
	});
}

function modal_video_header(){
	$('#modal_video_header').modal('show');
}

function redirect_naikkan_header(){
	$('#modal_video_header').modal('hide');
	window.open('https://dev.sekolah-smartbilling.net/porting/faq/naikkan_kelas', '_blank');
}

function redirect_tagihan_header(){
	$('#modal_video_header').modal('hide');
	window.open('https://dev.sekolah-smartbilling.net/porting/faq/input_tagihan', '_blank');
}

function redirect_input_header(){
	$('#modal_video_header').modal('hide');
	window.open('https://dev.sekolah-smartbilling.net/porting/faq/input_siswa_baru', '_blank');
}