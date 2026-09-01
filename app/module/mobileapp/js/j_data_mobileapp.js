$(document).ready(function(){
    load_data();
});

controller = 'mobileapp/ex/data_mobileapp/';

function jenjang(){
	load_data();
}

function load_data()
{
	dt={};
	dt.id_jenjang=$('#id_jenjang').val();
	ajx=$.post(controller+'tampil_data_info',dt,function(raw)
	{
		$('#div_data_slide').html(raw);
	});
	//load_ajx(ajx,'#col-userkeu');
}

function cari_slide()
{
	txt = $('#cari_slide').val();
	cari=cariable('#div_data_slide',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.slide_no_result').show(); 
	}
	else
	{
		$('.slide_no_result').hide();
	}
}

function aktif(id_informasi='')
{
	dt={};
	dt.id_informasi=id_informasi;
	if(id_informasi!='') 
	{
		alertify.confirm('Konfirmasi','Aktifkan informasi ini?', function(){
			ajx=$.post(controller+"aktif", dt, function(raw)
			{
				load_data();
			});
			load_ajx(ajx,'#div_data_slide');
		},
		function(){});
	}
	else
	{
		ajx=$.post(controller+"aktif",function(raw)
		{
			load_data();
		});
		load_ajx(ajx,'#div_data_slide');
	}
}

function nonaktif(id_informasi='')
{
	dt={};
	dt.id_informasi=id_informasi;
	if(id_informasi!='') 
	{
		alertify.confirm('Konfirmasi','Non-aktifkan informasi ini?', function(){
			ajx=$.post(controller+"nonaktif", dt, function(raw)
			{
				load_data();
			});
			load_ajx(ajx,'#div_data_slide');
		},
		function(){});
	}
	else
	{
		ajx=$.post(controller+"nonaktif",function(raw)
		{
			load_data();
		});
		load_ajx(ajx,'#div_data_slide');
	}
}

function modal_slide(event,mode=1, id_informasi=0)
{
	event.stopPropagation();
	glob_mode=mode;
	glob_id_informasi=id_informasi;
	dt.id_jenjang=$('#id_jenjang').val();
	$('#modal_slide input').val('');
	$('#modal_slide select').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_slide').hide();
	}
	else 
	{
		$('#btn_del_slide').show();
		dt={};
		dt.id_informasi=id_informasi;
		title='Edit';
		ajx=$.post(controller+'get_data_slide',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				//$('#frm_gambar').val(dt.image);
				$('#frm_judul').val(dt.judul);
				$('#frm_informasi').val(dt.isi_informasi);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_slide span').html(title+' Informasi');
	$('#ket_modal_slide').html(title);
	$('#modal_slide').modal('show');
}

function save_slide()
{
    let formData = new FormData();

    // file gambar
    let fileInput = $('#frm_gambar')[0];

    if(fileInput.files.length > 0){
        formData.append('gambar', fileInput.files[0]);
    }

    // data form
    formData.append('judul', $('#frm_judul').val());
    formData.append('informasi', $('#frm_informasi').val());
    formData.append('mode', glob_mode);
    formData.append('id_informasi', glob_id_informasi);
    formData.append('id_jenjang', $('#id_jenjang').val());

    // validasi
    let judul = $('#frm_judul').val();
    let informasi = $('#frm_informasi').val();

    if (judul == '' || informasi == '') {
        alertify.error('Data belum lengkap');
        return;
    }

    // validasi tambah wajib gambar
    if(glob_mode == 1 && fileInput.files.length == 0){
        alertify.error('Gambar belum dipilih');
        return;
    }

    let mode_ket = ['', 'Tambah', 'Edit'];

    alertify.confirm(
        'Konfirmasi',
        mode_ket[glob_mode] + ' Informasi?',
        function () {

            let ajx = $.ajax({
                url: controller + 'save_slide',
                type: 'POST',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,

                success: function (raw) {

                    console.log(raw);

                    if (isJson(raw)) {

                        let rs = JSON.parse(raw);

                        if (rs.stat == 1) {

                            alertify.success(rs.ket);

                            load_data();

                            $('#modal_slide').modal('hide');

                        } else {

                            alertify.alert('Alert', rs.ket);

                        }

                    } else {

                        alertify.alert(
                            'Server Error',
                            raw
                        );

                    }
                },

                error: function (xhr) {

                    console.log(xhr.responseText);

                    alertify.alert(
                        'Ajax Error',
                        xhr.responseText
                    );
                }
            });

            load_ajx(ajx);

        },
        function () {}
    );
}

function del_slide(id_informasi)
{
	dt={};
	dt.id_informasi=glob_id_informasi;
	alertify.confirm('Konfirmasi','Hapus Informasi?', function(){
		$.post(controller+'del_slide',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Informasi Berhasil Dihapus');
					load_data();
					$('#modal_slide').modal('hide');
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