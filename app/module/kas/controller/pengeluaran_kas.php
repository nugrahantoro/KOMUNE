<?php

class Pengeluaran_kas extends Controller{

	function send_th()
	{
		global $con;
		$querykls = "SELECT * FROM data_tahunajar ORDER BY aktif DESC, nama_tahunajar DESC";
		$result = mysqli_query($con, $querykls);
		echo "<select id='pilih_tahun' class= 'btn btn-light btn-lg' style='font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;' onChange='myNewFunction(this);'>
		<option value='0'>Pilih</option>";
		while ($dt = mysqli_fetch_array($result)) {
			echo '<option value="' . $dt['id_tahunajar'] . '">' . $dt['nama_tahunajar'] . '</option>';
		}
		echo '</select>';
	}

	function send_jenjang()
	{
		global $con;
		$sesi = $_SESSION['id'];
		$query = "SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang
					INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'";
		$resultjen = mysqli_query($con, $query);
		// var_dump($query);
		echo "<select id='pilih_jenjang' class= 'btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;' onChange='myNewFunctionjang(this);'>
			";
		while ($dt = mysqli_fetch_array($resultjen)) {
			echo '<option value="' . $dt['id_jenjang'] . '">' . $dt['nama_jenjang'] . '</option>';
		}
		echo '</select>';
	}

	function send_ambil_dari(){
		global $con;
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
		$id_kategori=isset($_GET['id_kategori'])? $_GET['id_kategori']:"";
		$query = "SELECT
			kategori_pengeluaran.id_kategori_pengeluaran AS a,
			kategori_pengeluaran.nama_kategori_pengeluaran AS aa,
			id_jenis_transaksi AS ccc,
			nama_transaksi AS ddd  
		FROM
			master_jenis_transaksi
			INNER JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran 
		WHERE
			master_jenis_transaksi.id_sesi = '$tahun' 
			AND master_jenis_transaksi.id_jenjang = $jenjang 
		GROUP BY
			master_jenis_transaksi.id_kategori_pengeluaran UNION ALL
		SELECT
			id_join AS a,
			nama_akun AS bb,
			id_akun AS ccc,
			'' AS ddd 
		FROM
			akun";
			$qry=mysqli_query($con,$query);
			
			echo "<select id='myselects_thj_a' class='btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;' onChange='myNewFunctionran(this);'>
			<option value='0'>Pilih</option>";
			while($rs=mysqli_fetch_array($qry)){
				echo '<option value="'.$rs['a'].'">'.$rs['aa'].'</option>';
		}
		echo '<script>$("#myselects_thj_a").val('.$id_kategori.');</script>'; 
	}

	function send_ambil_jenis(){
		global $con;
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
		$qry = "SELECT * FROM jenis_pengeluaran WHERE id_jenjang=$jenjang AND id_sesi=$tahun";
		$sql=mysqli_query($con,$qry);
			echo "<select id='myselects_thj_b' class='btn btn-light btn-lg' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;'>";
			while($rs=mysqli_fetch_array($sql)){
				echo '<option value="'.$rs['id_jenis_pengeluaran'].'">'.$rs['nama_jns_pengeluaran'].'</option>';
		}
		echo '</select>';
	}

	function send_jenis_pengeluaran(){
		global $con;
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
		echo '
			<table class="table table-sm table-bordered" style="margin-top: 5%;" id="tabse">
				<thead class="table-light">
					<tr>
						<th style="width: 10px;">No.</th>
						<th style="width: 80px;">Nama Pengeluaran</th>
						<th style="width: 10px;">Aksi</th>
					</tr>
				</thead>
				<tbody>	
		';

		$sql="SELECT * from jenis_pengeluaran WHERE id_sesi='$tahun' AND id_jenjang='$jenjang' ORDER BY id_jenis_pengeluaran ASC";
		$hs=mysqli_query($con,$sql);

		$no = 0;
		while ($rs = mysqli_fetch_array($hs)) {
			$no++;
			echo'<tr>
			<td>'.$no.'</td>
			<td style="display:none;">'.$rs['id_jenis_pengeluaran'].'</td>
			<td style="display:none;">'.$rs['id_sesi'].'</td>
			<td style="display:none;">'.$rs['id_jenjang'].'</td>
			<td>'.$rs['nama_jns_pengeluaran'].'</td>
			<td width="10px"><button class="btn btn-primary btn-xs" onclick="theare_kat();"><i class="glyphicon glyphicon-pencil"></i> action</button></td>
			</tr>';
		}
		echo '</tbody></table>';
	}

