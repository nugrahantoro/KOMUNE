<?php
class kas extends Controller
{
	var $content;
	public function index($param1 = "", $param2 = "")
	{

		$data["title"] = "Laporan Kas";
		$data["href"] = "";
		$this->view("main/view/header", $data);
		$this->view("kas/view/v_kas");
		$this->view("main/view/footer");
	}

	public function pengeluaran_kas($param1 = "", $param2 = "")
	{

		$data["title"] = "Laporan Kas";
		$data["href"] = "";
		$this->view("main/view/header", $data);
		$this->view("kas/view/v_pengeluaran_kas");
		$this->view("main/view/footer");
	}

	public function ex($param1, $params2)
	{
		$ex_controller = '';
		if (file_exists("app/module/kas/controller/$param1.php")) {
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


	function send_th()
	{
		global $con;
		$querykls = "SELECT * FROM data_tahunajar ORDER BY aktif DESC, nama_tahunajar DESC";
		$result = mysqli_query($con, $querykls);
		echo "<select id='pilih_tahun' class= 'btn btn-light btn-lg' style='font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;' onChange='myNewFunction(this);'>";
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="' . $dt['id_tahunajar'] . '">' . $dt['nama_tahunajar'] . '</option>';
		}
	}
	function send_jenjang()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$query = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
					INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'";
		$resultjen = mysqli_query($con, $query);
		// var_dump($query);
		echo "<select id='pilih_jenjang' class= 'btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;' onChange='myNewFunctionjang(this);'>";
		while ($dt = mysqli_fetch_array($resultjen)) {
			echo '<option value="' . $dt['id_jenjang'] . '">' . $dt['nama_jenjang'] . '</option>';
		}
		echo '</select>';
	}

