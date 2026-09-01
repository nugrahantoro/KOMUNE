$(document).ready(function(){
	load_kelas();
});

controller = 'mobileapp/ex/aktivitas/';

function pilih_tahun(){
	tab_aktivitas();
	tab_konfig_aktivitas();
}

function jenjang(){
	tab_aktivitas();
	tab_konfig_aktivitas();
}


/* TAB AKTIVITAS */
function tab_aktivitas()
{
	load_kelas();
}

function load_kelas()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	ajx=$.post(controller+'load_kelas',dt,function(raw)
	{
		$('#div_list_kelas').html(raw);
		active_kelas();
	});
	load_ajx(ajx,'#div_kelas');
}

function click_kelas(obj)
{
	id_kelas = $(obj).attr('id_kelas');
	active_kelas(id_kelas);
}

glob_kb_kelas_active=0;

function active_kelas(id_kelas='?')
{
	if(id_kelas=='?')
	{
		id_kelas = glob_kb_kelas_active;
	}
	else{
		id_kelas=$('#div_list_kelas tbody tr').eq(0).attr('id_kelas');
		glob_kb_kelas_active = id_kelas;
	}
	par = "#div_list_kelas";
	glob_kb_kelas_active=id_kelas;
	$(par+' .kelas-item').removeClass('kelas-active');
	obj = $(par+" .kelas-item[id_kelas='"+id_kelas+"']");
	$(obj).addClass('kelas-active');
	nama_kelas = $(obj).find('td').eq(1).html();
	$('#span_nm_kelas').html(nama_kelas);
	load_siswa();
}

function cari_kelas()
{
	txt = $('#cari_kelas').val();
	cari=cariable('#div_list_kelas',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.kelas_no_result').show(); 
	}
	else
	{
		$('.kelas_no_result').hide();
	}
}

function load_siswa(){
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_kelas=glob_kb_kelas_active;
	ajx=$.post(controller+'load_siswa',dt,function(raw)
	{
		$('#div_list_siswa').html(raw);
	});
	load_ajx(ajx,'#div_siswa');
}

function cari_siswa()
{
	txt = $('#cari_siswa').val();
	cari=cariable('#div_list_siswa',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.siswa_no_result').show(); 
	}
	else
	{
		$('.siswa_no_result').hide();
	}
}

function click_siswa(id_siswa){
	title='Aktivitas Siswa';
	$('#ket_modal_detail_aktivitas').html(title);
	$('#modal_detail_aktivitas').modal('show');
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_siswa=id_siswa;
	dt.id_kelas=glob_kb_kelas_active;
	ajx=$.post(controller+'load_aktivitas',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#kh_nama').html(dt.nama);
			$('#kh_kelas').html(dt.kelas);
			$('#sek_nama').val(dt.nama);
			$('#sek_kelas').val(dt.kelas);
			$('#kh_nis').val(dt.id_siswa);
			lembar_aktivitas();
		}
		else
		{
			alert(raw);
		}
	});
	load_ajx(ajx,'#div_aktivitas');
}

function lembar_aktivitas(){
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_siswa=$('#kh_nis').val();
	dt.year=$('#year').val();
	dt.month=$('#month').val();
	ajx=$.post(controller+'lembar_aktivitas',dt,function(raw)
	{
		$('#div_list_aktivitas').html(raw);
	});
	load_ajx(ajx,'#div_list_aktivitas');
}

function cetak_mount(bln='',thn=''){
	id_jenjang=$('#id_jenjang').val();
	id_ta=$('#id_ta').val();
	id_siswa=$('#kh_nis').val();
	nama=$('#sek_nama').val();
	kelas=$('#sek_kelas').val();
	var url = controller+"cetak_lembar_aktivitas/?bulan="+bln+"&tahun="+thn+"&id_jenjang="+id_jenjang+"&id_ta="+id_ta+"&id_siswa="+id_siswa+"&nama="+nama+"&kelas="+kelas;
	window.open(url);
}

/* TAB KONFIG AKTIVITAS */
function tab_konfig_aktivitas()
{
	load_item();
}

function load_item()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	ajx=$.post(controller+'load_item',dt,function(raw)
	{
		$('#div_list_item').html(raw);
		active_kategori();
	});
	load_ajx(ajx,'#div_item');
}

glob_id_item=0;

