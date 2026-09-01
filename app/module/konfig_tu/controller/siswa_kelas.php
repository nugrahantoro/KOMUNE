<?php

class Siswa_Kelas extends Controller
{

	public function __construct()
	{
		$this->client_id       = BNI_CLIENT_ID;
		$this->secret_key      = BNI_SECRET_KEY;
		$this->urltarget       = BNI_URL_TARGET;
		$this->TIME_DIFF_LIMIT = BNI_DIFF_LIMIT;
	}

	function load_list_tingkat()
	{
		$pt = $_POST;
		$data = [];
		$options = '';
		for ($i = 1; $i < 7; $i++) {
			$options .= "<option value='$i'>$i</option>";
		}
		$data['select_tingkat'] = "<option value='all'>Semua</option>$options";

		$options = '';
		$sql = "SELECT
					data_kelas.* 
				FROM
					data_kelas 
				WHERE
					id_jenjang = $pt[id_jenjang]
					AND id_sesi = $pt[id_ta]
				ORDER BY 
					nama_kelas";
		$res = db_query($sql);
		while ($dt = mysqli_fetch_array($res)) {
			$options .= "<option value='$dt[id_kelas]'>$dt[nama_kelas]</option>";
		}
		$data['select_kelas'] = "<option value='all'>Semua</option>$options";
		$data['form_kelas'] = "<option value='0'>-pilih-</option>$options";

		$next_ta = db_assoc("SELECT
								*
							FROM
								data_tahunajar 
							WHERE
								tgl_mulai > ( SELECT tgl_mulai FROM data_tahunajar WHERE id_tahunajar = $pt[id_ta] ) 
							ORDER BY
								tgl_mulai 
								LIMIT 1");
		if (!is_null($next_ta)) {
			$sql = "SELECT
						* 
					FROM
						data_kelas 
					WHERE
						id_jenjang = $pt[id_jenjang] AND
						id_sesi = $next_ta[id_tahunajar]
					ORDER BY nama_kelas";
			$res = db_query($sql);
			$options = '';
			while ($dt = mysqli_fetch_array($res)) {
				$options .= "<option value='$dt[id_kelas]'>$dt[nama_kelas]</option>";
			}
			$data['form_kelas_next'] = "<option value='0'>-pilih-</option>$options";
			$data['id_ta_next'] = "<option value='$next_ta[id_tahunajar]'>$next_ta[nama_tahunajar]</option>";
		} else {
			$data['form_kelas_next'] = "";
			$data['id_ta_next'] = "";
		}
		echo json_encode($data);
	}

	function load_kelas()
	{
		$pt = $_POST;
		$data = '';
		$level = '';
		if ($level == "all") {
			$level = '';;
		}
		$sql = "SELECT
					data_kelas.* ,
					SUM(IF(id_penempatan IS NULL,0,1)) AS jumlah_siswa
				FROM
					data_kelas 
					LEFT JOIN penempatan_siswa ON data_kelas.id_kelas = penempatan_siswa.id_kelas 
				WHERE
					id_jenjang = $pt[id_jenjang]
					$level
					AND id_sesi = $pt[id_ta]
				GROUP BY
					data_kelas.id_kelas
				ORDER BY 
					nama_kelas";
		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$no++;
			$tbody .= "
				<tr class='cari-item kelas-item' id_kelas='$dt[id_kelas]' onclick='click_kelas(this);'>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_kelas]</td>
					<td class='tc'>$dt[jumlah_siswa]</td>
					<td  class='tc'>
						<button class='btn btn-sm btn-dark' onclick='modal_kelas(event,2,$dt[id_kelas]);'>
							<i class='bx bx-edit'></i> 
						Edit</button>
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc kelas_no_result'  style='$no_res'>
					<td colspan='4'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama Wilayah</th>
					<th>Jumlah Warga</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
	}

	function get_data_kelas()
	{
		$pt = $_POST;
		$data = db_assoc("SELECT * FROM data_kelas WHERE id_kelas = $pt[id_kelas]");
		echo json_encode($data);
	}