	function send_laporan_default()
	{
		global $con;
		// th def yafa
		$th_aktif = "SELECT id_tahunajar FROM data_tahunajar WHERE aktif = '1'";
		$resulth = mysqli_query($con, $th_aktif);
		$th = mysqli_fetch_array($resulth);
		$th = $th['id_tahunajar'];

		// jenjang def yafa
		$sesi = $_SESSION['id'];
		$jenjang_aktif = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
					INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi' LIMIT 1";
		$resultjenjang = mysqli_query($con, $jenjang_aktif);
		$id_jenjang = mysqli_fetch_array($resultjenjang);
		$id_jenjang = $id_jenjang['id_jenjang'];
		$sqlone = "SELECT
				nama_transaksi,
				SUM( nominal ) AS estimasi 
			FROM
				master_jenis_transaksi
				LEFT JOIN master_alokasi_transaksi_all ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi 
			WHERE
				master_jenis_transaksi.id_sesi ='$th' AND master_jenis_transaksi.id_jenjang='$id_jenjang'
			GROUP BY
				master_jenis_transaksi.id_jenis_transaksi 
			ORDER BY
				master_jenis_transaksi.id_jenis_transaksi";
		// var_dump($sqlone);
		$exce = mysqli_query($con, $sqlone);

		echo '
	<!-- ESTIMASI -->
              <div class="col-xl-6">
                <!-- card -->
                <div class="card">
                  <!-- HEAD -->
                  <div class="card-header">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                      <h5 class="card-title me-2">ESTIMASI</h5>
                      <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-light waves-effect waves-light" onclick="cetak_estimasi();" style="border:1px;padding:5px;margin:4px;">
						  	<img src="images/logo/excel.png" style="width:15px;height:15px;">
							Download excel
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- YAFA -->
					
                    <div class="table-responsive">
                      <table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama Trannsaksi</th>
                            <th>Estimasi</th>
                          </tr>
                        </thead>
						<tbody>
				';
		$no = 1;
		$total = 0;
		while ($rs = mysqli_fetch_array($exce)) {
			echo '
                          <tr>
                            <th scope="row">' . $no++ . '</th>
                            <td>' . $rs['nama_transaksi'] . '</td>
                            <td>' . number_format($rs['estimasi'], "0", "", ".") . '</td>
                          </tr>
				';
			$total += $rs['estimasi'];
		}

		echo '
						<tr>
                            <th scope="row">#</th>
                            <td>Total</td>
                            <td>' . number_format($total, "0", "", ".") . '</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
	';
		$sqltwo = "SELECT
						nama_transaksi,
						IFNULL(SUM( nominal_dibayarkan ),0) AS pendapatan 
					FROM
						master_jenis_transaksi
						LEFT JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi 
					WHERE
						master_jenis_transaksi.id_sesi ='$th' AND master_jenis_transaksi.id_jenjang='$id_jenjang'
					GROUP BY
						master_jenis_transaksi.id_jenis_transaksi 
					ORDER BY
						master_jenis_transaksi.id_jenis_transaksi";
		$excetwo = mysqli_query($con, $sqltwo);
		echo '
		<!-- PENDAPATAN -->
              <div class="col-xl-6">
                <!-- card -->
                <div class="card">
                  <!-- HEAD -->
                  <div class="card-header">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                      <h5 class="card-title me-2">PENDAPATAN</h5>
                      <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-light waves-effect waves-light" onclick="cetak_pendapatan();" style="border:1px;padding:5px;margin:4px;">
						  	<img src="images/logo/excel.png" style="width:15px;height:15px;">
							Download excel
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- BODY -->
                    <div class="table-responsive">
                      <table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama Trannsaksi</th>
                            <th>Pendapatan</th>
                          </tr>
                        </thead>
						<tbody>
			';
		$no = 1;
		$total = 0;
		while ($rs = mysqli_fetch_array($excetwo)) {
			echo '
                          <tr>
                            <th scope="row">' . $no++ . '</th>
                            <td>' . $rs['nama_transaksi'] . '</td>
                            <td>' . number_format($rs['pendapatan'], "0", "", ".") . '</td>
                          </tr>

					';
			$total += $rs['pendapatan'];
		}
		echo '
						<tr>
                            <th scope="row">#</th>
                            <td>Total</td>
                            <td>' . number_format($total, "0", "", ".") . '</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
		';
		$sqlone = "SELECT
						* 
					FROM
						(
						SELECT
							master_jenis_transaksi.id_kategori_pengeluaran as a,
							kategori_pengeluaran.nama_kategori_pengeluaran,
							IFNULL( SUM( nominal ), 0 ) AS estimasi 
						FROM
							master_jenis_transaksi
							LEFT JOIN master_alokasi_transaksi_all ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
							LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran 
						WHERE
							master_jenis_transaksi.id_sesi = '$th' 
							AND master_jenis_transaksi.id_jenjang = '$id_jenjang' 
						GROUP BY
							a 
						ORDER BY
							master_jenis_transaksi.id_jenis_transaksi 
						) AS tbl_ketetapan
						LEFT JOIN (
						SELECT
							master_jenis_transaksi.id_kategori_pengeluaran as b,
							kategori_pengeluaran.id_kategori_pengeluaran,
							IFNULL( SUM( nominal_dibayarkan ), 0 ) AS pendapatan 
						FROM
							master_jenis_transaksi
							LEFT JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
							LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran 
						WHERE
							master_jenis_transaksi.id_sesi = '$th' 
							AND master_jenis_transaksi.id_jenjang = '$id_jenjang' 
						GROUP BY
							b 
						ORDER BY
						master_jenis_transaksi.id_jenis_transaksi 
						) AS tbl_bayar ON tbl_bayar.b = tbl_ketetapan.a";
		$exce = mysqli_query($con, $sqlone);
		echo '
		 <!-- SALDO -->
              <div class="col-xl-3">
                <!-- card -->
                <div class="card">
                  <!-- HEAD -->
                  <div class="card-header">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                      <h5 class="card-title me-2">SALDO</h5>
                      <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-light waves-effect waves-light" onclick="cetak_saldo();" style="border:1px;padding:5px;margin:4px;">
						  	<img src="images/logo/excel.png" style="width:15px;height:15px;">
							Download excel
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- BODY -->
                    <div class="table-responsive">
                      
					  ';
		$saldo = 0;
		while ($rs = mysqli_fetch_array($exce)) {
			$sql = "SELECT SUM(nominal_pengeluaran) FROM pengeluaran_kas WHERE id_kategori_pengeluaran='$rs[id_kategori_pengeluaran]' AND id_sesi=$th";
			$ekse = mysqli_query($con, $sql);
			$hsl = mysqli_fetch_row($ekse);

			echo '
					<table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>' . $rs['nama_kategori_pengeluaran'] . '</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Estimasi</td>
                            <td>' . number_format($rs['estimasi'], "0", "", ".") . '</td>
                          </tr>
                          <tr>
                            <td>Pendapatans</td>
                            <td>' . number_format($rs['pendapatan'], "0", "", ".") . '</td>
                          </tr>
                          <tr>
                            <td>Pengeluaran</td>
                            <td>' . number_format($hsl[0], "0", "", ".") . '</td>
                          </tr>
                          <tr>
                            <td>Saldo</td>
                            <td>' . number_format($rs['pendapatan'] - $hsl[0], "0", "", ".") . '</td>
                          </tr>
						  </tbody>
                    </table>
						';
			$total += $rs['estimasi'];
			$saldo += $rs['pendapatan'] - $hsl[0];
		}
		echo '        
                      <!-- TOTAL SALDO -->
                      <table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>Total Saldo</th>
                            <th>' . number_format($saldo, "0", "", ".") . '</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <!-- card body -->
                </div>
              </div>
		';
		$querykls = "SELECT
						* 
					FROM
						pengeluaran_kas
						LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = pengeluaran_kas.id_kategori_pengeluaran
						LEFT JOIN jenis_pengeluaran ON jenis_pengeluaran.id_jenis_pengeluaran = pengeluaran_kas.id_jenis_pengeluaran 
					WHERE
						pengeluaran_kas.id_sesi = '$th' AND pengeluaran_kas.id_jenjang='$id_jenjang'
					ORDER BY
						id_pengeluaran ASC";
		$result = mysqli_query($con, $querykls);
		echo '
		<!-- PENGELUARAN -->
              <div class="col-xl-9">
                <div class="card">
                  <!-- HEAD -->
                  <div class="card-header">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                      <h5 class="card-title me-2">PENGELUARAN</h5>
                      <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-light waves-effect waves-light" onclick="cetak_pengeluaran();" style="border:1px;padding:5px;margin:4px;">
						  	<img src="images/logo/excel.png" style="width:15px;height:15px;">
							Download excel
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- BODY -->
                    <div class="table-responsive">
                      <table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pengambilan Dari</th>
                            <th>Jenis Pengeluaran</th>
                            <th>Nama Pengeluaran</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                          </tr>
                        </thead>
                        <tbody>
					';
		$no = 0;
		$total = 0;
		while ($dt = mysqli_fetch_array($result)) {
			$date = $dt['tanggal'];
			$tgl = date("d-m-Y", strtotime($date));
			$no++;
			echo '
                          <tr>
                            <th scope="row">' . $no . '</th>
                           	<td>' . $tgl . '</td>
							<td>' . $dt['nama_kategori_pengeluaran'] . '</td>
							<td>' . $dt['nama_jns_pengeluaran'] . '</td>
							<td>' . $dt['nama_pengeluaran'] . '</td>
							<td>' . $dt['deskripsi_pengeluaran'] . '</td>
                            <td>' . number_format((float)$dt['nominal_pengeluaran'], "0", "", ".") . '</td>
                          </tr>';
			$total = $total + $dt['nominal_pengeluaran'];
		}
		echo
		'
 						<tr>
                            <th scope="row">#</th>
                           	<td>Total</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
                            <td>' . number_format($total, "0", "", ".") . '</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
		
		';
	}

