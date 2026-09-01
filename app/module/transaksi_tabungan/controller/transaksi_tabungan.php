<?php
class transaksi_tabungan extends Controller
{
	var $content;


	public function index($param1 = "", $param2 = "")
	{

		$data["title"] = "Laporan Pembayaran";
		$data["href"] = "";
		$this->view("main/view/header", $data);
		$this->view("transaksi_tabungan/view/v_laporan");
		$this->view("main/view/footer");
	}

	public function ex($param1, $params2)
	{
		$ex_controller = '';
		if (file_exists("app/module/laporan/controller/$param1.php")) {
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


	function cari_tab($id = null, $crc = null)
	{
		global $con;
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		if (isset($_GET['id']) == '') {
			echo '';
		} else {
			$sql = "select * from data_siswa 
			WHERE nama_siswa LIKE '%$id%'";
			$result = mysqli_query($con, $sql);
			$count = mysqli_num_rows($result);
			//echo '<div id="btncrtot">[ '.$count.'  Data ]</div><br>';
			echo '<br>';
			echo '<table id="tabs" width="1150px;">
			<tr>
			<th>NO</th>
			<th>Nama</th>
			<th>Alamat</th>
			<th>ID</th>
			</tr>';
			$no = 0;
			while ($cari = mysqli_fetch_array($result)) {
				$date = $cari['tanggal_lahir'];
				$tgl = date("d-m-Y", strtotime($date));
				if ($count > 0) {
					$no++;
					echo '<tbody>';
					echo '<tr onclick=theares();>
			<td>' . $no . '</td>
			<td style="display:none;">' . $cari['nama_siswa'] . '</td>
			<td>' . $cari['nama_siswa'] . '</td>
			<td>' . $cari['alamat'] . '</td>
			<td style="display:none;">' . $cari['tempat_lahir'] . '</td>
			<td style="display:none;">' . $tgl . '</td>
			<td style="display:none;">' . $cari['nama_ayah'] . '</td>
			<td style="display:none;">' . $cari['nama_ibu'] . '</td>
			<td>' . $cari['id_siswa'] . '</td>
			</tr>';
				}
			}
			echo '<tbody>';
			echo '</table>';
		}
	}

	function texttab()
	{
		echo '<div id="lbl">TABUNGAN</div>
			<input type="text" id="tb"><br>';
		echo '<div id="lbl">KETERANGAN</div>
			<input type="text" id="keterangan_tab"><br>';
		echo '<button class="btn btn-primary btn-xs" style="margin-left:10px;margin-top:5px;" onclick="proses_bung();">Simpan</button>';
	}

	function insert_tab($id = null, $nominal_tab_dt = null, $nominal_amb = null, $th = null, $hasil = null, $keterangan_tab = null)
	{
		global $con;
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$nominal_tab_dt = isset($_GET['nominal_tab_dt']) ? $_GET['nominal_tab_dt'] : "";
		$nominal_amb = "0";
		$hasil = isset($_GET['hasil']) ? $_GET['hasil'] : "";
		$keterangan_tab = isset($_GET['keterangan_tab']) ? $_GET['keterangan_tab'] : "";
		$sesi = $_SESSION['id'];
		echo $hasil;
		$send = mysqli_query($con, "INSERT INTO tabungan(nominal_tab,nominal_amb,nis_from_idsiswa,saldo,st,id_user,ket) 
			VALUES (
			'$nominal_tab_dt',
			'$nominal_amb',
			'$id',
			'$hasil',
			'F',
			'$sesi',
			'$keterangan_tab')");
		echo ("INSERT INTO tabungan(nominal_tab,nominal_amb,nis_from_idsiswa,saldo,st,id_user,ket) 
			VALUES (
			'$nominal_tab_dt',
			'$nominal_amb',
			'$id',
			'$hasil',
			'F',
			'$sesi',
			'$keterangan_tab')");
	}
	function tot_sal($id = null)
	{
		global $con;
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$sql = "SELECT *,SUM(nominal_tab) as tot_tab, SUM(nominal_amb) as tot_amb FROM tabungan WHERE nis_from_idsiswa='$id'";
		$result = mysqli_query($con, $sql);
		while ($cari = mysqli_fetch_array($result)) {
			echo '<input type="hidden" id="tot_tb" value="' . ($cari['tot_tab'] + 0) . '"><p>
			<input type="hidden" id="tot_pn" value="' . ($cari['tot_amb'] + 0) . '"><p>
			<input type="hidden" id="tot_do" value="' . ($cari['tot_tab'] - $cari['tot_amb']) . '"><p>';
		}
	}



	function insert_tarik($id = null, $nominal_tab_dt = null, $nominal_amb_dt = null, $th = null, $hasil = null)
	{
		global $con;
		$tgl = date("Y-m-d");
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$nominal_tab_dt = isset($_GET['nominal_tab_dt']) ? $_GET['nominal_tab_dt'] : "";
		$nominal_amb_dt = isset($_GET['nominal_amb']) ? $_GET['nominal_amb'] : "";
		echo $nominal_amb_dt;
		$hasil = isset($_GET['hasil']) ? $_GET['hasil'] : "";
		echo $hasil;
		$cek = "INSERT INTO tabungan(nominal_tab,nominal_amb,nis_from_idsiswa,saldo,st,tgl) 
			VALUES (
			'$nominal_tab_dt',
			'$nominal_amb_dt',
			'$id',
			
			'$hasil',
			'F',
			'$tgl')";
		echo $cek;
		$send = mysqli_query($con, $cek);
	}
	function history_tab($id = null)
	{
		global $con;
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$sql = "select * from tabungan 
			inner join data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa
			WHERE nis_from_idsiswa ='$id'";
		$result = mysqli_query($con, $sql);
		echo '<div id="ar_left" style="height:350px;width:520px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">';
		echo '<table id="tabsskls" width="500px;">
			<tr>
			<th width="15px;">No</th>
			<th width="15px;">Tanggal</th>
			<th width="300px;">Nama</th>
			<th width="50px;">Tabungan</th>
			<th width="50px;">Penarikan</th>
			<th width="50px;">Saldo</th>
			</tr>';
		$no = 0;
		while ($rskls = mysqli_fetch_array($result)) {
			$no++;
			echo '<tr>
			<td>' . $no . '</td>
			<td>' . $rskls['tgl'] . '</td>
			<td>' . $rskls['nama_siswa'] . '</td>
			<td>' . number_format($rskls['nominal_tab']) . '</td>
			<td>' . number_format($rskls['nominal_amb']) . '</td>
			<td>' . number_format($rskls['saldo']) . '</td>
			<td style="display:none;">' . $rskls['id_tab'] . '</td>
			<td width="10px"><input type="submit" value="Del" id="ffr" onclick="dle();"></td>
			<td width="10px"><input type="submit" value="Cetak" id="ffr" onclick="cet_trans();"></td>
			</tr>';
		}
		echo '</table>';
		echo '</div>';
		echo '<div style="clear:both;"></div>';
	}

	function cekt($id = null)
	{
		global $con;
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$querykls = "SELECT *,SUM((nominal_tab)-(nominal_amb)) as sal FROM tabungan WHERE nis_from_idsiswa='$id'";
		$result = mysqli_query($con, $querykls);
		$queryklss = "SELECT * FROM tabungan WHERE nis_from_idsiswa='$id' ORDER BY id_tab DESC LIMIT 0,1";
		$results = mysqli_query($con, $queryklss);
		while ($dt = mysqli_fetch_array($result)) {
			while ($dtt = mysqli_fetch_array($results)) {
				if ($dtt['saldo'] == $dt['sal']) {
					echo '<input type="text" style="background-color:white;margin-left:10px;font-family:tahoma;font-size:12px;width:250px;" value="' . $dt['sal'] . '"><span style="margin-left:5px;font-family:tahoma;font-size:12px;">Perhitungan Benar</span>';
				} else {
					echo '<input type="text" style="background-color:red;font-family:tahoma;font-size:12px;width:250px;" value="' . $dt['sal'] . '"><span style="margin-left:5px;font-family:tahoma;font-size:12px;">SALAH, Cek transaksi anda</span>';
				}
			}
		}
	}



	function textpen()
	{
		echo '<div id="lbl">PENARIKAN</div>
			<input type="text" id="pn"><br>';
		echo '<div id="lbl">KETERANGAN</div>
			<input type="text" id="keterangan"><br>';
		echo '<button class="btn btn-primary btn-xs" style="margin-left:10px;margin-top:5px;" onclick="proses_pena();">Simpan</button>';
	}

	function update_cetak_ilang($nis = null)
	{
		global $con;
		$nis = isset($_GET['nis']) ? $_GET['nis'] : "";
		$send = mysqli_query($con, "UPDATE tabungan SET st='T' WHERE nis_from_idsiswa=$nis");
	}

	function cetak_ilang($nis = null)
	{
		require('app/lib/html_table.php');
		session_start();
		global $con;
		$nis = isset($_GET['nis']) ? $_GET['nis'] : "";
		//$sd = mysql_query("UPDATE transaksi_siswa SET print='T' WHERE id_where=$id");
		$nis = isset($_GET['nis']) ? $_GET['nis'] : "";
		$sql = "SELECT tabungan.id_tab,tabungan.tgl,tabungan.nominal_tab,tabungan.nominal_amb,tabungan.saldo,tabungan.id_user,users_log.nama_us FROM tabungan 
				LEFT JOIN users_log ON tabungan.id_user=users_log.id
				WHERE nis_from_idsiswa='$nis' ORDER BY tabungan.id_tab ASC ";
		$result = mysqli_query($con, $sql);

		$pdf = new PDF_HTML_Table();
		$pdf = new FPDF('P', 'mm', array(140, 185));
		$pdf->AddPage();
		$pdf->SetMargins(16, 0);
		$pdf->ln(6);
		$no = 1;
		while ($rowe = mysqli_fetch_array($result)) {
			if ($no == 25) {
				$pdf->AddPage();
				$pdf->SetMargins(16, 0);
				$pdf->ln(18);
			}
			$date = $rowe['tgl'];
			$tgl = date("d-m-Y", strtotime($date));
			$pdf->SetFont('Arial', '', 10);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Cell(25, 4, $tgl, '', 0, 'L');
			$pdf->Cell(20, 4, number_format($rowe['nominal_amb']), '', 0, 'R');
			$pdf->Cell(27, 4, number_format($rowe['nominal_tab']), '', 0, 'R');
			$pdf->Cell(29, 4, number_format($rowe['saldo']), '', 0, 'R');
			$pdf->Cell(12, 4, substr($rowe['nama_us'], 0, 3), '', 0, 'L');
			$pdf->ln(6);
			$no++;
		}


		$pdf->Output();
	}
	function pildul()
	{
		echo '';
	}
	function cetak($nis = null)
	{
		require('app/lib/html_table.php');
		session_start();
		global $con;
		$nis = isset($_GET['nis']) ? $_GET['nis'] : "";
		//$sd = mysql_query("UPDATE transaksi_siswa SET print='T' WHERE id_where=$id");
		$nis = isset($_GET['nis']) ? $_GET['nis'] : "";
		$sql = "SELECT tabungan.id_tab,tabungan.tgl,tabungan.nominal_tab,tabungan.nominal_amb,tabungan.saldo,tabungan.id_user,users_log.nama_us,tabungan.st FROM tabungan 
				INNER JOIN users_log ON tabungan.id_user=users_log.id
				WHERE nis_from_idsiswa='$nis'";
		$result = mysqli_query($con, $sql);

		$pdf = new PDF_HTML_Table();
		$pdf = new FPDF('P', 'mm', array(140, 185));
		$pdf->AddPage();
		$pdf->SetMargins(16, 0);
		$pdf->ln(6);
		$no = 1;
		while ($rowe = mysqli_fetch_array($result)) {
			if ($no % 24 == 0) {
				$pdf->AddPage();
				$pdf->SetMargins(16, 0);
				$pdf->ln(18);
			}
			$date = $rowe['tgl'];
			$tgl = date("d-m-Y", strtotime($date));
			if ($rowe['st'] == "T") {
				$pdf->SetFont('Arial', '', 10);
				$pdf->SetTextColor(255, 255, 255);
				$pdf->Cell(25, 4, $tgl, '', 0, 'L');
				$pdf->Cell(20, 4, number_format($rowe['nominal_amb']), '', 0, 'R');
				$pdf->Cell(27, 4, number_format($rowe['nominal_tab']), '', 0, 'R');
				$pdf->Cell(29, 4, number_format($rowe['saldo']), '', 0, 'R');
				$pdf->Cell(12, 4, substr($rowe['nama_us'], 0, 3), '', 0, 'L');
				$pdf->ln(6);
				$no++;
			} else if ($rowe['st'] == "F") {
				$pdf->SetFont('Arial', '', 10);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->Cell(25, 4, $tgl, '', 0, 'L');
				$pdf->Cell(20, 4, number_format($rowe['nominal_amb']), '', 0, 'R');
				$pdf->Cell(27, 4, number_format($rowe['nominal_tab']), '', 0, 'R');
				$pdf->Cell(29, 4, number_format($rowe['saldo']), '', 0, 'R');
				$pdf->Cell(12, 4, substr($rowe['nama_us'], 0, 3), '', 0, 'L');
				$pdf->ln(6);
				$no++;
			}
		}


		$pdf->Output();
	}

	function cetakbuk_tab($nis = null, $th = null)
	{
		require('app/lib/html_table.php');
		global $con;
		$id = isset($_GET['nis']) ? $_GET['nis'] : "";
		$sql = "select * from data_siswa WHERE id_siswa=$id";
		$result = mysqli_query($con, $sql);
		$pdf = new PDF_HTML_Table();
		$pdf = new FPDF('P', 'mm', array(140, 185));
		$pdf->AddPage();
		$pdf->SetMargins(42, 0);
		while ($rowe = mysqli_fetch_array($result)) {
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->ln(43);
			$pdf->Cell(100, 4, $rowe['nama_siswa'], 0, 'L');
			$pdf->ln(4);
			$pdf->Cell(100, 4, $rowe['alamat'], '', 0, 'L');
			$pdf->ln(4);
		}
		$pdf->Output();
	}

	function load_list_siswa_cari()
	{
		$pt = $_POST;
		$sql = "SELECT
			data_siswa.id_siswa,
			data_siswa.nama_siswa,
			data_siswa.gender,
			data_siswa.nis_siswa AS nis,
			data_siswa.no_kartu,
			nama_kelas 
		FROM
			data_siswa
			LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
			LEFT JOIN data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas 
		WHERE
			data_kelas.id_sesi = '53'
		ORDER BY
			nama_kelas,
			nama_siswa";
		// echo $sql;

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
						<th>Nama Siswa</th>
						<th>L/P</th>
						<th>NIS</th>
						<th>Kelas</th>
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

	function cetak_ra($id = null)
	{

		global $con;
		require_once('app/lib/tcpdf/tcpdf.php');
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$sql = "SELECT * FROM tabungan
			inner join data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa 
			inner join penempatan_siswa ON penempatan_siswa.nis_from_idsiswa=tabungan.nis_from_idsiswa
			WHERE id_tab='$id'";
		$result = mysqli_query($con, $sql);

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
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);
		$pdf->SetFont('times', '', 10);
		$pdf->AddPage();
		//$pdf->Image('asli.jpg',8,10,20,20);
		$no = 1;
		while ($rowe = mysqli_fetch_array($result)) {
			$date = $rowe['tgl'];
			$tgl = date("d-M-Y", strtotime($date));
			$pdf->SetXY(10, 10);
			$pdf->Image('kausar.jpeg', '', '', 20, 20, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
			$pdf->SetFont('times', 'B', 10);
			$pdf->SetXY(30, 11);
			$htmlsx = '<table border="0">
			<tr>
			<td width="160px" align="left">BUKTI TRANSAKSI</td>
			</tr>
			<tr>
			<td width="120px" align="left">MUHAMMADIYAH AL-KAUTSAR</td>
			</tr>
			<tr>
			<td width="160px" align="left" style="font-size:10px;">Alamat : Jl. Cendana II RT 2 RW III </td>
			</tr>
			<tr>
			<td width="160px" align="center" style="font-size:8px;">Gumpang Kartasura Telp. (0271) 765 2814</td>
			</tr>
			</table>';
			$pdf->writeHTML($htmlsx, true, false, true, false, '');
			$pdf->SetXY(10, 33);
			$htmls = '<table border="0">
			<tr>
			<td width="100px" align="left">Nomor Transaksi</td>
			<td width="135px" align="left">: ' . $rowe['id_tab'] . '</td>
			</tr>
			</table>';
			$pdf->writeHTML($htmls, true, false, true, false, '');
			$pdf->SetXY(10, 40);
			if ($rowe['nominal_amb'] == 0) {
				$html = '<table border="0">
			<tr>
			<td width="80px"align="left">Tanggal</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">' . $tgl . '</td>
			</tr>
			<tr>
			<td width="80px"align="left">NIS</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">' . $rowe['nis'] . '</td>
			</tr>
			<tr>
			<td width="80px"align="left">Nama Siswa</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">' . $rowe['nama_siswa'] . '</td>
			</tr>
			<tr>
			<td width="80px"align="left">Status</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">Tabungan</td>
			</tr>
			<tr>
			<td width="80px"align="left">Nominal</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">Rp ' . number_format($rowe['nominal_tab']) . '</td>
			</tr>
			</table>';
				// output the HTML content



				$style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
				$stylet = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));

				$pdf->Line(10, 32, 75, 32, $style);
				$pdf->Line(10, 33, 75, 33, $stylet);
				$pdf->writeHTML($html, true, false, true, false, '');
			} else {
				$htmlb = '<table border="0">
			<tr>
			<td width="80px"align="left">Tanggal</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">' . $tgl . '</td>
			</tr>
			<tr>
			<td width="80px"align="left">NIS</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">' . $rowe['nis'] . '</td>
			</tr>
			<tr>
			<td width="80px"align="left">Nama Siswa</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">' . $rowe['nama_siswa'] . '</td>
			</tr>
			<tr>
			<td width="80px"align="left">Status</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">Penarikan</td>
			</tr>
			<tr>
			<td width="80px"align="left">Nominal</td>
			<td width="5px"align="left">:</td>
			<td width="145px" align="left">Rp ' . number_format($rowe['nominal_amb']) . '</td>
			</tr>
			</table>';
				// output the HTML content



				$stylew = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
				$styletw = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));

				$pdf->Line(10, 32, 75, 32, $stylew);
				$pdf->Line(10, 33, 75, 33, $styletw);
				$pdf->writeHTML($htmlb, true, false, true, false, '');
			}
			$pdf->SetLineWidth(0);
			$pdf->line(10, 38, 75, 38);
			$pdf->line(10, 68, 75, 68);
			// output the HTML content

			/*$pdf->ln(21);
			$pdf->Cell(70, 4,'Nomor transaksi     : '.$rowe['id_tab'].'', '', 0, 'L');
			$pdf->ln(4);
			$pdf->ln(2);
			$pdf->Cell(70, 4,'Tanggal : '.$rowe['tgl'].'', '', 0, 'L');
			$pdf->ln(4);
			$pdf->Cell(70, 4,'NIS     : 1234', '', 0, 'L');
			$pdf->ln(4);
			$pdf->Cell(70, 4,'Nama : '.$rowe['nama_siswa'].'', '', 0, 'L');
			if($rowe['nominal_amb']==0){
			$pdf->ln(5);
			$pdf->Cell(70, 4,'Status  : Tabungan', '', 0, 'L');
			$pdf->ln(4);
			$pdf->Cell(70, 4,'Nominal  : Rp. '.number_format($rowe['nominal_tab']).'', '', 0, 'L');
			}else{
			$pdf->ln(5);
			$pdf->Cell(70, 4,'Status   : Penarikan', '', 0, 'L');	
			$pdf->ln(4);
			$pdf->Cell(70, 4,'Nominal  : Rp. '.number_format($rowe['nominal_amb']).'', '', 0, 'L');
			}
			$pdf->ln(4);
			$pdf->Cell(70, 4,'Saldo : Rp. '.number_format($rowe['saldo']).'', '', 0, 'L');
			*/
			$pdf->SetXY(10, 70);
			$htmlbc = '<table border="0">
			<tr>
			<td width="230px"align="center">Admin</td>
			</tr>
			<tr>
			<td width="230px"align="center"></td>
			</tr>
			<tr>
			<td width="230px"align="center"></td>
			</tr>
			<tr>
			<td width="230px"align="center">' . $_SESSION['username'] . '</td>
			</tr>
			<tr>
			<td width="230px"align="center">Perhatian : Struk ini sebagai bukti transaksi yang sah, mohon disimpan dengan baik.</td>
			</tr>
			</table>';
			$pdf->writeHTML($htmlbc, true, false, true, false, '');
			$stylewe = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
			$styletwe = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));

			$pdf->Line(10, 103, 75, 103, $stylewe);
			$pdf->Line(10, 104, 75, 104, $styletwe);
		}


