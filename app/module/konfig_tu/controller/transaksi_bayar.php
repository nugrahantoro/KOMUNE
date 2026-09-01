<?php
class Transaksi_Bayar extends Controller
{

	// SKEMA BARU NOTIF WA [12 Agustus 2025] //
		function kuota_harian_meta(){
			$kuota = 0;
			$query = "
				SELECT kuota_harian FROM master_kuota_wa WHERE `status` = '1'
			";
			$res = db_query_core($query);
			while ($dt = mysqli_fetch_array($res)) {
				$kuota = $dt['kuota_harian'];
			}
			return $kuota;
		}
		function notif_wa_draft($number, $nama, $total, $rincian, $id_order){
			$sesi = $_SESSION['id'];
			$nama_us = $_SESSION['nama_us'];

			if ($number[0] == '0') {
				$number = '62' . substr($number, 1);
			}
			$tgl_bayar = $this->tgl_ind(date("d-m-Y"));
			$log_pesan = "*Halo, " . $nama . "!* Pembayaran sekolah Ananda secara tunai telah berhasil. Berikut adalah rinciannya: *Tanggal Pembayaran:* " . $tgl_bayar . " *Jumlah Pembayaran:* Rp " . $total . " *Detail Pembayaran:* " . $rincian . " Anda dapat melihat rincian lebih lanjut di aplikasi. Jika ada pertanyaan atau membutuhkan bantuan, tim kami siap membantu Anda! Hubungi kami melalui aplikasi atau layanan chatbot. Tetap semangat dan terus berprestasi!";

			$db = ABS;
			$tgl_skrg = date("Y-m-d");
			$terpakai = 0;
			$query = "
				SELECT COUNT(id) terpakai FROM `log_notif_wa_official` WHERE db = '$db' AND DATE(created_at) = '$tgl_skrg'
			";
			$res = db_query_core($query);
			while ($dt = mysqli_fetch_array($res)) {
				$terpakai = $dt['terpakai'];
			}
			$kuota = $this->kuota_harian_meta();
			$sisa = $kuota - $terpakai;

			if ($sisa < 1){
				// pakai wa unofficial
				$ins = "
					INSERT INTO log_notif_wa_unofficial(nomor,pesan,id_user,nama_user,db,sender,id_order_trans) 
					VALUES ('$number','$log_pesan','$sesi','$nama_us','$db','waha','$id_order')
				";
				db_query_core($ins);
			}
			else{
				// pakai wa meta
				$param1 = $nama;
				$param2 = $tgl_bayar;
				$param3 = "Rp ".$total;
				$param4 = $rincian;
				$param5 = "Tetap semangat dan terus berprestasi!";
				$ins = "
					INSERT INTO log_notif_wa_official(nomor,param1,param2,param3,param4,param5,pesan,id_user,nama_user,db,sender,id_order_trans) 
					VALUES ('$number','$param1','$param2','$param3','$param4','$param5','$log_pesan','$sesi','$nama_us','$db','meta','$id_order')
				";
				db_query_core($ins);
			}
		}
	// SKEMA BARU NOTIF WA [12 Agustus 2025] //

	/* API ETN META */
	function tgl_ind($date)
	{
		$bulan = array(
			1 => "Januari",
			2 => "Februari",
			3 => "Maret",
			4 => "April",
			5 => "Mei",
			6 => "Juni",
			7 => "Juli",
			8 => "Agustus",
			9 => "September",
			10 => "Oktober",
			11 => "November",
			12 => "Desember"
		);

		// Memisahkan tanggal, bulan, dan tahun
		list($day, $month, $year) = explode("-", $date);

		// Mengubah format bulan ke bahasa Indonesia
		$month_ind = $bulan[(int)$month];

		// Format tanggal dalam bahasa Indonesia
		$date_indonesia = $day . " " . $month_ind . " " . $year;

		return $date_indonesia; // Output: 02 Juli 2024
	}
	function notif_wa($number, $nama, $total, $rincian)
	{
		$sesi = $_SESSION['id'];

		if ($number[0] == '0') {
			$number = '62' . substr($number, 1);
		}

		$tgl_bayar = $this->tgl_ind(date("d-m-Y"));

		$log_pesan = "*Halo, " . $nama . "!* Pembayaran sekolah Ananda secara tunai telah berhasil. Berikut adalah rinciannya: *Tanggal Pembayaran:* " . $tgl_bayar . " *Jumlah Pembayaran:* Rp " . $total . " *Detail Pembayaran:* " . $rincian . " Anda dapat melihat rincian lebih lanjut di aplikasi. Jika ada pertanyaan atau membutuhkan bantuan, tim kami siap membantu Anda! Hubungi kami melalui aplikasi atau layanan chatbot. 📚 Tetap semangat dan terus berprestasi! 📚";

		$url = "https://apps.etnindonesia.id/api/message/text";
		//$url = "https://apps.etnindonesia.id/api/wabusiness/text";

		$data = [
			"client_id" => "480650881798248",
			"project_id" => "20112401",
			"type" => "info_transaksi",
			"recipient_number" => $number,
			"language_code" => "en",
			"params" => [
				"1" => $nama,
				"2" => $tgl_bayar,
				"3" => "Rp " . $total,
				"4" => $rincian,
				"5" => "Tetap semangat dan terus berprestasi!"
			],
			"header" => [
				"param" => SCHOOL_NAME_SEME
			]
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Accept: application/json',
			'Authorization: Basic c2Vrb2xhaGt1YXBwaW5kb3NhdDplOGIyZjk5ZTlhZjcyNTY2M2NlMTFlOGI2MjRlMjhhMA=='
		]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			//echo 'Error:' . curl_error($ch);
			$query = "
				INSERT INTO 
					ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
				VALUES
					( '$number', '$log_pesan', '$response', '01', '$sesi', '750', '7' )
			";
			$ins = db_query($query);
		} else {
			//echo $response;
			$query = "
				INSERT INTO 
					ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
				VALUES
					( '$number', '$log_pesan', '$response', '200', '$sesi', '750', '7' )
			";
			$ins = db_query($query);
		}

