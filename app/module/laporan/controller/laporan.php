<?php
class laporan extends Controller
{
	var $content;


	public function index($param1 = "", $param2 = "")
	{

		$data["title"] = "Laporan Pembayaran";
		$data["href"] = "";
		$this->view("main/view/header", $data);
		$this->view("laporan/view/v_laporan");
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


	function send_tahun()
	{
		global $con;
		$querykls = "SELECT * FROM data_tahunajar ORDER BY urutan ASC";
		$result = mysqli_query($con, $querykls);
		// echo "<select id='myselects_th' class= 'btn btn-light btn-lg' style='font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;' onChange='myNewFunctionth(this);'>";

		$query_tahunajar = "SELECT
				id_tahunajar 
			FROM
				data_tahunajar 
			WHERE
				aktif = '1'
		";
		$exeQueryTahunAjar = mysqli_query($con, $query_tahunajar);
		$idTahunAjar = "";
		while ($dt = mysqli_fetch_array($exeQueryTahunAjar)) {
			$idTahunAjar = $dt['id_tahunajar'];
		}

		echo "<select class='form-select'; id='myselects_th' class='btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:14px;width:180px;height:50px;margin-left:8px;' onChange='myNewFunctionth(this);'>";
		echo '<option value="0">Semua tahun ajaran</option>';
		while ($dt = mysqli_fetch_array($result)) {
			if ($dt['id_tahunajar'] == $idTahunAjar) {
				echo '<option value="' . $dt['id_tahunajar'] . '" selected>' . $dt['nama_tahunajar'] . '</option>';
			} else {
				echo '<option value="' . $dt['id_tahunajar'] . '">' . $dt['nama_tahunajar'] . '</option>';
			}
		}
		echo '</select>';
	}



	function jenjang_null()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$query1 = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
				INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'";

		$resultjang = mysqli_query($con, $query1);

		$query_jenjang = "SELECT
		id_jenjang 
			FROM
		data_jenjang
		WHERE
		beranda = '1'
		";

		$exeQueryjenjang = mysqli_query($con, $query_jenjang);
		$id_jenjang = "";
		while ($dt = mysqli_fetch_array($exeQueryjenjang)) {
			$id_jenjang = $dt['id_jenjang'];
		}


		echo "<select class='form-select'; id='myselects_thj' class='btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:14px;width:180px;height:50px;margin-left:10px;' onChange='myNewFunctionjang(this);'>";
		echo '<option value="0">Pilih wilayah</option>';

		while ($dt = mysqli_fetch_array($resultjang)) {
			if ($dt['id_jenjang'] == $id_jenjang) {
				echo '<option value="' . $dt['id_jenjang'] . '" selected>' . $dt['nama_jenjang'] . '</option>';
			} else {
				echo '<option value="' . $dt['id_jenjang'] . '">' . $dt['nama_jenjang'] . '</option>';
			}
		}
		echo '</select>';
	}


	function send_kelas_null()
	{
		global $con;
		//$sesi = $_SESSION['id'];

		$query_tahunajar = "SELECT
				id_tahunajar 
			FROM
				data_tahunajar 
			WHERE
				aktif = '1'
		";
		$exeQueryTahunAjar = mysqli_query($con, $query_tahunajar);
		$idTahunAjar = "";
		while ($dt = mysqli_fetch_array($exeQueryTahunAjar)) {
			$idTahunAjar = $dt['id_tahunajar'];
		}

		$query_jenjang = "
			SELECT
				id_jenjang 
			FROM
				data_jenjang
			WHERE
				beranda = '1'
		";

		$exeQueryjenjang = mysqli_query($con, $query_jenjang);
		$id_jenjang = "";
		while ($dt = mysqli_fetch_array($exeQueryjenjang)) {
			$id_jenjang = $dt['id_jenjang'];
		}

		$query = "
			SELECT * FROM data_kelas WHERE id_sesi = $idTahunAjar AND id_jenjang = $id_jenjang
		";
		$res = mysqli_query($con, $query);

		echo "<select id='myselects_thdd' multiple class='dropdown-toggle' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:30px;margin-left:10px;' placeholder='tampilkan semua wilyah';>";
		echo '<option value="">Tampil semua wilayah</option>';
		while ($dt = mysqli_fetch_array($res)) {
			echo '<option value="' . $dt['id_kelas'] . '">' . $dt['nama_kelas'] . '</option>';
		}
		echo '</select>';
	}


	function send_user_null()
	{


		echo "<select id='myselects_thc' multiple class='btn btn-secondary dropdown-toggle' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:30px;margin-left:10px;'>";
		echo '<option value="">Tampil semua petugas</option>';
		echo '</select>';
	}