	function send_laporan()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		$id_jenjang = isset($_GET['id_jenjang']) ? $_GET['id_jenjang'] : "";
		$sqlone = "SELECT
				nama_transaksi,
				SUM( nominal ) AS estimasi 
			FROM
				master_jenis_transaksi
				LEFT JOIN master_alokasi_transaksi_all ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi 
			WHERE
				master_jenis_transaksi.id_sesi ='$th' AND master_jenis_transaksi.id_jenjang='$id_jenjang'
			GROUP BY
				master_jenis_transaksi.id_jenis_transaksi 
			ORDER BY
				master_jenis_transaksi.id_jenis_transaksi";
		$exce = mysqli_query($con, $sqlone);

		echo '
	<!-- ESTIMASI -->
              <div class="col-xl-6">
                <!-- card -->
                <div class="card">
                  <!-- HEAD -->
                  <div class="card-header">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                      <h5 class="card-title me-2">ESTIMASI</h5>
                      <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-light waves-effect waves-light" onclick="cetak_estimasi();" style="border:1px;padding:5px;margin:4px;">
						  	<img src="images/logo/excel.png" style="width:15px;height:15px;">
							Download excel
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- YAFA -->
					
                    <div class="table-responsive">
                      <table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama Trannsaksi</th>
                            <th>Estimasi</th>
                          </tr>
                        </thead>
						<tbody>
				';
		$no = 1;
		$total = 0;
		while ($rs = mysqli_fetch_array($exce)) {
			echo '
                          <tr>
                            <th scope="row">' . $no++ . '</th>
                            <td>' . $rs['nama_transaksi'] . '</td>
                            <td>' . number_format($rs['estimasi'], "0", "", ".") . '</td>
                          </tr>
				';
			$total += $rs['estimasi'];
		}

