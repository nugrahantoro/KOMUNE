$(document).ready(function(){
	
	in_tanggal = $("#in_tanggal").flatpickr({
		dateFormat: "d-m-Y H:i",
		enableTime: true,
		time_24hr: true
	});
	
	change_periode();
	load_jenis_kat();
	reset_form();
	
});

glob_id_trans=0;
//EVENT EVENT


ctr_inp_kas = 'konfig_tu/ex/inp_kas_in/';

glob_mode_entry=1;
function set_mode_entry(val)
{
	$('.col-mode-entry input').prop('checked',false);
	$('#btn_mode_'+val).prop('checked',true);
	glob_mode_entry = val;
	console.log(glob_mode_entry);
}

function change_periode()
{
	init_tgl();
	load_list_trans();
	edit_close();
}

function init_tgl()
{
	sel_date = $('#periode_kas').val()+'-01';
	day = getFirstAndLastDateOfCurrentMonth(sel_date);
	firstDay=day.firstDay;
	lastDay=day.lastDay;
	
	$('#tgl_awal').val(firstDay);
	$('#tgl_akhir').val(lastDay);
	
	tgl_awal = $("#tgl_awal").flatpickr({
		dateFormat: "d-m-Y",
		maxDate: lastDay,
		minDate: firstDay
	});
	tgl_akhir = $("#tgl_akhir").flatpickr({
		dateFormat: "d-m-Y",
		maxDate: lastDay,
		minDate: firstDay
	});
}

function load_jenis_kat()
{
	dt={};
	dt.mode_entry=glob_mode_entry;
	$.post(ctr_inp_kas+'load_jenis_kat',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#in_jenis').html(dt.in_jenis[mode_entry]);
			dt_kategori=dt.kategori;
			dt_subkat=dt.subkat;
		}
		else
		{
			alertify.error("Error");
		}
	});
	
	$('.row_kat').hide();
	$('.row_subkat').hide();
}

function list_kategori()
{
	id_jns = $('#in_jenis').val();
	opt="<option value='0'>-pilih-</option>";
	$('.row_kat').hide();
	$('.row_subkat').hide();
	if(id_jns!=0)
	{
		if (typeof dt_kategori[id_jns] !== 'undefined') {
			$('.row_kat').show();
			kat = dt_kategori[id_jns];
			
			for(i=0;i<kat.length;i++)
			{
				opt+="<option value='"+kat[i]['id_kat_trans']+"'>"+kat[i]['ref']+" "+kat[i]['nama_kat_trans']+"</option>";
			}
		}
		
	}
	$('#in_kategori').html(opt);
}

function list_subkat()
{
	id_kat = $('#in_kategori').val();
	opt="<option value='0'>-pilih-</option>";
	$('.row_subkat').hide();
	if(id_kat!=0)
	{
		if (typeof dt_subkat[id_kat] !== 'undefined') {
			$('.row_subkat').show();
			subkat = dt_subkat[id_kat];
			console.log(subkat.length);
			for(i=0;i<subkat.length;i++)
			{
				opt+="<option value='"+subkat[i]['id_subkat_trans']+"'>"+subkat[i]['ref']+" "+subkat[i]['nama_subkat_trans']+"</option>";
			}
		}
		
	}
	$('#in_subkategori').html(opt);
}

function reset_form()
{
	$('#in_trans, #in_nominal').val('');
	$('#frm_trans select').val('0');
	glob_mode=1;
	list_kategori(0);
	list_subkat(0);
	tgl = $('#in_tanggal').val();
	if(isCurrentPeriode())
	{
		in_tanggal.setDate(getCurrentDateTime());
	}
}

function save_trans()
{
	dt={};
	dt.mode=glob_mode;
	dt.id_trans=glob_id_trans;
	dt.jenis=$('#in_jenis').val();
	dt.kategori=$('#in_kategori').val();
	dt.subkategori=$('#in_subkategori').val();
	dt.tanggal=$('#in_tanggal').val();
	dt.trans=$('#in_trans').val();
	dt.nominal=rmSep($('#in_nominal').val());
	
	if(dt.jenis==0 || dt.tanggal=='' || dt.trans=='' || dt.nominal=='')
	{
		alertify.error("Data Belum Lengkap");
		return;
	}
	
	mode_txt=['','Simpan','Edit'];
	alertify.confirm('Konfirmasi', mode_txt[glob_mode]+' Data Transaksi?', function(){
		
		ajx=$.post(ctr_inp_kas+'save_trans', dt, function(raw) 
			{
				if(raw.trim()=='ok')
				{
					alertify.success("Disimpan");
					edit_close();
					load_list_trans();
				}
				else
				{
					alertify.error("Error");
				}
			});
		load_show(ajx);
	},
	function(){});
}

