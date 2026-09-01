$(document).ready(function(){
    load_data();
    $("#kantin").hide();
    $("#walikelas").hide();
    $("#absen").hide();
    $("#jurnalguru").hide();
    $("#absen_walikelas").hide();
    $("#absen_walikelas_jurnal").hide();
    $("#menu_default").hide();
    $("#menu_custom").hide();
});

controller = 'mobileapp/ex/officer/';

function load_data()
{
	dt={};
	ajx=$.post(controller+'tampil_data',dt,function(raw)
	{
		$('#div_data_user').html(raw);
	});
	ajx=$.post(controller+'tampil_data_akses',dt,function(raw)
	{
		$('#div_data_akses').html(raw);
	});
	load_ajx(ajx,'#col-userkeu');
	ajx=$.post(controller+'tampil_data_menu',dt,function(raw)
	{
		$('#div_data_menu').html(raw);
	});
	load_ajx(ajx,'#col-userkeu');
}

function cari_user()
{
	txt = $('#cari_user').val();
	cari=cariable('#div_data_user',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.user_no_result').show(); 
	}
	else
	{
		$('.user_no_result').hide();
	}
}

function modal_user(event,mode=1, id_user='')
{
	glob_mode=mode;
	glob_id_user=id_user;
	$('#modal_user input').val('');
	$('#modal_user select').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_user').hide();
	}
	else 
	{
		$('#btn_del_user').show();
		dt={};
		dt.id_user=id_user;
		title='Edit';
		ajx=$.post(controller+'get_data_user',dt,function(raw)
		{
			if(isJson(raw))
			{
				dts = JSON.parse(raw);

				dt.id_jenjang = dts.id_jenjang;
				dt.id_mapel = dts.id_mapel;
				dt.akses_kelas = dts.akses_kelas;

				$('#frm_mode').val(dts.mode);
				$('#us_jenjang').val(dts.mode);
				$('#us_jabatan2').val(dts.jenis_user);
				$('#us_kode2').val(dts.id);
				$('#us_nama2').val(dts.nama_us);
				$('#us_user2').val(dts.username);
				$('#us_pass2').val(dts.password);
				$('#us_alamat2').val(dts.alamat);
				$('#us_kelas').val(dts.id_kelas_perwalian);
				$('#us_no_kartu').val(dts.rfid);
				$('#us_mapel').val(dts.id_mapel);
				$('#us_ampu').val(dts.akses_kelas);
				$('#us_kode_alat').val(dts.kode_alat);

				if (dts.mode == "1") {
					dts.mode = "Standar";
					$("#walikelas").hide();
					$("#absen").hide();
					$("#jurnalguru").hide();
					$("#kantin").hide();
				}
				else if (dts.mode == "2") {
					dts.mode = "Wali Kelas";
					$("#walikelas").show();
					$("#absen").hide();
					$("#jurnalguru").hide();
					$("#kantin").hide();
				}
				else if (dts.mode == "3") {
					dts.mode = "Absen";
					$("#walikelas").hide();
					$("#absen").show();
					$("#jurnalguru").hide();
					$("#kantin").hide();
				}
				else if (dts.mode == "4") {
					dts.mode = "Jurnal Guru";
					$("#walikelas").hide();
					$("#absen").hide();
					$("#kantin").hide();

					if (dt.id_mapel !== null) {
						$("#jurnalguru").show();

						ajx=$.post(controller+'get_data_mata_pelajaran',dt,function(raw)
						{
							$('#div_mata_pelajaran').html(raw);
							const select = new Choices("#us_mapel", {
					            removeItems: true,
					            removeItemButton: true,
					            maxItemCount: 5,
					        });
						});
					}
					else{
						$("#jurnalguru").hide();
					}
				}
				else if (dts.mode == "5") {
					dts.mode = "Kantin";
					$("#walikelas").hide();
					$("#absen").hide();
					$("#jurnalguru").hide();
					$("#kantin").show();
				}
				else if (dts.mode == "6") {
					dts.mode = "Absen / Wali Kelas";
					$("#walikelas").show();
					$("#absen").show();
					$("#jurnalguru").hide();
					$("#kantin").hide();

					ajx=$.post(controller+'get_data_kelas',dt,function(raw)
					{
						$('#div_kelas_ampu').html(raw);
						const select = new Choices("#us_ampu", {
				            removeItems: true,
				            removeItemButton: true,
				            maxItemCount: 5,
				        });
					});
				}
				else if (dts.mode == "7") {
					dts.mode = "Absen / Wali Kelas / Jurnal Guru";
					$("#walikelas").show();
					$("#absen").show();
					$("#kantin").hide();

					if (dt.id_mapel !== null) {
						$("#jurnalguru").show();

						ajx=$.post(controller+'get_data_mata_pelajaran',dt,function(raw)
						{
							$('#div_mata_pelajaran').html(raw);
							const select = new Choices("#us_mapel", {
					            removeItems: true,
					            removeItemButton: true,
					            maxItemCount: 5,
					        });
						});
					}
					else{
						$("#jurnalguru").hide();
					}
					ajx=$.post(controller+'get_data_kelas',dt,function(raw)
					{
						$('#div_kelas_ampu').html(raw);
						const select = new Choices("#us_ampu", {
				            removeItems: true,
				            removeItemButton: true,
				            maxItemCount: 5,
				        });
					});
				}
				else{

				}
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_user span').html(title+' User');
	$('#ket_modal_user').html(title);
	$('#modal_user').modal('show');
}

function detail_user(id_user=''){
	dt={};
	dt.id_user=id_user;
	ajx=$.post(controller+'get_data_user',dt,function(raw)
	{
		if(isJson(raw))
		{
			dt = JSON.parse(raw);
			if (dt.mode == "1") {
				dt.mode = "Standar";
				$("#detail_walikelas").hide();
				$("#detail_absen").hide();
				$("#detail_jurnalguru").hide();
				$("#detail_kantin").hide();
			}
			else if (dt.mode == "2") {
				dt.mode = "Wali Kelas";
				$("#detail_walikelas").show();
				$("#detail_absen").hide();
				$("#detail_jurnalguru").hide();
				$("#detail_kantin").hide();
			}
			else if (dt.mode == "3") {
				dt.mode = "Absen";
				$("#detail_walikelas").hide();
				$("#detail_absen").show();
				$("#detail_jurnalguru").hide();
				$("#detail_kantin").hide();
			}
			else if (dt.mode == "4") {
				dt.mode = "Jurnal Guru";
				$("#detail_walikelas").hide();
				$("#detail_absen").hide();
				$("#detail_jurnalguru").show();
				$("#detail_kantin").hide();
			}
			else if (dt.mode == "5") {
				dt.mode = "Kantin";
				$("#detail_walikelas").hide();
				$("#detail_absen").hide();
				$("#detail_jurnalguru").hide();
				$("#detail_kantin").show();
			}
			else if (dt.mode == "6") {
				dt.mode = "Absen / Wali Kelas";
				$("#detail_walikelas").show();
				$("#detail_absen").show();
				$("#detail_jurnalguru").hide();
				$("#detail_kantin").hide();
			}
			else if (dt.mode == "7") {
				dt.mode = "Absen / Wali Kelas / Jurnal Guru";
				$("#detail_walikelas").show();
				$("#detail_absen").show();
				$("#detail_jurnalguru").show();
				$("#detail_kantin").hide();
			}
			else{

			}
			$('#res_mode').val(dt.mode);
			$('#res_kode').val(dt.id);
			$('#res_nama').val(dt.nama_us);
			$('#res_username').val(dt.username);
			$('#res_password').val(dt.password);
			$('#res_alamat').val(dt.alamat);
			$('#res_kelas').val(dt.id_kelas_perwalian);
			$('#res_kartu').val(dt.rfid);
			$('#res_mapel').val(dt.id_mapel);
			$('#res_ampu').val(dt.akses_kelas);
			$('#res_kode_alat').val(dt.kode_alat);
		}
	});
	setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	$('#modal_detail_user').modal('show');
}

function mode_user(){
	mode = $('#frm_mode').val();
	jenis = $('#us_jabatan2').val();	
	if (jenis == 2 && mode == 2 || mode == 6) {
		$("#walikelas").show();
		$("#absen").hide();
	}
	else if (mode == 3) {
		$("#walikelas").hide();
		$("#absen").show();
	}
	else if (mode == 4 && jenis == 2) {
		$("#walikelas").hide();
		$("#absen").hide();
		$("#jurnalguru").show();
	}
	else if (mode == 5 && jenis == 10) {
		$("#walikelas").hide();
		$("#absen").hide();
		$("#jurnalguru").hide();
		$("#kantin").show();
	}
	else if (mode == 6) {
		$("#walikelas").show();
		$("#absen").show();
		$("#jurnalguru").hide();
	}
	else if (mode == 7) {
		$("#walikelas").show();
		$("#absen").show();
		$("#jurnalguru").show();
	}
	else{
		$("#walikelas").hide();
		$("#absen").hide();
		$("#jurnalguru").hide();
	}
}

function ubah(){
	mode       = $('#frm_mode').val();
	jenis      = $('#us_jabatan2').val();
	id_jenjang = $('#us_jenjang').val();
	if (jenis == 2 && mode == 2 || mode == 6) {
		dt={};
		dt.id_jenjang = id_jenjang;
		ajx=$.post(controller+'tampil_kelas',dt,function(raw)
		{
			$('#us_kelas').html(raw);
			$("#walikelas").show();
		});
		if (mode == 6) {
			$("#absen").show();
		}
		else{
			$("#absen").hide();
		}
	}
	else if (mode == 3) {
		$("#walikelas").hide();
		$("#absen").show();
	}
	else if (jenis == 2 && mode == 4 || mode == 7) {
		dt={};
		dt.id_jenjang = id_jenjang;
		ajx=$.post(controller+'get_data_mata_pelajaran',dt,function(raw)
		{
			$('#div_mata_pelajaran').html(raw);
			const select = new Choices("#us_mapel", {
	            removeItems: true,
	            removeItemButton: true,
	            maxItemCount: 5,
	        });
		});
		ajx=$.post(controller+'get_data_kelas',dt,function(raw)
		{
			$('#div_kelas_ampu').html(raw);
			const select = new Choices("#us_ampu", {
	            removeItems: true,
	            removeItemButton: true,
	            maxItemCount: 5,
	        });
		});
		$("#walikelas").hide();
		$("#absen").hide();
		$("#jurnalguru").show();
	}
	else if (jenis == 10 && mode == 5) {
		$("#walikelas").hide();
		$("#absen").hide();
		$("#jurnalguru").hide();
		$("#kantin").show();
	}
	else if (mode == 6) {
		$("#walikelas").show();
		$("#absen").show();
		$("#jurnalguru").hide();
		$("#kantin").hide();
	}
	else{
		$("#walikelas").hide();
		$("#absen").hide();
		$("#jurnalguru").hide();
		$("#kantin").hide();
	}
}

function save_user()
{
	dt={};
	dt.glob_mode=glob_mode;
	dt.id_user=glob_id_user;
	dt.mode 		=$('#frm_mode').val();
	dt.id_jenjang 	=$('#us_jenjang').val();
	dt.jenis 		=$('#us_jabatan2').val();
	dt.kode 		=$('#us_kode2').val();
	dt.nama 		=$('#us_nama2').val();
	dt.username 	=$('#us_user2').val();
	dt.password 	=$('#us_pass2').val();
	dt.alamat 		=$('#us_alamat2').val();
	dt.kelas 		=$('#us_kelas').val();
	dt.no_kartu 	=$('#us_no_kartu').val();
	dt.id_mapel 	=$('#us_mapel').val();
	dt.akses_kelas 	=$('#us_ampu').val();
	dt.kode_alat 	=$('#us_kode_alat').val();
	if(dt.mode=='' || dt.jenis_user=='' || dt.kode=='' || dt.nama=='' || dt.username=='' || dt.password=='')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Data User?', function(){
		$.post(controller+'save_user',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' Data User');
					load_data();
					$('#modal_user').modal('hide');
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

function del_user(id_informasi)
{
	dt={};
	dt.id_informasi=glob_id_informasi;
	alertify.confirm('Konfirmasi','Hapus Informasi?', function(){
		$.post(controller+'del_user',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Informasi Berhasil Dihapus');
					load_data();
					$('#modal_user').modal('hide');
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

function cari_menu()
{
	txt = $('#cari_menu').val();
	cari=cariable('#div_data_menu',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.menu_no_result').show(); 
	}
	else
	{
		$('.menu_no_result').hide();
	}
}

function aktif(id_menu_app='')
{
	dt={};
	dt.id_menu_app=id_menu_app;
	if(id_menu_app!='') 
	{
		alertify.confirm('Konfirmasi','Aktifkan menu ini?', function(){
			ajx=$.post(controller+"aktif", dt, function(raw)
			{
				load_data();
			});
			load_ajx(ajx,'#div_data_menu');
		},
		function(){});
	}
	else
	{
		ajx=$.post(controller+"aktif",function(raw)
		{
			load_data();
		});
		load_ajx(ajx,'#div_data_menu');
	}
}

function nonaktif(id_menu_app='')
{
	dt={};
	dt.id_menu_app=id_menu_app;
	if(id_menu_app!='') 
	{
		alertify.confirm('Konfirmasi','Non-aktifkan menu ini?', function(){
			ajx=$.post(controller+"nonaktif", dt, function(raw)
			{
				load_data();
			});
			load_ajx(ajx,'#div_data_menu');
		},
		function(){});
	}
	else
	{
		ajx=$.post(controller+"nonaktif",function(raw)
		{
			load_data();
		});
		load_ajx(ajx,'#div_data_menu');
	}
}

function modal_menu(event,mode=1, id_menu_app=0)
{
	event.stopPropagation();
	glob_mode=mode;
	glob_id_menu_app=id_menu_app;
	$('#modal_menu input').val('');
	$('#modal_menu select').val('0');
	if(mode==1)
	{
		title='Tambah';
		$('#btn_del_menu').hide();
	}
	else 
	{
		$('#btn_del_menu').show();
		dt={};
		dt.id_menu_app=id_menu_app;
		title='Edit';
		ajx=$.post(controller+'get_data_menu',dt,function(raw)
		{
			if(isJson(raw))
			{
				dt = JSON.parse(raw);
				$('#frm_nama_menu').val(dt.nama_menu);
				$('#frm_urutan').val(dt.urutan);
				$('#frm_tipe').val(dt.tipe);
				$('#frm_aktif').val(dt.aktif);
				//$('#frm_gambar').val(dt.new_icon);
			}
		});
		setTimeout(function(){load_ajx(ajx,'#modal_body1')},225);
	}
	$('#btn_save_menu span').html(title+' Menu');
	$('#ket_modal_menu').html(title);
	$('#modal_menu').modal('show');
}

function save_menu()
{
	dt={}
	dt.mode=glob_mode;

	var data = new FormData();
    data.append("gambar", document.getElementById('frm_icon').files[0]);
	data.append("nama_menu", $('#frm_nama_menu').val());
	data.append("urutan", $('#frm_urutan').val());
	data.append("tipe", $('#frm_tipe').val());
	data.append("aktif", $('#frm_aktif').val());
	data.append("mode", glob_mode);
	data.append("id_menu_app", glob_id_menu_app);

	if (data.get('nama_menu') == '' || data.get('urutan') == '' || data.get('tipe') == '' || data.get('aktif') == '') {
        alertify.error('Data Belum Lengkap');
		return;
    }
    mode_ket=['','Tambah','Edit'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' Menu?', function(){
		ajx=$.ajax({
            url: controller+'save_menu',
            type: 'POST',
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            success: function (raw) {
              	if(isJson(raw))
				{
					rs=JSON.parse(raw);
					if(rs.stat == 1)
					{
						alertify.success(mode_ket[dt.mode]+' Menu berhasil');
						load_data();
						$('#modal_menu').modal('hide');
					}
					else
					{
						alertify.alert('Alert',rs.ket);
						load_data();
					}
				}
				else
				{
					alertify.alert('Alert', raw);
				}
            }, error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
            }
        });
        load_ajx(ajx,'#col-userkeu');
	},
	function(){});	
}

function del_menu(id_menu_app)
{
	dt={};
	dt.id_menu_app=glob_id_menu_app;
	alertify.confirm('Konfirmasi','Hapus Menu?', function(){
		$.post(controller+'del_menu',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Menu Berhasil Dihapus');
					load_data();
					$('#modal_menu').modal('hide');
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


function cari_akses()
{
	txt = $('#cari_akses').val();
	cari=cariable('#div_data_akses',txt);
	if(cari.stat=='no_result' || cari.count==0)
	{
		$('.akses_no_result').show(); 
	}
	else
	{
		$('.akses_no_result').hide();
	}
}

function modal_akses(event,mode=1)
{
	event.stopPropagation();
	glob_mode=mode;
	$('#modal_akses input').val('');
	$('#modal_akses select').val('0');
	title='Tambah';
	$('#btn_save_akses span').html(title+' User Akses');
	$('#ket_modal_akses').html(title);
	$('#modal_akses').modal('show');
}

function ubah_pilihan(){
	pilihan = $('#frm_user_pilihan').val();
	if (pilihan == 1) {
		$("#menu_default").show();
    	$("#menu_custom").hide();
	}
	else if (pilihan == 2) {
		$("#menu_default").hide();
    	$("#menu_custom").show();
	}
	else{
		$("#menu_default").hide();
    	$("#menu_custom").hide();
	}
}

function save_akses()
{
	dt={}
	dt.mode=glob_mode;
	dt.id_user=$('#frm_user').val();
	dt.pilihan=$('#frm_user_pilihan').val();
	dt.id_menu=$('#frm_menu_user').val();
	if(dt.id_user=='0' || dt.id_menu=='0' || dt.pilihan=='0')
	{
		alertify.error('Data Belum Lengkap');
		return;
	}
	mode_ket=['','Tambah'];
	alertify.confirm('Konfirmasi', mode_ket[dt.mode]+' User Akses?', function(){
		$.post(controller+'save_akses',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(mode_ket[dt.mode]+' User Akses');
					load_data();
					$('#modal_akses').modal('hide');
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

function del_akses(id)
{
	dt={};
	dt.id=id;
	alertify.confirm('Konfirmasi','Hapus Akses?', function(){
		$.post(controller+'del_akses',dt,function(raw)
		{
			if(isJson(raw))
			{
				rs=JSON.parse(raw);
				if(rs.stat == 1)
				{
					alertify.success(' Akses Berhasil Dihapus');
					load_data();
					$('#modal_akses').modal('hide');
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