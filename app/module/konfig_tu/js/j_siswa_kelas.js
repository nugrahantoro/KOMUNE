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

controller = 'konfig_tu/ex/siswa_kelas/';

function ta(){
	tab_siswa_kelas();
}

function jenjang(){
	tab_siswa_kelas();
}

function cari_semua(){
	tab_siswa_kelas();
	sk_load_kelas();
	load_list_siswa();
}

function cari_semua_jenjang(){
	tab_siswa_kelas();
	sk_load_kelas();
	load_list_siswa();
}

function pageSiswa(id_siswa)
{
	elem_class = ".pg_siswa";
	elem_to='.nav_siswa';
	nav_cur=id_siswa;
	func_name='modal_siswa(2,#nav_val#)';
	initNavdt(elem_class,elem_to,nav_cur,func_name);
}

function initNavdt(elem_class,elem_to,nav_cur,func_name='')
{
	count = $(elem_class).length;
	elem = $(elem_class+"[id_item='"+nav_cur+"']");
	cur_index = $(elem).index(elem_class);
	bt_prev = "<button class='btn btn-light btn-rounded waves-effect' disabled> <i class='bx bx-left-arrow-alt'></i> </button> ";
	if(cur_index>0)
	{
		prev_id=elem.prev(elem_class).attr('id_item');
		func=func_name.replace('#nav_val#',prev_id);
		bt_prev="<button class='btn btn-light btn-rounded waves-effect' onclick="+func+"> <i class='bx bx-left-arrow-alt'></i> </button> ";
	}
	bt_next = " <button class='btn btn-light btn-rounded waves-effect' disabled> <i class='bx bx-right-arrow-alt'></i> </button>";
	if(cur_index<count-1)
	{
		next_id=elem.next(elem_class).attr('id_item');
		func=func_name.replace('#nav_val#',next_id);
		bt_next=" <button class='btn btn-light btn-rounded waves-effect' onclick="+func+"> <i class='bx bx-right-arrow-alt'></i> </button>";
	}
	html_nav = bt_prev+(cur_index+1)+"/"+count+bt_next;
	$(elem_to).html(html_nav);
}

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
	$('#btn_save_kelas span').html(title+' Wilayah');
	$('#ket_modal_kelas').html(title);
	$('#modal_kelas').modal('show');
}

