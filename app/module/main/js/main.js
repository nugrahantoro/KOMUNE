$(document).ready(function(){
	id_th = readCookie('id_th');
	if(id_th>0)
	{
		$('#sel_th').val(id_th);
	}
		console.log(document.cookie);
	init_beranda();
});

controller = 'main'

$('#sel_th, #sel_jenjang').on('change',function()
{
	init_beranda();
});

$('.select_ta').on('change',function()
{
	elem = $(this).attr('for');
	get_beranda(elem);
});

function modal_video(){
	$('#modal_video').modal('show');
}

function redirect_naikkan(){
	$('#modal_video').modal('hide');
	window.open('https://dev.sekolah-smartbilling.net/porting/faq/naikkan_kelas', '_blank');
}

function redirect_tagihan(){
	$('#modal_video').modal('hide');
	window.open('https://dev.sekolah-smartbilling.net/porting/faq/input_tagihan', '_blank');
}

function redirect_input(){
	$('#modal_video').modal('hide');
	window.open('https://dev.sekolah-smartbilling.net/porting/faq/input_siswa_baru', '_blank');
}

function init_beranda() 
{
	dt={};
	dt.id_th=$('#sel_th').val();
	dt.id_jenjang=$('#sel_jenjang').val();
	ajx=$.get(controller+'/get_select_ta',dt,function(raw){
		$('.select_ta').html(raw);
		get_beranda();
		//modal_video();
	});
	load_show(ajx);
	createCookie('id_th',dt.id_th);
}

function get_beranda(elem='')
{
	dt={};
	dt.elem=elem;
	dt.id_th=$('#sel_th').val();
	dt.id_jenjang=$('#sel_jenjang').val();
	dt.data_va_tgl=$('#data_va_tgl select').val();
	dt.data_saku_tgl=$('#data_saku_tgl select').val();
	dt.trn_bulan_tgl=$('#trn_bulan_tgl select').val();
	dt.pers_bulan_tgl=$('#pers_bulan_tgl select').val();
	dt.pers_bulan_tgl=$('#donut_bulan_tgl select').val();
	ajx=$.get(controller+'/get_beranda',dt,function(raw){
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('.elem_load').each(function()
			{
				elem=$(this).attr('id');
				if (typeof dt[elem] !== 'undefined')
				{
					$('#'+elem).html(dt[elem]);
				}
			});
		}
		else
		{
			alert(raw);
		}
	});
	if(elem!='')
	{
		par='#par_'+elem;
		load_ajx(ajx,par);
	}
	else
	{
		load_show(ajx);
	}
}

chart=[];
function chart_transaksi(elem_id,dt)
{
	elem=[];
	elem[1]='#div_pers_bulan';
	elem[2]='#div_pers_tahun';
	$(elem[elem_id]).html('');
	if(dt.nama=='')
	{
		//return;
	}
	if (typeof chart[elem_id] !== 'undefined')
	{
		chart[elem_id].destroy();
	}
	var options = {
          series: [{
			name: "Persentase",
          data: dt.nilai
        }],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            borderRadius: 4,
            horizontal: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: dt.nama,
		  max: 100,
		  labels: {
			formatter: function (value) {
			  return value+'%';
			}
		  }
        }
        };
		
        chart[elem_id] = new ApexCharts(document.querySelector(elem[elem_id]), options);
        chart[elem_id].render();
}

donut=[];
function donut_transaksi(elem_id,dt)
{
	elem=[];
	elem[1]='#div_donut_bulan';
	elem[2]='#div_donut_tahun';
	if (typeof donut[elem_id] !== 'undefined')
	{
		donut[elem_id].destroy();
	}
	$(elem[elem_id]).html('');
	var options = {
          series: dt.nilai,
          chart: {
          type: 'donut',
        },
		labels: dt.nama,
        responsive: [{
          breakpoint: 400,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };

        donut[elem_id] = new ApexCharts(document.querySelector(elem[elem_id]), options);
        donut[elem_id].render();
}