	function save_kelas()
	{
		$dt = $_POST;
		$cb['stat'] = 0;
		$cb['ket'] = '';
		$dup = db_get("SELECT COUNT(*) FROM data_kelas WHERE 
						nama_kelas='$dt[nama_kelas]'  
						AND id_sesi=$dt[id_ta]
						AND id_jenjang=$dt[id_jenjang]
						AND id_kelas!=$dt[id_kelas]");
		if ($dup > 0) {
			$cb['ket'] = "Nama Kelas sudah terdaftar, gunakan nama lain";
		} else {
			if ($dt['mode'] == 1) {
				$sql = "
					INSERT INTO 
					data_kelas(
							nama_kelas,
							level,
							id_sesi,
							id_jenjang
						) 
					VALUES (
						'$dt[nama_kelas]',
						'1',
						$dt[id_ta],
						$dt[id_jenjang]
					)";
			} else {
				$sql = "
						UPDATE  
							data_kelas
						SET 
							nama_kelas='$dt[nama_kelas]',
							level = '1'
						WHERE
							id_kelas='$dt[id_kelas]'
						";
			}
		}
		if (isset($sql)) {
			$res = db_query($sql);
			if ($res) {
				$cb['stat'] = 1;
			} else {
				$cb['ket'] = 'Error';
			}
		}
		echo json_encode($cb);
	}

	function del_kelas()
	{
		$id_kelas = $_POST['id_kelas'];
		$cb['stat'] = 0;
		$cb['ket'] = '';
		$c_trans = db_get("SELECT COUNT(*) FROM penempatan_siswa WHERE id_kelas=$id_kelas");
		if ($c_trans < 1) {
			$sql = "DELETE FROM data_kelas WHERE id_kelas = $id_kelas";
		} else {
			$cb['ket'] = 'Tidak bisa dihapus! Kelas sudah terisi siswa';
		}
		if (isset($sql)) {
			$res = db_query($sql);
			if ($res) {
				$cb['stat'] = 1;
			} else {
				$cb['ket'] = 'Error';
			}
		}
		echo json_encode($cb);
	}

	function del_siswa()
	{
		global $con;
		$cb = [];
		$cb['stat'] = 0;
		$cb['ket'] = '';
		$id = $_POST['id_siswa'];
		$qerd = "SELECT
			nis_from_idsiswa,
			master_alokasi_transaksi_all.id_master_trans_sis 
		FROM
			master_alokasi_transaksi_all
			INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi 
		WHERE nis_from_idsiswa='$id' AND 
		NOT EXISTS (SELECT nis_from_idsiswa FROM master_alokasi_transaksi_all WHERE nis_from_idsiswa='$id' AND master_jenis_transaksi.nama_transaksi='Uang Saku')";
		$sqldel = mysqli_query($con, $qerd);
		$count = mysqli_num_rows($sqldel);
		$cek = "SELECT
			nis_dari_siswa, non_aktif
		FROM
			data_va_uangsaku
		WHERE nis_dari_siswa='$id' AND non_aktif IS NULL";
		$sqlcek = mysqli_query($con, $cek);
		$counts = mysqli_num_rows($sqlcek);
		if ($count > 0) {
			$cb['ket'] = 'Maaf, tidak bisa di hapus, sudah ada penetapan pembayaran!';
		} else if ($counts > 0) {
			$cb['ket'] = 'Maaf, tidak bisa di hapus, VA masih aktif';
		} else if ($count > 0 and $counts > 0) {
			$cb['ket'] = 'VA masih aktif dan masih ada kewajiban pembayaran';
		} else if ($count < 1 and $counts < 1) {
			$sql = mysqli_query($con, "UPDATE data_siswa SET is_deleted = 1, deleted_at = NOW() WHERE id_siswa=$id");
			$sql3 = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET is_deleted = 1, deleted_at = NOW() WHERE nis_from_idsiswa=$id");
			audit_log('SOFT_DELETE', 'warga', $id, "Soft delete warga ID $id");
			if ($sql) {
				$cb['stat'] = 1;
			} else {
				$cb['ket'] = "Gagal dihapus!";
			}
		} else {
			$cb['ket'] = 'Silahkan cek ulang atif dan tidak VA dan kewajiban pembayaran';
		}
		echo json_encode($cb);
	}

	function load_list_siswa()
	{

		////LIST DATA SISWA
		$pt = $_POST;
		$data = '';
		$json = [];
		$kelas = "AND data_kelas.id_kelas = $pt[id_kelas]";
		if ($pt['id_kelas'] == "all") {
			$kelas = '';;
		}
		$sql = "SELECT
					data_siswa.id_siswa,
					data_siswa.nama_siswa,
					data_siswa.gender,
					nama_kelas
				FROM
					data_siswa
					LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN  data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas
				WHERE
					data_kelas.id_jenjang = $pt[id_jenjang]
					$kelas
					AND data_kelas.id_sesi = $pt[id_ta]
				ORDER BY 
					nama_kelas, nama_siswa";
		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$no++;
			$tbody .= "
				<tr class='cari-item siswa-item pg_siswa' id_item = '$dt[id_siswa]' id='tr_$dt[id_siswa]'>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td>" . toLP($dt['gender']) . "</td>
					<td>$dt[nama_kelas]</td>
					<td>-</td>
					<td  class='tc' style='white-space:nowrap;'>
						<button class='btn btn-sm btn-dark'  onclick='modal_siswa(2,$dt[id_siswa]);'>
							<i class='bx bx-edit'></i> 
						Edit</button>
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc siswa_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama Warga</th>
					<th>Gender</th>
					<th>Wilayah</th>
					<th>Nomor VA</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		$json['data_siswa'] = $table;

		////LIST VA SISWA

		$query = "SELECT
					data_siswa.id_siswa,
					data_siswa.nama_siswa,
					data_siswa.gender,
					data_siswa.pin,
					nama_kelas,
					`user`.id_user,
					`user`.username,
					`user`.password,
					IF(no_va IS NOT NULL,1,0) as va_aktif,
					data_va_uangsaku.no_va,
					data_va_uangsaku.created_at,
					data_va_uangsaku.payment_id,
					data_va_uangsaku.opsional,
					id_master_trans_sis,
					data_va_uangsaku.id_order,
					YEAR(data_va_uangsaku.expired) as exp,
					DATE(data_va_uangsaku.expired) as expired
				FROM
					data_siswa
					LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas
					LEFT JOIN `user` ON `user`.nis_from_idsiswa = penempatan_siswa.nis_from_idsiswa
					LEFT JOIN ( SELECT id_master_trans_sis, nis_from_idsiswa FROM master_alokasi_transaksi_all INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi AND nama_transaksi = 'Uang Saku' ) AS tb_saku ON data_siswa.id_siswa = tb_saku.nis_from_idsiswa
					LEFT JOIN data_va_uangsaku ON `user`.id_user = data_va_uangsaku.id_user AND  non_aktif IS NULL
					LEFT JOIN idorder ON idorder.id_order = data_va_uangsaku.id_order
				WHERE
					
					data_kelas.id_jenjang = $pt[id_jenjang]
					$kelas
					AND data_kelas.id_sesi = $pt[id_ta]
				ORDER BY 
					nama_kelas, nama_siswa";
		//echo $query;
		//$json['data_siswa_va']=$query; echo json_encode($json); die;
		$res = db_query($query);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$td_akun = "<span style='white-space: nowrap;'><b style='color: gray;'>user : </b> $dt[username] <span><br>
					<span style='white-space: nowrap;'><b style='color: gray;'>pass : </b> $dt[password] </span><br>
					<span style='white-space: nowrap;'><b style='color: gray;'>no. VA : 
						
						";
			$td_va = '- </b></span>';
			$btn_act = "
				<button class='btn btn-sm btn-primary' onclick='aktif_va($dt[id_siswa]);'><i class=' bx bx-check-circle '></i>  Aktifkan</button> <br><br>
				<button class='btn btn-sm btn-light' onclick='aktif_va_bmi($dt[id_siswa]);'><i class=' bx bx-check-circle '></i>  Aktifkan BMI</button> <br><br>
			";
			if ($dt['va_aktif'] == 1) {
				$pin_stat = '';
				if ($dt['pin'] != '') {
					$pin_stat = '-check';
				}
				$td_va = " $dt[no_va] </b></span> <br>
						<b style='color: gray;'>exp. : </b> $dt[expired]";
				if ($dt['payment_id'] == "" && $dt['opsional'] == "") {
					$btn_act = "
						<button class='btn btn-sm btn-dark mb-1'
							onclick='nonaktif_va($dt[id_order])'>
							<i class='bx bxs-x-circle'></i>  Nonaktif</button>
					";
				} elseif ($dt['opsional'] == "BSI") {
					$btn_act = "
						<button class='btn btn-sm btn-dark mb-1'
							onclick='nonaktif_va_bsi($dt[id_order])'>
							<i class='bx bxs-x-circle'></i>  Nonaktif BSI</button>
					";
				} elseif ($dt['opsional'] == "Mandiri") {
					$btn_act = "
						<button class='btn btn-sm btn-dark mb-1'
							onclick='nonaktif_va_mandiri($dt[id_order])'>
							<i class='bx bxs-x-circle'></i>  Nonaktif Mandiri</button>
					";
				} elseif ($dt['opsional'] == "BMI") {
					$btn_act = "
						<button class='btn btn-sm btn-dark mb-1'
							onclick='nonaktif_va_bmi($dt[id_order])'>
							<i class='bx bxs-x-circle'></i>  Nonaktif BMI</button>
					";
				} elseif ($dt['opsional'] == "BPD JATENG") {
					$btn_act = "
						<button class='btn btn-sm btn-dark mb-1'
							onclick='nonaktif_va_bpdjateng($dt[id_order])'>
							<i class='bx bxs-x-circle'></i>  Nonaktif BPD JATENG</button>
					";
				} elseif ($dt['opsional'] == "BRI") {
					$btn_act = "
						<button class='btn btn-sm btn-dark mb-1'
							onclick='nonaktif_va_bri($dt[id_order])'>
							<i class='bx bxs-x-circle'></i>  Nonaktif BRI</button>
					";
				} else {
					$btn_act = "
						<button class='btn btn-sm btn-dark mb-1'
							onclick='nonaktif_va_cring($dt[id_order])'>
							<i class='bx bxs-x-circle'></i>  Nonaktif Cring</button>
					";
				}
				// <button class='btn btn-sm btn-primary mb-1' onclick='cetak_va($dt[id_siswa]);'>
				// 			<i class='bx bx-printer '></i>  Cetak
				// 	</button> 
				$btn_act .= "
					<button class='btn btn-sm btn-info mb-1' onclick='kirim_notif($dt[id_siswa],$dt[no_va]);'>
					  Kirim Notif
					</button>
				";
			}
			$no++;
			$tbody .= "
				<tr class='cari-item siswa-item' id_siswa='$dt[id_siswa]'>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td>$dt[nama_kelas]</td>
					<td>$td_akun $td_va</td>
					<td  class='tc' style='vertical-align:middle;'>
						$btn_act
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc siswa_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama Warga</th>
					<th>Wilayah</th>
					<th>Akun</th>
					<th></th>
				</tr>
			</thead> 
			<tbody>
				$tbody
			</tbody>
			</table>";

		$json['data_siswa_va'] = $table;


		///LIST NAIK Kelas
		$sql = "SELECT
				id_penempatan,
				id_siswa,
				nama_siswa,
				gender,
				thn_ajar,
				nama_kelas,
				penempatan_siswa.id_kelas,
				nis,
				sta
			FROM
				penempatan_siswa
				INNER JOIN data_siswa ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa
				INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas 
			WHERE
				data_kelas.id_jenjang = $pt[id_jenjang]
				AND data_kelas.id_sesi = $pt[id_ta]
				$kelas
			ORDER BY 
			nama_kelas, nama_siswa ASC";

		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$btn = '<button type="button" class="btn btn-danger btn-sm" onclick="naik_siswa_batal(' . $dt['id_penempatan'] . ');">Batal</button>';
			$tr_style = 'table-success';
			if ($dt['sta'] == 'bn') {
				$btn = '<button type="button" onclick="naik_siswa(' . $dt['id_siswa'] . ');" class="btn btn-primary btn-sm">Naikkan</button>';
				$tr_style = '';
			}
			$no++;
			$tbody .= "
				<tr class='cari-item siswa-item $tr_style' >
					<td class='tc'>
						<input type='checkbox' class='chk_item' id_siswa='$dt[id_siswa]' sta='$dt[sta]' id_penempatan='$dt[id_penempatan]' nama_siswa='$dt[nama_siswa]'>
					</td>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td>" . toLP($dt['gender']) . "</td>
					<td class='tc'>$dt[nama_kelas]</td>
					<td  class='tc'>
						<span class='act_item'>$btn</span>
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc siswa_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>
						<input type='checkbox'>
					</th>
					<th>No.</th>
					<th>Nama Siswa</th>
					<th>L/P</th>
					<th>Kelas</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>		
		";

		$json['data_naik_kelas'] = $table;


		///LIST PINDAH KELAS
		$sql = "SELECT
				id_penempatan,
				id_siswa,
				nama_siswa,
				gender,
				thn_ajar,
				nama_kelas,
				penempatan_siswa.id_kelas,
				nis,
				sta
			FROM
				penempatan_siswa
				INNER JOIN data_siswa ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa
				INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas 
			WHERE
				data_kelas.id_jenjang = $pt[id_jenjang]
				AND data_kelas.id_sesi = $pt[id_ta]
				$kelas
			ORDER BY 
			nama_kelas, nama_siswa ASC";

		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$btn = '';
			$tr_style = 'table-success';
			$chk = '';
			if ($dt['sta'] == 'bn') {
				$btn = '<button type="button" onclick="pindah_siswa(' . $dt['id_siswa'] . ');" class="btn btn-primary btn-sm">Pindah</button>';
				$tr_style = '';
				$chk = "<input type='checkbox' class='chk_item' id_siswa='$dt[id_siswa]' sta='$dt[sta]' id_penempatan='$dt[id_penempatan]' >";
			}
			$no++;
			$tbody .= "
				<tr class='cari-item siswa-item $tr_style' >
					<td class='tc'>
						$chk
					</td>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td>" . toLP($dt['gender']) . "</td>
					<td class='tc'>$dt[nama_kelas]</td>
					<td  class='tc'>
						<span class='act_item'>$btn</span>
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc siswa_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>
						<input type='checkbox'>
					</th>
					<th>No.</th>
					<th>Nama Siswa</th>
					<th>L/P</th>
					<th>Kelas</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>		
		";

		$json['data_pindah_kelas'] = $table;

		///LIST SOUND NAMA
		$sql = "SELECT
				id_penempatan,
				id_siswa,
				nama_siswa,
				gender,
				nama_kelas,
				penempatan_siswa.id_kelas,
				nis,
				sound_name
			FROM
				penempatan_siswa
				INNER JOIN data_siswa ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa
				INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas 
			WHERE
				data_kelas.id_jenjang = $pt[id_jenjang]
				AND data_kelas.id_sesi = $pt[id_ta]
				$kelas
			ORDER BY 
			nama_kelas, nama_siswa ASC";

		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$stat = '<span class="badge rounded-pill bg-success">
						<i class="bx bxs-check-circle"></i>
					</span>';
			$btn = '
					<div class="btn-group">
						<button type="button" onclick="play_sound_nama(' . "'" . $dt['sound_name'] . "'" . ');" class="btn btn-primary btn-sm">
							<i class=" bx bx-play-circle "></i>
							Play
						</button>
						<button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="mdi mdi-chevron-down"></i>
						</button>
						<div class="dropdown-menu" style="">
							<a class="dropdown-item" href="#" onclick="event.preventDefault();dialog_sound_nama(' . $dt['id_siswa'] . ')">
								<i class="bx bx-upload"></i> 
								Upload
							</a>
						</div
					</div>';
			if ($dt['sound_name'] == null) {
				$btn = '<button type="button" onclick="dialog_sound_nama(' . $dt['id_siswa'] . ');" class="btn btn-primary btn-sm">
						<i class="bx bx-upload"></i>
						Upload</button>';
				$stat = '-';
				//$stat = "";
			}
			$no++;
			$tbody .= "
				<tr class='cari-item siswa-item ' >
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td>" . toLP($dt['gender']) . "</td>
					<td class='tc'>$dt[nama_kelas]</td>
					<td class='tc'>$stat</td>
					<td  class='tc'>
						<span class='act_item'>$btn</span>
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc siswa_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama Siswa</th>
					<th>L/P</th>
					<th>Kelas</th>
					<th>Sound</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>		
		";

		$json['data_sound_nama'] = $table;

		///LIST NO TELP CARD
		$sql = "SELECT
				id_penempatan,
				id_siswa,
				nama_siswa,
				gender,
				nama_kelas,
				penempatan_siswa.id_kelas,
				nis,
				no_kartu,
				telp_one
			FROM
				penempatan_siswa
				INNER JOIN data_siswa ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa
				INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas 
			WHERE
				data_kelas.id_jenjang = $pt[id_jenjang]
				AND data_kelas.id_sesi = $pt[id_ta]
				$kelas
			ORDER BY 
			nama_kelas, nama_siswa ASC";

		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$no++;
			$tbody .= "
				<tr class='cari-item siswa-item ' >
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td>" . toLP($dt['gender']) . "</td>
					<td class='tc'>$dt[nama_kelas]</td>
					<td class='tc no_kartu'>$dt[no_kartu]</td>
					<td class='tc telp_one'>$dt[telp_one]</td>
					<td class='tc td_last'>" . '
						<span class="act_item">
							<button type="button" onclick="save_card_telp(' . $dt['id_siswa'] . ');" class="btn btn-primary btn-sm" style="white-space:nowrap">
								<i class=" bx bx-save "></i> 
								Simpan
							</button>' . "
						</span>
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc siswa_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama Warga</th>
					<th>Gender</th>
					<th>Wilayah</th>
					<th>Nomor Kartu</th>
					<th>WhatsApp</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>		
		";

		$json['data_card_telp'] = $table;

		echo json_encode($json);
	}

	function upload_excel_siswa()
	{
		$key = 'excel';
		$tmp_name = $_FILES[$key]["tmp_name"];
		$name = $_FILES[$key]["name"];
		$name_ar = explode(".", $name);
		$ext = end($name_ar);
		array_pop($name_ar);
		$file_name = 'excel_tmp.' . $ext;
		$file_name = str_replace(" ", "_", $file_name);
		$path = 'app/module/konfig_tu/controller/' . $file_name;
		move_uploaded_file($tmp_name, $path);
		echo 'ok';
	}

	function upload_sound_nama()
	{
		$id_siswa = $_POST['id_siswa'];
		$path = '/home/smartbilling/www-dev/panggilan-siswa/files/';
		$cur_file = db_get("SELECT sound_name FROM data_siswa WHERE id_siswa = $id_siswa");
		if (!is_null($cur_file) || $cur_file != '') {
			unlink($path . $cur_file);
		}
		$key = 'sound';
		$tmp_name = $_FILES[$key]["tmp_name"];
		$name = $_FILES[$key]["name"];
		$name_ar = explode(".", $name);
		$ext = end($name_ar);
		array_pop($name_ar);
		$file_name = "siswa-$id_siswa-" . uniqid() . ".$ext";
		$path = $path . $file_name;
		$moved = move_uploaded_file($tmp_name, $path);
		if ($moved) {
			$sql = "UPDATE data_siswa SET sound_name = '$file_name' WHERE id_siswa = $id_siswa";
			db_query($sql);
			echo 'ok';
		} else {
			echo "Not uploaded because of error #" . $_FILES["file"]["error"];
		}
	}

	function load_import_siswa()
	{
		$pt = $_POST;
		$data = [];
		$lib_dir = "app/lib/";
		require_once $lib_dir . 'PHPExcel/PHPExcel.php';
		require_once $lib_dir . 'PHPExcel/PHPExcel/IOFactory.php';

		$file = 'app/module/konfig_tu/controller/excel_tmp.xlsx';
		$load = PHPExcel_IOFactory::load($file);
		$sheets = $load->getActiveSheet()->toArray(null, true, true, true);
		$tbody = '';
		$no = 1;
		$sqlkls = "SELECT
					id_kelas,
					nama_kelas 
				FROM
					data_kelas
				WHERE 
					id_sesi = $pt[id_ta]
					AND id_jenjang = $pt[id_jenjang]
				ORDER BY
					nama_kelas";
		$kelas = [];
		$res = db_query($sqlkls);
		while ($dt = mysqli_fetch_assoc($res)) {
			array_push($kelas, $dt);
		}
		foreach ($sheets as $dt) {
			$line = key($sheets);
			next($sheets);
			if ($line < 2) {
				continue;
			}
			if (trim($dt['B']) == '' || trim($dt['C']) == '' || trim($dt['D']) == '') {
				continue;
			}
			$option = "";
			for ($i = 0; $i < count($kelas); $i++) {
				$selected = '';
				$nm_kls = trim($kelas[$i]['nama_kelas']);
				$nm_imp = trim($dt['F']);
				$nm_kls = str_replace(' ', '', $nm_kls);
				$nm_imp = str_replace(' ', '', $nm_imp);
				$nm_kls = strtoupper($nm_kls);
				$nm_imp = strtoupper($nm_imp);
				if ($nm_kls == $nm_imp) {
					$selected = 'selected';
				}
				$option .= "<option value = '" . $kelas[$i]['id_kelas'] . "' $selected>" . $kelas[$i]['nama_kelas'] . "</option>";
			}
			$option = "<select class='import_id_kelas_$no form-select form-select-sm'>
							<option value=0>-pilih-</option>
							$option
						</select>";
			$tbody .= "<tr>
						<td class='tc'>
							<input type='checkbox' checked class=' chk_item' onclick='import_hitung_dipilih();'
							no=$no 
							nama_siswa='" . $dt['B'] . "' 
							lp='" . $dt['C'] . "' 
							tlahir='" . $dt['D'] . "' 
							no_wa='" . $dt['E'] . "' 
							>
						</td>
						<td>" . $no++ . "</td>
						<td>" . $dt['B'] . "</td>
						<td>" . $dt['C'] . "</td>
						<td>" . $dt['D'] . "</td>
						<td>" . $dt['E'] . "</td>
						<td>" . $option . "</td>
					</tr>";
		}
		$table = "<table class='table table-sm m-0' >
			<thead>
				<tr>
					<th>
						<input type='checkbox' id='import_chk_all' checked onclick='import_pilih_semua();'>
					</th>
					<th>No.</th>
					<th>Nama Siswa</th>
					<th>L/P</th>
					<th>Tgl. Lahir</th>
					<th>Nomor WA</th>
					<th>Kelas</th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		$data['data_import'] = $table;
		echo json_encode($data);
	}

	function import_siswa()
	{
		$pt = $_POST;
		$id_ta = $pt['id_ta'];
		$id_jenjang = $pt['id_jenjang'];
		$siswa = $pt['siswa'];
		foreach ($siswa as $dt) {
			$value = "(
						'$dt[nama_siswa]',
						'$dt[lp]',
						'$dt[tlahir]',
						'$id_ta',
						'cs',
						'$id_jenjang',
						'$dt[no_wa]'
						)";
			$sql = "INSERT INTO data_siswa (
						nama_siswa,
						gender,
						tanggal_lahir,
						id_sesi,
						status,
						id_jenjang,
						telp_one
					)
					VALUES
					$value
					";

			$res = db_query($sql);
			$id_siswa = db_id();

			//ADD Akun
			$dt['fld_nama_siswa'] = $dt['nama_siswa'];
			$dt['no_telp'] = $dt['no_wa'];
			$this->add_akun_siswa($id_siswa, $dt);

			if ($dt['id_kelas'] != 0) {
				$sql = "INSERT INTO penempatan_siswa (
							nis_from_idsiswa,
							id_kelas,
							thn_ajar,
							sta
						)
						VALUES
						(
							$id_siswa,
							'$dt[id_kelas]',
							'$id_ta',
							'bn'
						)
						";
				$res = db_query($sql);
			}
		}
		$cb['stat'] = 0;
		$cb['ket'] = '';
		if ($res) {
			$cb['stat'] = 1;
		} else {
			$cb['ket'] = 'Error';
		}
		echo json_encode($cb);
	}

	function import_sample()
	{
		$filename = "data_siswa_sample.xlsx";
		$file = "app/module/konfig_tu/controller/$filename";
		header('Content-Disposition: attachment; filename=' . $filename);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Length: ' . filesize($file));
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();
		readfile($file);
	}

	function save_siswa()
	{
		global $database;

		$response = [
			'stat' => 0,
			'ket'  => ''
		];

		$post = $_POST;

		$id_siswa = intval($post['id_siswa'] ?? 0);
		$mode     = intval($post['mode'] ?? 1);

		$data_siswa = [];

		// =========================================
		// PARSING FIELD
		// =========================================
		foreach ($post as $key => $value) {

			if (substr($key, 0, 4) !== 'fld_') {
				continue;
			}

			$field = substr($key, 4);
			$value = trim($value);

			// FORMAT DATE
			if (in_array($field, ['tanggal_lahir', 'tgl_diterima'])) {

				$value = ($value != '')
					? "'" . db_dtindo($value) . "'"
					: "NULL";

			} else {

				$value = "'" . db_escape_string($value) . "'";
			}

			$data_siswa[] = [
				'field' => $field,
				'value' => $value
			];
		}

		// =========================================
		// BUILD QUERY FIELD
		// =========================================
		$fields = [];
		$values = [];
		$updates = [];

		foreach ($data_siswa as $item) {

			$fields[]  = $item['field'];
			$values[]  = $item['value'];
			$updates[] = "{$item['field']} = {$item['value']}";
		}

		// =========================================
		// MODE TAMBAH
		// =========================================
		if ($mode == 1) {

			$fields[] = 'status';
			$values[] = "'s'";

			$insert = "
				INSERT INTO data_siswa (
					" . implode(', ', $fields) . "
				) VALUES (
					" . implode(', ', $values) . "
				)
			";

			$res = db_query($insert);

			if (!$res) {

				$response['ket'] = 'Gagal menyimpan data warga';
				echo json_encode($response);
				return;
			}

			$id_siswa = db_id();

			// =========================================
			// INSERT PENEMPATAN WILAYAH
			// =========================================
			$id_kelas = db_escape_string($post['fld_id_kelass']);
			$id_ta    = db_escape_string($post['id_ta']);

			$sql_penempatan = "
				INSERT INTO penempatan_siswa (
					nis_from_idsiswa,
					id_kelas,
					thn_ajar,
					sta
				) VALUES (
					'$id_siswa',
					'$id_kelas',
					'$id_ta',
					'bn'
				)
			";

			db_query($sql_penempatan);

			// =========================================
			// AUTO CREATE ACCOUNT
			// =========================================
			$this->add_akun_siswa($id_siswa, $post);

		}

		// =========================================
		// MODE EDIT
		// =========================================
		else {

			$update = "
				UPDATE data_siswa
				SET " . implode(', ', $updates) . "
				WHERE id_siswa = '$id_siswa'
			";

			$res = db_query($update);

			if (!$res) {

				$response['ket'] = 'Gagal memperbarui data warga';
				echo json_encode($response);
				return;
			}

			// =========================================
			// UPDATE USER
			// =========================================
			$full_name = "'" . db_escape_string($post['fld_nama_siswa']) . "'";
			$telp_one  = "'" . db_escape_string($post['fld_telp_one']) . "'";
			$gender    = db_escape_string($post['fld_gender']);

			$update_user = "
				UPDATE user
				SET
					full_name = $full_name,
					hp = $telp_one,
					gender = '$gender'
				WHERE nis_from_idsiswa = '$id_siswa'
			";

			db_query($update_user);

			// =========================================
			// UPDATE DATA WHATSAPP
			// =========================================
			$check_wa = db_get("
				SELECT COUNT(*)
				FROM billing_core.all_data_whatsapp
				WHERE db = '$database'
				AND id_siswa = '$id_siswa'
			");

			if ($check_wa > 0) {

				$sql_wa = "
					UPDATE billing_core.all_data_whatsapp
					SET no_wa = $telp_one
					WHERE db = '$database'
					AND id_siswa = '$id_siswa'
				";

			} else {

				$sql_wa = "
					INSERT INTO billing_core.all_data_whatsapp (
						no_wa,
						db,
						id_siswa
					) VALUES (
						$telp_one,
						'$database',
						'$id_siswa'
					)
				";
			}

			db_query($sql_wa);
		}

		// =========================================
		// UPLOAD FOTO
		// =========================================
		if (isset($_FILES['foto']) && $_FILES['foto']['name'] != '') {

			// DELETE OLD PHOTO
			$old_photo = db_get("
				SELECT foto
				FROM data_siswa
				WHERE id_siswa = '$id_siswa'
			");

			if ($old_photo) {

				$old_path = 'images/foto_siswa/' . $old_photo;

				if (file_exists($old_path)) {
					unlink($old_path);
				}
			}

			// SAVE NEW PHOTO
			$image_name = $this->save_image('foto');

			$sql_photo = "
				UPDATE data_siswa
				SET foto = '$image_name'
				WHERE id_siswa = '$id_siswa'
			";

			db_query($sql_photo);
		}

		// =========================================
		// SUCCESS
		// =========================================
		$response['stat'] = 1;
		$response['ket']  = 'Data warga berhasil disimpan';

		echo json_encode($response);
	}

	function add_akun_siswa($id_siswa, $data)
	{
		global $database;
		$id_jenis_saku = db_get("SELECT id_jenis_transaksi FROM master_jenis_transaksi WHERE nama_transaksi='Uang Saku'");
		//ADD UANG SAKU
		$sql = "INSERT INTO master_alokasi_transaksi_all(
							nis_from_idsiswa,
							id_jenis_transaksi,
							nominal)
						VALUES(
							'$id_siswa',
							'$id_jenis_saku',
							'0')";
		//USER PASSWORD
		$res = db_query($sql);
		$pass = PASS_SEKOLAHKU;
		$user = $id_siswa . $pass;
		$sql = "INSERT INTO user(
							nis_from_idsiswa,
							username,
							password,
							full_name,
							hp)
						VALUES(
							'$id_siswa',
							'$user',
							'$user',
							'$data[fld_nama_siswa]',
							'$data[fld_telp_one]')";
		$res = db_query($sql);

		///ADD NO TELP TO ALL WA DATA TABLE
		if($data['fld_telp_one']!='')
				{
					 $sql = "INSERT INTO billing_core.all_data_whatsapp(
								no_wa,
								db,
								id_siswa)
							VALUES(
								'$data[fld_telp_one]',
								'$database',
								$id_siswa)";
					$res = db_query($sql);
				}
	}

	function get_data_siswa()
	{
		$id_siswa = intval($_POST['id_siswa']);
		$id_ta    = intval($_POST['id_ta']);

		// =========================================
		// GET DATA WARGA
		// =========================================
		$query = "
			SELECT
				data_siswa.*,
				penempatan_siswa.id_kelas
			FROM data_siswa

			LEFT JOIN penempatan_siswa
				ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa

			WHERE
				data_siswa.id_siswa = '$id_siswa'
				AND penempatan_siswa.thn_ajar = '$id_ta'

			LIMIT 1
		";

		$data = db_assoc($query);

		// =========================================
		// VALIDASI DATA
		// =========================================
		if (!$data) {

			echo json_encode([
				'stat' => 0,
				'ket'  => 'Data warga tidak ditemukan'
			]);

			return;
		}

		// =========================================
		// FORMAT TANGGAL
		// =========================================
		if (!empty($data['tanggal_lahir'])) {
			$data['tanggal_lahir'] = db_dtindo($data['tanggal_lahir']);
		}

		// =========================================
		// FORMAT GENDER
		// =========================================
		if ($data['gender'] == 'L') {
			$data['gender_text'] = 'Laki-Laki';
		} else if ($data['gender'] == 'P') {
			$data['gender_text'] = 'Perempuan';
		} else {
			$data['gender_text'] = '-';
		}

		// =========================================
		// RESPONSE
		// =========================================
		echo json_encode([
			'stat' => 1,
			'data' => $data
		], JSON_PRETTY_PRINT);
	}

	function naik_siswa()
	{
		$pt = $_POST;
		if (trim($pt['id_kelas_next']) == '') {
			echo "Tahun Ajaran tujuan tidak valid";
			die;
		}
		$id_siswa = $_POST['id_siswa'];
		$que = "SELECT * FROM penempatan_siswa WHERE nis_from_idsiswa IN ($id_siswa) AND sta = 'bn'";
		$res = db_query($que);
		$values = [];
		$id_old = [];
		while ($dt = mysqli_fetch_array($res)) {
			$val = "(
						'$dt[nis_from_idsiswa]',
						'$pt[id_kelas_next]',
						'$pt[id_ta_next]',
						'bn',
						'$dt[nis_from_idsiswa]',
						'$dt[id_kelas]',
						'$dt[thn_ajar]',
						'bn',
						'$dt[id_penempatan]'
					)";
			array_push($values, $val);
			array_push($id_old, $dt['id_penempatan']);
		}
		$qins = "INSERT INTO penempatan_siswa ( 
						nis_from_idsiswa, 
						id_kelas, 
						thn_ajar, 
						sta, 
						nis, 
						id_kel, 
						th_aj, 
						st, 
						id
						)
				VALUES
					" . implode(", ", $values);
		$inp = db_query($qins);
		if ($inp) {
			db_query("UPDATE penempatan_siswa SET sta = 'n' WHERE id_penempatan IN (" . implode(', ', $id_old) . ")");
		}
	}

	function naik_siswa_batal()
	{
		$pt = $_POST;
		$id_penempatan = $_POST['id_penempatan'];
		$que = "SELECT * FROM penempatan_siswa WHERE id IN ($id_penempatan)";
		$res = db_query($que);
		$id_new = [];
		$id_old = [];
		$id_siswa = [];
		while ($dt = mysqli_fetch_array($res)) {
			array_push($id_siswa, $dt['nis_from_idsiswa']);
			array_push($id_new, $dt['id_penempatan']);
			array_push($id_old, $dt['id']);
		}
		$trn = db_get("SELECT COUNT(*) FROM master_alokasi_transaksi_all 
						WHERE 
							nis_from_idsiswa IN (" . implode(', ', $id_new) . ")
							AND id_sesi = " . $pt['id_ta_next']);


		if ($trn > 0) {
			echo "Sudah ada ketetapan transaksi!";
		} else {
			db_query("DELETE FROM penempatan_siswa WHERE id_penempatan IN (" . implode(', ', $id_new) . ")");
			db_query("UPDATE penempatan_siswa SET sta = 'bn' WHERE id_penempatan IN (" . implode(', ', $id_old) . ")");
		}
	}

	function pindah_siswa()
	{
		$pt = $_POST;
		$id_siswa = $_POST['id_siswa'];
		$id_kelas_move = $_POST['id_kelas_move'];
		$id_ta = $_POST['id_ta'];
		//$que = "SELECT * FROM penempatan_siswa WHERE nis_from_idsiswa IN ($id_siswa) AND sta = 'bn'";
		//$res = db_query($que);
		db_query("UPDATE master_alokasi_transaksi_all 
					SET id_kelas='$id_kelas_move' 
					WHERE nis_from_idsiswa IN ($id_siswa) AND id_sesi='$id_ta'");
		db_query("UPDATE transaksi_siswa 
					SET id_kelas='$id_kelas_move' 
					WHERE nis_from_idsiswa IN ($id_siswa) AND thn_aj='$id_ta'");
		db_query("UPDATE penempatan_siswa 
					SET id_kelas='$id_kelas_move' 
					WHERE nis_from_idsiswa IN ($id_siswa)
						AND thn_ajar = $id_ta");
	}

	function save_card_telp()
	{
		$pt = $_POST;
		$field = ['telp_one', 'no_kartu'];
		if (in_array($pt['mode'], $field)) {
			$que = "UPDATE data_siswa SET $pt[mode] = '$pt[val]' WHERE id_siswa = $pt[id_siswa]";
			db_query($que);
			if ($pt['mode'] == 'telp_one') {
				$que = "UPDATE user SET hp = '$pt[val]' WHERE nis_from_idsiswa = $pt[id_siswa]";
				db_query($que);
			}
		}
	}

	function pin_siswa_save()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();
		$pt = $_POST;
		$sesi = $_SESSION['id'];
		$sql = "UPDATE data_siswa SET pin = '$pt[pin]' WHERE id_siswa = $pt[id_siswa]";
		db_query($sql);

		//Notif WA
		$_GET['id_siswa'] = $pt['id_siswa'];
		$TU_old->wappin_notif_byr_pin();

		if ($pt['ket'] != '') {
			$sql_ket = "INSERT INTO keterangan_pin (nis_from_idsiswa, pin, ket, id_user) VALUES 
				('$pt[id_siswa]', '$pt[pin]', '$pt[ket]', '$sesi')";
			db_query($sql_ket);

			$_GET['id_siswa'] = $pt['id_siswa'];
			$TU_old->wappin_notif_byr_pin($pt['id_siswa']);
		}
	}

	function admin()
	{
		global $con;
		$q = "
			SELECT * FROM biaya_admin WHERE aktif = '1'
		";
		//$cek = $this->db->query($q);
		$cek = mysqli_query($con, $q);
		$j   = mysqli_num_rows($cek);
		if ($j > 0) {
			while ($ar = mysqli_fetch_object($cek)) {
				return $ar->jasa;
			}
		}
	}

	function save_image($key)
	{
		$tmp_name = $_FILES[$key]["tmp_name"];
		$name = $_FILES[$key]["name"];
		$name_ar = explode(".", $name);
		$ext = end($name_ar);
		array_pop($name_ar);
		//$image_name = implode('.',$name_ar).'-'.(uniqid()).'.'.$ext;
		$image_name = uniqid() . '.' . $ext;
		$path = 'images/foto_siswa/' . $image_name;
		//$this->thumb_image($image_name,170);
		move_uploaded_file($tmp_name, $path);
		return $image_name;
	}

	function cetak_va($id_siswa)
	{
		$sql = "SELECT
					data_siswa.nama_siswa,
					data_siswa.gender,
					`user`.username,
					`user`.password,
					data_va_uangsaku.no_va 
				FROM
					data_siswa
					LEFT JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN data_va_uangsaku ON `user`.id_user = data_va_uangsaku.id_user
					LEFT JOIN idorder ON idorder.id_order = data_va_uangsaku.id_order 
					AND non_aktif IS NULL 
				WHERE
					data_siswa.id_siswa = $id_siswa 
					LIMIT 1";
		$dt = db_assoc($sql);
		$html = '
				<h4 style="text-align:center">PEMBAYARAN SEKOLAH ONLINE</h4>
				</br>
				</br>
				</br>
				<br>
				<b>Informasi Akun Android Siswa</b>
				<br>
				<table>
					<tr>
						<td width="10%">Nama</td>
						<td width="2%">:</td>
						<td>' . $dt['nama_siswa'] . '</td>
					</tr>
					<tr>
						<td>Username</td>
						<td>:</td>
						<td>' . $dt['username'] . '</td>
					</tr>
					<tr>
						<td>Password</td>
						<td>:</td>
						<td>' . $dt['password'] . '</td>
					</tr>
				</table>
				<br>
				<h4>CARA PEMBAYARAN SEKOLAH DENGAN HP ANDROID</h4>
				<ul>
				  <li>Instal aplikasi pembayaran sekolah online di PLAYSTORE dengan mencari key "' . SCHOOL_APP . '".</li>
				  <li>Pilih Sekolah-App.</li>
				  <li>Masukan username dan password diatas.</li>
				  <li>Lakukan pembayaran dengan pilih menu bayar sesuai dengan keinginan.</li>
				  <li>Pilih metode bayar BNI Virtual Account.</li>
				  <li>Pilih bayar sekarang.</li>
				  <li>Muncul nomor rekening tujuan pembayaran.</li>
				  <li>Lakukan pembayaran ke nomor rekening tujuan sesuai dengan nominal total amount yang tertera di HP Android.</li>
				</ul>
				<br>
				<h4>CARA PEMBAYARAN TRANSFER LANGSUNG (Tanpa Aplikasi Android)</h4>
				<ul>
				  <li>Lakukan transfer atas nama putra/putri anda ke nomor rekening <b>BNI ' . $dt['no_va'] . '</b>.</li>
				  <li>Lebihkan nominal transfer anda senilai <b>Rp 3.500</b> untuk biaya admin transaksi online agar uang yang anda kirimkan tidak terpotong.</li>
				</ul>
				<p>Pembayaran bisa dilakukan dari bank BNI, BRI, BCA, MANDIRI, MUAMALAT, Bank BPD Jateng, Bank Jatim melalui ATM, teller, M-Banking, internet banking dan uang elektronik Link Aja.</p>';
		//echo $html;die;
		$lib_dir = "app/lib/";
		require_once($lib_dir . 'tcpdf/tcpdf.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setCellHeightRatio(1.2);
		$pdf->SetFont('freeserif', '', 10, '', true);
		$pdf->SetMargins(20, 10, 20);
		$pdf->SetFooterMargin(0);
		$pdf->SetAutoPageBreak(TRUE, 3);
		$pdf->AddPage('P', 'A4');
		$text = str_replace("'", '"', $html);
		$pdf->SetXY(10, 10);
		$pdf->Image('app/lib/header_surat.png', '', '', 180, 33, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$pdf->Ln(4);
		$style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$stylet = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 44, 197, 44, $style);
		$pdf->Line(10, 45, 197, 45, $stylet);
		$pdf->Ln(40);
		$pdf->writeHTML($text);
		$pdf->Output($dt['nama_siswa'] . '.pdf', 'I');
	}

	function kirim_notif()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();
		$nis = $_GET['nis'];
		$id_ta = $_GET['id_ta'];
		$id_jenjang = $_GET['id_jenjang'];
		$va = $_GET['va'];
		$TU_old->wappin_notif_akun($nis, $va);
	}

	/* BNI VA Egref */
	function aktif_va()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();
		$id_siswa = $_POST['id_siswa'];
		$id_ta = $_POST['id_ta'];
		$id_jenjang = $_POST['id_jenjang'];
		$data = "SELECT
					`user`.nis_from_idsiswa AS id_siswa,
					id_master_trans_sis,
					master_jenis_transaksi.id_jenis_transaksi,
					nama_transaksi,
					id_user,
					id_kelas,
					id_transaksi,
					disc,
					nominal,
					(nominal - IF(tot_dibayar IS NULL, 0, tot_dibayar)) as tot_kekurangan
				FROM
					data_siswa
					INNER JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
					INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi
					INNER JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN (	SELECT
								id_transaksi,
								nis_from_idsiswa,
								SUM( ts.nominal_dibayarkan ) AS tot_dibayar 
							FROM
								`transaksi_siswa` ts
								JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi 
							WHERE
								nis_from_idsiswa = $id_siswa 
							GROUP BY
								mj.id_jenis_transaksi
					) as tb_trans ON data_siswa.id_siswa = tb_trans.nis_from_idsiswa
				WHERE
					nama_transaksi = 'UANG SAKU'
					AND `user`.nis_from_idsiswa = $id_siswa
					AND sta IS NULL";
		$dt = db_assoc($data);
		$chk_mobile = db_get("
                        SELECT 
                        COUNT(*) 
                        FROM 
								transaksi_siswa_mobile
							WHERE
                            nis_from_idsiswa = '$dt[id_siswa]' AND
                            id_master_trans_sis = '$dt[id_master_trans_sis]' AND
                            id_jenis_transaksi = '$dt[id_jenis_transaksi]'
                    ");
		if ($chk_mobile == 0) {
			$order = db_query("
							INSERT INTO idorder(
                                object,
                                id_user)
                             VALUES (
                                1,
                                '$dt[id_user]'
                            )
                        ");
			$ins = "
					INSERT INTO 
                        transaksi_siswa_mobile (
                        nis_from_idsiswa,
                        id_master_trans_sis,
                        id_kelas,
                        id_jenis_transaksi,
                        nominal_ketetapan,
                        disc,
                        nominal_dibayarkan,
                        id_user) 
                    VALUES 
					(
						 NULLIF('" . $dt['id_siswa'] . "', ''),
                            NULLIF('" . $dt['id_master_trans_sis'] . "', ''),
                            NULLIF('" . $dt['id_kelas'] . "', ''),
                            NULLIF('" . $dt['id_jenis_transaksi'] . "', ''),
                            NULLIF('" . $dt['nominal'] . "', ''),
                            NULLIF('" . $dt['disc'] . "', ''),
                            NULLIF('" . $dt['tot_kekurangan'] . "', ''),
                            NULLIF('" . $dt['id_user'] . "', '')
					)
                        ";
			$res = db_query($ins);
			if ($res) {
				$id_sekolah = str_pad(KODE_SEKOLAH, 2, "0", STR_PAD_LEFT);
				$trx_length = 7 - strlen($id_sekolah);
				$trx_id = str_pad($id_siswa, $trx_length, "0", STR_PAD_LEFT);
				echo $no_va = BNI_VA . '1' . $trx_id;
				echo ' - ';
				//echo $no_va=BNI_VA.'1'.str_pad($id_siswa, 5, "0", STR_PAD_LEFT);
				$exp = db_get("SELECT
									6-(YEAR(NOW()) - YEAR ( tgl_mulai )) AS exp 
								FROM
									data_tahunajar 
								WHERE
									id_tahunajar = $id_ta");
				$TU_old->checkout_global($id_siswa, $dt['id_user'], $no_va, $exp);

				//Notif WA
				$_GET['nis'] = $id_siswa;
				$_GET['va'] = $no_va;
				$TU_old->wappin_notif_akun();
			}
		}
	}
	/* BNI VA Egref */

	/* CRING VA CREDIT */
	function get_token()
	{
		$path = "auth/token";
		$url  = "https://api-core-cring.spesandbox.com/" . $path;

		$client_id = '55bb928b-aced-47d7-9a81-8c7bf8aae262';
		$client_secret = '5e82p5DLrUry2JUK8KU0uGw8IfmYddVZ5lRpmeezONI=';

		$data = json_encode(array("grantType" => "client_credentials"));

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data)
		));


		curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $client_secret);

		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			echo 'cURL error: ' . curl_error($ch);
		}

		curl_close($ch);

		$response = json_decode($result);
		$responseCode = $response->responseCode;
		$responseMessage = $response->responseMessage;
		$accessToken = "";

		if ($responseCode == "20000") {
			$accessToken = $response->accessToken;
		}

		return $accessToken;
	}
	function aktif_va_cring()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();

		$id_siswa = $_POST['id_siswa'];
		$id_ta = $_POST['id_ta'];
		$id_jenjang = $_POST['id_jenjang'];

		$sql_jenjang = "
			SELECT nama_jenjang FROM data_jenjang WHERE id_jenjang = $id_jenjang
		";
		$res_jenjang = db_query($sql_jenjang);
		$tambahan_thn = 0;
		while ($dt_jenjang = mysqli_fetch_array($res_jenjang)) {
			if ($dt_jenjang['nama_jenjang'] == "KB" || $dt_jenjang['nama_jenjang'] == "TK" || $dt_jenjang['nama_jenjang'] == "KB/TK") {
				$tambahan_thn += 1;
			} elseif ($dt_jenjang['nama_jenjang'] == "SD" || $dt_jenjang['nama_jenjang'] == "sd") {
				$tambahan_thn += 6;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} else {
				$tambahan_thn += 3;
			}
		}

		$sql_siswa = "
			SELECT nama_siswa FROM data_siswa WHERE id_siswa = $id_siswa
		";
		$res_siswa = db_query($sql_siswa);
		$nama_siswa = "";
		while ($dt_siswa = mysqli_fetch_array($res_siswa)) {
			$nama_siswa = $dt_siswa['nama_siswa'];
		}

		$numberSuffix = ""; // harus 8 karakter // 2 atau 3 digit awal adalah kode sekolah 
		if (strlen(KODE_SEKOLAH) == 2) {
			if (strlen($id_siswa) == 1) {
				$numberSuffix = KODE_SEKOLAH . "00000" . $id_siswa;
			} elseif (strlen($id_siswa) == 2) {
				$numberSuffix = KODE_SEKOLAH . "0000" . $id_siswa;
			} elseif (strlen($id_siswa) == 3) {
				$numberSuffix = KODE_SEKOLAH . "000" . $id_siswa;
			} elseif (strlen($id_siswa) == 4) {
				$numberSuffix = KODE_SEKOLAH . "00" . $id_siswa;
			} elseif (strlen($id_siswa) == 5) {
				$numberSuffix = KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = KODE_SEKOLAH . $id_siswa;
			}
		} else {
			if (strlen($id_siswa) == 1) {
				$numberSuffix = KODE_SEKOLAH . "0000" . $id_siswa;
			} elseif (strlen($id_siswa) == 2) {
				$numberSuffix = KODE_SEKOLAH . "000" . $id_siswa;
			} elseif (strlen($id_siswa) == 3) {
				$numberSuffix = KODE_SEKOLAH . "00" . $id_siswa;
			} elseif (strlen($id_siswa) == 4) {
				$numberSuffix = KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = KODE_SEKOLAH . $id_siswa;
			}
		}

		$th = date("Y") + $tambahan_thn;
		$bl = date("m");
		$hr = date("d");
		$jam = date("H:i:s");

		$expiredDate = $th . "-" . $bl . "-" . $hr . "T" . $jam . "+07:00";
		$request =  array(
			'expiredDate'	=> $expiredDate,
			'amount'		=> 0,
			'paymentType'	=> "va-open-payment",
			'customerName'	=> $nama_siswa,
			'customerPhone'	=> "08112550513",
			'customerEmail'	=> "etn.egref@gmail.com",
			'accountType'	=> "credit",
			'numberSuffix'	=> $numberSuffix,
			'additionalInfo' => array(
				'id_siswa'	=> $id_siswa,
				'sekolah'	=> base64_encode(ABS),
			)
		);
		$payload = json_encode($request);

		$path = "payment";
		$url  = "https://api-core-cring.spesandbox.com/" . $path;

		$token = $this->get_token();
		if ($token == "") {
			$result_json = array(
				'status'  => "03",
				'message' => "Token Expired"
			);
		} else {
			$sql = "
                SELECT
                    `user`.nis_from_idsiswa AS id_siswa,
                    id_master_trans_sis,
                    master_jenis_transaksi.id_jenis_transaksi,
                    nama_transaksi,
                    id_user,
                    id_kelas,
                    id_transaksi,
                    disc,
                    nominal,
                    (nominal - IF(tot_dibayar IS NULL, 0, tot_dibayar)) as tot_kekurangan
                FROM
                    data_siswa
                    INNER JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
                    INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi
                    INNER JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
                    LEFT JOIN ( SELECT
                                id_transaksi,
                                nis_from_idsiswa,
                                SUM( ts.nominal_dibayarkan ) AS tot_dibayar 
                            FROM
                                `transaksi_siswa` ts
                                JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi 
                            WHERE
                                nis_from_idsiswa = $id_siswa 
                            GROUP BY
                                mj.id_jenis_transaksi
                    ) as tb_trans ON data_siswa.id_siswa = tb_trans.nis_from_idsiswa
                WHERE
                    nama_transaksi = 'UANG SAKU'
                    AND `user`.nis_from_idsiswa = $id_siswa
                    AND sta IS NULL
            ";
			$res = db_query($sql);
			$dt = db_assoc($sql);
			$j = mysqli_num_rows($res);
			$id_user = "";
			$id_order = "";
			$trx_id = "";
			if ($j > 0) {
				$id_user = $dt['id_user'];
				$chk_mobile = db_get("
                    SELECT 
                    COUNT(*) 
                    FROM 
							transaksi_siswa_mobile
						WHERE
                        nis_from_idsiswa = '$dt[id_siswa]' AND
                        id_master_trans_sis = '$dt[id_master_trans_sis]' AND
                        id_jenis_transaksi = '$dt[id_jenis_transaksi]'
                ");
				if ($chk_mobile == 0) {
					$order = db_query("
						INSERT INTO idorder(
                            object,
                            id_user)
                         VALUES (
                            1,
                            '$dt[id_user]'
                        )
                    ");
					$id_order = db_id();

					$ins = "
						INSERT INTO transaksi_siswa_mobile 
						(
	                        nis_from_idsiswa,
                            id_master_trans_sis,
                            id_kelas,
                            id_jenis_transaksi,
                            nominal_ketetapan,
                            disc,
                            nominal_dibayarkan,
                            id_user,
                            siap_bayar,
                            id_order
	                    ) 
	                    VALUES 
						(
							NULLIF('" . $dt['id_siswa'] . "', ''),
                            NULLIF('" . $dt['id_master_trans_sis'] . "', ''),
                            NULLIF('" . $dt['id_kelas'] . "', ''),
                            NULLIF('" . $dt['id_jenis_transaksi'] . "', ''),
                            NULLIF('" . $dt['nominal'] . "', ''),
                            NULLIF('" . $dt['disc'] . "', ''),
                            NULLIF('" . $dt['tot_kekurangan'] . "', ''),
                            NULLIF('" . $dt['id_user'] . "', ''),
                            NULLIF('1', ''),
                            NULLIF('" . $id_order . "', '')
						)
	                ";
					$res = db_query($ins);
					if ($res) {
						$ch = curl_init($url);

						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
						curl_setopt($ch, CURLOPT_HTTPHEADER, array(
							'Content-Type: application/json',
							'Authorization: Bearer ' . $token
						));

						curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

						$result = curl_exec($ch);

						if (curl_errno($ch)) {
							echo 'cURL error: ' . curl_error($ch);
						}

						curl_close($ch);

						$result_json = json_decode($result);
						$responseCode = $result_json->responseCode;
						$responseMessage = $result_json->responseMessage;

						if ($responseCode == "20000") {
							$paymentId = $result_json->paymentId;
							$virtualAccount = $result_json->virtualAccount;
							$amount = $result_json->amount;
							$nama = $result_json->customerName;
							$expiredDate = $result_json->expiredDate;
							$dbs = ABS;

							db_query_core("
								INSERT INTO master_create_bni_cring ( id_order, id_siswa, request_json, response_json, va, db, payment_id )
                                VALUES ( '$id_order', '$id_siswa', '$payload', '$result', '$virtualAccount', '$dbs', '$paymentId' )
	                        ");
							$id_cring = db_id_core();

							if (strlen($id_order) == "1") {
								$trx_id = KODE_SEKOLAH . "00000" . $id_order;
							} elseif (strlen($id_order) == "2") {
								$trx_id = KODE_SEKOLAH . "0000" . $id_order;
							} elseif (strlen($id_order) == "3") {
								$trx_id = KODE_SEKOLAH . "000" . $id_order;
							} elseif (strlen($id_order) == "4") {
								$trx_id = KODE_SEKOLAH . "00" . $id_order;
							} elseif (strlen($id_order) == "5") {
								$trx_id = KODE_SEKOLAH . "0" . $id_order;
							} else {
								$trx_id = KODE_SEKOLAH . $id_order;
							}

							$order_json = array(
								'client_id'         => "",
								'trx_id'            => $trx_id,
								'trx_amount'        => $amount,
								'billing_type'      => "va-open-payment",
								'datetime_expired'  => $expiredDate,
								'virtual_account'   => $virtualAccount,
								'customer_name'     => $nama,
								'customer_email'    => "etn.egref@gmail.com",
								'customer_phone'    => "082223519811",
								'description'       => "Uang Saku",
								'type'              => "createCring"
							);
							$enc_order = json_encode($order_json);

							$upd_order = db_query("
                                UPDATE idorder
                                SET trx_id = '$trx_id', data_json = '$enc_order'
                                WHERE 
                                    id_order = '$id_order'
                            ");

							$expired = str_replace("T", " ", $expiredDate);
							$expired = substr($expired, 0, 10);
							db_query("
								INSERT INTO data_va_uangsaku ( id_order, id_user, no_va, expired, nis_dari_siswa, payment_id )
                                VALUES ( '$id_order', '$id_user', '$virtualAccount', '$expired', '$id_siswa', '$paymentId' )
	                        ");

							//Notif WA
							$_GET['nis'] = $id_siswa;
							$_GET['va'] = $virtualAccount;
							//$TU_old->wappin_notif_akun();
						}

						$result_json = array(
							'status'    => $responseCode,
							'message'   => $responseMessage
						);
					} else {
						$result_json = array(
							'status'    => "5",
							'message'   => "gagal konfigurasi VA"
						);
					}
				} else {
					db_query("
						DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa
                    ");

					$result_json = array(
						'status'    => "5",
						'message'   => "gagal konfigurasi VA, silahkan coba kembali"
					);
				}
			} else {
				$result_json = array(
					'status'    => "4",
					'message'   => "data jenis transaksi tidak ada"
				);
			}
		}
		echo json_encode($result_json);
	}
	function nonaktif_va_cring()
	{
		$id_order = $_POST['id_order'];

		$path = "payment/update";
		$url  = "https://apicfs.cring.id/" . $path;

		$token = $this->get_token();
		if ($token == "") {
			$result_json = array(
				'status'  => "03",
				'message' => "Token Expired"
			);
		} else {
			$sekolah = ABS;
			$currentYear = date("Y");
			$previousYear = date('Y', strtotime("$currentYear -1 year"));
			$exp = $previousYear . date("-m-d") . "T" . date("H:i:s") . "+07:00";

			$cek_data = "
        		SELECT
					a.data_json,
					b.nis_dari_siswa,
					b.payment_id
				FROM
					idorder a
					JOIN data_va_uangsaku b ON b.id_order = a.id_order
				WHERE
					a.id_order = $id_order
        	";
			$res = db_query($cek_data);
			$j = mysqli_num_rows($res);
			if ($j > 0) {
				while ($dt = mysqli_fetch_array($res)) {
					$id_siswa = $dt['nis_dari_siswa'];
					$data = json_decode($dt['data_json']);
					$nama = $data->customer_name;
					$telp = $data->customer_phone;
					$email = $data->customer_email;

					$request = array(
						'expiredDate'	=> $exp,
						'paymentId'		=> $dt['payment_id'],
						'amount'		=> 0,
						'customerName'	=> $nama,
						'customerPhone'	=> $telp,
						'customerEmail'	=> $email,
						'additionalInfo' => array(
							'id_siswa'	=> $id_siswa,
							'sekolah'	=> base64_encode($sekolah)
						)
					);
					$payload = json_encode($request);

					$ch = curl_init($url);

					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Authorization: Bearer ' . $token
					));

					curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

					$result = curl_exec($ch);

					if (curl_errno($ch)) {
						echo 'cURL error: ' . curl_error($ch);
					}

					curl_close($ch);

					$result_json = json_decode($result);
					$responseCode = $result_json->responseCode;
					$responseMessage = $result_json->responseMessage;

					if ($responseCode == "20000") {
						$paymentId = $result_json->paymentId;
						$virtualAccount = $result_json->virtualAccount;

						$now = date("Y-m-d H:i:s");

						$upd_core = db_query_core("
							UPDATE master_create_bni_cring 
                            SET request_json = '$payload', response_json = '$result', exp ='1', updated_at = '$now'
                            WHERE
                                payment_id = '$dt[payment_id]'
                                AND id_siswa = $id_siswa
                                AND db = '$sekolah'
                        ");

						if ($upd_core) {
							$upd = db_query("
								UPDATE data_va_uangsaku SET non_aktif = '1', reset_in_use = '1' WHERE id_order = $id_order AND payment_id = '$dt[payment_id]'
                        	");
							if ($upd) {
								db_query("
									DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa AND id_order = $id_order
			                    ");
							}
						}
					}

					$result_json = array(
						'status'    => $responseCode,
						'message'   => $responseMessage
					);
				}
			} else {
				$result_json = array(
					'status'    => "4",
					'message'   => "data tidak ditemukan"
				);
			}
		}
		echo json_encode($result_json);
	}
	/* CRING VA CREDIT */

	/* BSI VA */
	function aktif_va_bsi()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();

		$id_siswa = $_POST['id_siswa'];
		$id_ta = $_POST['id_ta'];
		$id_jenjang = $_POST['id_jenjang'];

		$sql_jenjang = "
			SELECT nama_jenjang FROM data_jenjang WHERE id_jenjang = $id_jenjang
		";
		$res_jenjang = db_query($sql_jenjang);
		$tambahan_thn = 0;
		while ($dt_jenjang = mysqli_fetch_array($res_jenjang)) {
			if ($dt_jenjang['nama_jenjang'] == "KB" || $dt_jenjang['nama_jenjang'] == "TK" || $dt_jenjang['nama_jenjang'] == "KB/TK") {
				$tambahan_thn += 1;
			} elseif ($dt_jenjang['nama_jenjang'] == "SD" || $dt_jenjang['nama_jenjang'] == "sd") {
				$tambahan_thn += 6;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} else {
				$tambahan_thn += 3;
			}
		}

		$sql_siswa = "
			SELECT nama_siswa FROM data_siswa WHERE id_siswa = $id_siswa
		";
		$res_siswa = db_query($sql_siswa);
		$nama_siswa = "";
		while ($dt_siswa = mysqli_fetch_array($res_siswa)) {
			$nama_siswa = $dt_siswa['nama_siswa'];
		}

		$numberSuffix = ""; // maksimal 12 karakter // 1 digit awal  tipe va // 2 atau 3 digit berikutnya kode sekolah // space free // id siswa 
		if (strlen(KODE_SEKOLAH) == 2) {
			if (strlen($id_siswa) == 1) {
				$numberSuffix = "1" . KODE_SEKOLAH . "00000000" . $id_siswa;
			} elseif (strlen($id_siswa) == 2) {
				$numberSuffix = "1" . KODE_SEKOLAH . "0000000" . $id_siswa;
			} elseif (strlen($id_siswa) == 3) {
				$numberSuffix = "1" . KODE_SEKOLAH . "000000" . $id_siswa;
			} elseif (strlen($id_siswa) == 4) {
				$numberSuffix = "1" . KODE_SEKOLAH . "00000" . $id_siswa;
			} elseif (strlen($id_siswa) == 5) {
				$numberSuffix = "1" . KODE_SEKOLAH . "0000" . $id_siswa;
			} elseif (strlen($id_siswa) == 6) {
				$numberSuffix = "1" . KODE_SEKOLAH . "000" . $id_siswa;
			} elseif (strlen($id_siswa) == 7) {
				$numberSuffix = "1" . KODE_SEKOLAH . "00" . $id_siswa;
			} elseif (strlen($id_siswa) == 8) {
				$numberSuffix = "1" . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = "1" . KODE_SEKOLAH . $id_siswa;
			}
		} else {
			if (strlen($id_siswa) == 1) {
				$numberSuffix = "1" . KODE_SEKOLAH . "0000000" . $id_siswa;
			} elseif (strlen($id_siswa) == 2) {
				$numberSuffix = "1" . KODE_SEKOLAH . "000000" . $id_siswa;
			} elseif (strlen($id_siswa) == 3) {
				$numberSuffix = "1" . KODE_SEKOLAH . "00000" . $id_siswa;
			} elseif (strlen($id_siswa) == 4) {
				$numberSuffix = "1" . KODE_SEKOLAH . "0000" . $id_siswa;
			} elseif (strlen($id_siswa) == 5) {
				$numberSuffix = "1" . KODE_SEKOLAH . "000" . $id_siswa;
			} elseif (strlen($id_siswa) == 6) {
				$numberSuffix = "1" . KODE_SEKOLAH . "00" . $id_siswa;
			} elseif (strlen($id_siswa) == 7) {
				$numberSuffix = "1" . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = "1" . KODE_SEKOLAH . $id_siswa;
			}
		}

		$th = date("Y") + $tambahan_thn;
		$bl = date("m");
		$hr = date("d");
		$jam = date("H:i:s");

		$expiredDate = $th . "-" . $bl . "-" . $hr . "T" . $jam . "+07:00";

		$sql = "
            SELECT
                `user`.nis_from_idsiswa AS id_siswa,
                id_master_trans_sis,
                master_jenis_transaksi.id_jenis_transaksi,
                nama_transaksi,
                id_user,
                id_kelas,
                id_transaksi,
                disc,
                nominal,
                (nominal - IF(tot_dibayar IS NULL, 0, tot_dibayar)) as tot_kekurangan
            FROM
                data_siswa
                INNER JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
                INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi
                INNER JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
                LEFT JOIN ( SELECT
                            id_transaksi,
                            nis_from_idsiswa,
                            SUM( ts.nominal_dibayarkan ) AS tot_dibayar 
                        FROM
                            `transaksi_siswa` ts
                            JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi 
                        WHERE
                            nis_from_idsiswa = $id_siswa 
                        GROUP BY
                            mj.id_jenis_transaksi
                ) as tb_trans ON data_siswa.id_siswa = tb_trans.nis_from_idsiswa
            WHERE
                nama_transaksi = 'UANG SAKU'
                AND `user`.nis_from_idsiswa = $id_siswa
                AND sta IS NULL
        ";
		$res = db_query($sql);
		$dt = db_assoc($sql);
		$j = mysqli_num_rows($res);
		$id_user = "";
		$id_order = "";
		$trx_id = "";
		if ($j > 0) {
			$id_user = $dt['id_user'];
			$chk_mobile = db_get("
                SELECT 
                COUNT(*) 
                FROM 
						transaksi_siswa_mobile
					WHERE
                    nis_from_idsiswa = '$dt[id_siswa]' AND
                    id_master_trans_sis = '$dt[id_master_trans_sis]' AND
                    id_jenis_transaksi = '$dt[id_jenis_transaksi]'
            ");
			if ($chk_mobile == 0) {
				$order = db_query("
					INSERT INTO idorder(
                        object,
                        id_user)
                     VALUES (
                        1,
                        '$dt[id_user]'
                    )
                ");
				$id_order = db_id();

				$ins = "
					INSERT INTO transaksi_siswa_mobile 
					(
                        nis_from_idsiswa,
                        id_master_trans_sis,
                        id_kelas,
                        id_jenis_transaksi,
                        nominal_ketetapan,
                        disc,
                        nominal_dibayarkan,
                        id_user,
                        siap_bayar,
                        id_order
                    ) 
                    VALUES 
					(
						NULLIF('" . $dt['id_siswa'] . "', ''),
                        NULLIF('" . $dt['id_master_trans_sis'] . "', ''),
                        NULLIF('" . $dt['id_kelas'] . "', ''),
                        NULLIF('" . $dt['id_jenis_transaksi'] . "', ''),
                        NULLIF('" . $dt['nominal'] . "', ''),
                        NULLIF('" . $dt['disc'] . "', ''),
                        NULLIF('" . $dt['tot_kekurangan'] . "', ''),
                        NULLIF('" . $dt['id_user'] . "', ''),
                        NULLIF('1', ''),
                        NULLIF('" . $id_order . "', '')
					)
                ";
				$res = db_query($ins);
				if ($res) {
					$trx_id = $numberSuffix;

					$order_json = array(
						'client_id'         => "",
						'trx_id'            => $trx_id,
						'trx_amount'        => "0",
						'billing_type'      => "va-open-payment",
						'datetime_expired'  => $expiredDate,
						'virtual_account'   => $numberSuffix,
						'customer_name'     => $nama_siswa,
						'customer_email'    => "etn.egref@gmail.com",
						'customer_phone'    => "082223519811",
						'description'       => "Uang Saku",
						'type'              => "createBSI"
					);
					$enc_order = json_encode($order_json);

					$upd_order = db_query("
                            UPDATE idorder
                            SET trx_id = '$trx_id', data_json = '$enc_order'
                            WHERE 
                                id_order = '$id_order'
                        ");

					$expired = str_replace("T", " ", $expiredDate);
					$expired = substr($expired, 0, 10);
					db_query("
							INSERT INTO data_va_uangsaku ( id_order, id_user, no_va, expired, nis_dari_siswa, opsional )
                            VALUES ( '$id_order', '$id_user', '$numberSuffix', '$expired', '$id_siswa', 'BSI' )
                        ");

					//Notif WA
					$_GET['nis'] = $id_siswa;
					$_GET['va'] = $numberSuffix;
					$TU_old->wappin_notif_akun();
					$result_json = array(
						'status'  => "00",
						'message' => "Berhasil"
					);
				} else {
					$result_json = array(
						'status'    => "5",
						'message'   => "gagal konfigurasi VA"
					);
				}
			} else {
				db_query("
					DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa
                ");

				$result_json = array(
					'status'    => "5",
					'message'   => "gagal konfigurasi VA, silahkan coba kembali"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data jenis transaksi tidak ada"
			);
		}

		echo json_encode($result_json);
	}
	function nonaktif_va_bsi()
	{
		$id_order = $_POST['id_order'];

		$sekolah = base64_encode(ABS);

		$cek_data = "
    		SELECT
				a.data_json,
				b.nis_dari_siswa,
				b.payment_id
			FROM
				idorder a
				JOIN data_va_uangsaku b ON b.id_order = a.id_order
			WHERE
				a.id_order = $id_order
    	";
		$res = db_query($cek_data);
		$j = mysqli_num_rows($res);
		if ($j > 0) {
			while ($dt = mysqli_fetch_array($res)) {
				$id_siswa = $dt['nis_dari_siswa'];
				$data = json_decode($dt['data_json']);
				$nama = $data->customer_name;
				$telp = $data->customer_phone;
				$email = $data->customer_email;

				$now = date("Y-m-d H:i:s");

				$upd = db_query("
					UPDATE data_va_uangsaku SET non_aktif = '1' WHERE id_order = $id_order AND opsional = 'BSI'
            	");
				if ($upd) {
					db_query("
						DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa AND id_order = $id_order
                    ");
				}

				$result_json = array(
					'status'    => "00",
					'message'   => "Berhasil"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data tidak ditemukan"
			);
		}
		echo json_encode($result_json);
	}
	/* BSI VA */

	/* Mandiri VA */
	function aktif_va_mandiri()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();

		$id_siswa = $_POST['id_siswa'];
		$id_ta = $_POST['id_ta'];
		$id_jenjang = $_POST['id_jenjang'];

		$sql_jenjang = "
			SELECT nama_jenjang FROM data_jenjang WHERE id_jenjang = $id_jenjang
		";
		$res_jenjang = db_query($sql_jenjang);
		$tambahan_thn = 0;
		while ($dt_jenjang = mysqli_fetch_array($res_jenjang)) {
			if ($dt_jenjang['nama_jenjang'] == "KB" || $dt_jenjang['nama_jenjang'] == "TK" || $dt_jenjang['nama_jenjang'] == "KB/TK") {
				$tambahan_thn += 1;
			} elseif ($dt_jenjang['nama_jenjang'] == "SD" || $dt_jenjang['nama_jenjang'] == "sd") {
				$tambahan_thn += 6;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} else {
				$tambahan_thn += 3;
			}
		}

		$sql_siswa = "
			SELECT nama_siswa FROM data_siswa WHERE id_siswa = $id_siswa
		";
		$res_siswa = db_query($sql_siswa);
		$nama_siswa = "";
		while ($dt_siswa = mysqli_fetch_array($res_siswa)) {
			$nama_siswa = $dt_siswa['nama_siswa'];
		}

		$prefix = '89239'; // dari mandiri
		$numberSuffix = "";
		if (strlen(KODE_SEKOLAH) == 2) {
			if (strlen($id_siswa) == "1") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "00000" . $id_siswa;
			} else if (strlen($id_siswa) == "2") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "0000" . $id_siswa;
			} else if (strlen($id_siswa) == "3") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "000" . $id_siswa;
			} else if (strlen($id_siswa) == "4") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "00" . $id_siswa;
			} else if (strlen($id_siswa) == "5") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . $id_siswa;
			}
		} else {
			if (strlen($id_siswa) == "1") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "0000" . $id_siswa;
			} else if (strlen($id_siswa) == "2") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "000" . $id_siswa;
			} else if (strlen($id_siswa) == "3") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "00" . $id_siswa;
			} else if (strlen($id_siswa) == "4") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . $id_siswa;
			}
		}

		$th = date("Y") + $tambahan_thn;
		$bl = date("m");
		$hr = date("d");
		$jam = date("H:i:s");

		$expiredDate = $th . "-" . $bl . "-" . $hr . "T" . $jam . "+07:00";

		$sql = "
            SELECT
                `user`.nis_from_idsiswa AS id_siswa,
                id_master_trans_sis,
                master_jenis_transaksi.id_jenis_transaksi,
                nama_transaksi,
                id_user,
                id_kelas,
                id_transaksi,
                disc,
                nominal,
                (nominal - IF(tot_dibayar IS NULL, 0, tot_dibayar)) as tot_kekurangan
            FROM
                data_siswa
                INNER JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
                INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi
                INNER JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
                LEFT JOIN ( SELECT
                            id_transaksi,
                            nis_from_idsiswa,
                            SUM( ts.nominal_dibayarkan ) AS tot_dibayar 
                        FROM
                            `transaksi_siswa` ts
                            JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi 
                        WHERE
                            nis_from_idsiswa = $id_siswa 
                        GROUP BY
                            mj.id_jenis_transaksi
                ) as tb_trans ON data_siswa.id_siswa = tb_trans.nis_from_idsiswa
            WHERE
                nama_transaksi = 'UANG SAKU'
                AND `user`.nis_from_idsiswa = $id_siswa
                AND sta IS NULL
        ";
		$res = db_query($sql);
		$dt = db_assoc($sql);
		$j = mysqli_num_rows($res);
		$id_user = "";
		$id_order = "";
		$trx_id = "";
		if ($j > 0) {
			$id_user = $dt['id_user'];
			$chk_mobile = db_get("
                SELECT 
                COUNT(*) 
                FROM 
						transaksi_siswa_mobile
					WHERE
                    nis_from_idsiswa = '$dt[id_siswa]' AND
                    id_master_trans_sis = '$dt[id_master_trans_sis]' AND
                    id_jenis_transaksi = '$dt[id_jenis_transaksi]'
            ");
			if ($chk_mobile == 0) {
				$order = db_query("
					INSERT INTO idorder(
                        object,
                        id_user)
                     VALUES (
                        1,
                        '$dt[id_user]'
                    )
                ");
				$id_order = db_id();

				$ins = "
					INSERT INTO transaksi_siswa_mobile 
					(
                        nis_from_idsiswa,
                        id_master_trans_sis,
                        id_kelas,
                        id_jenis_transaksi,
                        nominal_ketetapan,
                        disc,
                        nominal_dibayarkan,
                        id_user,
                        siap_bayar,
                        id_order
                    ) 
                    VALUES 
					(
						NULLIF('" . $dt['id_siswa'] . "', ''),
                        NULLIF('" . $dt['id_master_trans_sis'] . "', ''),
                        NULLIF('" . $dt['id_kelas'] . "', ''),
                        NULLIF('" . $dt['id_jenis_transaksi'] . "', ''),
                        NULLIF('" . $dt['nominal'] . "', ''),
                        NULLIF('" . $dt['disc'] . "', ''),
                        NULLIF('" . $dt['tot_kekurangan'] . "', ''),
                        NULLIF('" . $dt['id_user'] . "', ''),
                        NULLIF('1', ''),
                        NULLIF('" . $id_order . "', '')
					)
                ";
				$res = db_query($ins);
				if ($res) {
					$trx_id = $numberSuffix;

					$order_json = array(
						'client_id'         => "",
						'trx_id'            => $trx_id,
						'trx_amount'        => "0",
						'billing_type'      => "va-open-payment",
						'datetime_expired'  => $expiredDate,
						'virtual_account'   => $numberSuffix,
						'customer_name'     => $nama_siswa,
						'customer_email'    => "etn.egref@gmail.com",
						'customer_phone'    => "082223519811",
						'description'       => "Uang Saku",
						'type'              => "createMandiri"
					);
					$enc_order = json_encode($order_json);

					$upd_order = db_query("
                            UPDATE idorder
                            SET trx_id = '$trx_id', data_json = '$enc_order'
                            WHERE 
                                id_order = '$id_order'
                        ");

					$expired = str_replace("T", " ", $expiredDate);
					$expired = substr($expired, 0, 10);
					db_query("
							INSERT INTO data_va_uangsaku ( id_order, id_user, no_va, expired, nis_dari_siswa, opsional )
                            VALUES ( '$id_order', '$id_user', '$numberSuffix', '$expired', '$id_siswa', 'Mandiri' )
                        ");

					//Notif WA
					$_GET['nis'] = $id_siswa;
					$_GET['va'] = $numberSuffix;
					$TU_old->wappin_notif_akun();
					$result_json = array(
						'status'  => "00",
						'message' => "Berhasil"
					);
				} else {
					$result_json = array(
						'status'    => "5",
						'message'   => "gagal konfigurasi VA"
					);
				}
			} else {
				db_query("
					DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa
                ");

				$result_json = array(
					'status'    => "5",
					'message'   => "gagal konfigurasi VA, silahkan coba kembali"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data jenis transaksi tidak ada"
			);
		}

		echo json_encode($result_json);
	}
	function nonaktif_va_mandiri()
	{
		$id_order = $_POST['id_order'];

		$sekolah = base64_encode(ABS);

		$cek_data = "
    		SELECT
				a.data_json,
				b.nis_dari_siswa,
				b.payment_id
			FROM
				idorder a
				JOIN data_va_uangsaku b ON b.id_order = a.id_order
			WHERE
				a.id_order = $id_order
    	";
		$res = db_query($cek_data);
		$j = mysqli_num_rows($res);
		if ($j > 0) {
			while ($dt = mysqli_fetch_array($res)) {
				$id_siswa = $dt['nis_dari_siswa'];
				$data = json_decode($dt['data_json']);
				$nama = $data->customer_name;
				$telp = $data->customer_phone;
				$email = $data->customer_email;

				$now = date("Y-m-d H:i:s");

				$upd = db_query("
					UPDATE data_va_uangsaku SET non_aktif = '1' WHERE id_order = $id_order AND opsional = 'Mandiri'
            	");
				if ($upd) {
					db_query("
						DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa AND id_order = $id_order
                    ");
				}

				$result_json = array(
					'status'    => "00",
					'message'   => "Berhasil"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data tidak ditemukan"
			);
		}
		echo json_encode($result_json);
	}
	/* Mandiri VA */

	/* BMI VA */
	function aktif_va_bmi()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();

		$id_siswa = $_POST['id_siswa'];
		$id_ta = $_POST['id_ta'];
		$id_jenjang = $_POST['id_jenjang'];

		$sql_jenjang = "
			SELECT nama_jenjang FROM data_jenjang WHERE id_jenjang = $id_jenjang
		";
		$res_jenjang = db_query($sql_jenjang);
		$tambahan_thn = 0;
		while ($dt_jenjang = mysqli_fetch_array($res_jenjang)) {
			if ($dt_jenjang['nama_jenjang'] == "KB" || $dt_jenjang['nama_jenjang'] == "TK" || $dt_jenjang['nama_jenjang'] == "KB/TK") {
				$tambahan_thn += 1;
			} elseif ($dt_jenjang['nama_jenjang'] == "SD" || $dt_jenjang['nama_jenjang'] == "sd") {
				$tambahan_thn += 6;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} else {
				$tambahan_thn += 3;
			}
		}

		$sql_siswa = "
			SELECT nama_siswa FROM data_siswa WHERE id_siswa = $id_siswa
		";
		$res_siswa = db_query($sql_siswa);
		$nama_siswa = "";
		while ($dt_siswa = mysqli_fetch_array($res_siswa)) {
			$nama_siswa = $dt_siswa['nama_siswa'];
		}


		/* kode produk setelah prefix
		01 - Full
		02 - Open
		03 - Partial
		*/
		$prefix_devel = '7383'; // devel BMI
		$prefix = '8217'; // BCN (prefix VA) dari BMI
		$numberSuffix = "";
		if (strlen(KODE_SEKOLAH) == 2) {
			if (strlen($id_siswa) == "1") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "00000" . $id_siswa;
			} else if (strlen($id_siswa) == "2") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "0000" . $id_siswa;
			} else if (strlen($id_siswa) == "3") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "000" . $id_siswa;
			} else if (strlen($id_siswa) == "4") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "00" . $id_siswa;
			} else if (strlen($id_siswa) == "5") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . $id_siswa;
			}
		} else {
			if (strlen($id_siswa) == "1") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "0000" . $id_siswa;
			} else if (strlen($id_siswa) == "2") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "000" . $id_siswa;
			} else if (strlen($id_siswa) == "3") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "00" . $id_siswa;
			} else if (strlen($id_siswa) == "4") {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = $prefix . '0200' . KODE_SEKOLAH . $id_siswa;
			}
		}

		$th = date("Y") + $tambahan_thn;
		$bl = date("m");
		$hr = date("d");
		$jam = date("H:i:s");

		$expiredDate = $th . "-" . $bl . "-" . $hr . "T" . $jam . "+07:00";

		$sql = "
            SELECT
                `user`.nis_from_idsiswa AS id_siswa,
                id_master_trans_sis,
                master_jenis_transaksi.id_jenis_transaksi,
                nama_transaksi,
                id_user,
                id_kelas,
                id_transaksi,
                disc,
                nominal,
                (nominal - IF(tot_dibayar IS NULL, 0, tot_dibayar)) as tot_kekurangan
            FROM
                data_siswa
                INNER JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
                INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi
                INNER JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
                LEFT JOIN ( SELECT
                            id_transaksi,
                            nis_from_idsiswa,
                            SUM( ts.nominal_dibayarkan ) AS tot_dibayar 
                        FROM
                            `transaksi_siswa` ts
                            JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi 
                        WHERE
                            nis_from_idsiswa = $id_siswa 
                        GROUP BY
                            mj.id_jenis_transaksi
                ) as tb_trans ON data_siswa.id_siswa = tb_trans.nis_from_idsiswa
            WHERE
                nama_transaksi = 'UANG SAKU'
                AND `user`.nis_from_idsiswa = $id_siswa
                AND sta IS NULL
        ";
		$res = db_query($sql);
		$dt = db_assoc($sql);
		$j = mysqli_num_rows($res);
		$id_user = "";
		$id_order = "";
		$trx_id = "";
		if ($j > 0) {
			$id_user = $dt['id_user'];
			$chk_mobile = db_get("
                SELECT 
                COUNT(*) 
                FROM 
						transaksi_siswa_mobile
					WHERE
                    nis_from_idsiswa = '$dt[id_siswa]' AND
                    id_master_trans_sis = '$dt[id_master_trans_sis]' AND
                    id_jenis_transaksi = '$dt[id_jenis_transaksi]'
            ");
			if ($chk_mobile == 0) {
				$order = db_query("
					INSERT INTO idorder(
                        object,
                        id_user)
                     VALUES (
                        1,
                        '$dt[id_user]'
                    )
                ");
				$id_order = db_id();

				$ins = "
					INSERT INTO transaksi_siswa_mobile 
					(
                        nis_from_idsiswa,
                        id_master_trans_sis,
                        id_kelas,
                        id_jenis_transaksi,
                        nominal_ketetapan,
                        disc,
                        nominal_dibayarkan,
                        id_user,
                        siap_bayar,
                        id_order
                    ) 
                    VALUES 
					(
						NULLIF('" . $dt['id_siswa'] . "', ''),
                        NULLIF('" . $dt['id_master_trans_sis'] . "', ''),
                        NULLIF('" . $dt['id_kelas'] . "', ''),
                        NULLIF('" . $dt['id_jenis_transaksi'] . "', ''),
                        NULLIF('" . $dt['nominal'] . "', ''),
                        NULLIF('" . $dt['disc'] . "', ''),
                        NULLIF('" . $dt['tot_kekurangan'] . "', ''),
                        NULLIF('" . $dt['id_user'] . "', ''),
                        NULLIF('1', ''),
                        NULLIF('" . $id_order . "', '')
					)
                ";
				$res = db_query($ins);
				if ($res) {
					$trx_id = $numberSuffix;

					$order_json = array(
						'client_id'         => "",
						'trx_id'            => $trx_id,
						'trx_amount'        => "0",
						'billing_type'      => "va-open-payment",
						'datetime_expired'  => $expiredDate,
						'virtual_account'   => $numberSuffix,
						'customer_name'     => $nama_siswa,
						'customer_email'    => "etn.egref@gmail.com",
						'customer_phone'    => "082223519811",
						'description'       => "Uang Saku",
						'type'              => "createBMI"
					);
					$enc_order = json_encode($order_json);

					$upd_order = db_query("
                            UPDATE idorder
                            SET trx_id = '$trx_id', data_json = '$enc_order'
                            WHERE 
                                id_order = '$id_order'
                        ");

					$expired = str_replace("T", " ", $expiredDate);
					$expired = substr($expired, 0, 10);
					db_query("
							INSERT INTO data_va_uangsaku ( id_order, id_user, no_va, expired, nis_dari_siswa, opsional )
                            VALUES ( '$id_order', '$id_user', '$numberSuffix', '$expired', '$id_siswa', 'BMI' )
                        ");

					//Notif WA
					$_GET['nis'] = $id_siswa;
					$_GET['va'] = $numberSuffix;
					$TU_old->wappin_notif_akun();
					$result_json = array(
						'status'  => "00",
						'message' => "Berhasil"
					);
				} else {
					$result_json = array(
						'status'    => "5",
						'message'   => "gagal konfigurasi VA"
					);
				}
			} else {
				db_query("
					DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa
                ");

				$result_json = array(
					'status'    => "5",
					'message'   => "gagal konfigurasi VA, silahkan coba kembali"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data jenis transaksi tidak ada"
			);
		}

		echo json_encode($result_json);
	}
	function nonaktif_va_bmi()
	{
		$id_order = $_POST['id_order'];

		$sekolah = base64_encode(ABS);

		$cek_data = "
    		SELECT
				a.data_json,
				b.nis_dari_siswa,
				b.payment_id
			FROM
				idorder a
				JOIN data_va_uangsaku b ON b.id_order = a.id_order
			WHERE
				a.id_order = $id_order
    	";
		$res = db_query($cek_data);
		$j = mysqli_num_rows($res);
		if ($j > 0) {
			while ($dt = mysqli_fetch_array($res)) {
				$id_siswa = $dt['nis_dari_siswa'];
				$data = json_decode($dt['data_json']);
				$nama = $data->customer_name;
				$telp = $data->customer_phone;
				$email = $data->customer_email;

				$now = date("Y-m-d H:i:s");

				$upd = db_query("
					UPDATE data_va_uangsaku SET non_aktif = '1' WHERE id_order = $id_order AND opsional = 'BMI'
            	");
				if ($upd) {
					db_query("
						DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa AND id_order = $id_order
                    ");
				}

				$result_json = array(
					'status'    => "00",
					'message'   => "Berhasil"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data tidak ditemukan"
			);
		}
		echo json_encode($result_json);
	}
	/* BMI VA */

	/* BRI VA */
	function aktif_va_bri()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();

		$id_siswa = $_POST['id_siswa'];
		$id_ta = $_POST['id_ta'];
		$id_jenjang = $_POST['id_jenjang'];

		$sql_jenjang = "
			SELECT nama_jenjang FROM data_jenjang WHERE id_jenjang = $id_jenjang
		";
		$res_jenjang = db_query($sql_jenjang);
		$tambahan_thn = 0;
		while ($dt_jenjang = mysqli_fetch_array($res_jenjang)) {
			if ($dt_jenjang['nama_jenjang'] == "KB" || $dt_jenjang['nama_jenjang'] == "TK" || $dt_jenjang['nama_jenjang'] == "KB/TK") {
				$tambahan_thn += 1;
			} elseif ($dt_jenjang['nama_jenjang'] == "SD" || $dt_jenjang['nama_jenjang'] == "sd") {
				$tambahan_thn += 6;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} else {
				$tambahan_thn += 3;
			}
		}

		$sql_siswa = "
			SELECT nama_siswa FROM data_siswa WHERE id_siswa = $id_siswa
		";
		$res_siswa = db_query($sql_siswa);
		$nama_siswa = "";
		while ($dt_siswa = mysqli_fetch_array($res_siswa)) {
			$nama_siswa = $dt_siswa['nama_siswa'];
		}

		$prefix = '77777';
		$numberSuffix = "";
		if (strlen(KODE_SEKOLAH) == 2) {
			if (strlen($id_siswa) == "1") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "00000" . $id_siswa;
			} else if (strlen($id_siswa) == "2") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "0000" . $id_siswa;
			} else if (strlen($id_siswa) == "3") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "000" . $id_siswa;
			} else if (strlen($id_siswa) == "4") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "00" . $id_siswa;
			} else if (strlen($id_siswa) == "5") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . $id_siswa;
			}
		} else {
			if (strlen($id_siswa) == "1") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "0000" . $id_siswa;
			} else if (strlen($id_siswa) == "2") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "000" . $id_siswa;
			} else if (strlen($id_siswa) == "3") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "00" . $id_siswa;
			} else if (strlen($id_siswa) == "4") {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = $prefix . '000' . KODE_SEKOLAH . $id_siswa;
			}
		}

		$th = date("Y") + $tambahan_thn;
		$bl = date("m");
		$hr = date("d");
		$jam = date("H:i:s");

		$expiredDate = $th . "-" . $bl . "-" . $hr . "T" . $jam . "+07:00";

		$sql = "
            SELECT
                `user`.nis_from_idsiswa AS id_siswa,
                id_master_trans_sis,
                master_jenis_transaksi.id_jenis_transaksi,
                nama_transaksi,
                id_user,
                id_kelas,
                id_transaksi,
                disc,
                nominal,
                (nominal - IF(tot_dibayar IS NULL, 0, tot_dibayar)) as tot_kekurangan
            FROM
                data_siswa
                INNER JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
                INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi
                INNER JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
                LEFT JOIN ( SELECT
                            id_transaksi,
                            nis_from_idsiswa,
                            SUM( ts.nominal_dibayarkan ) AS tot_dibayar 
                        FROM
                            `transaksi_siswa` ts
                            JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi 
                        WHERE
                            nis_from_idsiswa = $id_siswa 
                        GROUP BY
                            mj.id_jenis_transaksi
                ) as tb_trans ON data_siswa.id_siswa = tb_trans.nis_from_idsiswa
            WHERE
                nama_transaksi = 'UANG SAKU'
                AND `user`.nis_from_idsiswa = $id_siswa
                AND sta IS NULL
        ";
		$res = db_query($sql);
		$dt = db_assoc($sql);
		$j = mysqli_num_rows($res);
		$id_user = "";
		$id_order = "";
		$trx_id = "";
		if ($j > 0) {
			$id_user = $dt['id_user'];
			$chk_mobile = db_get("
                SELECT 
                COUNT(*) 
                FROM 
						transaksi_siswa_mobile
					WHERE
                    nis_from_idsiswa = '$dt[id_siswa]' AND
                    id_master_trans_sis = '$dt[id_master_trans_sis]' AND
                    id_jenis_transaksi = '$dt[id_jenis_transaksi]'
            ");
			if ($chk_mobile == 0) {
				$order = db_query("
					INSERT INTO idorder(
                        object,
                        id_user)
                     VALUES (
                        1,
                        '$dt[id_user]'
                    )
                ");
				$id_order = db_id();

				$ins = "
					INSERT INTO transaksi_siswa_mobile 
					(
                        nis_from_idsiswa,
                        id_master_trans_sis,
                        id_kelas,
                        id_jenis_transaksi,
                        nominal_ketetapan,
                        disc,
                        nominal_dibayarkan,
                        id_user,
                        siap_bayar,
                        id_order
                    ) 
                    VALUES 
					(
						NULLIF('" . $dt['id_siswa'] . "', ''),
                        NULLIF('" . $dt['id_master_trans_sis'] . "', ''),
                        NULLIF('" . $dt['id_kelas'] . "', ''),
                        NULLIF('" . $dt['id_jenis_transaksi'] . "', ''),
                        NULLIF('" . $dt['nominal'] . "', ''),
                        NULLIF('" . $dt['disc'] . "', ''),
                        NULLIF('" . $dt['tot_kekurangan'] . "', ''),
                        NULLIF('" . $dt['id_user'] . "', ''),
                        NULLIF('1', ''),
                        NULLIF('" . $id_order . "', '')
					)
                ";
				$res = db_query($ins);
				if ($res) {
					$trx_id = $numberSuffix;

					$order_json = array(
						'client_id'         => "",
						'trx_id'            => $trx_id,
						'trx_amount'        => "0",
						'billing_type'      => "va-open-payment",
						'datetime_expired'  => $expiredDate,
						'virtual_account'   => $numberSuffix,
						'customer_name'     => $nama_siswa,
						'customer_email'    => "etn.egref@gmail.com",
						'customer_phone'    => "082223519811",
						'description'       => "Uang Saku",
						'type'              => "createBRI"
					);
					$enc_order = json_encode($order_json);

					$upd_order = db_query("
                            UPDATE idorder
                            SET trx_id = '$trx_id', data_json = '$enc_order'
                            WHERE 
                                id_order = '$id_order'
                        ");

					$expired = str_replace("T", " ", $expiredDate);
					$expired = substr($expired, 0, 10);
					db_query("
							INSERT INTO data_va_uangsaku ( id_order, id_user, no_va, expired, nis_dari_siswa, opsional )
                            VALUES ( '$id_order', '$id_user', '$numberSuffix', '$expired', '$id_siswa', 'BRI' )
                        ");

					//Notif WA
					$_GET['nis'] = $id_siswa;
					$_GET['va'] = $numberSuffix;
					$TU_old->wappin_notif_akun();
					$result_json = array(
						'status'  => "00",
						'message' => "Berhasil"
					);
				} else {
					$result_json = array(
						'status'    => "5",
						'message'   => "gagal konfigurasi VA"
					);
				}
			} else {
				db_query("
					DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa
                ");

				$result_json = array(
					'status'    => "5",
					'message'   => "gagal konfigurasi VA, silahkan coba kembali"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data jenis transaksi tidak ada"
			);
		}

		echo json_encode($result_json);
	}
	function nonaktif_va_bri()
	{
		$id_order = $_POST['id_order'];

		$sekolah = base64_encode(ABS);

		$cek_data = "
    		SELECT
				a.data_json,
				b.nis_dari_siswa,
				b.payment_id
			FROM
				idorder a
				JOIN data_va_uangsaku b ON b.id_order = a.id_order
			WHERE
				a.id_order = $id_order
    	";
		$res = db_query($cek_data);
		$j = mysqli_num_rows($res);
		if ($j > 0) {
			while ($dt = mysqli_fetch_array($res)) {
				$id_siswa = $dt['nis_dari_siswa'];
				$data = json_decode($dt['data_json']);
				$nama = $data->customer_name;
				$telp = $data->customer_phone;
				$email = $data->customer_email;

				$now = date("Y-m-d H:i:s");

				$upd = db_query("
					UPDATE data_va_uangsaku SET non_aktif = '1' WHERE id_order = $id_order AND opsional = 'BRI'
            	");
				if ($upd) {
					db_query("
						DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa AND id_order = $id_order
                    ");
				}

				$result_json = array(
					'status'    => "00",
					'message'   => "Berhasil"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data tidak ditemukan"
			);
		}
		echo json_encode($result_json);
	}
	/* BRI VA */

	/* BPD JATENG VA */
	function aktif_va_bpdjateng()
	{
		include 'app/lib/TU_old.php';
		$TU_old = new TU_old();

		$id_siswa = $_POST['id_siswa'];
		$id_ta = $_POST['id_ta'];
		$id_jenjang = $_POST['id_jenjang'];

		$sql_jenjang = "
			SELECT nama_jenjang FROM data_jenjang WHERE id_jenjang = $id_jenjang
		";
		$res_jenjang = db_query($sql_jenjang);
		$tambahan_thn = 0;
		while ($dt_jenjang = mysqli_fetch_array($res_jenjang)) {
			if ($dt_jenjang['nama_jenjang'] == "KB" || $dt_jenjang['nama_jenjang'] == "TK" || $dt_jenjang['nama_jenjang'] == "KB/TK") {
				$tambahan_thn += 1;
			} elseif ($dt_jenjang['nama_jenjang'] == "SD" || $dt_jenjang['nama_jenjang'] == "sd") {
				$tambahan_thn += 6;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} elseif ($dt_jenjang['nama_jenjang'] == "SMP" || $dt_jenjang['nama_jenjang'] == "smp") {
				$tambahan_thn += 3;
			} else {
				$tambahan_thn += 3;
			}
		}

		$sql_siswa = "
			SELECT nama_siswa FROM data_siswa WHERE id_siswa = $id_siswa
		";
		$res_siswa = db_query($sql_siswa);
		$nama_siswa = "";
		while ($dt_siswa = mysqli_fetch_array($res_siswa)) {
			$nama_siswa = $dt_siswa['nama_siswa'];
		}

		$prefix = '1'; // 0 close dan 1 open
		if (strlen(KODE_SEKOLAH) == 2) {
			if (strlen($id_siswa) == "1") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "00000" . $id_siswa;
			} else if (strlen($id_siswa) == "2") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "0000" . $id_siswa;
			} else if (strlen($id_siswa) == "3") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "000" . $id_siswa;
			} else if (strlen($id_siswa) == "4") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "00" . $id_siswa;
			} else if (strlen($id_siswa) == "5") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = $prefix . KODE_SEKOLAH . $id_siswa;
			}
		} else {
			if (strlen($id_siswa) == "1") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "0000" . $id_siswa;
			} else if (strlen($id_siswa) == "2") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "000" . $id_siswa;
			} else if (strlen($id_siswa) == "3") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "00" . $id_siswa;
			} else if (strlen($id_siswa) == "4") {
				$numberSuffix = $prefix . KODE_SEKOLAH . "0" . $id_siswa;
			} else {
				$numberSuffix = $prefix . KODE_SEKOLAH . $id_siswa;
			}
		}

		$th = date("Y") + $tambahan_thn;
		$bl = date("m");
		$hr = date("d");
		$jam = date("H:i:s");

		$expiredDate = $th . "-" . $bl . "-" . $hr . "T" . $jam . "+07:00";

		$sql = "
            SELECT
                `user`.nis_from_idsiswa AS id_siswa,
                id_master_trans_sis,
                master_jenis_transaksi.id_jenis_transaksi,
                nama_transaksi,
                id_user,
                id_kelas,
                id_transaksi,
                disc,
                nominal,
                (nominal - IF(tot_dibayar IS NULL, 0, tot_dibayar)) as tot_kekurangan
            FROM
                data_siswa
                INNER JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
                INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi
                INNER JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
                LEFT JOIN ( SELECT
                            id_transaksi,
                            nis_from_idsiswa,
                            SUM( ts.nominal_dibayarkan ) AS tot_dibayar 
                        FROM
                            `transaksi_siswa` ts
                            JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi 
                        WHERE
                            nis_from_idsiswa = $id_siswa 
                        GROUP BY
                            mj.id_jenis_transaksi
                ) as tb_trans ON data_siswa.id_siswa = tb_trans.nis_from_idsiswa
            WHERE
                nama_transaksi = 'UANG SAKU'
                AND `user`.nis_from_idsiswa = $id_siswa
                AND sta IS NULL
        ";
		$res = db_query($sql);
		$dt = db_assoc($sql);
		$j = mysqli_num_rows($res);
		$id_user = "";
		$id_order = "";
		$trx_id = "";
		if ($j > 0) {
			$id_user = $dt['id_user'];
			$chk_mobile = db_get("
                SELECT 
                COUNT(*) 
                FROM 
						transaksi_siswa_mobile
					WHERE
                    nis_from_idsiswa = '$dt[id_siswa]' AND
                    id_master_trans_sis = '$dt[id_master_trans_sis]' AND
                    id_jenis_transaksi = '$dt[id_jenis_transaksi]'
            ");
			if ($chk_mobile == 0) {
				$order = db_query("
					INSERT INTO idorder(
                        object,
                        id_user)
                     VALUES (
                        1,
                        '$dt[id_user]'
                    )
                ");
				$id_order = db_id();

				$ins = "
					INSERT INTO transaksi_siswa_mobile 
					(
                        nis_from_idsiswa,
                        id_master_trans_sis,
                        id_kelas,
                        id_jenis_transaksi,
                        nominal_ketetapan,
                        disc,
                        nominal_dibayarkan,
                        id_user,
                        siap_bayar,
                        id_order
                    ) 
                    VALUES 
					(
						NULLIF('" . $dt['id_siswa'] . "', ''),
                        NULLIF('" . $dt['id_master_trans_sis'] . "', ''),
                        NULLIF('" . $dt['id_kelas'] . "', ''),
                        NULLIF('" . $dt['id_jenis_transaksi'] . "', ''),
                        NULLIF('" . $dt['nominal'] . "', ''),
                        NULLIF('" . $dt['disc'] . "', ''),
                        NULLIF('" . $dt['tot_kekurangan'] . "', ''),
                        NULLIF('" . $dt['id_user'] . "', ''),
                        NULLIF('1', ''),
                        NULLIF('" . $id_order . "', '')
					)
                ";
				$res = db_query($ins);
				if ($res) {
					$trx_id = $numberSuffix;

					$order_json = array(
						'client_id'         => "",
						'trx_id'            => $trx_id,
						'trx_amount'        => "0",
						'billing_type'      => "va-open-payment",
						'datetime_expired'  => $expiredDate,
						'virtual_account'   => $numberSuffix,
						'customer_name'     => $nama_siswa,
						'customer_email'    => "etn.egref@gmail.com",
						'customer_phone'    => "082223519811",
						'description'       => "Uang Saku",
						'type'              => "createBPDJateng"
					);
					$enc_order = json_encode($order_json);

					$upd_order = db_query("
                            UPDATE idorder
                            SET trx_id = '$trx_id', data_json = '$enc_order'
                            WHERE 
                                id_order = '$id_order'
                        ");

					$expired = str_replace("T", " ", $expiredDate);
					$expired = substr($expired, 0, 10);
					db_query("
							INSERT INTO data_va_uangsaku ( id_order, id_user, no_va, expired, nis_dari_siswa, opsional )
                            VALUES ( '$id_order', '$id_user', '$numberSuffix', '$expired', '$id_siswa', 'BPD JATENG' )
                        ");

					//Notif WA
					$_GET['nis'] = $id_siswa;
					$_GET['va'] = $numberSuffix;
					$TU_old->wappin_notif_akun();
					$result_json = array(
						'status'  => "00",
						'message' => "Berhasil"
					);
				} else {
					$result_json = array(
						'status'    => "5",
						'message'   => "gagal konfigurasi VA"
					);
				}
			} else {
				db_query("
					DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa
                ");

				$result_json = array(
					'status'    => "5",
					'message'   => "gagal konfigurasi VA, silahkan coba kembali"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data jenis transaksi tidak ada"
			);
		}

		echo json_encode($result_json);
	}
	function nonaktif_va_bpdjateng()
	{
		$id_order = $_POST['id_order'];

		$sekolah = base64_encode(ABS);

		$cek_data = "
    		SELECT
				a.data_json,
				b.nis_dari_siswa,
				b.payment_id
			FROM
				idorder a
				JOIN data_va_uangsaku b ON b.id_order = a.id_order
			WHERE
				a.id_order = $id_order
    	";
		$res = db_query($cek_data);
		$j = mysqli_num_rows($res);
		if ($j > 0) {
			while ($dt = mysqli_fetch_array($res)) {
				$id_siswa = $dt['nis_dari_siswa'];
				$data = json_decode($dt['data_json']);
				$nama = $data->customer_name;
				$telp = $data->customer_phone;
				$email = $data->customer_email;

				$now = date("Y-m-d H:i:s");

				$upd = db_query("
					UPDATE data_va_uangsaku SET non_aktif = '1' WHERE id_order = $id_order AND opsional = 'BPD JATENG'
            	");
				if ($upd) {
					db_query("
						DELETE FROM transaksi_siswa_mobile WHERE nis_from_idsiswa = $id_siswa AND id_order = $id_order
                    ");
				}

				$result_json = array(
					'status'    => "00",
					'message'   => "Berhasil"
				);
			}
		} else {
			$result_json = array(
				'status'    => "4",
				'message'   => "data tidak ditemukan"
			);
		}
		echo json_encode($result_json);
	}
	/* BPD JATENG VA */

	/* BCA PLINK VA */
	
	/* BCA PLINK VA */
}