glob_mode=1;
function edit_data(id_trans)
{
	glob_mode=2;
	glob_id_trans = id_trans;
	$('.mode_add').hide();
	$('.mode_edit').show();
	$("#div_list_trans tr").removeClass('active');
	$("tr[id_trans='"+id_trans+"'").addClass('active');
	$('#spn_mode').html('Edit');
	
	dt={};
	dt.id_trans=id_trans;
	ajx=$.post(ctr_inp_kas+'get_data_trans',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#in_jenis').val(dt.id_jns_trans);
			list_kategori(dt.id_jns_trans);
			$('#in_kategori').val(dt.id_kat_trans);
			list_subkat(dt.id_kat_trans);
			$('#in_subkategori').val(dt.id_subkat_trans);
			in_tanggal.setDate(dt.tanggal);
			$('#in_trans').val(dt.keterangan);
			$('#in_nominal').val(sep(dt.nominal_in));
		}
	});
	load_ajx(ajx,'#col-form-input');
}

function edit_close()
{
	glob_mode=2;
	glob_id_trans = 0;
	$('.mode_add').show();
	$('.mode_edit').hide();
	$("#div_list_trans tr").removeClass('active');
	$('#spn_mode').html('Input');
	reset_form();
}

function load_list_trans()
{
	dt={};
	dt.tgl_awal=$("#tgl_awal").val();
	dt.tgl_akhir=$("#tgl_akhir").val();
	ajx = $.post(ctr_inp_kas+'load_list_trans',dt,function(raw)
	{
		$('#div_list_trans').html(raw);
	});
	load_ajx(ajx,'#div_list_trans');
}

function cari_transaksi()
{
	txt = $('#cari_transaksi').val();
	cari=cariable('#div_list_trans',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#cari_transaksi .no_result').show(); 
	}
	else
	{
		$('#cari_transaksi .no_result').hide();
	}
}

