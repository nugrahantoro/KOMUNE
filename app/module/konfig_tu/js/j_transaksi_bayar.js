$(document).ready(function(){
	$('#toggle').hide();
	change_ta_jenjang();
	$(document).click(function(e) {
        var container = $("#div_cari_siswa, #tabel_tagihan_empty, #txt_cari_siswa");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
          hide_list_siswa_cari();
        }
	});
	
	$('#modal_add_trans').on('click','#kb_add_siswa_list td, #kb_add_trans_list .chk_trigger',function(event) {
		if (!$(event.target).is('input[type="checkbox"]')) {
		  var checkbox = $(this).closest('tr').find('input[type="checkbox"]');
		  //checkbox.prop('checked', !checkbox.prop('checked'));
		  checkbox.click();
		}
	});
	
	del_trans_sementara();
});

controller = 'konfig_tu/ex/transaksi_bayar/';
glob_id_siswa=0;
glob_id_order=0;

function change_ta_jenjang()
{
	ajx=load_list_siswa_cari();
	load_show(ajx);
	glob_id_siswa=0;
	$('#tabel_tagihan').hide();
	$('#tabel_tagihan_empty').show();
	$('#total_tagihan').val('');
	$('#card_siswa input').val('');
	$('#blink_tagihan').html('');
	uang_saku_reset();
	//show_toggle();
}

function edit_tanggal(id_transaksi){
	dt={};
	dt.kb_edit_tgl_jth = $('#kb_edit_tgl_jth').val();
	dt.id_transaksi=id_transaksi;
	ajx=$.post(controller+'update_tanggal',dt,function(raw)
	{
		modal_verifikasi();
		alertify.success('Berhasil Dirubah');
	});
	load_show(ajx);
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

function hide_list_siswa_cari()
{
	$('#div_cari_siswa').slideUp('fast');
}

function show_list_siswa_cari()
{
	$('#div_cari_siswa').slideDown('fast');
	$('#txt_cari_siswa').focus();
}

function cari_siswa()
{
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

function pilih_siswa(id_siswa=null)
{
	if(id_siswa==null)
	{
		id_siswa=glob_id_siswa;
	}
	dt={};
	dt.id_ta = $('#id_ta').val();
	glob_id_siswa = id_siswa;
	glob_id_order=0;
	dt.id_siswa=id_siswa;
	ajx=$.post(controller+"load_data_siswa",dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#dt_nis_id').val(dt.nis_siswa+' / '+dt.id_siswa);
			$('#dt_nama_siswa').val(dt.nama_siswa+' ('+dt.gender+')');
			$('.nama_siswa').html(dt.nama_siswa+' ('+dt.gender+')');
			$('#dt_tgl_lahir').val(dt.tanggal_lahir);
			$('#dt_kelas').val(dt.nama_kelas);
			$('#tabel_tagihan').show();
			$('#tabel_tagihan_empty').hide();
			glob_id_kelas=dt.id_kelas;
			glob_id_user=dt.id_user;
			saldo_uang_saku();
			saldo_tabungan();
		}
	});
	load_show(ajx);
	hide_list_siswa_cari();
	
	ajx = load_data_tagihan();
	load_show(ajx);
	
	del_trans_sementara();
}

function load_data_tagihan()
{
	dt={};
	dt.id_siswa=glob_id_siswa;
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	ajx=$.post(controller+"load_data_tagihan",dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#tabel_tagihan').html(dt.table);
			$('#blink_tagihan').html(dt.blink);
			$('#total_tagihan').val(dt.total_tagihan);
		}
	});
	return ajx;
}

function insert_trans_lunas(id_mst_trans)
{
	dt={};
	dt.id_mst_trans=id_mst_trans;
	ajx=$.post(controller+"insert_trans_lunas",dt,function(raw)
	{
		load_trans_sementara();
		return;
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#tabel_tagihan').html(dt.table);
			$('#blink_tagihan').html(dt.blink);
			$('#total_tagihan').val(dt.total_tagihan);
		}
	});
	return ajx;
}