function save_kelas()
{
	dt={};
	dt.mode=glob_mode;
	dt.id_kelas=glob_id_kelas;
	dt.nama_kelas=$('#frm_nama_kelas').val();
	//dt.id_level=$('#frm_id_level').val();
	dt.id_ta=$('#id_ta').val();
	dt.id_jenjang=$('#id_jenjang').val();
	if(dt.id_level==0 || dt.nama_kelas=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Wilayah?', function(){
		$.post(controller+'save_kelas',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Wilayah berhasil');
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
		alertify.confirm('Konfirmasi','Hapus Wilayah?', function(){
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
		alertify.confirm('Konfirmasi','Hapus Warga?', function(){
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

function modal_siswa(mode = 1, id_siswa = 0)
{
    glob_mode = mode;
    glob_id_siswa = id_siswa;

    const formSelector = '#form_data_siswa';

    // =========================================
    // RESET FORM
    // =========================================
    reset_foto();

    $(`
        ${formSelector} input,
        ${formSelector} textarea
    `).val('');

    $(`
        ${formSelector} select
    `).prop('selectedIndex', 0);

    // RESET PHOTO
    $('#prs_photo_preview').attr(
        'src',
        'assets/images/users/avatar-1.jpg'
    );

    // RESET TITLE
    $('#txt_nama_siswa').html('Nama Warga');
    $('#txt_kelas_siswa').html('Wilayah');

    // =========================================
    // MODE TAMBAH
    // =========================================
    if (mode == 1)
    {
        $('#btn_del_siswa').hide();

        $('#ket_data_siswa').html('Tambah');
        $('#btn_save_siswa').html('Simpan Data Warga');

        $('#modalSiswa').modal('show');

        return;
    }

    // =========================================
    // MODE EDIT
    // =========================================
    $('#btn_del_siswa').show();

    $('#ket_data_siswa').html('Edit');
    $('#btn_save_siswa').html('Update Data Warga');

    const payload = {
        id_siswa: id_siswa,
        id_ta: $('#id_ta').val()
    };

    const ajx = $.post(
        controller + 'get_data_siswa',
        payload,

        function(raw)
        {
            // =========================
            // INVALID JSON
            // =========================
            if (!isJson(raw))
            {
                alertify.alert('Alert', raw);
                return;
            }

            const response = JSON.parse(raw);

            // =========================
            // FAILED
            // =========================
            if (response.stat != 1)
            {
                alertify.error(response.ket);
                return;
            }

            const data = response.data;

            // =========================
            // RENDER FIELD
            // =========================
            for (const key in data)
            {
                $(`
                    ${formSelector} *[field=${key}]
                `).val(data[key]);

                $(`
                    ${formSelector} *[sp_field=${key}]
                `).val(data[key]);
            }

            // =========================
            // RENDER HEADER
            // =========================
            $('#txt_nama_siswa').html(
                data.nama_siswa || 'Nama Warga'
            );

            $('#txt_kelas_siswa').html(
                $('#sw_kelas option:selected').text()
            );

            // =========================
            // RENDER PHOTO
            // =========================
            if (data.foto)
            {
                $('#prs_photo_preview').attr(
                    'src',
                    'images/foto_siswa/' + data.foto
                );
            }

            change_nama_kelas();
        }
    );

    load_show(ajx);

    $('#modalSiswa').modal('show');
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
function save_siswa() {

    const form = {
        mode: glob_mode,
        id_siswa: glob_id_siswa,
        id_ta: $('#id_ta').val(),
        id_jenjang: $('#id_jenjang').val(),
        fld_id_jenjang: $('#id_jenjang').val(),
        fld_id_sesi: $('#id_ta').val()
    };

    const formSelector = '#form_data_siswa';
    const foto = $('#prs_photo').prop('files')[0];

    let isValid = true;

    $(`
        ${formSelector} input,
        ${formSelector} select[field],
        ${formSelector} select[sp_field],
        ${formSelector} textarea
    `).each(function () {

        const $el = $(this);
        const value = $el.val()?.trim();

        if (
            ($el.attr('required') && !value) ||
            ($el.is('select') && value === '0')
        ) {
            isValid = false;
            $el.addClass('is-invalid');
        } else {
            $el.removeClass('is-invalid');
        }

        const spField = $el.attr('sp_field');
        if (spField) {
            form[spField] = value;
        }

        const field = $el.attr('field');
        if (field) {
            form[`fld_${field}`] = value;
        }
    });

    if (form.fld_telp_one) {

        const phoneRegex = /^[0-9]+$/;

        if (!phoneRegex.test(form.fld_telp_one)) {
            alertify.error('Nomor WhatsApp tidak valid');
            return;
        }
    }

    if (!isValid) {
        alertify.error('Data belum lengkap');
        return;
    }

    const data = getFormData(form);

    if (foto) {
        data.append('foto', foto);
    }

    const modeText = {
        1: 'Tambah',
        2: 'Edit'
    };

    const actionText = modeText[form.mode] || 'Simpan';

    alertify.confirm(
        'Konfirmasi',
        `${actionText} Data Warga?`,

        function () {

            const ajx = $.ajax({
                url: controller + 'save_siswa',
                type: 'POST',
                data: data,

                dataType: 'text',
                cache: false,
                contentType: false,
                processData: false,

                success: function (raw) {

                    if (!isJson(raw)) {
                        alertify.alert('Alert', raw);
                        return;
                    }

                    const response = JSON.parse(raw);

                    if (response.stat == 1) {

                        alertify.success(
                            `${actionText} Data Warga berhasil`
                        );

                        sk_load_kelas();
                        load_list_siswa();

                        $('#modalSiswa').modal('hide');

                        return;
                    }

                    alertify.alert(
                        'Alert',
                        response.ket
                    );
                }
            });

            load_show(ajx);
        },

        function () {
            // cancel
        }
    );
}

function kirim_notif(id_siswa,no_va)
{
	dt={};
	dt.nis = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	dt.va = no_va
	ajx=$.get(controller+'kirim_notif',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('Info Akun', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function aktif_va(id_siswa)
{
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	ajx=$.post(controller+'aktif_va',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('No. VA dibuat', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function nonaktif_va(id_order)
{
	url = "https://wsprimacendikia.sekolah-smartbilling.net/newws/batalkan/expglobal/"+glob_dbn+'/' + id_order;
	ajx=$.post(url,function(raw){
		//alertify.warning(raw);
		load_list_siswa(); 
	});
	load_ajx(ajx,'#colom-siswa');
}

function aktif_va_cring(id_siswa)
{
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	ajx=$.post(controller+'aktif_va_cring',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('No. VA dibuat', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function nonaktif_va_cring(id_order)
{
	dt={};
	dt.id_order = id_order;
	ajx=$.post(controller+'nonaktif_va_cring',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('Berhasil non-aktifkan VA', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function aktif_va_bsi(id_siswa)
{
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	ajx=$.post(controller+'aktif_va_bsi',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('No. VA dibuat', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function nonaktif_va_bsi(id_order)
{
	dt={};
	dt.id_order = id_order;
	ajx=$.post(controller+'nonaktif_va_bsi',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('Berhasil non-aktifkan VA', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function aktif_va_mandiri(id_siswa)
{
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	ajx=$.post(controller+'aktif_va_mandiri',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('No. VA dibuat', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function nonaktif_va_mandiri(id_order)
{
	dt={};
	dt.id_order = id_order;
	ajx=$.post(controller+'nonaktif_va_mandiri',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('Berhasil non-aktifkan VA', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function aktif_va_bmi(id_siswa)
{
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	ajx=$.post(controller+'aktif_va_bmi',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('No. VA dibuat', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function nonaktif_va_bmi(id_order)
{
	dt={};
	dt.id_order = id_order;
	ajx=$.post(controller+'nonaktif_va_bmi',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('Berhasil non-aktifkan VA', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function aktif_va_bpdjateng(id_siswa)
{
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	ajx=$.post(controller+'aktif_va_bpdjateng',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('No. VA dibuat', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function nonaktif_va_bpdjateng(id_order)
{
	dt={};
	dt.id_order = id_order;
	ajx=$.post(controller+'nonaktif_va_bpdjateng',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('Berhasil non-aktifkan VA', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function aktif_va_bri(id_siswa)
{
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	ajx=$.post(controller+'aktif_va_bri',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('No. VA dibuat', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function nonaktif_va_bri(id_order)
{
	dt={};
	dt.id_order = id_order;
	ajx=$.post(controller+'nonaktif_va_bri',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('Berhasil non-aktifkan VA', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function aktif_va_bca_plink(id_siswa)
{
	dt={};
	dt.id_siswa = id_siswa;
	dt.id_jenjang = $('#id_jenjang').val();
	dt.id_ta = $('#id_ta').val();
	ajx=$.post(controller+'aktif_va_bca_plink',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('No. VA dibuat', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function nonaktif_va_bca_plink(id_order)
{
	dt={};
	dt.id_order = id_order;
	ajx=$.post(controller+'nonaktif_va_bca_plink',dt,function(raw){
		if(raw.trim()=='success')
		{
			alertify.success('Berhasil non-aktifkan VA', raw);
			load_list_siswa();
		}
		else
		{
			alertify.alert('Alert', raw);
			load_list_siswa();
		}
	});
	load_ajx(ajx,'#colom-siswa');
}

function cetak_va(id_siswa)
{
	window.open(controller+'cetak_va/'+id_siswa, "", "width=720,height=720,toolbar=no");
}

function pin_siswa(id_siswa,pin='')
{
	glob_id_siswa=id_siswa;
	glob_pin_siswa=pin;
	$('#modal_pin_siswa input').val('');
	if(pin=='')
	{
		title='Tambah';
		$('#col_pin_keterangan').hide();
	}
	else 
	{
		title='Edit';
		$('#pin_nomor').val(pin);
		$('#col_pin_keterangan').show();
	}
	$('#modal_pin_siswa .modal_title').html(title);
	$('#modal_pin_siswa').modal('show');
}

function pin_siswa_save()
{
	dt.id_siswa = glob_id_siswa;
	dt.pin = $('#pin_nomor').val().trim();
	dt.ket = $('#pin_keterangan').val();
	if(dt.pin.length<6 || dt.pin.length>6)
	{
		alertify.error("PIN harus 6 digit angka");
		return;
	}
	if(dt.pin == glob_pin_siswa)
	{
		alertify.error("PIN tidak berubah");
		return;
	}
	if(dt.ket=='' && glob_pin_siswa!='')
	{
		alertify.error("Keterangan Kosong");
		return;
	}
	mode = $('#modal_pin_siswa .modal_title').html();
	alertify.confirm('Konfirmasi', mode+' Pin Siswa?', function(){
			ajx=$.post(controller+'pin_siswa_save',dt,function(raw)
			{
				load_list_siswa();
				$('#modal_pin_siswa').modal('hide');
			});
			load_ajx(ajx,'#colom-siswa');
		},
		function(){});
}

function batch_naik_siswa()
{
	id_siswa=[];
	id_kelas=$("#id_kelas_next").val();
	nama_kelas=$('#id_kelas_next option:selected').text();;
	nama_ta=$('#id_ta_next option:selected').text();;
	count=0;
	$('#tbl_naik_kelas tbody .chk_item').each(function()
	{
		ts = $(this);
		if(ts.attr('sta')=='bn' && ts.is(':checked'))
		{
			id_siswa.push(ts.attr('id_siswa'));
			count+=1;
		}
	});
	if(count==0)
	{
		alertify.error("Tandai Siswa yg akan dinaikkan");
	}
	else if (id_kelas == 0)
	{
		alertify.error("Pilih Kelas Tujuan");
	}
	else
	{
		alertify.confirm('Konfirmasi', 'Naikkan <b>'+count+' Siswa</b> yang ditandai ke kelas <b>'+nama_kelas+'</b> TA '+nama_ta+'?', function(){
			naik_siswa(id_siswa.join());
		},
		function(){});
	}
}

function naik_siswa(id_siswa)
{
	dt={};
	dt.id_siswa=id_siswa;
	dt.id_kelas_next=$('#id_kelas_next').val();
	dt.id_ta_next=$('#id_ta_next').val();
	if(dt.id_kelas_next==0)
	{
		alertify.error("Pilih Kelas Tujuan Dahulu");
		return;
	}
	ajx=$.post(controller+'naik_siswa',dt,function(raw)
		{
			load_list_siswa();
			//alert(raw);
			return;
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
	load_ajx(ajx,'#colom-siswa');
}

function batch_naik_siswa_batal()
{
	id_penempatan=[];
	count=0;
	$('#tbl_naik_kelas tbody .chk_item').each(function()
	{
		ts = $(this);
		if(ts.attr('sta')=='n' && ts.is(':checked'))
		{
			id_penempatan.push(ts.attr('id_penempatan'));
			count+=1;
		}
	});
	if(count==0)
	{
		alertify.error("Tandai Siswa yg akan dibatalakn");
	}
	else
	{
		alertify.confirm('Konfirmasi', 'Batalkan <b>'+count+' Siswa</b> yang ditandai?', function(){
			naik_siswa_batal(id_penempatan.join());
		},
		function(){});
	}
}

function naik_siswa_batal(id_penempatan)
{
	dt={};
	dt.id_penempatan=id_penempatan;
	dt.id_kelas_next=$('#id_kelas_next').val();
	dt.id_ta_next=$('#id_ta_next').val();
	ajx=$.post(controller+'naik_siswa_batal',dt,function(raw)
		{
			if(raw.trim()=='')
			{
				load_list_siswa();
			}
			else
			{
				alertify.error(raw);
			}
			//alert(raw);
			return;
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
	load_ajx(ajx,'#colom-siswa');
}

function batch_pindah_siswa()
{
	id_siswa=[];
	id_kelas=$("#id_kelas_move").val();
	nama_kelas=$('#id_kelas_move option:selected').text();;
	count=0;
	$('#tbl_pindah_kelas tbody .chk_item').each(function()
	{
		ts = $(this);
		if(ts.attr('sta')=='bn' && ts.is(':checked'))
		{
			id_siswa.push(ts.attr('id_siswa'));
			count+=1;
		}
	});
	if(count==0)
	{
		alertify.error("Tandai Siswa yg akan dipindah kelas");
	}
	else if (id_kelas == 0)
	{
		alertify.error("Pilih Kelas Tujuan");
	}
	else
	{
		alertify.confirm('Konfirmasi', 'Pindahkan <b>'+count+' Siswa</b> yang ditandai ke kelas <b>'+nama_kelas+'</b> ?', function(){
			pindah_siswa(id_siswa.join());
		},
		function(){});
	}
}

function pindah_siswa(id_siswa)
{
	dt={};
	dt.id_siswa=id_siswa;
	dt.id_kelas_move=$('#id_kelas_move').val();
	dt.id_ta=$('#id_ta').val();
	if(dt.id_kelas_move==0)
	{
		alertify.error("Pilih Kelas Tujuan Dahulu");
		return;
	}
	ajx=$.post(controller+'pindah_siswa',dt,function(raw)
		{
			sk_load_kelas();
			load_list_siswa();
		});
	load_ajx(ajx,'#colom-siswa');
}

var currentAudio = null;
function play_sound_nama(file) {
  var audio = new Audio();
  audio.src = 'panggilan-siswa/files/'+file;
   if (currentAudio != null) {
    currentAudio.pause();
  }
  currentAudio = audio;
  audio.play();
}

function play_sound_nama_local() {
  fileInput = $('#file_sound_nama');
  var audio = new Audio();
  audio.src = URL.createObjectURL(fileInput[0].files[0]);

  // pause any currently playing audio before starting the new one
  if (currentAudio != null) {
    currentAudio.pause();
  }

  currentAudio = audio;
  audio.play();
}

function dialog_sound_nama(id_siswa)
{
	glob_id_siswa=id_siswa;
	$('#file_sound_nama').click();
}

$('#file_sound_nama').on('change', function()
{
	if($(this).val()=='')
	{
		//nothing
	}
	else
	{
		$('#modal_sound_nama').modal('show');
		nama_siswa = $('#tr_'+glob_id_siswa).find('td').eq(1).html() + ' ('+ $('#tr_'+glob_id_siswa).find('td').eq(3).html()+ ') ' ;
		$('#sound_nama_siswa').html(nama_siswa);
		
		var file = this.files[0];
		$('#snd_name').html(file.name);
		fileSize=file.size;
		 var sizeInKB = fileSize < 1000000 ? (fileSize / 1024).toLocaleString('en', { maximumFractionDigits: 2 }) + ' KB' : (fileSize / 1000000).toLocaleString('en', { maximumFractionDigits: 2 }) + ' MB';
		$('#snd_size').html(sizeInKB);
		
		 // create a new file reader
		  var reader = new FileReader();

		  // start reading the file
		  reader.readAsDataURL(file);

		  // when the file is loaded, get the duration
		  reader.onload = function() {
			var audio = new Audio();
			audio.src = reader.result;

			// get the duration of the audio file
			audio.addEventListener('loadeddata', function() {
			  var duration = audio.duration;
			  
			  // convert duration to mm:ss format
			  var minutes = Math.floor(duration / 60);
			  var seconds = Math.floor(duration % 60);
			  var durationFormatted = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
			  if(durationFormatted=='Infinity:NaN')
			  {
				  durationFormatted='-';
			  }
			  $('#snd_duration').html(durationFormatted);
			});
		  }
		
	}
});

function upload_sound_nama()
{
	$('#modal_sound_nama').modal('hide');
	var upload_msg=alertify.warning('<div class="spinner-border" role="status" style="color: white;width: 1rem;height: 1rem;">\
                                                <span class="sr-only">Loading...</span>\
                                            </div>'+" Sedang Mengunggah Sound",0);
	input_file=document.getElementById('file_sound_nama');
		file = input_file.files[0];
		dt={};
		dt.sound=$(input_file).prop('files')[0]; 
		dt.id_siswa=glob_id_siswa;
		data=getFormData(dt);
		ajx_vid=$.ajax({
				url: controller+'upload_sound_nama', 
				mimeType: "multipart/form-data",
				dataType: 'text', 
				cache: false,
				contentType: false,
				processData: false,
				data: data,                         
				type: 'post',
				async: true,
				timeout:60000,
				success: function(raw){
					alertify.success('<i class="bx bx-check-circle"></i>'+" Berhasil Upload");
					load_list_siswa();
					upload_msg.dismiss();
				},
				error: function() 
				{
					alertify.error('<i class="bx bx-x-circle"></i>'+" Gagal Upload");
					ajx_vid.readyState=4;
					upload_msg.dismiss();
				} 
			 });
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