function del_transaksi()
{
		dt={};
		dt.id_trans=glob_id_trans;
		alertify.confirm('Konfirmasi','Hapus Transaksi Transaksi?', function(){
		$.post(ctr_inp_kas+'del_transaksi',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success('Data Berhasil Dihapus');
					load_list_trans();
					edit_close();
				}
				else
				{
					alertify.error('Alert',rs.ket);
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


function download_excel()
{
	tgl_awal=$("#tgl_awal").val();
	tgl_akhir=$("#tgl_akhir").val();
	periode=$("#periode_kas").val();
	window.open(ctr_inp_kas+"download_excel/?tgl_awal=" + tgl_awal+"&tgl_akhir="+tgl_akhir+"&periode="+periode);
}

function modal_master_jenis_kat()
{
	$('#modal_master_jenis_kat').modal('show');
	load_list_jenis();
	load_list_kat();
	load_list_subkat();
}

function load_list_jenis()
{
	dt={};
	ajx = $.post(ctr_inp_kas+'load_list_jenis',dt,function(raw)
	{
		$('#div_list_jenis').html(raw);
		$("#div_list_jenis tr[id_jenis='"+glob_id_jenis+"'").addClass('active');
		
		$('#div_list_jenis tr[data-bs-toggle="tooltip"]').tooltip(); 				
		
	});
	load_ajx(ajx,'#div_list_jenis');
}

function cari_jenis()
{
	txt = $('#cari_jenis').val();
	cari=cariable('#div_list_jenis',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#div_list_jenis .no_result').show(); 
	}
	else
	{
		$('#div_list_jenis .no_result').hide();
	}
}

glob_id_jenis=0;
function modal_jenis(mode,id_jenis=0)
{
	glob_jenis_mode=mode;
	$('#modal_jenis').modal('show');
	$('#modal_jenis input').val('');
	ket_txt=['','Tambah','Edit'];
	$('#modal_jenis .ket_modal').html(ket_txt[mode]);
	if(mode==1)
	{
		$('#modal_jenis .btn_hapus').hide();
	}
	else
	{
		glob_id_jenis=id_jenis;
		dt={};
		dt.id_jenis = id_jenis;
		ajx=$.post(ctr_inp_kas+'load_data_jenis',dt,function(raw)
		{
			if(isJson(raw))
			{
				jn = JSON.parse(raw);
				$('#jns_ref').val(jn.ref);
				$('#jns_nama').val(jn.nama_jns_trans);
			}
		});
		load_show(ajx);
		$('#modal_jenis .btn_hapus').show();
	}
}

function save_jenis()
{
	dt={};
	dt.mode=glob_jenis_mode;
	dt.id_jenis=glob_id_jenis;
	dt.ref=$('#jns_ref').val();
	dt.nama=$('#jns_nama').val();
	ket_txt=['','Tambah','Edit'];
	if(dt.nama=='')
	{
		alertify.error("Data Belum Lengkap");
		return;
	}
	alertify.confirm('Konfirmasi', ket_txt[dt.mode]+' Jenis?', function(){
		$.post(ctr_inp_kas+'save_jenis',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(ket_txt[dt.mode]+' data berhasil');
					load_list_jenis();
					load_jenis_kat();
					$('#modal_jenis').modal('hide');
					load_jenis_kat();
				}
				else
				{
					alertify.alert('Alert',rs.ket);
				}
			}
			else
			{
				alertify.error('Error');
			}
		});
	},
	function(){});
}

function del_jenis()
{
	dt={};
	dt.id_jenis = glob_id_jenis;
	alertify.confirm('Konfirmasi', "Hapus Data Jenis?", function(){
		ajx=$.post(ctr_inp_kas+'del_jenis',dt,function(raw)
		{
			if(raw.trim()=='')
			{
				alertify.success("Dihapus");
				$('#modal_jenis').modal('hide');
				glob_id_jenis=0;
				load_list_jenis();
				load_list_kat();
			}
			else
			{
				alertify.error("Error : "+raw);
			}
		});
		load_show(ajx);
	},
	function(){});
}

function click_jenis(obj,id_jenis)
{
	glob_id_jenis = id_jenis;
	glob_id_kat = 0;
	glob_id_subkat = 0;
	par=$(obj).parent('tbody');
	par.find('tr').removeClass('active');
	$(obj).addClass('active');
	load_list_kat();
	load_list_subkat();
}

function load_list_kat()
{
	dt={};
	dt.id_jenis=glob_id_jenis;
	if(dt.id_jenis==0)
	{
		$('#div_default_kat').show();
		$('#div_list_kat').hide();
		return
	}
	
	ajx = $.post(ctr_inp_kas+'load_list_kat',dt,function(raw)
	{
		
		$('#div_default_kat').hide();
		$('#div_list_kat').show();
		$('#div_list_kat').html(raw);
		$("#div_list_kat tr[id_kat='"+glob_id_kat+"'").addClass('active');
		$('#div_list_kat tr[data-bs-toggle="tooltip"]').tooltip(); 
	});
	load_ajx(ajx,'#div_list_kat');
}

function cari_kat()
{
	txt = $('#cari_kat').val();
	cari=cariable('#div_list_kat',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#div_list_kat .no_result').show(); 
	}
	else
	{
		$('#div_list_kat .no_result').hide();
	}
}

glob_id_kat=0;
function modal_kat(mode,id_kat=0)
{
	if(glob_id_jenis==0)
	{
		return;
	}
	glob_kat_mode=mode;
	$('#modal_kat').modal('show');
	$('#modal_kat input').val('');
	ket_txt=['','Tambah','Edit'];
	$('#modal_kat .ket_modal').html(ket_txt[mode]);
	nm_jenis = $("tr[id_jenis='"+glob_id_jenis+"'] td").eq(0).html();
	nm_jenis += ' '+$("tr[id_jenis='"+glob_id_jenis+"'] td").eq(1).html();
	ref_jenis=$("tr[id_jenis='"+glob_id_jenis+"'] td").eq(0).html();
	$('#kat_jenis').val(nm_jenis);
	if(mode==1)
	{
		$('#modal_kat .btn_hapus').hide();
		$('#kat_ref').val(ref_jenis+'.');
	}
	else
	{
		glob_id_kat=id_kat;
		dt={};
		dt.id_kat = id_kat;
		ajx=$.post(ctr_inp_kas+'load_data_kat',dt,function(raw)
		{
			if(isJson(raw))
			{
				jn = JSON.parse(raw);
				$('#kat_ref').val(jn.ref);
				$('#kat_nama').val(jn.nama_kat_trans);
			}
		});
		load_show(ajx);
		$('#modal_kat .btn_hapus').show();
	}
}

function save_kat()
{
	dt={};
	dt.mode=glob_kat_mode;
	dt.id_kat=glob_id_kat;
	dt.id_jenis=glob_id_jenis;
	dt.ref=$('#kat_ref').val();
	dt.nama=$('#kat_nama').val();
	ket_txt=['','Tambah','Edit'];
	if(dt.nama=='')
	{
		alertify.error("Data Belum Lengkap");
		return;
	}
	alertify.confirm('Konfirmasi', ket_txt[dt.mode]+' Kategori?', function(){
		$.post(ctr_inp_kas+'save_kat',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(ket_txt[dt.mode]+' data berhasil');
					load_list_kat();
					load_list_jenis();
					$('#modal_kat').modal('hide');
					load_jenis_kat();
				}
				else
				{
					alertify.alert('Alert',rs.ket);
				}
			}
			else
			{
				alertify.error('Error');
			}
		});
	},
	function(){});
}