	function pengeluaran(){
		global $con;
		$id=isset($_GET['id'])? $_GET['id']:"";
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
			$querykls = "SELECT
			SUM( pengeluaran_kas.nominal_pengeluaran ) AS juml 
		FROM
			pengeluaran_kas 
		WHERE
			id_kategori_pengeluaran = '$id' AND id_sesi=$tahun AND id_jenjang='$jenjang'";
			$result = mysqli_query($con,$querykls);
		while($dt=mysqli_fetch_array($result)){
			echo $dt['juml'];
		}
	}

	function saldo(){
		global $con;
		$id=isset($_GET['id'])? $_GET['id']:"";
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
			$querykls = "SELECT
			SUM( transaksi_siswa.nominal_dibayarkan ) AS juml 
		FROM
			master_jenis_transaksi
			INNER JOIN transaksi_siswa ON transaksi_siswa.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi 
		WHERE
			master_jenis_transaksi.id_kategori_pengeluaran = $id
			AND master_jenis_transaksi.id_sesi = $tahun
			AND id_jenjang='$jenjang'";
				$queryklsx = "SELECT
			SUM( pengeluaran_kas.nominal_pengeluaran ) AS juml 
		FROM
			pengeluaran_kas 
		WHERE
			id_kategori_pengeluaran = '$id' AND id_sesi=$tahun AND id_jenjang='$jenjang'";
			$result = mysqli_query($con,$querykls);
			$resultx = mysqli_query($con,$queryklsx);
		while($dt=mysqli_fetch_array($result)){
			while($dtx=mysqli_fetch_array($resultx)){
			echo ($dt['juml']-$dtx['juml']);
		}
	}
	}

	function simpan_kat(){
		global $con;
		$jenjang = $_POST['jenjang'];
		$tahun = $_POST['tahun'];
		$nama_id = $_POST['nama_id'];
		$q = "INSERT INTO jenis_pengeluaran(nama_jns_pengeluaran,id_sesi,id_jenjang) 
			VALUES ('$nama_id','$tahun','$jenjang')";
		$send=mysqli_query($con,$q);
	}

	function edit_kat(){
		global $con;
		$jenjang = $_POST['jenjang'];
		$tahun = $_POST['tahun'];
		$nama_id = $_POST['nama_id'];
		$id_jenis_pengeluaran = $_POST['id_jenis_pengeluaran'];
		$q = "UPDATE jenis_pengeluaran SET nama_jns_pengeluaran = '$nama_id' WHERE id_jenis_pengeluaran = '$id_jenis_pengeluaran'";
		$send=mysqli_query($con,$q);
	}

	function hapus_kat(){
		global $con;
		$jenjang = $_POST['jenjang'];
		$tahun = $_POST['tahun'];
		$nama_id = $_POST['nama_id'];
		$id_jenis_pengeluaran = $_POST['id_jenis_pengeluaran'];
		$q = "DELETE FROM jenis_pengeluaran WHERE id_jenis_pengeluaran = '$id_jenis_pengeluaran'";
		$send=mysqli_query($con,$q);
	}

	function pemasukan(){
		global $con;
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
		$id=isset($_GET['id'])? $_GET['id']:"";
			$querykls = "SELECT
			SUM( transaksi_siswa.nominal_dibayarkan ) AS juml 
		FROM
			master_jenis_transaksi
			INNER JOIN transaksi_siswa ON transaksi_siswa.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi 
		WHERE
			master_jenis_transaksi.id_kategori_pengeluaran = $id
			AND master_jenis_transaksi.id_sesi = $tahun
			AND id_jenjang='$jenjang'";
			$result = mysqli_query($con,$querykls);
		// echo $querykls;
		while($dt=mysqli_fetch_array($result)){
				echo $dt['juml'];
		}
	}

	function send_cek_kel(){
		global $con;
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
		$id=isset($_GET['id'])? $_GET['id']:"";
			$querykls = "SELECT
			SUM( pengeluaran_kas.nominal_pengeluaran ) AS juml 
		FROM
			pengeluaran_kas 
		WHERE
			id_kategori_pengeluaran = '$id' AND id_sesi=$tahun AND id_jenjang='$jenjang'";
			$result = mysqli_query($con,$querykls);
		while($dt=mysqli_fetch_array($result)){
			echo $dt['juml'];
		}
	}

