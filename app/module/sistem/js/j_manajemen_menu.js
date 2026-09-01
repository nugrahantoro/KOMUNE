$(document).ready(function(){
	list_menu();
	window.setInterval(function()
	{
		chk_menu_order();
		chk_submenu_order();
	},500);
});

controller = 'sistem/ex/manajemen_menu/';

glob_id_menu=0;
glob_order='';
function chk_menu_order()
{
	tmp_order = [];
	$('.item_menu').each(function()
	{
		id=$(this).attr('id_menu');
		tmp_order.push(id);
	});
	tmp_order = tmp_order.join();
	if(tmp_order!=glob_order)
	{
		$('#btn_sort_menu').show();
	}
	else
	{
		$('#btn_sort_menu').hide();
	}
}

function sort_menu()
{
	tmp_order = [];
	$('.item_menu').each(function()
	{
		id=$(this).attr('id_menu');
		tmp_order.push(id);
	});
	dt={};
	dt.order = tmp_order;
	alertify.confirm('Konfirmasi', 'Simpan urutan Menu?', function(){
		ajx = $.post(controller+'sort_menu',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1) 
				{
					alertify.success("Urutan Menu disimpan");
					list_menu();
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
		load_show(ajx);
	},
	function(){});
	
}

glob_suborder='';
function chk_submenu_order()
{
	tmp_suborder = [];
	$('.item_submenu').each(function()
	{
		id=$(this).attr('id_submenu');
		tmp_suborder.push(id);
	});
	tmp_suborder = tmp_suborder.join();
	if(tmp_suborder!=glob_suborder)
	{
		$('#btn_sort_submenu').show();
	}
	else
	{
		$('#btn_sort_submenu').hide();
	}
}

function sort_submenu()
{
	tmp_suborder = [];
	$('.item_submenu').each(function()
	{
		id=$(this).attr('id_submenu');
		tmp_suborder.push(id);
	});
	dt={};
	dt.suborder = tmp_suborder;
	alertify.confirm('Konfirmasi', 'Simpan urutan Submenu?', function(){
		ajx = $.post(controller+'sort_submenu',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1) 
				{
					alertify.success("Urutan Submenu disimpan");
					list_submenu();
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
		load_show(ajx);
	},
	function(){});
	
}

function list_menu(focusto='')
{
	ajx=$.post(controller+'data_menu',function(raw)
	{
		$('#col_data_menu').html(raw);
		cari_menu('');
		if(focusto!='')
		{
			focusto=$(".item_menu[id_menu='"+focusto+"']");
			if(focusto.length==0)
			{
				isi="<div class='tc p-5' style='font-size: initial;'>\
												Klik Menu Accordion untuk menampilkan Submenu\
											</div>";
				$('#col_data_submenu').html(isi);
			}
			else
			{
				focusto.click();
				$('#col_data_menu').scrollTo(focusto);
			}
		}
		$(".item_menu[id_menu='"+glob_id_menu+"']").addClass("active");
	});
	load_ajx(ajx,'#col_par_menu');
}

function cari_menu(anim='slide')
{
	q=$('#q_menu').val();
	cb=cariable($('#col_data_menu .cari-item'),q,anim);
	if(cb.stat=='no_result' || cb.count==0)
	{
		$('.menu_no_result').show();
	}
	else
	{
		$('.menu_no_result').hide();
	}
}

function cari_submenu(anim='slide')
{
	q=$('#q_submenu').val();
	cb=cariable($('#col_data_submenu .cari-item'),q,anim);
	if(cb.stat=='no_result' || cb.count==0)
	{
		$('.submenu_no_result').show();
	}
	else
	{
		$('.submenu_no_result').hide();
	}
}

function cari_icon()
{
	q=$('#q_icon').val();
	cb=cariable($('#daftar_icon .icon-item'),q,'fade');
	if(cb.stat=='no_result' || cb.count==0)
	{
		$('.icon_no_result').show();
	}
	else
	{
		$('.icon_no_result').hide();
	}
}

$('.icon-item').click(function()
{
	icon = $(this).find('.cari-q').html();
	set_icon(icon);
});

function set_icon(icon)
{
	$('.icon-prev').html('<i data-feather="'+icon+'"></i>');
	$('#nm_icon').html(icon);
	feather.replace();
}

function modal_menu(mode=1,id_menu='')
{
	glob_mode=mode;
	glob_id_menu=id_menu;
	$('#modal_menu input').val('');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_menu').hide();
		set_icon('circle');
		verify_jenis_menu();
	}
	else
	{
		$('#btn_del_menu').show();
		dt={};
		dt.id_menu=id_menu;
		title='Edit';
		ajx=$.post(controller+'get_menu',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#mn_nama').val(dt.menu_name);
				$('#mn_jenis').val(dt.class);
				$('#mn_url').val(dt.url);
				$('#mn_urutan').val(dt.urutan);
				if(dt.img_two == null)
				{
					set_icon('circle');
				}
				else
				{
					set_icon(dt.img_two);
				}
				verify_jenis_menu();
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_menu .modal-body')},320);
	}
	$('#btn_save_menu span').html(title+' Menu');
	$('#ket_modal').html(title);
	cari_icon();
	$('#modal_menu').modal('show');
}

function modal_submenu(mode=1,id_submenu='')
{
	glob_mode=mode;
	glob_id_submenu=id_submenu;
	$('#modal_submenu input').val('');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_submenu').hide();
	}
	else
	{
		$('#btn_del_submenu').show();
		dt={};
		dt.id_submenu=id_submenu;
		title='Edit';
		ajx=$.post(controller+'get_submenu',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#smn_nama').val(dt.sub_menuname);
				$('#smn_url').val(dt.href);
				$('#smn_urutan').val(dt.urutan);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_submenu .modal-body')},320);
	}
	$('#btn_save_submenu span').html(title+' Submenu');
	$('#ket_modal_submenu').html(title);
	cari_icon();
	$('#modal_submenu').modal('show');
}

function verify_jenis_menu()
{
	jenis = $('#mn_jenis').val();
	if(jenis=='single')
	{
		$('#mn_url').prop('disabled',false);
		$('#mn_url').focus();
	}
	else
	{
		$('#mn_url').prop('disabled',true);
		$('#mn_url').val('');
	}
}

function menu_click(obj)
{
	glob_id_menu = $(obj).attr('id_menu');
	$('.item_menu').removeClass('active');
	$(obj).addClass('active');
	list_submenu();
}

function list_submenu()
{
	dt={};
	dt.id_menu = glob_id_menu;
	req=$.post(controller+"data_submenu",dt,function(raw)
	{
		if(isJson(raw))
		{
			dt = JSON.parse(raw);
			if(dt.count=='-')
			{
				reset_list_submenu();
			}
			else
			{
				$('.cur_menu').html("'"+dt.menu_name+"'");
				isi=dt.html;
				$('#col_data_submenu').html(isi);
				if(dt.count==0)
				{
					$('.submenu_no_result').show();
				}
				$('.submenu-elem').show();
			}
			focusto=$(".item_menu[id_menu='"+glob_id_menu+"']");
			if(focusto.length==0)
			{
				reset_list_submenu();
			}
		}
		else
		{
			alertify.alert('Alert',raw);
		}
	});
	load_ajx(req,'#root_submenu');
}

function reset_list_submenu()
{
	isi="<div class='tc p-5' style='font-size: initial;'>\
			Klik Menu Accordion untuk menampilkan Submenu\
		</div>";
	$('#col_data_submenu').html(isi);
	$('#cur_menu').html('');
	$('.submenu-elem').hide();
}

function save_menu()
{
	dt={};
	dt.mode = glob_mode;
	dt.id_menu = glob_id_menu;
	dt.nama = $('#mn_nama').val();
	dt.jenis = $('#mn_jenis').val();
	dt.url = $('#mn_url').val();
	dt.icon = $('#nm_icon').html();
	dt.urutan = parseInt($('#mn_urutan').val());
	if(isNaN(dt.urutan))
	{
		dt.urutan = 'NULL';
	}
	ket=['','Tambah','Edit'];
	ket2=['','Menu Ditambahkan','Perubahan Disimpan'];
	if(dt.nama=='' || dt.jenis==0 || (dt.jenis=='single' && dt.url==''))
	{
		alertify.error('Data belum lengkap');
		return;
	}
	alertify.confirm('Konfirmasi', ket[glob_mode]+' Menu?', function(){
		ajx = $.post(controller+'save_menu',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1) 
				{
					alertify.success(ket2[glob_mode]);
					list_menu(rs.id_menu);
					$('#modal_menu').modal('hide');
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
		load_show(ajx);
	},
	function(){});
}

function save_submenu()
{
	dt={};
	dt.mode = glob_mode;
	dt.id_menu = glob_id_menu;
	dt.id_submenu = glob_id_submenu;
	dt.nama = $('#smn_nama').val();
	dt.url = $('#smn_url').val();
	dt.urutan = parseInt($('#smn_urutan').val());
	if(isNaN(dt.urutan))
	{
		dt.urutan = 'NULL';
	}
	ket=['','Tambah','Edit'];
	ket2=['','Submenu Ditambahkan','Perubahan Disimpan'];
	if(dt.nama=='' ||  dt.url=='')
	{
		alertify.error('Data belum lengkap');
		return;
	}
	alertify.confirm('Konfirmasi', ket[glob_mode]+' Submenu?', function(){
		ajx = $.post(controller+'save_submenu',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1) 
				{
					alertify.success(ket2[glob_mode]);
					list_submenu();
					$('#modal_submenu').modal('hide');
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
		load_show(ajx);
	},
	function(){});
}

function del_menu(force=0)
{
	dt={};
	dt.id_menu = glob_id_menu;
	dt.force = force;
	ket=['Hapus Menu?','Beberapa User masih memiliki akses ke Menu dipilih. Tetap Hapus?'];
	alertify.confirm('Konfirmasi', ket[force], function(){
		ajx = $.post(controller+'del_menu',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 2) 
				{
					del_menu(1);
				}
				else
				if(rs.stat == 1) 
				{
					alertify.success("Menu dihapus");
					list_menu();
					list_submenu();
					$('#modal_menu').modal('hide');
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
		load_show(ajx);
	},
	function(){});
}

function del_submenu(force=0)
{
	dt={};
	dt.id_submenu = glob_id_submenu;
	dt.force = force;
	ket=['Hapus Submenu?','Beberapa User masih memiliki akses ke Submenu dipilih. Tetap Hapus?'];
	alertify.confirm('Konfirmasi', ket[force], function(){
		ajx = $.post(controller+'del_submenu',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 2) 
				{
					del_submenu(1);
				}
				else
				if(rs.stat == 1) 
				{
					alertify.success("Submenu dihapus");
					list_submenu();
					$('#modal_submenu').modal('hide');
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
		load_show(ajx);
	},
	function(){});
}
