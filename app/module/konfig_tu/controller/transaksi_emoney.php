<?php

class Transaksi_Emoney extends Controller{
	

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
		while($dt = mysqli_fetch_array($res))
		{
			$gender = $dt['gender'];
			if(stripos($gender,"laki") !== false)
			{
				$gender = "L";
			}
			if(stripos($gender,"perempuan") !== false)
			{
				$gender = "P";
			}
			$tbody .= "
			<tr class='cari-item'>
				<td class='cari-no'>".$no++."</td>
				<td class='cari-q'>".$dt['nama_siswa']."</td>
				<td class=''>".$gender."</td>
				<td class='cari-q'>".$dt['nis']."</td>
				<td class='cari-q tc'>".$dt['nama_kelas']."</td>
				<td class='cari-q' style='display:none;'>".$dt['no_kartu']."</td>
				<td class='tc'>
						<button class='btn btn-xs btn-dark' onclick='pilih_siswa(".$dt['id_siswa'].");'>
						Pilih</button>
					</td>
			</tr>
			";
		}
		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr  class='tc no_result'  style='$no_res'>
					<td colspan='5'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Nama Warga</th>
					<th>Gender</th>
					<th>ID Warga</th>
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
		$sql = "SELECT
					data_siswa.id_siswa,
					data_siswa.nama_siswa,
					data_kelas.id_kelas,
					data_siswa.gender,
					nama_kelas,
					tanggal_lahir,
					IF(nis_siswa IS NULL OR nis_siswa ='','-',nis_siswa) as nis_siswa
				FROM
					data_siswa
					LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN  data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas
				WHERE
					data_siswa.id_siswa=$id_siswa";
		$data = db_assoc($sql);
		$data['tanggal_lahir'] = tgl_indo($data['tanggal_lahir']);
		$data['saldo_us'] = $this->saldo_us($id_siswa);
		//$data['saldo_tab'] = $this->saldo_money($id_siswa);
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
	
