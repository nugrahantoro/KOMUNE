$(document).ready(function(){
    /*sw_tgl_awal = $("#tgl_awal").flatpickr({
		dateFormat: "d-m-Y",
		static:true
	});
	sw_tgl_akhir = $("#tgl_akhir").flatpickr({
		dateFormat: "d-m-Y",
		static:true
	});*/
	proses_task();
});

controller = 'setelmen/ex/data_setelmen/';

function proses_task()
{
	dt={};
	dt.tgl_awal=$('#tgl_awal').val();
	dt.tgl_akhir=$('#tgl_akhir').val();
	if(dt.tgl_awal=='' || dt.tgl_akhir=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	ajx=$.post(controller+'tampil_data_pembayaran',dt,function(raw)
	{
		$('#div_data_setelmen').html(raw);
	});
	load_ajx(ajx,'#col-userkeu');

	ajx=$.post(controller+'tampil_data_saku',dt,function(raw)
	{
		$('#data_setelmen_saku').html(raw);
	});
	load_ajx(ajx,'#col-userkeu');
}

function cetak_excel_saja()
{
	tgl_awal=$('#tgl_awal').val();
	tgl_akhir=$('#tgl_akhir').val();
	window.open(controller+"/cetak_excel_saja/?tgl_akhir=" + tgl_akhir + "&tgl_awal=" + tgl_awal);
}

function cetak_excel_saja_saku()
{
	tgl_awal=$('#tgl_awal').val();
	tgl_akhir=$('#tgl_akhir').val();
	window.open(controller+"/cetak_excel_saja_saku/?tgl_akhir=" + tgl_akhir + "&tgl_awal=" + tgl_awal);
}

