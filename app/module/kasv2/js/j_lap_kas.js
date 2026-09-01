$(document).ready(function(){
	
	
	
	change_periode();
	/* return;
	reset_form();
	in_tanggal = $("#in_tanggal").flatpickr({
		dateFormat: "d-m-Y H:i",
		enableTime: true,
		time_24hr: true
	}); */
	
});

ctr_lap = 'kasv2/ex/lap_kas/';

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
	dt.id_jenjang=$('#id_jenjang').val();
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
	dt.id_jenjang=$('#id_jenjang').val();
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
	dt.id_jenjang=$('#id_jenjang').val();
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
	dt.id_jenjang=$('#id_jenjang').val();
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
	dt.id_jenjang=$('#id_jenjang').val();
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
	dt.id_jenjang=$('#id_jenjang').val();
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
	id_jenjang=$('#id_jenjang').val();
	window.open(ctr_lap+"excel_jurnal/?id_jenjang=" + id_jenjang+"&tgl_awal=" + tgl_awal+"&tgl_akhir="+tgl_akhir+"&periode="+periode);
}

function excel_jurnal_khusus(mode)
{
	periode=$("#periode_kas").val();
	id_jenjang=$('#id_jenjang').val();
	window.open(ctr_lap+"excel_jurnal_khusus/?mode="+mode+"&id_jenjang=" + id_jenjang+"&periode="+periode);
}

function cetak_lap_masuk()
{
	periode=$('#periode_kas').val();
	id_jenjang=$('#id_jenjang').val();
	var cetak = window.open(ctr_lap+"cetak_lap_masuk/?id_jenjang=" + id_jenjang+"&periode="+periode,'', "width=900,height=720,toolbar=no");
}

function cetak_lap_keluar()
{
	periode=$('#periode_kas').val();
	id_jenjang=$('#id_jenjang').val();
	var cetak = window.open(ctr_lap+"cetak_lap_keluar/?id_jenjang=" + id_jenjang+"&periode="+periode,'', "width=900,height=720,toolbar=no");
}

function cetak_neraca()
{
	periode=$('#periode_kas').val();
	id_jenjang=$('#id_jenjang').val();
	var cetak = window.open(ctr_lap+"cetak_neraca/?id_jenjang=" + id_jenjang+"&periode="+periode,'', "width=900,height=720,toolbar=no");
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