		echo '
						<tr>
                            <th scope="row">#</th>
                            <td>Total</td>
                            <td>' . number_format($total, "0", "", ".") . '</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
	';
		$sqltwo = "SELECT
						nama_transaksi,
						IFNULL(SUM( nominal_dibayarkan ),0) AS pendapatan 
					FROM
						master_jenis_transaksi
						LEFT JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi 
					WHERE
						master_jenis_transaksi.id_sesi ='$th' AND master_jenis_transaksi.id_jenjang='$id_jenjang'
					GROUP BY
						master_jenis_transaksi.id_jenis_transaksi 
					ORDER BY
						master_jenis_transaksi.id_jenis_transaksi";
		$excetwo = mysqli_query($con, $sqltwo);
		echo '
		<!-- PENDAPATAN -->
              <div class="col-xl-6">
                <!-- card -->
                <div class="card">
                  <!-- HEAD -->
                  <div class="card-header">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                      <h5 class="card-title me-2">PENDAPATAN</h5>
                      <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-light waves-effect waves-light" onclick="cetak_pendapatan();" style="border:1px;padding:5px;margin:4px;">
						  	<img src="images/logo/excel.png" style="width:15px;height:15px;">
							Download excel
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- BODY -->
                    <div class="table-responsive">
                      <table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Nama Trannsaksi</th>
                            <th>Pendapatan</th>
                          </tr>
                        </thead>
						<tbody>
			';
		$no = 1;
		$total = 0;
		while ($rs = mysqli_fetch_array($excetwo)) {
			echo '
                          <tr>
                            <th scope="row">' . $no++ . '</th>
                            <td>' . $rs['nama_transaksi'] . '</td>
                            <td>' . number_format($rs['pendapatan'], "0", "", ".") . '</td>
                          </tr>

					';
			$total += $rs['pendapatan'];
		}
		echo '
						<tr>
                            <th scope="row">#</th>
                            <td>Total</td>
                            <td>' . number_format($total, "0", "", ".") . '</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
		';
		$sqlone = "SELECT
						* 
					FROM
						(
						SELECT
							master_jenis_transaksi.id_kategori_pengeluaran as a,
							kategori_pengeluaran.nama_kategori_pengeluaran,
							IFNULL( SUM( nominal ), 0 ) AS estimasi 
						FROM
							master_jenis_transaksi
							LEFT JOIN master_alokasi_transaksi_all ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
							LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran 
						WHERE
							master_jenis_transaksi.id_sesi = '$th' 
							AND master_jenis_transaksi.id_jenjang = '$id_jenjang' 
						GROUP BY
							a 
						ORDER BY
							master_jenis_transaksi.id_jenis_transaksi 
						) AS tbl_ketetapan
						LEFT JOIN (
						SELECT
							master_jenis_transaksi.id_kategori_pengeluaran as b,
							kategori_pengeluaran.id_kategori_pengeluaran,
							IFNULL( SUM( nominal_dibayarkan ), 0 ) AS pendapatan 
						FROM
							master_jenis_transaksi
							LEFT JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
							LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran 
						WHERE
							master_jenis_transaksi.id_sesi = '$th' 
							AND master_jenis_transaksi.id_jenjang = '$id_jenjang' 
						GROUP BY
							b 
						ORDER BY
						master_jenis_transaksi.id_jenis_transaksi 
						) AS tbl_bayar ON tbl_bayar.b = tbl_ketetapan.a";
		$exce = mysqli_query($con, $sqlone);
		echo '
		 <!-- SALDO -->
              <div class="col-xl-3">
                <!-- card -->
                <div class="card">
                  <!-- HEAD -->
                  <div class="card-header">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                      <h5 class="card-title me-2">SALDO</h5>
                      <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-light waves-effect waves-light" onclick="cetak_saldo();" style="border:1px;padding:5px;margin:4px;">
						  	<img src="images/logo/excel.png" style="width:15px;height:15px;">
							Download excel
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- BODY -->
                    <div class="table-responsive">
                      