function modal_item(event,mode=1, id_item=0){
	event.stopPropagation();
	glob_mode=mode;
	glob_id_item=id_item;
	$('#modal_item input').val('');
	$('#modal_item select').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_item').hide();
	}
	else 
	{
		$('#btn_del_item').show();
		dt={};
		dt.id_item=id_item;
		title='Edit';
		ajx=$.post(controller+'get_data_item',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#frm_nama_item').val(dt.kategori);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_item span').html(title+' Item');
	$('#ket_modal_item').html(title);
	$('#modal_item').modal('show');
}

function save_item(){
	dt={};
	dt.mode=glob_mode;
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_item=glob_id_item;
	dt.nama_item=$('#frm_nama_item').val();
	if(dt.nama_item=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Item?', function(){
		$.post(controller+'save_item',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Item Berhasil');
					tab_konfig_aktivitas();
					$('#modal_item,').modal('hide');
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

function del_item(){
	dt={};
	dt.id_item=glob_id_item;
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	alertify.confirm('Konfirmasi','Hapus Item?', function(){
	$.post(controller+'del_item',dt,function(raw)
	{
		if(isJson(raw))
		{
			rs=JSON.parse(raw);
			if(rs.stat == 1)
			{
				alertify.success(' Item dihapus');
				tab_konfig_aktivitas();
				$('#modal_item').modal('hide');
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

function click_kategori(id_kategori='0')
{
	active_kategori(id_kategori);
}

glob_kb_item_active=0;

function active_kategori(id_kategori='?')
{
	if(glob_kb_item_active==0)
	{
		id_kategori=$('#div_list_item tbody tr').eq(0).attr('id_kategori');
		glob_kb_item_active = id_kategori;
	}
	if(id_kategori=='?')
	{
		id_kategori = glob_kb_item_active;
	}
	par = "#div_list_item";
	glob_kb_item_active=id_kategori;
	$(par+' .kategori-item').removeClass('kategori-active');
	obj = $(par+" .kategori-item[id_kategori='"+id_kategori+"']");
	$(obj).addClass('kategori-active');
	nama_item = $(obj).find('td').eq(1).html();
	$('#span_nm_item').html(nama_item);
	load_sub_item();
}

function cari_item()
{
	txt = $('#cari_item').val();
	cari=cariable('#div_list_item',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.kategori_no_result').show(); 
	}
	else
	{
		$('.kategori_no_result').hide();
	}
}

function load_sub_item(){
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_item=glob_kb_item_active;
	ajx=$.post(controller+'load_sub_item',dt,function(raw)
	{
		$('#div_list_sub_item').html(raw);
	});
	load_ajx(ajx,'#div_sub_item');
}

glob_id_sub_item=0;

function modal_sub_item(event,mode=1, id_sub_item=0){
	event.stopPropagation();
	glob_mode=mode;
	glob_id_sub_item=id_sub_item;
	$('#modal_sub_item input').val('');
	$('#modal_sub_item select').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#div_gambar').show();
		$('#btn_del_sub_item').hide();
	}
	else 
	{
		$('#div_gambar').hide();
		$('#btn_del_sub_item').show();
		dt={};
		dt.id_sub_item=id_sub_item;
		title='Edit';
		ajx=$.post(controller+'get_data_sub_item',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#frm_nama_sub_item').val(dt.item);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_sub_item span').html(title+' Sub Item');
	$('#ket_modal_sub_item').html(title);
	$('#modal_sub_item').modal('show');
}

function save_sub_item(event,mode=1, id_sub_item=0)
{
	dt={}
	dt.mode=glob_mode;

	var data = new FormData();
    data.append("gambar", document.getElementById('frm_gambar_item').files[0]);
	data.append("sub_item", $('#frm_nama_sub_item').val());
	data.append("mode", glob_mode);
	data.append("id_item", glob_kb_item_active);
	data.append("id_jenjang", $('#id_jenjang').val());
	data.append("id_ta", $('#id_ta').val());
	data.append("id_sub_item", glob_id_sub_item);

	if (data.get('sub_item') == '' || data.get('id_item') == '') {
        alertify.error('Data Belum Lengkap');
		return;
    }
    mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Sub Item?', function(){
		ajx=$.ajax({
            url: controller+'save_sub_item',
            type: 'POST',
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (raw) {
              	if(isJson(raw))
				{
					rs=JSON.parse(raw);
					if(rs.stat == 1)
					{
						alertify.success(rs.ket);
						load_sub_item();
						$('#modal_sub_item').modal('hide');
					}
					else
					{
						alertify.alert('Alert',rs.ket);
						load_sub_item();
					}
				}
				else
				{
					alertify.alert('Alert', raw);
				}
            }, error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
            }
        });
        load_ajx(ajx,'#col-userkeu');
	},
	function(){});	
}

function del_sub_item()
{
	dt={};
	dt.id_sub_item=glob_id_sub_item;
	alertify.confirm('Konfirmasi','Hapus Sub Item?', function(){
		$.post(controller+'del_sub_item',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Sub Item Berhasil Dihapus');
					load_sub_item();
					$('#modal_sub_item').modal('hide');
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

function cari_sub_item()
{
	txt = $('#cari_sub_item').val();
	cari=cariable('#div_list_sub_item',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.sub_no_result').show(); 
	}
	else
	{
		$('.sub_no_result').hide();
	}
}