// $(document).ready(function () {
//     tampil_tahun();
//     jenjang_null();
//     tampil_kelas_null();
//     tampil_user();
//     tampil_trans_null();
//     tampil_metode();
//     tampil_default();

//     var today = new Date();
//     var dd = String(today.getDate()).padStart(2, '0');
//     var mm = String(today.getMonth() + 1).padStart(2, '0');
//     var yyyy = today.getFullYear();

//     today = yyyy + '-' + mm + '-' + dd;
//     $('#e').val(today);
//     $('#ee').val(today);

//     e = $("#e").flatpickr({
//         dateFormat: "Y-m-d",
//         static: true,
//     });
//     ee = $("#ee").flatpickr({
//         dateFormat: "Y-m-d",
//         static: true,
//     });
// });
$(document).ready(function(){ 
    //tampil_th();
    load_list_siswa_cari();
    $("#paging_button option[value=1]").attr('selected','selected');
    $("#1").css({'background-color' : ''}); //agar paging button bagian pertama berubah warna, karena page 1 yang pertama di jalankan
    $('#i').val(null);
    $('#a').val(null);
    $('#b').val(null);
    $('#s').val(null);
    });

controller ='transaksi_tabungan/';
function change_ta_jenjang(){
	ajx=load_list_siswa_cari();
	load_show(ajx);
	glob_id_siswa=0;
	$('#tabel_tagihan').hide();
	$('#tabel_tagihan_empty').show();
	$('#total_tagihan').val('');
	$('#card_siswa input').val('');
	$('#blink_tagihan').html('');
	uang_saku_reset();
    }

 
    function tampil_th(){
        $("#tam_null").load("../controller/tabungan.php/send_th");
        }
     
    function pilih_siswa(id_siswa)
    {
        dt={};
        dt.id_siswa = id_siswa;
        hide_list_siswa_cari();
        ajx=$.post(controller+'tampil_siswa',dt,function(raw)
        {

            $('#tabel_data').html(raw);
            $('#i').val(id_siswa);
            ajx=$.post(controller+'detail_siswa',dt,function(nama) {
                $('#b').val(nama);
            });
        });
    }
        
        function cet_trans(){
        var table = document.getElementById('tabsskls'),
            cells = table.getElementsByTagName('tr');
        
        for (var i=0,len=cells.length; i<len; i++){
            cells[i].onclick = function(){
                var id = this.cells[6].innerText;
                //alert(id);
                $.ajax({
                type:"GET",
                url: controller+'cetak_ra/?id=' + id,
               
                success: function(){
                //  window.open('transaksi_tabungan/cetak_ra/?id='+id);
                 window.open(controller+'cetak_ra/?id='+id);
                //  window.open('../controller/transaksi_tabungan/cetak_ra/?id='+id);
        
                }
                });
                //$("#keranjang").load("../controller/tabungan.php/history_tab/?id="+id);
        
            }
            
        }
        }
        
        function load_list_siswa_cari()
        {
            dt={};
            dt.id_jenjang=$('#id_jenjang').val();
            dt.id_ta=$('#id_ta').val();
            ajx=$.post(controller+'load_list_siswa_cari',dt,function(raw)
            {
                $('#list_siswa_cari').html(raw);
            });
            return ajx;
        }
        
        function dle(){
        var nis = $('#i').val();
        var table = document.getElementById('tabsskls'),
            cells = table.getElementsByTagName('tr');
        
        for (var i=0,len=cells.length; i<len; i++){
                cells[i].onclick = function(){
                    var id = this.cells[6].innerText;
                        $.ajax({
                    type:"GET",
                    
                    url: controller+'dle_tabu/?id=' + id,
                    success: function(){
                        alertify.success("Tabungan Dihapus");
				        pilih_siswa(nis);

            //             url: controller+'cetak_ra/?id=' + nis,
            // load(controller/'tampil_siswa/?id=' +nis);
            // load(controller/'cekt/?id=' +id);
                    }
                    });	
            }
        }
        }
        
        $("#popcr").click(function(){
        $('#modal-kotak , #bg').fadeIn("slow");
        //var th = document.querySelector('[id="myselects_th"]').value;
        //$(".ui-dialog-titlebar-close,ui").show();
        $('#cr').val(null);
        $('#idtrsk').val(null);
        $('#i').val(null);
        $('#a').val(null);
        $('#b').val(null);
        $('#s').val(null);
        $("#tam").load("../controller/transaksi_tabungan/cari_tab");
        $("#pop ").load("../controller/transaksi_tabungan/dalamcr/");
        
        });

    function show_list_siswa_cari()
    {
        $('#div_cari_siswa').slideDown('fast');
        $('#txt_cari_siswa').focus();
    }

    function cari_siswa(){
	txt = $('#txt_cari_siswa').val();
	par="#list_siswa_cari";
	cari=cariable(par,txt);
	show_list_siswa_cari();
	if(cari.stat=='no_result' || cari.count==0)
	{
		$(par+' .no_result').show(); 
	}
	else
	{
		$(par+' .no_result').hide();
	}
    }
    
        
        
        function cet(){
        var i = $('#i').val();
                $.ajax({
                type:"GET",
                url:"../controller/tabungan.php/cetak/?nis="+i,
                success: function(){
                 window.open('../controller/tabungan.php/cetak/?nis='+i);
        $('#modal-kotaka , #bga').fadeIn("slow");
                }
                });
        }
        
        function perb_cet(){
        var i = $('#i').val();
                $.ajax({
                type:"GET",
                url:"../controller/tabungan.php/perbaikan_cetak/?nis="+i,
                success: function(){
                 window.open('../controller/tabungan.php/perbaikan_cetak/?nis='+i);
        $('#modal-kotaka , #bga').fadeIn("slow");
                }
                });
        }
        
        function cetaks(){
        
        document.getElementById('clo').style.visibility='visible';
        document.getElementById('cla').style.visibility='hidden';
        var id = $('#idtrsk').val();
        var i = $('#i').val();
                $.ajax({
                type:"GET",
                url:"../controller/tabungan.php/cetak/?nis="+i+"&id="+id,
                success: function(){
                 window.open('../controller/tabungan.php/cetak/?nis='+i+"&id="+id);
        
                }
                });
                
        }
        
        function cetakul(){
        
        var i = $('#i').val();
        
        if ($("#i").val()==""){
        alert('anda belum melakukan transaksi');
        }else if($("#i").val()!==""){
                $.ajax({
                type:"GET",
                url:"../controller/tabungan.php/cetakul/?nis="+i,
                success: function(){
                 window.open('../controller/tabungan.php/cetakul/?nis='+i);
        
                }
                });
        }
        }

        function hide_list_siswa_cari()
        {
            $('#div_cari_siswa').slideUp('fast');
        }
        
        function cetakbuk_tab(){
            //alert('');
            //var th = document.querySelector('[id="myselects_th"]').value;
        var i = $('#i').val();
        
        if ($("#i").val()==""){
        alert('anda belum melakukan search siswa');
        }else if($("#i").val()!==""){
                $.ajax({
                type:"GET",
                url:controller+'cetakbuk_tab/?nis='+i,
                success: function(){
                 window.open(controller+'cetakbuk_tab/?nis='+i);
        
                }
                });
        }
        }

        function cet(){
            var i = $('#i').val();
                    $.ajax({
                    type:"GET",
                    url:controller+'cetak/?nis='+i,
                    success: function(){
                     window.open(controller+'cetak/?nis='+i);
            $('#modal-kotaka , #bga').fadeIn("slow");
                    }
                    });
            }
        function cetakbuk_tab_lm(){
            //var th = document.querySelector('[id="myselects_th"]').value;
        var i = $('#i').val();
        
        if ($("#i").val()==""){
        alert('anda belum melakukan search siswa');
        }else if($("#i").val()!==""){
                $.ajax({
                type:"GET",
                url:"../controller/tabungan.php/cetakbuk_tab_lm/?nis="+i,
                success: function(){
                 window.open('../controller/tabungan.php/cetakbuk_tab_lm/?nis='+i);
        
                }
                });
        }
        }
        
        function cover(){
                $.ajax({
                type:"GET",
                url:"../controller/tabungan.php/cover",
                success: function(){
                window.open('../controller/tabungan.php/cover');
                }
                });	
        }
        
        function cetaktrans(){
        
        var id = $('#idtrsk').val();
        if ($("#idtrsk").val()==""){
            alert('anda belum melakukan transaksi');
        }else if($("#idtrsk").val()!==""){
                $.ajax({
                type:"GET",
                url:"../controller/tabungan.php/cetaktrans/?idtrs="+id,
                success: function(){
                 window.open('../controller/tabungan.php/cetaktrans/?idtrs='+id);
        
                }
                });
        }
        }
        
        function noact(){
        alert('NO ACTION');
        }
        
        
        function theares(){
        
        var table = document.getElementById('tabs'),
            cells = table.getElementsByTagName('tr');
        
        for (var i=0,len=cells.length; i<len; i++){
            cells[i].onclick = function(){
                //document.getElementById('a').value=this.cells[10].innerText;
                document.getElementById('b').value=this.cells[2].innerText;
                document.getElementById('i').value=this.cells[8].innerText;
                //document.getElementById('bkel').value=this.cells[8].innerText;
                var id = this.cells[8].innerText;
                $('#modal-kotak , #bg').fadeOut("slow");
                $("#keranjang").load("../controller/tabungan.php/history_tab/?id="+id);
                $('#myselects_ths').val(0);
                $("#tam_tabu").load("../controller/tabungan.php/pildul");
                $("#cek").load("../controller/tabungan.php/cekt/?id="+id);
        
            }
            
        }
        }
        
        
        function proses_bung(){
        //var th = document.querySelector('[id="myselects_th"]').value;
     
        var id = $('#i').val();
        var nominal_tab = $('#tb').val();
        var nominal_amb = $('#pn').val();
        var keterangan_tab = $('#keterangan_tab').val();
        var tot_tb=document.getElementById('tot_tb').value;
        var tot_pn=document.getElementById('tot_pn').value;
        var nominal_tab_dt=parseInt(nominal_tab);
        
        var tot_tbint=parseInt(tot_tb);
        var tot_pnint=parseInt(tot_pn);
        var hasil=nominal_tab_dt+tot_tbint-tot_pnint;
        //alert(tot_tbint);
        // alert (id + " " + nominal_amb);
        
        //var hasil = (parseInt(nominal_tab)-parseInt(nominal_tab));
        if ($("#i").val()==""){
            alert('anda belum melakukan searc siswa');
        }else if($("#i").val()!==""){
            $.ajax({
            type:"GET",
            // url: controller+'insert_tab/?id="+id+"&nominal_tab_dt="+nominal_tab_dt+"&nominal_amb=0"+"&hasil="+hasil+"&keterangan_tab="+keterangan_tab',
            url: controller+'insert_tab/?id='+id+'&nominal_tab_dt='+nominal_tab_dt+'&nominal_amb ='+0+'&hasil='+hasil+'&keterangan_tab='+keterangan_tab,
            // url:"../controller/tabungan.php/insert_tab/?id="+id+"&nominal_tab_dt="+nominal_tab_dt+"&nominal_amb=0"+"&hasil="+hasil+"&keterangan_tab="+keterangan_tab,
            success:function(){
            alert("Berhasil");
            pilih_siswa(id);
            // $("#tabel_tagihan").load(controller/'tampil_siswa/?id='+id);
            $("#tot_sal").load(controller/'tot_sal/?id='+id);
            $('#myselects_ths').val(0);
            $("#tam_tabu").load(controller/'pildul');
            $("#cek").load(controller/'cekt/?id='+id);
            }
            });
        }
        
        
        }
        
        function proses_pena(){
        //var th = document.querySelector('[id="myselects_th"]').value;
        var id = $('#i').val();
        var nominal_tab = $('#tb').val();
        var nominal_amb = $('#pn').val();
        var keterangan = $('#keterangan').val();
        var tot_tb=document.getElementById('tot_tb').value;
        var tot_pn=document.getElementById('tot_pn').value;
        var tot_do=document.getElementById('tot_do').value;
        var nominal_tab_dt=parseInt(nominal_tab);
        var nominal_amb_dt=parseInt(nominal_amb);
        var tot_do_dt=parseInt(tot_do);
        var tot_tbint=parseInt(tot_tb);
        var tot_pnint=parseInt(tot_pn);
        //alert(tot_do_dt-nominal_amb_dt);
        var hasil=(tot_do_dt-nominal_amb_dt);
        //alert(hasil);
        //var hasil = (parseInt(nominal_tab)-parseInt(nominal_tab));
        if ($("#i").val()==""){
        alert('anda belum melakukan searc siswa');
        }else if($("#i").val()!==""){
        $.ajax({
        type:"GET",
        // url:"../controller/transaksi_tabungan.php/insert_tarik/?id="+id+"&nominal_tab_dt=0"+"&nominal_amb_dt="+nominal_amb_dt+"&hasil="+hasil+"&keterangan="+keterangan,
        url: controller+'insert_tarik/?id='+id+'&nominal_tab_dt='+0+'&nominal_amb='+nominal_amb_dt+'&hasil='+hasil+'&keterangan='+keterangan+'&keterangan='+keterangan,
        // url: controller+'insert_tab/?id='+id+'&nominal_tab_dt='+nominal_tab_dt+'&nominal_amb ='+0+'&hasil='+hasil+'&keterangan_tab='+keterangan_tab,
        
        success:function(){
        alert("Berhasil");
        pilih_siswa(id);
        $("#keranjang").load(controller+'history_tab/?id='+id);
        $("#tot_sal").load(controller+'tot_sal/?id='+id);
        $('#myselects_ths').val(0);
        $("#tam_tabu").load(controller+'pildul');
        $("#cek").load(controller+'cekt/?id='+id);
        }
        });
        }
        
        
        }
        
        function cet_ilang(){
        var i = $('#i').val();
                $.ajax({
                type:"GET",
                url:controller+'update_cetak_ilang/?nis='+i,
                success: function(){
                 window.open(controller+'cetak_ilang/?nis='+i);
        $("#dialog_cetak").dialog("open");
                }
                });
        }
        
        function blo(){
            var i = $('#i').val();
                $.ajax({
                type:"GET",
                url:"../controller/tabungan.php/upd_cet/?i="+i
                });
                $("#dialog_cetak").dialog("close");
        }
        
        function tabu(){
            var id = $('#i').val();
        // $("#tam_tabu").load("../controller/tabungan.php/texttab");
        $("#tam_tabu").load(controller+'texttab');
        // $("#tot_sal").load("../controller/tabungan.php/tot_sal/?id="+id);
        $("#tot_sal").load(controller+'tot_sal/?id='+id);
        }
        function penar(){
        var id = $('#i').val();
                
        // $("#tam_tabu").load("../controller/tabungan.php/textpen");
        // $("#tot_sal").load("../controller/tabungan.php/tot_sal/?id="+id);

        $("#tam_tabu").load(controller+'textpen');
        $("#tot_sal").load(controller+'tot_sal/?id='+id);
        }
        function nole(){
        $("#tam_tabu").load("../controller/tabungan.php/pildul");
        }
        
        function caritext(){
        var nama_siswa=encodeURI(document.querySelector('[id="cr"]').value);
        //var crc=document.querySelector('[id="crc"]').value;
            $.ajax({
            type:"GET",
            url:"../controller/tabungan.php/cari_tab/?id="+nama_siswa,
            success:function(){
                /*$(function(){
                    $("body").mLoading();
                    setTimeout(function(){
                    $("body").mLoading('hide');
                    }, 500);
                    });*/
                $("#tam").load("../controller/tabungan.php/cari_tab/?id="+nama_siswa);
                //$("#history").load("../controller/pasca.php/history");
                }
            })
        }
        
        function caritext_nis(){
        var nis = document.querySelector('[id="cr_nis"]').value;
        var crc = document.querySelector('[id="crc_nis').value;
        $.ajax({
        type:"GET",
        url:"../controller/tabungan.php/cari_tab_nis/?id="+nis+"&crc="+crc,
        success:function(){
        $(function(){
        $("body").mLoading();
        setTimeout(function(){
        $("body").mLoading('hide');
        }, 500);
        });
        $("#tam").load("../controller/tabungan.php/cari_tab_nis/?id="+nis+"&crc="+crc);
        //$("#history").load("../controller/pasca.php/history");
        }
        })
        }    



ctr_kfg_bayar = 'laporan/ex/laporan/';

var modalimport = document.getElementById("myModaldata");
var modalinputtrans = document.getElementById("myModalinputtrans");
var modalkelompok = document.getElementById("myModalkelompok");


