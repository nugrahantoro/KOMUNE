<?php
class laporan_rekap_tagihan extends Controller
{
	var $content;

	//
	////
	////// PHP CODE FOR FORMAT SURAT
	////
	// 

	function get_form_surat()
	{
		$data = db_assoc("SELECT * FROM surat LIMIT 1");
		echo json_encode($data,JSON_PRETTY_PRINT);
	}
	
	function save_format_surat()
	{
		$pt = $this->escapeArrayValues($_POST);
		$query = "UPDATE surat SET
					no = '$pt[no_surat]',
					tempat = '$pt[tempat]',
					tanggal = '$pt[tanggal]',
					hal = '$pt[perihal]',
					lamp = '$pt[lampiran]',
					isi = '$pt[paragraf_1]',
					bawah = '$pt[paragraf_2]',
					isi_a = '$pt[paragraf_3]'
				LIMIT 1";
		$res = db_query($query);
		$data = [];
		if($query)
		{
			$data['stat']=1;
			$data['ket']="Success";
		}
		else
		{
			$data['stat']=0;
			$data['ket']="Failed";
		}
		echo json_encode($data);
	}

	function escapeArrayValues($array) {
		global $con;
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = escapeArrayValues($value);
			} else {
				$array[$key] = mysqli_real_escape_string($con, $value);
			}
		}
		return $array;
	}

	//
	//// CODE END
	
	public function index($param1 = "", $param2 = "")
	{

		$data["title"] = "Laporan Rekap Tagihan";
		$data["href"] = "";
		$this->view("main/view/header", $data);
		$this->view("laporan_rekap_tagihan/view/v_laporan_rekap_tagihan");
		$this->view("main/view/footer");
	}

	public function ex($param1, $params2)
	{
		$ex_controller = '';
		if (file_exists("app/module/laporan_rekap_tagihan/controller/$param1.php")) {
			$ex_controller = $param1;
		}
		if ($ex_controller != '') {
			require_once "$param1.php";
			$ex_controller = new $ex_controller;
			//Method
			if (isset($params2)) {
				if (method_exists($ex_controller, $params2)) {
					//Run Controller & Method. Send Paramaters if exists
					call_user_func_array([$ex_controller, $params2], []);
				}
			}
		}
	}
	function tampil_tahun()
	{
		global $con;
		$querykls = "SELECT * FROM data_tahunajar ORDER BY aktif DESC, nama_tahunajar DESC";
		$result = mysqli_query($con, $querykls);
		echo '<select style="margin-left: 10px;" id="pilih_tahun" class= "btn btn-md btn-light form-control" onChange="tampil_kelas_tahun(this);">';
		// echo '<option value="0">Pilih Tahun</option>';
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="' . $dt['id_tahunajar'] . '">' . $dt['nama_tahunajar'] . '</option>';
		}
		echo '</select>';
	}
	function tampil_jenjang()
	{
		global $con;
		$sesi = $_SESSION['id'];
		// var_dump($sesi);
		$query = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
					INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'";
		$resultjen = mysqli_query($con, $query);
		echo '<select  id="pilih_jenjang" class= "btn btn-md btn-light form-control" onChange="tampil_kelas_jenjang(this);">';
		// echo '<option value="0">Pilih Jenjang</option>';
		while ($dt = mysqli_fetch_array($resultjen)) {
			echo '<option value="' . $dt['id_jenjang'] . '">' . $dt['nama_jenjang'] . '</option>';
		}
		echo '</select>';
	}

	// tampil kelas deffault
	function tampil_kelas_awal()
	{
		global $con;
		$sesi = $_SESSION['id'];

		// tahun sekarang
		$th_aktif = "SELECT * FROM data_tahunajar ORDER BY aktif DESC, nama_tahunajar DESC LIMIT 1";
		$resulth = mysqli_query($con, $th_aktif);
		$th = mysqli_fetch_array($resulth);
		$th = $th['id_tahunajar'];
		// jenjang awal
		$jenjang_awal = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
					INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'";
		$resultjen_awal = mysqli_query($con, $jenjang_awal);
		$id_jenjang = mysqli_fetch_array($resultjen_awal);
		$id_jenjang = $id_jenjang['id_jenjang'];

		// kelas awal
		$querykls = "SELECT * FROM penempatan_siswa
		INNER JOIN data_kelas ON data_kelas.id_kelas=penempatan_siswa.id_kelas
		WHERE data_kelas.id_sesi='$th' AND id_jenjang='$id_jenjang'
		GROUP BY penempatan_siswa.id_kelas ORDER BY nama_kelas ASC";
		$result = mysqli_query($con, $querykls);
		// echo $querykls;
		echo "<select id='pilih_kelas' class='btn btn-md btn-light form-control'>";
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="' . $dt['id_kelas'] . '">' . $dt['nama_kelas'] . '</option>';
		}
		echo '</select>';
	}

	function tampil_kelas()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$querykls = "SELECT * FROM penempatan_siswa
		INNER JOIN data_kelas ON data_kelas.id_kelas=penempatan_siswa.id_kelas
		WHERE data_kelas.id_sesi='$th' AND id_jenjang='$id_jenjang'
		GROUP BY penempatan_siswa.id_kelas ORDER BY nama_kelas ASC";
		$result = mysqli_query($con, $querykls);
		// echo $querykls;
		echo "<select id='pilih_kelas' class='btn btn-md btn-light form-control'>";
		// echo '<option value="0">Pilih Kelas</option>';
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="' . $dt['id_kelas'] . '">' . $dt['nama_kelas'] . '</option>';
		}
		echo '</select>';
	}
	function hasil_rekap_awal($cetak = 0)
	{
		global $con;
		$sesi = $_SESSION['id'];
		// tahun sekarang
		$th_aktif = "SELECT * FROM data_tahunajar ORDER BY aktif DESC, nama_tahunajar DESC LIMIT 1";
		$resulth = mysqli_query($con, $th_aktif);
		$th = mysqli_fetch_array($resulth);
		$th = $th['id_tahunajar'];
		// echo 'Anom';

		// jenjang sekarang
		$jenjang_awal = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
					INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi' LIMIT 1";
		$resultjen_awal = mysqli_query($con, $jenjang_awal);
		$id_jenjang = mysqli_fetch_array($resultjen_awal);
		$id_jenjang = $id_jenjang['id_jenjang'];
		// var_dump($th);
		// var_dump($id_jenjang);

		// kelas sekarang
		$querykls = "SELECT * FROM penempatan_siswa
		INNER JOIN data_kelas ON data_kelas.id_kelas=penempatan_siswa.id_kelas
		WHERE data_kelas.id_sesi='$th' AND id_jenjang='$id_jenjang'
		GROUP BY penempatan_siswa.id_kelas ORDER BY nama_kelas ASC LIMIT 1";
		$resultkls = mysqli_query($con, $querykls);
		$id_kls = 0;
		while ($bh = mysqli_fetch_array($resultkls)) {
			$id_kls = $bh['id_kelas'];
		}

		$hari_ini = date("Y-m-d");
		// tamnggal awal
		$a = date('Y-m-01', strtotime($hari_ini));

		// tanggal akhir
		$b = date('Y-m-d', strtotime($hari_ini));



		$bl = substr($b, 5, 2);
		$thn = substr($b, 0, 4);
		$namaBulan = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		$idBulan = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
		$jml = count($namaBulan);
		$bl = floatval($bl) - 1;
		for ($i = 0; $i < $jml; $i++) {
			if ($i == $bl) {
				$bulan = $namaBulan[$i];
			}
		}
		$id = "";
		$sq = mysqli_query($con, "SELECT GROUP_CONCAT(nis_from_idsiswa) as id FROM penempatan_siswa WHERE id_kelas=$id_kls");
		while ($rs = mysqli_fetch_array($sq)) {
			$id = $rs['id'];
		}
		$que_t = "
			SELECT
				tb_ketetapan.*,
				tb_bayar.bayar,
				tb_ketetapan.jml - IFNULL( tb_bayar.bayar, 0 ) AS tagihan 
			FROM
				(
			SELECT
				master_alokasi_transaksi_all.id_master_trans_sis,
				nama_siswa,
				telp_one,
				nis_from_idsiswa as id_siswa,
				nis_from_idsiswa,
				nama_transaksi,
				SUM( nominal ) AS jml,
				master_alokasi_transaksi_all.id_jenis_transaksi,
				master_alokasi_transaksi_all.id_sesi,
				data_tahunajar.nama_tahunajar 
			FROM
				master_alokasi_transaksi_all
				INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
				INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi 
				INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar=master_alokasi_transaksi_all.id_sesi	
			WHERE
				master_alokasi_transaksi_all.nis_from_idsiswa IN ($id) 
				-- master_alokasi_transaksi_all.id_kelas = $id_kls 
				AND tgl_jth BETWEEN '$a' 
				AND '$b' 
			GROUP BY master_alokasi_transaksi_all.id_master_trans_sis
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
				master_alokasi_transaksi_all.nis_from_idsiswa IN ($id) 
				-- master_alokasi_transaksi_all.id_kelas = $id_kls
				AND master_alokasi_transaksi_all.tgl_jth BETWEEN '$a' 
				AND '$b' 
			GROUP BY master_alokasi_transaksi_all.id_master_trans_sis
				) AS tb_bayar ON tb_bayar.id_master_trans_sis = tb_ketetapan.id_master_trans_sis
				ORDER BY nama_siswa,id_jenis_transaksi
		";
		$sql = mysqli_query($con, $que_t);
		$tagihan = [];
		if (!$sql || mysqli_num_rows($sql) == 0) {
		}
		else{
			while ($rs = mysqli_fetch_array($sql)) {
				if (!isset($tagihan[$rs['id_siswa']])) {
					$tagihan[$rs['id_siswa']] = [];
				}
				array_push($tagihan[$rs['id_siswa']], $rs);
			}	//echo $que_t;
		}
		echo ' 
			<input type="hidden" id="th" value="' . $th . '">
			<input type="hidden" id="id_kls" value="' . $id_kls . '">
			<input type="hidden" id="a" value="' . $a . '">
			<input type="hidden" id="b" value="' . $b . '">
			<button class="btn btn-sm btn-success" style="display:none;" type="button" id="btnWa" onclick="sendWa()">Kirim Whatsapp Semua</button>
			<button class="btn btn-sm btn-warning" style="display:none;" onclick="getScan()" >Scan Ulang Whatsapp</button>
			<table id="tabsesur" class="table table-sm m-0 table-bordered" width="100%" border="' . $cetak . '">
				<thead>
					<tr>
						<th>No</th>
						<th>Nama Warga</th>
						<th>Tagihan Iuran</th>
					</tr>
				</thead>
				<tbody>
		';

		$no = 0;
		$totalall = 0;
		$telp = "";
		$nama = "";
		foreach ($tagihan as $rs) {
			$inner = "";
			$telp = $rs[0]['telp_one'];
			$nama = $rs[0]['nama_siswa'];
			$id_sis = $rs[0]['id_siswa'];
			$no++;
			echo '<tr>
					<th scope="row">' . $no . '</th>
					<td style="display:none;">' . $rs[0]['id_siswa'] . '</td>
					<td>' . $rs[0]['nama_siswa'] . '</td>';
			$total = 0;
			$html = '';
			$tag = "";
			$tag2 = "";
			$nom = 1;
			$nn = 1;
			$thnn = '';
			$thnn2 = '';
			for ($i = 0; $i < count($rs); $i++) {
				$thnn2 = $rs[$i]['id_sesi'];
				if ($nn == 1) {
					$tag .= '%0A*Tahun Ajaran* *' . $rs[$i]['nama_tahunajar'] . "* %0A";
				};

				if ($thnn != $thnn2 && $nn > 1) {
					$tag .= '%0A*Tahun Ajaran* *' . $rs[$i]['nama_tahunajar'] . "* %0A";
					$nom = 1;
				}

				if ($rs[$i]['tagihan'] == 0) {
				} else {
					$tag .= $nom . ". " . $rs[$i]['nama_transaksi'] . " - Rp." . number_format($rs[$i]['tagihan'], 0, "", ".") . "%0A";
					$tag2 .= $nom . ". " . $rs[$i]['nama_transaksi'] . " - Rp." . number_format($rs[$i]['tagihan'], 0, "", ".") . "\n";
					$nom++;
					$html .= "<td>" . $rs[$i]['nama_transaksi'] . "<b> " . number_format($rs[$i]['tagihan']) . "</b></td>";
				}
				$total += $rs[$i]['tagihan'];
				$id = $rs[$i]['nis_from_idsiswa'];
				$nn++;
				$thnn = $rs[$i]['id_sesi'];
			}
			if ($total == 0) {
				echo '
					<td class="bg-warning">Total 0</td>
				';
			} else {
				echo $html;
				echo '<td class="bg-warning">Total <b>' . number_format($total) . '</b></td>';
				if ($cetak == 0) {
					echo '<td width="80px"><button class="btn btn-sm btn-primary"  onclick="cetak_surat();">
						<i class="bx bx-printer"></i> Cetak
					</button></td>';
				}
			}

			if ($telp == "" || $telp == null) {
			} else {
				$ptng = substr($telp, 0, 2);
				$ptng2 = substr($telp, 1, 17);
				if ($ptng == '08') {
					$telp = '62' . $ptng2;
				}
				$se = "SELECT data_jenjang.unit FROM data_jenjang WHERE id_jenjang=$id_jenjang";
				$sqlse = mysqli_query($con, $se);
				while ($rs = mysqli_fetch_array($sqlse)) {
					$isi = "Halo Bapak/Ibu Warga\n\n"
						. "$nama 👋\n\n"
						. "Kami ingin menginformasikan bahwa masih terdapat tagihan/iuran warga sampai periode $bulan $thn sebesar:\n\n"
						. "Rp. " . number_format($total, 0, "", ".") . "\n\n"
						. "Dengan rincian sebagai berikut:\n"
						. "$tag2\n\n"
						. "Terima kasih atas perhatian dan partisipasi Bapak/Ibu untuk lingkungan bersama 🙏";

					$pesanwa = "*Komune - Komunitas Warga*%0A%0A"
						. "Halo Bapak/Ibu Warga%0A"
						. "*$nama* 👋%0A%0A"
						. "Kami ingin menginformasikan bahwa masih terdapat tagihan/iuran warga sampai periode *$bulan $thn* sebesar:%0A%0A"
						. "💰 *Rp. " . number_format($total, 0, "", ".") . "*%0A%0A"
						. "Dengan rincian sebagai berikut:%0A"
						. "$tag%0A%0A"
						. "🙏 Iuran warga digunakan untuk mendukung kegiatan dan kebutuhan lingkungan bersama.%0A%0A"
						. "Apabila pembayaran sudah dilakukan, mohon abaikan pesan ini.%0A%0A"
						. "Terima kasih atas perhatian dan partisipasi Bapak/Ibu 😊%0A%0A"
						. "— Pengurus Komunitas Warga";

					echo '
					<td width="50px">
						<a target="_blank" href="https://api.whatsapp.com/send?phone=' . $telp . '&text=' . $pesanwa . '">
							<button class="btn btn-sm btn-success" type="submit" name="button">
								Whatsapp
							</button>
						</a>
					</td>
					';
				}
			}
			$totalall += $total;
			echo '</tr>';
		}

		echo '
			<tr>
				<th scope="row">#</th>
				<td colspan="2"><b>TOTAL TAGIHAN : Rp.' . number_format($totalall) . '</b></td>
			</tr>
			</tbody>
			</table>
		';
	}
	function hasil_rekap($cetak = 0)
	{
		global $con;
		$a = isset($_GET['a']) ? $_GET['a'] : "";
		$b = isset($_GET['b']) ? $_GET['b'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_kls = isset($_GET['id_kls']) ? $_GET['id_kls'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$bl = substr($b, 5, 2);
		$thn = substr($b, 0, 4);
		$namaBulan = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		$idBulan = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
		$jml = count($namaBulan);
		$bl = floatval($bl) - 1;
		for ($i = 0; $i < $jml; $i++) {
			if ($i == $bl) {
				$bulan = $namaBulan[$i];
			}
		}
		$id = "";
		$sq = mysqli_query($con, "SELECT GROUP_CONCAT(nis_from_idsiswa) as id FROM penempatan_siswa WHERE id_kelas=$id_kls");
		while ($rs = mysqli_fetch_array($sq)) {
			$id = $rs['id'];
		}
		$que_t = "
			SELECT
				tb_ketetapan.*,
				tb_bayar.bayar,
				tb_ketetapan.jml - IFNULL( tb_bayar.bayar, 0 ) AS tagihan 
			FROM
				(
			SELECT
				master_alokasi_transaksi_all.id_master_trans_sis,
				nama_siswa,
				telp_one,
				nis_from_idsiswa as id_siswa,
				nis_from_idsiswa,
				nama_transaksi,
				SUM( nominal ) AS jml,
				master_alokasi_transaksi_all.id_jenis_transaksi,
				master_alokasi_transaksi_all.id_sesi,
				data_tahunajar.nama_tahunajar 
			FROM
				master_alokasi_transaksi_all
				INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
				INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi 
				INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar=master_alokasi_transaksi_all.id_sesi	
			WHERE
				master_alokasi_transaksi_all.nis_from_idsiswa IN ($id) 
				-- master_alokasi_transaksi_all.id_kelas = $id_kls 
				AND tgl_jth BETWEEN '$a' 
				AND '$b' 
			GROUP BY master_alokasi_transaksi_all.id_master_trans_sis
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
				master_alokasi_transaksi_all.nis_from_idsiswa IN ($id) 
				-- master_alokasi_transaksi_all.id_kelas = $id_kls
				AND master_alokasi_transaksi_all.tgl_jth BETWEEN '$a' 
				AND '$b' 
			GROUP BY master_alokasi_transaksi_all.id_master_trans_sis
				) AS tb_bayar ON tb_bayar.id_master_trans_sis = tb_ketetapan.id_master_trans_sis
				ORDER BY nama_siswa,id_jenis_transaksi
		";
		$sql = mysqli_query($con, $que_t);
		$tagihan = [];
		if (mysqli_num_rows($sql)<1) {
		}
		else{
			while ($rs = mysqli_fetch_array($sql)) {
				if (!isset($tagihan[$rs['id_siswa']])) {
					$tagihan[$rs['id_siswa']] = [];
				}
				array_push($tagihan[$rs['id_siswa']], $rs);
			}	//echo $que_t;
		}
		echo ' 
		<input type="hidden" id="th" value="' . $th . '">
		<input type="hidden" id="id_kls" value="' . $id_kls . '">
		<input type="hidden" id="a" value="' . $a . '">
		<input type="hidden" id="b" value="' . $b . '">
		<button class="btn btn-sm btn-success" style="display:none;" type="button" id="btnWa" onclick="sendWa()">Kirim Whatsapp Semua</button>
		<button class="btn btn-sm btn-warning" style="display:none;" onclick="getScan()" >Scan Ulang Whatsapp</button>
		<table id="tabsesur" class="table table-sm m-0 table-bordered" width="100%" border="' . $cetak . '">
			<thead>
				<tr>
					<th>No</th>
					<th>Nama Warga</th>
					<th>Tagihan Iuran</th>
				</tr>
			</thead>
			<tbody>
		';

		$no = 0;
		$totalall = 0;
		$telp = "";
		$nama = "";
		foreach ($tagihan as $rs) {
			$inner = "";
			$telp = $rs[0]['telp_one'];
			$nama = $rs[0]['nama_siswa'];
			$id_sis = $rs[0]['id_siswa'];
			$no++;
			echo '<tr>
					<th scope="row">' . $no . '</th>
					<td style="display:none;">' . $rs[0]['id_siswa'] . '</td>
					<td>' . $rs[0]['nama_siswa'] . '</td>';
			$total = 0;
			$html = '';
			$tag = "";
			$tag2 = "";
			$nom = 1;
			$nn = 1;
			$thnn = '';
			$thnn2 = '';
			for ($i = 0; $i < count($rs); $i++) {
				$thnn2 = $rs[$i]['id_sesi'];
				if ($nn == 1) {
					$tag .= '%0A*Tahun Ajaran* *' . $rs[$i]['nama_tahunajar'] . "* %0A";
				};

				if ($thnn != $thnn2 && $nn > 1) {
					$tag .= '%0A*Tahun Ajaran* *' . $rs[$i]['nama_tahunajar'] . "* %0A";
					$nom = 1;
				}

				if ($rs[$i]['tagihan'] == 0) {
				} else {
					$tag .= $nom . ". " . $rs[$i]['nama_transaksi'] . " - Rp." . number_format($rs[$i]['tagihan'], 0, "", ".") . "%0A";
					$tag2 .= $nom . ". " . $rs[$i]['nama_transaksi'] . " - Rp." . number_format($rs[$i]['tagihan'], 0, "", ".") . "\n";
					$nom++;
					$html .= "<td>" . $rs[$i]['nama_transaksi'] . "<b> " . number_format($rs[$i]['tagihan']) . "</b></td>";
				}
				$total += $rs[$i]['tagihan'];
				$id = $rs[$i]['nis_from_idsiswa'];
				$nn++;
				$thnn = $rs[$i]['id_sesi'];
			}
			if ($total == 0) {
				echo '
					<td class="bg-warning">Total 0</td>
				';
			} else {
				echo $html;
				echo '<td class="bg-warning">Total <b>' . number_format($total) . '</b></td>';
				if ($cetak == 0) {
					echo '<td><button class="btn btn-sm btn-primary"  onclick="cetak_surat();">
						<i class="bx bx-printer"></i> Cetak
					</button></td>';
				}
			}

			if ($telp == "" || $telp == null) {
			} else {
				$ptng = substr($telp, 0, 2);
				$ptng2 = substr($telp, 1, 17);
				if ($ptng == '08') {
					$telp = '62' . $ptng2;
				}
				$se = "SELECT data_jenjang.unit FROM data_jenjang WHERE id_jenjang=$id_jenjang";
				$sqlse = mysqli_query($con, $se);
				while ($rs = mysqli_fetch_array($sqlse)) {
					$isi = "Halo Bapak/Ibu Warga\n\n"
						. "$nama 👋\n\n"
						. "Kami ingin menginformasikan bahwa masih terdapat tagihan/iuran warga sampai periode $bulan $thn sebesar:\n\n"
						. "Rp. " . number_format($total, 0, "", ".") . "\n\n"
						. "Dengan rincian sebagai berikut:\n"
						. "$tag2\n\n"
						. "Terima kasih atas perhatian dan partisipasi Bapak/Ibu untuk lingkungan bersama 🙏";

					$pesanwa = "*Komune - Komunitas Warga*%0A%0A"
						. "Halo Bapak/Ibu Warga%0A"
						. "*$nama* 👋%0A%0A"
						. "Kami ingin menginformasikan bahwa masih terdapat tagihan/iuran warga sampai periode *$bulan $thn* sebesar:%0A%0A"
						. "💰 *Rp. " . number_format($total, 0, "", ".") . "*%0A%0A"
						. "Dengan rincian sebagai berikut:%0A"
						. "$tag%0A%0A"
						. "🙏 Iuran warga digunakan untuk mendukung kegiatan dan kebutuhan lingkungan bersama.%0A%0A"
						. "Apabila pembayaran sudah dilakukan, mohon abaikan pesan ini.%0A%0A"
						. "Terima kasih atas perhatian dan partisipasi Bapak/Ibu 😊%0A%0A"
						. "— Pengurus Komunitas Warga";

					echo '
					<td width="50px">
						<a target="_blank" href="https://api.whatsapp.com/send?phone=' . $telp . '&text=' . $pesanwa . '">
							<button class="btn btn-sm btn-success" type="submit" name="button">
								Whatsapp
							</button>
						</a>
					</td>
					';							
				}
			}
			$totalall += $total;
			echo '</tr>';
		}

		echo '
	<tr>
		<th scope="row">#</th>
		<td colspan="2"><b>TOTAL TAGIHAN : Rp.' . number_format($totalall) . '</b></td>
	</tr>
	</tbody>
	</table>';
	}

	function tbl_rincian()
	{
		$gt=$_GET;
		$a = isset($_GET['a']) ? $_GET['a'] : "";
		$b = isset($_GET['b']) ? $_GET['b'] : "";
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_kls = isset($_GET['id_kls']) ? $_GET['id_kls'] : "";
		$query = "SELECT
						master_alokasi_transaksi_all.id_master_trans_sis,
						nama_transaksi,
						master_alokasi_transaksi_all.id_sesi,
						nama_tahunajar,
						nominal,
						SUM( IFNULL( nominal_dibayarkan, 0 ) ) AS dibayarkan,
						nominal - SUM( IFNULL( nominal_dibayarkan, 0 ) ) AS kekurangan,
						IF( nominal - SUM( IFNULL( nominal_dibayarkan, 0 ) )= 0, 1, 0 ) AS lunas,
						SUM(IF( nominal_dibayarkan IS NULL, 0, 1 )) jml_trans 
					FROM
						master_jenis_transaksi
						INNER JOIN data_tahunajar ON master_jenis_transaksi.id_sesi = data_tahunajar.id_tahunajar
						INNER JOIN master_alokasi_transaksi_all ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
						LEFT JOIN transaksi_siswa ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
					WHERE
						nama_transaksi != 'Uang Saku'
						AND master_alokasi_transaksi_all.nis_from_idsiswa = $id
						AND tgl_jth BETWEEN '$a' AND '$b'
					GROUP BY
						master_alokasi_transaksi_all.id_sesi,
						master_alokasi_transaksi_all.id_master_trans_sis 
					HAVING
						lunas = 0 
					ORDER BY master_alokasi_transaksi_all.id_sesi, tgl_jth
						";
		$res=db_query($query);
		$total=0;
		$rinc=[];
		while($dt = mysqli_fetch_array($res))
		{
			if(!isset($rinc[$dt['id_sesi']]))
			{
				$ta = array(
						'nama_ta'=>$dt['nama_tahunajar'],
						'id_ta'=>$dt['id_sesi'],
						'trans'=>[]);
				$rinc[$dt['id_sesi']]=$ta;
			}
			array_push($rinc[$dt['id_sesi']]['trans'],$dt);
			$total+=$dt['kekurangan'];
		}
		$tbody = "";
		foreach($rinc as $ta)
		{
			$tbody.="<tr>
						<td colspan='2'><b>PERIODE  $ta[nama_ta]</b></td>
					</tr>";
			$total_ta = 0;
			foreach($ta['trans'] as $trn)
			{
				$tbody.="<tr>
							<td>$trn[nama_transaksi]</td>
							<td style='text-align:right;'>Rp".rupiah($trn['kekurangan'])."</td>
						</tr>";
				$total_ta+=$trn['kekurangan'];
			}
			if(count($rinc)>1)
			{
				$tbody.="<tr>
							<td style='text-align:right;'><b>TOTAL</b></td>
							<td style='text-align:right;'>Rp".rupiah($total_ta)."</td>
						</tr>";
			}
		}
		$table = "
		<table>
		<tr>
			<td width='10%'></td>
			<td width='80%'>
				<table border='1' cellpadding='2px' width='100%'>
					<tr>
						<th style='text-align:center;'><b>Jenis Iuran</b></th>	
						<th style='text-align:center;'><b>Nominal</b></th>
					</tr>
						$tbody
					<tr>
						<th style='text-align:right;'><b>TOTAL TAGIHAN</b></th>
						<th style='text-align:right;'><b>Rp".rupiah($total_ta)."</b></th>
					</tr>
				</table>
			</td>
			<td width='10%'></td>
		</tr>
		</table>";
		return $table;
		
	}

	function htmltables($id = null, $th = null, $id_kls = null, $a = null, $b = null)
	{
		global $con;
		$lib_dir = "app/lib/";
		require_once($lib_dir . 'tcpdf/tcpdf.php');
		$a = isset($_GET['a']) ? $_GET['a'] : "";
		$b = isset($_GET['b']) ? $_GET['b'] : "";
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_kls = isset($_GET['id_kls']) ? $_GET['id_kls'] : "";
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
		$pdf = new TCPDF('P', 'mm', array(330, 215));
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetFont('times', '', 10);

		$pdf->AddPage();
		//$pdf->Image('app/lib/kopelkisi.png', '', '', 180, 35, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$pdf->SetXY(10, 10);
		$sql = "SELECT * FROM master_alokasi_transaksi_all 
inner join master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=master_alokasi_transaksi_all.id_jenis_transaksi
inner join data_siswa ON data_siswa.id_siswa=master_alokasi_transaksi_all.nis_from_idsiswa
inner join penempatan_siswa ON master_alokasi_transaksi_all.nis_from_idsiswa=penempatan_siswa.nis_from_idsiswa
inner join data_kelas ON data_kelas.id_kelas=master_alokasi_transaksi_all.id_kelas
WHERE master_alokasi_transaksi_all.id_sesi='$th' AND master_alokasi_transaksi_all.id_kelas='$id_kls' AND master_alokasi_transaksi_all.nis_from_idsiswa='$id' GROUP BY master_alokasi_transaksi_all.nis_from_idsiswa";
		$result = mysqli_query($con, $sql);
		$surat = "SELECT * FROM surat WHERE id='1'";
		$result_surat = mysqli_query($con, $surat);
		$pdf->SetFont('times', 'B', 12);

		$style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$stylet = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 48, 202, 48, $style);
		$pdf->Line(10, 49, 202, 49, $stylet);
		$pdf->Ln(41);
		while ($rowe = mysqli_fetch_array($result)) {

			while ($rowet = mysqli_fetch_array($result_surat)) {
				$sqlt = "SELECT *,SUM(nominal) as totnomi FROM master_alokasi_transaksi_all
inner join master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=master_alokasi_transaksi_all.id_jenis_transaksi
inner join data_tahunajar ON data_tahunajar.id_tahunajar=master_alokasi_transaksi_all.id_sesi
WHERE nis_from_idsiswa='$id' AND master_alokasi_transaksi_all.tgl_jth BETWEEN '$a' AND '$b' GROUP BY master_alokasi_transaksi_all.id_jenis_transaksi ORDER BY master_jenis_transaksi.id_jenis_transaksi";
				$resultt = mysqli_query($con, $sqlt);

				$tot = "SELECT *,SUM(nominal) as totnomitot FROM master_alokasi_transaksi_all
inner join master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=master_alokasi_transaksi_all.id_jenis_transaksi
WHERE nis_from_idsiswa='" . $rowe['nis_from_idsiswa'] . "' AND master_alokasi_transaksi_all.tgl_jth BETWEEN '$a' AND '$b' GROUP BY master_alokasi_transaksi_all.nis_from_idsiswa";
				$result_tot = mysqli_query($con, $tot);

				$sudo = "SELECT *,SUM(transaksi_siswa.nominal_dibayarkan) as a FROM master_alokasi_transaksi_all
inner join master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=master_alokasi_transaksi_all.id_jenis_transaksi
inner join transaksi_siswa ON transaksi_siswa.id_master_trans_sis=master_alokasi_transaksi_all.id_master_trans_sis
WHERE transaksi_siswa.nis_from_idsiswa='" . $rowe['nis_from_idsiswa'] . "' AND master_alokasi_transaksi_all.tgl_jth BETWEEN '$a' AND '$b'";
				$result_sudo = mysqli_query($con, $sudo);

				$pdf->SetFont('times', '', 11);
				$html = '<table border="0">
<tr>
<td width="40px" align="left">No</td>
<td width="300px" align="left">: ' . $rowet['no'] . '</td>
<td width="200px" align="right">' . $rowet['tempat'] . ', ' . $rowet['tanggal'] . '</td>
</tr>
<tr>
<td width="40px"align="left">Lamp</td>
<td width="400px" align="left">: ' . $rowet['lamp'] . '</td>
</tr>
<tr>
<td width="40px"align="left">Hal</td>
<td width="400px" align="left">: ' . $rowet['hal'] . '</td>
</tr>
</table>';
				// output the HTML content
				$pdf->writeHTML($html, true, false, true, false, '');

				$htmld = '<table border="0">
<tr>
<td width="400px" align="left">Kepada Yth.</td>
</tr>
<tr>
<td width="400px" align="left"><strong>Bpk/Ibu ' . $rowe['nama_siswa'] . '</strong></td>
</tr>
<tr>
<td width="400px" align="left">' . $rowe['nama_kelas'] . '</td>
</tr>
</table>';
$nama_siswa = $rowe['nama_siswa'];
$nama_kelas = $rowe['nama_kelas'];
				// output the HTML content
				$pdf->writeHTML($htmld, true, false, true, false, '');

				$htmll = '<table border="0">
<tr>
<td width="540px" align="justify">' . $rowet['isi'] . '</td>
</tr>
</table>';
				// output the HTML content
				$pdf->writeHTML($htmll, true, false, true, false, '');
				$htmlr = '<table border="0">
<tr>
<td width="540px" align="justify">' . $rowet['bawah'] . '</td>
</tr>
</table>';
				// output the HTML content
				$pdf->writeHTML($htmlr, true, false, true, false, '');

				$htmllz = '<table border="0">
<tr>
<td width="540px" align="left">' . $rowet['isi_a'] . '</td>
</tr>
</table>';
				// output the HTML content
				$pdf->writeHTML($htmllz, true, false, true, false, '');
			}
		}


		$htmlas = '<table border="0">
<tr>
<td width="40px" align="left"></td>
<td width="370px" align="left"></td>
<td width="230px" align="left">Ketua RT</td>
</tr>
<tr>
<td width="40px" align="left"></td>
<td width="350px" align="left"></td>
<td width="230px" align="left"><img src="ttd.png" width="100px" height="70px"></td>
</tr>
<tr>
<td width="40px" align="left"></td>
<td width="370px" align="left"></td>
<td width="230px" align="left"><strong><u>Noor Fatchurrizik</u></strong></td>
</tr>
</table>';
		// output the HTML content
		$pdf->writeHTML($htmlas, true, false, true, false, '');
		//$pdf->Ln(16);
		$htmlase = '<table border="0">

</table>';
		// output the HTML content
		$pdf->writeHTML($htmlase, true, false, true, false, '');
		$pdf->Ln(1);
		$nb = '<table border="0">
<tr>
<td>Catatan : </td>
</tr>
<tr>
<td width="100%" align="left">1. Jika ada perbedaan catatan pembayaran mohon berkenan menghubungi bagian keuangan.</td>
</tr>
<tr>
<td align="left">2. Pembayaran bisa dilakukan online transfer menggunakan Virtual Account warga.</td>
</tr>
</table>';
		// output the HTML content
		$pdf->writeHTML($nb, true, false, true, false, '');
		// output the HTML content


		$pdf->AddPage();
		////TABEL RINCIAN ----------
		$title = "<br><br><div style='text-align:center;'> RINCIAN TAGIHAN WARGA <b>$nama_siswa</b> WILAYAH <b>$nama_kelas</b></div><br>";
				$table_rincian = $this->tbl_rincian();
				$table_rincian = str_replace("'",'"', $title.$table_rincian);
				$pdf->writeHTML($table_rincian, true, false, true, false, '');

		$pdf->Output('example_006.pdf', 'I');
	}
	
	function send_tagihan_wa()
	{
		$lib_dir = "app/lib/";
		$pt=$_POST;
		$pdf_url = SCHOOL_DOMAIN."/doc/surat_tagihan/?th=$pt[id_ta]&id_kls=$pt[id_kelas]&id=$pt[id_siswa]&a=$pt[tanggal_awal]&b=$pt[tanggal_akhir]";
		$nama_sekolah = SCHOOL_NAME;
		$siswa = db_assoc("SELECT nama_siswa, telp_one FROM data_siswa WHERE id_siswa = $pt[id_siswa]");
		$nama_ta = db_get("SELECT nama_tahunajar FROM data_tahunajar WHERE id_tahunajar = $pt[id_ta]");
		$tgl = db_assoc("SELECT YEAR('$pt[tanggal_akhir]') as tahun, MONTH('$pt[tanggal_akhir]') as bulan");
		$bulan = array('',"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
		
		$query = "SELECT
						master_alokasi_transaksi_all.id_master_trans_sis,
						nama_transaksi,
						master_alokasi_transaksi_all.id_sesi,
						nama_tahunajar,
						nominal,
						SUM( IFNULL( nominal_dibayarkan, 0 ) ) AS dibayarkan,
						nominal - SUM( IFNULL( nominal_dibayarkan, 0 ) ) AS kekurangan,
						IF( nominal - SUM( IFNULL( nominal_dibayarkan, 0 ) )= 0, 1, 0 ) AS lunas,
						SUM(IF( nominal_dibayarkan IS NULL, 0, 1 )) jml_trans 
					FROM
						master_jenis_transaksi
						INNER JOIN data_tahunajar ON master_jenis_transaksi.id_sesi = data_tahunajar.id_tahunajar
						INNER JOIN master_alokasi_transaksi_all ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
						LEFT JOIN transaksi_siswa ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
					WHERE
						nama_transaksi != 'Uang Saku'
						AND master_alokasi_transaksi_all.nis_from_idsiswa = $pt[id_siswa]
						AND tgl_jth BETWEEN '$pt[tanggal_awal]' AND '$pt[tanggal_akhir]'
					GROUP BY
						master_alokasi_transaksi_all.id_sesi,
						master_alokasi_transaksi_all.id_master_trans_sis 
					HAVING
						lunas = 0 
					ORDER BY master_alokasi_transaksi_all.id_sesi, tgl_jth
						";
		$res=db_query($query);
		$total=0;
		$no=1;
		$items=[];
		while($dt=mysqli_fetch_array($res))
		{
			$total+=$dt['kekurangan'];
			$items[]=$no++.". $dt[nama_transaksi] - Rp".rupiah($dt['kekurangan']);
		}
		
		$json = '{
				  "to": "'.$siswa['telp_one'].'", 
				  "recipient_type": "individual",
				  "type": "template",
				  "template": {
					"namespace": "8f33dd73_b08d_42f7_88a2_dd2687056515",
					"name": "notif_tagihan",
					"language": {
					  "code": "id",
					  "policy": "deterministic"
					},
					"components": [
					  {
						"type": "header",
						"parameters": [
						  {
							"type": "document",
							"document": {
							  "filename": "tagihan.pdf", 
							  "link": "'.$pdf_url.'" 
							}
						  }
						]
					  },
					  {
						"type": "body",
						"parameters": [
						  {
							"type": "text",
							"text": "'.$nama_sekolah.'"
						  },
						  {
							"type": "text",
							"text": "*'.$siswa['nama_siswa'].'*"
						  },
						  {
							"type": "text",
							"text": "*'.$bulan[$tgl['bulan']].' '.$tgl['tahun'].'*"
						  },
						  {
							"type": "text",
							"text": "Rp'.rupiah($total).'"
						  },
						  {
							"type": "text",
							"text": "*'.$nama_ta.'*"
						  },
						  {
							"type": "text",
							"text": "'.implode(", ",$items).'"
						  }
						]
					  }
					]
				  }
				}';
		
		require_once($lib_dir . 'wappin.php');
		$wap = new Wappin();
		if(!$wap->isWA($siswa['telp_one']))
		{
			return;
		}
		$wap->send_message(json_decode($json));
		echo "Terkirim";
	}

	public function js($param1)
	{
		$this->get_js("laporan_rekap_tagihan/js/" . $param1);
	}
}
	




