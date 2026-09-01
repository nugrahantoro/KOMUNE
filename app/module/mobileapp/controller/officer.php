<?php

class Officer extends Controller{

	// User Officer
		function tampil_data()
		{
			$dt = $_POST;
			$query = "
				SELECT
					a.id,
					a.username,
					a.`password`,
					a.nama_us,
					a.alamat,
					a.jabat,
					a.jenis_user,
					a.akses_kelas,
					a.rfid,
					a.id_nfc,
					a.kode_alat,
					a.id_sesi,
					a.id_jenjang,
					a.id_mapel,
					a.id_kelas_perwalian,
					a.mode,
					b.nama_jabatan AS jenis 
				FROM
					users_log a
					LEFT JOIN tbl_jabatan b ON b.id_jabatan = a.jenis_user
				WHERE
					a.jenis_user IN (2,3,4,5,10,11,7)
				GROUP BY a.id ORDER BY b.nama_jabatan DESC
			";
			$res = db_query($query);
			$no = 1;
			$tbody = '';
			while($dt=mysqli_fetch_array($res))
			{
				if ($dt['jenis'] == "Guru" && $dt['rfid'] != "" && $dt['akses_kelas'] == "" && $dt['mode'] == 6) {
					$mode = "<td style='background-color: pink; color: white;'>Absen / Wali Kelas </td>";
				}
				elseif ($dt['jenis'] == "Guru" && $dt['rfid'] != "" && $dt['akses_kelas'] != "") {
					$mode = "<td style='background-color: purple; color: white;'>Absen / Wali Kelas / Jurnal Guru</td>";
				}
				elseif ($dt['jenis'] == "Guru" && $dt['rfid'] == "" && $dt['akses_kelas'] != "") {
					$mode = "<td style='background-color: black; color: white;'>Jurnal Guru</td>";
				}
				elseif ($dt['jenis'] == "Kepala Sekolah" && $dt['rfid'] != "") {
					$mode = "<td style='background-color: #249a6a; color: white;'>Absen</td>";
				}
				elseif ($dt['jenis'] == "Kantin") {
					$mode = "<td style='background-color: brown; color: white;'>Kantin</td>";
				}
				elseif ($dt['jenis'] == "Karyawan") {
					$mode = "<td style='background-color: red; color: white;'>Karyawan</td>";
				}
				elseif ($dt['jenis'] == "Guru" && $dt['akses_kelas'] != "") {
					$mode = "<td style='background-color: #408dcb; color: white;'>Wali Kelas</td>";
				}
				elseif ($dt['jenis'] == "Guru" && $dt['rfid'] != "" && $dt['akses_kelas'] == "") {
					$mode = "<td style='background-color: #249a6a;; color: white;'>Absen</td>";
				}
				else{
					$mode = "<td style='background-color: gray; color: white;'>Standar</td>";
				}
				$btn = "
					<button type='button' class='btn btn-sm btn-info' onclick=detail_user('$dt[id]')>Detail</button>
					<button class='btn btn-xs btn-dark btn-rounded' onclick=modal_user(event,2,'$dt[id]')>
						<i class='bx bx-edit'></i> Edit
					</button>
				";
				$tbody.="
					<tr class='cari-item'>
						<td>".$no++."</td>
						<td>".$dt['id']."</td>
						<td class='cari-q'>".$dt['nama_us']."</td>
						<td class='cari-q'>".$dt['jenis']."</td>
						$mode
						<td>".$btn."</td>
					</tr>
				";
			}

			$no_res = "display:none;";
			if(mysqli_num_rows($res)==0)
			{
				$no_res = "";
			}
			$tbody .="<tr class=' tc user_no_result' style='$no_res'>
						<td colspan='6'>Tidak Ada Data</td>
					</tr>";

			$table = "
				<table class='table table-sm m-0' style='font-size: 12px;'>
					<thead>
					<tr>
						<th width='3%'>#</th>
						<th >Kode</th>
						<th>Nama User</th>
						<th>Jenis User</th>
						<th>Mode</th>
						<th></th>
					</tr>
					</thead>
					<tbody class='table-hover'>
						$tbody
					</tbody>
				</table>";
			echo $table;
		}

