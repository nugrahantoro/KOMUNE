$(document).ready(function(){
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

controller = 'konfig_tu/ex/transaksi_emoney/';
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
			limit_emoney();
		}
	});
	load_show(ajx);
	hide_list_siswa_cari();
	
	ajx = load_data_tagihan();
	load_show(ajx);
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


ctr_kfg_bayar = 'konfig_tu/ex/konfig_bayar/';

function modal_uang_saku()
{
	if(glob_id_siswa==0)
	{
		alertify.error("Pilih Warga Terlebih Dahulu");
		return;
	}
	$('#nominal_saku_topup').val('');
	$('#keterangan_saku_topup').val('Isi Saldo Dompet Digital');
	$('#modal_uang_saku').modal('show');
}

function clear_input_saku()
{
	$('#modal_uang_saku input').val('');
}

function saku_topup()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.nominal = $('#nominal_saku_topup').val();
	dt.keterangan = $('#keterangan_saku_topup').val();
	if(dt.nominal==0 || dt.nominal=='')
	{
		alertify.error("Isi Nominal Top Up");
		return;
	}
	alertify.confirm('Konfirmasi', 'Simpan Isi Saldo Dompet Digital?', function(){
		ajx=$.post(controller+'saku_topup',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					$('#modal_uang_saku').modal('hide');
					alertify.success(rs.ket);
					load_data_tagihan();
					saldo_uang_saku();
				}
				else
				{
					alertify.error(rs.ket);
				}
			}
			else
			{
				$('#modal_uang_saku').modal('hide');
				alertify.success("Pengisian saldo berhasil");
				load_data_tagihan();
				saldo_uang_saku();
			}
		});
		load_show(ajx);
	},
	function(){});
}

function del_topup_uangku(id_trans, id_siswa)
{
	alertify.confirm('Konfirmasi Hapus', 'Yakin ingin menghapus transaksi ini?', function(){
		$.post(controller+'del_topup_uangku', { id_trans: id_trans, id_siswa: id_siswa }, function(res){
			if(isJson(res))
			{
				let rs = JSON.parse(res);
				if(rs.stat == 1)
				{
					alertify.success(rs.ket);
					load_data_tagihan();
					saldo_uang_saku();
				}
				else
				{
					alertify.error(rs.ket);
				}
			}
			else
			{
				alertify.success("Transaksi dihapus");
				load_data_tagihan();
				saldo_uang_saku();
			}
		});
	}, function(){});
}

function saku_tarik()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	dt.nominal = $('#nominal_saku_tarik').val();
	dt.ket = $('#nominal_saku_tarik_ket').val();
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
	alertify.confirm('Konfirmasi', 'Simpan Penarikan Dompet Digital?', function(){
		ajx=$.post(controller+'saku_tarik',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat== 1)
				{
					load_data_tagihan();
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
	del_topup_uangku(id_trans, glob_id_siswa);
}

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
				pilih_siswa(id_siswa);
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

function limit_emoney()
{
	dt={};
	dt.id_siswa = glob_id_siswa;
	ajx=$.post(controller+'limit_money',dt,function(raw)
	{
		dt=JSON.parse(raw);
		glob_money_saldo=dt.saldo;
		$('.money_limit').html(dt.saldo_rp);
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

function del_topup_emoney(id_trans,id_siswa,dialog=1)
{
	dt={};
	dt.id_trans=id_trans;
	if(dialog==1)
	{
		alertify.confirm('Konfirmasi', 'Batalkan Top Up?', function(){
			ajx=$.post(ctr_kfg_bayar+'del_trans',dt,function(raw)
			{
				dt.id_mst = dt.id_trans;
				ajx=$.post(controller+"del_trans_money", dt, function(raw)
				{
					load_trans_sementara();
				});
				alertify.success("Top Up Dibatalkan");
				pilih_siswa(id_siswa);
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
