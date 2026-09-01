$(document).ready(function(){
	tab_siswa_kelas();
	sw_tgl_lahir = $("#sw_tgl_lahir").flatpickr({
		dateFormat: "d-m-Y",
		static:true
	});
	sw_tgl_diterima = $("#sw_tgl_diterima").flatpickr({
		dateFormat: "d-m-Y",
		static:true
	});
	click_tab_siswa(1);
});

controller = 'siswa/ex/data_konfigurasi_absensi/';

function ta(){
	tab_siswa_kelas();
	tab_konfig();
}

function jenjang(){
	tab_siswa_kelas();
	tab_konfig();
}

/* TAB 1 */

function tab_siswa_kelas()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	ajx = $.post(controller+'load_list_tingkat',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#id_level').html(dt.select_tingkat);
			$('#id_kelas').html(dt.select_kelas);
			$('#sw_kelas, #kb_add_trans_siswa_kls').html(dt.form_kelas);
			$('#id_kelas_move').html(dt.form_kelas);
			$('#id_kelas_next').html(dt.form_kelas_next);
			$('#id_ta_next').html(dt.id_ta_next);
			sk_load_kelas();
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#div_kelas');
}

function sk_load_kelas()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_level=$('#id_level').val();
	ajx=$.post(controller+'load_kelas',dt,function(raw)
	{
		$('#div_list_kelas').html(raw);
		sk_cari_kelas();
		active_kelas();
	});
	load_ajx(ajx,'#div_kelas');
}

function sk_cari_kelas()
{
	txt = $('#sk_cari_kelas').val();
	cari=cariable('#div_list_kelas',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.kelas_no_result').show(); 
	}
	else
	{
		$('.kelas_no_result').hide();
	}
}

