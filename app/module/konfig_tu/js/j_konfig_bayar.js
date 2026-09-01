$(document).ready(function(){
	 kb_edit_tgl_jth = $("#kb_edit_tgl_jth").flatpickr({
		dateFormat: "d-m-Y"
	});
	kb_chk_edit_tgl_jth_val = $("#kb_chk_edit_tgl_jth_val").flatpickr({
		dateFormat: "d-m-Y"
	});
	  
	$('#modal_add_trans').on('click','#kb_add_siswa_list td, #kb_add_trans_list .chk_trigger',function(event) {
		if (!$(event.target).is('input[type="checkbox"]')) {
		  var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
		  //checkbox.prop('checked', !checkbox.prop('checked'));
		  checkbox.click();
		}
	});
});

ctr_kfg_bayar = 'konfig_tu/ex/konfig_bayar/';

function tab_konfig_bayar()
{
	kb_load_kelas();
}

function kb_load_kelas()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	ajx=$.post(ctr_kfg_bayar+'load_kelas',dt,function(raw)
	{
		$('#kb_div_list_kelas').html(raw);
		kb_active_kelas();
	});
	load_ajx(ajx,'#kb_div_kelas');
}

function kb_click_kelas(obj)
{
	id_kelas = $(obj).attr('id_kelas');
	kb_active_kelas(id_kelas);
}

glob_kb_kelas_active=0;
function kb_active_kelas(id_kelas='?')
{
	if(glob_kb_kelas_active==0)
	{
		id_kelas=$('#kb_div_list_kelas tbody tr').eq(0).attr('id_kelas');
		glob_kb_kelas_active = id_kelas;
	}
	if(id_kelas=='?')
	{
		id_kelas = glob_kb_kelas_active;
	}
	par = "#kb_div_list_kelas";
	glob_kb_kelas_active=id_kelas;
	$(par+' .kelas-item').removeClass('kelas-active');
	obj = $(par+" .kelas-item[id_kelas='"+id_kelas+"']");
	$(obj).addClass('kelas-active');
	nama_kelas = $(obj).find('td').eq(1).html();
	$('#kb_span_nm_kelas').html(nama_kelas);
	kb_load_list_trans();
}

function kb_cari_kelas()
{
	txt = $('#kb_cari_kelas').val();
	cari=cariable('#kb_div_list_kelas',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.kb_kelas_no_result').show(); 
	}
	else
	{
		$('.kb_kelas_no_result').hide();
	}
}

function kb_load_list_trans()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_kelas=glob_kb_kelas_active;
	ajx=$.post(ctr_kfg_bayar+'load_list_trans',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#kb_per_siswa').html(dt.per_siswa);
			$('#kb_per_jns_trans').html(dt.per_jns_trans);
		}
	});
	load_ajx(ajx,'#kb_div_trans');
}

function kb_cari_trans()
{
	txt = $('#kb_cari_trans').val();
	cari=cariable('#kb_div_trans',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#kb_div_trans .list_no_result').show(); 
	}
	else
	{
		$('#kb_div_trans .list_no_result').hide();
	}
}

function modal_show_trans(id_siswa=0,id_jns_trans=0)
{
	$('#modal_show_trans').modal('show');
	glob_id_siswa=id_siswa;
	glob_id_jns_trans=id_jns_trans;
	load_trans_view();
}

function load_trans_view()
{
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_siswa=glob_id_siswa;
	dt.id_jns_trans=glob_id_jns_trans;
	dt.id_kelas=glob_kb_kelas_active;
	kb_reset_edit_trans();
	ajx=$.post(ctr_kfg_bayar+'load_trans_view',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#kb_trans_view').html(dt.trans_siswa);
			checkboxTable('#kb_trans_view table', '#kb_cnt_trans_view, #kb_cnt_trans_view_del');
			data_trans = dt.trans;
		}
		else
		{
			alert(raw);
		}
	});
	load_show(ajx);
}

