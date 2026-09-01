<?php

class Aktivitas extends Controller{
	
	/* TAB AKTIVITAS */
	function load_kelas()
	{
		$pt = $_POST;
		$data = '';
		if($pt['id_level']="all")
		{
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
		$tbody='';
		$no = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$no++;
			$tbody.="
				<tr class='cari-item kelas-item' id_kelas='$dt[id_kelas]' onclick='click_kelas(this);'>
					<td  class='cari-q'>$dt[nama_kelas]</td>
					<td class='tc'>$dt[jumlah_siswa]</td>
				</tr>";
		}
		
		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr  class=' tc kelas_no_result'  style='$no_res'>
					<td colspan='5'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>Kelas</th>
					<th>Jumlah</th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
	}

	function load_siswa()
	{
		$pt = $_POST;
		if($pt['id_kelas'] != ""){
			$id_kelas = $pt['id_kelas'];
		}
		else{
			$id_kelas = 0;
		}
		
		$sql = "
			SELECT
				b.id_siswa,
				b.nama_siswa
			FROM
				penempatan_siswa a
				JOIN data_siswa b ON b.id_siswa = a.nis_from_idsiswa
			WHERE
				a.id_kelas = '$id_kelas'
		";
		$res = db_query($sql);
		$tbody='';
		$no = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$no++;
			$tbody.="
				<tr class='cari-item siswa-item' id_kelas='$dt[id_siswa]' onclick='click_siswa($dt[id_siswa]);'>
					<td  class='cari-q'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td> <button class='btn btn-sm btn-primary'><i class='bx bx-list-ul '> Lihat Aktivitas</i></button> </td>
				</tr>";
		}
		
		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr  class=' tc siswa_no_result'  style='$no_res'>
					<td colspan='5'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
	}

	function load_aktivitas(){
		$pt = $_POST;

		$sql = "
			SELECT
				b.nama_siswa,
				c.nama_kelas,
				d.nama_tahunajar,
				b.id_siswa
			FROM
				penempatan_siswa a
				JOIN data_siswa b ON b.id_siswa = a.nis_from_idsiswa
				JOIN data_kelas c ON c.id_kelas = a.id_kelas
				JOIN data_tahunajar d ON d.id_tahunajar = a.thn_ajar
			WHERE
				a.nis_from_idsiswa = '$pt[id_siswa]'
		";
		$res = db_query($sql);
		if(mysqli_num_rows($res)>0)
		{
			while($dt=mysqli_fetch_array($res))
			{
				$nama  = $dt['nama_siswa'];
				$kelas = $dt['nama_kelas'];
				$ajar  = $dt['nama_tahunajar'];
			}

			$bulan = date("m");
			$tahun = date("Y");
		}

		$response = array(
			'nama'		=> $nama,
			'kelas'		=> $kelas,
			'tahun'		=> $ajar,
			'id_siswa'	=> $pt['id_siswa']
		);

		echo json_encode($response);
	}

	function lembar_aktivitas(){
		$pt = $_POST;

		$now = date("Y");
		if ($pt['year'] == $now) {
			//$bulan = date("m");
			$bulan = $pt['month'];
			$tahun = $now;
		}
		else{
			$bulan = $pt['month'];
			$tahun = $pt['year'];
		}
		$html = "";

		$html.="
			<p>
				<b>Keterangan</b>:<br>
				1. Tanda [<b>v</b>] berarti sudah melaksanakan kegiatan.<br>
				2. Data bisa menjadi bahan pertimbangan mengisi raport kepribadian.<br>
				3. Diisi tiap hari oleh Orang Tua Siswa.
			</p>
		";

		// $html.="
		// 	<button class='btn btn-info btn-xs' style='margin-bottom: 20px;' onclick='cetak_all();'> Cetak All</button><br><br>
		// ";

		for ($x=$bulan; $x <= $bulan ; $x++) { 
			$d     = cal_days_in_month(CAL_GREGORIAN,$x,$tahun);
			$apa   = $d+2;

			$bulan_data = array (
				1  => 'Januari',
				2  => 'Februari',
				3  => 'Maret',
				4  => 'April',
				5  => 'Mei',
				6  => 'Juni',
				7  => 'Juli',
				8  => 'Agustus',
				9  => 'September',
				10 => 'Oktober',
				11 => 'November',
				12 => 'Desember'
			);
			$bln = $bulan_data[(int)$x]." ".$tahun;

			$html.="
				<button class='btn btn-success btn-xs' style='margin-bottom: 20px; float: right;' onclick=cetak_mount('".$x."','".$tahun."')> Cetak</button>
			";

			$html.="
				<table class='table table-sm table-bordered text-sm'>
					<tr>
						<th rowspan='2'><center>No.</center></th>
						<th rowspan='2' colspan='2' width='25%'><center>Kegiatan</center></th>
						<th colspan=".$d."><center>Bulan ".$bln."</center></th>
						<th rowspan='2'><center>Jml</center></th>
					</tr>
			";
			$html.="
					<tr>
			";
			for ($i=1; $i <= $d; $i++) { 
				$html.="<td>".$i."</td>";		
			}
			$html.="
					</tr>
			";

			$sql = "
				SELECT 
					id_kat_mutabaah,kategori 
				FROM 
					tbl_kategori_mutabaah 
				WHERE  
					id_jenjang = $pt[id_jenjang]
					AND id_sesi = $pt[id_ta]
			";
			$res = db_query($sql);
			$no = 0;
			if(mysqli_num_rows($res)>0)
			{
				while($dt=mysqli_fetch_array($res))
				{
					$no++;
					$html.="
						<tr>
							<td>$no</td>
							<th colspan='2'>$dt[kategori]</th>
							<th colspan=$apa></th>
						</tr>
					";

					$sqlx = "
						SELECT 
							item 
						FROM 
							tbl_item_mutabaah 
						WHERE 
							id_jenjang = $pt[id_jenjang]
							AND id_sesi = $pt[id_ta]
							AND id_kat_mutabaah = $dt[id_kat_mutabaah]
					";
					$resx = db_query($sqlx);
					$abc = 0;
					if(mysqli_num_rows($resx)>0)
					{
						while($dtx=mysqli_fetch_array($resx))
						{
							$abc++;
							$item = $dtx['item'];
							$html.="
								<tr>
									<td></td>
									<td>$abc</td>
									<td>$dtx[item]</td>
							";
							for ($i=1; $i <= $d; $i++) {
								$now = $tahun."-".$x."-".$i;

								$queri_c = "
									SELECT 
										* 
									FROM 
										tbl_mutabaah 
									WHERE 
										nis = $pt[id_siswa]
										AND tanggal = '".$now."'
								";
								$sqli_c = db_query($queri_c);
								if(mysqli_num_rows($sqli_c)>0)
								{
									while($rsi_c=mysqli_fetch_array($sqli_c)){
										$dj = json_decode($rsi_c['implementasi']);

										//$replace = str_replace(" ", "_", $item);
										if (!isset($dj->item)) {
											$html.="
												<td>".$dj->$item."</td>
											";
										}
									}
								}
								else{
									$html.="
										<td></td>
									";
								}
							}
							$html.="
								</tr>
							";
						}
					}
				}
			}

			$html.="
				</table>
				<hr>
			";
		}

		echo $html;
	}

	function cetak_lembar_aktivitas(){
		header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=laporan_aktivitas_siswa.xls");

        $pt = $_GET;

		$tahun = $pt['tahun'];
		$bulan = $pt['bulan'];

		$html = "";

		$html.="
			<table border='0'>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <td>".$pt['nama']."</td>
                    </tr>
                    <tr>
                        <th>Kelas</th>
                        <td>".$pt['kelas']."</td>
                    </tr>
                    <tr>
                        <th>Tahun</th>
                        <td>".$tahun."</td>
                    </tr>
                </thead>
            </table>
		";

		$html.="
			<p>
				<b>Keterangan</b>:<br>
				1. Tanda [<b>v</b>] berarti sudah melaksanakan kegiatan.<br>
				2. Data bisa menjadi bahan pertimbangan mengisi raport kepribadian.<br>
				3. Diisi tiap hari oleh Orang Tua Siswa.
			</p>
		";

		for ($x=1; $x <= $bulan ; $x++) { 
			$d     = cal_days_in_month(CAL_GREGORIAN,$x,$tahun);
			$apa   = $d+2;

			$bulan_data = array (
				1  => 'Januari',
				2  => 'Februari',
				3  => 'Maret',
				4  => 'April',
				5  => 'Mei',
				6  => 'Juni',
				7  => 'Juli',
				8  => 'Agustus',
				9  => 'September',
				10 => 'Oktober',
				11 => 'November',
				12 => 'Desember'
			);
			$bln = $bulan_data[(int)$x]." ".$tahun;

			$html.="
				<table border='1'>
					<tr>
						<th rowspan='2'><center>No.</center></th>
						<th rowspan='2' colspan='2' width='25%'><center>Kegiatan</center></th>
						<th colspan=".$d."><center>Bulan ".$bln."</center></th>
						<th rowspan='2'><center>Jml</center></th>
					</tr>
			";
			$html.="
					<tr>
			";
			for ($i=1; $i <= $d; $i++) { 
				$html.="<td>".$i."</td>";		
			}
			$html.="
					</tr>
			";

			$sql = "
				SELECT 
					id_kat_mutabaah,kategori 
				FROM 
					tbl_kategori_mutabaah 
				WHERE  
					id_jenjang = $pt[id_jenjang]
					AND id_sesi = $pt[id_ta]
			";
			$res = db_query($sql);
			$no = 0;
			if(mysqli_num_rows($res)>0)
			{
				while($dt=mysqli_fetch_array($res))
				{
					$no++;
					$html.="
						<tr>
							<td>$no</td>
							<th colspan='2'>$dt[kategori]</th>
							<th colspan=$apa></th>
						</tr>
					";

					$sqlx = "
						SELECT 
							item 
						FROM 
							tbl_item_mutabaah 
						WHERE 
							id_jenjang = $pt[id_jenjang]
							AND id_sesi = $pt[id_ta]
							AND id_kat_mutabaah = $dt[id_kat_mutabaah]
					";
					$resx = db_query($sqlx);
					$abc = 0;
					if(mysqli_num_rows($resx)>0)
					{
						while($dtx=mysqli_fetch_array($resx))
						{
							$abc++;
							$item = $dtx['item'];
							$html.="
								<tr>
									<td></td>
									<td>$abc</td>
									<td>$dtx[item]</td>
							";
							for ($i=1; $i <= $d; $i++) {
								$now = $tahun."-".$x."-".$i;

								$queri_c = "
									SELECT 
										* 
									FROM 
										tbl_mutabaah 
									WHERE 
										nis = $pt[id_siswa]
										AND tanggal = '".$now."'
								";
								$sqli_c = db_query($queri_c);
								if(mysqli_num_rows($sqli_c)>0)
								{
									while($rsi_c=mysqli_fetch_array($sqli_c)){
										$dj = json_decode($rsi_c['implementasi']);

										//$replace = str_replace(" ", "_", $item);
										if (!isset($dj->item)) {
											$html.="
												<td>".$dj->$item."</td>
											";
										}
									}
								}
								else{
									$html.="
										<td></td>
									";
								}
							}
							$html.="
								</tr>
							";
						}
					}
				}
			}

			$html.="
				</table>
				<hr>
			";
		}

		echo $html;
	}

	function lembar_aktivitas_ori(){
		$pt = $_POST;
		$bulan = date("m");
		$tahun = date("Y");
		$html = "";

		$html.="
			<p>
				<b>Keterangan</b>:<br>
				1. Tanda [<b>v</b>] berarti sudah melaksanakan kegiatan.<br>
				2. Data bisa menjadi bahan pertimbangan mengisi raport kepribadian.<br>
				3. Diisi tiap hari oleh Orang Tua Siswa.
			</p>
		";

		// $html.="
		// 	<button class='btn btn-info btn-xs' style='margin-bottom: 20px;' onclick='cetak_all();'> Cetak All</button><br><br>
		// ";

		for ($x=1; $x <= $bulan ; $x++) { 
			$d     = cal_days_in_month(CAL_GREGORIAN,$x,$tahun);
			$apa   = $d+2;

			$bulan_data = array (
				1  => 'Januari',
				2  => 'Februari',
				3  => 'Maret',
				4  => 'April',
				5  => 'Mei',
				6  => 'Juni',
				7  => 'Juli',
				8  => 'Agustus',
				9  => 'September',
				10 => 'Oktober',
				11 => 'November',
				12 => 'Desember'
			);
			$bln = $bulan_data[(int)$x];

			$html.="
				<button class='btn btn-success btn-xs' style='margin-bottom: 20px; float: right;' onclick='cetak_mount(".$x.");'> Cetak</button>
			";

			$html.="
				<table class='table table-sm table-bordered text-sm'>
					<tr>
						<th rowspan='2'><center>No.</center></th>
						<th rowspan='2' colspan='2' width='25%'><center>Kegiatan</center></th>
						<th colspan=".$d."><center>Bulan ".$bln."</center></th>
						<th rowspan='2'><center>Jml</center></th>
					</tr>
			";
			$html.="
					<tr>
			";
			for ($i=1; $i <= $d; $i++) { 
				$html.="<td>".$i."</td>";		
			}
			$html.="
					</tr>
			";

			$sql = "
				SELECT 
					id_kat_mutabaah,kategori 
				FROM 
					tbl_kategori_mutabaah 
				WHERE  
					id_jenjang = $pt[id_jenjang]
					AND id_sesi = $pt[id_ta]
			";
			$res = db_query($sql);
			$no = 0;
			if(mysqli_num_rows($res)>0)
			{
				while($dt=mysqli_fetch_array($res))
				{
					$no++;
					$html.="
						<tr>
							<td>$no</td>
							<th colspan='2'>$dt[kategori]</th>
							<th colspan=$apa></th>
						</tr>
					";

					$sqlx = "
						SELECT 
							item 
						FROM 
							tbl_item_mutabaah 
						WHERE 
							id_jenjang = $pt[id_jenjang]
							AND id_sesi = $pt[id_ta]
							AND id_kat_mutabaah = $dt[id_kat_mutabaah]
					";
					$resx = db_query($sqlx);
					$abc = 0;
					if(mysqli_num_rows($resx)>0)
					{
						while($dtx=mysqli_fetch_array($resx))
						{
							$abc++;
							$item = $dtx['item'];
							$html.="
								<tr>
									<td></td>
									<td>$abc</td>
									<td>$dtx[item]</td>
							";
							for ($i=1; $i <= $d; $i++) {
								$now = $tahun."-".$x."-".$i;

								$queri_c = "
									SELECT 
										* 
									FROM 
										tbl_mutabaah 
									WHERE 
										nis = $pt[id_siswa]
										AND tanggal = '".$now."'
								";
								$sqli_c = db_query($queri_c);
								if(mysqli_num_rows($sqli_c)>0)
								{
									while($rsi_c=mysqli_fetch_array($sqli_c)){
										$dj = json_decode($rsi_c['implementasi']);

										//$replace = str_replace(" ", "_", $item);
										if (!isset($dj->item)) {
											$html.="
												<td>".$dj->$item."</td>
											";
										}
									}
								}
								else{
									$html.="
										<td></td>
									";
								}
							}
							$html.="
								</tr>
							";
						}
					}
				}
			}

			$html.="
				</table>
				<hr>
			";
		}

		echo $html;
	}

	/* TAB KONFIG AKTIVITAS */
	function load_item()
	{
		$pt = $_POST;
		$sql = "
			SELECT * FROM tbl_kategori_mutabaah WHERE id_jenjang = $pt[id_jenjang] AND id_sesi = $pt[id_ta]
		";
		$res = db_query($sql);
		$tbody='';
		$no = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$no++;
			$tbody.="
				<tr class='cari-item kategori-item' id_kategori='$dt[id_kat_mutabaah]'>
					<td class='tc'>$no</td>
					<td class='cari-q'>$dt[kategori]</td>
					<td> 
						<button class='btn btn-sm btn-dark' onclick='modal_item(event,2,$dt[id_kat_mutabaah]);'><i class='bx bx-edit'> Edit</i></button>
						<button class='btn btn-sm btn-primary' onclick='click_kategori($dt[id_kat_mutabaah]);'><i class='bx bx-list-ul '> --> </i></button> 
					</td>
				</tr>";
		}
		
		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr  class=' tc kategori_no_result'  style='$no_res'>
					<td colspan='2'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Item</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
	}

	function get_data_item()
	{
		$pt=$_POST;
		$data = db_assoc("SELECT * FROM tbl_kategori_mutabaah WHERE id_kat_mutabaah = $pt[id_item]");
		echo json_encode($data);
	}

	function save_item()
	{
		$dt = $_POST;
		$cb['stat']=0;
		$cb['ket']='';
		$dup = db_get("SELECT COUNT(*) FROM tbl_kategori_mutabaah WHERE id_kat_mutabaah='$dt[id_item]' AND kategori = '$dt[nama_item]'");
		if($dup>0)
		{
			$cb['ket']="Item sudah terdaftar, gunakan nama lain";
		}
		else
		{
			if($dt['mode']==1)
			{
				$sql="
					INSERT INTO 
					tbl_kategori_mutabaah ( kategori, id_jenjang, id_sesi ) 
					VALUES ( '$dt[nama_item]', '$dt[id_jenjang]', '$dt[id_ta]' )
				";
			}
			else
			{
				$sql="
					UPDATE  
						tbl_kategori_mutabaah
					SET 
						kategori='$dt[nama_item]',
						id_jenjang='$dt[id_jenjang]',
						id_sesi='$dt[id_ta]'
					WHERE
						id_kat_mutabaah=$dt[id_item]
				";
			}
		}
		if(isset($sql))
		{
			$res=db_query($sql);
			if($res)
			{
				$cb['stat']=1;
			}
			else
			{
				$cb['ket']='Error';
			}
		}
		echo json_encode($cb);
	}

	function del_item()
	{
		$id_item = $_POST['id_item'];
		$cb['stat']=0;
		$cb['ket']='';
		$sql ="DELETE FROM tbl_kategori_mutabaah WHERE id_kat_mutabaah = $id_item";
		if(isset($sql))
		{
			$res=db_query($sql);
			if($res)
			{
				$cb['stat']=1;
			}
			else
			{
				$cb['ket']='Error';
			}
		}
		echo json_encode($cb);
	}

	function load_sub_item()
	{
		$pt = $_POST;
		
		$sql = "
			SELECT
				* 
			FROM
				tbl_item_mutabaah 
			WHERE
				id_kat_mutabaah = $pt[id_item]
				AND id_jenjang = $pt[id_jenjang]
				AND id_sesi = $pt[id_ta]
		";
		$res = db_query($sql);
		$tbody='';
		$no = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$no++;
			$tbody.="
				<tr class='cari-item sub-item'>
					<td class='tc'>$no</td>
					<td class='cari-q'>$dt[item]</td>
					<td class='tc'><img src='$dt[bc]' alt='$dt[item]' class='img-thumbnail' width='50xp' height='50px'></td>
					<td class='tc'> <button class='btn btn-sm btn-dark' onclick='modal_sub_item(event,2,$dt[id_item_mutabaah]);'><i class='bx bx-edit'> Edit</i></button> </td>
				</tr>";
		}
		
		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr  class=' tc sub_no_result'  style='$no_res'>
					<td colspan='4'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Sub Item</th>
					<th>Gambar</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
	}

	function get_data_sub_item()
	{
		$pt=$_POST;
		$data = db_assoc("SELECT * FROM tbl_item_mutabaah WHERE id_item_mutabaah = $pt[id_sub_item]");
		echo json_encode($data);
	}

	function save_sub_item(){
		$item = $_POST['sub_item'];
        $mode = $_POST['mode'];
        $id_item = $_POST['id_item'];
        $id_jenjang = $_POST['id_jenjang'];
        $id_ta = $_POST['id_ta'];
        $id_sub_item = $_POST['id_sub_item'];

        if ($mode=="1") {
        	if (!isset($_FILES["gambar"]["name"])) {
	            $response =  array(
	            	'stat'	=> 0,
	            	'ket' 	=> "Gambar belum dipilih"
	            );
	        }
	        else{
	            $tmp = explode('.', $_FILES["gambar"]["name"]);
	            $extension = end($tmp);
	            $allowed_extension = array("jpeg", "png", "jpg");

	            if (in_array($extension, $allowed_extension)) {
	                $file_name = date('Y-m-d_H_i_s') . '-' . basename($_FILES["gambar"]["name"]);
	                $tanggal = date('Y-m-d');
	                $location = "/home/smartbilling/www-live/assets/images/dev/aktivitas/";
	                $target_file = $location . $file_name;

	                if( isset( $_FILES['gambar'] ) ){
	                	$obj=(object)$_FILES['gambar'];
	    				$tmp=$obj->tmp_name;
	    				$name=$obj->name;
					    if( is_uploaded_file( $tmp ) ){
					        $filename = time() . uniqid( rand() ) . $name;
					        
					        if(move_uploaded_file( $tmp, "/home/smartbilling/www-live/assets/images/dev/aktivitas/{$filename}")){
					        	$bc = "https://live.sekolah-smartbilling.net/assets/images/dev/aktivitas/".$filename;
				        		$query = "
				                	INSERT INTO 
				                		tbl_item_mutabaah ( id_kat_mutabaah, item, id_jenjang, icon, id_sesi, bc )
				                	VALUES
				                    	( '$id_item', '$item', '$id_jenjang', '$filename', '$id_ta', '$bc' )
				                ";
				                $proses = db_query($query);
				                if ($proses) {
				                	$response =  array(
						            	'stat'	=> 1,
						            	'ket' 	=> "Sub Item berhasil ditambah"
						            );
				                }
				                else{
				                	$response =  array(
						            	'stat'	=> 0,
						            	'ket' 	=> "Sub Item gagal ditambah"
						            );
				                }
				        	}
				        	else{
				        		$response =  array(
					            	'stat'	=> 0,
					            	'ket' 	=> "Gagal upload gambar ".$_FILES["gambar"]["error"]
					            );
				        	}
				        	$saveArr['gambar'] = $filename;
				    }
	                }
	                else{

	                }
	            } else {
	                $response =  array(
		            	'stat'	=> 0,
		            	'ket' 	=> "Format harus JPEG, JPG, dan PNG"
		            );
	            }
	        }
        }
        else{
        	$upd = "UPDATE tbl_item_mutabaah SET item = '$item' WHERE id_item_mutabaah = $id_sub_item";
        	$proses = db_query($upd);
            if ($proses) {
            	$response =  array(
	            	'stat'	=> 1,
	            	'ket' 	=> "Sub Item berhasil dirubah"
	            );
            }
            else{
            	$response =  array(
	            	'stat'	=> 0,
	            	'ket' 	=> "Sub Item gagal dirubah"
	            );
            }
        }
        echo json_encode($response);
	}

	function del_sub_item(){
		$pt = $_POST;

		$query = "SELECT icon FROM tbl_item_mutabaah WHERE id_item_mutabaah = $pt[id_sub_item]";
		$res = db_query($query);
		$gambar = '';
		while($dt=mysqli_fetch_array($res)){
			$gambar = $dt['icon'];
		}

		$del = "DELETE FROM tbl_item_mutabaah WHERE id_item_mutabaah = $pt[id_sub_item]";
		$proses = db_query($del);
		if ($proses) {
			$path = '/home/smartbilling/www-live/assets/images/dev/aktivitas/'.$gambar;
			if (file_exists($path)) {
				unlink($path);
			}

			$response =  array(
	        	'stat'	=> 1,
	        	'ket' 	=> "Sub Item berhasil dihapus"
	        );
		}
		else{
			$response =  array(
	        	'stat'	=> 0,
	        	'ket' 	=> "Sub Item gagal dihapus"
	        );
		}
		echo json_encode($response);
	}

	// /home/smartbilling/www-live/assets/images/dev/aktivitas
}

?>