function valid_nominal_bayar(rest=0) {
  var jns = $('#byr_metode').val();
  var kekurangan = parseInt($('#byr_kekurangan').val(), 10);
  if(rest==1)
  {
	  $('#byr_nominal_bayar').val(kekurangan);
  }
  if($('#byr_nominal_bayar').val()=='')
  {
	  return;
  }
  nominal = parseInt($('#byr_nominal_bayar').val(), 10);
  saldo_saku=glob_saku_saldo;
  if(jns == "Uang Saku" || jns == "Ikhlas Saku" || jns == "kartu")
  {
	  
	  if (nominal > saldo_saku)
	  {
		  alertify.error("Saldo Tidak Cukup");
		  nominal = saldo_saku;
	  }
	  if(nominal>kekurangan)
	  {
		  alertify.error("Melebihi Ketetapan");
		  nominal = kekurangan;
	  }
  }
  else if (jns != "Ikhlas")
  {
	  if(nominal>kekurangan)
	  {
		  alertify.error("Melebihi Ketetapan");
		  nominal = kekurangan;
	  }
  }
	$('#byr_nominal_bayar').val(nominal);
}


function modal_bayar_trans(id_trans)
{
	$('#modal_bayar_trans').modal('show');
	dt={};
	dt.id_trans=id_trans;
	glob_id_trans = id_trans;
	$('#modal_bayar_trans input').val('');
	$('#byr_metode').val('teller');
	ajx=$.post(controller+'load_data_trans',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#byr_nama_trans').val(dt.nama_transaksi);
			$('#byr_nominal').val(dt.nominal);
			$('#byr_terbayarkan').val(dt.nominal_dibayarkan);
			$('#byr_kekurangan').val(dt.kekurangan);
			$('#byr_nominal_bayar').val(dt.kekurangan);
			glob_id_jenis_transaksi = dt.id_jenis_transaksi;
			glob_nominal_ketetapan = dt.nominal;
			glob_nominal_kekurangan = dt.kekurangan;
			glob_id_kat = dt.id_kat;
		}
	});
	setTimeout(function(){load_ajx(ajx,'#modal_bayar_trans .modal-body')},225);
}

function edit_trans(id_trans)
{
	$('#modal_edit_trans').modal('show');
	dt={};
	dt.id_trans=id_trans;
	glob_id_trans = id_trans;
	$('#modal_edit_trans input').val('');
	ajx=$.post(controller+'load_data_trans',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#edt_nama_trans').val(dt.nama_transaksi);
			$('#edt_nominal').val(dt.nominal);
			$('#edt_terbayarkan').val(dt.nominal_dibayarkan);
			$('#edt_kekurangan').val(dt.kekurangan);
		}
	});
	setTimeout(function(){load_ajx(ajx,'#modal_edit_trans .modal-body')},225);
}

