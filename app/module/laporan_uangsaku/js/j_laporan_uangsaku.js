$(document).ready(function(){
    tampil_default_masuk();
    tampil_default_keluar();
    tgl_awal = $("#tgl_awal").flatpickr({
		dateFormat: "Y-m-d",
		static:true
	});
	tgl_akhir = $("#tgl_akhir").flatpickr({
		dateFormat: "Y-m-d",
		static:true
	});
    
    });


ctr_kfg_bayar = 'laporan_uangsaku/ex/laporan_uangsaku/';

function flag(){
    $("#tampil_default_masuk").hide();
    $("#tampil_default_keluar").hide();
    var tglawal = $('#tgl_awal').val();
    var tglakhir = $('#tgl_akhir').val();
    $.ajax({
    type:"GET",
    url:ctr_kfg_bayar+'laporan_masuk/?a='+tglawal+"&b="+tglakhir,
    success:function(){
    $("#tampil_masuk").load("/laporan_uangsaku/laporan_masuk/?a="+tglawal+"&b="+tglakhir);
    $("#tampil_keluar").load("/laporan_uangsaku/laporan_keluar/?a="+tglawal+"&b="+tglakhir);
    }
    })
    }

function flag_masuk(){
    var tglawal = $('#tgl_awal').val();
    var tglakhir = $('#tgl_akhir').val();
    window.open(ctr_kfg_bayar+'ex_laporan_masuk/?a='+tglawal+"&b="+tglakhir);
    
    }

    function flag_keluar(){
        var tglawal = $('#tgl_awal').val();
        var tglakhir = $('#tgl_akhir').val();
        window.open(ctr_kfg_bayar+'ex_laporan_keluar/?a='+tglawal+"&b="+tglakhir);
        
        }
    function tampil_default_masuk(){
        $("#tampil_default_masuk").load(ctr_kfg_bayar+'laporan_default_masuk');
    }
    function tampil_default_keluar(){
        $("#tampil_default_keluar").load(ctr_kfg_bayar+'laporan_default_keluar');
    }