		curl_close($ch);
	}
	function notif_wa_saku($number, $nama, $total)
	{
		$sesi = $_SESSION['id'];

		$tgl_isi = $this->tgl_ind(date("d-m-Y"));

		$log_pesan = "*Hai, " . $nama . "!* Pengisian uang saku Ananda secara tunai telah berhasil. *Tanggal Pengisian:* " . $tgl_isi . " *Jumlah Pengisian:* Rp " . $total . " Rincian lebih lanjut dapat dilihat melalui aplikasi. Jika ada pertanyaan atau membutuhkan bantuan, tim kami siap membantu Anda. Hubungi kami melalui aplikasi atau layanan chatbot. 📚 Tetap semangat dan terus berprestasi! 📚";

		$url = "https://apps.etnindonesia.id/api/message/text";
		//$url = "https://apps.etnindonesia.id/api/wabusiness/text";

		$data = [
			"client_id" => "480650881798248",
			"project_id" => "20112401",
			"type" => "info_uang_saku",
			"recipient_number" => $number,
			"language_code" => "id",
			"params" => [
				"1" => $nama,
				"2" => "uang saku",
				"3" => $tgl_isi,
				"4" => "Rp " . $total,
				"5" => "Tetap semangat dan terus berprestasi!"
			],
			"header" => [
				"param" => SCHOOL_NAME
			]
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Accept: application/json',
			'Authorization: Basic c2Vrb2xhaGt1YXBwaW5kb3NhdDplOGIyZjk5ZTlhZjcyNTY2M2NlMTFlOGI2MjRlMjhhMA=='
		]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			//echo 'Error:' . curl_error($ch);
			$query = "
				INSERT INTO 
					ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
				VALUES
					( '$number', '$log_pesan', '$response', '01', '$sesi', '750', '8' )
			";
			$ins = db_query($query);
		} else {
			//echo $response;
			$query = "
				INSERT INTO 
					ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
				VALUES
					( '$number', '$log_pesan', '$response', '200', '$sesi', '750', '8' )
			";
			$ins = db_query($query);
		}

		curl_close($ch);
	}
	function notif_wa_tarik_saku($number, $nama, $total)
	{
		$sesi = $_SESSION['id'];

		$tgl_tarik = $this->tgl_ind(date("d-m-Y"));

		$log_pesan = "*Hai, " . $nama . "* Penarikan uang saku Ananda telah berhasil. *Tanggal Penarikan:* " . $tgl_tarik . " *Jumlah Penarikan:* Rp " . $total . " Rincian lebih lanjut dapat dilihat melalui aplikasi. Jika ada pertanyaan atau membutuhkan bantuan, tim kami siap membantu Anda. Hubungi kami melalui aplikasi atau layanan chatbot. 📚 Tetap semangat dan terus berprestasi! 📚";

		$url = "https://apps.etnindonesia.id/api/message/text";
		//$url = "https://apps.etnindonesia.id/api/wabusiness/text";

		$data = [
			"client_id" => "480650881798248",
			"project_id" => "20112401",
			"type" => "info_uang_saku",
			"recipient_number" => $number,
			"language_code" => "id",
			"params" => [
				"1" => $nama,
				"2" => "uang saku",
				"3" => $tgl_tarik,
				"4" => "Rp " . $total,
				"5" => "Tetap semangat dan terus berprestasi!"
			],
			"header" => [
				"param" => SCHOOL_NAME
			]
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Accept: application/json',
			'Authorization: Basic c2Vrb2xhaGt1YXBwaW5kb3NhdDplOGIyZjk5ZTlhZjcyNTY2M2NlMTFlOGI2MjRlMjhhMA=='
		]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			//echo 'Error:' . curl_error($ch);
			$query = "
				INSERT INTO 
					ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
				VALUES
					( '$number', '$log_pesan', '$response', '01', '$sesi', '750', '9' )
			";
			$ins = db_query($query);
		} else {
			//echo $response;
			$query = "
				INSERT INTO 
					ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
				VALUES
					( '$number', '$log_pesan', '$response', '200', '$sesi', '750', '9' )
			";
			$ins = db_query($query);
		}

		curl_close($ch);
	}
	/* END API ETN META */

	function load_list_siswa_cari()
	{
		$pt = $_POST;
		$sql = "SELECT
					data_siswa.id_siswa,
					data_siswa.nama_siswa,
					data_siswa.gender,
					data_siswa.nis_siswa as nis,
					data_siswa.no_kartu,
					nama_kelas
				FROM
					data_siswa
					LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN  data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas
				WHERE
					data_kelas.id_jenjang = $pt[id_jenjang]
					AND data_kelas.id_sesi = $pt[id_ta]
				ORDER BY 
					nama_kelas, nama_siswa";
		$res = db_query($sql);
		$no = 1;
		$tbody = '';
		while ($dt = mysqli_fetch_array($res)) {
			$gender = $dt['gender'];
			if (stripos($gender, "laki") !== false) {
				$gender = "L";
			}
			if (stripos($gender, "perempuan") !== false) {
				$gender = "P";
			}
			$tbody .= "
			<tr class='cari-item'>
				<td class='cari-no'>" . $no++ . "</td>
				<td class='cari-q'>" . $dt['nama_siswa'] . "</td>
				<td class=''>" . $gender . "</td>
				<td class='cari-q'>" . $dt['nis'] . "</td>
				<td class='cari-q tc'>" . $dt['nama_kelas'] . "</td>
				<td class='cari-q' style='display:none;'>" . $dt['no_kartu'] . "</td>
				<td class='tc'>
						<button class='btn btn-xs btn-dark' onclick='pilih_siswa(" . $dt['id_siswa'] . ");'>
						Pilih</button>
					</td>
			</tr>
			";
		}
		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class='tc no_result'  style='$no_res'>
					<td colspan='5'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Nama Warga</th>
					<th>Gender</th>
					<th>ID</th>
					<th>Wilayah</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>
		";
		echo $table;
	}

	function load_data_siswa()
	{
		$id_user = $_SESSION['id'];
		$id_siswa = $_POST['id_siswa'];
		$id_ta = $_POST['id_ta'];
		$sql = "SELECT
					data_siswa.id_siswa,
					data_siswa.nama_siswa,
					data_kelas.id_kelas,
					data_siswa.gender,
					MAX(nama_kelas) AS nama_kelas,
					tanggal_lahir,
					IF(nis_siswa IS NULL OR nis_siswa ='','-',nis_siswa) as nis_siswa
				FROM
					data_siswa
					LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN  data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas
				WHERE
					data_siswa.id_siswa=$id_siswa
					AND penempatan_siswa.thn_ajar = $id_ta";
		$data = db_assoc($sql);
		$data['tanggal_lahir'] = tgl_indo($data['tanggal_lahir']);
		$data['saldo_us'] = $this->saldo_us($id_siswa);
		$data['saldo_tab'] = $this->saldo_tab($id_siswa);
		$data['id_user'] = $id_user;
		echo json_encode($data);
	}

	function saldo_us($id_siswa)
	{
		global $con;
		$id = $id_siswa;
		$query = mysqli_query($con, "SELECT SUM(jumlah) as top FROM tbl_uangku WHERE id_tbl_status_transaksi=1 AND id_siswa=$id");
		$top = mysqli_fetch_row($query);
		$query_satu = mysqli_query($con, "SELECT SUM(jumlah) as top FROM tbl_uangku WHERE id_tbl_status_transaksi=3 AND id_siswa=$id");
		$top_satu = mysqli_fetch_row($query_satu);
		$sql = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=2 AND id_siswa=$id");
		$cek = mysqli_fetch_row($sql);
		$sql_satu = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=4 AND id_siswa=$id");
		$cek_satu = mysqli_fetch_row($sql_satu);
		$sql_terakhir = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=5 AND id_siswa=$id");
		$cek_terakhir = mysqli_fetch_row($sql_terakhir);
		$saldo = $top[0] + $top_satu[0] - $cek[0] - $cek_satu[0] - $cek_terakhir[0];
		return $saldo;
	}

	function saldo_tab($id_siswa)
	{
		global $con;
		$id = $id_siswa;
		$query = mysqli_query($con, "SELECT IFNULL(SUM(nominal_tab),0) as tab FROM tabungan WHERE nis_from_idsiswa=$id");
		$top = mysqli_fetch_row($query);
		$que = mysqli_query($con, "SELECT IFNULL(SUM(nominal_amb),0) as amb FROM tabungan WHERE nis_from_idsiswa=$id");
		$amb = mysqli_fetch_row($que);
		$saldo = $top[0] - $amb[0];
		return $saldo;
	}

	function load_data_tagihan()
	{
		global $con;
		$pt = $_POST;
		$th = $pt['id_ta'];
		$id = $pt['id_siswa'];
		$data = [];
		$sql = "SELECT
						master_jenis_transaksi.nama_transaksi,
						mst_all.nominal,
						mst_all.nis_from_idsiswa,
						mst_all.id_master_trans_sis,
						mst_all.id_jenis_transaksi,
						SUM(IFNULL(nominal_dibayarkan,0)) as nominal_dibayarkan,
						(mst_all.nominal - SUM(IFNULL(nominal_dibayarkan,0))) as kekurangan
					FROM
						master_alokasi_transaksi_all AS mst_all
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = mst_all.id_jenis_transaksi
						LEFT JOIN transaksi_siswa ON mst_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
					WHERE
						mst_all.nis_from_idsiswa = '$id' 
						AND mst_all.id_sesi =  '$th' 
						AND sta IS NULL 
					GROUP BY
						mst_all.id_master_trans_sis 
					ORDER BY
						mst_all.id_jenis_transaksi ASC
					";
		$res = db_query($sql);
		$tbody = "";
		$no = 1;
		while ($dt = mysqli_fetch_array($res)) {
			$btn_hapus = "
						<button class='btn btn-xs btn-danger' onclick='del_ketetapan($dt[id_master_trans_sis]);' title='Hapus'>
							<i class='bx bx-trash '></i></button>";
			if ($dt['nominal'] == $dt['kekurangan']) {
				$dt['kekurangan'] = '-';
			} else {
				$dt['kekurangan'] = rupiah($dt['kekurangan']);

				$btn_hapus = "";
			}
			$tbody .= "
				<tr>
					<td>" . $no++ . "</td>
					<td>" . $dt['nama_transaksi'] . "</td>
					<td>" . rupiah($dt['nominal']) . "</td>
					<td>" . $dt['kekurangan'] . "</td>
					<td>
						<button class='btn btn-xs btn-info' onclick='modal_bayar_trans($dt[id_master_trans_sis]);' >
							<i class='bx bx-money '></i> Bayar</button>
						<button class='btn btn-xs btn-success' onclick='insert_trans_lunas($dt[id_master_trans_sis]);'>
							<i class='bx bx-check '></i> Lunas</button>
						<button class='btn btn-xs btn-dark' onclick='edit_trans($dt[id_master_trans_sis]);' title='Edit'>
							<i class='bx bx-edit '></i></button>
							$btn_hapus
					</td>
				</tr>
			";
		}
		if (mysqli_num_rows($res) == 0) {
			$tbody = "
				<tr>
					<td colspan ='5' class='tc'>Tidak Ada Data</td>
				</tr>";
		}
		$table = "
			<table class='table table-sm m-0'>
				<thead>
					<tr>
						<th>#</th>
						<th>Nama Iuran</th>
						<th>Nominal Iuran</th>
						<th>Kekurangan</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					$tbody
				</tbody>
			</table>
		";
		$data['table'] = $table;


		///BLINK TAGIHAN
		$tagihan = db_query("SELECT SUM(nominal),nama_tahunajar FROM master_alokasi_transaksi_all 
				INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar=master_alokasi_transaksi_all.id_sesi WHERE nis_from_idsiswa='$id' AND id_sesi !='$th' AND sta IS NULL
				GROUP BY id_sesi
			");
		$count = mysqli_num_rows($tagihan);
		$data['blink'] = '';
		if ($count > 0) {
			$data['blink'] = '<blink> Masih memiliki tagihan di tahun ajaran </blink>';
			while ($cek = mysqli_fetch_array($tagihan)) {
				$data['blink'] .= '<blink>' . $cek['nama_tahunajar'] . ' ; </blink>';
			}
		}

		//TOTAL TAGIHAN

		$que = "
			SELECT
				tb_ketetapan.*,
				tb_bayar.bayar,
				tb_ketetapan.jml - IFNULL( tb_bayar.bayar, 0 ) AS tagihan 
			FROM
				(
			SELECT
				master_alokasi_transaksi_all.id_master_trans_sis,
				nama_siswa,
				nis_from_idsiswa,
				nama_transaksi,
				no_va,
				SUM( nominal ) AS jml 
			FROM
				master_alokasi_transaksi_all
				INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
				INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi
				LEFT JOIN data_va_uangsaku ON data_va_uangsaku.nis_dari_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
			WHERE
				master_alokasi_transaksi_all.nis_from_idsiswa =$id
				AND master_alokasi_transaksi_all.id_sesi=$th
				) AS tb_ketetapan
				LEFT JOIN (
			SELECT
				master_alokasi_transaksi_all.id_master_trans_sis,
				master_alokasi_transaksi_all.nis_from_idsiswa,
				SUM( nominal_dibayarkan ) AS bayar 
			FROM
				transaksi_siswa
				INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
			WHERE
				master_alokasi_transaksi_all.nis_from_idsiswa =$id
				AND master_alokasi_transaksi_all.id_sesi=$th
				) AS tb_bayar ON tb_bayar.nis_from_idsiswa = tb_ketetapan.nis_from_idsiswa
			";
		$resu = db_assoc($que);

		$data['total_tagihan'] = rupiah($resu['tagihan']);

		echo json_encode($data);
	}

	function load_data_trans()
	{
		$id_transaksi = $_POST['id_trans'];
		$sql = "SELECT
						master_jenis_transaksi.nama_transaksi,
						mst_all.nominal,
						mst_all.nis_from_idsiswa,
						mst_all.id_master_trans_sis,
						mst_all.id_jenis_transaksi,
						SUM(IFNULL(nominal_dibayarkan,0)) as nominal_dibayarkan,
						(mst_all.nominal - SUM(IFNULL(nominal_dibayarkan,0))) as kekurangan,
						urut as id_kat
					FROM
						master_alokasi_transaksi_all AS mst_all
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = mst_all.id_jenis_transaksi
						LEFT JOIN transaksi_siswa ON mst_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
					WHERE
						mst_all.id_master_trans_sis = $id_transaksi
						AND sta IS NULL 
					GROUP BY
						mst_all.id_master_trans_sis 
					ORDER BY
						mst_all.id_jenis_transaksi ASC
					";
		$data = db_assoc($sql);
		echo json_encode($data);
	}

	function insert_trans_lunas()
	{
		global $con;
		$sesi = $_SESSION['id'];

		$pt = $_POST;
		$sql = "
			SELECT
				master_jenis_transaksi.nama_transaksi,
				mst_all.nominal,
				mst_all.nis_from_idsiswa,
				mst_all.id_master_trans_sis,
				mst_all.id_jenis_transaksi,
				SUM( nominal_dibayarkan ) AS nominal_dibayarkan,
				(
				mst_all.nominal - SUM( nominal_dibayarkan )) AS kekurangan,
				urut,
				mst_all.id_kelas
			FROM
				master_alokasi_transaksi_all AS mst_all
				INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = mst_all.id_jenis_transaksi
				LEFT JOIN transaksi_siswa ON mst_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
			WHERE
				mst_all.id_master_trans_sis = $pt[id_mst_trans]
			GROUP BY
				mst_all.id_master_trans_sis 
		";
		$_GET = db_assoc($sql);
		json_encode($_GET);

		$id_jenis_transaksi = isset($_GET['id_jenis_transaksi']) ? $_GET['id_jenis_transaksi'] : "";
		$nis_from_idsiswa = isset($_GET['nis_from_idsiswa']) ? $_GET['nis_from_idsiswa'] : "";
		$id_master_trans_sis = isset($_GET['id_master_trans_sis']) ? $_GET['id_master_trans_sis'] : "";
		$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : "";
		$nominal = isset($_GET['nominal']) ? $_GET['nominal'] : "";
		$nominal_dibayarkan = isset($_GET['nominal_dibayarkan']) ? $_GET['nominal_dibayarkan'] : "";
		$id_kel = isset($_GET['urut']) ? $_GET['urut'] : "";
		$sql = mysqli_query($con, "SELECT * FROM transaksi_siswa_sementara WHERE id_jenis_transaksi='$id_jenis_transaksi' AND id_user='$sesi'");
		$count = mysqli_num_rows($sql);
		$tagihans = "
			SELECT
				tagihan.nominal,
				IFNULL(bayar.total,0) as total  
			FROM
				( SELECT id_master_trans_sis, nominal FROM master_alokasi_transaksi_all WHERE id_master_trans_sis = '$id_master_trans_sis' ) AS tagihan
				LEFT JOIN ( SELECT id_master_trans_sis, SUM( nominal_dibayarkan ) AS total FROM transaksi_siswa GROUP BY id_master_trans_sis ) AS bayar ON bayar.id_master_trans_sis = tagihan.id_master_trans_sis
		";
		$dat = mysqli_query($con, $tagihans);
		while ($rs = mysqli_fetch_array($dat)) {
			$kurang = $rs['nominal'] - $rs['total'];
			if ($id_kel != "") {
				$a = ",id_kel";
				$b = ",'$id_kel'";
			} else {
				$a = "";
				$b = "";
			}
			if ($count < 1) {
				$query = "
					INSERT INTO transaksi_siswa_sementara(nis_from_idsiswa,id_master_trans_sis,id_kelas,id_jenis_transaksi,nominal_ketetapan,id_status,nominal_dibayarkan,id_user,ket$a,nominal_kekurangan,infaq,tipe) 
					VALUES (
					'$nis_from_idsiswa',
					'$id_master_trans_sis',
					'$id_kelas',
					'$id_jenis_transaksi',
					'$nominal',
					'1',
					'$kurang',
					'$sesi',
					'teller'
					$b,
					'0',
					'0',
					'')
				";
				$send = mysqli_query($con, $query);
			} else if ($count > 0) {
				echo 'Maaf data sudah ada';
			}
		}
	}

	function simpan_transaksi()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$id_siswa = $_POST['id_siswa'];
		$id_jenjang = $_POST['id_jenjang'];
		$id_order = db_get("SELECT id_order FROM idorder WHERE id_user='$sesi' ORDER BY id_order DESC LIMIT 1");
		if (!is_null($id_order)) {
			$sql = "SELECT
						id_transaksi,
						nis_from_idsiswa,
						id_master_trans_sis,
						id_kelas,
						transaksi_siswa_sementara.id_jenis_transaksi,
						master_jenis_transaksi.nama_transaksi as transaksi,
						nominal_ketetapan,
						disc,
						nominal_dibayarkan,
						infaq,
						nominal_kekurangan as kekurangan,
						ket as jns, 
						master_jenis_transaksi.id_sesi as th,
						id_kel 
					FROM
						transaksi_siswa_sementara
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa_sementara.id_jenis_transaksi 
					WHERE
						id_user = '$sesi' 
					ORDER BY
						master_jenis_transaksi.id_jenis_transaksi ASC";
			$res = db_query($sql);
			while ($dt = mysqli_fetch_array($res)) {
				$_GET = $dt;
				$_GET['id_where'] = $id_order;
				$_GET['id_whereku'] = $id_order;
				if ($dt['kekurangan'] == 0) {
					$this->edit_lun();
				}
				$this->simpan_real_transaksi();
			}

			// cek konfig notif
			$cek_notif = "SELECT id, notif_transaksi FROM tbl_wappin_konfig WHERE id_jenjang = $id_jenjang LIMIT 1";
			$res_cek_notif = db_query($cek_notif);
			if (mysqli_num_rows($res_cek_notif) > 0) {
				while ($x = mysqli_fetch_array($res_cek_notif)) {
					if ($x['notif_transaksi'] == "1") {
						$this->wappin_notif_byr($id_siswa, $id_order);
					}
				}
			}

			$this->orderid();
			$data = [];
			$data['id_order'] = $id_order;
			echo json_encode($data);
		} else {
			echo "User belum diverifikasi";
		}
	}

	function edit_lun()
	{
		global $con;
		$id_master_trans_sis = isset($_GET['id_master_trans_sis']) ? $_GET['id_master_trans_sis'] : "";
		$sql = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET sta='1' WHERE id_master_trans_sis=$id_master_trans_sis");
	}

	function simpan_real_transaksi()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$a = isset($_GET['nis_from_idsiswa']) ? $_GET['nis_from_idsiswa'] : "";
		$b = isset($_GET['id_master_trans_sis']) ? $_GET['id_master_trans_sis'] : "";
		$c = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : "";
		$d = isset($_GET['id_jenis_transaksi']) ? $_GET['id_jenis_transaksi'] : "";
		$e = isset($_GET['nominal_ketetapan']) ? $_GET['nominal_ketetapan'] : "";
		$g = isset($_GET['nominal_dibayarkan']) ? $_GET['nominal_dibayarkan'] : "";
		$h = isset($_GET['infaq']) ? $_GET['infaq'] : "";
		$i = isset($_GET['id_where']) ? $_GET['id_where'] : "";
		$kekurangan = isset($_GET['kekurangan']) ? $_GET['kekurangan'] : "";
		$jns = isset($_GET['jns']) ? $_GET['jns'] : "";
		$trans = isset($_GET['transaksi']) ? $_GET['transaksi'] : "";
		$id_kel = isset($_GET['id_kel']) ? $_GET['id_kel'] : "";
		if ($jns == 'Ikhlas') {
			$mtd = 'teller';
			$kekurangan = 0;
			$e = $g;
		} else if ($jns == 'Bank BRI') {
			$mtd = 'teller';
			// $kekurangan=0;
			$e = $g;
		} else if ($jns == 'Bank BNI') {
			$mtd = 'teller';
			// $kekurangan=0;
			$e = $g;
		} else if ($jns == 'Bank Mandiri') {
			$mtd = 'teller';
			// $kekurangan=0;
			$e = $g;
		} else if ($jns == 'Bank BCA') {
			$mtd = 'teller';
			// $kekurangan=0;
			$e = $g;
		} else if ($jns == 'Ikhlas Saku') {
			$mtd = 'Uang Saku';
			$kekurangan = 0;
			$e = $g;
		} else {
			$mtd = $jns;
		}

		if ($jns == 'Bank BRI') {
			$send = mysqli_query($con, "INSERT INTO transaksi_siswa(nis_from_idsiswa,id_master_trans_sis,id_kelas,id_jenis_transaksi,nominal_ketetapan,nominal_dibayarkan,infaq,id_where,print,kekurangan,id_user,thn_aj,office,ket) 
			VALUES (
			'$a',
			'$b',
			'$c',
			'$d',
			'$e',
			'$g',
			'$h',
			'$i',
			'F',
			'$kekurangan',
			'$sesi',
			'$th',
			'$mtd',
			'$jns')");
		} else if ($jns == 'Bank BNI') {
			$send = mysqli_query($con, "INSERT INTO transaksi_siswa(nis_from_idsiswa,id_master_trans_sis,id_kelas,id_jenis_transaksi,nominal_ketetapan,nominal_dibayarkan,infaq,id_where,print,kekurangan,id_user,thn_aj,office,ket) 
			VALUES (
			'$a',
			'$b',
			'$c',
			'$d',
			'$e',
			'$g',
			'$h',
			'$i',
			'F',
			'$kekurangan',
			'$sesi',
			'$th',
			'$mtd',
			'$jns')");
		} else if ($jns == 'Bank Mandiri') {
			$send = mysqli_query($con, "INSERT INTO transaksi_siswa(nis_from_idsiswa,id_master_trans_sis,id_kelas,id_jenis_transaksi,nominal_ketetapan,nominal_dibayarkan,infaq,id_where,print,kekurangan,id_user,thn_aj,office,ket) 
			VALUES (
			'$a',
			'$b',
			'$c',
			'$d',
			'$e',
			'$g',
			'$h',
			'$i',
			'F',
			'$kekurangan',
			'$sesi',
			'$th',
			'$mtd',
			'$jns')");
		} else if ($jns == 'Bank BCA') {
			$send = mysqli_query($con, "INSERT INTO transaksi_siswa(nis_from_idsiswa,id_master_trans_sis,id_kelas,id_jenis_transaksi,nominal_ketetapan,nominal_dibayarkan,infaq,id_where,print,kekurangan,id_user,thn_aj,office,ket) 
			VALUES (
			'$a',
			'$b',
			'$c',
			'$d',
			'$e',
			'$g',
			'$h',
			'$i',
			'F',
			'$kekurangan',
			'$sesi',
			'$th',
			'$mtd',
			'$jns')");
		} else {
			$send = mysqli_query($con, "INSERT INTO transaksi_siswa(nis_from_idsiswa,id_master_trans_sis,id_kelas,id_jenis_transaksi,nominal_ketetapan,nominal_dibayarkan,infaq,id_where,print,kekurangan,id_user,thn_aj,office) 
			VALUES (
			'$a',
			'$b',
			'$c',
			'$d',
			'$e',
			'$g',
			'$h',
			'$i',
			'F',
			'$kekurangan',
			'$sesi',
			'$th',
			'$mtd')");
		}
		$id_trans = mysqli_insert_id($con);
		$total = 0;
		if ($jns == 'Uang Saku') {
			$total = $g + $h;
			$id_tbl_uangku = mysqli_insert_id($con);
			if ($id_kel == '') {
				$id_kategori_tr = 'null';
				$sql = mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa,id_tbl_status_transaksi,jumlah,keterangan,id_trans,id_kel)VALUES('$a','2','$total','$trans','$id_trans','id_$id_tbl_uangku')");
			} else {
				$sql = mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa,id_tbl_status_transaksi,jumlah,keterangan,id_trans,id_kel)VALUES('$a','2','$total','$trans','$id_trans','$id_kel')");
			}
		} else if ($jns == 'tabungan') {
			$total = $g + $h;
			$query = mysqli_query($con, "SELECT IFNULL(SUM(nominal_tab),0) as tab FROM tabungan WHERE nis_from_idsiswa=$a");
			$top = mysqli_fetch_row($query);
			$que = mysqli_query($con, "SELECT IFNULL(SUM(nominal_amb),0) as amb FROM tabungan WHERE nis_from_idsiswa=$a");
			$amb = mysqli_fetch_row($que);
			$saldo = $top[0] - $amb[0];
			$tgl = date('Y-m-d');
			$saldoakhir = $saldo - $g;
			$sql = mysqli_query($con, "INSERT INTO tabungan(nominal_tab,nominal_amb,saldo,nis_from_idsiswa,st,thn_ajar,tgl,id_trans,ket)VALUES('0','$total','$saldoakhir','$a','F','$th','$tgl','$id_trans','$trans')");
		} else if ($jns == 'Ikhlas') {
			$ins = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$g',sta=1 WHERE id_master_trans_sis=$b");
		} else if ($jns == 'Ikhlas Saku') {
			$ins = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$g',sta=1 WHERE id_master_trans_sis=$b");
			$total = $g + $h;
			$sql = mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa,id_tbl_status_transaksi,jumlah,keterangan,id_trans,id_kel)VALUES('$a','2','$total','$trans','$id_trans','$id_kel')");
		}

		$send = mysqli_query($con, "INSERT INTO idorder(object,id_user) VALUES ('$a','$sesi')");
	}

	function orderid($nis_from_idsiswa = null)
	{
		global $con;
		$sesi = $_SESSION['id'];
		$a = isset($_GET['nis_from_idsiswa']) ? $_GET['nis_from_idsiswa'] : "";
		$send = mysqli_query($con, "INSERT INTO idorder(object,id_user) 
		VALUES ('$a','$sesi')");
	}

	function load_trans_sementara()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$sqlx = "SELECT id_order FROM idorder WHERE id_user='$sesi' ORDER BY id_order DESC LIMIT 1";
		$result = mysqli_query($con, $sqlx);
		$sql = "SELECT
					id_transaksi,
					nis_from_idsiswa,
					id_master_trans_sis,
					id_kelas,
					transaksi_siswa_sementara.id_jenis_transaksi,
					master_jenis_transaksi.nama_transaksi,
					nominal_ketetapan,
					disc,
					nominal_dibayarkan,
					infaq,
					id_order,
					nominal_kekurangan,
					ket,
					id_kel 
				FROM
					transaksi_siswa_sementara
					INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa_sementara.id_jenis_transaksi 
				WHERE
					id_user = '$sesi' 
				ORDER BY
					master_jenis_transaksi.id_jenis_transaksi ASC";
		$res = db_query($sql);
		$no = 1;
		$tbody = '';
		$total = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$tbody .= "
				<tr>
					<td>" . $no++ . "</td>
					<td>" . $dt['nama_transaksi'] . "</td>
					<td>" . $dt['nominal_ketetapan'] . "</td>
					<td>" . rupiah($dt['nominal_dibayarkan']) . "</td>
					<td>" . $dt['infaq'] . "</td>
					<td>" . $dt['nominal_kekurangan'] . "</td>
					<td>
						<button class='btn btn-xs btn-danger' onclick='del_trans_sementara($dt[id_transaksi]);' title='Hapus'>
							<i class='bx bx-trash '></i> Hapus
						</button>
					</td>
				</tr>";
			$total += $dt['nominal_dibayarkan'];
		}
		$tfoot = "
			<tr>
				<th colspan='3' style='text-align:right;'>Total</th>
				<th>" . rupiah($total) . "</th>
				<th colspan='3'><span style='display:none;' id='total_dibayar'>$total</span></th>
			</tr>
		";
		if (mysqli_num_rows($res) == 0) {
			$tbody = "
				<tr>
					<td colspan ='6' class='tc'>Tidak Ada Data</td>
				</tr>";
			$tfoot = '';
		}
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama Iuran</th>
					<th>Nominal Iuran</th>
					<th>Nominal Dibayarkan</th>
					<th>Donasi</th>
					<th>Kekurangan</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody 
			</tbody>
			<tfoot>
				$tfoot
			</tfoot>
			</table>";
		echo $table;
	}

	function del_trans_sementara()
	{
		$id_transaksi = post('id_transaksi');
		$wh_id = '';
		if ($id_transaksi != '') {
			$wh_id = " AND id_transaksi = $id_transaksi";
		}
		$sesi = $_SESSION['id'];
		$sql = "DELETE FROM transaksi_siswa_sementara WHERE id_user='$sesi' $wh_id";
		$res = db_query($sql);
	}

	function del_trans_sementara_sis()
	{
		$id_mst = post('id_mst');
		$sesi = $_SESSION['id'];
		$sql = "DELETE FROM transaksi_siswa_sementara WHERE id_master_trans_sis = $id_mst";
		$res = db_query($sql);
	}

	function edit_trans_save()
	{
		$pt = $_POST;
		if ($pt['terbayarkan'] == $pt['nominal_baru']) {
			$sta = ", sta ='1'";
		} else {
			$sta = '';
		}
		$quer = "UPDATE master_alokasi_transaksi_all SET nominal='$pt[nominal_baru]' $sta WHERE id_master_trans_sis=$pt[id_trans]";
		$res = db_query($quer);

		$cb['stat'] = 0;
		$cb['ket'] = '';
		if ($res) {
			$cb['stat'] = 1;
		} else {
			$cb['ket'] = 'Error';
		}
		echo json_encode($cb);
	}

	function cetak_first()
	{
		ob_start();
		require('app/lib/html_table.php');
		global $con;
		$th = isset($_GET['id_ta']) ? $_GET['id_ta'] : "";
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		//$sd = mysql_query("UPDATE transaksi_siswa SET print='T' WHERE id_where=$id");
		$nis = isset($_GET['id_siswa']) ? $_GET['id_siswa'] : "";
		$sql = "SELECT transaksi_siswa.id_transaksi,
			transaksi_siswa.nis_from_idsiswa,
			transaksi_siswa.id_master_trans_sis,
			transaksi_siswa.id_kelas,
			transaksi_siswa.id_jenis_transaksi,
			transaksi_siswa.nominal_ketetapan,
			transaksi_siswa.disc,
			transaksi_siswa.id_status,
			transaksi_siswa.nominal_dibayarkan,
			transaksi_siswa.infaq,
			transaksi_siswa.id_where,
			transaksi_siswa.print,
			transaksi_siswa.tgl,
			transaksi_siswa.kekurangan,
			transaksi_siswa.id_user,
			transaksi_siswa.thn_aj,
			transaksi_siswa.office,
			master_jenis_transaksi.nama_transaksi,
			IFNULL( SUBSTRING( users_log.username, 1, 3 ), 'ol' ) AS nama  FROM transaksi_siswa 
			LEFT JOIN users_log ON users_log.id=transaksi_siswa.id_user
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE nis_from_idsiswa='$nis' AND print='F' AND thn_aj='$th' ORDER BY transaksi_siswa.tgl,transaksi_siswa.id_jenis_transaksi ASC";
		$result = mysqli_query($con, $sql);
		$sqltot = "SELECT transaksi_siswa.id_transaksi,
			transaksi_siswa.nis_from_idsiswa,
			transaksi_siswa.id_master_trans_sis,
			transaksi_siswa.id_kelas,
			transaksi_siswa.id_jenis_transaksi,
			transaksi_siswa.nominal_ketetapan,
			transaksi_siswa.disc,
			transaksi_siswa.id_status,
			transaksi_siswa.nominal_dibayarkan,
			transaksi_siswa.infaq,
			transaksi_siswa.id_where,
			transaksi_siswa.print,
			transaksi_siswa.tgl,
			transaksi_siswa.kekurangan,
			transaksi_siswa.id_user,
			transaksi_siswa.thn_aj,
			transaksi_siswa.office,
			master_jenis_transaksi.nama_transaksi,
			IFNULL( SUBSTRING( users_log.username, 1, 3 ), 'ol' ) AS nama  FROM transaksi_siswa 
			INNER JOIN users_log ON users_log.id=transaksi_siswa.id_user
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE nis_from_idsiswa='$nis' AND thn_aj='$th' ORDER BY transaksi_siswa.tgl,transaksi_siswa.id_jenis_transaksi ASC";
		$result2 = mysqli_query($con, $sqltot);
		$sql = mysqli_query($con, "UPDATE transaksi_siswa SET print='T' WHERE nis_from_idsiswa=$nis");

		$pdf = new PDF_HTML_Table();
		$pdf->AddPage();
		$pdf->SetMargins(14, 0);
		$pdf->ln(67);
		$no = 1;
		while ($rowe = mysqli_fetch_array($result2)) {
			if ($no == 31) {
				$pdf->AddPage();
				$pdf->ln(19);
			}
			$date = $rowe['tgl'];
			$tgl = date("d-m-Y", strtotime($date));
			if ($rowe['print'] == "T") {
				$pdf->SetFont('Arial', '', 19);
				$pdf->SetTextColor(255, 255, 255);

				$pdf->Cell(20, 4, $tgl, 0, 'L');
				$pdf->Cell(58, 4, $rowe['nama_transaksi'], 0, 'L');
				$pdf->Cell(30, 4, number_format($rowe['nominal_dibayarkan']), 0, 'L');
				$pdf->Cell(20, 4, number_format($rowe['kekurangan']), 0, 'L');
				$pdf->Cell(1, 4, $rowe['nama'], 0, 'L');
				if ($rowe['infaq'] == 0) {
					$pdf->Cell(1, 4, '', 0, 'L');
				} else {
					$pdf->ln(4);
					$pdf->Cell(25, 4, $tgl, '', 0, 'L');
					$pdf->Cell(45, 4, 'Infaq', '', 0, 'L');
					$pdf->Cell(85, 4, '' . number_format($row['infaq']), 0, 'L');
				}
				$pdf->ln(4);
				$no++;
			}
		}
		while ($row = mysqli_fetch_array($result)) {
			if ($no == 31) {
				$pdf->AddPage();
				$pdf->ln(19);
			}
			$date = $row['tgl'];
			$tgl = date("d-m-Y", strtotime($date));
			$pdf->SetFont('Arial', '', 9);
			$pdf->SetTextColor(0, 0, 0);
			//$pdf->Cell(1, 4, $no, '', 0, 'L'); 
			//$pdf->Cell(1, 4, '.', '', 0, 'L');
			$pdf->Cell(23, 4, $tgl, 0, 'L');
			$pdf->Cell(58, 4, $row['nama_transaksi'], 0, 'L');
			$pdf->Cell(30, 4, number_format($row['nominal_dibayarkan']), 0, 'L');
			$pdf->Cell(20, 4, number_format($row['kekurangan']), 0, 'L');
			$pdf->Cell(1, 4, $row['nama'], 0, 'L');
			if ($row['infaq'] == 0) {
				$pdf->Cell(1, 4, '', 0, 'L');
			} else {
				$pdf->ln(4);
				$pdf->Cell(25, 4, $tgl, '', 0, 'L');
				$pdf->Cell(45, 4, 'Infaq', '', 0, 'L');
				$pdf->Cell(85, 4, '' . number_format($row['infaq']), 0, 'L');
			}
			$pdf->ln(4);
			$no++;
		}
		ob_end_clean();
		$pdf->Output();
	}

	function cetak_again($nis = null, $th = null)
	{
		ob_start();
		require('app/lib/html_table.php');
		global $con;
		$th = isset($_GET['id_ta']) ? $_GET['id_ta'] : "";
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		//$sd = mysql_query("UPDATE transaksi_siswa SET print='T' WHERE id_where=$id");
		$nis = isset($_GET['id_siswa']) ? $_GET['id_siswa'] : "";
		$sql = "SELECT transaksi_siswa.id_transaksi,
			transaksi_siswa.nis_from_idsiswa,
			transaksi_siswa.id_master_trans_sis,
			transaksi_siswa.id_kelas,
			transaksi_siswa.id_jenis_transaksi,
			transaksi_siswa.nominal_ketetapan,
			transaksi_siswa.disc,
			transaksi_siswa.id_status,
			transaksi_siswa.nominal_dibayarkan,
			transaksi_siswa.infaq,
			transaksi_siswa.id_where,
			transaksi_siswa.print,
			transaksi_siswa.tgl,
			transaksi_siswa.kekurangan,
			transaksi_siswa.id_user,
			transaksi_siswa.thn_aj,
			transaksi_siswa.office,
			master_jenis_transaksi.nama_transaksi,
			IFNULL( SUBSTRING( users_log.username, 1, 3 ), 'ol' ) AS nama FROM transaksi_siswa 
			LEFT JOIN users_log ON users_log.id=transaksi_siswa.id_user
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE nis_from_idsiswa='$nis' AND print='F' AND thn_aj='$th' ORDER BY transaksi_siswa.tgl,transaksi_siswa.id_jenis_transaksi ASC";
		$result = mysqli_query($con, $sql);
		$sqltot = "SELECT transaksi_siswa.id_transaksi,
			transaksi_siswa.nis_from_idsiswa,
			transaksi_siswa.id_master_trans_sis,
			transaksi_siswa.id_kelas,
			transaksi_siswa.id_jenis_transaksi,
			transaksi_siswa.nominal_ketetapan,
			transaksi_siswa.disc,
			transaksi_siswa.id_status,
			transaksi_siswa.nominal_dibayarkan,
			transaksi_siswa.infaq,
			transaksi_siswa.id_where,
			transaksi_siswa.print,
			transaksi_siswa.tgl,
			transaksi_siswa.kekurangan,
			transaksi_siswa.id_user,
			transaksi_siswa.thn_aj,
			transaksi_siswa.office,
			master_jenis_transaksi.nama_transaksi,
			IFNULL( SUBSTRING( users_log.username, 1, 3 ), 'ol' ) AS nama FROM transaksi_siswa 
			LEFT JOIN users_log ON users_log.id=transaksi_siswa.id_user
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE nis_from_idsiswa='$nis' AND thn_aj='$th' ORDER BY transaksi_siswa.tgl,transaksi_siswa.id_jenis_transaksi ASC";
		$result2 = mysqli_query($con, $sqltot);

		$pdf = new PDF_HTML_Table();
		$pdf->AddPage();
		$pdf->SetMargins(14, 0);
		$pdf->ln(67);
		$no = 1;
		while ($rowe = mysqli_fetch_array($result2)) {
			if ($no == 31) {
				$pdf->AddPage();
				$pdf->ln(19);
			}
			$date = $rowe['tgl'];
			$tgl = date("d-m-Y", strtotime($date));
			if ($rowe['print'] == "T") {
				$pdf->SetFont('Arial', '', 8);
				$pdf->SetTextColor(0, 0, 0);

				$pdf->SetFont('Arial', '', 9);
				$pdf->SetTextColor(0, 0, 0);
				//$pdf->Cell(1, 4, $no, '', 0, 'L'); 
				//$pdf->Cell(1, 4, '.', '', 0, 'L');
				$pdf->Cell(23, 4, $tgl, 0, 'L');
				$pdf->Cell(58, 4, $rowe['nama_transaksi'], 0, 'L');
				$pdf->Cell(30, 4, number_format($rowe['nominal_dibayarkan']), 0, 'L');
				$pdf->Cell(20, 4, number_format($rowe['kekurangan']), 0, 'L');
				$pdf->Cell(1, 4, $rowe['nama'], 0, 'L');
				if ($rowe['infaq'] == 0) {
					$pdf->Cell(1, 4, '', 0, 'L');
				} else {
					$pdf->SetFont('Arial', '', 9);
					$pdf->Cell(1, 4, ',Infaq ' . number_format($rowe['infaq']), 0, 'L');
				}
				$pdf->ln(4);
				$no++;
			}
		}
		while ($row = mysqli_fetch_array($result)) {
			if ($no == 31) {
				$pdf->AddPage();
				$pdf->ln(19);
			}
			$date = $row['tgl'];
			$tgl = date("d-m-Y", strtotime($date));
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetTextColor(0, 0, 0);

			$pdf->Cell(30, 4, $tgl, '', 0, 'L');
			$pdf->Cell(51, 4, 'TRANSAKSI INI HARUS DI CETAK DULU', '', 0, 'L');
			$pdf->Cell(16, 4, '', '', 0, 'L');
			if ($row['infaq'] == 0) {
				$pdf->Cell(1, 4, '', 0, 'L');
			} else {
				$pdf->SetFont('Arial', '', 9);
				$pdf->Cell(1, 4, ',Infaq ' . number_format($rowe['infaq']), 0, 'L');
			}
			$pdf->ln(4);
			$no++;
		}
		ob_end_clean();
		$pdf->Output();
	}

	function cetak_nota()
	{
		ob_start();
		require_once('app/lib/tcpdf/tcpdf.php');
		global $con;
		$id = isset($_GET['id_order']) ? $_GET['id_order'] : "";
		$th = isset($_GET['id_ta']) ? $_GET['id_ta'] : "";

		//$pdf=new PDF_HTML_Table();
		//$pdf=new FPDF('P','mm',array(87,500));

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006f', PDF_HEADER_STRING);
		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf = new TCPDF('P', 'mm', array(87, 500));
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetFont('times', '', 10);
		$pdf->setMargins(10, 10, 15, true);
		$pdf->AddPage();
		$sqltot = "
			SELECT
				*,
				transaksi_siswa.tgl AS tangg,
				kategori_trans.nama_kategori
			FROM
				transaksi_siswa
				INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
				LEFT JOIN kategori_trans ON kategori_trans.id_kat_trans = master_jenis_transaksi.urut
			WHERE
				id_where = '$id'
			ORDER BY
				transaksi_siswa.id_jenis_transaksi ASC
		";
		$sqltotdua = "select *,transaksi_siswa.tgl as tangg,SUM(nominal_dibayarkan) as bayar from transaksi_siswa 
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE id_where='$id' AND status_kelompok='1' GROUP BY kel ORDER BY transaksi_siswa.id_jenis_transaksi ASC";
		$sqltott = "select * from transaksi_siswa 
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			inner join data_siswa ON data_siswa.id_siswa=transaksi_siswa.nis_from_idsiswa
			inner join penempatan_siswa ON penempatan_siswa.nis_from_idsiswa=transaksi_siswa.nis_from_idsiswa
			inner join data_kelas ON data_kelas.id_kelas=transaksi_siswa.id_kelas
			inner join data_jenjang ON data_jenjang.id_jenjang=data_siswa.id_jenjang
			WHERE id_where='$id' ORDER BY transaksi_siswa.id_jenis_transaksi ASC";
		$sqltottinfaq = "select SUM(infaq) as info from transaksi_siswa 
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			inner join data_siswa ON data_siswa.id_siswa=transaksi_siswa.nis_from_idsiswa
			inner join penempatan_siswa ON penempatan_siswa.nis_from_idsiswa=transaksi_siswa.nis_from_idsiswa
			inner join data_kelas ON data_kelas.id_kelas=transaksi_siswa.id_kelas
			WHERE id_where='$id' ORDER BY transaksi_siswa.id_jenis_transaksi ASC";
		$sqltotal = "select SUM(nominal_dibayarkan) as nom from transaksi_siswa 
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			inner join data_siswa ON data_siswa.id_siswa=transaksi_siswa.nis_from_idsiswa
			inner join penempatan_siswa ON penempatan_siswa.nis_from_idsiswa=transaksi_siswa.nis_from_idsiswa
			inner join data_kelas ON data_kelas.id_kelas=transaksi_siswa.id_kelas
			WHERE id_where='$id' AND penempatan_siswa.thn_ajar='$th' ORDER BY transaksi_siswa.id_jenis_transaksi ASC";
		$result2 = mysqli_query($con, $sqltot);
		$result3 = mysqli_query($con, $sqltott);
		$result4 = mysqli_query($con, $sqltotal);
		$resultinfaq = mysqli_query($con, $sqltottinfaq);
		$result5 = mysqli_query($con, $sqltotdua);	
		$pdf->Image('app/lib/komune.png', 10, 7, 15, 15);
		$pdf->SetFont('courierB', '', 11);
		$pdf->Cell(75, 1, 'BUKTI TRANSAKSI', '', 0, 'C');
		$pdf->ln(5);
		$pdf->SetFont('courierB', '', 8);
		$pdf->Cell(75, 4, SCHOOL_NAME_SEME, '', 0, 'C');
		$pdf->ln(4);
		$pdf->Cell(75, 4, '', '', 0, 'C');
		$pdf->SetFont('courierB', '', 12);
		$pdf->SetLineWidth(0.1);
		$pdf->Line(10, 24, 73, 24);
		$pdf->SetLineWidth(0.3);
		$pdf->Line(10, 24.5, 73, 24.5);
		while ($rowet = mysqli_fetch_array($result3)) {
			$pdf->SetFont('times', '', 10);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->ln(6);
			$html = '<table border="0">
			<tr>
			<td width="36px"align="left">Nama</td>
			<td width="400px" align="left">: ' . $rowet['nama_siswa'] . '</td>
			</tr>
			<tr>
			<td width="36px"align="left">Wilayah</td>
			<td width="400px" align="left">: ' . $rowet['nama_kelas'] . '</td>
			</tr>
			</table>';
			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');
			$pdf->SetLineWidth(0.1);
			$pdf->Line(10, 39, 73, 39);
			$pdf->SetLineWidth(0.3);
			$pdf->Line(10, 39.5, 73, 39.5);
			$pdf->SetFont('times', '', 11);
			$pdf->ln(1);
			$no = 1;
			$html = '<table border="1">';
			$total = 0;
			while ($rowe = mysqli_fetch_array($result2)) {
				$date = $rowe['tangg'];
				$tgl = date("d-m-Y", strtotime($date));
				if ($rowe['status_kelompok'] == '0') {
					$html .= '
					<tr>
					<td width="20px">' . $no . '.</td>
					<td width="110px">' . $rowe['nama_transaksi'] . '</td>
					<td style="text-align:right;width:45px;">' . number_format($rowe['nominal_dibayarkan']) . '</td>
					</tr>
					';
					$total += $rowe['nominal_dibayarkan'] + $rowe['infaq'];
					if ($rowe['infaq'] == 0) {
						//$pdf->Cell(1, 4, '', 0, 'L'); 
					} else {
						$no++;
						$html .= '
						<tr>
						<td width="10px">' . $no . '.</td>
						<td width="110px">Infaq</td>
						<td style="text-align:right;width:45px;">' . number_format($rowe['infaq']) . '</td>
						</tr>
						';
					}
					$no++;
				} else {

					$query_jumlah = "SELECT
						SUM( nominal_dibayarkan ) AS akhir 
					FROM
						transaksi_siswa 
						LEFT JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
					WHERE
						id_where = '$id'
						AND status_kelompok = '1'";
					$resultku = mysqli_query($con, $query_jumlah);
					$akhir = 0;
					while ($kb = mysqli_fetch_array($resultku)) {
						$akhir = $kb['akhir'];
					}

					$sqlkedua = "
						SELECT
							*,
							transaksi_siswa.tgl AS tangg,
							kategori_trans.nama_kategori
						FROM
							transaksi_siswa
							INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
							LEFT JOIN kategori_trans ON kategori_trans.id_kat_trans = master_jenis_transaksi.urut
						WHERE
							id_where = '$id'
							AND status_kelompok = '1'
						ORDER BY
							transaksi_siswa.id_jenis_transaksi ASC
							LIMIT 1
					";
					$resultkedua = mysqli_query($con, $sqlkedua);
					$namaku = '';
					while ($jh = mysqli_fetch_array($resultkedua)) {
						$namaku = $jh['nama_kategori'];
					}
					$html .= '
					<tr>
					<td width="20px">' . $no . '.</td>
					<td width="110px">' . $namaku . '</td>
					<td style="text-align:right;width:45px;">' . number_format($akhir) . '</td>
					</tr>
					';

					if ($rowe['infaq'] == 0) {
						//$pdf->Cell(1, 4, '', 0, 'L'); 
					} else {
						$no++;
						$html .= '
						<tr>
						<td width="10px">' . $no . '.</td>
						<td width="110px">Donasi</td>
						<td style="text-align:right;width:45px;">' . number_format($rowe['infaq']) . '</td>
						</tr>
						';
					}
					$no++;
					break;
				}
			}

			$total_seluruh = $total + $akhir;
			$pdf->writeHTML($html, true, false, true, false, '');
			$htmlbb = '<hr >';
			$pdf->writeHTML($htmlbb, true, false, true, false, '');
			$pdf->Cell(63, 4, 'Total : ' . number_format($total_seluruh), '', 0, 'R');
			$pdf->ln(5);
			$pdf->SetFont('times', '', 9);
			$pdf->Cell(0, 0, "ID TRANSAKSI : $id", 0, 1);
			$pdf->write1DBarcode($id, 'UPCE', '', '', '', 5, 0.5, $style, 'N');

			$pdf->SetFont('times', '', 10);
			$pdf->ln(10);
			$pdf->Cell(0, 4, $tgl, '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'Diproses Oleh', '', 0, 'C');
			$pdf->ln(20);
			$pdf->Cell(0, 4, $_SESSION['nama_us'], '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'Transaksi berhasil diproses', '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'Simpan bukti transaksi ini', '', 0, 'C');
			$no++;
			ob_end_clean();
			$pdf->Output();
		}
	}

	function load_verifikasi()
	{
		global $con;
		$th = $_POST['id_ta'];
		$nis = $_POST['id_siswa'];
		$sqls = "SELECT
					transaksi_siswa.id_transaksi,
					transaksi_siswa.nis_from_idsiswa,
					master_jenis_transaksi.nama_transaksi,
					transaksi_siswa.nominal_dibayarkan,
					transaksi_siswa.id_where,
					users_log.nama_us,
					transaksi_siswa.office,
					transaksi_siswa.id_master_trans_sis,
					transaksi_siswa.id_user,
					transaksi_siswa.tgl AS tangg,
					transaksi_siswa.ket,
					data_tahunajar.id_tahunajar 
				FROM
				transaksi_siswa
				INNER JOIN data_kelas ON data_kelas.id_kelas = transaksi_siswa.id_kelas
				INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
				INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar= transaksi_siswa.thn_aj 
				LEFT JOIN users_log ON users_log.id=transaksi_siswa.id_user
			WHERE
				transaksi_siswa.nis_from_idsiswa = '$nis' 
				AND transaksi_siswa.thn_aj = '$th' 
			ORDER BY
				transaksi_siswa.tgl ASC,
				transaksi_siswa.id_jenis_transaksi ASC";
		// echo $sqls;
		$result = mysqli_query($con, $sqls);
		$thn = "select * FROM data_tahunajar
			WHERE id_tahunajar='$th'";
		$no = 0;
		$total = 0;
		$tangg = "";
		$tbody = '';
		while ($cari = mysqli_fetch_array($result)) {
			$no++;
			$date = $cari['tangg'];
			$tgl = date("d-m-Y", strtotime($date));
			$tglds = date("Y-m-d", strtotime($date));
			if ($tangg != $tgl && $no > 1) {
				$tbody .=  '<tr>
					<th style="text-align:right;background-color:lightgray;" colspan="3">Total</th>
					<th style="background-color:yellow;">' . number_format($total) . '</th>
					<th colspan="2" style="background-color:lightgray;"></th>
				</tr>';
				$total = 0;
			}
			$tbody .=  '<tr>
			<td>' . $no . '</td>
			<td>' . $tgl . '</td>
			<td>' . $cari['nama_transaksi'] . '</td>
			<td>' . number_format($cari['nominal_dibayarkan']) . '</td>
			<td style="display:none;">' . $cari['nominal_dibayarkan'] . '</td>
			<td style="display:none;">' . $cari['id_master_trans_sis'] . '</td>
			<td style="display:none;">' . $cari['nis_from_idsiswa'] . '</td>
			<td style="display:none;">' . $cari['id_transaksi'] . '</td>';

			if ($cari['ket'] == "Bank BRI") {
				$tbody .=  '
					<td>' . $cari['office'] . ' |  Bank BRI (' . $cari['nama_us'] . ')</td>
				';
			} else if ($cari['ket'] == "Bank BNI") {
				$tbody .=  '
					<td>' . $cari['office'] . ' |  Bank BNI (' . $cari['nama_us'] . ')</td>
				';
			} else if ($cari['ket'] == "Bank Mandiri") {
				$tbody .=  '
					<td>' . $cari['office'] . ' |  Bank Mandiri (' . $cari['nama_us'] . ')</td>
				';
			} else if ($cari['ket'] == "Bank BCA") {
				$tbody .=  '
					<td>' . $cari['office'] . ' |  Bank BCA (' . $cari['nama_us'] . ')</td>
				';
			} else if ($cari['ket'] == "kartu") {
				$tbody .=  '
					<td>' . $cari['office'] . ' |  Kartu (' . $cari['nama_us'] . ')</td>
				';
			} else {
				$tbody .=  '
					<td>' . $cari['nama_us'] . '</td>
				';
			}

			$tbody .=  '<td style="display:none;">' . $tglds . '</td>
			';

			echo '<script>
				$(document).ready(function(){
					kb_edit_tgl_jth = $("#kb_edit_tgl_jth").flatpickr({
						dateFormat: "Y-m-d"
					});
				});
			</script>';
			$id_transaksi = $cari['id_transaksi'];

			if ($cari['office'] == 'teller' || $cari['office'] == 'Teller') {
				$tbody .=  '<td> 
					<button class="btn btn-sm btn-danger" onclick="del_trans(' . $cari['id_transaksi'] . ',' . $cari['id_master_trans_sis'] . ');" > 
							<i class="bx bx-trash "></i> 
					</button>
					<button class="btn btn-sm btn-secondary" onclick="cetak_nota(' . $cari['id_where'] . ');">
						<i class="bx bx-printer "></i>  
					Struk</button>
					<button class="btn btn-sm btn-info" onclick="edit_tanggal(' . $id_transaksi . ');">Ubah Tanggal</button>
				</td>';
			} else {
				$tbody .=  '<td></td>';
			}
			$tbody .= 	'
			<td style="display:none;">' . $cari['id_where'] . '</td>
			</tr>';

			$total += $cari['nominal_dibayarkan'];
			$tangg = date("d-m-Y", strtotime($date));
		}

		$table = '<input id="kb_edit_tgl_jth" type="text" style="width: 50%;" readonly placeholder="Pilih tanggal"><br><br>
				<table class="table table-sm m-0 ">
					<thead>
						<tr>
							<th >No</th>
							<th >Tanggal</th>
							<th >Nama Iuran</th>
							<th >Nominal</th>
							<th >Dicatat Oleh</th>
							<th >Aksi</th> 
							</tr>
						</tr>
					<tbody>
						' . $tbody . '
					</tbody>
					<tfoot>
						<tr>
							<th style="text-align:right;background-color:lightgray;" colspan="3">Total Pembayaran</th>
							<th style="background-color:yellow;">' . number_format($total) . '</th>
							<th colspan="2" style="background-color:lightgray;"></th>
						</tr>
					</foot>
				</table>';
		echo $table;
	}

	function update_tanggal()
	{
		global $con;
		$id_transaksi = post('id_transaksi');
		$kb_edit_tgl_jth = post('kb_edit_tgl_jth');
		$sql = "UPDATE transaksi_siswa SET tgl = '$kb_edit_tgl_jth 00:00:00' WHERE id_transaksi = '$id_transaksi'";
		$res = db_query($sql);
	}

	function del_transaksi()
	{
		global $con;
		global $con2;
		$e = $_POST['id_mst_trans'];
		$a = $_POST['id_trans'];
		$qry = mysqli_query($con, "SELECT * FROM transaksi_siswa WHERE id_transaksi=$a");
		while ($rs = mysqli_fetch_array($qry)) {
			$log = array(
				'id_transaksi' 			=> $rs['id_transaksi'],
				'nis_from_idsiswa'		=> $rs['nis_from_idsiswa'],
				'id_master_trans_sis'	=> $rs['id_master_trans_sis'],
				'id_kelas'				=> $rs['id_kelas'],
				'id_jenis_transaksi'	=> $rs['id_jenis_transaksi'],
				'nominal_ketetapan'		=> $rs['nominal_ketetapan'],
				'disc'					=> $rs['disc'],
				'id_status'				=> $rs['id_status'],
				'nominal_dibayarkan'	=> $rs['nominal_dibayarkan'],
				'infaq'					=> $rs['infaq'],
				'id_where'				=> $rs['id_where'],
				'print'					=> $rs['print'],
				'tgl'					=> $rs['tgl'],
				'kekurangan'			=> $rs['kekurangan'],
				'id_user'				=> $rs['id_user'],
				'thn_aj'				=> $rs['thn_aj'],
				'office'				=> $rs['office'],
				'kirim'					=> $rs['kirim'],
				'mutasi'				=> $rs['mutasi'],
				'referensi'				=> $rs['referensi'],
				'Dihapus_oleh'			=> $_SESSION['username']
			);
		}
		$hasil = json_encode($log);
		//$insert_log = mysqli_query($con2, "INSERT INTO log_all(data_log,kode_sekolah,executed_by,ket)VALUE('$hasil','07','$_SESSION[username]','Hapus Transaksi')");
		$sql = mysqli_query($con, "UPDATE transaksi_siswa SET is_deleted = 1, deleted_at = NOW() WHERE id_transaksi='$a'");
		$sqlt = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET sta=NULL WHERE id_master_trans_sis='$e'");
		$que = mysqli_query($con, "UPDATE tbl_uangku SET is_deleted = 1, deleted_at = NOW() WHERE id_trans=$a");
		$que2 = mysqli_query($con, "UPDATE tabungan SET is_deleted = 1, deleted_at = NOW() WHERE id_trans=$a");
		audit_log('SOFT_DELETE', 'administrasi_iuran', $a, "Pembatalan / soft-delete transaksi iuran ID $a");
	}

	function history_uang_saku()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$sql = "SELECT * from tbl_uangku 
				INNER JOIN  data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
				WHERE data_siswa.id_siswa ='$id' ORDER BY tanggal ASC";
		$result = mysqli_query($con, $sql);
		$tbody = '';
		$no = 0;
		$saldo = 0;
		while ($rskls = mysqli_fetch_array($result)) {

			$btn_hps = "<button class='btn btn-danger btn-sm' onclick=saku_hapus($rskls[id_tbl_uangku],$rskls[jumlah],$rskls[id_tbl_status_transaksi],'" . base64_encode($rskls['keterangan']) . "');>
								<i class='bx bx-trash'></i> 
								Hapus</button>";
			$ctk = '<button class="btn btn-primary btn-sm" onclick="saku_nota(' . $rskls['id_tbl_uangku'] . ');">
							<i class="bx bx-printer"></i>
							Cetak</button>';
			$ctk = '<div class="btn-group">
						<button class="btn btn-primary btn-sm" onclick="saku_nota(' . $rskls['id_tbl_uangku'] . ');">
							<i class="bx bx-printer"></i>
							Cetak</button>
						<button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="mdi mdi-chevron-down"></i>
						</button>
						<div class="dropdown-menu" style="">
							<a class="dropdown-item" href="#" onclick="event.preventDefault();saku_notif_wa(' . $rskls['id_tbl_uangku'] . ');">
								<i class="bx bxl-whatsapp" style="color:green;"></i> 
							Notif WA</a>
						</div>
					</div>';
			$db = "";
			$kr = "";
			$no++;
			if ($rskls['id_tbl_status_transaksi'] == 1 || $rskls['id_tbl_status_transaksi'] == 3) {
				$seluruh = $saldo;
				$saldo = $saldo + ($rskls['jumlah']);
				$db = number_format($rskls['jumlah']);
				if ($rskls['id_tbl_status_transaksi'] == 1) {
					$btn = '';
					$bt = '';
				} else {
					$button = '';
					$btn = $btn_hps;
					$bt = $ctk;
				}
			} else if ($rskls['id_tbl_status_transaksi'] == 2) {
				$seluruh = $saldo;
				$saldo = $saldo - ($rskls['jumlah']);
				if ($rskls['id_trans'] == null) {
					$btn = $btn_hps;
				} else {
					$btn = "";
				}
				$bt = $ctk;
				$kr = number_format($rskls['jumlah']);
			} else if ($rskls['id_tbl_status_transaksi'] == 4) {
				$seluruh = $saldo;
				$saldo = $saldo - ($rskls['jumlah']);
				$bt = $ctk;
				$kr = number_format($rskls['jumlah']);
			} else if ($rskls['id_tbl_status_transaksi'] == 5) {
				$seluruh = $saldo;
				$saldo = $saldo - ($rskls['jumlah']);
				$kr = number_format($rskls['jumlah']);
				$btn = $btn_hps;
				$bt = $ctk;
			}

			$tbody .= '<tr>
				<td>' . $no . '</td>
				<td style="display:none;">' . $rskls['id_siswa'] . '</td>
				<td>' . date("d-m-Y", strtotime($rskls['tanggal'])) . '</td>
				<td >' . $rskls['keterangan'] . '</td>
				<td>' . $db . '</td>
				<td>' . $kr . '</td>
				<td>' . number_format($saldo) . '</td>
				<td style="display:none;">' . $rskls['jumlah'] . '</td>
				<td style="display:none;">' . number_format($seluruh) . '</td>
				<td >' . $bt . ' ' . $btn . ' </td>
				<td style="display:none;">' . $rskls['id_tbl_status_transaksi'] . '</td>
				<td style="display:none;">' . $rskls['jumlah'] . '</td>
				</tr>';
			$btn = '';
			$bt = '';
		}

		if ($tbody == '') {
			$no_res = '';
			$tbody .= "<tr  class='tc no_result'  style='$no_res'>
					<td colspan='7'>Belum ada aktivitas dompet digital</td>
				</tr>";
		}

		$table = '<table class="table table-sm">
			<thead>
				<tr>
					<th>#</th>
					<th>Tanggal</th>
					<th>Deskripsi</th>
					<th>Saldo Masuk</th>
					<th>Saldo Keluar</th>
					<th>Saldo Akhir</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				' . $tbody . '
			</tbody>
			</table>';
		echo $table;
	}

	// tabungan 
	function tabungan_topup()
	{
		global $con;
		ob_start();
		$id = $_POST['id_siswa'];
		$nominal = $_POST['nominal'];
		$date = date('Y-m-d H:i:s');
		$sesi = $_SESSION['id'];
		$saldo = 0;
		$saldo_baru = 0;
		$sql_tabungan = "SELECT * FROM tabungan WHERE nis_from_idsiswa = '$id' ORDER BY id_tab DESC LIMIT 1";
		$result = mysqli_query($con, $sql_tabungan);
		while ($tab = mysqli_fetch_array($result)) {
			$saldo = $tab['saldo'];
			if ($saldo == '') {
				$saldo = 0;
			}
		}
		$saldo_baru = $nominal + $saldo;
		$sql = mysqli_query($con, "INSERT INTO tabungan (nominal_tab, nominal_amb, nis_from_idsiswa, saldo, st, id_user, tgl) VALUES ('$nominal', '0', '$id', '$saldo_baru', 'F', '$sesi', '$date')");
	}

	function history_tabungan()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$sql = "SELECT * from tabungan 
				INNER JOIN  data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa
				WHERE data_siswa.id_siswa ='$id' ORDER BY tgl ASC";
		$result = mysqli_query($con, $sql);
		$tbody = '';
		$no = 0;
		$saldo = 0;
		$tabungan_masuk = 0;
		$tabungan_keluar = 0;
		while ($rskls = mysqli_fetch_array($result)) {

			$btn_hps = '<button class="btn btn-danger btn-sm" onclick="tabungan_hapus(' . $rskls['id_tab'] . ');">
								<i class="bx bx-trash"></i> 
								Hapus</button>';
			$ctk = '<div class="btn-group">
						<button class="btn btn-primary btn-sm" onclick="tabungan_nota(' . $rskls['id_tab'] . ');">
							<i class="bx bx-printer"></i>
							Cetak</button>
					</div>';
			$db = "";
			$kr = "";
			$no++;
			$tabungan_masuk += $rskls['nominal_tab'];
			$tabungan_keluar += $rskls['nominal_amb'];
			$saldo = $tabungan_masuk - $tabungan_keluar;

			$tbody .= '<tr>
				<td>' . $no . '</td>
				<td style="display:none;">' . $rskls['id_siswa'] . '</td>
				<td>' . date("d-m-Y", strtotime($rskls['tgl'])) . '</td>
				<td>' . number_format($rskls['nominal_tab']) . '</td>
				<td>' . number_format($rskls['nominal_amb']) . '</td>
				<td>' . number_format($saldo) . '</td>
				<td >' . $ctk . ' ' . $btn_hps . ' </td>
				</tr>';
			$btn = '';
			$bt = '';
		}

		if ($tbody == '') {
			$no_res = '';
			$tbody .= "<tr  class='tc no_result'  style='$no_res'>
					<td colspan='7'>Tidak Ada Data</td>
				</tr>";
		}

		$table = '<table class="table table-sm">
			<thead>
				<tr>
					<th>#</th>
					<th>Tanggal</th>
					<th>Debit</th>
					<th>Kredit</th>
					<th>Saldo</th>
					<th>Act</th>
				</tr>
			</thead>
			<tbody>
				' . $tbody . '
			</tbody>
			</table>';
		echo $table;
	}

	function tabungan_tarik()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$id = $_POST['id_siswa'];
		$nom = $_POST['nominal'];
		$ket = $_POST['ket'];
		$date = date('Y-m-d H:i:s');
		$id_jenjang = $_POST['id_jenjang'];
		$query = mysqli_query($con, "SELECT SUM(nominal_tab) as top FROM tabungan WHERE nis_from_idsiswa=$id");
		$top = mysqli_fetch_row($query);
		$sql = mysqli_query($con, "SELECT SUM(nominal_amb) as cek FROM tabungan WHERE nis_from_idsiswa=$id");
		$cek = mysqli_fetch_row($sql);
		$saldo = $top[0] - $cek[0];
		$saldo_akhir = 0;
		$query_akhir = mysqli_query($con, "SELECT saldo FROM tabungan WHERE nis_from_idsiswa = '$id' ORDER BY id_tab DESC LIMIT 1");
		while ($ks = mysqli_fetch_array($query_akhir)) {
			$saldo_akhir = $ks['saldo'];
		}
		$saldo_asli = $saldo_akhir - $nom;
		$cb = [];
		$cb['stat'] = 0;
		if ($nom > $saldo) {
			$cb['ket'] = "Saldo Tidak Cukup";
		} else {
			$ins = mysqli_query($con, "INSERT INTO tabungan(nominal_tab, nominal_amb, nis_from_idsiswa, saldo, st, id_user, tgl)VALUES('0','$nom','$id','$saldo_asli','F','$sesi','$date')");
			if ($ins) {
				$last_id = $con->insert_id;

				$cb['ket'] = "Berhasil diinput";
				$cb['stat'] = 1;
			} else {
				$cb['ket'] = "Gagal diinput!";
			}
		}
		echo json_encode($cb);
	}

	function tabungan_hapus()
	{
		global $con;
		$id_tab = $_POST['id_tab'];
		$cb = [];
		$ins = mysqli_query($con, "DELETE FROM tabungan WHERE id_tab=$id_tab");
		if ($ins) {
			$last_id = $con->insert_id;

			$cb['ket'] = "Berhasil diinput";
			$cb['stat'] = 1;
		} else {
			$cb['ket'] = "Gagal diinput!";
		}
		echo json_encode($cb);
	}

	function tabungan_nota()
	{
		ob_start();
		require_once('app/lib/tcpdf/tcpdf.php');
		global $con;
		$id_tab = $_GET['id_tab'];

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006f', PDF_HEADER_STRING);
		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf = new TCPDF('P', 'mm', array(87, 500));
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetFont('times', '', 10);
		$pdf->setMargins(10, 10, 15, true);

		$pdf->AddPage();
		$sql = mysqli_query($con, "SELECT * FROM tabungan  
		INNER JOIN data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa
		INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_siswa.id_jenjang
		WHERE id_tab='$id_tab'");

		// $pdf->Image('../config_sekolah/logo.png', 10, 7, 15, 15);
		$pdf->SetFont('courierB', '', 11);
		$pdf->Cell(75, 1, 'BUKTI TRANSAKSI', '', 0, 'C');
		$pdf->ln(5);
		$pdf->SetFont('courierB', '', 8);
		$pdf->Cell(75, 4, SCHOOL_NAME, '', 0, 'C');
		$pdf->ln(4);
		$pdf->Cell(75, 4, '', '', 0, 'C');
		$pdf->SetFont('courierB', '', 12);
		$pdf->SetLineWidth(0.1);
		$pdf->Line(10, 24, 73, 24);
		$pdf->SetLineWidth(0.3);
		$pdf->Line(10, 24.5, 73, 24.5);

		$no = 0;
		while ($rowet = mysqli_fetch_array($sql)) {
			$no++;
			$pdf->SetFont('times', '', 10);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->ln(6);
			$html = '<table border="0">
						<tr>
			<td width="200px"align="left"><strong>Top Up Tabungan</strong></td>
			<td width="400px" align="left"></td>
			</tr>
			<tr>
			<td width="26px"align="left">Nama</td>
			<td width="400px" align="left">: ' . $rowet['nama_siswa'] . '</td>
			</tr>
			<tr>
			<td width="26px"align="left">Unit</td>
			<td width="400px" align="left">: ' . $rowet['nama_jenjang'] . '</td>
			</tr>

			</table>';
			$pdf->writeHTML($html, true, false, true, false, '');

			$pdf->SetFont('times', '', 9);
			$htmllp = '<table border="0">
			<tr>
			<td width="10px">#</td>
			<td width="60px" align="left">Debit</td>
			<td width="60px" align="left">Kredit</td>
			<td width="60px" align="left">Saldo</td>
			</tr>
			</table>';
			$pdf->writeHTML($htmllp, true, false, true, false, '');

			$pdf->SetFont('times', '', 9);
			$htmll = '<table border="0">
			<tr>
			<td width="10px">' . $no . '.</td>
			<td width="60px" align="left">' . number_format($rowet['nominal_tab']) . '</td>
			<td width="60px" align="left">' . number_format($rowet['nominal_amb']) . '</td>
			<td width="60px" align="left">' . number_format($rowet['saldo']) . '</td>
			</tr>
			</table>';
			$pdf->writeHTML($htmll, true, false, true, false, '');

			$pdf->SetLineWidth(0.1);
			$pdf->Line(10, 38, 70, 38);
			$pdf->SetLineWidth(0.3);
			$pdf->Line(10, 38.5, 70, 38.5);
			$pdf->ln(2);
			$pdf->SetFont('times', '', 9);
			$pdf->Cell(0, 0, "NO NOTA : $iduang", 0, 1);
			$pdf->write1DBarcode($iduang, 'UPCE', '', '', '', 5, 0.5, $style, 'N');
			$date = $rowet['tanggal'];
			// $tgl = date("d-m-Y", strtotime($date));
			$tgl = substr($rowet['tgl'], 0, 10);

			$pdf->SetFont('times', '', 7);
			$pdf->Cell(0, 4, $tgl, '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'ADMIN', '', 0, 'C');
			$pdf->ln(10);
			$pdf->Cell(0, 4, $_SESSION['username'], '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'Terima kasih', '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'Kwitansi mohon disimpan', '', 0, 'C');

			$no++;
			ob_end_clean();
			$pdf->Output();
		}
	}

	function tabungan_saldo()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$query = mysqli_query($con, "SELECT SUM(nominal_tab) as top FROM tabungan WHERE nis_from_idsiswa=$id");
		$top = mysqli_fetch_row($query);
		$query_satu = mysqli_query($con, "SELECT SUM(nominal_amb) as top_one FROM tabungan WHERE nis_from_idsiswa=$id");
		$top_satu = mysqli_fetch_row($query_satu);
		$saldo = $top[0] - $top_satu[0];
		$data = [];
		$data['saldo_rp'] = rupiah($saldo);
		$data['saldo'] = $saldo;
		echo json_encode($data);
	}

	// and tabungan

	function saku_topup()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$nom = $_POST['nominal'];
		$id_jenjang = $_POST['id_jenjang'];
		$sql = mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa,id_tbl_status_transaksi,jumlah,keterangan)VALUES('$id','3','$nom','Isi Saldo Dompet')");
		if ($sql) {
			$last_id = $con->insert_id;

			// cek konfig notif
			$cek_notif = "SELECT id, notif_transaksi FROM tbl_wappin_konfig WHERE id_jenjang = $id_jenjang LIMIT 1";
			$res_cek_notif = db_query($cek_notif);
			if (mysqli_num_rows($res_cek_notif) > 0) {
				while ($x = mysqli_fetch_array($res_cek_notif)) {
					if ($x['notif_transaksi'] == "1") {
						//$this->wappin_notif_byr_saku($last_id);
					}
				}
			}

			echo "Berhasil Top Up!";
		} else {
			echo "Gagal Top Up!";
		}
	}

	function saku_tarik()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$nom = $_POST['nominal'];
		$ket = $_POST['ket'];
		$id_jenjang = $_POST['id_jenjang'];
		$query = mysqli_query($con, "SELECT SUM(jumlah) as top FROM tbl_uangku WHERE id_tbl_status_transaksi!=2 AND id_siswa=$id");
		$top = mysqli_fetch_row($query);
		$sql = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=2 AND id_siswa=$id");
		$cek = mysqli_fetch_row($sql);
		$saldo = $top[0] - $cek[0];
		$cb = [];
		$cb['stat'] = 0;
		if ($nom > $saldo) {
			$cb['ket'] = "Saldo Tidak Cukup";
		} else {
			$ins = mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa,id_tbl_status_transaksi,jumlah,keterangan)VALUES('$id','2','$nom','$ket')");
			if ($ins) {
				$last_id = $con->insert_id;

				// cek konfig notif
				$cek_notif = "SELECT id, notif_transaksi FROM tbl_wappin_konfig WHERE id_jenjang = $id_jenjang LIMIT 1";
				$res_cek_notif = db_query($cek_notif);
				if (mysqli_num_rows($res_cek_notif) > 0)
					while ($x = mysqli_fetch_array($res_cek_notif)) {
						if ($x['notif_transaksi'] == "1") {
							//$this->wappin_notif_tarik_saku($last_id, $ket);
						}
					}

				$cb['ket'] = "Berhasil diinput";
				$cb['stat'] = 1;
			} else {
				$cb['ket'] = "Gagal diinput!";
			}
		}
		echo json_encode($cb);
	}

	function saku_emoney()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$nominal_saldo = $_POST['nominal'];
		$tanggal = date('Y-m-d H:i:s');
		$query = mysqli_query($con, "SELECT SUM(jumlah) as top FROM tbl_uangku WHERE id_tbl_status_transaksi=1 AND id_siswa=$id");
		$top = mysqli_fetch_row($query);
		$query_satu = mysqli_query($con, "SELECT SUM(jumlah) as top FROM tbl_uangku WHERE id_tbl_status_transaksi=3 AND id_siswa=$id");
		$top_satu = mysqli_fetch_row($query_satu);
		$sql = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=2 AND id_siswa=$id");
		$cek = mysqli_fetch_row($sql);
		$sql_satu = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=4 AND id_siswa=$id");
		$cek_satu = mysqli_fetch_row($sql_satu);
		$sql_terakhir = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=5 AND id_siswa=$id");
		$cek_terakhir = mysqli_fetch_row($sql_terakhir);
		$saldo = $top[0] + $top_satu[0] - $cek[0] - $cek_satu[0] - $cek_terakhir[0];
		// echo $saldo;

		if ($nominal_saldo > $saldo) {
			echo 'jumlah melebihi nominal saldo uang saku!';
		} else {
			$sql = "INSERT INTO tbl_ekantin(id_siswa,id_status_transaksi,jumlah,keterangan,tanggal)VALUES('$id','1','$nominal_saldo','Top Up Offline','$tanggal')";
			$result_sql = mysqli_query($con, $sql);
			$query = "SELECT id_ekantin FROM tbl_ekantin WHERE id_siswa = '$id' AND id_status_transaksi = 1 ORDER BY id_ekantin DESC LIMIT 1";
			$result_query = mysqli_query($con, $query);
			$ambil = 0;
			while ($cari = mysqli_fetch_array($result_query)) {
				$ambil = $cari['id_ekantin'];
			}
			$jumlah = mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa, id_tbl_status_transaksi, jumlah, keterangan, tanggal, id_trans)VALUES('$id', '5', '$nominal_saldo', 'Split Saldo E-Money', '$tanggal', '$ambil')");
			if ($jumlah) {
				echo 'Berhasil';
			} else {
				echo 'Gagal';
			}
		}
	}

	function saku_saldo()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$query = mysqli_query($con, "SELECT SUM(jumlah) as top FROM tbl_uangku WHERE id_tbl_status_transaksi=1 AND id_siswa=$id");
		$top = mysqli_fetch_row($query);
		$query_satu = mysqli_query($con, "SELECT SUM(jumlah) as top FROM tbl_uangku WHERE id_tbl_status_transaksi=3 AND id_siswa=$id");
		$top_satu = mysqli_fetch_row($query_satu);
		$sql = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=2 AND id_siswa=$id");
		$cek = mysqli_fetch_row($sql);
		$sql_satu = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=4 AND id_siswa=$id");
		$cek_satu = mysqli_fetch_row($sql_satu);
		$sql_terakhir = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=5 AND id_siswa=$id");
		$cek_terakhir = mysqli_fetch_row($sql_terakhir);
		$saldo = $top[0] + $top_satu[0] - $cek[0] - $cek_satu[0] - $cek_terakhir[0];
		$data = [];
		$data['saldo_rp'] = rupiah($saldo);
		$data['saldo'] = $saldo;
		echo json_encode($data);
	}

	function saku_hapus()
	{
		global $con;
		$id_siswa = $_POST['id_siswa'];
		$id_trans = $_POST['id_trans'];
		$nom = $_POST['nominal'];
		$sta = $_POST['stat'];
		$ketket = base64_decode($_POST['ket']);
		$cb = [];
		$cb['stat'] = 0;
		if ($sta == 3 || $sta == 2) {
			$qry = mysqli_query($con, "SELECT SUM(jumlah) as topup FROM tbl_uangku WHERE id_siswa=$id_siswa AND id_tbl_status_transaksi!=2");
			$hasil = mysqli_fetch_row($qry);
			$topup = $hasil[0];

			$qryx = mysqli_query($con, "SELECT SUM(jumlah) as topup FROM tbl_uangku WHERE id_siswa=$id_siswa AND id_tbl_status_transaksi=2 AND id_tbl_uangku !=$id_trans");
			$hasilx = mysqli_fetch_row($qryx);
			$keluar = $hasilx[0];

			$saldo = $topup - $keluar;
			$saldoakhir = $saldo - $nom;
			if ($ketket = 'Tabungan') {
				if ($saldoakhir < 0) {
					$cb['ket'] = "Saldo sudah digunakan,tidak dapat dihapus!";
				} else {
					//$sql_po = mysqli_query($con, "DELETE FROM tabungan WHERE id_saku=$id_trans");
					$sql = mysqli_query($con, "DELETE FROM tbl_uangku WHERE id_tbl_uangku=$id_trans");
					if ($sql) {
						$cb['ket'] = "Berhasil dihapus!";
						$cb['stat'] = 1;
					} else {
						$cb['ket'] = "Gagal dihapus!";
					}
				}
			} else {
				if ($saldoakhir < 0) {
					$cb['ket'] = "Saldo sudah digunakan,tidak dapat dihapus!";
				} else {
					$sql = mysqli_query($con, "DELETE FROM tbl_uangku WHERE id_tbl_uangku=$id_trans");
					if ($sql) {
						$cb['ket'] = "Berhasil dihapus!";
						$cb['stat'] = 1;
					} else {
						$cb['ket'] = "Gagal dihapus!";
					}
				}
			}
		} else {
			$cb['ket'] = "Tidak boleh dihapus!";
		}
		echo json_encode($cb);
	}

	function saku_nota()
	{
		ob_start();
		require_once('app/lib/tcpdf/tcpdf.php');
		global $con;
		$iduang = $_GET['id'];

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006f', PDF_HEADER_STRING);
		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf = new TCPDF('P', 'mm', array(87, 500));
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetFont('times', '', 10);
		$pdf->setMargins(10, 10, 15, true);

		$pdf->AddPage();
		$sql = mysqli_query($con, "SELECT * FROM tbl_uangku  
		INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
		INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_siswa.id_jenjang
		WHERE id_tbl_uangku='$iduang'");

		$pdf->Image('../config_sekolah/logo.png', 10, 7, 15, 15);
		$pdf->SetFont('courierB', '', 11);
		$pdf->Cell(75, 1, 'BUKTI TRANSAKSI', '', 0, 'C');
		$pdf->ln(5);
		$pdf->SetFont('courierB', '', 8);
		$pdf->Cell(75, 4, 'Dompet Digital Warga', '', 0, 'C');
		$pdf->ln(4);
		$pdf->Cell(75, 4, '', '', 0, 'C');
		$pdf->SetFont('courierB', '', 12);
		$pdf->SetLineWidth(0.1);
		$pdf->Line(10, 24, 73, 24);
		$pdf->SetLineWidth(0.3);
		$pdf->Line(10, 24.5, 73, 24.5);

		$no = 0;
		while ($rowet = mysqli_fetch_array($sql)) {
			$no++;
			$pdf->SetFont('times', '', 10);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->ln(6);
			$html = '<table border="0">
						<tr>
			<td width="400px" align="left"></td>
			</tr>
			<tr>
			<td width="26px"align="left">Nama</td>
			<td width="400px" align="left">: ' . $rowet['nama_siswa'] . '</td>
			</tr>

			</table>';
			$pdf->writeHTML($html, true, false, true, false, '');

			$pdf->SetFont('times', '', 9);
			$htmll = '<table border="0">
			<tr>
			<td width="10px">' . $no . '.</td>
			<td width="100px" align="left">'. $rowet['keterangan'] .'</td>
			<td style="text-align:right;"> Rp. ' . number_format($rowet['jumlah']) . '</td>
			</tr>
			</table>';
			$pdf->writeHTML($htmll, true, false, true, false, '');

			$pdf->SetLineWidth(0.1);
			$pdf->Line(10, 38, 70, 38);
			$pdf->SetLineWidth(0.3);
			$pdf->Line(10, 38.5, 70, 38.5);
			$pdf->ln(2);
			$pdf->SetFont('times', '', 9);
			$pdf->Cell(0, 0, "ID TRANSAKSI : $iduang", 0, 1);
			$pdf->write1DBarcode($iduang, 'UPCE', '', '', '', 5, 0.5, $style, 'N');
			$date = $rowet['tanggal'];
			$tgl = date("d-m-Y", strtotime($date));

			$pdf->SetFont('times', '', 7);
			$pdf->Cell(0, 4, $tgl, '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'Diproses Oleh', '', 0, 'C');
			$pdf->ln(10);
			$pdf->Cell(0, 4, $_SESSION['nama_us'], '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'Transaksi berhasil diproses', '', 0, 'C');
			$pdf->ln(4);
			$pdf->Cell(0, 4, 'Simpan bukti transaksi ini', '', 0, 'C');

			$no++;
			ob_end_clean();
			$pdf->Output();
		}
	}

	function saku_notif_url()
	{
		global $con;

		$id = $_POST['id_trans'];

		$query = "
			SELECT 
				tbl_uangku.jumlah,
				data_siswa.nama_siswa,
				data_siswa.telp_one,
				data_jenjang.unit
			FROM tbl_uangku 
			INNER JOIN data_siswa 
				ON data_siswa.id_siswa = tbl_uangku.id_siswa
			INNER JOIN data_jenjang 
				ON data_jenjang.id_jenjang = data_siswa.id_jenjang
			WHERE id_tbl_uangku = '$id'
		";

		$result = mysqli_query($con, $query);

		if ($rs = mysqli_fetch_assoc($result)) {

			// format nomor
			$telp = trim($rs['telp_one']);

			if ($telp != '') {
				if (substr($telp, 0, 2) == '08') {
					$telp = '62' . substr($telp, 1);
				}
			}

			$unit   = $rs['unit'];
			$nama   = $rs['nama_siswa'];
			$jumlah = number_format($rs['jumlah'], 0, "", ".");

			$pesan = "
*Komune*

Halo Bapak/Ibu *$nama* 👋

Isi saldo Dompet Digital berhasil ya ✅

Nominal: *Rp$jumlah*

Saldo sudah bisa digunakan untuk transaksi di Komune 😊

Terima kasih 🙏
			";

			$pesan = urlencode(trim($pesan));

			$url = "https://api.whatsapp.com/send?phone=$telp&text=$pesan";

			echo $url;
		}
	}

	function rincian_tagihan()
	{
		ob_start();
		require_once('app/lib/tcpdf/tcpdf.php');
		global $con;
		$id = "";
		$th = $_GET['id_ta'];
		$nis = $_GET['id_siswa'];
		$id_kls = $_GET['id_kelas'];

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006f', PDF_HEADER_STRING);
		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf = new TCPDF('P', 'mm', array(87, 500));
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetFont('times', '', 10);
		$pdf->setMargins(10, 10, 15, true);
		$pdf->AddPage();
		$sqltot = "select *,transaksi_siswa.tgl as tangg from transaksi_siswa 
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE id_where='$id' AND status_kelompok='0' ORDER BY transaksi_siswa.id_jenis_transaksi ASC";
		$sqltotdua = "select *,transaksi_siswa.tgl as tangg,SUM(nominal_dibayarkan) as bayar from transaksi_siswa 
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE id_where='$id' AND status_kelompok='1' GROUP BY kel ORDER BY transaksi_siswa.id_jenis_transaksi ASC";
		$sqltott = "SELECT
				* 
			FROM
				penempatan_siswa
				INNER JOIN data_siswa ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa
				INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas
				INNER JOIN data_jenjang ON data_jenjang.id_jenjang = data_siswa.id_jenjang 
			WHERE
				id_siswa = '$nis'
				AND penempatan_siswa.id_kelas = '$id_kls'";
		$sqltottinfaq = "select SUM(infaq) as info from transaksi_siswa 
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			inner join data_siswa ON data_siswa.id_siswa=transaksi_siswa.nis_from_idsiswa
			inner join penempatan_siswa ON penempatan_siswa.nis_from_idsiswa=transaksi_siswa.nis_from_idsiswa
			inner join data_kelas ON data_kelas.id_kelas=transaksi_siswa.id_kelas
			WHERE id_where='$id' ORDER BY transaksi_siswa.id_jenis_transaksi ASC";
		$sqltotal = "select SUM(nominal_dibayarkan) as nom from transaksi_siswa 
			INNER JOIN master_jenis_transaksi on master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			inner join data_siswa ON data_siswa.id_siswa=transaksi_siswa.nis_from_idsiswa
			inner join penempatan_siswa ON penempatan_siswa.nis_from_idsiswa=transaksi_siswa.nis_from_idsiswa
			inner join data_kelas ON data_kelas.id_kelas=transaksi_siswa.id_kelas
			WHERE id_where='$id' AND penempatan_siswa.thn_ajar='$th' ORDER BY transaksi_siswa.id_jenis_transaksi ASC";
		$sqlbiasa = "SELECT
		tb_ketetapan.*,
		tb_bayar.bayar,
		tb_ketetapan.jml - IFNULL( tb_bayar.bayar, 0 ) AS tagihan 
		FROM
			(
			SELECT
				master_alokasi_transaksi_all.id_master_trans_sis,
				master_alokasi_transaksi_all.nis_from_idsiswa,
				master_jenis_transaksi.nama_transaksi,
				data_tahunajar.nama_tahunajar,
				SUM( nominal ) AS jml 
			FROM
				master_alokasi_transaksi_all
				JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
				LEFT JOIN data_tahunajar ON data_tahunajar.id_tahunajar = master_alokasi_transaksi_all.id_sesi 
			WHERE
				master_alokasi_transaksi_all.nis_from_idsiswa = '$nis' 
				AND master_alokasi_transaksi_all.sta IS NULL 
				AND master_jenis_transaksi.nama_transaksi != 'Uang Saku' 
				AND master_alokasi_transaksi_all.id_sesi = '$th'
			GROUP BY
				master_alokasi_transaksi_all.id_master_trans_sis 
			) AS tb_ketetapan
			LEFT JOIN (
			SELECT
				master_alokasi_transaksi_all.id_master_trans_sis,
				master_alokasi_transaksi_all.nis_from_idsiswa,
				SUM( nominal_dibayarkan ) AS bayar 
			FROM
				transaksi_siswa
				INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
			WHERE
				master_alokasi_transaksi_all.nis_from_idsiswa = '$nis' 
				AND master_alokasi_transaksi_all.sta IS NULL 
				AND master_alokasi_transaksi_all.id_sesi = '$th'
			GROUP BY
			master_alokasi_transaksi_all.id_master_trans_sis 
			) AS tb_bayar ON tb_bayar.id_master_trans_sis = tb_ketetapan.id_master_trans_sis";
		$result2 = mysqli_query($con, $sqltot);
		$result3 = mysqli_query($con, $sqltott);
		$result4 = mysqli_query($con, $sqltotal);
		$resultinfaq = mysqli_query($con, $sqltottinfaq);
		$result5 = mysqli_query($con, $sqltotdua);
		$resultbiasa = mysqli_query($con, $sqlbiasa);
		$pdf->Image('../config_sekolah/logo.png', 10, 7, 15, 15);
		$pdf->SetFont('courierB', '', 11);
		$pdf->Cell(75, 1, 'RINCIAN TAGIHAN', '', 0, 'C');
		$pdf->ln(5);
		$pdf->SetFont('courierB', '', 8);
		$pdf->Cell(75, 4, SCHOOL_NAME, '', 0, 'C');
		$pdf->ln(4);
		$pdf->Cell(75, 4, '', '', 0, 'C');
		$pdf->SetFont('courierB', '', 12);
		$pdf->SetLineWidth(0.1);
		$pdf->Line(10, 24, 73, 24);
		$pdf->SetLineWidth(0.3);
		$pdf->Line(10, 24.5, 73, 24.5);
		while ($rowet = mysqli_fetch_array($result3)) {
			$pdf->SetFont('times', '', 10);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->ln(6);
			$html = '<table border="0">
			<tr>
			<td width="36px"align="left">Nama</td>
			<td width="400px" align="left">: ' . $rowet['nama_siswa'] . '</td>
			</tr>
			<tr>
			<td width="36px"align="left">Wilayah</td>
			<td width="400px" align="left">: ' . $rowet['nama_kelas'] . '</td>
			</tr>
			</table>';
			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');
			$pdf->SetLineWidth(0.1);
			$pdf->Line(10, 39, 73, 39);
			$pdf->SetLineWidth(0.3);
			$pdf->Line(10, 39.5, 73, 39.5);
			$pdf->SetFont('times', '', 11);
			$pdf->ln(1);
			$no = 1;
			$html = '<table border="1">';
			$total = 0;
			while ($rowe = mysqli_fetch_array($resultbiasa)) {
				$html .= '
				<tr>
				<td width="20px">' . $no . '.</td>
				<td width="110px">' . $rowe['nama_transaksi'] . '</td>
				<td style="text-align:right;width:45px;">' . number_format($rowe['tagihan']) . '</td>
				</tr>
				';
				$total += $rowe['tagihan'];
				$no++;
			}
			$pdf->writeHTML($html, true, false, true, false, '');
			$htmlbb = '<hr >';
			$pdf->writeHTML($htmlbb, true, false, true, false, '');
			$pdf->Cell(63, 4, 'Total Tagihan : ' . number_format($total), '', 0, 'R');
			$pdf->ln(5);
			$pdf->SetFont('times', '', 9);

			$no++;
			ob_end_clean();
			$pdf->Output();
		}
	}

	function history_emoney()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$sql = "
			SELECT id_ekantin, id_siswa, id_status_transaksi, jumlah, keterangan, tanggal FROM tbl_ekantin WHERE id_siswa = $id ORDER BY tanggal ASC
		";
		$result = mysqli_query($con, $sql);
		$tbody = '';
		$no = 0;
		$saldo = 0;
		while ($rskls = mysqli_fetch_array($result)) {

			$btn_hapus = "
						<button class='btn btn-xs btn-danger' onclick='del_topup_emoney($rskls[id_ekantin],$rskls[id_siswa]);' title='Hapus'>
							<i class='bx bx-trash '></i></button>";
			if ($rskls['id_status_transaksi'] == "2" || $rskls['id_status_transaksi'] == "4") {
				$btn_hapus = "";
			}

			$db = "";
			$kr = "";
			$no++;
			if ($rskls['id_status_transaksi'] == 1) {
				$seluruh = $saldo;
				$saldo = $saldo + ($rskls['jumlah']);
				$db = number_format($rskls['jumlah']);
			} else if ($rskls['id_status_transaksi'] == 0) {
				$seluruh = $saldo;
				$saldo = $saldo + ($rskls['jumlah']);
				$db = number_format($rskls['jumlah']);
			} else if ($rskls['id_status_transaksi'] == 2) {
				$seluruh = $saldo;
				$saldo = $saldo - ($rskls['jumlah']);
				$kr = number_format($rskls['jumlah']);
			} else if ($rskls['id_status_transaksi'] == 4) {
				$seluruh = $saldo;
				$saldo = $saldo - ($rskls['jumlah']);
				$kr = number_format($rskls['jumlah']);
			}

			$tbody .= '<tr>
				<td>' . $no . '</td>
				<td style="display:none;">' . $rskls['id_siswa'] . '</td>
				<td>' . date("d-m-Y", strtotime($rskls['tanggal'])) . '</td>
				<td >' . $rskls['keterangan'] . '</td>
				<td>' . $db . '</td>
				<td>' . $kr . '</td>
				<td>' . number_format($saldo) . '</td>
				<td style="display:none;">' . $rskls['jumlah'] . '</td>
				<td style="display:none;">' . number_format($seluruh) . '</td>
				<td >' . $btn_hapus . ' </td>
				<td style="display:none;">' . $rskls['id_status_transaksi'] . '</td>
				<td style="display:none;">' . $rskls['jumlah'] . '</td>
				</tr>';
			$btn = '';
			$bt = '';
		}

		if ($tbody == '') {
			$no_res = '';
			$tbody .= "<tr  class='tc no_result'  style='$no_res'>
					<td colspan='7'>Tidak Ada Data</td>
				</tr>";
		}

		$table = '<table class="table table-sm">
			<thead>
				<tr>
					<th>#</th>
					<th>Tanggal</th>
					<th>Keterangan</th>
					<th>Masuk</th>
					<th>Keluar</th>
					<th>Saldo</th>
					<th>Act</th>
				</tr>
			</thead>
			<tbody>
				' . $tbody . '
			</tbody>
			</table>';
		echo $table;
	}

	function del_trans_money()
	{
		$id_trans = $_POST['id_trans'];
		$cb = [];
		$cb['stat'] = 0;

		$sql = "DELETE FROM tbl_uangku WHERE id_trans = $id_trans";
		$res = db_query($sql);
		if ($res) {
			$sql1 = "DELETE FROM tbl_ekantin WHERE id_ekantin = $id_trans";
			$res1 = db_query($sql1);
			$cb['stat'] = 1;
			$cb['ket'] = "Berhasil dihapus!";
		} else {
			$cb['ket'] = "Gagal dihapus!";
		}
		echo json_encode($cb);
	}

	function wa_web()
	{
		$sesi = $_SESSION['id'];
		$id_where = $_POST['id_order'];

		$querykls = "
			SELECT 
				master_jenis_transaksi.nama_transaksi,
				data_jenjang.unit,
				data_siswa.id_jenjang,
				data_siswa.nama_siswa,
				data_siswa.telp_one,
				transaksi_siswa.nis_from_idsiswa,
				transaksi_siswa.id_jenis_transaksi,
				transaksi_siswa.nominal_dibayarkan,
				transaksi_siswa.id_where 
			FROM transaksi_siswa 
			INNER JOIN data_siswa 
				ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
			INNER JOIN data_jenjang 
				ON data_jenjang.id_jenjang = data_siswa.id_jenjang
			INNER JOIN master_jenis_transaksi 
				ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
			WHERE transaksi_siswa.id_where = '$id_where'
			ORDER BY id_where ASC
		";

		$result = db_query($querykls);

		$item_bayar = [];
		$total_bayar = 0;

		$telp = '';
		$nama_siswa = '';

		while ($rs = mysqli_fetch_array($result)) {

			$telp = $rs['telp_one'];
			$nama_siswa = $rs['nama_siswa'];

			// format nomor WA
			if (!empty($telp)) {
				if (substr($telp, 0, 2) == '08') {
					$telp = '62' . substr($telp, 1);
				}
			}

			$item_bayar[] =
				"• " . $rs['nama_transaksi'] .
				" — *Rp" . number_format($rs['nominal_dibayarkan'], 0, "", ".") . "*";

			$total_bayar += $rs['nominal_dibayarkan'];
		}

		$total_bayar = number_format($total_bayar, 0, "", ".");

		$pesan = "
*KOMUNE DIGITAL*

Halo Bapak/Ibu *$nama_siswa* 👋

Pembayaran iuran warga berhasil diterima ✅

📋 Rincian Pembayaran:
" . implode("\n", $item_bayar) . "

*Total Pembayaran: Rp$total_bayar*

Terima kasih sudah melakukan pembayaran tepat waktu 🙏
		";

		$pesan = urlencode(trim($pesan));

		echo '
			<a target="_blank"
			href="https://api.whatsapp.com/send?phone=' . $telp . '&text=' . $pesan . '">
				<button class="btn btn-primary" type="button">
					Ya
				</button>
			</a>

			<button type="button"
					class="btn btn-secondary"
					data-bs-dismiss="modal">
				Tutup
			</button>
		';
	}
	function show_toggle()
	{
		$sesi = $_SESSION['id'];
		$id_jenjang = $_POST['id_jenjang'];
		$div = "";

		// 1. cek sudah ada tabel belum. 2. jika belum ada create tabel dulu dan isikan value sesuai jenjang yang ada
		// Table name to check
		$tableName = "tbl_wappin_konfig";
		// SQL query to check if table exists
		$sql = "SHOW TABLES LIKE '$tableName'";
		$result = db_query($sql);
		if ($result->num_rows == 0) {
			// Table does not exist, create it
			$sql_create = "CREATE TABLE $tableName (
		        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		        notif_transaksi VARCHAR(1) NOT NULL,
		        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		        id_user VARCHAR(100) NOT NULL,
		        id_jenjang INT(11) NOT NULL
		    )";

			if (db_query($sql_create) === FALSE) {
				echo "Error creating table: ";
			} else {
				// cek all jenjang 
				$hsl = db_query("SELECT id_jenjang, nama_jenjang FROM data_jenjang");
				if (mysqli_num_rows($hsl) > 0) {
					while ($a = mysqli_fetch_array($hsl)) {
						// Insert new data into the table
						$sql_insert = "INSERT INTO $tableName (notif_transaksi, id_user, id_jenjang) VALUES ('1', '$sesi', '$a[id_jenjang]')";
						$ins = db_query($sql_insert);
					}
				}
			}
		}

		$toggle_state = '0';
		$res = db_query("SELECT id, notif_transaksi FROM tbl_wappin_konfig WHERE id_jenjang = '$id_jenjang'");
		if (mysqli_num_rows($res) > 0) {
			while ($dt = mysqli_fetch_array($res)) {
				$toggle_state = $dt['notif_transaksi'];
			}
			$div .= "
				<b style='color: green;'>Notif WA</b>
	        	<i class='bx bxl-whatsapp '> </i>  
	        	<label class='toggle-switch'>
			";
			if ($toggle_state == '1') {
				$div .= "
					<input type='checkbox' checked onchange='toggleSwitch(this)'>
				";
			} else {
				$div .= "
					<input type='checkbox' onchange='toggleSwitch(this)'>
				";
			}
			$div .= "
				<span class='slider'></span>
				  <span class='slider-text'></span>
				</label>
			";
		}
		echo $div;
	}
	function onoff_wappin()
	{
		$cb = [];
		$cb['stat'] = 0;
		$sesi = $_SESSION['id'];
		$id_jenjang = $_POST['id_jenjang'];
		$notif_transaksi = $_POST['notif_transaksi'];
		$tgl = date("Y-m-d H:i:s");

		$sql = "UPDATE tbl_wappin_konfig SET notif_transaksi = '$notif_transaksi', updated_at = '$tgl', id_user = '$sesi' WHERE id_jenjang = $id_jenjang";
		$res = db_query($sql);
		if ($res) {
			$cb['stat'] = 1;
			$cb['ket'] = "Berhasil!";
		} else {
			$cb['ket'] = "Gagal!";
		}
		echo json_encode($cb);
	}
	function wappin_notif_byr($id,$id_order)
	{
		global $con;
		$sesi = $_SESSION['id'];

		$querykls = "SELECT master_jenis_transaksi.nama_transaksi,data_jenjang.unit,data_siswa.id_jenjang,data_siswa.nama_siswa,data_siswa.telp_one,transaksi_siswa.nis_from_idsiswa,transaksi_siswa.id_jenis_transaksi,transaksi_siswa.nominal_dibayarkan
			FROM transaksi_siswa_sementara transaksi_siswa 
				INNER JOIN data_siswa ON data_siswa.id_siswa=transaksi_siswa.nis_from_idsiswa
				INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_siswa.id_jenjang
				INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE transaksi_siswa.id_user='$sesi' AND transaksi_siswa.nis_from_idsiswa='$id'
				ORDER BY id_transaksi ASC";
		$item_bayar  = [];
		$total_bayar = 0;
		$nama = '';
		$unit = '';
		$telp = '';
		$result = mysqli_query($con, $querykls);
		while ($rs = mysqli_fetch_array($result)) {
			$telp = str_replace("+", "", $rs['telp_one']);
			if ($telp == "" || $telp == null) {
			} else {
				$ptng  = substr($telp, 0, 2);
				$ptng2 = substr($telp, 1, 17);
				if ($ptng == '08') {
					$telp = '62' . $ptng2;
				}
			}
			array_push($item_bayar, $rs['nama_transaksi'] . " Rp. " . number_format($rs['nominal_dibayarkan'], 0, "", "."));
			$total_bayar += $rs['nominal_dibayarkan'];
			$unit = "*" . $rs['unit'] . "*.";
			$nama = $rs['nama_siswa'];
		}
		$item_bayar  = implode(", ", $item_bayar);
		$total_bayar = number_format($total_bayar, 0, "", ".");

		$noti        = "SELECT * FROM notif_wa_tunai";
		$resultnoti  = mysqli_query($con, $noti);
		while ($notie = mysqli_fetch_array($resultnoti)) {
			$header     = "*" . $notie['header'] . "* ";
		}

		if ($telp != "" || $telp != null) {
			//  $send_wappin = $this->send_message_trans($telp,$nama,$total_bayar,$item_bayar);
			//$send_wappin = $this->notif_wa($telp, $nama, $total_bayar, $item_bayar);
			$send_wappin = $this->notif_wa_draft($telp, $nama, $total_bayar, $item_bayar,$id_order); // tambahkan ini buat limit harian
			$hasil = json_decode($send_wappin);
			$message = "Success. ";
			if (isset($hasil->status)) {
				$status = $hasil->status;
				$message = $hasil->message . ". ";
			}
			//echo $message;
		}
	}
	function wappin_notif_byr_saku($id)
	{
		global $con;
		$querykls = "
			SELECT tbl_uangku.jumlah,data_siswa.nama_siswa,data_siswa.id_jenjang,data_jenjang.unit,data_siswa.telp_one 
			FROM tbl_uangku 
				INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
				INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_siswa.id_jenjang
			WHERE id_tbl_uangku='$id'";
		$result = mysqli_query($con, $querykls);
		$nama = '';
		$jumlah = '';
		$telp = '';
		while ($rs = mysqli_fetch_array($result)) {

			$telp = str_replace("+", "", $rs['telp_one']);
			if ($telp == "" || $telp == null) {
			} else {
				$ptng  = substr($telp, 0, 2);
				$ptng2 = substr($telp, 1, 17);
				if ($ptng == '08') {
					$telp = '62' . $ptng2;
				}
			}
			$pesanwadua = "*" . $rs['unit'] . "*.";
			$nama       = $rs['nama_siswa'];
			$jumlah     = number_format($rs['jumlah'], 0, "", ".");
		}

		if ($telp != "" || $telp != null) {
			//$send = $this->send_message_saku($telp,"top up uang saku tunai",$nama,$jumlah);
			$send = $this->notif_wa_saku($telp, $nama, $jumlah);
			$hasil = json_decode($send);
			$message = "Success.";
			if (isset($hasil->status)) {
				$status = $hasil->status;
				$message = $hasil->message . ". ";
			}
			echo $message;
		}
	}
	function wappin_notif_tarik_saku($id, $ket)
	{
		global $con;
		$querykls = "
			SELECT tbl_uangku.jumlah,data_siswa.nama_siswa,data_siswa.id_jenjang,data_jenjang.unit,data_siswa.telp_one 
			FROM tbl_uangku 
				INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
				INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_siswa.id_jenjang
			WHERE id_tbl_uangku='$id'";
		$result = mysqli_query($con, $querykls);
		while ($rs = mysqli_fetch_array($result)) {

			$telp = str_replace("+", "", $rs['telp_one']);
			if ($telp == "" || $telp == null) {
			} else {
				$ptng  = substr($telp, 0, 2);
				$ptng2 = substr($telp, 1, 17);
				if ($ptng == '08') {
					$telp = '62' . $ptng2;
				}
			}
			$pesanwadua = "*" . $rs['unit'] . "*.";
			$nama       = $rs['nama_siswa'];
			$jumlah     = number_format($rs['jumlah'], 0, "", ".");
		}

		if ($telp != "" || $telp != null) {
			//$send_wappin = $this->send_message_saku($telp,"penarikan uang saku tunai",$nama,$jumlah);
			$send = $this->notif_wa_tarik_saku($telp, $nama, $jumlah);
			$hasil = json_decode($send);
			$message = "Success. ";
			if (isset($hasil->status)) {
				$status = $hasil->status;
				$message = $hasil->message . ". ";
			}
			//echo $message;
		}
	}

	/* API WAPPIN 2.O  */
	function token()
	{
		$base_url = "https://api.chat.wappin.app/";

		$url_login  = $base_url . 'v1/users/login';

		$username = '97adf842bcaa6b02febcaaf6ec6d4e7bf1549c68';
		$password = 'bccd0baacaeb752d757d1229c4e179e31291960f';

		$session = curl_init($url_login);
		curl_setopt($session, CURLOPT_URL, $url_login);
		curl_setopt($session, CURLOPT_POST, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_USERPWD, "$username:$password");
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($session);
		$httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

		curl_close($session);

		$token = "";
		if ($httpCode === 200) {
			$respon = json_decode($result, true);
			$token = $respon['users'][0]['token'];
			return $token;
		} else {
			echo "Error: HTTP status code: $httpCode";
		}
	}
	function send_message_trans($number, $nama, $total, $rincian)
	{
		$sesi = $_SESSION['id'];
		$log_pesan = "Terima kasih, {{1}} atas nama " . $nama . " sejumlah Rp " . $total . " pada tanggal " . tgl_indo(date("Y-m-d")) . " dengan rincian sebagai berikut: " . $rincian . " sudah berhasil.";

		if (isset($number)) {
			$base_url = "https://api.chat.wappin.app/";

			$token = $this->token();
			$tok = "Authorization: Bearer " . $token;

			$url = $base_url . "v1/messages";

			$parameters_body[] = array(
				'type'  => "text",
				'text'  => "pembayaran sekolah secara tunai"
			);
			$a = array(
				'type'  => "text",
				'text'  => $nama
			);
			$b = array(
				'type'  => "text",
				'text'  => "*Rp " . $total . "*"
			);
			$c = array(
				'type'  => "text",
				'text'  => "*" . tgl_indo(date("Y-m-d")) . "*"
			);
			$d = array(
				'type'  => "text",
				'text'  => $rincian
			);

			array_push($parameters_body, $a, $b, $c, $d);

			$components[] = array(
				'type'  => "body",
				'parameters'    => $parameters_body
			);
			$parameters_header = array(
				'type'  => "header",
				'parameters'    => [array(
					'type'  => "text",
					'text'  => SCHOOL_NAME // nama sekolah disesuaikan
				)]
			);
			array_push($components, $parameters_header);

			$request = array(
				'to'    => $number,
				'type'  => "template",
				'template'  => array(
					'name'  => "notif_transaksi", // nama template disesuaikan
					'namespace' => "8f33dd73_b08d_42f7_88a2_dd2687056515",
					'language'  => array(
						'policy'    => "deterministic",
						'code'      => "id"
					),
					'components'    => $components
				)
			);
			$request = json_encode($request);

			$session = curl_init($url);
			curl_setopt($session, CURLOPT_URL, $url);
			curl_setopt($session, CURLOPT_HTTPHEADER, array($tok, 'Content-Type: application/json'));
			curl_setopt($session, CURLOPT_VERBOSE, true);
			curl_setopt($session, CURLOPT_POST, true);
			curl_setopt($session, CURLOPT_POSTFIELDS, $request);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

			$result = curl_exec($session);
			$httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

			curl_close($session);

			if ($httpCode === 200) {
				$response = $result;
				$query = "
	            	INSERT INTO 
	            		ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
	            	VALUES
	                	( '$number', '$log_pesan', '$response', '$httpCode', '$sesi', '750', '2' )
	            ";
				$ins = db_query($query);
			} else {
				$response = json_encode(array(
					'status'  => $httpCode,
					'message' => "Error: HTTP status code"
				));
				$query = "
	            	INSERT INTO 
	            		ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
	            	VALUES
	                	( '$number', '$log_pesan', '$response', '$httpCode', '$sesi', '750', '2' )
	            ";
				$ins = db_query($query);
			}
			return $response;
		}
	}
	function send_message_saku($number, $ket, $nama, $total)
	{
		$sesi = $_SESSION['id'];
		$log_pesan = "Terima kasih, " . $ket . " atas nama " . $nama . " sejumlah Rp " . $total . " pada tanggal " . tgl_indo(date("Y-m-d")) . " sudah berhasil.";

		if (isset($number)) {
			$base_url = "https://api.chat.wappin.app/";

			$token = $this->token();
			$tok = "Authorization: Bearer " . $token;

			$url = $base_url . "v1/messages";

			$parameters_body[] = array(
				'type'  => "text",
				'text'  => $ket
			);
			$a = array(
				'type'  => "text",
				'text'  => $nama
			);
			$b = array(
				'type'  => "text",
				'text'  => "*Rp* " . $total
			);
			$c = array(
				'type'  => "text",
				'text'  => "*" . tgl_indo(date("Y-m-d")) . "*"
			);

			array_push($parameters_body, $a, $b, $c);

			$components[] = array(
				'type'  => "body",
				'parameters'    => $parameters_body
			);
			$parameters_header = array(
				'type'  => "header",
				'parameters'    => [array(
					'type'  => "text",
					'text'  => SCHOOL_NAME // nama sekolah disesuaikan
				)]
			);
			array_push($components, $parameters_header);

			$request = array(
				'to'    => $number,
				'type'  => "template",
				'template'  => array(
					'name'  => "notif_saku", // nama template disesuaikan
					'namespace' => "8f33dd73_b08d_42f7_88a2_dd2687056515",
					'language'  => array(
						'policy'    => "deterministic",
						'code'      => "id"
					),
					'components'    => $components
				)
			);
			$request = json_encode($request);

			$session = curl_init($url);
			curl_setopt($session, CURLOPT_URL, $url);
			curl_setopt($session, CURLOPT_HTTPHEADER, array($tok, 'Content-Type: application/json'));
			curl_setopt($session, CURLOPT_VERBOSE, true);
			curl_setopt($session, CURLOPT_POST, true);
			curl_setopt($session, CURLOPT_POSTFIELDS, $request);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

			$result = curl_exec($session);
			$httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

			curl_close($session);

			if ($httpCode === 200) {
				$response = $result;
				$query = "
	            	INSERT INTO 
	            		ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
	            	VALUES
	                	( '$number', '$log_pesan', '$response', '$httpCode', '$sesi', '750', '3' )
	            ";
				$ins = db_query($query);
			} else {
				$response = json_encode(array(
					'status'  => $httpCode,
					'message' => "Error: HTTP status code"
				));
				$query = "
	            	INSERT INTO 
	            		ref_log_wa_gw ( phone, pesan, respon, code, id_user, harga, id_template_wa )
	            	VALUES
	                	( '$number', '$log_pesan', '$response', '$httpCode', '$sesi', '750', '3' )
	            ";
				$ins = db_query($query);
			}
			return $response;
		}
	}
	/* END API WAPPIN 2.O  */

	function testing()
	{
		require('app/lib/html_table.php');
	}


	////CODING LAMA 
	function insert_trans()
	{
		global $con;
		$sesi = isset($_SESSION['id']) ? $_SESSION['id'] : "";
		if (!isset($_SESSION['id'])) {
			echo 'sesikosong';
		} else {
			$xxx = isset($_GET['xxx']) ? $_GET['xxx'] : "";
			$a = isset($_GET['a']) ? $_GET['a'] : "";
			$b = isset($_GET['b']) ? $_GET['b'] : "";
			$c = isset($_GET['c']) ? $_GET['c'] : "";
			$d = isset($_GET['d']) ? $_GET['d'] : "";
			$e = isset($_GET['e']) ? $_GET['e'] : "";
			$f = isset($_GET['f']) ? $_GET['f'] : "";
			$y = isset($_GET['y']) ? $_GET['y'] : "";
			$th = isset($_GET['th']) ? $_GET['th'] : "";
			$jns = isset($_GET['jns']) ? $_GET['jns'] : "";
			$infaq = isset($_GET['infaq']) ? $_GET['infaq'] : "";
			$id_kategori_tr = isset($_GET['id_kategori_tr']) ? $_GET['id_kategori_tr'] : "";
			if ($id_kategori_tr == '') {
				$id_kategori_tr = 'null';
			}
			$l = isset($_GET['l']) ? $_GET['l'] : "";
			$sql = mysqli_query($con, "SELECT * FROM transaksi_siswa_sementara WHERE id_jenis_transaksi='$d' AND id_user='$sesi'");
			$count = mysqli_num_rows($sql);
			//var_dump($count);
			if ($jns == 'Ikhlas' || $jns == 'Ikhlas Saku') {
				$e = $l;
				$y = 0;
			}
			if ($count < 1) {
				if ($jns == 'kartu') {
					$send = mysqli_query($con, "INSERT INTO transaksi_siswa_sementara(
					nis_from_idsiswa,
					id_master_trans_sis,
					id_kelas,
					id_jenis_transaksi,
					nominal_ketetapan,
					id_status,
					nominal_dibayarkan,
					infaq,
					id_user,
					nominal_kekurangan,
					ket,
					id_kel,
					tipe,
					status_tipe,
					id_sesi) 
					VALUES (
					'$a',
					'$b',
					'$c',
					'$d',
					'$e',
					'$f',
					'$l',
					'$infaq',
					'$sesi',
					'$y',
					'Uang Saku',
					$id_kategori_tr,
					'kartu',
					1,
					'$th')");
				} else {
					echo $sql = "INSERT INTO transaksi_siswa_sementara(nis_from_idsiswa,id_master_trans_sis,id_kelas,id_jenis_transaksi,nominal_ketetapan,id_status,nominal_dibayarkan,infaq,id_user,nominal_kekurangan,ket,id_kel,tipe) 
					VALUES (
					'$a',
					'$b',
					'$c',
					'$d',
					'$e',
					'$f',
					'$l',
					'$infaq',
					'$sesi',
					'$y',
					'$jns',
					$id_kategori_tr,
					'')";
					$send = mysqli_query($con, $sql);
				}
			} else if ($count > 0) {
			}
		}
	}

	function insert_trans_qris($xxx = null, $a = null, $b = null, $c = null, $d = null, $e = null, $f = null, $l = null, $infaq = null, $y = null, $id_kategori_tr = null)
	{
		global $con;
		//$sesi = isset($_SESSION['id']) ? $_SESSION['id'] : "";
		if (!isset($_SESSION['id'])) {
			echo 'sesikosong';
		} else {
			$sesi = isset($_GET['xxx']) ? $_GET['xxx'] : "";
			$a = isset($_GET['a']) ? $_GET['a'] : "";
			$b = isset($_GET['b']) ? $_GET['b'] : "";
			$c = isset($_GET['c']) ? $_GET['c'] : "";
			$d = isset($_GET['d']) ? $_GET['d'] : "";
			$e = isset($_GET['e']) ? $_GET['e'] : "";
			$f = isset($_GET['f']) ? $_GET['f'] : "";
			$y = isset($_GET['y']) ? $_GET['y'] : "";
			$jns = isset($_GET['jns']) ? $_GET['jns'] : "";
			$infaq = isset($_GET['infaq']) ? $_GET['infaq'] : "";
			$id_kategori_tr = isset($_GET['id_kategori_tr']) ? $_GET['id_kategori_tr'] : "";
			if ($id_kategori_tr == '') {
				$id_kategori_tr = 'null';
			}
			$l = isset($_GET['l']) ? $_GET['l'] : "";
			$sql = mysqli_query($con, "SELECT * FROM transaksi_siswa_sementara WHERE id_jenis_transaksi='$d' AND id_user='$sesi'");
			$count = mysqli_num_rows($sql);
			var_dump($count);
			if ($jns == 'Ikhlas' || $jns == 'Ikhlas Saku') {
				$e = $l;
				$y = 0;
			}
			if ($count < 1) {
				$send = mysqli_query($con, "INSERT INTO transaksi_siswa_sementara(nis_from_idsiswa,id_master_trans_sis,id_kelas,id_jenis_transaksi,nominal_ketetapan,id_status,nominal_dibayarkan,infaq,id_user,nominal_kekurangan,ket,id_kel) 
					VALUES (
					'$a',
					'$b',
					'$c',
					'$d',
					'$e',
					'$f',
					'$l',
					'$infaq',
					'$sesi',
					'$y',
					'$jns',
					$id_kategori_tr)");
			} else if ($count > 0) {
			}
		}
	}
}