					  ';
		$saldo = 0;
		while ($rs = mysqli_fetch_array($exce)) {
			$sql = "SELECT SUM(nominal_pengeluaran) FROM pengeluaran_kas WHERE id_kategori_pengeluaran='$rs[id_kategori_pengeluaran]' AND id_sesi=$th";
			$ekse = mysqli_query($con, $sql);
			$hsl = mysqli_fetch_row($ekse);

			echo '
					<table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>' . $rs['nama_kategori_pengeluaran'] . '</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Estimasi</td>
                            <td>' . number_format($rs['estimasi'], "0", "", ".") . '</td>
                          </tr>
                          <tr>
                            <td>Pendapatans</td>
                            <td>' . number_format($rs['pendapatan'], "0", "", ".") . '</td>
                          </tr>
                          <tr>
                            <td>Pengeluaran</td>
                            <td>' . number_format($hsl[0], "0", "", ".") . '</td>
                          </tr>
                          <tr>
                            <td>Saldo</td>
                            <td>' . number_format($rs['pendapatan'] - $hsl[0], "0", "", ".") . '</td>
                          </tr>
						  </tbody>
                    </table>
						';
			$total += $rs['estimasi'];
			$saldo += $rs['pendapatan'] - $hsl[0];
		}
		echo '        
                      <!-- TOTAL SALDO -->
                      <table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>Total Saldo</th>
                            <th>' . number_format($saldo, "0", "", ".") . '</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <!-- card body -->
                </div>
              </div>
		';
		$querykls = "SELECT
						* 
					FROM
						pengeluaran_kas
						LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = pengeluaran_kas.id_kategori_pengeluaran
						LEFT JOIN jenis_pengeluaran ON jenis_pengeluaran.id_jenis_pengeluaran = pengeluaran_kas.id_jenis_pengeluaran 
					WHERE
						pengeluaran_kas.id_sesi = '$th' AND pengeluaran_kas.id_jenjang='$id_jenjang'
					ORDER BY
						id_pengeluaran ASC";
		$result = mysqli_query($con, $querykls);
		echo '
		<!-- PENGELUARAN -->
              <div class="col-xl-9">
                <div class="card">
                  <!-- HEAD -->
                  <div class="card-header">
                    <div class="d-flex flex-wrap align-items-center mb-4">
                      <h5 class="card-title me-2">PENGELUARAN</h5>
                      <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-light waves-effect waves-light" onclick="cetak_pengeluaran();" style="border:1px;padding:5px;margin:4px;">
						  	<img src="images/logo/excel.png" style="width:15px;height:15px;">
							Download excel
                          </button>
                        </div>
                      </div>
                    </div>

                    <!-- BODY -->
                    <div class="table-responsive">
                      <table class="table table-sm m-0">
                        <thead>
                          <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pengambilan Dari</th>
                            <th>Jenis Pengeluaran</th>
                            <th>Nama Pengeluaran</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                          </tr>
                        </thead>
                        <tbody>
					';
		$no = 0;
		$total = 0;
		while ($dt = mysqli_fetch_array($result)) {
			$date = $dt['tanggal'];
			$tgl = date("d-m-Y", strtotime($date));
			$no++;
			echo '
                          <tr>
                            <th scope="row">' . $no . '</th>
                           	<td>' . $tgl . '</td>
							<td>' . $dt['nama_kategori_pengeluaran'] . '</td>
							<td>' . $dt['nama_jns_pengeluaran'] . '</td>
							<td>' . $dt['nama_pengeluaran'] . '</td>
							<td>' . $dt['deskripsi_pengeluaran'] . '</td>
                            <td>' . number_format((float)$dt['nominal_pengeluaran'], "0", "", ".") . '</td>
                          </tr>';
			$total = $total + $dt['nominal_pengeluaran'];
		}
		echo
		'
 						<tr>
                            <th scope="row">#</th>
                           	<td>Total</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
                            <td>' . number_format($total, "0", "", ".") . '</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
		