function del_kat()
{
	dt={};
	dt.id_kat = glob_id_kat;
	alertify.confirm('Konfirmasi', "Hapus Data Kategori?", function(){
		ajx=$.post(ctr_inp_kas+'del_kat',dt,function(raw)
		{
			if(raw.trim()=='')
			{
				alertify.success("Dihapus");
				$('#modal_kat').modal('hide');
				glob_id_kat=0;
				load_list_kat();
			}
			else
			{
				alertify.error("Error : "+raw);
			}
		});
		load_show(ajx);
	},
	function(){});
}

function click_kat(obj,id_kat)
{
	glob_id_kat = id_kat;
	par=$(obj).parent('tbody');
	par.find('tr').removeClass('active');
	$(obj).addClass('active');
	load_list_subkat();
}

function load_list_subkat()
{
	dt={};
	dt.id_kat=glob_id_kat;
	if(dt.id_kat==0)
	{
		$('#div_default_subkat').show();
		$('#div_list_subkat').hide();
		return
	}
	
	ajx = $.post(ctr_inp_kas+'load_list_subkat',dt,function(raw)
	{
		
		$('#div_default_subkat').hide();
		$('#div_list_subkat').show();
		$('#div_list_subkat').html(raw);
		$("#div_list_subkat tr[id_subkat='"+glob_id_subkat+"'").addClass('active');
	});
	load_ajx(ajx,'#div_list_subkat');
}

function cari_subkat()
{
	txt = $('#cari_subkat').val();
	cari=cariable('#div_list_subkat',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#div_list_subkat .no_result').show(); 
	}
	else
	{
		$('#div_list_subkat .no_result').hide();
	}
}