function edit_trans_save()
{
	dt={};
	dt.id_trans=glob_id_trans;
	dt.nominal_baru=$('#edt_nominal_baru').val();
	dt.terbayarkan=$('#edt_terbayarkan').val();
	if((parseInt(dt.nominal_baru)<parseInt(dt.terbayarkan) )|| dt.nominal_baru=='')
	{
		alertify.error("Nominal Baru Tidak Valid");
		return;
	}
	alertify.confirm('Konfirmasi','Edit Nominal Ketetapan?', function(){
		ajx=$.post(controller+"edit_trans_save", dt, function(raw)
		{
			if(isJson(raw))
				{
					rs=JSON.parse(raw);
					if(rs.stat == 1)
					{
						alertify.success('Berhasil Dirubah');
						load_data_tagihan();
						$('#modal_edit_trans').modal('hide');
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
		load_ajx(ajx,'#modal_edit_trans .modal-content');
	},
	function(){});
}

function del_trans_sementara(id_transaksi='')
{
	dt={};
	dt.id_transaksi=id_transaksi;
	if(id_transaksi!='') 
	{
		alertify.confirm('Konfirmasi','Hapus dari daftar dibayar?', function(){
			ajx=$.post(controller+"del_trans_sementara", dt, function(raw)
			{
				load_trans_sementara();
			});
			load_ajx(ajx,'#transaksi_sementara');
		},
		function(){});
	}
	else
	{
		ajx=$.post(controller+"del_trans_sementara",function(raw)
		{
			load_trans_sementara();
		});
		load_ajx(ajx,'#transaksi_sementara');
	}
}

function load_trans_sementara()
{
	ajx=$.post(controller+'load_trans_sementara',function(raw)
	{
		$('#transaksi_sementara').html(raw);
	});
	load_ajx(ajx,'#transaksi_sementara');
}

function simpan_transaksi()
{
	dt={};
	dt.id_siswa=glob_id_siswa;
	dt.id_jenjang=$('#id_jenjang').val();
	if(glob_id_siswa==0)
	{
		alertify.error("Pilih Warga Dahulu");
		return;
	}
	chk = $('#transaksi_sementara tbody td').length;
	if(chk==1)
	{
		alertify.error("Pilih Transaksi yg dibayar");
		return;
	}
	ajx=$.post(controller+'simpan_transaksi',dt,function(raw)
	{
		pilih_siswa(glob_id_siswa);
		if(isJson(raw))
		{
			js=JSON.parse(raw);
			glob_id_order = js.id_order;
			alertify.success("Transaksi Disimpan");
			if (js.id_order != "") {
				wa_web(dt.id_siswa,js.id_order);
			}
		}
		else
		{
			alertify.error(raw);
		}
	});
	load_show(ajx);
}

function wa_web(id_siswa,id_order)
{
	$('#modal_wa_web').modal('show');
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_order = id_order;
	ajx=$.post(controller+"wa_web",dt,function(raw)
	{
		$('#btn_wa_web').html(raw);
	});
	return ajx;
}

function bayar_trans() {
  var xxx = glob_id_user;
  var th = $('#id_ta').val();
  var a = glob_id_siswa;
  var b = glob_id_trans;
  var c = glob_id_kelas;
  var d = glob_id_jenis_transaksi;
  var e = glob_nominal_ketetapan
  var f = 1;
 // var g = document.querySelector('[id="xxg"]').value;
  
  var y = parseInt(glob_nominal_kekurangan);
  var l = parseInt($('#byr_nominal_bayar').val());
  var infaq = $('#byr_infaq').val();
  var jenis = $('#byr_metode').val();
  var id_kategori_tr = glob_id_kat;
  if (isNaN(infaq) || infaq == '') {
    var infaq = 0;
  }
  var hasil = y - l;
  //alert (hasil);
  localStorage.setItem("jenis", jenis);
  if (jenis == "Ikhlas" || jenis == "Ikhlas Saku") {
    if (y > 0) {
      alertify.error("Metode Seikhlasnya Hanya Untuk Ketetapan Rp.0");
    } 
    else {
      ajx = $.ajax({
        type: "GET",
        url:
          controller+"insert_trans/?a=" +
          a +
          "&b=" +
          b +
          "&c=" +
          c +
          "&d=" +
          d +
          "&e=" +
          e +
          "&f=" +
          f +
          "&l=" +
          l +
          "&infaq=" +
          infaq +
          "&y=" +
          hasil +
          "&jns=" +
          jenis +
          "&id_kategori_tr=" +
          id_kategori_tr+
          "&th=" +
          th,
        success: function () {
			load_trans_sementara();
			$('#modal_bayar_trans').modal('hide');
        },
      });
	  load_show(ajx);
    }
  } 
  else if (jenis == "shopeeqris" || jenis == "bniqris") {
    if (l > y) {
      alertify.error("Melebihi ketentuan");
      return false;
    } 
    else {
      $.ajax({
        type: "GET",
        url:
          controller+"insert_trans_qris/?xxx=" +
          xxx +
          "&a=" +
          a +
          "&b=" +
          b +
          "&c=" +
          c +
          "&d=" +
          d +
          "&e=" +
          e +
          "&f=" +
          f +
          "&l=" +
          l +
          "&infaq=" +
          infaq +
          "&y=" +
          hasil +
          "&jns=" +
          jenis +
          "&id_kategori_tr=" +
          id_kategori_tr+
          "&th=" +
          th,
        success: function () {
          localStorage.setItem("iuser", xxx);
          $("#popcr_sim").hide();
          $("#popcr_sim_qris").show();
          $("#seme").load(controller+"seme_qris/?xxx="+xxx);
		  $("#jumlah_seme").load(controller+"jumlah_seme");
          modalbyr.style.display = "none";
        },
      });
    }
  }
  else {
    if (l > y) {
      alertify.error("Melebihi ketentuan l:"+l+" y:"+y);
      return false;
    } 
    else {
      ajx = $.ajax({
        type: "GET",
        url:
          controller+"insert_trans/?a=" +
          a +
          "&b=" +
          b +
          "&c=" +
          c +
          "&d=" +
          d +
          "&e=" +
          e +
          "&f=" +
          f +
          "&l=" +
          l +
          "&infaq=" +
          infaq +
          "&y=" +
          hasil +
          "&jns=" +
          jenis +
          "&id_kategori_tr=" +
          id_kategori_tr+
          "&th=" +
          th,
        success: function () {
         load_trans_sementara();
			$('#modal_bayar_trans').modal('hide');
        },
      }); 
	  load_show(ajx);
    }
  }
}

function cetak_first()
{
	id_ta=$('#id_ta').val();
	var cetak = window.open(controller+'cetak_first?id_siswa='+glob_id_siswa+'&id_ta='+id_ta,'', "width=850,height=720,toolbar=no,resizable=yes");
}

function cetak_nota(id_order=0)
{
	id_ta=$('#id_ta').val();
	bypass=1;
	if(id_order==0)
	{
		id_order = glob_id_order;
		bypass=0;
	}
	if(id_order==0 && bypass==0)
	{
		alertify.error("Belum Ada Transaksi");
		return;
	}
	var cetak = window.open(controller+'cetak_nota?id_order='+id_order+'&id_ta='+id_ta,'', "width=850,height=720,toolbar=no,resizable=yes");
}

function cetak_again()
{
	id_ta=$('#id_ta').val();
	var cetak = window.open(controller+'cetak_again?id_siswa='+glob_id_siswa+'&id_ta='+id_ta,'', "width=850,height=720,toolbar=no,resizable=yes");
}

function modal_verifikasi()
{
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_siswa = glob_id_siswa;
	$('#modal_verifikasi').modal('show');
	ajx=$.post(controller+'load_verifikasi',dt, function(raw)
	{
		$('#data_verifikasi').html(raw);
	});
	load_show(ajx);
	
	ta_text = $("#id_ta").find(":selected").text();
	$('#ta_verifikasi').html(ta_text);
}

function del_trans(id_trans,id_mst_trans)
{
	dt={};
	dt.id_trans=id_trans;
	dt.id_mst_trans=id_mst_trans;
	alertify.confirm('Konfirmasi','Hapus Transaksi?', function(){
			ajx=$.post(controller+"del_transaksi", dt, function(raw)
			{
				alertify.success("Transaksi Dihapus");
				load_data_tagihan();
				saldo_uang_saku();
				saldo_tabungan();
				modal_verifikasi();
			});
			load_show(ajx);
		},
		function(){});
}

ctr_kfg_bayar = 'konfig_tu/ex/konfig_bayar/';

function del_ketetapan(id_trans,dialog=1)
{
	dt={};
	dt.id_trans=id_trans;
	if(dialog==1)
	{
		alertify.confirm('Konfirmasi', 'Hapus Ketetapan Pembayaran?', function(){
			ajx=$.post(ctr_kfg_bayar+'del_trans',dt,function(raw)
			{
				dt.id_mst = dt.id_trans;
				ajx=$.post(controller+"del_trans_sementara_sis", dt, function(raw)
				{
					load_trans_sementara();
				});
				alertify.success("Ketetapan Dihapus");
				load_data_tagihan();
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

function modal_add_trans()
{
	if(glob_id_siswa==0)
	{
		alertify.error("Pilih Warga Dahulu");
		return;
	}
	$('#modal_add_trans').modal('show');
	kb_add_trans_list();
}

function kb_add_trans_list()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
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
	siswa=[glob_id_siswa];
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_kelas=glob_id_kelas;
	dt.trans=JSON.stringify(trans);
	dt.id_siswa=siswa;
	alertify.confirm('Konfirmasi', 'Tambahkan <b> '+num_trans+' Ketetapan </b>Pembayaran Baru Ke Siswa <b>'+$('#dt_nama_siswa').val()+'</b>?', function(){
		ajx=$.post(ctr_kfg_bayar+'add_ketetapan',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.count > 0)
				{
					alertify.success('Total <b>'+rs.count+' Ketetapan </b> Berhasil Ditambahakan');
					pilih_siswa(glob_id_siswa);
					$('#modal_add_trans').modal('hide');
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

function modal_uang_saku()
{
	if(glob_id_siswa==0)
	{
		alertify.error("Pilih Warga Dahulu");
		return;
	}
	$('#modal_uang_saku').modal('show');
	history_uang_saku();
}

function clear_input_saku()
{
	$('#modal_uang_saku input').val('');
}

function history_uang_saku()
{
	dt={};
	dt.id_siswa=glob_id_siswa;
	ajx=$.post(controller+'history_uang_saku',dt,function(raw)
	{
		$('#tbl_history_uang_saku').html(raw);
	});
	load_ajx(ajx,'#div_history_uang_saku');
	clear_input_saku();
}

function saku_topup()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.nominal = $('#nominal_saku_topup').val();
	dt.id_jenjang=$('#id_jenjang').val();
	if(dt.nominal==0 || dt.nominal=='')
	{
		alertify.error("Isi Nominal Top Up");
		return;
	}
	alertify.confirm('Konfirmasi', 'Proses isi saldo dompet digital?', function(){
		ajx=$.post(controller+'saku_topup',dt,function(raw)
		{
			history_uang_saku();
			saldo_uang_saku();
		});
		load_show(ajx);
	},
	function(){});
}

function saku_tarik()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.nominal = $('#nominal_saku_tarik').val();
	dt.ket = $('#nominal_saku_tarik_ket').val();
	dt.id_jenjang=$('#id_jenjang').val();
	if(dt.nominal==0 || dt.nominal=='')
	{
		alertify.error("Isi Nominal Penarikan");
		return;
	}
	if(dt.nominal>glob_saku_saldo)
	{
		alertify.error("Saldo Tidak Cukup");
		return;
	}
	alertify.confirm('Konfirmasi', 'Simpan Penarikan Uang Saku?', function(){
		ajx=$.post(controller+'saku_tarik',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat== 1)
				{
					history_uang_saku();
					saldo_uang_saku();
					alertify.success(rs.ket);
				}
				else
				{
					alertify.error(rs.ket);
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

function saku_hapus(id_trans,nominal,stat,ket)
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.id_trans = id_trans;
	dt.nominal = nominal;
	dt.stat = stat;
	dt.ket = ket;
	alertify.confirm('Konfirmasi', 'Hapus Transaksi Uang Saku?', function(){
		ajx=$.post(controller+'saku_hapus',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat== 1)
				{
					history_uang_saku();
					saldo_uang_saku();
					alertify.success(rs.ket);
				}
				else
				{
					alertify.error(rs.ket);
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


// modal tabungan
function modal_tabungan()
{
	if(glob_id_siswa==0)
	{
		alertify.error("Pilih Warga Dahulu");
		return;
	}
	$('#modal_tabungan').modal('show');
	history_tabungan();
}

function clear_input_tabungan()
{
	$('#modal_tabungan input').val('');
}

function history_tabungan()
{
	dt={};
	dt.id_siswa=glob_id_siswa;
	ajx=$.post(controller+'history_tabungan',dt,function(raw)
	{
		$('#tbl_history_tabungan').html(raw);
	});
	load_ajx(ajx,'#div_history_tabungan');
	clear_input_tabungan();
}

function tabungan_topup()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.nominal = $('#nominal_tabungan_topup').val();
	dt.id_jenjang=$('#id_jenjang').val();
	if(dt.nominal==0 || dt.nominal=='')
	{
		alertify.error("Isi Nominal Top Up");
		return;
	}
	alertify.confirm('Konfirmasi', 'Simpan Top Up Tabungan?', function(){
		ajx=$.post(controller+'tabungan_topup',dt,function(raw)
		{
			history_tabungan();
			saldo_tabungan();
		});
		load_show(ajx);
	},
	function(){});
}

function tabungan_tarik()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.nominal = $('#nominal_tabungan_tarik').val();
	dt.ket = $('#nominal_tabungan_tarik_ket').val();
	dt.id_jenjang=$('#id_jenjang').val();
	if(dt.nominal==0 || dt.nominal=='')
	{
		alertify.error("Isi Nominal Penarikan");
		return;
	}
	alertify.confirm('Konfirmasi', 'Simpan Penarikan tabungan?', function(){
		ajx=$.post(controller+'tabungan_tarik',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat== 1)
				{
					history_tabungan();
					saldo_tabungan();
					alertify.success(rs.ket);
				}
				else
				{
					alertify.error(rs.ket);
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

function tabungan_hapus(id_tab)
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.id_tab = id_tab;
	alertify.confirm('Konfirmasi', 'Hapus Transaksi Tabungan?', function(){
		ajx=$.post(controller+'tabungan_hapus',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat== 1)
				{
					history_tabungan();
					saldo_tabungan();
					alertify.success(rs.ket);
				}
				else
				{
					alertify.error(rs.ket);
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

function tabungan_nota(id_tab)
{
	var cetak = window.open(controller+'tabungan_nota?id_tab='+id_tab,'', "width=850,height=720,toolbar=no,resizable=yes");
}

function saldo_tabungan()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	ajx=$.post(controller+'tabungan_saldo',dt,function(raw)
	{
		dt=JSON.parse(raw);
		glob_tabungan_saldo=dt.saldo;
		$('.tabungan_saldo').html(dt.saldo_rp);
	});
	return ajx;
}

// and tabungan

function saku_emoney()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.nominal = $('#nominal_saku_emoney').val();
	if(dt.nominal==0 || dt.nominal=='')
	{
		alertify.error("Isi Nominal Top Up E-Money");
		return;
	}
	alertify.confirm('Konfirmasi', 'Simpan Top Up E-Money?', function(){
		ajx=$.post(controller+'saku_emoney',dt,function(raw)
		{
			if(raw.trim()!='Berhasil')
			{
				alertify.error(raw);
			}
			else
			{
				history_uang_saku();
				saldo_uang_saku();
			}
		});
		load_show(ajx);
	},
	function(){});
}

function saldo_uang_saku()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	ajx=$.post(controller+'saku_saldo',dt,function(raw)
	{
		dt=JSON.parse(raw);
		glob_saku_saldo=dt.saldo;
		$('.saku_saldo').html(dt.saldo_rp);
	});
	return ajx;
}

function saku_notif_wa(id_trans)
{
	dt={};
	dt.id_trans = id_trans;
	ajx=$.post(controller+'saku_notif_url',dt,function(url)
	{
		var newTab = window.open(url, '_blank');
		newTab.focus();
	});
}

function uang_saku_reset()
{
	$('.saku_saldo').html('0');
	glob_saku_saldo=0;
}

function saku_nota(id)
{
	var cetak = window.open(controller+'saku_nota?id='+id,'', "width=850,height=720,toolbar=no,resizable=yes");
}

function rincian_tagihan()
{
	id_ta=$('#id_ta').val();
	id_siswa=glob_id_siswa;
	id_kelas=glob_id_kelas;
	var cetak = window.open(controller+'rincian_tagihan?id_siswa='+id_siswa+'&id_ta='+id_ta+'&id_kelas='+id_kelas,'', "width=850,height=720,toolbar=no,resizable=yes");
}

function checkboxTable(tableId, countElementId='') {
  // Select the checkbox in the table header
  var $tableHeadCheckbox = $(tableId + ' thead input[type="checkbox"]');
  // Select the element to display the count of checked checkboxes
  var $countElement = $(countElementId);

  function updateCount() {
    // Get the number of checked checkboxes
    var checkedCount = $(tableId + ' tbody input[type="checkbox"]:checked').length;
    // Update the element's innerHTML with the checked count
    $countElement.html(checkedCount);
  }

  // When the checkbox in the table header is clicked
  $tableHeadCheckbox.click(function() {
    // Determine whether it is checked or not
    var isChecked = $(this).prop('checked');
    // Set all checkboxes in the table body to the same state as the checkbox in the table header
    $(tableId + ' tbody input[type="checkbox"]').prop('checked', isChecked);
    // Update the count of checked checkboxes
    updateCount();
    
    // If not all checkboxes are checked, but at least one is, set the checkbox in the header to indeterminate
    if ($(tableId + ' tbody input[type="checkbox"]:checked').length > 0 && $(tableId + ' tbody input[type="checkbox"]:checked').length < $(tableId + ' tbody input[type="checkbox"]').length) {
      $tableHeadCheckbox.prop('indeterminate', true);
    } else {
      $tableHeadCheckbox.prop('indeterminate', false);
    }
  });
  
  // When any checkbox in the table body is clicked
  $(tableId + ' tbody input[type="checkbox"]').click(function() {
    // Check if all checkboxes in the table body are checked
    var allChecked = $(tableId + ' tbody input[type="checkbox"]').length === $(tableId + ' tbody input[type="checkbox"]:checked').length;
    
    // Set the checkbox in the table header to the same state as all checkboxes in the table body
    $tableHeadCheckbox.prop('checked', allChecked);
    // Update the count of checked checkboxes
    updateCount();
    
    // If not all checkboxes are checked, but at least one is, set the checkbox in the header to indeterminate
    if ($(tableId + ' tbody input[type="checkbox"]:checked').length > 0 && $(tableId + ' tbody input[type="checkbox"]:checked').length < $(tableId + ' tbody input[type="checkbox"]').length) {
      $tableHeadCheckbox.prop('indeterminate', true);
    } else {
      $tableHeadCheckbox.prop('indeterminate', false);
    }
  });
  
  updateCount();
}

function modal_emoney()
{
	if(glob_id_siswa==0)
	{
		alertify.error("Pilih Warga Dahulu");
		return;
	}
	$('#modal_emoney').modal('show');
	history_emoney();
}

function clear_input_saku()
{
	$('#modal_emoney input').val('');
}

function history_emoney()
{
	dt={};
	dt.id_siswa=glob_id_siswa;
	ajx=$.post(controller+'history_emoney',dt,function(raw)
	{
		$('#tbl_history_emoney').html(raw);
	});
	load_ajx(ajx,'#div_history_emoney');
	clear_input_saku();
}

function emoney_topup()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.nominal = $('#nominal_emoney').val();
	if(dt.nominal==0 || dt.nominal=='')
	{
		alertify.error("Isi Nominal Top Up E-Money");
		return;
	}
	alertify.confirm('Konfirmasi', 'Simpan Top Up E-Money?', function(){
		ajx=$.post(controller+'saku_emoney',dt,function(raw)
		{
			if(raw.trim()!='Berhasil')
			{
				alertify.error(raw);
			}
			else
			{
				history_emoney();
				saldo_uang_saku();
			}
		});
		load_show(ajx);
	},
	function(){});
}

function del_topup_emoney(id_trans,id_siswa,dialog=1)
{
	dt={};
	dt.id_trans=id_trans;
	if(dialog==1)
	{
		alertify.confirm('Konfirmasi', 'Batalkan Top Up?', function(){
			ajx=$.post(controller+'del_trans_money',dt,function(raw)
			{
				alertify.success("Top Up Dibatalkan");
				history_emoney();
				saldo_uang_saku();
			});
		},
		function(){});
	}
	else
	{
		ajx=$.post(ctr_kfg_bayar+'del_trans',dt,function(raw)
			{
				alertify.success(raw);
				history_emoney();
				saldo_uang_saku();
			});
		load_show(ajx);
	}
}

function show_toggle() {
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'show_toggle',dt,function(raw)
	{
		$('#toggle').html(raw);
		$('#toggle').show();
	});
	return ajx;
}

function toggleSwitch(element) {
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
    if (element.checked) {
    	// Perform action when switch is toggled ON
    	dt.notif_transaksi='1';
    } else {
    	// Perform action when switch is toggled OFF
    	dt.notif_transaksi='0';
    }
    ajx=$.post(controller+'onoff_wappin',dt,function(raw)
	{
		if(isJson(raw))
		{
			rs=JSON.parse(raw);
			if(rs.stat == 1)
			{
				alertify.success('Berhasil Dirubah');
						
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
  }