	function send_cek_kel_hit(){
		global $con;
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
		$id=isset($_GET['id'])? $_GET['id']:"";
			$querykls = "SELECT
			SUM( transaksi_siswa.nominal_dibayarkan ) AS juml 
		FROM
			master_jenis_transaksi
			INNER JOIN transaksi_siswa ON transaksi_siswa.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi 
		WHERE
			master_jenis_transaksi.id_kategori_pengeluaran = $id
			AND master_jenis_transaksi.id_sesi = $tahun
			AND id_jenjang='$jenjang'";
				$queryklsx = "SELECT
			SUM( pengeluaran_kas.nominal_pengeluaran ) AS juml 
		FROM
			pengeluaran_kas 
		WHERE
			id_kategori_pengeluaran = '$id' AND id_sesi=$tahun AND id_jenjang='$jenjang'";
			$result = mysqli_query($con,$querykls);
			$resultx = mysqli_query($con,$queryklsx);
			while($dt=mysqli_fetch_array($result)){
				while($dtx=mysqli_fetch_array($resultx)){
				echo ($dt['juml']-$dtx['juml']);
			}
		}
	}

	function tampilkan_data(){
		global $con;
		$tahun=isset($_GET['tahun'])? $_GET['tahun']:"";
		$jenjang=isset($_GET['jenjang'])? $_GET['jenjang']:"";
		$id=isset($_GET['id'])? $_GET['id']:"";
		$awal=isset($_GET['awal'])? $_GET['awal']:"";
		$akhir=isset($_GET['akhir'])? $_GET['akhir']:"";

		$limit = 100;
		$pages = isset($_GET['page'])? $_GET['page']:"";
		$start = ($pages-1)*$limit;
		if ($awal!=='' && $akhir!==''){
			$tanggal = " AND DATE(pengeluaran_kas.tanggal) BETWEEN DATE('$awal') AND DATE('$akhir')";
		}else if($awal=='' && $akhir==''){
			$tanggal="";
		}else{
			$tanggal="";
		}
		$query="SELECT COUNT(*) FROM (SELECT id_pengeluaran FROM pengeluaran_kas WHERE id_sesi='$tahun' AND id_jenjang='$jenjang' $tanggal) as jml";
		$results=mysqli_query($con,$query);
		$hsl = mysqli_fetch_row($results);
		$count=$hsl[0];
		$page = ceil($count/$limit);


		echo '<select id="myselects_thhalm" style="border:1px solid #E1E1E1;height:25px;margin-bottom:5px;font-size:11px;" onChange="myNewFunctionhal(this);">';
		echo '<option>Page</option>';
			for($i=1; $i<=$page; $i++){
				echo '<option value="'.$i.'" id="'.$i.'">'.$i.'</option>';
			}
		echo '</select><span style="font-family:tahoma;font-size:11px;margin-left:10px;">halaman '.$pages.' dari '.$count.' data</span>';
			
			$querykls = "SELECT 
		users_log.nama_us,
		pengeluaran_kas.id_jenis_pengeluaran,
		pengeluaran_kas.id_jenjang,
		pengeluaran_kas.id_sesi,
		pengeluaran_kas.id_kategori_pengeluaran,
		pengeluaran_kas.tanggal,
		data_jenjang.nama_jenjang,
		kategori_pengeluaran.nama_kategori_pengeluaran,
		jenis_pengeluaran.nama_jns_pengeluaran,
		pengeluaran_kas.nama_pengeluaran,
		pengeluaran_kas.deskripsi_pengeluaran,
		pengeluaran_kas.nominal_pengeluaran,
		pengeluaran_kas.id_pengeluaran
		FROM
			pengeluaran_kas
			LEFT JOIN users_log ON users_log.id = pengeluaran_kas.id_user
			LEFT JOIN data_jenjang ON data_jenjang.id_jenjang = pengeluaran_kas.id_jenjang
			LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = pengeluaran_kas.id_kategori_pengeluaran
			LEFT JOIN jenis_pengeluaran ON jenis_pengeluaran.id_jenis_pengeluaran = pengeluaran_kas.id_jenis_pengeluaran
		WHERE
			pengeluaran_kas.id_jenjang = '$jenjang' 
			AND pengeluaran_kas.id_sesi = '$tahun'
			AND kategori_pengeluaran.id_kategori_pengeluaran = '$id'
			$tanggal
		ORDER BY
			pengeluaran_kas.id_pengeluaran ASC 
			LIMIT $start,
			$limit";
			$result = mysqli_query($con,$querykls);
			echo '<div style="overflow-x:auto;">';
			echo '<table id="tab" width="100%; class="table table-sm table-bordered"">
				<thead class="table-light">
				<tr>
				<th width="15px;">N0</th>
				<th width="100px;">Tanggal</th>
				<th width="300px;">Pengambilan Dari</th>
				<th width="300px;">Jenis Pengeluaran</th>
				<th width="300px;">Nama pengeluaran</th>
				<th width="300px;">Deskripsi</th>
				<th width="150px;">Nominal</th>
				<th width="150px;">User</th>
				<th width="100px;">Act</th>
				</tr>
				</thead>
				<tbody>';
			$no = $start;
			$total=0;
			
			while($dt=mysqli_fetch_array($result)){
				$date = $dt['tanggal'];
				$tgl = date("d-m-Y",strtotime($date));
				$no ++;
				echo'<tr>
				<td>'.$no.'</td>
				<td style="display:none;">'.$dt['id_pengeluaran'].'</td>
				<td style="display:none;">'.$dt['tanggal'].'</td>
				<td>'.$tgl.'</td>
				<td>'.$dt['nama_kategori_pengeluaran'].'</td>
				<td>'.$dt['nama_jns_pengeluaran'].'</td>
				<td>'.$dt['nama_pengeluaran'].'</td>
				<td>'.$dt['deskripsi_pengeluaran'].'</td>
				<td style="display:none;">'.$dt['nominal_pengeluaran'].'</td>
				<td style="text-align:right;">'.number_format((float)$dt['nominal_pengeluaran']).'</td>
				<td style="display:none;">'.$dt['id_kategori_pengeluaran'].'</td>
				<td style="display:none;">'.$dt['id_jenis_pengeluaran'].'</td>
				<td style="display:none;">'.$dt['id_sesi'].'</td>
				<td style="display:none;">'.$dt['id_jenjang'].'</td>
				<td>'.$dt['nama_us'].'</td>
				<td width="10px"><input type="submit" value="action" onclick="theare();"></td>
				</tr>';
				$total=$total+$dt['nominal_pengeluaran'];
			}
			
			echo '<tr>
				<th colspan="6"></th>
				<th width="150px;" style="text-align:right;">Rp. '.number_format($total).'</th>
				<th width="100px;"></th>
				</tr>
				</tbody>
				</table>';
		echo '</div>';
		
	}