glob_id_subkat=0;
function modal_subkat(mode,id_subkat=0)
{
	if(glob_id_kat==0)
	{
		return;
	}
	glob_subkat_mode=mode;
	$('#modal_subkat').modal('show');
	$('#modal_subkat input').val('');
	ket_txt=['','Tambah','Edit'];
	$('#modal_subkat .ket_modal').html(ket_txt[mode]);
	
	nm_jenis = $("tr[id_jenis='"+glob_id_jenis+"'] td").eq(0).html();
	nm_jenis += ' '+$("tr[id_jenis='"+glob_id_jenis+"'] td").eq(1).html();
	
	nm_kat = $("tr[id_kat='"+glob_id_kat+"'] td").eq(0).html();
	nm_kat += ' '+$("tr[id_kat='"+glob_id_kat+"'] td").eq(1).html();
	ref_kat=$("tr[id_kat='"+glob_id_kat+"'] td").eq(0).html();
	
	$('#subkat_jenis').val(nm_jenis);
	$('#subkat_kat').val(nm_kat);
	$('#subkat_kat').val(nm_kat);
	if(mode==1)
	{
		$('#modal_subkat .btn_hapus').hide();
		$('#subkat_ref').val(ref_kat+'.');
	}
	else
	{
		glob_id_subkat=id_subkat;
		dt={};
		dt.id_subkat = id_subkat;
		ajx=$.post(ctr_inp_kas+'load_data_subkat',dt,function(raw)
		{
			if(isJson(raw))
			{
				jn = JSON.parse(raw);
				$('#subkat_ref').val(jn.ref);
				$('#subkat_nama').val(jn.nama_subkat_trans);
			}
		});
		load_show(ajx);
		$('#modal_subkat .btn_hapus').show();
	}
}

function save_subkat()
{
	dt={};
	dt.mode=glob_subkat_mode;
	dt.id_subkat=glob_id_subkat;
	dt.id_kat=glob_id_kat;
	dt.ref=$('#subkat_ref').val();
	dt.nama=$('#subkat_nama').val();
	ket_txt=['','Tambah','Edit'];
	if(dt.nama=='')
	{
		alertify.error("Data Belum Lengkap");
		return;
	}
	alertify.confirm('Konfirmasi', ket_txt[dt.mode]+' Sub Kategori?', function(){
		$.post(ctr_inp_kas+'save_subkat',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(ket_txt[dt.mode]+' data berhasil');
					load_list_subkat();
					load_list_kat();
					$('#modal_subkat').modal('hide');
					load_jenis_kat();
				}
				else
				{
					alertify.alert('Alert',rs.ket);
				}
			}
			else
			{
				alertify.error('Error');
			}
		});
	},
	function(){});
}

function del_subkat()
{
	dt={};
	dt.id_subkat = glob_id_subkat;
	alertify.confirm('Konfirmasi', "Hapus Data Sub Kategori?", function(){
		ajx=$.post(ctr_inp_kas+'del_subkat',dt,function(raw)
		{
			if(raw.trim()=='')
			{
				alertify.success("Dihapus");
				$('#modal_subkat').modal('hide');
				glob_id_subkat=0;
				load_list_subkat();
			}
			else
			{
				alertify.error("Error : "+raw);
			}
		});
		load_show(ajx);
	},
	function(){});
}

function isCurrentPeriode() {
	sel_periode=$('#periode_kas').val();
    const date = new Date(sel_periode);
    const currentDate = new Date();
    
    return date.getFullYear() === currentDate.getFullYear() && date.getMonth() === currentDate.getMonth();
}

function getCurrentDateTime() {
  now = new Date();
  day = String(now.getDate()).padStart(2, '0');
  month = String(now.getMonth() + 1).padStart(2, '0');
  year = now.getFullYear();
  hours = String(now.getHours()).padStart(2, '0');
  minutes = String(now.getMinutes()).padStart(2, '0');

  return `${day}-${month}-${year} ${hours}:${minutes}`;
}

function getFirstAndLastDateOfCurrentMonth(datestring='') {
	if(datestring=='')
	{
		today = new Date();
	}
	else
	{
		today = new Date(datestring);
	}
  year = today.getFullYear();
  month = today.getMonth() + 1; // Months are zero-indexed

  // Get the first day of the month
  firstDay = new Date(year, month - 1, 1);
  formattedFirstDay = formatDate(firstDay);

  // Get the last day of the next month and subtract a day to get the last day of the current month
  lastDay = new Date(year, month, 0);
  formattedLastDay = formatDate(lastDay);

  return { firstDay: formattedFirstDay, lastDay: formattedLastDay };
}

// Function to format date as 'dd-mm-yyyy'
function formatDate(date,mode='indo') {
  day = String(date.getDate()).padStart(2, '0');
  month = String(date.getMonth() + 1).padStart(2, '0');
  year = date.getFullYear();
  if(mode=='indo')
  {
	return day+'-'+month+'-'+year;
  }
  else
  {
	  return year+'-'+month+'-'+day;
  }
}

