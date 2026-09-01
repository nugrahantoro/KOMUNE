$(document).ready(function(){
	
	
	list_kas();
	list_trans_transfer();
});

ctr_kas = 'kasv2/ex/data_kas/';



function list_trans_transfer()
{
	ajx=$.get(ctr_kas+'list_trans_transfer',function(raw)
	{
		$('#div_list_trans_transfer').html(raw);
	});
	load_show(ajx);
}

function list_kas()
{
	ajx=$.get(ctr_kas+'list_kas',function(raw)
	{
		$('#div_list_kas').html(raw);
	});
	load_show(ajx);
}

function load_select_jenis()
{
	ajx=$.get(ctr_kas+'load_select_jenis',function(raw)
	{
		$('#id_sumber').html(raw);
		$('#id_tujuan').html(raw);
	});
}

function modal_jns_kas(mode=1,id_jns_kas = 0)
{
	glob_mode=mode;
	glob_id_jns_kas=id_jns_kas;
	$('#modal_jns_kas').modal('show');
	mode_ket=['','Tambah','Edit'];
	$('#modal_jns_kas .modal_ket').html(mode_ket[mode]);
	
	$('#modal_jns_kas input').val('');
	if(mode==2)
	{
		dt={};
		dt.id_jns_kas=id_jns_kas;
		ajx=$.post(ctr_kas+'get_jns_kas',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt=JSON.parse(raw);
				$('#in_nama_jns_kas').val(dt.nama_kas);
				$('#in_saldo_awal').val(sep(dt.saldo_awal));
			}
		});
		load_show(ajx);
	}
}

function modal_transfer(mode=1,id_transfer = 0,id_sumber='')
{
	if(id_sumber!='')
	{
		$('#id_sumber').val(id_sumber);
	}
	
	glob_mode=mode;
	glob_id_transfer=id_transfer;
	$('#modal_transfer').modal('show');
	mode_ket=['','Tambah','Edit'];
	$('#modal_transfer .modal_ket').html(mode_ket[mode]);
	
	$('#modal_transfer input').val('');
	return;
	if(mode==2)
	{
		dt={};
		dt.id_jns_kas=id_jns_kas;
		ajx=$.post(ctr_kas+'get_jns_kas',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt=JSON.parse(raw);
				$('#in_nama_jns_kas').val(dt.nama_kas);
				$('#in_saldo_awal').val(sep(dt.saldo_awal));
			}
		});
		load_show(ajx);
	}
}