	function simpan_pengeluaran(){
		global $con;
		$sesi = $_SESSION['id'];
		$tahun = $_POST['tahun'];
		$jenjang = $_POST['jenjang'];
		$nama_pengeluaran = $_POST['nama_pengeluaran'];
		$deskripsi = $_POST['deskripsi'];
		$nominal = $_POST['nominal'];
		$kat = $_POST['kat'];
		$jns = $_POST['jns'];
		$saldo = $_POST['saldo'];
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO pengeluaran_kas(id_kategori_pengeluaran,id_jenis_pengeluaran,nama_pengeluaran,deskripsi_pengeluaran,nominal_pengeluaran,id_sesi,tanggal,id_jenjang,id_user) 
			VALUES (
			'$kat',
			'$jns',
			'$nama_pengeluaran',
			'$deskripsi',
			'$nominal',
			'$tahun',
			'$date',
			'$jenjang',
			'$sesi')";
			echo $query;
		$send=mysqli_query($con,$query);
	}

	function edit_pengeluaran(){
		global $con;
		$sesi = $_SESSION['id'];
		$tahun = $_POST['tahun'];
		$jenjang = $_POST['jenjang'];
		$nama_pengeluaran = $_POST['nama_pengeluaran'];
		$deskripsi = $_POST['deskripsi'];
		$nominal = $_POST['nominal'];
		$kat = $_POST['kat'];
		$jns = $_POST['jns'];
		$saldo = $_POST['saldo'];
		$id_pengeluaran = $_POST['id_pengeluaran'];
		$date = date("Y-m-d H:i:s");
		$query = "UPDATE pengeluaran_kas SET nominal_pengeluaran = '$nominal', nama_pengeluaran = '$nama_pengeluaran', deskripsi_pengeluaran = '$deskripsi' WHERE id_pengeluaran = '$id_pengeluaran'";
			echo $query;
		$send=mysqli_query($con,$query);
	}

	function hapus_pengeluaran(){
		global $con;
		$sesi = $_SESSION['id'];
		$tahun = $_POST['tahun'];
		$jenjang = $_POST['jenjang'];
		$nama_pengeluaran = $_POST['nama_pengeluaran'];
		$deskripsi = $_POST['deskripsi'];
		$nominal = $_POST['nominal'];
		$kat = $_POST['kat'];
		$jns = $_POST['jns'];
		$saldo = $_POST['saldo'];
		$id_pengeluaran = $_POST['id_pengeluaran'];
		$date = date("Y-m-d H:i:s");
		$query = "DELETE FROM pengeluaran_kas WHERE id_pengeluaran='$id_pengeluaran'";
			echo $query;
		$send=mysqli_query($con,$query);
	}
}

?>