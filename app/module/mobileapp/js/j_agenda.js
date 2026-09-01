$(document).ready(function(){
	load_data();
});

controller = 'mobileapp/ex/agenda/';

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
		$('#div_list_agenda').html(raw);
	});
	load_ajx(ajx);
}

function cari_agenda()
{
	txt = $('#cari_agenda').val();
	cari=cariable('#div_list_agenda',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.agenda_no_result').show(); 
	}
	else
	{
		$('.agenda_no_result').hide();
	}
}

function modal_agenda(event, mode = 1, id_agenda = 0)
{
	event.stopPropagation();

	glob_mode = mode;
	glob_id_agenda = id_agenda;

	$('#modal_agenda input').val('');
	$('#modal_agenda textarea').val('');
	$('#modal_agenda select').val('');

	if (mode == 1)
	{
		title = 'Tambah';

		$('#btn_del_agenda').hide();

		$('#frm_status').val('draft');
	}
	else
	{
		title = 'Edit';

		$('#btn_del_agenda').show();

		dt = {};
		dt.id_agenda = id_agenda;

		ajx = $.post(controller + 'get_data_agenda', dt, function(raw)
		{
			if (isJson(raw))
			{
				dt = JSON.parse(raw);

				$('#frm_judul').val(dt.judul);
				$('#frm_kegiatan').val(dt.deskripsi);
				$('#frm_kategori').val(dt.kategori);
				$('#frm_tanggal').val(dt.tanggal);
				$('#frm_lokasi').val(dt.lokasi);
				$('#frm_status').val(dt.status);
			}
		});

		setTimeout(function(){
			load_ajx(ajx, '#modal_body1');
		}, 225);
	}

	$('#btn_save_agenda span').html(title + ' Agenda');
	$('#ket_modal_kelas').html(title);

	$('#modal_agenda').modal('show');
}

function save_agenda()
{
	dt = {};

	dt.id_ta 		= $('#id_ta').val();
	dt.id_jenjang 	= $('#id_jenjang').val();

	dt.mode 		= glob_mode;
	dt.id_agenda 	= glob_id_agenda;

	dt.judul 		= $('#frm_judul').val();
	dt.kegiatan 	= $('#frm_kegiatan').val();
	dt.kategori 	= $('#frm_kategori').val();
	dt.tanggal 		= $('#frm_tanggal').val();
	dt.lokasi 		= $('#frm_lokasi').val();
	dt.status 		= $('#frm_status').val();

	if (
		dt.judul == '' ||
		dt.kegiatan == '' ||
		dt.kategori == '' ||
		dt.tanggal == '' ||
		dt.lokasi == ''
	)
	{
		alertify.error('Lengkapi data agenda terlebih dahulu');
		return;
	}

	mode_ket = ['', 'Tambah', 'Edit'];

	alertify.confirm(
		'Konfirmasi',
		mode_ket[dt.mode] + ' agenda warga?',
		function()
		{
			ajx = $.post(controller + 'save_agenda', dt, function(raw)
			{
				if (isJson(raw))
				{
					rs = JSON.parse(raw);

					if (rs.stat == 1)
					{
						alertify.success(mode_ket[dt.mode] + ' agenda berhasil');

						load_data();

						$('#modal_agenda').modal('hide');
					}
					else
					{
						alertify.alert('Alert', rs.ket);

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
		function(){}
	);
}

function del_agenda()
{
	dt = {};

	dt.id_agenda = glob_id_agenda;

	alertify.confirm(
		'Konfirmasi',
		'Hapus agenda warga ini?',
		function()
		{
			$.post(controller + 'del_agenda', dt, function(raw)
			{
				if (isJson(raw))
				{
					rs = JSON.parse(raw);

					if (rs.stat == 1)
					{
						alertify.success('Agenda berhasil dihapus');

						load_data();

						$('#modal_agenda').modal('hide');
					}
					else
					{
						alertify.alert('Alert', rs.ket);
					}
				}
				else
				{
					alertify.alert('Alert', raw);
				}
			});
		},
		function(){}
	);
}