	function cetaksurat($th = null, $id = null, $id_bulan = null, $id_jenis_transaksi = null)
	{
		global $con;
		require_once('app/lib/tcpdf/tcpdf.php');

		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id_bulan = isset($_GET['id_bulan']) ? $_GET['id_bulan'] : "";
		$id_jenis_transaksi = isset($_GET['id_jenis_transaksi']) ? $_GET['id_jenis_transaksi'] : "";

		if ($id_bulan != 0) {
			$idBulan = "AND MONTH(master_alokasi_transaksi_all.tgl_jth) IN ($id_bulan)";
		} else {
			$idBulan = "";
		}


		if ($id_jenis_transaksi != 0) {
			$idJenisTransaksi = "AND master_alokasi_transaksi_all.id_jenis_transaksi NOT IN ($id_jenis_transaksi)";
		} else {
			$idJenisTransaksi = "";
		}

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
		$pdf->Image('logo.png', '', '', 35, 35, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$pdf->SetXY(10, 10);
		$sql = "SELECT
				* 
			FROM
				master_alokasi_transaksi_all
				INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
				INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
				INNER JOIN penempatan_siswa ON master_alokasi_transaksi_all.nis_from_idsiswa = penempatan_siswa.nis_from_idsiswa
				INNER JOIN data_kelas ON data_kelas.id_kelas = master_alokasi_transaksi_all.id_kelas 
			WHERE
				master_alokasi_transaksi_all.id_sesi = '$th' 
				AND master_alokasi_transaksi_all.nis_from_idsiswa = '$id' 
			GROUP BY
				master_alokasi_transaksi_all.nis_from_idsiswa";
		$result = mysqli_query($con, $sql);
		$surat = "SELECT * FROM surat WHERE id='1'";
		$result_surat = mysqli_query($con, $surat);
		$pdf->SetFont('times', 'B', 12);
		$pdf->setCellPaddings(30, 0, 0, 0);
		$pdf->Cell(0, 0, '', 0, 0, 'L', 0, '', 3);
		$pdf->Ln(1);
		$pdf->SetFont('times', 'B', 12);
		$pdf->setCellPaddings(41, 0, 0, 0);
		$pdf->Cell(0, 0, 'YAYASAN NURUL HIKMAH', 0, 0, 'C', 0, '', 3);
		$pdf->Ln(6);
		$pdf->SetFont('times', 'B', 20);
		$pdf->setCellPaddings(41, 0, 0, 0);
		$pdf->Cell(0, 0, 'SMK PELITA YNH', 0, 0, 'C', 0, '', 3);
		$pdf->Ln(10);
		$pdf->SetFont('times', 'B', 10);
		$pdf->setCellPaddings(41, 0, 0, 0);
		$pdf->Cell(0, 0, 'Animasi - Tata Busana - Pemasaran - Tata Boga - Seni Musik - Otomotif', 0, 0, 'C', 0, '', 3);
		$pdf->Ln(4);
		$pdf->SetFont('times', 'I', 9);
		$pdf->setCellPaddings(38, 0, 0, 0);
		$pdf->Cell(0, 0, 'Jl.Otista Gg. Karya Bhakti, Nanggeleng Kec. Citamiang Kota Sukabumi', 0, 0, 'C', 0, '', 3);
		$pdf->Ln(4);
		$pdf->SetFont('times', 'I', 9);
		$pdf->setCellPaddings(38, 0, 0, 0);
		$text = "Tlp. (0266) 221 223 Email : ";
		//$pdf->SetTextColor(0, 63, 127);
		$text .= "pelitasmi87@yahoo.com ";
		$text .= "Website : ";
		$text .= "www.nurulhikmahfoundation.org";
		$pdf->Cell(0, 0, $text, 0, 0, 'C', 0, '', 3);
		$pdf->Ln(16);

		$style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$stylet = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$pdf->Line(10, 48, 202, 48, $style);
		$pdf->Line(10, 49, 202, 49, $stylet);
		// $pdf->Ln(41);
		while ($rowe = mysqli_fetch_array($result)) {

			while ($rowet = mysqli_fetch_array($result_surat)) {
				$sqlt = "SELECT
						*,
						SUM( master_alokasi_transaksi_all.nominal - IF ( transaksi_siswa.kekurangan IS NULL, 0, transaksi_siswa.nominal_dibayarkan ) ) AS totnomi 
					FROM
						master_alokasi_transaksi_all
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
						INNER JOIN kategori_trans
						INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar = master_alokasi_transaksi_all.id_sesi
						LEFT JOIN transaksi_siswa ON transaksi_siswa.id_master_trans_sis = master_alokasi_transaksi_all.id_master_trans_sis 
					WHERE
						master_alokasi_transaksi_all.nis_from_idsiswa = '$id' 
						AND master_alokasi_transaksi_all.sta IS NULL 
						$idBulan
						$idJenisTransaksi
						AND master_alokasi_transaksi_all.id_sesi = '$th' 
					GROUP BY
						MONTH(master_alokasi_transaksi_all.tgl_jth) 
					ORDER BY
						master_jenis_transaksi.id_jenis_transaksi";
				$resultt = mysqli_query($con, $sqlt);

				$tot = "SELECT
						*,
						SUM( nominal ) AS totnomitot 
					FROM
						master_alokasi_transaksi_all
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi 
					WHERE
						master_alokasi_transaksi_all.nis_from_idsiswa = '$id'
						$idBulan
						$idJenisTransaksi
						AND master_alokasi_transaksi_all.id_sesi = '$th' 
					GROUP BY
						master_alokasi_transaksi_all.nis_from_idsiswa";
				$result_tot = mysqli_query($con, $tot);

				$sudo = "SELECT
						*,
						SUM( transaksi_siswa.nominal_dibayarkan ) AS a 
					FROM
						master_alokasi_transaksi_all
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
						INNER JOIN transaksi_siswa ON transaksi_siswa.id_master_trans_sis = master_alokasi_transaksi_all.id_master_trans_sis 
					WHERE
						master_alokasi_transaksi_all.nis_from_idsiswa = '$id' 
						$idBulan
						$idJenisTransaksi
						AND master_alokasi_transaksi_all.id_sesi = '$th'";
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
<td width="400px" align="left"><strong>Bpk./Ibu Wali Murid Ananda ' . $rowe['nama_siswa'] . '</strong></td>
</tr>
<tr>
<td width="400px" align="left">' . $rowe['nama_kelas'] . '</td>
</tr>
</table>';
				// output the HTML content
				$pdf->writeHTML($htmld, true, false, true, false, '');
				$htmlt = '<table border="0">
<tr>
<td width="400px" align="left" >Assalamualaikum warahmatullahi wabarakatuh</td>
</tr>
</table>';
				// output the HTML content
				$pdf->writeHTML($htmlt, true, false, true, false, '');
				$htmle = '<table border="0">
<tr>
<td width="540px" align="justify">Segala puji bagi Allah swt. yang selalu mencurahkan rahmat dan nikmat-Nya kepada kita.
Shalawat dan salam semoga dilimpahkan kepada Nabi Muhammad saw.
</td>
</tr>
</table>';
				// output the HTML content
				$pdf->writeHTML($htmle, true, false, true, false, '');
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
				$pdf->SetFont('Times', 'B', 11);
				//$pdf->SetMargins(0, 20, 10, true);
				$pdf->setCellPaddings(5, 0, 0, 0);
				$pdf->Cell(130, 4, 'Jenis Pembayaran', 1, 0, 'C', 0);
				$pdf->setCellPaddings(5, 0, 5, 0);
				$pdf->Cell(43, 4, 'Jumlah Tagihan', 1, 0, 'C', 0);
				$pdf->SetFont('Times', '', 11);
				$no = 1;
				$nama = '';
				$cek = '';
				$total_anak = 0;
				$co = 0;
				while ($ro = mysqli_fetch_array($resultt)) {

					$sqldib = "SELECT
							*,
							SUM( nominal_dibayarkan ) nomdib 
						FROM
							transaksi_siswa
							INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi 
						WHERE
							nis_from_idsiswa = '" . $ro[' nis_from_idsiswa '] . "' 
							AND master_jenis_transaksi.id_jenis_transaksi = '" . $ro[' id_jenis_transaksi '] . "' 
						ORDER BY
							transaksi_siswa.id_jenis_transaksi ASC";
					$resul_dib = mysqli_query($con, $sqldib);

					if ($resul_dib) {
						while ($dib = mysqli_fetch_array($resul_dib)) {
							if (($ro['totnomi'] - $dib['nomdib']) == 0) {
								if ($nama != $ro['id_sesi'] && $total_anak > 0) {
									$htb = '<tr>
					<td style="text-align:right;background-color:yellow;" colspan="5">Total</td>
					<td style="background-color:yellow;">' . number_format($total_anak) . '</td>
					</tr>';
									//$pdf->writeHTML($htb, true, false, true, false, '');
									// output the HTML content
									$pdf->ln(5);
									$pdf->SetFont('Times', 'B', 11);
									//$pdf->setCellPaddings(5,0,0,0);
									//$pdf->Cell(14, 4, '', 0, 0, 'L',0); 
									$pdf->Cell(90, 4, 'TOTAL ', 1, 0, 'L', 0);
									//$pdf->setCellPaddings(0,0,5,0);
									$pdf->Cell(40, 4, 'Rp. ' . number_format(($total_anak), 0, "", "."), 1, 0, 'R', 0);
									//$pdf->Cell(43, 4,'' , 1, 0, 'R',0);
									$total_anak = 0;

									$pdf->ln(5);
									$pdf->SetFont('Times', 'B', 11);
									//$pdf->setCellPaddings(5,0,0,0);
									//$pdf->Cell(14, 4, '', 0, 0, 'L',0); 
									$pdf->Cell(173, 4, 'TAHUN AJARAN ' . $ro['nama_tahunajar'], 1, 0, 'L', 0);
								}
								$cek = $ro['id_sesi'];
							} else {
								$co++;
								if ($co == 1) {
									$pdf->ln(5);
									$pdf->SetFont('Times', 'B', 11);
									//$pdf->setCellPaddings(5,0,0,0);
									//$pdf->Cell(14, 4, '', 0, 0, 'L',0); 
									$pdf->Cell(173, 4, 'TAHUN AJARAN ' . $ro['nama_tahunajar'], 1, 0, 'L', 0);
								}

								if ($nama != $ro['id_sesi'] && $total_anak > 0) {
									$htb = '<tr>
					<td style="text-align:right;background-color:yellow;" colspan="5">Total</td>
					<td style="background-color:yellow;">' . number_format($total_anak) . '</td>
					</tr>';
									//$pdf->writeHTML($htb, true, false, true, false, '');
									// output the HTML content
									$pdf->ln(5);
									$pdf->SetFont('Times', 'B', 11);
									//$pdf->setCellPaddings(5,0,0,0);
									//$pdf->Cell(14, 4, '', 0, 0, 'L',0); 
									$pdf->Cell(90, 4, 'TOTAL ', 1, 0, 'L', 0);
									//$pdf->setCellPaddings(0,0,5,0);
									$pdf->Cell(40, 4, 'Rp. ' . number_format(($total_anak), 0, "", "."), 1, 0, 'R', 0);
									//$pdf->Cell(43, 4,'' , 1, 0, 'R',0);
									$total_anak = 0;

									$pdf->ln(5);
									$pdf->SetFont('Times', 'B', 11);
									//$pdf->setCellPaddings(5,0,0,0);
									//$pdf->Cell(14, 4, '', 0, 0, 'L',0); 
									$pdf->Cell(173, 4, 'TAHUN AJARAN ' . $ro['nama_tahunajar'], 1, 0, 'L', 0);
								}

								$cek = $ro['id_sesi'];
								$pdf->ln(5);
								$pdf->SetFont('Times', '', 11);
								// output the HTML content
								//$pdf->setCellPaddings(5,0,0,0);
								//$pdf->Cell(14, 4, '', 0, 0, 'L',0); 
								$pdf->Cell(130, 4, $ro['nama_transaksi'], 1, 0, 'L', 0);
								//$pdf->setCellPaddings(0,0,5,0);
								$pdf->Cell(43, 4, 'Rp. ' . number_format(($ro['totnomi'] - $dib['nomdib']), 0, "", "."), 1, 0, 'R', 0);
								//$pdf->Cell(130, 4, $ro['id_sesi'], 1, 0, 'L',0); 


							}
							$tag = $ro['totnomi'] - $dib['nomdib'];
							$total_anak = $total_anak + $tag;

							if ($nama == $ro['id_sesi']) {
							} else {
								$no++;
							}
							$nama = $ro['id_sesi'];
						}
					}
				}

				$pdf->SetFont('Times', 'B', 11);
				$pdf->ln(5);
				//$pdf->setCellPaddings(5,0,0,0);
				//$pdf->Cell(14, 4, '', 0, 0, 'L',0); 
				$pdf->Cell(90, 4, 'TOTAL ', 1, 0, 'L', 0);
				//$pdf->setCellPaddings(0,0,5,0);
				$pdf->Cell(40, 4, 'Rp. ' . number_format(($total_anak), 0, "", "."), 1, 0, 'R', 0);
				//$pdf->Cell(43, 4, ' ', 1, 0, 'L',0); 
				if ($result_tot) {
					while ($tot = mysqli_fetch_array($result_tot)) {
						if ($result_sudo) {
							while ($tot_sudo = mysqli_fetch_array($result_sudo)) {
								//echo '<td width="500px" style="background-color:yellow;">Total '.number_format(($tot['totnomitot']-$tot_sudo['a'])).'</td>';
								$pdf->SetFont('Times', '', 11);
								$pdf->ln(5);
								//$pdf->Cell(14, 4, '', 0, 0, 'L',0); 
								$pdf->SetFont('Times', 'B', 11);
								$pdf->Cell(130, 4, 'Total Tagihan', 1, 0, 'C', 0);
								$pdf->Cell(43, 4, 'Rp. ' . number_format(($tot['totnomitot'] - $tot_sudo['a']), 0, "", "."), 1, 0, 'R', 0);
								$pdf->SetFont('Times', '', 11);
							}
						}
					}
				}

				$pdf->Ln(5);

				// output the HTML content
				$pdf->writeHTML($htmlla, true, false, true, false, '');

				$htmllz = '<table border="0">
<tr>
<td width="540px" align="left">' . $rowet['isi_a'] . '</td>
</tr>
</table>';
				// output the HTML content
				$pdf->writeHTML($htmllz, true, false, true, false, '');
			}
		}


		$htmllb = '<table border="0">
<tr>
<td width="540px" align="left">Demikian surat ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu, kami mengucapkan terima kasih.</td>
</tr>
</table>';
		// output the HTML content
		$pdf->writeHTML($htmllb, true, false, true, false, '');

		$htmlvvv = '<table border="0">
<tr>
<td width="540px" align="left" >Wassalamualaikum warahmatullahi wabarakatuh</td>
</tr>
</table>';
		// output the HTML content
		$pdf->writeHTML($htmlvvv, true, false, true, false, '');
		$pdf->Ln(4);
		$htmlas = '<table border="0">
<tr>
<td width="40px" align="left"></td>
<td width="370px" align="left"></td>
<td width="230px" align="left"></td>
</tr>
<tr>
<td width="40px" align="left"></td>
<td width="350px" align="left"></td>
<td width="230px" align="left"><img src="ttd.png" width="150px" height="100px"></td>
</tr>
<tr>
<td width="40px" align="left"></td>
<td width="370px" align="left"></td>
<td width="230px" align="left"><strong></td>
</tr>
<tr>
<td width="40px" align="left"></td>
<td width="370px" align="left"></td>
<td width="230px" align="left"></td>
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
<td width="100%" align="left">1. Jika ada perbedaan catatan pembayaran mohon berkenan menghubungi bagian keuangan Sekolah.</td>
</tr>
<tr>
<td align="left">2. Pembayaran bisa dilakukan online transfer menggunakan Virtual Account ananda.</td>
</tr>
</table>';
		// output the HTML content
		$pdf->writeHTML($nb, true, false, true, false, '');
		// output the HTML content

		$pdf->Output('Surat Tagihan.pdf', 'I');
	}

	function send_user()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		if ($id_jenjang == '') {
			$qry = mysqli_query($con, "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
				INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'");
			$id_jenjang = array();
			while ($cs = mysqli_fetch_array($qry)) {
				array_push($id_jenjang, $cs['id_jenjang']);
			}
			$id_jenjang = implode(',', $id_jenjang);
		}
		$querykls = "SELECT data_user_jenjang.id_user,users_log.nama_us,data_user_jenjang.id_jenjang FROM data_user_jenjang
				INNER JOIN users_log ON users_log.id=data_user_jenjang.id_user
				WHERE data_user_jenjang.id_jenjang IN ($id_jenjang)";
		$result = mysqli_query($con, $querykls);
		/* echo '<option value=0>Semua user</option>';
				echo '<option value="bank">Bank</option>'; */

		echo "<select id='myselects_thc' multiple class='btn btn-secondary dropdown-toggle' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:30px;margin-left:10px;'>";

		while ($dt = mysqli_fetch_array($result)) {
			echo ' <option value="">Tampil semua user</option>';
			echo '<option value="' . $dt['id_user'] . '">' . $dt['nama_us'] . '</option>';
		}
		echo '</select>';
	}

	function send_trans_null()
	{
		global $con;
		$query_tahunajar = "SELECT
				id_tahunajar 
			FROM
				data_tahunajar 
			WHERE
				aktif = '1'
		";
		$exeQueryTahunAjar = mysqli_query($con, $query_tahunajar);
		$idTahunAjar = "";
		while ($dt = mysqli_fetch_array($exeQueryTahunAjar)) {
			$idTahunAjar = $dt['id_tahunajar'];
		}

		$query_jenjang = "
			SELECT
				id_jenjang 
			FROM
				data_jenjang
			WHERE
				beranda = '1'
		";

		$exeQueryjenjang = mysqli_query($con, $query_jenjang);
		$id_jenjang = "";
		while ($dt = mysqli_fetch_array($exeQueryjenjang)) {
			$id_jenjang = $dt['id_jenjang'];
		}

		$query = "
			SELECT * FROM master_jenis_transaksi WHERE id_sesi = $idTahunAjar AND id_jenjang = $id_jenjang
		";
		$res = mysqli_query($con, $query);

		echo "<select id='myselects_thd' multiple class='dropdown-toggle' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:30px;margin-left:10px;' placeholder='tampilkan semua wilayah';>";
		echo '<option value="">Tampil semua jenis iuran</option>';
		while ($dt = mysqli_fetch_array($res)) {
			echo '<option value="' . $dt['id_jenis_transaksi'] . '">' . $dt['nama_transaksi'] . '</option>';
		}
		echo '</select>';
	}

	function send_metode_null()
	{

		// echo "<input class='form-control' id='myselects_thm' type='text' placeholder='tampilkan semua metode' id='example-text-input' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:12px;width:170px;height:27px;margin-left:10px;'>";

		echo "<select id='myselects_thm' multiple class='btn btn-secondary dropdown-toggle' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:30px;margin-left:10px;'>";
		echo '<option value="">Tampil semua metode</option>';
		echo '</select>';
	}



	function send_metode()
	{
		echo "<select id='myselects_thm' multiple class='btn btn-secondary dropdown-toggle' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;'>";
		echo '<option value="">Tampil semua metode</option>';
		echo '<option value="teller">Tunai</option>';
		//echo '<option value="tabungan">Klaim Tabungan</option>';
		echo '<option value="bank">Virtual Account</option>';
		/*echo '<option value="Bank BRI">Bank BRI</option>';
		echo '<option value="Bank BNI">Bank BNI</option>';
		echo '<option value="Bank Mandiri">Bank Mandiri</option>';
		echo '<option value="Bank BCA">Bank BCA</option>';*/
		echo '<option value="Uang Saku">Klaim Dompet Digital</option>';
		echo '</select>';
	}


	function send_jenjang()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$query = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
				INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'";

		$resultjen = mysqli_query($con, $query);
		// echo "<select id='myselects_thj' class= 'btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;' onChange='myNewFunctionjang(this);'>";

		// echo "<select class= 'btn btn-light btn-lg'; id='myselects_thj' class='btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;' onChange='myNewFunctionjang(this);'>";


		echo "<select class='form-select'; id='myselects_thj' class='btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:180px;height:50px;margin-left:8px;' onChange='myNewFunctionjang(this);'>";
		echo '<option value="0">Pilih jenjang</option>';
		while ($dt = mysqli_fetch_array($resultjen)) {
			echo '<option value="' . $dt['id_jenjang'] . '">' . $dt['nama_jenjang'] . '</option>';
		}
		echo '</select>';
	}
	function send_trans()
	{
		global $con;
		$sesi = isset($_GET['id']) ? $_GET['id'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$querykls = "SELECT * FROM master_jenis_transaksi 
					WHERE id_sesi='$sesi' AND id_jenjang='$id_jenjang'";
		$result = mysqli_query($con, $querykls);
		echo "<select id='myselects_thd' multiple class='btn btn-secondary dropdown-toggle' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:30px;margin-left:10px;'placeholder='tampil semua jenis iuran';>";
		while ($dt = mysqli_fetch_array($result)) {
			echo ' <option value="">Tampil  semua jenis iuran</option>';
			echo '<option value="' . $dt['id_jenis_transaksi'] . '">' . $dt['nama_transaksi'] . '</option>';
		}
		echo '</select>';
	}


	function send_kelas()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		if ($id_jenjang == '') {
			echo $qer = "SELECT nama_kelas FROM data_kelas WHERE id_jenjang=$id_jenjang AND id_sesi=$th";
			$qry = mysqli_query($con, $qer);
			$id_jenjang = array();
			while ($cs = mysqli_fetch_array($qry)) {
				array_push($id_jenjang, $cs['id_jenjang']);
			}
			$id_jenjang = implode(',', $id_jenjang);
		}
		$querykls = "SELECT * FROM data_kelas 
					WHERE id_jenjang IN ($id_jenjang) AND id_sesi=$th";
		$result = mysqli_query($con, $querykls);

		echo "<select id='myselects_thdd' multiple class='btn btn-secondary dropdown-toggle' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:30px;margin-left:10px;' placeholder='tampil semuakelas';>";
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="">tampil semua wilayah</option>';
			echo '<option value="' . $dt['id_kelas'] . '">' . $dt['nama_kelas'] . '</option>';
		}
		echo "</select>";
	}

	function laporan_harian()
	{

		global $con;
		$sesi = $_SESSION['id'];
		$awal = isset($_GET['a']) ? $_GET['a'] : "";
		$akhir = isset($_GET['b']) ? $_GET['b'] : "";
		$c = isset($_GET['c']) ? $_GET['c'] : "";
		$d = isset($_GET['d']) ? $_GET['d'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : "";
		$metode = isset($_GET['metode']) ? $_GET['metode'] : "";
		$a = $awal;
		$b = $akhir;

		if ($c != '') {
			$arr = explode(",", $c);
			$arr = implode("','", $arr);
			$arr = "'" . $arr . "'";
			$user = " AND (transaksi_siswa.office NOT IN ('teller','Uang Saku') OR transaksi_siswa.id_user IN($arr))";
		} else {
			$user = "";
		}
		if ($id_kelas != 0) {
			$kls = " AND transaksi_siswa.id_kelas IN($id_kelas)";
		} else {
			$kls = "";
		}

		if ($d != 0) {
			$trans = " AND transaksi_siswa.id_jenis_transaksi IN($d)";
		} else {
			$trans = "";
		}

		if ($metode != '') {
			$arrs = explode(",", $metode);
			$arrs = implode("','", $arrs);
			$arrs = "'" . $arrs . "'";

			if ($arrs == "'Bank BRI'") {
				$arrs = "'teller'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket = 'Bank BRI'";
			} else if ($arrs == "'Bank BNI'") {
				$arrs = "'teller'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket = 'Bank BNI'";
			} else if ($arrs == "'Bank Mandiri'") {
				$arrs = "'teller'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket = 'Bank Mandiri'";
			} else if ($arrs == "'Bank BCA'") {
				$arrs = "'teller'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket = 'Bank BCA'";
			} else if ($arrs == "'teller'") {
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket IS NULL";
			} else if ($arrs == "'tabungan'") {
				$arrs = "'tabungan'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket IS NULL";
			} else {
				$meto = " AND transaksi_siswa.office IN($arrs)";
			}
		} else {
			$meto = "";
		}

		if ($th != 0) {
			$tahun = "AND transaksi_siswa.thn_aj = '$th'";
			$jenjang = "AND data_siswa.id_jenjang = '$id_jenjang'";
		} else {
			$tahun = "";
			$jenjang = "";
		}




		$sqls = "SELECT
						data_tahunajar.nama_tahunajar,
						transaksi_siswa.office,
						transaksi_siswa.infaq,
						users_log.nama_us,
						users_log.id,
						penempatan_siswa.nis,
						transaksi_siswa.tgl,
						master_jenis_transaksi.nama_transaksi,
						data_siswa.nama_siswa,
						data_siswa.id_jenjang,
						data_kelas.id_kelas,
						data_kelas.nama_kelas,
						transaksi_siswa.nominal_dibayarkan,
						transaksi_siswa.ket,
						master_alokasi_transaksi_all.nom_share 
					FROM
						transaksi_siswa
						LEFT JOIN users_log ON users_log.id = transaksi_siswa.id_user
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
						INNER JOIN data_kelas ON data_kelas.id_kelas = transaksi_siswa.id_kelas
						INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
						INNER JOIN penempatan_siswa ON penempatan_siswa.id_kelas = transaksi_siswa.id_kelas
						INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar = transaksi_siswa.thn_aj
						INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
					WHERE
						DATE( transaksi_siswa.tgl ) BETWEEN '$a' 
						AND '$b' 
						$tahun $kls $user $trans $meto $jenjang 
					GROUP BY
						transaksi_siswa.id_transaksi";


		$result = mysqli_query($con, $sqls);
		echo "<button onclick='cethar();' style='border-radius:3px;border:0px;padding:5px;margin-top:20px;margin-bottom:10px;'><img src='images/logo/excel.png' style='width:15px;height:15px;'> <span style='font-size:11px;'>Download excel</span></button>";
		echo "<button onclick='laporan_harian_list();' style='border-radius:3px;border:0px;padding:5px;margin-left:10px;margin-top:20px;margin-bottom:10px;'> <span style='font-size:11px;'>Tampilkan list</span></button>";
		echo "<button onclick='laporan_harian_table();' style='border-radius:3px;border:0px;padding:5px;margin-left:10px;margin-top:20px;margin-bottom:10px;'> <span style='font-size:11px;'>Tampilkan tabel</span></button><br>";

		echo '	

							<table class="table table-bordered table-md" width="100%" style="font-size:14px;">
							<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
								
								

					<thead style="background:#F8FAFC;">

					<tr>
						<th class="fw-semibold text-dark border-0">#</th>
						<th class="fw-semibold text-dark border-0">Periode</th>
						<th class="fw-semibold text-dark border-0">Tanggal</th>
						<th class="fw-semibold text-dark border-0">Nama Warga</th>
						<th class="fw-semibold text-dark border-0">Wilayah</th>
						<th class="fw-semibold text-dark border-0">Petugas</th>
						<th class="fw-semibold text-dark border-0">Nama Iuran</th>
						<th class="fw-semibold text-dark border-0 text-end">Nominal</th>
					</tr>

				</thead>';

		$no = 0;
		$total = 0;
		$totalshare = 0;
		while ($cari = mysqli_fetch_array($result)) {
			$no++;
			$date = $cari['tgl'];
			$tgl = date("d-m-Y", strtotime($date));

			echo '	<tbody>';
			echo '<tr>
														<td>' . $no . '</td>
														<td>' . $cari['nama_tahunajar'] . '</td>
														<td>' . $tgl . '</td>
														<td><div class="fw-semibold">' . $cari['nama_siswa'] . '</div></td>
														<td>' . $cari['nama_kelas'] . '</td>';

			if ($cari['ket'] == 'Bank BRI') {
				echo '<td>' . $cari['nama_us'] . ' (' . $cari['office'] . ' | Bank BRI)</td>';
			} else if ($cari['ket'] == 'Bank BNI') {
				echo '<td>' . $cari['nama_us'] . ' (' . $cari['office'] . ' | Bank BNI)</td>';
			} else if ($cari['ket'] == 'Bank Mandiri') {
				echo '<td>' . $cari['nama_us'] . ' (' . $cari['office'] . ' | Bank Mandiri)</td>';
			} else if ($cari['ket'] == 'Bank BCA') {
				echo '<td>' . $cari['nama_us'] . ' (' . $cari['office'] . ' | Bank BCA)</td>';
			} else {
				echo '<td><span class="badge bg-light text-dark border">' . $cari['nama_us'] . '</span></td>';
			}

			echo ' <td>' . $cari['nama_transaksi'] . '</td>';
			if ($cari['infaq'] == 0) {
				echo '<td style="text-align:right;" class="text-end fw-semibold">' . number_format($cari['nominal_dibayarkan'], 0, ".", ".") . '</td>';
				$jml = $cari['nominal_dibayarkan'];
			} else {
				echo '<td style="text-align:right;">' . number_format($cari['nominal_dibayarkan'], 0, ".", ".") . ' dan Donasi ' . number_format($cari['infaq'], 0, ".", ".") . '</td>';
				$jml = $cari['nominal_dibayarkan'] + $cari['infaq'];
			}
			echo '</tr>';
			$total = $total + $jml;


		}
		echo '
		<tr style="background:#F8FAFC;">

						<td colspan="7" class="text-end fw-bold fs-6">
							Total Pembayaran
						</td>

						<td class="text-end fw-bold text-primary fs-5">
							Rp ' . number_format($total, 0, ".", ".") . '
						</td>

					</tr>';

		echo '</tbody>';

		echo '
								</div>
							<table>
							';
	}





	function total_detil($a = null, $b = null, $th = null, $id_jenjang = null)
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$a = isset($_GET['a']) ? $_GET['a'] : date('Y-m-d') . " 00:00:00";
		$b = isset($_GET['b']) ? $_GET['b'] : date('Y-m-d') . " 23:23:59";
		$b = isset($_GET['b']) ? $_GET['b'] : "";
		$sqlinf = "SELECT SUM(infaq) as infa FROM transaksi_siswa 
							INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
							WHERE master_jenis_transaksi.id_sesi='$th' AND DATE(transaksi_siswa.tgl) BETWEEN '$a' AND '$b'";
		$rows = mysqli_query($con, $sqlinf);
		$row = mysqli_fetch_array($rows);
		if ($row['infa'] == null) {
			$sum = 0;
		} else {
			$sum = $row['infa'];
		}


		$sql = "SELECT
								kategori_laporan.nama_kategori_laporan,
								SUM( nominal_dibayarkan ) AS jum 
							FROM
								transaksi_siswa
								INNER JOIN data_kelas ON data_kelas.id_kelas = transaksi_siswa.id_kelas
								INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
								INNER JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = transaksi_siswa.nis_from_idsiswa
								INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi 
								INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan=master_jenis_transaksi.id_kategori_laporan
							WHERE
								master_jenis_transaksi.id_sesi = '$th' 
								AND penempatan_siswa.thn_ajar = '$th' 
								AND DATE( transaksi_siswa.tgl ) BETWEEN '$a' 
								AND '$b' AND data_siswa.id_jenjang = '$id_jenjang'
							GROUP BY
								master_jenis_transaksi.id_kategori_laporan";
		$sqlt = "SELECT SUM(nominal_dibayarkan)+" . $sum . " as jum FROM transaksi_siswa 
							INNER JOIN data_kelas ON data_kelas.id_kelas=transaksi_siswa.id_kelas
							INNER JOIN data_siswa ON data_siswa.id_siswa=transaksi_siswa.nis_from_idsiswa
							INNER JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa=transaksi_siswa.nis_from_idsiswa
							INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
							WHERE master_jenis_transaksi.id_sesi='$th' AND penempatan_siswa.thn_ajar='$th' AND data_siswa.id_jenjang='$id_jenjang' AND DATE(transaksi_siswa.tgl) BETWEEN '$a' AND '$b'";
		$result = mysqli_query($con, $sql);
		$resultt = mysqli_query($con, $sqlt);
		$resultinf = mysqli_query($con, $sqlinf);

		echo '<button type="button" style="margin-top:13px;margin-bottom:5px;font-size:12px;" class="btn btn-info waves-effect waves-light w-sm"onclick="konpig();">
                            + Pengaturan Kategori
                            </button>';

		echo '
							<table class="table table-bordered table-md" width="100%" style="font-size:14px;">
							<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
							<thead style="background:#F8FAFC;">
									<tr>
									<th style="width: 10%;">No</th>
									<th style="width: 25%;">Alokasi Dana</th>
									<th style="width: 50%;">Nominal</th>
									
									</tr>
							</thead>';
		$no = 0;
		$total = 0;
		while ($cari = mysqli_fetch_array($result)) {

			$no++;
			echo '<tbody>';
			echo '<tr>
							<td>' . $no . '</td>
							<td>' . $cari['nama_kategori_laporan'] . '</td>
							<td style="text-align:left;" class="text-end fw-semibold"><strong>Rp.' . number_format($cari['jum']) . '</strong></td>
							</tr>';
			$jml = $cari['jum'];
			$total = $total + $jml;
		}
		$no++;
		echo '<tr>
							<td>' . $no . '</td>';
		while ($cariinf = mysqli_fetch_array($resultinf)) {
			echo '<td>Donasi</td>
							<td style="text-align:left;" class="text-end fw-semibold"><strong>Rp.' . number_format($cariinf['infa']) . '</strong></td>
							</tr>';
		}
		while ($carit = mysqli_fetch_array($resultt)) {
			echo '<td colspan="2" class="text-end fw-bold fs-6">Total Pembayaran</td>';
			echo '<td style="text-align:right;" class="text-end fw-bold text-primary fs-5"><strong>Rp.' . number_format($total) . '</strong></td>';
		}
		echo '<tbody>';
		echo '</div>';
		echo '</table>';
	}

	function cetak_laporan_harian()
	{
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=laporan_harian.xls");
		global $con;
		$sesi = $_SESSION['id'];
		$awal = isset($_GET['a']) ? $_GET['a'] : "";
		$akhir = isset($_GET['b']) ? $_GET['b'] : "";
		$c = isset($_GET['c']) ? $_GET['c'] : "";
		$d = isset($_GET['d']) ? $_GET['d'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : "";
		$a = $awal;
		$b = $akhir;

		if ($c != 'null') {
			$arr = explode(",", $c);
			$arr = implode("','", $arr);
			$arr = "'" . $arr . "'";
			$user = " AND (transaksi_siswa.office NOT IN ('teller','Uang Saku') OR transaksi_siswa.id_user IN($arr))";
		} else {
			$user = "";
		}
		if ($id_kelas != 0) {
			$kls = " AND transaksi_siswa.id_kelas IN($id_kelas)";
		} else {
			$kls = "";
		}

		if ($d != 0) {
			$trans = " AND transaksi_siswa.id_jenis_transaksi IN($d)";
		} else {
			$trans = "";
		}

		$sqls = "SELECT
								data_tahunajar.nama_tahunajar,
								transaksi_siswa.office,
								transaksi_siswa.infaq,
								users_log.nama_us,
								users_log.id,
								penempatan_siswa.nis,
								transaksi_siswa.tgl,
								master_jenis_transaksi.nama_transaksi,
								data_siswa.nama_siswa,
								data_siswa.id_jenjang,
								data_kelas.id_kelas,
								data_kelas.nama_kelas,
								transaksi_siswa.nominal_dibayarkan,
								transaksi_siswa.ket,
								master_alokasi_transaksi_all.nom_share 
							FROM
								transaksi_siswa
								LEFT JOIN users_log ON users_log.id = transaksi_siswa.id_user
								INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
								INNER JOIN data_kelas ON data_kelas.id_kelas = transaksi_siswa.id_kelas
								INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
								INNER JOIN penempatan_siswa ON penempatan_siswa.id_kelas = transaksi_siswa.id_kelas
								INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar = transaksi_siswa.thn_aj
								INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
							WHERE
								DATE( transaksi_siswa.tgl ) BETWEEN '$a' 
								AND '$b' 
								AND transaksi_siswa.thn_aj = '$th' 
								AND data_siswa.id_jenjang = '$id_jenjang' 
							GROUP BY
								transaksi_siswa.id_transaksi";
		$result = mysqli_query($con, $sqls);
		echo '<table id="tabse" width="100%;" border="1" style="margin-top:10px;">
							<tr>
							<th width="50px">No</th>
							<th width="100px">Periode</th>
							<th width="100px">Tanggal</th>
							<th width="300px">Nama Warga</th>
							<th width="80px">Wilayah</th>
							<th width="300px">Petugas</th>
							<th width="200px">Nama Iuran</th>
							<th width="200px">Nominal</th>
							</tr>';
		$no = 0;
		$total = 0;
		$totalshare = 0;
		while ($cari = mysqli_fetch_array($result)) {
			$no++;
			$date = $cari['tgl'];
			$tgl = date("d-m-Y", strtotime($date));
			echo '<tr>
							<td>' . $no . '</td>
							<td>' . $cari['nama_tahunajar'] . '</td>
							<td>' . $tgl . '</td>
							<td>' . $cari['nama_siswa'] . '</td>
							<td>' . $cari['nama_kelas'] . '</td>
							<td>' . $cari['nama_us'] . '</td>
							<td>' . $cari['nama_transaksi'] . '</td>';
			if ($cari['infaq'] == 0) {
				echo '<td style="text-align:right;">' . $cari['nominal_dibayarkan'] . '</td>';
				$jml = $cari['nominal_dibayarkan'];
			} else {
				echo '<td style="text-align:right;">' . $cari['nominal_dibayarkan'] . ' dan donasi ' . $cari['infaq'] . '</td>';
				$jml = $cari['nominal_dibayarkan'] + $cari['infaq'];
			}
			echo '</tr>';
			$total = $total + $jml;
		}
		echo '<tr>
							<td colspan="7" width="10px" style="text-align:right;">Total Pembayaran &nbsp;&nbsp;&nbsp;&nbsp;</td>';
		echo '<td width="200px" style="font-weight:bold;text-align:right;">Rp ' . $total . '</td>
							</tr>
							</table>';
	}


	function laporan_harian_table()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$awal = isset($_GET['a']) ? $_GET['a'] : "";
		$akhir = isset($_GET['b']) ? $_GET['b'] : "";
		$c = isset($_GET['c']) ? $_GET['c'] : "";
		$d = isset($_GET['d']) ? $_GET['d'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : "";
		$a = $awal;
		$b = $akhir;

		if ($c != 'null') {
			$arr = explode(",", $c);
			$arr = implode("','", $arr);
			$arr = "'" . $arr . "'";
			$user = " AND (transaksi_siswa.office NOT IN ('teller','Uang Saku') OR transaksi_siswa.id_user IN($arr))";
		} else {
			$user = "";
		}
		if ($id_kelas != 0) {
			$kls = " AND penempatan_siswa.id_kelas IN($id_kelas)";
		} else {
			$kls = "";
		}

		if ($d != 0) {
			$trans = " AND transaksi_siswa.id_jenis_transaksi IN($d)";
		} else {
			$trans = "";
		}
		$kolom_kelas = 0;
		$arr_kelas = explode(',', $id_kelas);
		if (count($arr_kelas) > 1) {
			$kolom_kelas = 1;
		}

		$sqls = "SELECT
								data_siswa.id_siswa,
								master_alokasi_transaksi_all.id_jenis_transaksi,
								data_tahunajar.nama_tahunajar,
								transaksi_siswa.office,
								transaksi_siswa.infaq,
								users_log.nama_us,
								users_log.id,
								transaksi_siswa.tgl,
								master_jenis_transaksi.nama_transaksi,
								data_siswa.nama_siswa,
								data_siswa.id_jenjang,
								data_kelas.id_kelas,
								data_kelas.nama_kelas,
								SUM( transaksi_siswa.nominal_dibayarkan ) AS nominal_dibayarkan 
							FROM
							master_alokasi_transaksi_all
								INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa AND 
									master_alokasi_transaksi_all.id_sesi IS NOT NULL
								INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
								INNER JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
								INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas
								INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar = penempatan_siswa.thn_ajar
								LEFT JOIN transaksi_siswa ON transaksi_siswa.id_master_trans_sis=master_alokasi_transaksi_all.id_master_trans_sis
								LEFT JOIN users_log ON users_log.id = transaksi_siswa.id_user 
							WHERE
								((DATE( transaksi_siswa.tgl ) BETWEEN '$a' 
								AND '$b') OR transaksi_siswa.tgl IS NULL) 
								AND master_alokasi_transaksi_all.id_sesi = '$th' $kls $trans 
								AND data_siswa.id_jenjang = '$id_jenjang' 
								AND data_tahunajar.id_tahunajar='$th'
							GROUP BY
								data_siswa.id_siswa,
								id_jenis_transaksi
							ORDER BY master_alokasi_transaksi_all.id_jenis_transaksi ASC";
		// echo $sqls;

		$result = mysqli_query($con, $sqls);
		$siswa = [];
		$arr_transaksi = [];
		$arr_nmtransaksi = [];
		$total = [];
		$grandtotal = 0;
		$arr_kelas = [];
		$arr_nmkelas = [];
		while ($dt = mysqli_fetch_array($result)) {
			$transaksi = [];
			if (!isset($siswa[$dt['id_siswa']])) {
				$siswa[$dt['id_siswa']]['id_siswa'] = $dt['id_siswa'];
				$siswa[$dt['id_siswa']]['nama_siswa'] = $dt['nama_siswa'];
				$siswa[$dt['id_siswa']]['nama_tahunajar'] = $dt['nama_tahunajar'];
				$siswa[$dt['id_siswa']]['id_kelas'] = $dt['id_kelas'];
				$siswa[$dt['id_siswa']]['nama_kelas'] = $dt['nama_kelas'];
				$siswa[$dt['id_siswa']]['nama_us'] = $dt['nama_us'];
			}
			if (!is_null($dt['id_jenis_transaksi'])) {
				$transaksi['nama_transaksi'] = $dt['nama_transaksi'];
				$transaksi['nominal_dibayarkan'] = $dt['nominal_dibayarkan'];
				$transaksi['tgl'] = $dt['tgl'];
				$siswa[$dt['id_siswa']]['transaksi'][$dt['id_jenis_transaksi']] = $transaksi;
			}

			if (!isset($arr_nmtransaksi[$dt['id_jenis_transaksi']]) && !is_null($dt['id_jenis_transaksi'])) {
				array_push($arr_transaksi, $dt['id_jenis_transaksi']);
				$arr_nmtransaksi[$dt['id_jenis_transaksi']] = $dt['nama_transaksi'];
			}
			if (!isset($arr_nmkelas[$dt['id_kelas']]) && !is_null($dt['id_kelas'])) {
				array_push($arr_kelas, $dt['id_kelas']);
				$arr_nmkelas[$dt['id_kelas']] = $dt['nama_kelas'];
			}
		}
		foreach ($arr_kelas as $id_kel) {
			foreach ($arr_transaksi as $id_trans) {
				$total[$id_kel][$id_trans] = 0;
			}
		}
		/* echo json_encode($total);
							die; */
		echo '<div style="overflow-x:auto;">';
		echo "<button onclick='cethar();' style='border-radius:3px;border:0px;padding:5px;margin:5px;'><img src='images/logo/excel.png' style='width:15px;height:15px;'> <span style='font-size:11px;'>Download excel</span></button>";
		echo "<button onclick='laporan_harian_list();' style='border-radius:3px;border:0px;padding:5px;margin:5px;'> <span style='font-size:11px;'>Tampilkan list</span></button>";
		echo "<button onclick='laporan_harian_table();' style='border-radius:3px;border:0px;padding:5px;margin:5px;'> <span style='font-size:11px;'>Tampilkan tabel</span></button><br>";

		foreach ($arr_kelas as $id_kel) {

			// echo "<span style='font-size:11px;font-weight:bold;'>$arr_nmkelas[$id_kel]</span><br>";

			$no = 1;
			$jml_siswa = 0;
			$alltotal = 0;
			$tbody = '';
			foreach ($siswa as $dtsiswa) {
				if ($dtsiswa['id_kelas'] != $id_kel) {
					continue;
				} else {
					$jml_siswa += 1;
				}
				$tbody .= "<tr>
											<td>" . $no++ . "</td>
											<td>$dtsiswa[nama_tahunajar]</td>
											<td>$dtsiswa[nama_siswa]</td>
											<td>$dtsiswa[nama_kelas]</td>
											<td>$dtsiswa[nama_us]</td>";
				for ($i = 0; $i < count($arr_transaksi); $i++) {
					if (isset($dtsiswa['transaksi'][$arr_transaksi[$i]])) {
						$tbody .= "<td>" . $dtsiswa['transaksi'][$arr_transaksi[$i]]['nominal_dibayarkan'] . "</td>";
						$total[$id_kel][$arr_transaksi[$i]] += $dtsiswa['transaksi'][$arr_transaksi[$i]]['nominal_dibayarkan'];
						$grandtotal += $dtsiswa['transaksi'][$arr_transaksi[$i]]['nominal_dibayarkan'];
						$alltotal += $dtsiswa['transaksi'][$arr_transaksi[$i]]['nominal_dibayarkan'];
					} else {
						$tbody .= "<td></td>";
					}
				}
				$tbody .= "</tr>";
			}
			$th_trans = '';
			$th_total = '';
			for ($i = 0; $i < count($arr_transaksi); $i++) {
				$th_trans .= "<th>" . $arr_nmtransaksi[$arr_transaksi[$i]] . "</th>";
				$th_total .= "<th>" . $total[$id_kel][$arr_transaksi[$i]] . "</th>";
			}

			$table = "

				<div class='card border-0 shadow-sm rounded-4 mb-4'>

					<div class='card-header bg-white border-0 py-3'>

						<div class='d-flex justify-content-between align-items-center flex-wrap gap-2'>

							<div>
								<h5 class='fw-bold text-dark mb-1'>
									Rekap Pembayaran Iuran Warga
								</h5>

								<span class='text-muted'>
									Wilayah $arr_nmkelas[$id_kel]
								</span>
							</div>

							<button onclick='download_excel_perkelas(" . $id_kel . ");'
								class='btn btn-success rounded-3 shadow-sm'>
								<i class=\"bx bxs-file-export\"></i>
								Download Excel
							</button>

						</div>

					</div>

					<div class='card-body p-0'>

						<div class='table-responsive'>

							<table class='table table-bordered align-middle mb-0' width='100%' style='font-size:14px;'>

								<thead style='background:#F8FAFC;'>

									<tr>

										<th class='fw-semibold text-center'>No</th>
										<th class='fw-semibold'>Periode</th>
										<th class='fw-semibold'>Nama Warga</th>
										<th class='fw-semibold'>Wilayah</th>
										<th class='fw-semibold'>Petugas</th>

										$th_trans

									</tr>

								</thead>

								<tbody>

									$tbody

									<tr style='background:#FAFAFA;'>

										<td colspan='5'
											class='fw-bold text-end'>
											Subtotal
										</td>

										$th_total

									</tr>

									<tr style='background:#F8FAFC;'>

										<td colspan='5'
											class='fw-bold text-end fs-6'>
											Total Pembayaran
										</td>

										<td colspan='" . count($arr_transaksi) . "'
											class='fw-bold text-primary fs-5 text-start'>
											Rp " . number_format($alltotal, 0, ".", ".") . "
										</td>

									</tr>

								</tbody>

							</table>

						</div>

					</div>

				</div>

				";

				if ($jml_siswa > 0) {
					echo $table;
				}
		}
		echo "

			<div class='card border-0 shadow-sm rounded-4 mt-4'>

				<div class='card-body d-flex justify-content-between align-items-center'>

					<div>

						<h6 class='text-muted mb-1'>
							Grand Total Pembayaran
						</h6>

						<h2 class='fw-bold text-primary mb-0'>
							Rp " . number_format($grandtotal, 0, ".", ".") . "
						</h2>

					</div>

					<div>

						<span class='badge bg-primary fs-6 px-3 py-2 rounded-pill'>
							" . count($siswa) . " Warga
						</span>

					</div>

				</div>

			</div>

			</div>

			";
	}

	function cetak_perkelas_aja()
	{
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Laporan_Penerimaan_Pembayaran.xls");
		global $con;
		$sesi = $_SESSION['id'];
		$awal = isset($_GET['a']) ? $_GET['a'] : "";
		$akhir = isset($_GET['b']) ? $_GET['b'] : "";
		$c = isset($_GET['c']) ? $_GET['c'] : "";
		$d = isset($_GET['d']) ? $_GET['d'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$id_kelas_aja = isset($_GET['id_kel']) ? $_GET['id_kel'] : "";
		$a = $awal;
		$b = $akhir;

		if ($c != 'null') {
			$arr = explode(",", $c);
			$arr = implode("','", $arr);
			$arr = "'" . $arr . "'";
			$user = " AND (transaksi_siswa.office NOT IN ('teller','Uang Saku') OR transaksi_siswa.id_user IN($arr))";
		} else {
			$user = "";
		}
		if ($id_kelas_aja != 0) {
			$kls = " AND penempatan_siswa.id_kelas IN($id_kelas_aja)";
		} else {
			$kls = "";
		}

		if ($d != 0) {
			$trans = " AND transaksi_siswa.id_jenis_transaksi IN($d)";
		} else {
			$trans = "";
		}
		$kolom_kelas = 0;
		$arr_kelas = explode(',', $id_kelas_aja);
		if (count($arr_kelas) > 1) {
			$kolom_kelas = 1;
		}

		$sqls = "SELECT
								data_siswa.id_siswa,
								master_alokasi_transaksi_all.id_jenis_transaksi,
								data_tahunajar.nama_tahunajar,
								transaksi_siswa.office,
								transaksi_siswa.infaq,
								users_log.nama_us,
								users_log.id,
								transaksi_siswa.tgl,
								master_jenis_transaksi.nama_transaksi,
								data_siswa.nama_siswa,
								data_siswa.id_jenjang,
								data_kelas.id_kelas,
								data_kelas.nama_kelas,
								SUM( transaksi_siswa.nominal_dibayarkan ) AS nominal_dibayarkan 
							FROM
							master_alokasi_transaksi_all
								INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa AND 
									master_alokasi_transaksi_all.id_sesi IS NOT NULL
								INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=master_alokasi_transaksi_all.id_jenis_transaksi
								INNER JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
								INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas
								INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar = penempatan_siswa.thn_ajar
								LEFT JOIN transaksi_siswa ON transaksi_siswa.id_master_trans_sis=master_alokasi_transaksi_all.id_master_trans_sis
								LEFT JOIN users_log ON users_log.id = transaksi_siswa.id_user 
							WHERE
								((DATE( transaksi_siswa.tgl ) BETWEEN '$a' 
								AND '$b') OR transaksi_siswa.tgl IS NULL) 
								AND master_alokasi_transaksi_all.id_sesi = '$th' $kls $trans 
								AND data_siswa.id_jenjang = '$id_jenjang' 
								AND penempatan_siswa.id_kelas = '$id_kelas_aja'
								AND data_tahunajar.id_tahunajar='$th'
							GROUP BY
								data_siswa.id_siswa,
								id_jenis_transaksi
							ORDER BY master_alokasi_transaksi_all.id_jenis_transaksi ASC";
		// echo $sqls;

		$result = mysqli_query($con, $sqls);
		$siswa = [];
		$arr_transaksi = [];
		$arr_nmtransaksi = [];
		$total = [];
		$grandtotal = 0;
		$arr_kelas = [];
		$arr_nmkelas = [];
		while ($dt = mysqli_fetch_array($result)) {
			$transaksi = [];
			if (!isset($siswa[$dt['id_siswa']])) {
				$siswa[$dt['id_siswa']]['id_siswa'] = $dt['id_siswa'];
				$siswa[$dt['id_siswa']]['nama_siswa'] = $dt['nama_siswa'];
				$siswa[$dt['id_siswa']]['nama_tahunajar'] = $dt['nama_tahunajar'];
				$siswa[$dt['id_siswa']]['id_kelas'] = $dt['id_kelas'];
				$siswa[$dt['id_siswa']]['nama_kelas'] = $dt['nama_kelas'];
				$siswa[$dt['id_siswa']]['nama_us'] = $dt['nama_us'];
			}
			if (!is_null($dt['id_jenis_transaksi'])) {
				$transaksi['nama_transaksi'] = $dt['nama_transaksi'];
				$transaksi['nominal_dibayarkan'] = $dt['nominal_dibayarkan'];
				$transaksi['tgl'] = $dt['tgl'];
				$siswa[$dt['id_siswa']]['transaksi'][$dt['id_jenis_transaksi']] = $transaksi;
			}

			if (!isset($arr_nmtransaksi[$dt['id_jenis_transaksi']]) && !is_null($dt['id_jenis_transaksi'])) {
				array_push($arr_transaksi, $dt['id_jenis_transaksi']);
				$arr_nmtransaksi[$dt['id_jenis_transaksi']] = $dt['nama_transaksi'];
			}
			if (!isset($arr_nmkelas[$dt['id_kelas']]) && !is_null($dt['id_kelas'])) {
				array_push($arr_kelas, $dt['id_kelas']);
				$arr_nmkelas[$dt['id_kelas']] = $dt['nama_kelas'];
			}
		}
		foreach ($arr_kelas as $id_kel) {
			foreach ($arr_transaksi as $id_trans) {
				$total[$id_kel][$id_trans] = 0;
			}
		}
		/* echo json_encode($total);
							die; */

		foreach ($arr_kelas as $id_kel) {

			// echo "<span style='font-size:11px;font-weight:bold;'>$arr_nmkelas[$id_kel]</span><br>";

			$no = 1;
			$jml_siswa = 0;
			$alltotal = 0;
			$tbody = '';
			foreach ($siswa as $dtsiswa) {
				if ($dtsiswa['id_kelas'] != $id_kel) {
					continue;
				} else {
					$jml_siswa += 1;
				}
				$tbody .= "<tr>
											<td>" . $no++ . "</td>
											<td>$dtsiswa[nama_tahunajar]</td>
											<td>$dtsiswa[nama_siswa]</td>
											<td>$dtsiswa[nama_kelas]</td>
											<td>$dtsiswa[nama_us]</td>";
				for ($i = 0; $i < count($arr_transaksi); $i++) {
					if (isset($dtsiswa['transaksi'][$arr_transaksi[$i]])) {
						$tbody .= "<td>" . $dtsiswa['transaksi'][$arr_transaksi[$i]]['nominal_dibayarkan'] . "</td>";
						$total[$id_kel][$arr_transaksi[$i]] += $dtsiswa['transaksi'][$arr_transaksi[$i]]['nominal_dibayarkan'];
						$grandtotal += $dtsiswa['transaksi'][$arr_transaksi[$i]]['nominal_dibayarkan'];
						$alltotal += $dtsiswa['transaksi'][$arr_transaksi[$i]]['nominal_dibayarkan'];
					} else {
						$tbody .= "<td></td>";
					}
				}
				$tbody .= "</tr>";
			}
			$th_trans = '';
			$th_total = '';
			for ($i = 0; $i < count($arr_transaksi); $i++) {
				$th_trans .= "<th>" . $arr_nmtransaksi[$arr_transaksi[$i]] . "</th>";
				$th_total .= "<th>" . $total[$id_kel][$arr_transaksi[$i]] . "</th>";
			}

			$table = "

				<div class='card border-0 shadow-sm rounded-4 mb-4'>

					<div class='card-header bg-white border-0 py-3'>

						<div class='d-flex justify-content-between align-items-center flex-wrap gap-3'>

							<div>

								<h5 class='fw-bold text-dark mb-1'>
									Rekap Pembayaran Warga
								</h5>

								<span class='text-muted'>
									Wilayah $arr_nmkelas[$id_kel]
								</span>

							</div>

							<button
								onclick='download_excel_perkelas(" . $id_kel . ");'
								class='btn btn-success rounded-3 shadow-sm'
							>
								<i class='bx bxs-file-export'></i>
								Download Excel
							</button>

						</div>

					</div>

					<div class='card-body p-0'>

						<div class='table-responsive'>

							<table class='table align-middle table-hover mb-0'>

								<thead style='background:#F8FAFC;'>

									<tr>

										<th class='text-center fw-semibold'>No</th>

										<th class='fw-semibold'>
											Tahun Ajar
										</th>

										<th class='fw-semibold'>
											Nama Warga
										</th>

										<th class='fw-semibold'>
											Wilayah
										</th>

										<th class='fw-semibold'>
											Petugas
										</th>

										$th_trans

									</tr>

								</thead>

								<tbody>

									$tbody

									<tr style='background:#FAFAFA;'>

										<td colspan='5'
											class='text-end fw-bold'>
											Subtotal
										</td>

										$th_total

									</tr>

									<tr style='background:#F3F6F9;'>

										<td colspan='5'
											class='text-end fw-bold fs-6'>
											Total Pembayaran
										</td>

										<td colspan='" . count($arr_transaksi) . "'
											class='fw-bold text-primary fs-5'>
											Rp " . number_format($alltotal, 0, ".", ".") . "
										</td>

									</tr>

								</tbody>

							</table>

						</div>

					</div>

				</div>

				";

				if ($jml_siswa > 0) {
					echo $table;
				}
		}
		echo "

			<div class='card border-0 shadow-sm rounded-4 mt-4'>

				<div class='card-body'>

					<div class='row align-items-center'>

						<div class='col-md-8'>

							<span class='text-muted d-block mb-1'>
								Grand Total Pembayaran
							</span>

							<h2 class='fw-bold text-primary mb-0'>
								Rp " . number_format($grandtotal, 0, ".", ".") . "
							</h2>

						</div>

						<div class='col-md-4 text-md-end mt-3 mt-md-0'>

							<span class='badge bg-primary px-4 py-3 rounded-pill fs-6'>
								" . count($siswa) . " Warga
							</span>

						</div>

					</div>

				</div>

			</div>

			</div>

			";
	}

	function send_transaksi($th = null, $id_jenjang = null)
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$nama = isset($_GET['nama']) ? $_GET['nama'] : "";
		$limit = 30;
		$pages = $_GET["page"];
		$start = ($pages - 1) * $limit;
		$query = "SELECT COUNT(*) FROM (SELECT id_jenis_transaksi FROM master_jenis_transaksi WHERE id_sesi='$th' AND id_jenjang='$id_jenjang' AND nama_transaksi LIKE '%$nama%') as jml";
		$result = mysqli_query($con, $query);
		$hsl = mysqli_fetch_row($result);
		$count = $hsl[0];
		$page = ceil($count / $limit);

		// modal

		echo '<select id="myselects_thaltrans" style="border:1px solid #E1E1E1;height:25px;margin-bottom:5px;font-size:11px;" onChange="myNewFunctionpagetrans(this);">';
		echo '<option>Page</option>';
		for ($i = 1; $i <= $page; $i++) {
			echo '<option value="' . $i . '" id="' . $i . '">' . $i . '</option>';
		}
		echo '</select>
								<span style="font-family:tahoma;font-size:11px;margin-left:10px;">halaman ' . $pages . ' dari ' . $count . ' data</span>';
		echo '<button id="save_akses_jenjang" class="btn btn-success btn-sm" onclick="popkelompok();" style="margin-left:10px;">
									<i class="bx bx-add-to-queue"></i> Tambah Kategori
									</button>';

		$sql = "SELECT
									kategori_pengeluaran.nama_kategori_pengeluaran,
									kategori_laporan.nama_kategori_laporan,
									kategori_trans.nama_kategori,
									cicil,
									master_jenis_transaksi.id_kategori_pengeluaran,
									id_jenis_transaksi,
									master_jenis_transaksi.nama_transaksi,
									tgl,
									urut,
									status_kelompok,
									id_kategori_laporan,
									kondisi_khusus
								FROM
									master_jenis_transaksi
									LEFT JOIN kategori_trans ON kategori_trans.id_kat_trans = master_jenis_transaksi.urut
									LEFT JOIN kategori_laporan ON kategori_laporan.id_kat_laporan = master_jenis_transaksi.id_kategori_laporan
									LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran 
								WHERE
									master_jenis_transaksi.id_sesi = '$th' 
									AND master_jenis_transaksi.id_jenjang = '$id_jenjang' 
									AND master_jenis_transaksi.nama_transaksi LIKE '%%' 
								ORDER BY
									id_jenis_transaksi ASC 
									LIMIT $start,
									$limit";
		$hs = mysqli_query($con, $sql);


		echo '						
								<table class="table table-sm table-bordered" id="tablizz">
								<thead class="table-light">
									<tr>
										<th>No.</th>
										<th>Nama Iuran</th>
										<th>Alokasi Dana</th>
										<th>Aksi</th>
									</tr>
								</thead>
								<tbody>						
								';


		$no = $start;
		while ($rs = mysqli_fetch_array($hs)) {
			$no++;
			echo '<tr>
									<td>' . $no . '</td>
									<td style="display:none;">' . $rs['id_jenis_transaksi'] . '</td>
									<td style="display:none;">' . $rs['id_kategori_laporan'] . '</td>
									<td>' . $rs['nama_transaksi'] . '</td>
									<td>' . $rs['nama_kategori_laporan'] . '</td>
									<td><button class="btn btn-primary btn-xs" style="font-size:10px;" onclick="aksitrans();">aksi</button></td>
									</tr>';
		}
		echo '</tbody>';
		echo '</table>';
	}


	function simlapoup()
	{
		global $con;
		$id_kat_laporan = isset($_GET['id_kat_laporan']) ? $_GET['id_kat_laporan'] : "";
		$id_jenis_transaksi = isset($_GET['id_jenis_transaksi']) ? $_GET['id_jenis_transaksi'] : "";
		$qry = mysqli_query($con, "UPDATE master_jenis_transaksi SET id_kategori_laporan='$id_kat_laporan' WHERE id_jenis_transaksi=$id_jenis_transaksi");
	}


	function send_kategori_select_lap()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$id_kat_laporan = isset($_GET['id_kat_laporan']) ? $_GET['id_kat_laporan'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$qer = "SELECT * FROM kategori_laporan WHERE id_sesi='$th' AND id_jenjang='$id_jenjang'";
		$qry = mysqli_query($con, $qer);
		echo '<div id="lbl" style="font-size:12px;">Kelompok Laporan <br><span style="color:red;font-size:11px;">* Untuk mengatur kelompok laporan pembayaran *</span></div>
											<select id="myselects_ktg_lap" style="width:250px;border:1px solid #E1E1E1;height:25px;margin-bottom:5px;font-size:11px;">';
		while ($rs = mysqli_fetch_array($qry)) {
			echo '<option value="' . $rs['id_kat_laporan'] . '">' . $rs['nama_kategori_laporan'] . '</option>';
		}
		echo '</select><button onclick="simlapo()" style="margin-left:2px;margin-top:5px;margin-bottom:5px;font-size:10px;" class="btn btn-primary btn-xs">Save</button>';
		echo '<script>$("#myselects_ktg_lap").val(' . $id_kat_laporan . ');</script>';
	}

	function send_kelompok_lap()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$qry = mysqli_query($con, "SELECT * FROM kategori_laporan WHERE id_sesi='$th' AND id_jenjang='$id_jenjang'");
		echo '
									<button onclick="tambah_kel_lap()" style="margin-left:2px;margin-top:5px;margin-bottom:5px;font-size:10px;" class="btn btn-primary btn-xs">Tambah</button>	

									<table class="table table-sm table-bordered">
									<thead class="table-light">
										<tr>
											<th style="width: 10px;">No.</th>
											<th style="width: 80px;">Nama Kelompok</th>
											<th style="width: 10px;">Aksi</th>
										</tr>
									</thead>
									<tbody>	
									';
		$no = 0;
		while ($rs = mysqli_fetch_array($qry)) {
			$no++;
			echo '
												<tr>
													<td>' . $no . '</td>
													<td style="display:none;">' . $rs['id_kat_laporan'] . '</td>
													<td>' . $rs['nama_kategori_laporan'] . '</td>
													<td>
														<button type="button" class="btn btn-success btn-sm"onclick="edit_kelompok_lap();">
                                                        <i class="mdi mdi-pencil d-block font-size-16"></i>
                                                    	</button>

														<button type="button" class="btn btn-danger btn-sm" onclick="hapus_kelompok_lap();">
                                                        <i class="mdi mdi-trash-can d-block font-size-16"></i>
                                                    	</button>
													</td>
												</tr>
											';
		}
		echo "
									</tbody>
									</table>
									";
	}

	function simpan_kel_lap()
	{
		global $con;
		$nama = isset($_GET['nama']) ? $_GET['nama'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$qry = mysqli_query($con, "INSERT INTO kategori_laporan(nama_kategori_laporan,id_sesi,id_jenjang) VALUES ('$nama','$th','$id_jenjang')");
		if ($qry) {
			echo "Berhasil ditambahkan!";
		} else {
			echo "Gagal ditambahkan!";
		}
	}

	function hapus_kelompok_lap()
	{
		global $con;
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$sql = mysqli_query($con, "SELECT id_jenis_transaksi FROM master_jenis_transaksi WHERE id_kategori_laporan='$id'");
		$hitung = mysqli_num_rows($sql);
		if ($hitung > 0) {
			echo "Tidak dapat dihapus, kategori sudah dipakai!";
		} else {
			$qr = mysqli_query($con, "DELETE FROM kategori_laporan WHERE id_kat_laporan=$id");
			if ($qr) {
				echo "Berhasil dihapus!";
			} else {
				echo "Gagal dihapus!";
			}
		}
	}

	function edit_kelompok_lap()
	{
		global $con;
		$id = isset($_GET['id']) ? $_GET['id'] : "";
		$nama = isset($_GET['nama']) ? $_GET['nama'] : "";
		$qry = mysqli_query($con, "UPDATE kategori_laporan SET nama_kategori_laporan='$nama' WHERE id_kat_laporan=$id");
		if ($qry) {
			echo "Berhasil diupdate!";
		} else {
			echo "Gagal Update!";
		}
	}
	function laporan_harian_estimasi()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$d = isset($_GET['d']) ? $_GET['d'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : "";

		if ($id_kelas != 0) {
			$kls = " AND master_alokasi_transaksi_all.id_kelas IN($id_kelas)";
		} else {
			$kls = "";
		}

		if ($d != 0) {
			$trans = " AND master_alokasi_transaksi_all.id_jenis_transaksi IN($d)";
		} else {
			$trans = "";
		}
		$kolom_kelas = 0;
		$arr_kelas = explode(',', $id_kelas);
		if (count($arr_kelas) > 1) {
			$kolom_kelas = 1;
		}

		$sqls = "SELECT
											data_siswa.id_siswa,
											data_siswa.nama_siswa,
											master_jenis_transaksi.nama_transaksi,
											data_kelas.nama_kelas,
											data_kelas.id_kelas,
											master_alokasi_transaksi_all.nominal,
											master_alokasi_transaksi_all.id_jenis_transaksi 
										FROM
											data_siswa
											LEFT JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa AND 
												master_alokasi_transaksi_all.id_sesi IS NOT NULL
											LEFT JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi
											LEFT JOIN penempatan_siswa ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa
											INNER JOIN data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas AND data_kelas.id_sesi='$th'
										WHERE
											(data_siswa.id_jenjang = $id_jenjang
											AND penempatan_siswa.thn_ajar = '$th' AND master_alokasi_transaksi_all.id_sesi='$th'
											$kls 
											$trans )
												OR (data_siswa.id_jenjang = $id_jenjang AND master_alokasi_transaksi_all.nis_from_idsiswa  IS NULL)
												ORDER BY data_kelas.id_kelas,data_siswa.nama_siswa,master_alokasi_transaksi_all.id_jenis_transaksi ASC";
		$result = mysqli_query($con, $sqls);
		$siswa = [];
		$arr_transaksi = [];
		$arr_nmtransaksi = [];
		$total = [];
		$arr_kelas = [];
		$arr_nmkelas = [];
		$grandtotal = 0;
		while ($dt = mysqli_fetch_array($result)) {
			$transaksi = [];
			if (!isset($siswa[$dt['id_siswa']])) {
				$siswa[$dt['id_siswa']]['id_siswa'] = $dt['id_siswa'];
				$siswa[$dt['id_siswa']]['nama_siswa'] = $dt['nama_siswa'];
				$siswa[$dt['id_siswa']]['nama_kelas'] = $dt['nama_kelas'];
				$siswa[$dt['id_siswa']]['id_kelas'] = $dt['id_kelas'];
			}
			$transaksi['nama_transaksi'] = $dt['nama_transaksi'];
			$transaksi['nominal'] = $dt['nominal'];
			$siswa[$dt['id_siswa']]['transaksi'][$dt['id_jenis_transaksi']] = $transaksi;
			if (!isset($arr_nmtransaksi[$dt['id_jenis_transaksi']]) && !is_null($dt['id_jenis_transaksi'])) {
				array_push($arr_transaksi, $dt['id_jenis_transaksi']);
				$arr_nmtransaksi[$dt['id_jenis_transaksi']] = $dt['nama_transaksi'];
			}
			if (!isset($arr_nmkelas[$dt['id_kelas']]) && !is_null($dt['id_kelas'])) {
				array_push($arr_kelas, $dt['id_kelas']);
				$arr_nmkelas[$dt['id_kelas']] = $dt['nama_kelas'];
			}
		}
		foreach ($arr_kelas as $id_kel) {
			foreach ($arr_transaksi as $id_trans) {
				$total[$id_kel][$id_trans] = 0;
			}
		}
		echo "

			<style>

			.estimasi-card{
				border:none;
				border-radius:18px;
				overflow:hidden;
				box-shadow:0 2px 12px rgba(0,0,0,.05);
				margin-bottom:25px;
			}

			.estimasi-header{
				padding:20px 25px;
				background:#fff;
				border-bottom:1px solid #EEF2F6;
			}

			.estimasi-table th{
				background:#F8FAFC;
				color:#344054;
				font-size:14px;
				font-weight:600;
				padding:14px;
				white-space:nowrap;
			}

			.estimasi-table td{
				padding:14px;
				font-size:14px;
				color:#475467;
				vertical-align:middle;
			}

			.estimasi-table tbody tr:hover{
				background:#FAFAFA;
			}

			.total-box{
				background:#F0FDF4;
				border:1px solid #BBF7D0;
				border-radius:16px;
				padding:18px;
			}

			.total-title{
				font-size:13px;
				color:#16A34A;
			}

			.total-nominal{
				font-size:28px;
				font-weight:700;
				color:#15803D;
			}

			</style>

			";

			foreach ($arr_kelas as $id_kel) {

				$jml_siswa = 0;
				$alltotal = 0;
				$tbody = '';

				foreach ($arr_transaksi as $id_trans) {

					$nominal_transaksi = 0;

					foreach ($siswa as $dtsiswa) {

						if ($dtsiswa['id_kelas'] != $id_kel) {
							continue;
						}

						if (isset($dtsiswa['transaksi'][$id_trans])) {

							$nominal = $dtsiswa['transaksi'][$id_trans]['nominal'];

							$nominal_transaksi += $nominal;

							$alltotal += $nominal;

							$grandtotal += $nominal;
						}
					}

					$tbody .= "

						<tr>

							<td>
								" . $arr_nmtransaksi[$id_trans] . "
							</td>

							<td class='fw-semibold text-success'>
								Rp " . number_format($nominal_transaksi, 0, ".", ".") . "
							</td>

						</tr>

					";
				}

				echo "

				<div class='estimasi-card'>

					<div class='estimasi-header'>

						<div class='d-flex justify-content-between align-items-center flex-wrap gap-3'>

							<div>

								<h4 class='fw-bold mb-1'>
									Estimasi Pemasukan
								</h4>

								<span class='text-muted'>
									Wilayah " . $arr_nmkelas[$id_kel] . "
								</span>

							</div>

							<button
								onclick='download_excel_perkelas(" . $id_kel . ");'
								class='btn btn-success rounded-3'
							>
								<i class='bx bxs-file-export'></i>
								Download Excel
							</button>

						</div>

					</div>

					<div class='card-body'>

						<div class='table-responsive'>

							<table class='table estimasi-table align-middle mb-0'>

								<thead>

									<tr>

										<th>
											Jenis Iuran
										</th>

										<th width='220'>
											Target Pemasukan
										</th>

									</tr>

								</thead>

								<tbody>

									$tbody

									<tr style='background:#F9FAFB;'>

										<td class='fw-bold'>
											Total Estimasi
										</td>

										<td class='fw-bold text-success fs-5'>
											Rp " . number_format($alltotal, 0, ".", ".") . "
										</td>

									</tr>

								</tbody>

							</table>

						</div>

					</div>

				</div>

				";
			}

			echo "

			<div class='total-box mt-4'>

				<div class='total-title mb-2'>
					Grand Total Seluruh Wilayah
				</div>

				<div class='total-nominal'>
					Rp " . number_format($grandtotal, 0, ".", ".") . "
				</div>

			</div>

			";
	}

	function send_tahun_tag()
	{
		global $con;
		$querykls = "SELECT * FROM data_tahunajar ORDER BY urutan ASC";
		$result = mysqli_query($con, $querykls);
		echo "
										<div class='form-group mb-3'>
											<label class='form-label'>Tahun</label>
											<select required class='form-control' id='myselects_th_tag' onChange='tampil_kls(this);' data-trigger name='choices-single-default'>";
		echo '<option value="">Pilih Periode</option>';
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="' . $dt['id_tahunajar'] . '">' . $dt['nama_tahunajar'] . '</option>';
		}

		echo "		
											</select>
										</div>
										";
	}

	function tampil_laporan()
	{

		echo "
			<div class='form-group mb-3'>
				<label class='form-label'>Tahun</label>
				<select required class='form-control' id='myselects_th_tag' onChange='tampil_lap(this);' data-trigger name='choices-single-default' id='myselects_ran'>";

		echo "		
											</select>
										</div>
										";
	}

	function send_jenjang_tag()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$query = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
											INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'";
		$resultjen = mysqli_query($con, $query);

		echo "
											<div class='form-group mb-3'>
												<label class='form-label'>Wilayah</label>
												<select required class='form-control' id='myselects_thj_tag' onChange='myNewFunctionjangtag(this);'>";

		while ($dt = mysqli_fetch_array($resultjen)) {
			echo '<option value="' . $dt['id_jenjang'] . '">' . $dt['nama_jenjang'] . '</option>';
		}

		echo "		
												</select>
											</div>
											";
	}

	function send_kls_tag()
	{
		global $con;
		$sesi = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$querykls = "SELECT * FROM data_kelas 
											WHERE id_sesi='$sesi' AND id_jenjang='$id_jenjang' ORDER BY urutan ASC";
		$result = mysqli_query($con, $querykls);

		echo "
												<div class='form-group mb-12'>
													<label class='form-label'>Sub Wilayah</label>
													<select class='form-control' name='choices-multiple-remove-button'
													id='myselects_kls'
													placeholder='This is a placeholder' multiple style='width: 100%;'>";

		echo '<option value="">Tampilkan semua wilayah</option>';
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="' . $dt['id_kelas'] . '">' . $dt['nama_kelas'] . '</option>';
		}

		echo "	
													
													</select>
													</div>";
	}

	function nulls()
	{
		echo '';
	}

	function send_trans_tag()
	{
		global $con;
		$sesi = isset($_GET['id']) ? $_GET['id'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$querykls = "SELECT
											id_jenis_transaksi,nama_transaksi,id_sesi
											FROM
											master_jenis_transaksi
											WHERE
												master_jenis_transaksi.id_sesi = $sesi 
												AND id_jenjang = $id_jenjang
											ORDER BY id_jenis_transaksi ASC
											";
		$result = mysqli_query($con, $querykls);
		echo "
											<div class='col-lg-12 col-md-6'>
												<div class='mb-3'>
													<label class='form-label'>Jenis Pembayaran</label>
													<select class='form-control' name='chol-multiple-remove-button'
															id='myselects_jenis_pem'
															placeholder='This is a placeholder' multiple>";
		echo '<option value="">Tampilkan Jenis Pembayaran</option>';
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="' . $dt['id_jenis_transaksi'] . '">' . $dt['nama_transaksi'] . '</option>';
		}
		echo '</select>';
		echo '<button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="lap_tampil();" style="margin-top:15px;margin-bottom:5px"type="button">Tampilkan penerimaan</button>
		
		<button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="tampil_laporan_alls();" style="margin-top:15px;margin-bottom:5px"type="button">Tampil kategori jatuh tempo</button>';


		echo '</div>	
											</div>';
	}
	function send_laporan_tag_new($excel = 0)
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$kls = isset($_GET['kls']) ? $_GET['kls'] : "";
		$trans = isset($_GET['trans']) ? $_GET['trans'] : "";
		if ($kls != 0) {
			$kelas = " AND penempatan_siswa.id_kelas IN ($kls)";
		} else {
			$kelas = "";
		}

		if ($trans != 0) {
			$item = " AND master_alokasi_transaksi_all.id_jenis_transaksi IN ($trans)";
		} else {
			$item = "";
		}

		$query = "SELECT
													penempatan_siswa.nis_from_idsiswa,
													data_siswa.nama_siswa,
													data_siswa.id_siswa,
													data_kelas.nama_kelas,
													data_kelas.id_kelas,
													master_alokasi_transaksi_all.id_jenis_transaksi,
													master_jenis_transaksi.nama_transaksi,
													SUM( nominal ) AS kewajiban,
													IFNULL(bayar.pembayaran,0) as pembayaran
												FROM
													penempatan_siswa
													INNER JOIN data_siswa ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa
													INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas
													INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.nis_from_idsiswa = penempatan_siswa.nis_from_idsiswa 
													INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=master_alokasi_transaksi_all.id_jenis_transaksi
													LEFT JOIN 
													(SELECT
													transaksi_siswa.nis_from_idsiswa,master_alokasi_transaksi_all.id_jenis_transaksi,
													SUM(nominal_dibayarkan) AS pembayaran 
												FROM
													transaksi_siswa
													INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
												GROUP BY
													master_alokasi_transaksi_all.nis_from_idsiswa,id_jenis_transaksi) as bayar ON penempatan_siswa.nis_from_idsiswa=bayar.nis_from_idsiswa AND master_alokasi_transaksi_all.id_jenis_transaksi=bayar.id_jenis_transaksi
												WHERE
													data_siswa.id_jenjang = $id_jenjang 
													AND master_alokasi_transaksi_all.id_sesi = '$th' 
													AND penempatan_siswa.thn_ajar='$th'
													$kelas
													$item
												GROUP BY
													master_alokasi_transaksi_all.nis_from_idsiswa,id_jenis_transaksi 
												ORDER BY
													data_kelas.id_kelas,
													data_siswa.nama_siswa,
												master_alokasi_transaksi_all.id_jenis_transaksi	ASC	
												";
		// echo $query;
		$sql = mysqli_query($con, $query);
		$tagihan = [];
		$kelas = [];

		while ($cs = mysqli_fetch_array($sql)) {
			if (!isset($nama_kelas[$cs['id_kelas']])) {
				$nama_kelas[$cs['id_kelas']] = $cs['nama_kelas'];
				$idi_kelas[$cs['id_kelas']] = $cs['id_kelas'];
			}
			if (!isset($nama_transaksi[$cs['id_jenis_transaksi']])) {
				$nama_transaksi[$cs['id_jenis_transaksi']] = $cs['nama_transaksi'];
			}
			if (!isset($kelas[$cs['id_kelas']][$cs['id_siswa']])) {
				$kelas[$cs['id_kelas']][$cs['id_siswa']]['id_siswa'] = $cs['id_siswa'];
				$kelas[$cs['id_kelas']][$cs['id_siswa']]['nama_siswa'] = $cs['nama_siswa'];
				$kelas[$cs['id_kelas']][$cs['id_siswa']]['tagihan'] = [];
			}
			$tagihan['id_jenis_transaksi'] = $cs['id_jenis_transaksi'];
			$tagihan['nama_transaksi'] = $cs['nama_transaksi'];
			$tagihan['kewajiban'] = $cs['kewajiban'];
			$tagihan['pembayaran'] = $cs['pembayaran'];
			$tagihan['kekurangan'] = $cs['kewajiban'] - $cs['pembayaran'];
			$kelas[$cs['id_kelas']][$cs['id_siswa']]['tagihan'][$cs['id_jenis_transaksi']] = $tagihan;
		}
		if ($excel == 0) {
			echo "<span style='font-weight:bold;'>Laporan tagihan per tahun ajaran </span>";
			echo "<div style='overflow-x:auto;'>";
			echo "<button onclick='cetak_send_laporan_tag_newb();' style='border-radius:3px;border:0px;padding:5px;'><img src='images/logo/excel.png' style='width:15px;height:15px;'> <span style='font-size:11px;'>Download excel</span></button><p>";
		}
		$tabel = "";
		$grand_all = 0;
		foreach ($kelas as $data_kelas) {
			$id_kelas = key($kelas);
			next($kelas);
			$tabel .= "<b>$nama_kelas[$id_kelas]</b><button class='btn btn-primary btn-xs' style='margin-left:10px;margin-top:5px;margin-bottom:5px;font-size:10px;display:none;' id='$idi_kelas[$id_kelas]' onClick='reply_click(this.id)'>Cetak per kelas</button>
														<button class='btn btn-primary waves-effect waves-light' style='margin-left:10px;margin-top:5px;margin-bottom:5px;font-size:13px;' onClick='ambil_perkelas(" . $id_kelas . ");'>Cetak per kelas</button>";
			$tabel .= "
														
														<table class='table table-bordered table-sm' style='font-size:13px;width:100%'>
															<div style='max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;'>
																<thead>
																	<tr class='table-secondary'>
																	<th>No</th>
																	<th>Nama siswa</th>";
			foreach ($nama_transaksi as $data_jenistransaksi) {
				$id_jenis_transaksi = key($nama_transaksi);
				next($nama_transaksi);
				$total_jenistrans[$id_jenis_transaksi] = 0;
				$tabel .= "<th>$data_jenistransaksi</th>";
			}

			/* echo json_encode($total_jenistrans); */
			reset($nama_transaksi);
			$tabel .= '<th>Total</th>';
			$no = 1;
			echo '</tr>';
			"</thead>
																<tbody>	
																	<tr>";

			foreach ($data_kelas as $data_siswa) {
				$tabel .= "<tr>";
				$tabel .= "<td>" . $no++ . "</td>";
				$tabel .= "<td>$data_siswa[nama_siswa]</td>";
				$total = 0;
				foreach ($nama_transaksi as $data_tagihan) {
					$id_jenis_transaksi = key($nama_transaksi);
					next($nama_transaksi);
					if (isset($data_siswa['tagihan'][$id_jenis_transaksi])) {
						$total_jenistrans[$id_jenis_transaksi] += $data_siswa['tagihan'][$id_jenis_transaksi]['kekurangan'];
						$total += $data_siswa['tagihan'][$id_jenis_transaksi]['kekurangan'];
						$tabel .= "<td>" . $data_siswa['tagihan'][$id_jenis_transaksi]['kekurangan'] . "</td>";
					} else {
						$tabel .= "<td></td>";
					}
				}
				reset($nama_transaksi);
				$tabel .= "<td>$total</td></tr>";
			}

			$tabel .= "<tr><th colspan='2'></th>";
			$grand_total = 0;
			foreach ($nama_transaksi as $data_jenistransaksi) {
				$id_jenis_transaksi = key($nama_transaksi);
				$grand_total += $total_jenistrans[$id_jenis_transaksi];
				next($nama_transaksi);
				$tabel .= "<th>" . $total_jenistrans[$id_jenis_transaksi] . "</th>";
			}
			$grand_all += $grand_total;
			reset($nama_transaksi);
			$tabel .= "<th>$grand_total</th>
																	</tr>
																</tbody>
															</div
														</table>";
		}
		if ($excel == 0) {
			echo $tabel;
			echo "$grand_all</div>";
		} else {
			return $tabel;
		}
		//echo json_encode($kelas);
	}
	function cetak_send_laporan_tag_new()
	{
		$excel = $this->send_laporan_tag_new(1);
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=laporan_tagihannew.xls");
		echo $excel;
	}

	function ambil_perkelas_saja($excel = 0)
	{
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=laporan_harian.xls");
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$id_kelas_aja = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : "";
		$trans = isset($_GET['trans']) ? $_GET['trans'] : "";
		if ($id_kelas_aja != 0) {
			$kelas = " AND penempatan_siswa.id_kelas IN ($id_kelas_aja)";
		} else {
			$kelas = "";
		}

		if ($trans != 0) {
			$item = " AND master_alokasi_transaksi_all.id_jenis_transaksi IN ($trans)";
		} else {
			$item = "";
		}

		$query = "SELECT
													penempatan_siswa.nis_from_idsiswa,
													data_siswa.nama_siswa,
													data_siswa.id_siswa,
													data_kelas.nama_kelas,
													data_kelas.id_kelas,
													master_alokasi_transaksi_all.id_jenis_transaksi,
													master_jenis_transaksi.nama_transaksi,
													SUM( nominal ) AS kewajiban,
													IFNULL(bayar.pembayaran,0) as pembayaran
												FROM
													penempatan_siswa
													INNER JOIN data_siswa ON data_siswa.id_siswa = penempatan_siswa.nis_from_idsiswa
													INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas
													INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.nis_from_idsiswa = penempatan_siswa.nis_from_idsiswa 
													INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=master_alokasi_transaksi_all.id_jenis_transaksi
													LEFT JOIN 
													(SELECT
													transaksi_siswa.nis_from_idsiswa,master_alokasi_transaksi_all.id_jenis_transaksi,
													SUM(nominal_dibayarkan) AS pembayaran 
												FROM
													transaksi_siswa
													INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
												GROUP BY
													master_alokasi_transaksi_all.nis_from_idsiswa,id_jenis_transaksi) as bayar ON penempatan_siswa.nis_from_idsiswa=bayar.nis_from_idsiswa AND master_alokasi_transaksi_all.id_jenis_transaksi=bayar.id_jenis_transaksi
												WHERE
													data_siswa.id_jenjang = $id_jenjang 
													AND master_alokasi_transaksi_all.id_sesi = '$th' 
													AND penempatan_siswa.thn_ajar='$th'
													AND penempatan_siswa.id_kelas = '$id_kelas_aja'
													$kelas
													$item
												GROUP BY
													master_alokasi_transaksi_all.nis_from_idsiswa,id_jenis_transaksi 
												ORDER BY
													data_kelas.id_kelas,
													data_siswa.nama_siswa,
												master_alokasi_transaksi_all.id_jenis_transaksi	ASC	
												";
		$sql = mysqli_query($con, $query);
		$tagihan = [];
		$kelas = [];

		while ($cs = mysqli_fetch_array($sql)) {
			if (!isset($nama_kelas[$cs['id_kelas']])) {
				$nama_kelas[$cs['id_kelas']] = $cs['nama_kelas'];
				$idi_kelas[$cs['id_kelas']] = $cs['id_kelas'];
			}
			if (!isset($nama_transaksi[$cs['id_jenis_transaksi']])) {
				$nama_transaksi[$cs['id_jenis_transaksi']] = $cs['nama_transaksi'];
			}
			if (!isset($kelas[$cs['id_kelas']][$cs['id_siswa']])) {
				$kelas[$cs['id_kelas']][$cs['id_siswa']]['id_siswa'] = $cs['id_siswa'];
				$kelas[$cs['id_kelas']][$cs['id_siswa']]['nama_siswa'] = $cs['nama_siswa'];
				$kelas[$cs['id_kelas']][$cs['id_siswa']]['tagihan'] = [];
			}
			$tagihan['id_jenis_transaksi'] = $cs['id_jenis_transaksi'];
			$tagihan['nama_transaksi'] = $cs['nama_transaksi'];
			$tagihan['kewajiban'] = $cs['kewajiban'];
			$tagihan['pembayaran'] = $cs['pembayaran'];
			$tagihan['kekurangan'] = $cs['kewajiban'] - $cs['pembayaran'];
			$kelas[$cs['id_kelas']][$cs['id_siswa']]['tagihan'][$cs['id_jenis_transaksi']] = $tagihan;
		}
		if ($excel == 0) {
			echo "<span style='font-weight:bold;'>Laporan tagihan per tahun ajaran </span>";
			echo "<div style='overflow-x:auto;'>";
		}
		$tabel = "";
		$grand_all = 0;
		foreach ($kelas as $data_kelas) {
			$id_kelas = key($kelas);
			next($kelas);
			$tabel .= "<b>$nama_kelas[$id_kelas]</b><button class='btn btn-primary btn-xs' style='margin-left:10px;margin-top:5px;margin-bottom:5px;font-size:10px;display:none;' id='$idi_kelas[$id_kelas]' onClick='reply_click(this.id)'>Cetak per kelas</button>
														<button class='btn btn-primary btn-xs' style='margin-left:10px;margin-top:5px;margin-bottom:5px;font-size:10px;display:none;' onClick='ambil_perkelas(" . $id_kelas . ");'>Cetak per kelas</button>";
			$tabel .= "
														<table id='tab' width='1500px;' border='1' style='margin-top:10px;'>
														<tr>
														<th>No</th>
														<th>Nama siswa</th>
														";
			foreach ($nama_transaksi as $data_jenistransaksi) {
				$id_jenis_transaksi = key($nama_transaksi);
				next($nama_transaksi);
				$total_jenistrans[$id_jenis_transaksi] = 0;
				$tabel .= "<th>$data_jenistransaksi</th>";
			}

			/* echo json_encode($total_jenistrans); */
			reset($nama_transaksi);
			$tabel .= "<th>Total</th></tr>";
			$no = 1;
			foreach ($data_kelas as $data_siswa) {
				$tabel .= "<tr>";
				$tabel .= "<td>" . $no++ . "</td>";
				$tabel .= "<td>$data_siswa[nama_siswa]</td>";
				$total = 0;
				foreach ($nama_transaksi as $data_tagihan) {
					$id_jenis_transaksi = key($nama_transaksi);
					next($nama_transaksi);
					if (isset($data_siswa['tagihan'][$id_jenis_transaksi])) {
						$total_jenistrans[$id_jenis_transaksi] += $data_siswa['tagihan'][$id_jenis_transaksi]['kekurangan'];
						$total += $data_siswa['tagihan'][$id_jenis_transaksi]['kekurangan'];
						$tabel .= "<td>" . $data_siswa['tagihan'][$id_jenis_transaksi]['kekurangan'] . "</td>";
					} else {
						$tabel .= "<td></td>";
					}
				}
				reset($nama_transaksi);
				$tabel .= "<td>$total</td></tr>";
			}

			$tabel .= "<tr><th colspan='2'></th>";
			$grand_total = 0;
			foreach ($nama_transaksi as $data_jenistransaksi) {
				$id_jenis_transaksi = key($nama_transaksi);
				$grand_total += $total_jenistrans[$id_jenis_transaksi];
				next($nama_transaksi);
				$tabel .= "<th>" . $total_jenistrans[$id_jenis_transaksi] . "</th>";
			}
			$grand_all += $grand_total;
			reset($nama_transaksi);
			$tabel .= "<th>$grand_total</th></tr></table>";
		}
		if ($excel == 0) {
			echo $tabel;
			echo "$grand_all</div>";
		} else {
			return $tabel;
		}
		//echo json_encode($kelas);
	}

	function send_trans_bul()
	{
		echo "
											<div class='form-group mb-12'>
											<label class='form-label'>Tampilkan Bulan:</label>
												<select class='form-control' name='choices-multiple-remove-button'
													id='myselects_thj_bulan'
													placeholder='This is a placeholder' multiple style='width: 100%;'>";

		echo '<option value="07">Bulan Juli</option>';
		echo '<option value="08">Bulan Agustus</option>';
		echo '<option value="09">Bulan September</option>';
		echo '<option value="10">Bulan Oktober</option>';
		echo '<option value="11">Bulan November</option>';
		echo '<option value="12">Bulan Desember</option>';
		echo '<option value="01">Bulan Januari</option>';
		echo '<option value="02">Bulan Februari</option>';
		echo '<option value="03">Bulan Maret</option>';
		echo '<option value="04">Bulan April</option>';
		echo '<option value="05">Bulan Mei</option>';
		echo '<option value="06">Bulan Juni</option>';
		echo '
												</select>
											</div>';
	}
	function send_trans_kecuali()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$querykls = "SELECT
												master_jenis_transaksi.id_jenis_transaksi,
												data_tahunajar.nama_tahunajar,
												master_jenis_transaksi.nama_transaksi,
											data_tahunajar.id_tahunajar	
											FROM
												master_jenis_transaksi
												INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar = master_jenis_transaksi.id_sesi 
											WHERE
												id_jenjang = '$id_jenjang'";
		$result = mysqli_query($con, $querykls);
		echo "
											<div class='form-group mb-12'>
											<label class='form-label'>Tampilkan Item:</label>
												<select class='form-control' name='choices-multiple-remove-button'
													id='myselects_thk'
													placeholder='This is a placeholder' multiple style='width: 100%;'>";

		while ($dt = mysqli_fetch_array($result)) {
			if (!isset($tahun[$dt['id_tahunajar']])) {
				$tahun[$dt['id_tahunajar']] = [];
			}
			array_push($tahun[$dt['id_tahunajar']], $dt);
		}

		foreach ($tahun as $ths) {
			echo '<optgroup label="' . $ths[0]['nama_tahunajar'] . '">';
			foreach ($ths as $dt) {
				echo '<option value="' . $dt['id_jenis_transaksi'] . '">' . $dt['nama_tahunajar'] . ' - ' . $dt['nama_transaksi'] . '</option>';
			}
			echo '</optgroup>';
		}

		echo '
											
												</select>
											</div>
											<p><br>';
		echo '<button class="btn btn-primary waves-effect waves-light" style="margin-left:10px;" onclick="tampil_laporan_bul();"id="lapor_pak">Tampilkan</button>';
	}

	function send_laporan_tag_alls($excel = 0)
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$id_bulan = isset($_GET['id_bulan']) ? $_GET['id_bulan'] : "";
		$id_jenis_transaksi = isset($_GET['id_jenis_transaksi']) ? $_GET['id_jenis_transaksi'] : "";
		$kls = isset($_GET['kls']) ? $_GET['kls'] : "";
		$bulan['07']['id'] = '07';
		$bulan['07']['nama_bulan'] = 'Juli';
		$bulan['08']['id'] = '08';
		$bulan['08']['nama_bulan'] = 'Agustus';
		$bulan['09']['id'] = '09';
		$bulan['09']['nama_bulan'] = 'September';
		$bulan['10']['id'] = '10';
		$bulan['10']['nama_bulan'] = 'Oktober';
		$bulan['11']['id'] = '11';
		$bulan['11']['nama_bulan'] = 'November';
		$bulan['12']['id'] = '12';
		$bulan['12']['nama_bulan'] = 'Desember';
		$bulan['01']['id'] = '01';
		$bulan['01']['nama_bulan'] = 'Januari';
		$bulan['02']['id'] = '02';
		$bulan['02']['nama_bulan'] = 'Februari';
		$bulan['03']['id'] = '03';
		$bulan['03']['nama_bulan'] = 'Maret';
		$bulan['04']['id'] = '04';
		$bulan['04']['nama_bulan'] = 'April';
		$bulan['05']['id'] = '05';
		$bulan['05']['nama_bulan'] = 'Mei';
		$bulan['06']['id'] = '06';
		$bulan['06']['nama_bulan'] = 'Juni';
		if ($id_bulan != 0) {
			$bulan_dipilih = explode(',', $id_bulan);
		} else {
			$bulan_dipilih = [];
			foreach ($bulan as $data) {
				array_push($bulan_dipilih, $data['id']);
			}
		}
		if ($kls != 0) {
			$kelas = " AND penempatan_siswa.id_kelas IN($kls)";
		} else {
			$kelas = "";
		}

		if ($id_jenis_transaksi != 0) {
			$transand = "AND master_alokasi_transaksi_all.id_jenis_transaksi NOT IN ($id_jenis_transaksi)";
			$transwhere = "WHERE master_alokasi_transaksi_all.id_jenis_transaksi NOT IN ($id_jenis_transaksi)";
		} else {
			$transand = "";
			$transwhere = "";
		}
		$tagihan = [];
		$tagihan_siswa = [];
		$pembayaran = [];
		foreach ($bulan_dipilih as $hasil) {
			array_push($tagihan, "SUM(IF( DATE_FORMAT( master_alokasi_transaksi_all.tgl_jth, '%m') = '$hasil', master_alokasi_transaksi_all.nominal, 0  ) )AS tagihan_$hasil");
			array_push($tagihan_siswa, "tagihan_$hasil-IFNULL(pembayaran_$hasil,0) AS tagihan_siswa_$hasil");
			array_push($pembayaran, "SUM( IF ( DATE_FORMAT( master_alokasi_transaksi_all.tgl_jth, '%m' ) = '$hasil', transaksi_siswa.nominal_dibayarkan, 0 ) ) AS pembayaran_$hasil");
		}
		$tagihan = implode(', ', $tagihan);
		$tagihan_siswa = implode(', ', $tagihan_siswa);
		$pembayaran = implode(', ', $pembayaran);
		$query = "SELECT tagihan.id_siswa,tagihan.nama_siswa,tagihan.id_kelas,tagihan.nama_kelas,
											$tagihan_siswa FROM
											(SELECT
												id_siswa,
												nama_siswa,
												penempatan_siswa.id_kelas,
												data_kelas.nama_kelas,
												$tagihan
												FROM
													data_siswa
													INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.nis_from_idsiswa=data_siswa.id_siswa
													INNER JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
													INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas 
												WHERE
													penempatan_siswa.thn_ajar = '$th' 
													AND data_siswa.id_jenjang = $id_jenjang
											$kelas		
													$transand
												GROUP BY
													data_kelas.id_kelas,
													data_siswa.id_siswa 
												ORDER BY
												data_kelas.id_kelas,
												data_siswa.nama_siswa ASC) as tagihan
												LEFT JOIN (SELECT
												transaksi_siswa.nis_from_idsiswa,
												$pembayaran 
											FROM
												transaksi_siswa
												INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
												$transwhere
											GROUP BY
												master_alokasi_transaksi_all.nis_from_idsiswa) as pembayaran ON tagihan.id_siswa=pembayaran.nis_from_idsiswa";
		$sql = mysqli_query($con, $query);
		if ($excel == 0) {
			echo '<div style="overflow-x:auto;">';

			echo "<button onclick='cetak_send_laporan_tag_alls();' style='border-radius:3px;border:0px;padding:5px;'><img src='images/logo/excel.png' style='width:15px;height:15px;'> <span style='font-size:11px;'>Download excel</span></button><p>";
		}
		$kelas = [];
		$nama_kelas = [];

		while ($rs = mysqli_fetch_array($sql)) {
			if (!isset($kelas[$rs['id_kelas']])) {
				$kelas[$rs['id_kelas']] = [];
				$nama_kelas[$rs['id_kelas']] = $rs['nama_kelas'];
			}
			array_push($kelas[$rs['id_kelas']], $rs);
			$total[$rs['id_siswa']] = 0;
		}
		$tabel = "";
		$grand_total_all = 0;
		foreach ($kelas as $kl) {

			$key_kelas = key($kelas);
			$tabel .= "<b>" . $nama_kelas[$key_kelas] . "</b><button class='btn btn-primary btn-xs' style='margin-left:10px;margin-top:5px;margin-bottom:5px;font-size:10px;' onclick='cetak_surat_tagi2();'>Cetak per kelas</button>";
			next($kelas);
			$tabel .= '
													
													<table class="table table-bordered table-sm" style="font-size:13px;width:100%">
															<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
																<thead>
																	<tr class="table-secondary">
																	<th>No</th>
																	<th>Nama siswa</th>
																	<th>Kelas</th>';
			$no = 1;
			foreach ($bulan_dipilih as $hasil) {
				$total_bulan[$hasil] = 0;
				$tabel .= "<th>" . $bulan[$hasil]['nama_bulan'] . "</th>";
			}
			$tabel .= '<th width="15%">Total</th><th>Aksi</th></tr>';
			foreach ($kl as $siswa) {

				echo '</thead>';
				echo '<tbody>';




				$tabel .= "<tr>
																<td>" . $no++ . "</td>
																<td>" . $siswa['nama_siswa'] . "</td>
																<td>" . $siswa['nama_kelas'] . "</td>
																";
				foreach ($bulan_dipilih as $hasil_data) {
					$tabel .= "<td>" . number_format($siswa['tagihan_siswa_' . $hasil_data], 0, ".", ".") . "</td>";
					$total[$siswa['id_siswa']] += $siswa['tagihan_siswa_' . $hasil_data];

					$total_bulan[$hasil_data] += $siswa['tagihan_siswa_' . $hasil_data];
				}
				$tabel .= "<td>" . number_format($total[$siswa['id_siswa']], 0, ".", ".") . "</td><td><button class='btn btn-primary btn-xs' style='margin-left:10px;margin-top:5px;margin-bottom:5px;font-size:10px;' onclick='cetaksurat(" . $siswa['id_siswa'] . ");'>Cetak</button></td>
																</tr>";
			}
			$tabel .= "<tr><td colspan=3>Total</td>";
			$grand_total = 0;
			foreach ($bulan_dipilih as $hasil) {
				$tabel .= "<td>" . number_format($total_bulan[$hasil], 0, ".", ".") . "</td>";
				$grand_total += $total_bulan[$hasil];
			}
			$tabel .= "<td>" . number_format($grand_total, 0, ".", ".") . "</td></tr></table>";
			$grand_total_all += $grand_total;
		}
		if ($excel == 0) {
			echo $tabel;
			echo '<table id="tabse" width="100%" border="1" style="margin-top:10px;">
																	
																		<tr>
																			<th>Grand total ' . number_format($grand_total_all, 0, ".", ".") . '</th>
																		</tr>	
																</table></div>';
		} else {
			return $tabel;
		}
		echo '</tbody>
															</div>
													</table>';




		//echo json_encode($kelas);
	}


	function cetak_send_laporan_tag_alls()
	{
		$excel = $this->send_laporan_tag_alls(1);
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=laporan_tagihan.xls");
		echo $excel;
	}


	function tampilkan_default()
	{

		global $con;
		$sesi = $_SESSION['id'];
		$awal = isset($_GET['a']) ? $_GET['a'] : date('Y-m-d') . " 00:00:00";
		$akhir = isset($_GET['b']) ? $_GET['b'] : date('Y-m-d') . " 23:23:59";
		$c = isset($_GET['c']) ? $_GET['c'] : "";
		$d = isset($_GET['d']) ? $_GET['d'] : "";
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$id_kelas = isset($_GET['id_kelas']) ? $_GET['id_kelas'] : "";
		$metode = isset($_GET['metode']) ? $_GET['metode'] : "";
		$a = $awal;
		$b = $akhir;

		if ($c != '') {
			$arr = explode(",", $c);
			$arr = implode("','", $arr);
			$arr = "'" . $arr . "'";
			$user = " AND (transaksi_siswa.office NOT IN ('teller','Uang Saku') OR transaksi_siswa.id_user IN($arr))";
		} else {
			$user = "";
		}
		if ($id_kelas != 0) {
			$kls = " AND transaksi_siswa.id_kelas IN($id_kelas)";
		} else {
			$kls = "";
		}

		if ($d != 0) {
			$trans = " AND transaksi_siswa.id_jenis_transaksi IN($d)";
		} else {
			$trans = "";
		}

		if ($metode != '') {
			$arrs = explode(",", $metode);
			$arrs = implode("','", $arrs);
			$arrs = "'" . $arrs . "'";

			if ($arrs == "'Bank BRI'") {
				$arrs = "'teller'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket = 'Bank BRI'";
			} else if ($arrs == "'Bank BNI'") {
				$arrs = "'teller'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket = 'Bank BNI'";
			} else if ($arrs == "'Bank Mandiri'") {
				$arrs = "'teller'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket = 'Bank Mandiri'";
			} else if ($arrs == "'Bank BCA'") {
				$arrs = "'teller'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket = 'Bank BCA'";
			} else if ($arrs == "'teller'") {
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket IS NULL";
			} else if ($arrs == "'tabungan'") {
				$arrs = "'tabungan'";
				$meto = " AND transaksi_siswa.office IN($arrs) AND ket IS NULL";
			} else {
				$meto = " AND transaksi_siswa.office IN($arrs)";
			}
		} else {
			$meto = "";
		}

		if ($th != 0) {
			$tahun = "AND transaksi_siswa.thn_aj = '$th'";
			$jenjang = "AND data_siswa.id_jenjang = '$id_jenjang'";
		} else {
			$query_tahunajar = "SELECT
					id_tahunajar 
				FROM
					data_tahunajar 
				WHERE
					aktif = '1'
			";
			$exeQueryTahunAjar = mysqli_query($con, $query_tahunajar);
			$idTahunAjar = "";
			while ($dt = mysqli_fetch_array($exeQueryTahunAjar)) {
				$idTahunAjar = $dt['id_tahunajar'];
			}

			$query_jenjang = "
				SELECT
					id_jenjang 
				FROM
					data_jenjang
				WHERE
					beranda = '1'
			";

			$exeQueryjenjang = mysqli_query($con, $query_jenjang);
			$jenjen = "";
			while ($dt = mysqli_fetch_array($exeQueryjenjang)) {
				$jenjen = $dt['id_jenjang'];
			}

			$tahun = "AND transaksi_siswa.thn_aj = '$idTahunAjar'";
			$jenjang = "AND data_siswa.id_jenjang = '$jenjen'";
			// $jenjang = "";
		}

		$sqls = "SELECT
						data_tahunajar.nama_tahunajar,
						transaksi_siswa.office,
						transaksi_siswa.infaq,
						users_log.nama_us,
						users_log.id,
						penempatan_siswa.nis,
						transaksi_siswa.tgl,
						master_jenis_transaksi.nama_transaksi,
						data_siswa.nama_siswa,
						data_siswa.id_jenjang,
						data_kelas.id_kelas,
						data_kelas.nama_kelas,
						transaksi_siswa.nominal_dibayarkan,
						transaksi_siswa.ket,
						master_alokasi_transaksi_all.nom_share 
					FROM
						transaksi_siswa
						LEFT JOIN users_log ON users_log.id = transaksi_siswa.id_user
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
						INNER JOIN data_kelas ON data_kelas.id_kelas = transaksi_siswa.id_kelas
						INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
						INNER JOIN penempatan_siswa ON penempatan_siswa.id_kelas = transaksi_siswa.id_kelas
						INNER JOIN data_tahunajar ON data_tahunajar.id_tahunajar = transaksi_siswa.thn_aj
						INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
					WHERE
						DATE( transaksi_siswa.tgl ) BETWEEN '$a' 
						AND '$b' 
						$tahun $kls $user $trans $meto $jenjang 
					GROUP BY
						transaksi_siswa.id_transaksi";

		$result = mysqli_query($con, $sqls);
		echo "<button onclick='cethar();' style='border-radius:3px;border:0px;padding:5px;margin-top:20px;margin-bottom:10px;'><img src='images/logo/excel.png' style='width:15px;height:15px;'> <span style='font-size:11px;'>Download excel</span></button>";
		echo "<button onclick='laporan_harian_list();' style='border-radius:3px;border:0px;padding:5px;margin-left:10px;margin-top:20px;margin-bottom:10px;'> <span style='font-size:11px;'>Tampilkan list</span></button>";
		echo "<button onclick='laporan_harian_table();' style='border-radius:3px;border:0px;padding:5px;margin-left:10px;margin-top:20px;margin-bottom:10px;'> <span style='font-size:11px;'>Tampilkan tabel</span></button><br>";

		echo '	

							<table class="table table-bordered table-md" width="100%" style="font-size:14px;">
							<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
								
								

												<thead style="background:#F8FAFC;">

					<tr>
						<th class="fw-semibold text-dark border-0">#</th>
						<th class="fw-semibold text-dark border-0">Periode</th>
						<th class="fw-semibold text-dark border-0">Tanggal</th>
						<th class="fw-semibold text-dark border-0">Nama Warga</th>
						<th class="fw-semibold text-dark border-0">Wilayah</th>
						<th class="fw-semibold text-dark border-0">Petugas</th>
						<th class="fw-semibold text-dark border-0">Nama Iuran</th>
						<th class="fw-semibold text-dark border-0 text-end">Nominal</th>
					</tr>

				</thead>';

		$no = 0;
		$total = 0;
		$totalshare = 0;
		while ($cari = mysqli_fetch_array($result)) {
			$no++;
			$date = $cari['tgl'];
			$tgl = date("d-m-Y", strtotime($date));

			echo '	<tbody>';
			echo '<tr>
														<td>' . $no . '</td>
														<td>' . $cari['nama_tahunajar'] . '</td>
														<td>' . $tgl . '</td>
														<td><div class="fw-semibold">' . $cari['nama_siswa'] . '</div></td>
														<td>' . $cari['nama_kelas'] . '</td>';

			if ($cari['ket'] == 'Bank BRI') {
				echo '<td>' . $cari['nama_us'] . ' (' . $cari['office'] . ' | Bank BRI)</td>';
			} else if ($cari['ket'] == 'Bank BNI') {
				echo '<td>' . $cari['nama_us'] . ' (' . $cari['office'] . ' | Bank BNI)</td>';
			} else if ($cari['ket'] == 'Bank Mandiri') {
				echo '<td>' . $cari['nama_us'] . ' (' . $cari['office'] . ' | Bank Mandiri)</td>';
			} else if ($cari['ket'] == 'Bank BCA') {
				echo '<td>' . $cari['nama_us'] . ' (' . $cari['office'] . ' | Bank BCA)</td>';
			} else {
				echo '<td><span class="badge bg-light text-dark border">' . $cari['nama_us'] . '</span></td>';
			}

			echo ' <td>' . $cari['nama_transaksi'] . '</td>';
			if ($cari['infaq'] == 0) {
				echo '<td style="text-align:right;" class="text-end fw-semibold">' . number_format($cari['nominal_dibayarkan'], 0, ".", ".") . '</td>';
				$jml = $cari['nominal_dibayarkan'];
			} else {
				echo '<td style="text-align:right;">' . number_format($cari['nominal_dibayarkan'], 0, ".", ".") . ' dan Donasi ' . number_format($cari['infaq'], 0, ".", ".") . '</td>';
				$jml = $cari['nominal_dibayarkan'] + $cari['infaq'];
			}
			echo '</tr>';
			$total = $total + $jml;


			// $totalshare+=$cari['nom_share'];

		}
		echo '
		<tr style="background:#F8FAFC;">

						<td colspan="7" class="text-end fw-bold fs-6">
							Total Pembayaran
						</td>

						<td class="text-end fw-bold text-primary fs-5">
							Rp ' . number_format($total, 0, ".", ".") . '
						</td>

					</tr>';
		echo '<td width="200px" style="font-weight:bold;text-align:right;">Rp ' . number_format($total, 0, ".", ".") . '</td>
														</tr>';

		echo '</tbody>';

		echo '
								</div>
							<table>
							';
	}





	public function js($param1)
	{
		$this->get_js("laporan/js/" . $param1);
	}
}