	function load_data_tagihan()
	{
		global $con;
		$pt = Security::sanitize($_POST);
		$id = (int)($pt['id_siswa'] ?? 0);
		$data = [];

		if ($con) {
			@mysqli_query($con, "CREATE TABLE IF NOT EXISTS tbl_uangku (
				id_tbl_uangku INT AUTO_INCREMENT PRIMARY KEY,
				id_siswa INT NOT NULL,
				id_tbl_status_transaksi INT NOT NULL DEFAULT 1,
				jumlah DOUBLE NOT NULL DEFAULT 0,
				keterangan VARCHAR(255) NULL,
				tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
				kirim TINYINT(1) DEFAULT 0,
				id_user INT NULL,
				INDEX idx_uangku_siswa (id_siswa),
				INDEX idx_uangku_tgl (tanggal)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}

		$sql = "SELECT id_tbl_uangku, id_siswa, id_tbl_status_transaksi, jumlah, keterangan, tanggal 
				FROM tbl_uangku 
				WHERE id_siswa = '$id' 
				ORDER BY tanggal DESC, id_tbl_uangku DESC LIMIT 100";
		$res = @mysqli_query($con, $sql);
		$tbody = "";
		$no = 1;

		if ($res && mysqli_num_rows($res) > 0) {
			while ($dt = mysqli_fetch_assoc($res)) {
				$isMasuk = in_array($dt['id_tbl_status_transaksi'], [1, 3]);
				$badge = $isMasuk 
					? "<span class='badge bg-light text-success border font-size-11'>+ Saldo Masuk</span>" 
					: "<span class='badge bg-light text-danger border font-size-11'>- Saldo Keluar</span>";
				$warnaNominal = $isMasuk ? "text-success fw-bold" : "text-danger fw-bold";
				$prefix = $isMasuk ? "+ Rp " : "- Rp ";

				$btn_hapus = "<button class='btn btn-xs btn-outline-danger' onclick='del_topup_uangku($dt[id_tbl_uangku],$dt[id_siswa]);' title='Hapus Transaksi'><i class='bx bx-trash'></i></button>";

				$tgl = !empty($dt['tanggal']) ? date('d M Y H:i', strtotime($dt['tanggal'])) : '-';

				$tbody .= "
					<tr>
						<td class='text-center text-muted'>".$no++."</td>
						<td class='text-muted font-size-12'>".$tgl."</td>
						<td class='fw-semibold text-dark'>".Security::e($dt['keterangan'] ?: '-')."</td>
						<td>".$badge."</td>
						<td class='text-end $warnaNominal'>".$prefix.number_format($dt['jumlah'], 0, ',', '.')."</td>
						<td class='text-center'>$btn_hapus</td>
					</tr>
				";
			}
		} else {
			$tbody = "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada riwayat transaksi dompet digital</td></tr>";
		}

		$table = "
			<div class='table-responsive'>
				<table class='table table-hover align-middle mb-0' style='font-size:13px;'>
					<thead class='table-light text-muted font-size-12 text-uppercase'>
						<tr>
							<th width='5%' class='text-center'>#</th>
							<th width='20%'>Tanggal</th>
							<th>Keterangan</th>
							<th width='18%'>Tipe</th>
							<th width='20%' class='text-end'>Nominal</th>
							<th width='10%' class='text-center'>Aksi</th>
						</tr>
					</thead>
					<tbody>
						$tbody
					</tbody>
				</table>
			</div>
		";
		$data['table'] = $table;

		// Hitung Total Saldo Real-time dari tbl_uangku
		$qTop = @mysqli_query($con, "SELECT SUM(jumlah) FROM tbl_uangku WHERE id_siswa = '$id' AND id_tbl_status_transaksi IN (1, 3)");
		$rTop = ($qTop && is_object($qTop)) ? mysqli_fetch_row($qTop) : [0];
		$top = $rTop[0] ?? 0;

		$qOut = @mysqli_query($con, "SELECT SUM(jumlah) FROM tbl_uangku WHERE id_siswa = '$id' AND id_tbl_status_transaksi IN (2, 4, 5)");
		$rOut = ($qOut && is_object($qOut)) ? mysqli_fetch_row($qOut) : [0];
		$out = $rOut[0] ?? 0;

		$saldo = $top - $out;
		$data['total_tagihan'] = "Rp " . number_format($saldo, 0, ',', '.');
		$data['saldo_raw'] = $saldo;

		echo json_encode($data);
	}

	function del_topup_uangku()
	{
		global $con;
		$pt = Security::sanitize($_POST);
		$id_trans = (int)($pt['id_trans'] ?? 0);
		$id_siswa = (int)($pt['id_siswa'] ?? 0);

		$del = @mysqli_query($con, "DELETE FROM tbl_uangku WHERE id_tbl_uangku = '$id_trans' AND id_siswa = '$id_siswa'");
		if ($del) {
			@audit_log('DELETE', 'dompet_digital', $id_trans, "Menghapus transaksi dompet digital warga ID $id_siswa");
			echo json_encode(['stat' => 1, 'ket' => 'Transaksi berhasil dihapus']);
		} else {
			echo json_encode(['stat' => 0, 'ket' => 'Gagal menghapus transaksi']);
		}
	}

	function saku_topup()
	{
		global $con;
		$pt = Security::sanitize($_POST);
		$id = (int)($pt['id_siswa'] ?? 0);
		$nom = (double)str_replace(['.', ','], '', $pt['nominal'] ?? 0);
		$ket = !empty($pt['keterangan']) ? trim($pt['keterangan']) : 'Isi Saldo Dompet Digital';
		$id_user = (int)($_SESSION['id'] ?? 1);

		if ($nom <= 0) {
			echo json_encode(['stat' => 0, 'ket' => 'Nominal pengisian harus lebih besar dari 0']);
			return;
		}

		$ketEsc = mysqli_real_escape_string($con, $ket);
		$sql = @mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa, id_tbl_status_transaksi, jumlah, keterangan, tanggal, id_user) VALUES ('$id', '1', '$nom', '$ketEsc', NOW(), '$id_user')");

		if ($sql) {
			$last_id = mysqli_insert_id($con);
			@audit_log('CREATE', 'dompet_digital', $last_id, "Top Up Dompet Digital Rp ".number_format($nom)." untuk warga ID $id");
			echo json_encode(['stat' => 1, 'ket' => 'Pengisian saldo dompet digital berhasil!']);
		} else {
			echo json_encode(['stat' => 0, 'ket' => 'Gagal mengisi saldo dompet digital']);
		}
	}
	
