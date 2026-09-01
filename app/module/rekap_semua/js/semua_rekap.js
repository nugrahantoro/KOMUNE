
controller = 'rekap_semua/ex/rekap/';
var modale = document.getElementById("myModale");

$(document).ready(function(){
	tampil_data(1); 
    e = $("#e").flatpickr({
		dateFormat: "Y-m-d",
		static:true
	});
	ee = $("#ee").flatpickr({
		dateFormat: "Y-m-d",
		static:true
	});
    
    });

	ctr_kfg_bayar = 'rekap_semua/ex/rekap_semua/';
function flag() {
    var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;
    $("#tampil_data").load(ctr_kfg_bayar+'laporan_absensi_semua/?a=' + a + "&b=" + b);
}

function tampil_data(){
	var a = document.querySelector('[id="e"]').value;
    var b = document.querySelector('[id="ee"]').value;	
	$("#tampil_data").load(ctr_kfg_bayar+'rekap_semua_default/?a=' + a + "&b=" + b);
}