$(document).ready(function(){
	
	reset_form();
	in_tanggal = $("#in_tanggal").flatpickr({
		dateFormat: "d-m-Y H:i",
		enableTime: true,
		time_24hr: true
	});
	
	change_periode();
	load_jenis_kat();
	
});

ctr_inp_kas = 'konfig_tu/ex/inp_kas/';

function change_periode()
{
	init_tgl();
	load_list_trans();
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
	$.post(ctr_inp_kas+'load_jenis_kat',function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#in_jenis').html(dt.in_jenis);
			dt_kategori=dt.kategori;
		}
		else
		{
			alertify.error("Error");
		}
	});
}

function list_kategori()
{
	id_jns = $('#in_jenis').val();
	opt="<option value='0'>-pilih-</option>";
	if(id_jns!=0)
	{
		kat = dt_kategori[id_jns];
		if(kat.length==1)
		{
			opt ='';
		}
		
		for(i=0;i<kat.length;i++)
		{
			opt+="<option value='"+kat[i]['id_kat_pemasukan']+"'>"+kat[i]['ref']+" "+kat[i]['nama_kat_pemasukan']+"</option>";
		}
		
	}
	$('#in_kategori').html(opt);
}

function reset_form()
{
	$('#frm_pemasukan input').val('');
	$('#frm_pemasukan select').val('0');
	$('#in_tanggal').val(getCurrentDateTime());
}

function save_pemasukan()
{
	dt={};
	dt.jenis=$('#in_jenis').val();
	dt.kategori=$('#in_kategori').val();
	dt.tanggal=$('#in_tanggal').val();
	dt.pemasukan=$('#in_pemasukan').val();
	dt.nominal=$('#in_nominal').val();
	
	if(dt.jenis==0 || dt.kategori==0 || dt.tanggal=='' || dt.pemasukan=='' || dt.nominal=='')
	{
		alertify.error("Data Belum Lengkap");
		return;
	}
	
	alertify.confirm('Konfirmasi', 'Simpan Data Pemasukan?', function(){
		
		ajx=$.post(ctr_inp_kas+'save_pemasukan', dt, function(raw) 
			{
				if(raw.trim()=='ok')
				{
					alertify.success("Disimpan");
					reset_form();
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

function del_transaksi(id_trans)
{
		dt={};
		dt.id_trans=id_trans;
		alertify.confirm('Konfirmasi','Hapus Transaksi Pemasukan?', function(){
		$.post(ctr_inp_kas+'del_transaksi',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success('Data Berhasil Dihapus');
					load_list_trans();
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
}

function load_list_jenis()
{
	dt={};
	ajx = $.post(ctr_inp_kas+'load_list_jenis',dt,function(raw)
	{
		$('#div_list_jenis').html(raw);
		$("#div_list_jenis tr[id_jenis='"+glob_id_jenis+"'").addClass('active');
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
				$('#jns_nama').val(jn.nama_jns_pemasukan);
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
	par=$(obj).parent('tbody');
	par.find('tr').removeClass('active');
	$(obj).addClass('active');
	load_list_kat();
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
	$('#kat_jenis').val(nm_jenis);
	if(mode==1)
	{
		$('#modal_kat .btn_hapus').hide();
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
				$('#kat_nama').val(jn.nama_kat_pemasukan);
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
					load_jenis_kat();
					$('#modal_kat').modal('hide');
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