		$pdf->Output();
	}

	function tampil_siswa($id_siswa = null)
	{
		global $con;
		$id_siswa = isset($_POST['id_siswa']) ? $_POST['id_siswa'] : "";
		// echo $_POST['id_siswa'];
		$sql = "select * from tabungan 
				inner join data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa
				WHERE nis_from_idsiswa ='$id_siswa'";
		// echo $sql;
		$result = mysqli_query($con, $sql);

		echo '<table id="tabsskls" class="table table-mm table-bordered">
				<thead>
				<tr>
				<th>No</th>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Tabungan</th>
				<th>Penarikan</th>
				<th>Saldo</th>
				<th>Ket</th>
				</tr>
				</thead>
				<tbody>';
		$no = 0;
		$saldo = 0;
		while ($rskls = mysqli_fetch_array($result)) {
			$no++;
			$hitung = $rskls['nominal_tab'] - $rskls['nominal_amb'];
			$saldo += $hitung;
			echo '<tr>
				<td>' . $no . '</td>
				<td>' . $rskls['tgl'] . '</td>
				<td>' . $rskls['nama_siswa'] . '</td>
				<td>' . number_format($rskls['nominal_tab']) . '</td>
				<td>' . number_format($rskls['nominal_amb']) . '</td>
				<td>' . number_format($saldo) . '</td>
				<td style="display:none;">' . $rskls['id_tab'] . '</td>
				<td>' . $rskls['ket'] . '</td>
				<td width="10px"><input type="submit" value="Del" id="ffr" onclick="dle();"></td>
				<td width="10px"><input type="submit" value="Cetak" id="ffr" onclick="cet_trans();"></td>
				</tr>';
		}
		echo '</tbody></table>';
		echo '</div>';
		echo '<div style="clear:both;"></div>';
	}

	function dle_tabu()
	{
		global $con;
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$send = mysqli_query($con, "DELETE FROM tabungan WHERE id_tab=$id");
	}

	public function detail_siswa()
	{
		global $con;
		$id_siswa = isset($_POST['id_siswa']) ? $_POST['id_siswa'] : "";
		// echo $_POST['id_siswa'];
		$sql = "select * from data_siswa WHERE id_siswa ='$id_siswa'";
		// echo $sql;
		$result = mysqli_query($con, $sql);
		while ($rskls = mysqli_fetch_array($result)) {
			$nama = $rskls['nama_siswa'];
		}
		echo $nama;
	}



	public function js($param1)
	{
		$this->get_js("transaksi_tabungan/js/" . $param1);
	}
}