	function saku_tarik()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$nom = $_POST['nominal'];
		$ket = $_POST['ket'];
		$query = mysqli_query($con, "SELECT SUM(jumlah) as top FROM tbl_uangku WHERE id_tbl_status_transaksi!=2 AND id_siswa=$id");
		$top = mysqli_fetch_row($query);
		$sql = mysqli_query($con, "SELECT SUM(jumlah) as cek FROM tbl_uangku WHERE id_tbl_status_transaksi=2 AND id_siswa=$id");
		$cek = mysqli_fetch_row($sql);
		$saldo = $top[0] - $cek[0];
		$cb=[];
		$cb['stat']=0;
		if ($nom > $saldo) {
			$cb['ket']= "Saldo Tidak Cukup";
		} else {
			$ins = mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa,id_tbl_status_transaksi,jumlah,keterangan)VALUES('$id','2','$nom','$ket')");
			if ($ins) {
				$last_id = $con->insert_id;
				$this->wappin_notif_tarik_saku($last_id,$ket);
				$cb['ket']= "Berhasil diinput";
				$cb['stat']=1;
			} else {
				$cb['ket']= "Gagal diinput!";
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
		}else{
			$sql = "INSERT INTO tbl_ekantin(id_siswa,id_status_transaksi,jumlah,keterangan,tanggal)VALUES('$id','1','$nominal_saldo','Split Saldo Uang Saku Offline','$tanggal')";
			$result_sql = mysqli_query($con, $sql);
			$query = "SELECT id_ekantin FROM tbl_ekantin WHERE id_siswa = '$id' AND id_status_transaksi = 1 ORDER BY id_ekantin DESC LIMIT 1";
			$result_query = mysqli_query($con, $query);
			$ambil = 0;
			while ($cari= mysqli_fetch_array($result_query)) {
				$ambil = $cari['id_ekantin'];
			}
			$jumlah = mysqli_query($con, "INSERT INTO tbl_uangku(id_siswa, id_tbl_status_transaksi, jumlah, keterangan, tanggal, id_trans)VALUES('$id', '5', '$nominal_saldo', 'Split Saldo E-Money Offline', '$tanggal', '$ambil')"); 
			if ($jumlah) {
				echo 'Berhasil';
			}else{
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
		$data=[];
		$data['saldo_rp']=rupiah($saldo);
		$data['saldo']=$saldo;
		echo json_encode($data);
	}

	function limit_money()
	{
		global $con;
		$id = $_POST['id_siswa'];
		$query = mysqli_query($con, "SELECT `limit` FROM tbl_limit_uangku WHERE id_siswa = $id");
		$limit = mysqli_fetch_row($query);
		$data['saldo_rp']=rupiah($limit[0])." / hari";
		$data['saldo']=$limit[0];
		echo json_encode($data);
	}
	
	function saku_hapus()
	{
		global $con;
		$id_siswa =$_POST['id_siswa'];
		$id_trans = $_POST['id_trans'];
		$nom =$_POST['nominal'];
		$sta = $_POST['stat'];
		$ketket = base64_decode($_POST['ket']);
		$cb=[];
		$cb['stat']=0;
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
					$cb['ket']= "Saldo sudah digunakan,tidak dapat dihapus!";
				} else {
					$sql_po = mysqli_query($con, "DELETE FROM tabungan WHERE id_saku=$id_trans");
					$sql = mysqli_query($con, "DELETE FROM tbl_uangku WHERE id_tbl_uangku=$id_trans");
					if ($sql) {
						$cb['ket']= "Berhasil dihapus!";
						$cb['stat']=1;
					} else {
						$cb['ket']= "Gagal dihapus!";
					}
				}
			}else{
				if ($saldoakhir < 0) {
					$cb['ket']= "Saldo sudah digunakan,tidak dapat dihapus!";
				} else {
					$sql = mysqli_query($con, "DELETE FROM tbl_uangku WHERE id_tbl_uangku=$id_trans");
					if ($sql) {
						$cb['ket']= "Berhasil dihapus!";
						$cb['stat']=1;
					} else {
						$cb['ket']= "Gagal dihapus!";
					}
				}
			}
		} else {
			$cb['ket']= "Tidak boleh dihapus!";
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
			<td width="200px"align="left"><strong>Top Up Uang Saku</strong></td>
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
			$htmll = '<table border="0">
			<tr>
			<td width="10px">' . $no . '.</td>
			<td width="100px" align="left">' . $rowet['keterangan'] . '</td>
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
			$pdf->Cell(0, 0, "NO NOTA : $iduang", 0, 1);
			$pdf->write1DBarcode($iduang, 'UPCE', '', '', '', 5, 0.5, $style, 'N');
			$date = $rowet['tanggal'];
			$tgl = date("d-m-Y", strtotime($date));

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
	
	function saku_notif_url()
	{
		global $con;
		$id = $_POST['id_trans'];
		$querykls = "SELECT tbl_uangku.jumlah,data_siswa.nama_siswa,data_siswa.id_jenjang,data_jenjang.unit,data_siswa.telp_one FROM tbl_uangku 
			INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
			INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_siswa.id_jenjang
			WHERE id_tbl_uangku='$id'";
		$result = mysqli_query($con, $querykls);
		while ($rs = mysqli_fetch_array($result)) {

			$telp = $rs['telp_one'];
			if ($telp == "" || $telp == null) {
			} else {
				$ptng = substr($telp, 0, 2);
				$ptng2 = substr($telp, 1, 17);
				if ($ptng == '08') {
					$telp = '62' . $ptng2;
				}
			}
			$pesanwadua = "*" . $rs['unit'] . "*";
			$nama = "*" . $rs['nama_siswa'] . "*";
			$jumlah = "*" . number_format($rs['jumlah'], 0, "", ".") . "*";
		}
		$pesanwa = "*YAYASAN SEKOLAH MADANI*%0A%0AAssalamu`alaikum Wr.Wb %0A%0ABapak/Ibu Wali Murid atas nama $nama yg dimuliakan Alloh SWT %0ATerima kasih telah melakukan Top Up Uang Saku tunai dengan nominal :%0A*Rp.*" . $jumlah . "%0ATerima Kasih.Barokallohufiikum%0A%0AWassalamu`alaikum Wr.Wb";

		$url= 'https://api.whatsapp.com/send?phone=' . $telp . '&text=' . $pesanwadua . ' ' . $pesanwa;
		echo $url;
	}
	
	
	function wappin_notif_byr_saku($id){
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
			} 
			else {
				$ptng  = substr($telp, 0, 2);
				$ptng2 = substr($telp, 1, 17);
				if ($ptng == '08') {
					$telp = '62' . $ptng2;
				}
			}
			$pesanwadua = "*".$rs['unit']."*.";
			$nama       = "*".$rs['nama_siswa']."*";
			$jumlah     = "*".number_format($rs['jumlah'], 0, "", ".")."*";
		}

		$iklan = " Gunakan selalu SEKOLAHku-APP untuk melakukan pembayaran tagihan Sekolah dan mendapatkan informasi terbaru mengenai tagihan, riwayat pembayaran ataupun yang lainnya. Silahkan instal SEKOLAHku-APP: Android di https://bit.ly/SekolahKu-App & iOS di https://apple.co/3TubFpW";

		$pesanwa = " Terima kasih, top up uang saku tunai atas nama ".$nama." sejumlah Rp. ".$jumlah." pada tanggal *".date("d-m-Y H:i:s")."* sudah berhasil.";

		$end = $pesanwadua.$pesanwa.$iklan;

		if ($telp != "" || $telp != null) {
			$send_wappin = $this->send_message($telp,$end);
			$hasil = json_decode($send_wappin);
			$message = "Success. ";
			if (isset($hasil->status)) {
				$status = $hasil->status;
				$message = $hasil->message.". ";
			}
			//echo $message;
		}
	}
	function wappin_notif_tarik_saku($id,$ket){
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
			} 
			else {
				$ptng  = substr($telp, 0, 2);
				$ptng2 = substr($telp, 1, 17);
				if ($ptng == '08') {
					$telp = '62' . $ptng2;
				}
			}
			$pesanwadua = "*".$rs['unit']."*.";
			$nama       = "*".$rs['nama_siswa']."*";
			$jumlah     = "*".number_format($rs['jumlah'], 0, "", ".")."*";
		}

		$iklan = " Gunakan selalu SEKOLAHku-APP untuk melakukan pembayaran tagihan Sekolah dan mendapatkan informasi terbaru mengenai tagihan, riwayat pembayaran ataupun yang lainnya. Silahkan instal SEKOLAHku-APP: Android di https://bit.ly/SekolahKu-App & iOS di https://apple.co/3TubFpW";

		$pesanwa = " Terima kasih, penarikan uang saku tunai atas nama ".$nama." sejumlah Rp. ".$jumlah." pada tanggal *".date("d-m-Y H:i:s")."* untuk ".$ket." sudah berhasil.";

		$end = $pesanwadua.$pesanwa.$iklan;

		if ($telp != "" || $telp != null) {
			$send_wappin = $this->send_message($telp,$end);
			$hasil = json_decode($send_wappin);
			$message = "Success. ";
			if (isset($hasil->status)) {
				$status = $hasil->status;
				$message = $hasil->message.". ";
			}
			//echo $message;
		}
	}
	
	/* API WAPPIN 2.O  */
	function token(){
		$base_url = "https://api.chat.wappin.app/";

		$url_login  = $base_url.'v1/users/login';

		$username = '97adf842bcaa6b02febcaaf6ec6d4e7bf1549c68';
        $password = 'bccd0baacaeb752d757d1229c4e179e31291960f';

        $session = curl_init ( $url_login );
        curl_setopt ( $session, CURLOPT_URL, $url_login );
        curl_setopt ( $session, CURLOPT_POST, true );
        curl_setopt ( $session, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt ( $session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt ( $session, CURLOPT_USERPWD, "$username:$password");
        curl_setopt ( $session, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec ( $session );
        $httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

        curl_close($session);

        $token = "";
        if ($httpCode === 200) {
            $respon = json_decode($result,true);
            $token = $respon['users'][0]['token'];
            return $token;
        } else {
            echo "Error: HTTP status code: $httpCode";
        }
	}
	function send_message($number,$message){
		if (isset($number)) {
			$base_url = "https://api.chat.wappin.app/";

			$token = $this->token();
		    $tok = "Authorization: Bearer ".$token;

		    $url = $base_url."v1/messages";

		    $parameters[] = array(
				'type'	=> "text",
	        	'text'	=> $message // isi pesan
			);
			$components[] = array(
				'type'	=> "body",
				'parameters'	=> $parameters
			);

	    	$token = $this->token();
	    	$tok = "Authorization: Bearer ".$token;

	        $url = $base_url."v1/messages";

	        $request = array(
	        	'to'	=> $number,
	        	'type'	=> "template",
	        	'template'	=> array(
	        		'name'	=> "info_global_sekolahku", // nama template
	        		'namespace'	=> "8f33dd73_b08d_42f7_88a2_dd2687056515", // namespace template
	        		'language'	=> array(
	        			'policy'	=> "deterministic",
	        			'code'		=> "id"
	        		),
	        		'components'	=> $components
	        	)
	        );
	    	$request = json_encode($request);
	    	
	    	$session = curl_init ( $url );
			curl_setopt ( $session, CURLOPT_URL, $url );
			curl_setopt ( $session, CURLOPT_HTTPHEADER, array($tok,'Content-Type: application/json') );
			curl_setopt ( $session, CURLOPT_VERBOSE, true );
			curl_setopt ( $session, CURLOPT_POST, true );
			curl_setopt ( $session, CURLOPT_POSTFIELDS, $request );
			curl_setopt ( $session, CURLOPT_RETURNTRANSFER, TRUE );
			curl_setopt ( $session, CURLOPT_SSL_VERIFYPEER, false);

			$result = curl_exec ( $session );
			$httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);

			curl_close($session);

	        if ($httpCode === 200) {
	            $response = $result;
	        } 
	        else{
	        	$response = json_encode(array(
					'status'  => $httpCode,
					'message' => "Error: HTTP status code"
				));
	        }
	        return $response;
		}
	}
	
	function testing()
	{
		require('app/lib/html_table.php');
	}
	
}

?>