function save_jns_kas()
{
	dt={};
	dt.mode=glob_mode;
	dt.id_jns_kas=glob_id_jns_kas;
	dt.nama_jns_kas=$('#in_nama_jns_kas').val();
	//dt.saldo_awal=$('#in_saldo_awal').val();
	dt.saldo_awal='0';
	
	if(dt.nama_jns_kas=='' || dt.saldo_awal=='')
	{
		alertify.error("Data Belum Lengkap");
		return;
	}
	
	
	dt.saldo_awal=rmSep($('#in_saldo_awal').val());
	mode_txt=['','Simpan','Edit'];
	alertify.confirm('Konfirmasi', mode_txt[glob_mode]+' Jenis Kas?', function(){
		
		ajx=$.post(ctr_kas+'save_jns_kas', dt, function(raw) 
			{
				if(raw.trim()=='ok')
				{
					alertify.success("Disimpan");
					$('#modal_jns_kas').modal('hide');
					list_kas();
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


///UNUSED



function change_periode()
{
	init_tgl();
	 load_tab_lap_masuk();
	 load_tab_lap_keluar();
	 load_tab_neraca();
	 load_tab_jurnal_all();
	 load_tab_jurnal_pemasukan();
	 load_tab_jurnal_pengeluaran();
}

function init_tgl()
{
	sel_date = $('#periode_kas').val()+'-01';
	day = getFirstAndLastDateOfCurrentMonth(sel_date);
	firstDay=day.firstDay;
	lastDay=day.lastDay;
	
	$('#jrn_tgl_awal').val(firstDay);
	$('#jrn_tgl_akhir').val(lastDay);
	
	jrn_tgl_awal = $("#jrn_tgl_awal").flatpickr({
		dateFormat: "d-m-Y",
		maxDate: lastDay,
		minDate: firstDay
	});
	jrn_tgl_akhir = $("#jrn_tgl_akhir").flatpickr({
		dateFormat: "d-m-Y",
		maxDate: lastDay,
		minDate: firstDay
	});
}

function load_tab_lap_masuk()
{
	dt={};
	dt.periode=$('#periode_kas').val();
	ajx=$.get(ctr_lap+'load_lap_masuk',dt,function(raw)
	{
		$('#div_lap_masuk').html(raw);
	});
	load_show(ajx);
}

function load_tab_lap_keluar()
{
	dt={};
	dt.periode=$('#periode_kas').val();
	ajx=$.get(ctr_lap+'load_lap_keluar',dt,function(raw)
	{
		$('#div_lap_keluar').html(raw);
	});
	load_show(ajx);
}

function load_tab_neraca()
{
	dt={};
	dt.periode=$('#periode_kas').val();
	ajx=$.get(ctr_lap+'load_neraca',dt,function(raw)
	{
		$('#div_neraca').html(raw);
	});
	load_show(ajx);
}

function load_tab_jurnal_pemasukan()
{
	dt={};
	dt.periode=$('#periode_kas').val();
	dt.mode='masuk';
	ajx=$.get(ctr_lap+'load_jurnal_khusus',dt,function(raw)
	{
		$('#div_jurnal_pemasukan').html(raw);
	});
	load_show(ajx);
}

function load_tab_jurnal_pengeluaran()
{
	dt={};
	dt.periode=$('#periode_kas').val();
	dt.mode='keluar';
	ajx=$.get(ctr_lap+'load_jurnal_khusus',dt,function(raw)
	{
		$('#div_jurnal_pengeluaran').html(raw);
	});
	load_show(ajx);
}

function load_tab_jurnal_all()
{
	dt={};
	dt.periode=$('#periode_kas').val();
	dt.tgl_awal=$('#jrn_tgl_awal').val();
	dt.tgl_akhir=$('#jrn_tgl_akhir').val();
	ajx=$.get(ctr_lap+'load_jurnal_all',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#div_jurnal').html(dt.table_jurnal);
			$('#div_jurnal_summary').html(dt.table_summary);
			$('#jml_jrn_trans').html(dt.jml_trans);
			$('#cari_jurnal').val('');
			cari_jurnal();
		}
		else
		{
			//alert(raw);
		}
	});
	load_show(ajx);
}

function cari_jurnal()
{
	txt = $('#cari_jurnal').val();
	cari=cariable('#div_jurnal table',txt);
	$('#jml_cari_jurnal').html(cari.result);
	if(txt.trim()!='')
	{
		$('#spn_cari_jurnal').show();
	}
	else
	{
		$('#spn_cari_jurnal').hide();
	}
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('#div_jurnal .no_result').show(); 
	}
	else
	{
		$('#div_jurnal .no_result').hide();
	}
}

function excel_jurnal()
{
	//tgl_awal=$("#jrn_tgl_awal").val();
	//tgl_akhir=$("#jrn_tgl_akhir").val();
	tgl_awal=firstDay;
	tgl_akhir=lastDay;
	periode=$("#periode_kas").val();
	window.open(ctr_lap+"excel_jurnal/?tgl_awal=" + tgl_awal+"&tgl_akhir="+tgl_akhir+"&periode="+periode);
}

function excel_jurnal_khusus(mode)
{
	periode=$("#periode_kas").val();
	window.open(ctr_lap+"excel_jurnal_khusus/?mode="+mode+"&periode="+periode);
}

function cetak_lap_masuk()
{
	periode=$('#periode_kas').val();
	var cetak = window.open(ctr_lap+"cetak_lap_masuk/?periode="+periode,'', "width=900,height=720,toolbar=no");
}

function cetak_lap_keluar()
{
	periode=$('#periode_kas').val();
	var cetak = window.open(ctr_lap+"cetak_lap_keluar/?periode="+periode,'', "width=900,height=720,toolbar=no");
}

function cetak_neraca()
{
	periode=$('#periode_kas').val();
	var cetak = window.open(ctr_lap+"cetak_neraca/?periode="+periode,'', "width=900,height=720,toolbar=no");
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