		';
	}

	function cetak_estimasi()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=ESTIMASI.xls");
		$sqlone = "SELECT
	nama_transaksi,
	SUM( nominal ) AS estimasi 
FROM
	master_jenis_transaksi
	LEFT JOIN master_alokasi_transaksi_all ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi 
WHERE
	master_jenis_transaksi.id_sesi = $th 
GROUP BY
	master_jenis_transaksi.id_jenis_transaksi 
ORDER BY
	master_jenis_transaksi.id_jenis_transaksi";
		$exce = mysqli_query($con, $sqlone);

		echo '
	ESTIMASI<br>
	<table width="100%" id="tab" border="1">
		<tr>
			<th>No</th>
			<th>Nama Transaksi</th>
			<th>Estimasi</th>
		</tr>
	';
		$no = 1;
		$total = 0;
		while ($rs = mysqli_fetch_array($exce)) {
			echo '
			<tr>
				<td>' . $no++ . '</td>
				<td>' . $rs['nama_transaksi'] . '</td>
				<td style="text-align:right;">' . ($rs['estimasi']) . '</td>
			</tr>
		';
			$total += $rs['estimasi'];
		}

		echo '
	<tr>
		<td colspan="2">Total</td>
		<td style="text-align:right;">' . ($total) . '</td>
	</tr>
	</table>';
	}
	function cetak_pendapatan()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=PENDAPATAN.xls");
		$sqltwo = "SELECT
		nama_transaksi,
		IFNULL(SUM( nominal_dibayarkan ),0) AS pendapatan 
	FROM
		master_jenis_transaksi
		LEFT JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi 
	WHERE
		master_jenis_transaksi.id_sesi = $th
	GROUP BY
		master_jenis_transaksi.id_jenis_transaksi 
	ORDER BY
		master_jenis_transaksi.id_jenis_transaksi";
		$excetwo = mysqli_query($con, $sqltwo);

		echo '
	PENDAPATAN<br>
	<table width="100%" id="tab" border="1">
		<tr>
			<th>No</th>
			<th>Nama Transaksi</th>
			<th>Pendapatan</th>
		</tr>
	';
		$no = 1;
		$total = 0;
		while ($rs = mysqli_fetch_array($excetwo)) {
			echo '
			<tr>
				<td>' . $no++ . '</td>
				<td>' . $rs['nama_transaksi'] . '</td>
				<td style="text-align:right;">' . ($rs['pendapatan']) . '</td>
			</tr>
		';
			$total += $rs['pendapatan'];
		}

		echo '
	<tr>
		<td colspan="2">Total</td>
		<td style="text-align:right;">' . ($total) . '</td>
	</tr>
	</table>';
	}

	function cetak_pengeluaran()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=PENGELUARAN.xls");
		$querykls = "SELECT
					* 
				FROM
					pengeluaran_kas 
					LEFT JOIN kategori_laporan ON kategori_laporan.id_kat_laporan=pengeluaran_kas.id_kategori
					LEFT JOIN jenis_pengeluaran ON jenis_pengeluaran.id_jenis_pengeluaran=pengeluaran_kas.id_jns
				WHERE pengeluaran_kas.id_sesi= $th
				ORDER BY
					id_pengeluaran ASC";
		$result = mysqli_query($con, $querykls);

		echo '
	PENGELUARAN<br>
		<table width="100%;" id="tab" border="1">
		<tr>
		<th >N0</th>
		<th >Tanggal</th>
		<th >Pengambilan Dari</th>
		<th >Jenis Pengeluaran</th>
		<th >Nama pengeluaran</th>
		<th >Deskripsi</th>
		<th >Nominal</th>
		</tr>';
		$no = 0;
		$total = 0;

		while ($dt = mysqli_fetch_array($result)) {
			$date = $dt['tanggal'];
			$tgl = date("d-m-Y", strtotime($date));
			$no++;
			echo '<tr>
		<td>' . $no . '</td>
		<td>' . $tgl . '</td>
		<td>' . $dt['nama_kategori_laporan'] . '</td>
		<td>' . $dt['nama_jns_pengeluaran'] . '</td>
		<td>' . $dt['nama_pengeluaran'] . '</td>
		<td>' . $dt['deskripsi_pengeluaran'] . '</td>
		<td style="text-align:right;">' . ($dt['nominal_pengeluaran']) . '</td>
		</tr>';
			$total = $total + $dt['nominal_pengeluaran'];
		}

		echo '<tr>
		<td colspan="6">Total</td>
		<td width="150px;" style="text-align:right;">' . ($total) . '</td>
		</tr></table>';
	}

	function cetak_saldo()
	{
		global $con;
		$th = isset($_GET['th']) ? $_GET['th'] : "";
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=SALDO.xls");
		$sqlone = "SELECT * FROM (SELECT
		master_jenis_transaksi.id_kategori_laporan,
		kategori_laporan.nama_kategori_laporan,
		IFNULL(SUM( nominal ),0) AS estimasi 
	FROM
		master_jenis_transaksi
		LEFT JOIN master_alokasi_transaksi_all ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi 
		LEFT JOIN kategori_laporan ON kategori_laporan.id_kat_laporan=master_jenis_transaksi.id_kategori_laporan
	WHERE
		master_jenis_transaksi.id_sesi = $th 
	GROUP BY
		master_jenis_transaksi.id_kategori_laporan 
	ORDER BY
		master_jenis_transaksi.id_jenis_transaksi) AS tbl_ketetapan LEFT JOIN (
	SELECT
		master_jenis_transaksi.id_kategori_laporan,
		kategori_laporan.id_kat_laporan,
		IFNULL(SUM( nominal_dibayarkan ),0) AS pendapatan 
	FROM
		master_jenis_transaksi
		LEFT JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi 
		LEFT JOIN kategori_laporan ON kategori_laporan.id_kat_laporan=master_jenis_transaksi.id_kategori_laporan
	WHERE
		master_jenis_transaksi.id_sesi = $th 
	GROUP BY
		master_jenis_transaksi.id_kategori_laporan
	ORDER BY
		master_jenis_transaksi.id_jenis_transaksi)
		AS tbl_bayar ON tbl_bayar.id_kategori_laporan=tbl_ketetapan.id_kategori_laporan";
		$exce = mysqli_query($con, $sqlone);

		echo '
	SALDO<br>
	<table width="100%" id="tab" border="1">
	';
		$saldo = 0;
		$total = 0;
		while ($rs = mysqli_fetch_array($exce)) {
			$sql = "SELECT SUM(nominal_pengeluaran) FROM pengeluaran_kas WHERE id_kategori='$rs[id_kat_laporan]' AND id_sesi=$th";
			$ekse = mysqli_query($con, $sql);
			$hsl = mysqli_fetch_row($ekse);

			echo '
			<tr>
				<th colspan="2">' . $rs['nama_kategori_laporan'] . '</th>
			</tr>
			<tr>
				<td>Estimasi</td>
				<td style="text-align:right;">' . ($rs['estimasi']) . '</td>
			</tr>
			<tr>
				<td>Pendapatan</td>
				<td style="text-align:right;">' . ($rs['pendapatan']) . '</td>
			</tr>
			<tr>
				<td>Pengeluaran</td>
				<td style="text-align:right;">' . ($hsl[0]) . '</td>
			</tr>
			<tr>
				<td>Saldo</td>
				<td  style="text-align:right;">' . ($rs['pendapatan'] - $hsl[0]) . '</td>
			</tr>
		';
			$total += $rs['estimasi'];
			$saldo += $rs['pendapatan'] - $hsl[0];
		}

		echo '
	<tr>
		<td><b>Total Saldo</b></td>
		<td style="text-align:right;">' . ($saldo) . '</td>
	</tr>
	</table>';
	}

	public function js($param1)
	{
		$this->get_js("kas/js/" . $param1);
	}
}