function kb_reset_edit_trans()
{
	glob_id_mst_trans=0;
	$('#modal_show_trans input').val('');
	kb_edit_tgl_jth.clear();
	kb_chk_edit_tgl_jth_val.clear();
}

function kb_save_edit_trans(mode=0)
{
	dt={};
	dt.nominal='';
	dt.tgl_jatuh_tempo='';
	c_trans=0;
	checked=0;
	if(mode==1)
	{
		dt.id_mst_trans=glob_id_mst_trans;
		dt.nominal=$('#kb_edit_nominal').val();
		dt.tgl_jatuh_tempo=$('#kb_edit_tgl_jth').val();
		if(dt.id_mst_trans==0)
		{
			alertify.error("Pilih Ketetapan yang akan di-edit");
			return;
		}
		if(dt.nominal=='')
		{
			alertify.error("Masukkan Nominal Ketetapan");
			return;
		}
	}
	else
	{
		if(!$('#kb_chk_edit_nominal').prop('checked') && !$('#kb_chk_edit_tgl_jth').prop('checked'))
		{
			alertify.error("Tandai Field yang akan di-edit");
			return;
		}
		else
		{
			id_trans = [];
			$('#kb_trans_view .chk_item').each(function()
			{
				if($(this).is(':checked'))
				{
					checked++;
					id_trans.push($(this).attr('id_trans'));
				}
				c_trans++;
			});
			dt.id_mst_trans=id_trans.join();
		}
		jml=$('#kb_cnt_trans_view').html();
		if(jml<1)
		{
			alertify.error("Tandai Ketetapan yang akan di-edit");
			return;
		}
		if($('#kb_chk_edit_nominal').prop('checked'))
		{
			dt.nominal=$('#kb_chk_edit_nominal_val').val();
			if(dt.nominal=='' || dt.nominal=='0')
			{
				alertify.error("Masukkan Nominal Ketetapan");
				return;
			}
		}
		if($('#kb_chk_edit_tgl_jth').prop('checked'))
		{
			dt.tgl_jatuh_tempo=$('#kb_chk_edit_tgl_jth_val').val();
			if(dt.tgl_jatuh_tempo=='')
			{
				alertify.error("Masukkan Tanggal Jatuh Tempo");
				return;
			}
		}
	}
	nominal_warn=''
	if(dt.nominal!='')
	{
		nominal_warn='(Apabila sudah ada pembayaran dan terdapat sisa pembayaran \
					maka uang sisa pembayaran akan dimasukkan ke uang saku \
					dan laporan harian pemasukkan akan berubah)';
	}
	mode_txt=['','dipilih','ditandai ('+checked+' Ketetapan)'];
	alertify.confirm('Konfirmasi', '<b>Ubah Ketetapan Pembayaran yang '+mode_txt[mode]+'?</b> <br> '+nominal_warn, function(){
		ajx=$.post(ctr_kfg_bayar+'save_edit_trans',dt,function(raw)
		{
			if(dt.nominal!='')
			{
				$('#modal_kb_lap_edit').modal('show');
				$('#div_lap_edit').html(raw);
			}
			load_trans_view();
			return;
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Kelas berhasil');
					$('#modal_kelas').modal('hide');
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

function cetak_report_edit_trans() {
	var all = document.getElementById("hasil_report_edit").innerHTML;
	window.open(ctr_kfg_bayar+"cetak_hasil_report/?data=" + all,'', "width=850,height=720,toolbar=no,resizable=yes");
}

function kb_del_trans(id_mst_trans)
{
	dt.id_mst_trans=id_mst_trans;
	return;
	alertify.confirm('Konfirmasi', '<b>Ubah Ketetapan Pembayaran yang '+mode_txt[mode]+'?</b> <br> '+nominal_warn, function(){
		$.post(ctr_kfg_bayar+'save_edit_trans',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Kelas berhasil');
					$('#modal_kelas').modal('hide');
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

function kb_edit_trans(id_mst_trans)
{
	glob_id_mst_trans=id_mst_trans;
	t=data_trans[id_mst_trans];
	$('#kb_edit_nama_siswa').val(t['nama_siswa']);
	$('#kb_edit_nama_transaksi').val(t['nama_transaksi']);
	$('#kb_edit_nominal').val(t['nominal']);
	kb_edit_tgl_jth.setDate(t['tgl_jatuh_tempo']);
	par='#kb_trans_view';
	$(par+' .trans-item').removeClass('kelas-active');
	obj = $(par+" .trans-item[id_trans='"+id_mst_trans+"']");
	$(obj).addClass('kelas-active');
	$('#collapseOne').collapse('show');
}

function kb_cari_trans_view()
{
	txt = $('#kb_cari_trans_view').val();
	cari=cariable('#kb_trans_view',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#kb_cari_trans_view .list_no_result').show(); 
	}
	else
	{
		$('#kb_cari_trans_view .list_no_result').hide();
	}
}

function modal_add_trans()
{
	$('#modal_add_trans').modal('show');
	$('#kb_add_trans_siswa_kls').val(glob_kb_kelas_active);
	kb_add_trans_list();
	kb_add_siswa_list();
}

function kb_add_trans_list()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_kelas=glob_kb_kelas_active;
	if (typeof jth_tempo !== 'undefined') {
		for (let i = 0; i < jth_tempo.length; i++) {
		  flatpickrInstance = jth_tempo[i];
		  if (flatpickrInstance) {
			flatpickrInstance.destroy();
		  }
		}
	}
	ajx=$.post(ctr_kfg_bayar+'load_add_trans_list',dt,function(raw)
	{
		$('#kb_add_trans_list').html(raw);
		jth_tempo = $("#kb_add_trans_list .tgl_jth").flatpickr({
			dateFormat: "d-m-Y"
		});
		checkboxTable('#kb_add_trans_list table', '#kb_add_trans_list_cnt');
	});
	load_ajx(ajx,'#kb_add_trans_list');
}

function kb_cari_jenis_trans()
{
	txt = $('#kb_cari_jenis_trans').val();
	console.log(txt);
	cari=cariable('#kb_add_trans_list',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#kb_add_trans_list .no_result').show(); 
	}
	else
	{
		$('#kb_add_trans_list .no_result').hide();
	}
	tampil = $('#kb_jenis_trans_tampil_chk').val();
	if(tampil!='all')
	{
		cari.count=0
		$('#kb_add_trans_list .cari-item:visible').each(function()
		{
			chk = $(this).find('.chk_item');
			if((chk.is(':checked') && tampil =='check') || (!chk.is(':checked') && tampil =='uncheck'))
			{
				$(this).show();
				cari.count++;
			}
			else
			{
				$(this).hide();
			}
		});
	}
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#kb_add_trans_list .no_result').show(); 
	}
	else
	{
		$('#kb_add_trans_list .no_result').hide();
	}
}

function kb_add_siswa_list()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_kelas=$('#kb_add_trans_siswa_kls').val();
	if(dt.id_kelas==0)
	{
		alertify.warning("Pilih Kelas Siswa");
		return;
	}
	ajx=$.post(ctr_kfg_bayar+'load_add_siswa_list',dt,function(raw)
	{
		$('#kb_add_siswa_list').html(raw);
		checkboxTable('#kb_add_siswa_list table', '#kb_add_siswa_list_cnt');
	});
	load_ajx(ajx,'#kb_add_siswa_list');
}

function kb_cari_add_siswa()
{
	par='#kb_add_siswa_list';
	txt = $('#kb_cari_add_siswa').val();
	cari=cariable(par,txt);
	tampil = $('#kb_cari_add_siswa_chk').val();
	if(tampil!='all')
	{
		cari.count=0
		$(par+' .cari-item:visible').each(function()
		{
			chk = $(this).find('.chk_item');
			if((chk.is(':checked') && tampil =='check') || (!chk.is(':checked') && tampil =='uncheck'))
			{
				$(this).show();
				cari.count++;
			}
			else
			{
				$(this).hide();
			}
		});
	}
	if(cari.stat=='no_result' || cari.count==0)
	{
		$(par+' .no_result').show(); 
	}
	else
	{
		$(par+' .no_result').hide();
	}
}


function kb_del_trans_multi()
{
	c_trans=0;
	checked=0;
	id_trans = [];
	$('#kb_trans_view .chk_item').each(function()
	{
		if($(this).is(':checked'))
		{
			checked++;
			id_trans.push($(this).attr('id_trans'));
		}
		c_trans++;
	})
	if(checked==0)
	{
		alertify.error("Tandai Transaksi Yg Akan  Dihapus");
		return;
	}
	 
	alertify.confirm('Konfirmasi', 'Hapus <b> '+checked+' Ketetapan </b>Pembayaran  Siswa?', function(){
		id_trans=id_trans.join();
		kb_del_trans(id_trans,0);
	},
	function(){});
}

function kb_del_trans(id_trans,dialog=1)
{
	dt={};
	dt.id_trans=id_trans;
	if(dialog==1)
	{
		alertify.confirm('Konfirmasi', 'Hapus Ketetapan Pembayaran?', function(){
			ajx=$.post(ctr_kfg_bayar+'del_trans',dt,function(raw)
			{
				alertify.success(raw);
				kb_load_list_trans();
				load_trans_view();
				load_show(ajx);
			});
		},
		function(){});
	}
	else
	{
		ajx=$.post(ctr_kfg_bayar+'del_trans',dt,function(raw)
			{
				alertify.success(raw);
				kb_load_list_trans();
				load_trans_view();
			});
		load_show(ajx);
	}
}

function add_ketetapan()
{
	trans={};
	num_trans=0;
	num_trans_empty=0;
	$('#kb_add_trans_list .chk_item').each(function()
	{
		if($(this).is(':checked'))
		{
			t={};
			t.id_mst_trans=$(this).attr('id_mst_trans');
			t.tgl_jatuh_tempo=$('#add_trans_'+t.id_mst_trans+' .tgl_jth').val();
			t.nominal=$('#add_trans_'+t.id_mst_trans+' .nominal').val();
			if(t.nominal=='')
			{
				num_trans_empty++;
			}
			trans[num_trans]=t;
			num_trans++;
		}
	})
	if(num_trans==0)
	{
		alertify.error("Tandai Jenis Transaksi");
		return;
	}
	if(num_trans_empty>0)
	{
		alertify.error("Isi Nominal Ketetapan");
		return;
	}
	siswa=[];
	num_siswa=0;
	$('#kb_add_siswa_list .chk_item').each(function()
	{
		if($(this).is(':checked'))
		{
			id_siswa=$(this).attr('id_siswa');
			num_siswa++;
			siswa.push(id_siswa);
		}
	})
	if(num_siswa==0)
	{
		alertify.error("Tandai Siswa yg akan ditetapkan");
		return;
	}
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_kelas=$('#kb_add_trans_siswa_kls').val();
	dt.trans=JSON.stringify(trans);
	dt.id_siswa=siswa;
	alertify.confirm('Konfirmasi', 'Tambahkan <b> '+num_trans+' Ketetapan </b>Pembayaran Baru Ke <b>'+num_siswa+' Siswa?', function(){
		ajx=$.post(ctr_kfg_bayar+'add_ketetapan',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.count > 0)
				{
					alertify.success('Total <b>'+rs.count+' Ketetapan </b> Berhasil Ditambahakan');
					kb_load_list_trans();
				}
				else
				{
					alertify.warning('Tidak ada penambahan Ketetapan Baru');
				}
			}
			else
			{
				alertify.error('ERROR');
			}
		});
		load_show(ajx);
	},
	function(){});
}