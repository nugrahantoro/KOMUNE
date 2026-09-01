<?php
class laporan_uangsaku extends Controller
{
    var $content;
	

    public function index($param1 = "", $param2 = "")
	{
		
		$data["title"] = "Laporan Dompet Digital";
		$data["href"] = "";
		$this->view("main/view/header", $data);
		$this->view("laporan_uangsaku/view/v_laporan");
		$this->view("main/view/footer");
	}

    public function ex($param1, $params2)
	{
		$ex_controller = '';
		if (file_exists("app/module/laporan_uangsaku/controller/$param1.php")) {
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

	function laporan_masuk(){
		global $con;
		$a=isset($_GET['a'])? $_GET['a']:"";
		$b=isset($_GET['b'])? $_GET['b']:"";
		$sql=mysqli_query($con,"SELECT * FROM tbl_uangku 
		INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
		WHERE id_tbl_status_transaksi=1 OR id_tbl_status_transaksi = 3 AND DATE(tbl_uangku.tanggal) >= DATE('$a') AND DATE(tbl_uangku.tanggal) <= DATE('$b') ORDER BY tanggal DESC");
		echo '
		<table class="table table-bordered table-sm" width="100%" style="font-size:12px;">
			<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
			<thead>
				<tr class="table-secondary">
				<th>NO</th>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Keterangan</th>
				<th>Nominal</th>
				</tr>
			</thead>
			<tbody>';
		
		
		$no = 0;
		$total=0;
			while($cari = mysqli_fetch_array($sql)){
			$no ++;
			$tgl=date("d-m-Y",strtotime($cari['tanggal']));
			echo'<tr >
			<td>'.$no.'</td>
			<td>'.$tgl.'</td>
			<td>'.$cari['nama_siswa'].'</td>
			<td>'.$cari['keterangan'].'</td>
			<td style="text-align:right;">'.number_format($cari['jumlah']).'</td>
			</tr>';	
			$total=$total+$cari['jumlah'];
			}
		echo '<tr>
		<td colspan="4">Total</td>
		<td style="text-align:right;">'.number_format($total).'</td>
		</tr>';
		echo'
		</tbody>
		</table>';
	}

	function laporan_keluar(){
		global $con;
		$a=isset($_GET['a'])? $_GET['a']:"";
		$b=isset($_GET['b'])? $_GET['b']:"";
		$sql=mysqli_query($con,"SELECT * FROM tbl_uangku 
		INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
		WHERE id_tbl_status_transaksi=2 OR id_tbl_status_transaksi = 4 AND DATE(tbl_uangku.tanggal) >= DATE('$a') AND DATE(tbl_uangku.tanggal) <= DATE('$b') ORDER BY tanggal DESC");
		echo '
		<table class="table table-bordered table-sm" width="100%" style="font-size:12px;">
			<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
			<thead>
				<tr class="table-secondary">
				<th>NO</th>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Keterangan</th>
				<th>Nominal</th>
				</tr>
			</thead>
			<tbody>';
		$no = 0;
		$total=0;
			while($cari = mysqli_fetch_array($sql)){
			$no ++;
			$tgl=date("d-m-Y",strtotime($cari['tanggal']));
			echo'<tr >
			<td>'.$no.'</td>
			<td>'.$tgl.'</td>
			<td>'.$cari['nama_siswa'].'</td>
			<td>'.$cari['keterangan'].'</td>
			<td style="text-align:right;">'.number_format($cari['jumlah']).'</td>
			</tr>';	
			$total=$total+$cari['jumlah'];
			}
		echo '<tr>
		<td colspan="4">Total</td>
		<td style="text-align:right;">'.number_format($total).'</td>
		</tr>';
		echo'
		</tbody>
		</table>';
	}

	function ex_laporan_masuk(){
		global $con;
		$a=isset($_GET['a'])? $_GET['a']:"";
		$b=isset($_GET['b'])? $_GET['b']:"";
		
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=laporan_uangsaku_masuk.xls");
		$sql=mysqli_query($con,"SELECT * FROM tbl_uangku 
		INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
		WHERE id_tbl_status_transaksi=1 OR id_tbl_status_transaksi = 3 AND DATE(tbl_uangku.tanggal) >= DATE('$a') AND DATE(tbl_uangku.tanggal) <= DATE('$b') ORDER BY tanggal ASC");
		echo '
		<table class="table table-bordered table-sm" width="100%" style="font-size:12px;">
			<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
			<thead>
				<tr class="table-secondary">
				<th>NO</th>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Keterangan</th>
				<th>Nominal</th>
				</tr>
			</thead>
			<tbody>';
		
		
		$no = 0;
		$total=0;
			while($cari = mysqli_fetch_array($sql)){
			$no ++;
			$tgl=date("d-m-Y",strtotime($cari['tanggal']));
			echo'<tr >
			<td>'.$no.'</td>
			<td>'.$tgl.'</td>
			<td>'.$cari['nama_siswa'].'</td>
			<td>'.$cari['keterangan'].'</td>
			<td style="text-align:right;">'.number_format($cari['jumlah']).'</td>
			</tr>';	
			$total=$total+$cari['jumlah'];
			}
		echo '<tr>
		<td colspan="4">Total</td>
		<td style="text-align:right;">'.number_format($total).'</td>
		</tr>';
		echo'
		</tbody>
		</table>';

	}

	function ex_laporan_keluar(){
		global $con;
		$a=isset($_GET['a'])? $_GET['a']:"";
		$b=isset($_GET['b'])? $_GET['b']:"";
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=laporan_uangsaku_keluar.xls");
		$sql=mysqli_query($con,"SELECT * FROM tbl_uangku 
		INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
		WHERE id_tbl_status_transaksi=2 OR id_tbl_status_transaksi = 4 AND DATE(tbl_uangku.tanggal) >= DATE('$a') AND DATE(tbl_uangku.tanggal) <= DATE('$b') ORDER BY tanggal ASC");
		echo '
		<table class="table table-bordered table-sm" width="100%" style="font-size:12px;">
			<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
			<thead>
				<tr class="table-secondary">
				<th>NO</th>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Keterangan</th>
				<th>Nominal</th>
				</tr>
			</thead>
			<tbody>';
		$no = 0;
		$total=0;
			while($cari = mysqli_fetch_array($sql)){
			$no ++;
			$tgl=date("d-m-Y",strtotime($cari['tanggal']));
			echo'<tr >
			<td>'.$no.'</td>
			<td>'.$tgl.'</td>
			<td>'.$cari['nama_siswa'].'</td>
			<td>'.$cari['keterangan'].'</td>
			<td style="text-align:right;">'.number_format($cari['jumlah']).'</td>
			</tr>';	
			$total=$total+$cari['jumlah'];
			}
		echo '<tr>
		<td colspan="4">Total</td>
		<td style="text-align:right;">'.number_format($total).'</td>
		</tr>';
		echo'
		</tbody>
		</table>';

	}

	function laporan_default_masuk(){
		global $con;
		$a=isset($_GET['a'])? $_GET['a']: date('Y-m-d') . " 00:00:00";
		$b=isset($_GET['b'])? $_GET['b']:date('Y-m-d') . " 23:23:59";
		
		$sql=mysqli_query($con,"SELECT * FROM tbl_uangku 
		INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
		WHERE DATE(tbl_uangku.tanggal) >= DATE('$a') AND DATE(tbl_uangku.tanggal) <= DATE('$b') ORDER BY tanggal ASC");
		
		// id_tbl_status_transaksi=1 OR id_tbl_status_transaksi = 3

		echo '
		<table class="table table-bordered table-sm" width="100%" style="font-size:12px;">
			<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
			<thead>
				<tr class="table-secondary">
				<th>NO</th>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Keterangan</th>
				<th>Nominal</th>
				</tr>
			</thead>
			<tbody>';
		
		
		$no = 0;
		$total=0;
			while($cari = mysqli_fetch_array($sql)){

			if($cari['id_tbl_status_transaksi'] == '1' || $cari['id_tbl_status_transaksi'] == '3' ){
					$no ++;
					$tgl=date("d-m-Y",strtotime($cari['tanggal']));
					echo'<tr >
					<td>'.$no.'</td>
					<td>'.$tgl.'</td>
					<td>'.$cari['nama_siswa'].'</td>
					<td>'.$cari['keterangan'].'</td>
					<td style="text-align:right;">'.number_format($cari['jumlah']).'</td>
					</tr>';	
					$total=$total+$cari['jumlah'];
			}
			
			}
		echo '<tr>
		<td colspan="4">Total</td>
		<td style="text-align:right;">'.number_format($total).'</td>
		</tr>';
		echo'
		</tbody>
		</table>';
	}

	
	function laporan_default_keluar(){
		global $con;
		$a=isset($_GET['a'])? $_GET['a']: date('Y-m-d') . " 00:00:00";
		$b=isset($_GET['b'])? $_GET['b']:date('Y-m-d') . " 23:23:59";
		$sql=mysqli_query($con,"SELECT * FROM tbl_uangku 
		INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
		WHERE DATE(tbl_uangku.tanggal) >= DATE('$a') AND DATE(tbl_uangku.tanggal) <= DATE('$b') ORDER BY tanggal ASC");
		echo '
		<table class="table table-bordered table-sm" width="100%" style="font-size:12px;">
			<div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">
			<thead>
				<tr class="table-secondary">
				<th>NO</th>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Keterangan</th>
				<th>Nominal</th>
				</tr>
			</thead>
			<tbody>';
		$no = 0;
		$total=0;
			while($cari = mysqli_fetch_array($sql)){

			if($cari['id_tbl_status_transaksi'] == '2' || $cari['id_tbl_status_transaksi'] == '4' ){
			$no ++;
			$tgl=date("d-m-Y",strtotime($cari['tanggal']));
			echo'<tr >
			<td>'.$no.'</td>
			<td>'.$tgl.'</td>
			<td>'.$cari['nama_siswa'].'</td>
			<td>'.$cari['keterangan'].'</td>
			<td style="text-align:right;">'.number_format($cari['jumlah']).'</td>
			</tr>';	
			$total=$total+$cari['jumlah'];
			}
		}
		echo '<tr>
		<td colspan="4">Total</td>
		<td style="text-align:right;">'.number_format($total).'</td>
		</tr>';
		echo'
		</tbody>
		</table>';
	}



								
							



    public function js($param1)
	{
		$this->get_js("laporan_uangsaku/js/" . $param1);
	}
}
?>