		function tampil_kelas(){
			$dt = $_POST;

			$ta = db_get("SELECT id_tahunajar FROM data_tahunajar WHERE aktif='1' LIMIT 1");
			$query = "
				SELECT * FROM data_kelas WHERE id_sesi = '$ta' AND id_jenjang = '$dt[id_jenjang]'
			";
			$res = db_query($query);
			$no = 1;
			$option = "<option value='0'>-pilih-</option>";
			while($dt=mysqli_fetch_array($res)){
				$option.="
					<option value='".$dt['id_kelas']."'>".$dt['nama_kelas']."</option>
				";
			}
			echo $option;
		}

		function get_data_user()
		{
			$kode = $_POST['id_user'];
			$data = db_assoc("
				SELECT
					a.id,
					a.nama_us,
					a.username,
					a.`password`,
					a.alamat,
					(SELECT nama_kelas FROM data_kelas WHERE id_kelas = id_kelas_perwalian) AS id_kelas_perwalian,
					a.rfid,
					(SELECT GROUP_CONCAT(nama_kelas) FROM data_kelas WHERE id_kelas IN (a.akses_kelas)) AS akses_kelas,
					a.mode,
					b.nama_mapel AS id_mapel,
					a.kode_alat,
					a.id_kelas_perwalian,
					a.jenis_user,
					a.id_jenjang,
					a.id_mapel
				FROM
					users_log a
					LEFT JOIN data_mapel b ON b.id_mapel = a.id_mapel
				WHERE
					a.id = '$kode'
			");
			echo json_encode($data);
		}

		function get_data_mata_pelajaran()
		{
			$dt = $_POST;

			$ta = db_get("SELECT id_tahunajar FROM data_tahunajar WHERE aktif='1' LIMIT 1");

			$query = "
				SELECT * FROM data_mapel WHERE id_sesi = '$ta' AND id_jenjang = $dt[id_jenjang]
			";
			$res = db_query($query);
			$tbody = '';
			$no = 1;
			while($dt=mysqli_fetch_array($res))
			{
				$tbody .= "
					<option value='".$dt['id_mapel']."'>".$dt['nama_mapel']."</option>
				";
			}
			$table = "
				<select class='dropdown-toggle' multiple id='us_mapel' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:12px;'>
					$tbody
				</select>
			";
			echo $table;
		}

		function get_data_kelas()
		{
			$ps = $_POST;

			$ta = db_get("SELECT id_tahunajar FROM data_tahunajar WHERE aktif='1' LIMIT 1");

			$query = "
				SELECT * FROM data_kelas WHERE id_sesi = '$ta' AND id_jenjang = $ps[id_jenjang]
			";
			$res = db_query($query);
			$tbody = '';
			$no = 1;
			while($dt=mysqli_fetch_array($res))
			{
				$tbody .= "
					<option value='".$dt['id_kelas']."'>".$dt['nama_kelas']."</option>
				";
				
			}
			$table = "
				<select class='dropdown-toggle' multiple id='us_ampu' style='border:1px solid #E1E1E1;font-family:tahoma;font-size:12px;'>
					$tbody
				</select>
			";
			echo $table;
		}

		function save_user()
		{
			$dt = $_POST;
			$cb['stat']=0;
			$cb['ket']='';
			$id_karyawan = "ex".$dt['kode'];
			$id_ampu = '';
			if (isset($dt['akses_kelas'])) {
				$id_ampu = implode(',', $dt['akses_kelas']);
			}
			$id_map = '';
			if (isset($dt['id_mapel'])) {
				$id_map = implode(',', $dt['id_mapel']);
			}

			$a = "
				SELECT id_tahunajar FROM data_tahunajar WHERE aktif = '1'
			";
			$res_a = db_query($a);
			while($dta=mysqli_fetch_array($res_a))
			{
				$id_sesi = $dta['id_tahunajar'];
			}
				
			if($dt['glob_mode']==1)
			{
				$query_all = db_get("SELECT COUNT(*) FROM billing_core.users_log_all WHERE username='$dt[username]' AND db_name = '".ABS."'");
				if ($query_all>0) 
				{
					$cb['ket']="Sudah ada username yang sama, gunakan username lain";
				}
				else{
					$dup = db_get("SELECT * FROM users_log WHERE nama_us='$dt[nama]' OR rfid='$dt[no_kartu]'");
					if($dup>0)
					{
						$cb['ket']="Data User sudah terdaftar, silahkan cek kembali";
					}
					else{
						$sql_all="
							INSERT INTO 
							billing_core.users_log_all ( id, username, password, nama_us, alamat, jenis_user, rfid, db_name ) 
							VALUES ( '$id_karyawan', '$dt[username]', '$dt[password]', '$dt[nama]', '$dt[alamat]', '$dt[jenis]', '$dt[no_kartu]', '".ABS."' )
						";
						$sql="
							INSERT INTO 
							users_log ( id, username, password, nama_us, alamat, jenis_user, rfid, id_sesi, id_jenjang, akses_kelas, kode_alat, id_mapel, id_kelas_perwalian, mode ) 
							VALUES ( '$id_karyawan', '$dt[username]', '$dt[password]', '$dt[nama]', '$dt[alamat]', '$dt[jenis]', '$dt[no_kartu]', '$id_sesi', '$dt[id_jenjang]', '$id_ampu', '$dt[kode_alat]', '$id_map', '$dt[kelas]', '$dt[mode]' )
						";
					}
				}
			}
			else
			{
				if (isset($dt['akses_kelas'])) {
					$tambahhan = "akses_kelas='$id_ampu',";
				}
				else{
					$tambahhan = '';
				}
				if (isset($dt['id_mapel'])) {
					$tambahhan_mapel = ", id_mapel='$id_map'";
				}
				else{
					$tambahhan_mapel = '';
				}

				$query_all = db_get("SELECT COUNT(*) FROM billing_core.users_log_all WHERE username='$dt[username]' AND db_name = '".ABS."'");
				if ($query_all>0) 
				{
					$sql_all="
						UPDATE  
							billing_core.users_log_all
						SET 
							password='$dt[password]', 
							nama_us='$dt[nama]',
							alamat='$dt[alamat]',
							jenis_user='$dt[jenis]', 
							rfid='$dt[no_kartu]'
						WHERE
							id='$dt[kode]' AND db_name = '".ABS."'
					";

					$sql="
						UPDATE  
							users_log
						SET  
							username='$dt[username]', 
							password='$dt[password]', 
							nama_us='$dt[nama]',
							alamat='$dt[alamat]',
							jenis_user='$dt[jenis]', 
							rfid='$dt[no_kartu]',
							id_jenjang='$dt[id_jenjang]',
							$tambahhan
							kode_alat='$dt[kode_alat]',
							id_kelas_perwalian='$dt[kelas]',
							mode='$dt[mode]'
							$tambahhan_mapel
						WHERE
							id='$dt[kode]'
					";
				}
				else{
					$sql_all="
						UPDATE  
							billing_core.users_log_all
						SET 
							username='$dt[username]', 
							password='$dt[password]', 
							nama_us='$dt[nama]',
							alamat='$dt[alamat]',
							jenis_user='$dt[jenis]', 
							rfid='$dt[no_kartu]'
						WHERE
							id='$dt[kode]' AND db_name = '".ABS."'
					";

					$sql="
						UPDATE  
							users_log
						SET  
							username='$dt[username]', 
							password='$dt[password]', 
							nama_us='$dt[nama]',
							alamat='$dt[alamat]',
							jenis_user='$dt[jenis]', 
							rfid='$dt[no_kartu]',
							id_jenjang='$dt[id_jenjang]',
							$tambahhan
							kode_alat='$dt[kode_alat]',
							id_kelas_perwalian='$dt[kelas]',
							mode='$dt[mode]'
							$tambahhan_mapel
						WHERE
							id='$dt[kode]'
					";
				}
			}
			if(isset($sql))
			{
				$res=db_query($sql_all);
				if($res)
				{
					db_query($sql);
					$cb['stat']=1;
				}
				else
				{
					$cb['ket']='Error';
				}
			}
			echo json_encode($cb);
		}

	// Master Menu Officer
		function tampil_data_menu()
		{
			$dt = $_POST;
			$query = "
				SELECT
					id_menu_app,
					nama_menu,
					urutan,
					aktif,
					tipe,
					icon AS new_icon 
				FROM
					tbl_menu_app 
				WHERE
					tipe IN ('kepala sekolah','guru','kantin','bk')
				ORDER BY tipe, urutan ASC
			";
			$res = db_query_core($query);
			$no = 1;
			$tbody = '';
			while($dt=mysqli_fetch_array($res))
			{
				
				if ($dt['aktif'] == "ya") {
					$btn = "
						<button type='button' class='btn btn-sm btn-danger' onclick='nonaktif($dt[id_menu_app])'>Non-Aktifkan</button>
					";
				}
				else{
					$btn = "<button type='button' class='btn btn-sm btn-info' onclick='aktif($dt[id_menu_app])'>Aktifkan</button>";
				}
				$btn.="
					<button class='btn btn-sm btn-dark' onclick='modal_menu(event,2,$dt[id_menu_app]);'>
						<i class='bx bx-edit'></i> Edit
					</button>
				";
				$tbody.="
					<tr class='cari-item'>
						<td>".$no++."</td>
						<td class='cari-q'>".$dt['nama_menu']."</td>
						<td>".$dt['urutan']."</td>
						<td>".$dt['aktif']."</td>
						<td class='cari-q'>".$dt['tipe']."</td>
						<td><img src='https://live.sekolah-smartbilling.net/assets/icon/".$dt['new_icon']."' style='height: 50px;'></td>
						<td>".$btn."</td>
					</tr>
				";
			}

			$no_res = "display:none;";
			if(mysqli_num_rows($res)==0)
			{
				$no_res = "";
			}
			$tbody .="<tr class=' tc menu_no_result' style='$no_res'>
						<td colspan='7'>Tidak Ada Data</td>
					</tr>";

			$table = "
				<table class='table table-sm m-0' style='font-size: 12px;'>
					<thead>
					<tr>
						<th width='3%'>#</th>
						<th>Nama Menu</th>
						<th>Urutan</th>
						<th>Aktif</th>
						<th>Tipe</th>
						<th>Icon</th>
						<th></th>
					</tr>
					</thead>
					<tbody class='table-hover'>
						$tbody
					</tbody>
				</table>";
			echo $table;
		}

		function aktif(){
			$pt = $_POST;
			$upd = db_assoc_core("UPDATE tbl_menu_app SET aktif = 'ya' WHERE id_menu_app = $pt[id_menu_app]");
			echo json_encode($upd);
		}

		function nonaktif(){
			$pt = $_POST;
			$upd = db_assoc_core("UPDATE tbl_menu_app SET aktif = 'tidak' WHERE id_menu_app = $pt[id_menu_app]");
			echo json_encode($upd);
		}

		function get_data_menu(){
			$pt = $_POST;
			$data = db_assoc_core("
				SELECT
					id_menu_app,
					nama_menu,
					urutan,
					aktif,
					tipe,
					icon AS new_icon
				FROM
					tbl_menu_app 
				WHERE
					tipe IN ('kepala sekolah','guru','kantin','bk')
					AND id_menu_app = $pt[id_menu_app]
				ORDER BY tipe, urutan ASC
			");
			echo json_encode($data);
		}

		function save_menu(){
	        $nama_menu    = $_POST['nama_menu'];
	        $urutan       = $_POST['urutan'];
	        $tipe         = $_POST['tipe'];
	        $aktif        = $_POST['aktif'];
	        $mode         = $_POST['mode'];
	        $id_menu_app  = $_POST['id_menu_app'];

	        if ($mode=="1") {
	        	if (!isset($_FILES["gambar"]["name"])) {
		            $response =  array(
		            	'stat'	=> 0,
		            	'ket' 	=> "Icon belum dipilih"
		            );
		        }
		        else{
		            $tmp = explode('.', $_FILES["gambar"]["name"]);
		            $extension = end($tmp);
		            $allowed_extension = array("jpeg", "png", "jpg");

		            if (in_array($extension, $allowed_extension)) {
		                $file_name = date('Y-m-d_H_i_s') . '-' . basename($_FILES["gambar"]["name"]);
		                $tanggal = date('Y-m-d');
		                $location = "/home/smartbilling/www-live/assets/images/dev/";
		                $target_file = $location . $file_name;

		                if( isset( $_FILES['gambar'] ) ){
		                	$obj=(object)$_FILES['gambar'];
		    				$tmp=$obj->tmp_name;
		    				$name=$obj->name;
						    if( is_uploaded_file( $tmp ) ){
						        $filename = time() . uniqid( rand() ) . $name;
						        
						        if(move_uploaded_file( $tmp, "/home/smartbilling/www-live/assets/icon/{$filename}")){
					        		$query = "
					                	INSERT INTO 
					                		tbl_menu_app ( nama_menu, urutan, tipe, aktif, icon )
					                	VALUES
					                    	( '$nama_menu', '$urutan', '$tipe', '$aktif', '$filename' )
					                ";
					                $proses = db_query_core($query);
					                if ($proses) {
					                	$response =  array(
							            	'stat'	=> 1,
							            	'ket' 	=> "Menu berhasil ditambah"
							            );
					                }
					                else{
					                	$response =  array(
							            	'stat'	=> 0,
							            	'ket' 	=> "Menu gagal ditambah"
							            );
					                }
					        	}
					        	else{
					        		$response =  array(
						            	'stat'	=> 0,
						            	'ket' 	=> "Gagal upload icon ".$_FILES["gambar"]["error"]
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
	        	$upd = "UPDATE tbl_menu_app SET nama_menu = '$nama_menu', urutan = '$urutan', tipe = '$tipe' WHERE id_menu_app = $id_menu_app";
	        	$proses = db_query_core($upd);
	            if ($proses) {
	            	$response =  array(
		            	'stat'	=> 1,
		            	'ket' 	=> "Menu berhasil dirubah"
		            );
	            }
	            else{
	            	$response =  array(
		            	'stat'	=> 0,
		            	'ket' 	=> "Menu gagal dirubah"
		            );
	            }
	        }
	        echo json_encode($response);
		}

		function del_menu(){
			$pt = $_POST;

			$query = "SELECT icon,new_icon FROM tbl_menu_app WHERE id_menu_app = $pt[id_menu_app]";
			$res = db_query($query);
			$gambar = '';
			while($dt=mysqli_fetch_array($res)){
				$gambar_old = $dt['icon'];
				$gambar_new = $dt['new_icon'];
			}

			if ($gambar_new != "") {
				$gambar = $gambar_new;
			}
			else{
				$gambar = $gambar_old;
			}

			$del = "DELETE FROM tbl_menu_app WHERE id_menu_app = $pt[id_menu_app]";
			$proses = db_query($del);
			if ($proses) {
				$path = '/home/smartbilling/www-live/assets/icon/'.$gambar;
				if (file_exists($path)) {
					unlink($path);
				}

				$response =  array(
		        	'stat'	=> 1,
		        	'ket' 	=> "Menu berhasil dihapus"
		        );
			}
			else{
				$response =  array(
		        	'stat'	=> 0,
		        	'ket' 	=> "Menu gagal dihapus"
		        );
			}
			echo json_encode($response);
		}

	// Akses Menu Officer
		function tampil_data_akses()
		{
			$dt = $_POST;
			$query = "
				SELECT
					a.id,
					a.id_user,
					b.*,
					c.nama_us
				FROM
					tbl_user_akes_officer a
					JOIN tbl_menu_app b ON b.id_menu_app = a.id_menu 
					JOIN users_log_all c ON c.id = a.id_user
				WHERE
					a.db_name = '".ABS."'
					AND c.db_name = '".ABS."'
				ORDER BY
					c.nama_us
			";
			$res = db_query_core($query);
			$no = 1;
			$tbody = '';
			while($dt=mysqli_fetch_array($res))
			{
				
				$btn = "
					<button type='button' class='btn btn-sm btn-danger' onclick='del_akses($dt[id])'>Hapus</button>
				";
				$tbody.="
					<tr class='cari-item'>
						<td>".$no++."</td>
						<td class='cari-q'>".$dt['nama_us']."</td>
						<td>".$dt['nama_menu']."</td>
						<td>".$dt['aktif']."</td>
						<td class='cari-q'>".$dt['tipe']."</td>
						<td>".$btn."</td>
					</tr>
				";
			}

			$no_res = "display:none;";
			if(mysqli_num_rows($res)==0)
			{
				$no_res = "";
			}
			$tbody .="<tr class=' tc akses_no_result' style='$no_res'>
						<td colspan='7'>Tidak Ada Data</td>
					</tr>";

			$table = "
				<table class='table table-sm m-0' style='font-size: 12px;'>
					<thead>
					<tr>
						<th width='3%'>#</th>
						<th>User</th>
						<th>Menu</th>
						<th>Aktif</th>
						<th>Tipe</th>
						<th></th>
					</tr>
					</thead>
					<tbody class='table-hover'>
						$tbody
					</tbody>
				</table>";
			echo $table;
		}
		
		function save_akses()
		{
			$dt = $_POST;
			$cb['stat']=0;
			$cb['ket']='';
			$id_user = $dt['id_user'];
			$pilihan = $dt['pilihan'];
			$id_menu = $dt['id_menu'];
			$db_name = ABS;

			$cek_dulu = db_get_core("SELECT COUNT(id) FROM tbl_user_akes_officer WHERE id_user = '$dt[id_user]' AND id_menu = '$dt[id_menu]' AND db_name = '$db_name'");
			if ($cek_dulu>0) 
			{
				$cb['ket']="akses sudah ada";
			}
			else{
				if ($pilihan == "1") {
					$query = "
						SELECT id_menu_app FROM tbl_menu_app WHERE tipe = '$id_menu'
					";
					$res = db_query_core($query);
					while($x=mysqli_fetch_array($res))
					{
						$cek_lagi = db_get_core("SELECT COUNT(id) FROM tbl_user_akes_officer WHERE id_user = '$dt[id_user]' AND id_menu = '$x[id_menu_app]' AND db_name = '$db_name'");
						if ($cek_lagi<1){
							$sql="
								INSERT INTO 
								tbl_user_akes_officer ( id_user, id_menu, db_name ) 
								VALUES ( '$dt[id_user]', '$x[id_menu_app]', '$db_name' )
							";
							$exe=db_query_core($sql);
							if($exe)
							{
								$cb['stat']=1;
							}
						} 
						else{
							$cb['ket']='Error';
						}
					}
				}
				else{
					$sql="
						INSERT INTO 
						tbl_user_akes_officer ( id_user, id_menu, db_name ) 
						VALUES ( '$dt[id_user]', '$dt[id_menu]', '$db_name' )
					";
					$exe=db_query_core($sql);
					if($exe)
					{
						$cb['stat']=1;
					}
				}
			}
			echo json_encode($cb);
		}

		function del_akses(){
			$pt = $_POST;

			$del = "DELETE FROM tbl_user_akes_officer WHERE id = $pt[id]";
			$proses = db_query_core($del);
			if ($proses) {
				$response =  array(
		        	'stat'	=> 1,
		        	'ket' 	=> "Akses berhasil dihapus"
		        );
			}
			else{
				$response =  array(
		        	'stat'	=> 0,
		        	'ket' 	=> "Akses gagal dihapus"
		        );
			}
			echo json_encode($response);
		}
}
?>