function modal_kelas(event,mode=1, id_kelas=0)
{
	event.stopPropagation();
	glob_mode=mode;
	glob_id_kelas=id_kelas;
	$('#modal_kelas input').val('');
	$('#modal_kelas select').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_kelas').hide();
	}
	else 
	{
		$('#btn_del_kelas').show();
		dt={};
		dt.id_kelas=id_kelas;
		title='Edit';
		ajx=$.post(controller+'get_data_kelas',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#frm_nama_kelas').val(dt.nama_kelas);
				$('#frm_id_level').val(dt.level);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_kelas span').html(title+' Kelas');
	$('#ket_modal_kelas').html(title);
	$('#modal_kelas').modal('show');
}

function save_kelas()
{
	dt={};
	dt.mode=glob_mode;
	dt.id_kelas=glob_id_kelas;
	dt.nama_kelas=$('#frm_nama_kelas').val();
	dt.id_level=$('#frm_id_level').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	if(dt.id_level==0 || dt.nama_kelas=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Kelas?', function(){
		$.post(controller+'save_kelas',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Kelas berhasil');
					sk_load_kelas();
					load_list_siswa();
					$('#modal_kelas').modal('hide');
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
	},
	function(){});
	
}

function del_kelas(id_kelas)
{
		dt={};
		dt.id_kelas=glob_id_kelas;
		alertify.confirm('Konfirmasi','Hapus Kelas?', function(){
		$.post(controller+'del_kelas',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Kelas Berhasil Dihapus');
					sk_load_kelas();
					$('#modal_kelas').modal('hide');
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
	},
	function(){});
}

function del_siswa()
{
		dt={};
		dt.id_siswa=glob_id_siswa;
		alertify.confirm('Konfirmasi','Hapus Siswa?', function(){
		ajx = $.post(controller+'del_siswa',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Siswa Berhasil Dihapus');
					load_list_siswa();
					$('#modalSiswa').modal('hide');
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
	},
	function(){});
	load_show(ajx);
}

function click_kelas(obj)
{
	id_kelas = $(obj).attr('id_kelas');
	active_kelas(id_kelas);
	$('#id_kelas').val(id_kelas);
	load_list_siswa();
}

glob_kelas_active=0;
function active_kelas(id_kelas='?')
{
	if(id_kelas=='?')
	{
		id_kelas = glob_kelas_active;
	}
	glob_kelas_active=id_kelas;
	$('.kelas-item').removeClass('kelas-active');
	obj = $(".kelas-item[id_kelas='"+id_kelas+"']");
	$(obj).addClass('kelas-active');
}

function change_kelas()
{
	id_kelas=$('#id_kelas').val();
	glob_kelas_active=id_kelas;
	load_list_siswa();
	active_kelas();
}

function load_list_siswa()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_kelas=$('#id_kelas').val();
	ajx=$.post(controller+'load_list_siswa',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt=JSON.parse(raw);
			$('#tab_data_siswa').html(dt.data_siswa);
			$('#tab_aktivasi_va').html(dt.data_siswa_va);
			$('#tab_sound_nama').html(dt.data_sound_nama);
			$('#tbl_card_telp').html(dt.data_card_telp);
			
			$('#tbl_naik_kelas').html(dt.data_naik_kelas);
			checkboxTable('#tbl_naik_kelas table','#cnt_naik_siswa');
			
			$('#tbl_pindah_kelas').html(dt.data_pindah_kelas);
			checkboxTable('#tbl_pindah_kelas table','#cnt_pindah_siswa');
			
			sk_cari_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function sk_cari_siswa()
{
	txt = $('#sk_cari_siswa').val();
	cari=cariable('.list_siswa',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.siswa_no_result').show(); 
	}
	else
	{
		$('.siswa_no_result').hide();
	}
}

function  click_tab_siswa(val)
{
	elem=[];
	elem[1]='act_data_siswa';
	elem[2]='act_va_dll';
	elem[3]='act_pindah_naik';
	$('.act_tab_siswa').hide();
	$('#'+elem[val]).show();
}

function modal_import_siswa()
{
	$('#modal_import_siswa').modal('show');
	import_btn_verify();
}

function upload_excel_siswa()
{
	input_file=document.getElementById('file_excel_siswa');
		file = input_file.files[0];
		size = file.size/1000/1000;
		size=size.toFixed(2);
		dt={};
		dt.excel=$(input_file).prop('files')[0]; 
		data=getFormData(dt);
		data=getFormData(dt);
		$('#prog_upload_siswa').show();
		$('#status_upload_excel').html("");
		$('#prog_perc').html(parseInt(0));
		$('#prog_upload_siswa_bar').css('width','0%');
		$('#prog_upload_siswa_bar').prop('aria-valuenow',parseInt(0));
		if (typeof ajx_vid !== 'undefined') {
			ajx_vid.abort();ajx_vid.readyState=4;
			
		}
		ajx_vid=$.ajax({
				xhr: function() {
					var xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function(evt) {
						if (evt.lengthComputable) {
							var percentComplete = ((evt.loaded / evt.total) * 100);
							$('#prog_uploaded').html(numberWithCommas(parseInt(evt.loaded/1000)));
							$('#prog_total').html(numberWithCommas(parseInt(evt.total/1000)));
							$('#prog_perc').html(parseInt(percentComplete));
							$('#prog_upload_siswa_bar').css('width',parseInt(percentComplete)+'%');
							$('#prog_upload_siswa_bar').prop('aria-valuenow',parseInt(percentComplete));
						}
					}, false);
					return xhr;
				},
				url: controller+'upload_excel_siswa', 
				mimeType: "multipart/form-data",
				dataType: 'text', 
				cache: false,
				contentType: false,
				processData: false,
				data: data,                         
				type: 'post',
				async: true,
				timeout:600000,
				success: function(raw){
					$('#status_upload_excel').html("Upload file '<i>"+file.name+"</i>' <span class='badge bg-success' style='font-size: medium;'>Berhasil</span>");
					setTimeout(function()
					{
						load_import_siswa()
					},1000);
				},
				error: function() 
				{
					$('#status_upload_excel').html("<span class='badge bg-danger'>Upload Gagal</span>");
					ajx_vid.readyState=4;
				} 
			 });
}

function load_import_siswa()
{
	 var tab_id = document.querySelector('#tab_button_import_siswa')
	 var tab = new bootstrap.Tab(tab_id);
	 tab.show();
	 dt={};
	 dt.id_ta=$('#id_ta').val();
	 dt.id_jenjang=$('#id_jenjang').val();
	 ajx=$.post(controller+'load_import_siswa',dt,function(raw)
		{
			if(isJson(raw))
			{
				spn_ta=$( "#id_ta option:selected" ).text();
				spn_jenjang=$( "#id_jenjang option:selected" ).text();
				$('.spn_ta').html(spn_ta);
				$('.spn_jenjang').html(spn_jenjang);
				$('#import_siswa_no_file').hide();
				$('#import_siswa_file_avail').show();
				dt=JSON.parse(raw);
				$('#tbl_import_siswa').html(dt.data_import);
				import_btn_verify();
				import_hitung_dipilih();
			}
			else
			{
				$('#import_siswa_no_file').show();
				$('#import_siswa_file_avail').hide();
			}
		});
	load_ajx(ajx,'#content_tab_import_siswa');
}

function import_btn_verify()
{
	setTimeout(function()
	{
		if($('#import_siswa_file_avail').is(':visible'))
		{
			$('.btn_import').fadeIn();
		}
		else
		{
			$('.btn_import').fadeOut();
		}
	},300);
}

function import_pilih_semua()
{
	if($('#import_chk_all').prop('checked'))
	{
		$('#tbl_import_siswa tbody .chk_item').prop('checked',true);
	}
	else
	{
		$('#tbl_import_siswa tbody .chk_item').prop('checked',false);
	}
	import_hitung_dipilih();
}

function import_hitung_dipilih()
{
	dipilih=0;
	total_chk=0;
	$('#tbl_import_siswa tbody .chk_item').each(function()
	{
		if($(this).prop('checked'))
		{
			dipilih+=1;
		}
		total_chk+=1;
	});
	if(dipilih==0)
	{
		$('#import_chk_all').prop('checked',false);
		document.getElementById("import_chk_all").indeterminate = false;
	}
	else if(dipilih==total_chk)
	{
		$('#import_chk_all').prop('checked',true);
		document.getElementById("import_chk_all").indeterminate = false;
	}
	else
	{
		$('#import_chk_all').prop('checked',false);
		document.getElementById("import_chk_all").indeterminate = true;
	}
	$('.import_jml_pilih').html(dipilih);
}

function import_siswa_btn()
{
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	dt.siswa=[];
	num=0;
	no_class=0;
	$('#tbl_import_siswa tbody .chk_item').each(function()
	{
		if($(this).prop('checked'))
		{
			siswa={};
			no = $(this).attr('no');
			siswa.nama_siswa = $(this).attr('nama_siswa');
			siswa.lp = $(this).attr('lp');
			siswa.tlahir = $(this).attr('tlahir');
			siswa.no_wa = $(this).attr('no_wa');
			siswa.id_kelas = $('.import_id_kelas_'+no).val();
			if(siswa.id_kelas==0)
			{
				no_class=1;
			}
			dt.siswa.push(siswa);
			num+=1;
		}
	});
	if(num==0)
	{
		alertify.error('Belum ada Data Siswa yang dipilih');
		return;
	}
	if(no_class==1)
	{
		alertify.error('Ada Siswa yang belum dapat kelas');
		return;
	}
		alertify.confirm('Konfirmasi','Import siswa sebanyak '+num+' data?', function(){
		ajx=$.post(controller+'import_siswa',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success('Import Siswa Berhasil');
					sk_load_kelas();
					load_list_siswa();
					$('#modal_import_siswa').modal('hide');
					$('#prog_upload_siswa').hide();
					$('#import_siswa_no_file').show();
					$('#import_siswa_file_avail').hide();
					import_btn_verify();
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
	},
	function(){});
}

function modal_siswa(mode=1, id_siswa=0)
{
	glob_mode=mode;
	glob_id_siswa=id_siswa;
	$('#modal_siswa input').val('');
	reset_foto()
	elem_siswa='#form_data_siswa';
	$(elem_siswa +' input, ' + elem_siswa + ' select, ' + elem_siswa + ' textarea').val('');
	$('#sw_kelas').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_siswa').hide();
	}
	else 
	{
		$('#btn_del_siswa').show();
		dt={};
		dt.id_siswa=id_siswa;
		dt.id_ta=$('#id_ta').val();
		title='Edit';
		ajx=$.post(controller+'get_data_siswa',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				for(var ky in dt) {
					$(elem_siswa+' *[field='+ky+']').val(dt[ky]);
					$(elem_siswa+' *[sp_field='+ky+']').val(dt[ky]);
				}
				change_nama_kelas();
				if(dt.foto !='' && dt.foto != null)
				{
					$('#prs_photo_preview').css('background-image', 'url(images/foto_siswa/'+dt.foto+')');
				}
			}
		});
		load_show(ajx);
	}
	$('#btn_save_siswa').html(title+' Siswa');
	$('#ket_data_siswa').html(title);
	$('#modalSiswa').modal('show');
	change_nama_kelas();
}

function reset_foto()
{
	$('#prs_photo_preview').css('background-image', 'url("images/no-profile.png")');
	$('#prs_photo').val('');
}

function change_nama_kelas()
{
	nama=$('#frm_nama_siswa').val();
	kelas= $( "#sw_kelas option:selected" ).text();
	$('#txt_nama_siswa').html(nama);
	$('#txt_kelas_siswa').html(kelas);
	if(nama=='')
	{
		$('#txt_nama_siswa').html('Nama Siswa');
	}
	if(kelas=='-pilih-')
	{
		$('#txt_kelas_siswa').html('Kelas');
	}
}

document.querySelector("#prs_photo").onchange = function() {
		var file = this.files[0];
		if(file.size>512000)
		{
			alert("Ukuran gambar melebihi ukuran maksimal (512KB)");
			$('#dialog_dispay').modal('hide');
			return;
		}
            var reader = new FileReader();
            reader.onloadend = function () {
				$('#prs_photo_preview').css('background-image', 'url("' + reader.result + '")');
            }
            if (file) {
                reader.readAsDataURL(file);
            } else {
            }
};

glob_id_siswa=0;
function save_siswa()
{
	frm={};
	frm.mode=glob_mode;
	frm.id_siswa=glob_id_siswa;
	frm.id_ta=$('#id_ta').val();
	frm.id_jenjang=$('#id_jenjang').val();
	frm.fld_id_jenjang=$('#id_jenjang').val();
	frm.fld_id_sesi=$('#id_ta').val();
	valid=1;
	elem ='#form_data_siswa';
	foto= $('#prs_photo').prop('files')[0]; 
	$(elem +' input, ' + elem + ' select[field], ' + elem + ' select[sp_field], ' + elem + ' textarea').each(function(){
		val = $(this).val();
		if(($(this).attr('required') && val=='') || ($(this).prop("tagName")=='SELECT' && val=='0'))
		{
			valid=0;
		}
		if(field=$(this).attr('sp_field')){
			frm[field]=val;
		}
		if(field=$(this).attr('field')){
			frm['fld_'+field]=val; 
		}
	});
	if(frm['fld_telp_one']!='') 
	{
		if(frm['fld_telp_one'].match(/^[0-9]+$/) != null)
		{
			//nothing
		}
		else
		{
			alertify.error('Nomor Telepon tidak valid');
			return;
		}
	}
	data=getFormData(frm);
	data.append('foto',foto);
	if(valid==0)
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[frm.mode]+' Siswa?', function(){
		ajx=$.ajax({
			url: controller+'save_siswa', 
			dataType: 'text', 
			cache: false,
			contentType: false,
			processData: false,
			data: data,                         
			type: 'post',
			success: function(raw){
				if(isJson(raw))
				{
					rs=JSON.parse(raw);
					if(rs.stat == 1)
					{
						alertify.success(mode_ket[frm.mode]+' Siswa Berhasil');
						sk_load_kelas();
						load_list_siswa();
						$('#modalSiswa').modal('hide');
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
			}
		 });
		 load_show(ajx);
	},
	function(){});
}

$('#tbl_card_telp').on('click', 'tbody tr', function(event) {
  if ($(event.target).closest('.td_last').length) {
    return;
  }
  mode = $('#mode_card_telp').val();
  val = $(this).find('.'+mode).html();
  $('#card_telp_val').val(val);
});

function save_card_telp(id_siswa)
{
	dt={};
	dt.id_siswa=id_siswa;
	dt.mode=$('#mode_card_telp').val();
	dt.val = $('#card_telp_val').val();
	ajx=$.post(controller+'save_card_telp',dt,function(raw)
		{
			if(raw.trim()=='')
			{
				load_list_siswa();
			}
			else
			{
				alertify.error(raw);
			}
		});
	load_ajx(ajx,'#colom-siswa');
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

/* TAB 2 */
function tab_konfig(){
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'tampil_data_konfig',dt,function(raw)
	{
		$('#div_list_konfig').html(raw);
		sk_cari_konfig();
	});
	load_ajx(ajx,'#div_konfig');
}

function sk_cari_konfig(){
	txt = $('#sk_cari_konfig').val();
	cari=cariable('#div_list_konfig',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.konfig_no_result').show(); 
	}
	else
	{
		$('.konfig_no_result').hide();
	}
}

function modal_konfig(event,mode=1, id_konfig=0){
	event.stopPropagation();
	glob_mode=mode;
	glob_id_konfig=id_konfig;
	$('#modal_konfig input').val('');
	$('#modal_konfig select').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_konfig').hide();
	}
	else 
	{
		$('#btn_del_konfig').show();
		dt={};
		dt.id_konfig=id_konfig;
		dt.id_ta=$('#id_ta').val();
		dt.id_jenjang=$('#id_jenjang').val();
		title='Edit';
		ajx=$.post(controller+'get_data_konfig',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#frm_kelas').val(dt.id_kelas);
				$('#frm_hari').val(dt.hari);
				$('#frm_jam_masuk').val(dt.jam_masuk);
				$('#frm_jam_terlambat').val(dt.jam_awal_keterlambatan);
				$('#frm_jam_awal').val(dt.jam_awal_pulang);
				$('#frm_jam_pulang').val(dt.jam_pulang);
				$('#frm_status').val(dt.status);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_konfig span').html(title+' Konfigurasi Absensi');
	$('#ket_modal_konfig').html(title);
	$('#modal_konfig').modal('show');
	dt={};
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'get_data_kelas_konfig',dt,function(raw)
	{
		$('#div_kelas_konfig').html(raw);
	});
}

function save_konfig(){
	dt={};
	dt.mode=glob_mode;
	dt.id_konfig=glob_id_konfig;
	dt.jenis=$('#frm_jenis').val();
	dt.hari=$('#frm_hari').val();
	dt.jam_masuk=$('#frm_jam_masuk').val();
	dt.jam_terlambat=$('#frm_jam_terlambat').val();
	dt.jam_awal=$('#frm_jam_awal').val();
	dt.jam_pulang=$('#frm_jam_pulang').val();
	dt.status=$('#frm_status').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	if(dt.jenis=='' || dt.hari=='' || dt.jam_masuk=='' || dt.jam_terlambat=='' || dt.jam_awal=='' || dt.jam_pulang=='' || dt.status=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Konfigurasi Absensi?', function(){
		$.post(controller+'save_konfig',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Konfigurasi Absensi');
					tab_konfig();
					$('#modal_konfig').modal('hide');
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
	},
	function(){});
}

function del_konfig(id_konfig){
	dt={};
	dt.id_konfig=glob_id_konfig;
	alertify.confirm('Konfirmasi','Hapus Konfigurasi Absensi?', function(){
	$.post(controller+'del_konfig',dt,function(raw)
	{
		if(isJson(raw))
		{
			rs=JSON.parse(raw);
			if(rs.stat == 1)
			{
				alertify.success(' Konfigurasi Absensi berhasil dihapus');
				tab_konfig();
				$('#modal_konfig').modal('hide');
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
	},
	function(){});
}