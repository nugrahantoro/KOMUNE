


controller = 'sistem/ex/manajemen_user/';

function tampil_data()
{
	dt={};
	$.post(controller+'tampil_data',dt,function(raw)
	{
		$('#div_data_user').html(raw);
	});
}

function cari_akses()
{
	q=$('#q_akses').val();
	cb=cariable('#div_akses',q,'fade');
	console.log(cb);
	if(cb.stat=='no_result' || cb.count==0)
	{
		$('.akses_no_result').show();
	}
	else
	{
		$('.akses_no_result').hide();
	}
}

function cari_user()
{
	q=$('#q_user').val();
	cb=cariable($('#col_data_user .cari-item'),q,'slide');
	if(cb.stat=='no_result' || cb.count==0)
	{
		$('.user_no_result').show();
	}
	else
	{
		$('.user_no_result').hide();
	}
}

glob_id_user = 0;
function user_click(obj)
{
	dt={};
	dt.id_user = $(obj).attr('id_user');
	if(glob_id_user == dt.id_user)
	{
		return;
	}
	glob_id_user = dt.id_user;
	req=$.post(controller+"load_akses",dt,function(raw)
	{
		$('#div_akses').html(raw);
		$('.akses-elem').show();
	});
	load_ajx(req,'#root_akses');
	$('.item_user').removeClass('active');
	$(obj).addClass('active');
}

function save_akses()
{
	dt={};
	dt.id_user = glob_id_user;
	dt.id_menu = [];
	$('#div_akses input').each(function()
	{
		if($(this).is(':checked'))
		{
			id_menu = $(this).attr('id_menu');
			dt.id_menu.push(id_menu);
		}
	});
	alertify.confirm('Konfirmasi', 'Simpan Akses Menu?', function(){
		ajx = $.post(controller+'save_akses',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1) 
				{
					alertify.success('Akses Menu disimpan');
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
		load_ajx(req,'#root_akses');
	},
	function(){});
}

