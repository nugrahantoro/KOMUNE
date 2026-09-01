<?php

class Data_User extends Controller{
	function tampil_data()
	{
		$query = "SELECT 
					id,nama_us,username,password,alamat,jabat,tas
			   FROM 
					users_log
			   WHERE 
					jenis_user=1 AND (is_deleted = 0 OR is_deleted IS NULL)
				ORDER BY LENGTH(id),id";
		$res = db_query($query);
		$tbody = '';
		$no=1;
		while($dt=mysqli_fetch_array($res))
		{
			$verif='';
			$btn_verif="
				<button class='btn btn-xs btn-success btn-rounded' onclick=aktif_user('$dt[id]')>
									<i class='bx bx-check'></i> Aktifkan
								</button>
			";
			if($dt['tas']==1)
			{
				$verif='<span class="badge rounded-pill badge-soft-success" title="aktif">
							<i class="bx bx-check "></i>
						</span>';
				$btn_verif='';
			}
			$tbody .= "<tr>
							<td>".$no++."</td>
							<td>".$dt['id']."</td> 
							<td>".$dt['nama_us']." $verif</td>
							<td>".$dt['jabat']."</td>
							<td class=''>
								<button class='btn btn-xs btn-dark btn-rounded' onclick=modal_user(2,'$dt[id]')>
									<i class='bx bx-edit'></i> Edit
								</button>
								$btn_verif
							</td>
					</tr>";
		}
		$table = "
			<table class='table table-sm table-bordered  text-sm'>
				<thead class='table-light'>
				<tr>
					<th>No.</th>
					<th>ID Pengguna</th>
					<th>Nama Pengguna</th>
					<th>Role</th>
					<th>Aksi</th>
				</tr>
				</thead>
				<tbody>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}
	
	// function tampil_data2()
	// {
	// 	$query = "SELECT
	// 				id,
	// 				nama_us,
	// 				username,
	// 				password,
	// 				alamat,
	// 				if(jenis_user=2,CONCAT(jabat,' ',IFNULL(nama_kelas,'')),jabat) as jabat,
	// 				tas 
	// 			FROM
	// 				users_log 
	// 				LEFT JOIN data_kelas ON akses_kelas = id_kelas
	// 			WHERE
	// 				jenis_user IN ( 2, 3, 10 ) 
	// 			ORDER BY
	// 				LENGTH( id ),
	// 				id";
	// 	$res = db_query($query); 
	// 	$tbody = '';
	// 	$no=1;
	// 	while($dt=mysqli_fetch_array($res))
	// 	{
	// 		$tbody .= "<tr>
	// 						<td>".$no++."</td>
	// 						<td>".$dt['id']."</td> 
	// 						<td>".$dt['nama_us']."</td>
	// 						<td>".$dt['jabat']."</td>
	// 						<td class=''>
	// 							<button class='btn btn-xs btn-dark btn-rounded' onclick=modal_user2(2,'$dt[id]')>
	// 								<i class='bx bx-edit'></i> Edit
	// 							</button>
	// 						</td>
	// 				</tr>";
	// 	}
	// 	$table = "
	// 		<table class='table table-sm table-bordered text-sm'>
	// 			<thead class='table-light'>
	// 			<tr>
	// 				<th>No.</th>
	// 				<th>Kode</th>
	// 				<th>Nama User</th>
	// 				<th>Jabatan</th>
	// 				<th>Aksi</th>
	// 			</tr>
	// 			</thead>
	// 			<tbody>
	// 				$tbody
	// 			</tbody>
	// 		</table>";
	// 	echo $table;
	// }

	function get_user()
	{
		$kode = $_POST['kode'];
		$data = db_assoc("SELECT * FROM users_log WHERE id='$kode' LIMIT 1");
		echo json_encode($data);
	}
	
	function save_user()
	{
		$dt = $_POST;
		$cb['stat']=0;
		$cb['ket']='';
		if($dt['mode']==1)
		{
			$dup = db_get("SELECT COUNT(*) FROM users_log WHERE id='ex$dt[kode]'");
			if($dup>0)
			{
				$cb['ket']="Kode user sudah terdaftar, gunakan kode lain";
			}
			else
			{
				$sql="
					INSERT INTO 
					users_log(
						id,
						nama_us,
						username,
						password,
						alamat,
						jabat,
						jenis_user) 
					VALUES (
						'ex$dt[kode]',
						'$dt[nama]',
						'$dt[user]',
						'$dt[pass]',
						'$dt[alamat]',
						'$dt[jabatan]',
						'1'
					)";
				$input_2 = "INSERT INTO billing_core.users_log_all (id, nama_us, username, password, alamat, jabat, jenis_user, db_name)VALUES('ex$dt[kode]', '$dt[nama]', '$dt[user]', '$dt[pass]', '$dt[alamat]', '$dt[jabatan]', '1', '".ABS."')";
				$resque = db_query($input_2);
			}
		}
		else
		{
			$sql="
					UPDATE  
						users_log
					SET 
						nama_us='$dt[nama]',
						username='$dt[user]',
						password='$dt[pass]',
						alamat='$dt[alamat]',
						jabat='$dt[jabatan]'
					WHERE
						id='$dt[kode]'
					";
			$input_2 =
				"
				UPDATE  
						billing_core.users_log_all
					SET 
						nama_us='$dt[nama]',
						username='$dt[user]',
						password='$dt[pass]',
						alamat='$dt[alamat]',
						jabat='$dt[jabatan]'
					WHERE
						id='$dt[kode]'
						AND db_name = '".ABS."'
				";
			$resque = db_query($input_2);
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
	
	// function save_user2()
	// {
	// 	$dt = $_POST;
	// 	$cb['stat']=0;
	// 	$cb['ket']='';
	// 	if($dt['mode']==1)
	// 	{
	// 		$dup = db_get("SELECT COUNT(*) FROM users_log WHERE id='ex$dt[kode]'");
	// 		if($dup>0)
	// 		{
	// 			$cb['ket']="Kode user sudah terdaftar, gunakan kode lain";
	// 		}
	// 		else
	// 		{
	// 			$sql="
	// 				INSERT INTO 
	// 				users_log(
	// 					id,
	// 					nama_us,
	// 					username,
	// 					password,
	// 					alamat,
	// 					jabat,
	// 					jenis_user,
	// 					akses_kelas
	// 					) 
	// 				VALUES (
	// 					'ex$dt[kode]',
	// 					'$dt[nama]',
	// 					'$dt[user]',
	// 					'$dt[pass]',
	// 					'$dt[alamat]',
	// 					'$dt[jabatan]',
	// 					'$dt[jenis_user]',
	// 					'$dt[akses_kelas]'
	// 				)";
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$sql="
	// 				UPDATE  
	// 					users_log
	// 				SET 
	// 					nama_us='$dt[nama]',
	// 					username='$dt[user]',
	// 					password='$dt[pass]',
	// 					alamat='$dt[alamat]',
	// 					jabat='$dt[jabatan]',
	// 					jenis_user='$dt[jenis_user]',
	// 					akses_kelas='$dt[akses_kelas]'
	// 				WHERE
	// 					id='$dt[kode]'
	// 				";
	// 	}
	// 	if(isset($sql))
	// 	{
	// 		$res=db_query($sql);
	// 		if($res)
	// 		{
	// 			$cb['stat']=1;
	// 		}
	// 		else
	// 		{
	// 			$cb['ket']='Error';
	// 		}
	// 	}
	// 	echo json_encode($cb);
	// }
	
	function aktif_user()
	{
		$dt = $_POST;
		$cb['stat']=0;
		$cb['ket']='';
		$sql = "UPDATE users_log SET tas = 1 WHERE id = '$dt[kode]'";
		if(isset($sql))
		{
			$res=db_query($sql);
			if($res)
			{
				$cb['stat']=1;
				$insert = "INSERT INTO idorder (object, id_user)VALUES('1', '$dt[kode]')";
				$resku=db_query($insert);
			}
			else
			{
				$cb['ket']='Error';
			}
		}
		$this->orderid($dt['kode']);
		echo json_encode($cb);
	}
	
	function orderid($id_user)
	{
		global $con;
		$send = mysqli_query($con, "INSERT INTO idorder(object,id_user) 
		VALUES ('0','$id_user')");
	}
	
	function del_user()
	{
		$post = Security::sanitize($_POST);
		$id = $post['kode'] ?? null;
		$cb['stat'] = 0;
		$cb['ket'] = '';

		if (!$id) {
			$cb['ket'] = 'ID pengguna tidak valid';
			echo json_encode($cb);
			return;
		}

		$user = db_prepared_assoc("SELECT * FROM users_log WHERE id = ?", [$id], "i");
		if (!$user) {
			$cb['ket'] = 'Pengguna tidak ditemukan';
			echo json_encode($cb);
			return;
		}

		$sql = "UPDATE users_log SET is_deleted = 1, deleted_at = NOW() WHERE id = ?";
		$res = db_prepared_query($sql, [$id], "i");

		if ($res) {
			audit_log('SOFT_DELETE', 'users_log', $id, "Soft delete user pengurus: {$user['username']} ({$user['nama_us']})");
			$cb['stat'] = 1;
			$cb['ket'] = 'Pengguna berhasil dihapus (Soft Delete)';
		} else {
			$cb['ket'] = 'Gagal menghapus pengguna';
		}
		echo json_encode($cb);
	}

	function form_akses_jenjang()
	{
		//JENJANG
		$res = db_query("SELECT * FROM data_jenjang");
		$jenjang = "<option value='all'>-pilih-</option>";
		while($dt=mysqli_fetch_array($res))
		{
			$jenjang .= "<option value='$dt[id_jenjang]'>$dt[nama_jenjang]</option>";
		}
		
		//USER
		$res = db_query("SELECT * FROM users_log ORDER BY nama_us");
		$user = "<option value='all'>-pilih-</option>";
		while($dt=mysqli_fetch_array($res))
		{
			$user .= "<option value='$dt[id]'>$dt[nama_us]</option>";
		} 
		
		$data['aj_jenjang']=$jenjang;
		$data['aj_kode']=$user;
		echo json_encode($data);
	}
	
	function data_akses_jenjang()
	{
		$kode=$_POST['kode'];
		$where = '';
		if($kode!='all')
		{
			$where = "WHERE id_user = '$kode'";
		}
		$sql = "SELECT
					id_user_jenjang,
					id_user,
					nama_us,
					nama_jenjang 
				FROM
					data_user_jenjang
					INNER JOIN users_log ON users_log.id = data_user_jenjang.id_user
					INNER JOIN data_jenjang ON data_jenjang.id_jenjang = data_user_jenjang.id_jenjang
				$where";
		$res = db_query($sql);
		$tbody = '';
		$no=1;
		while($dt = mysqli_fetch_array($res))
		{
			$tbody .= "
				<tr>
					<td>".$no++."</td>
					<td>$dt[nama_us]</td>
					<td>$dt[nama_jenjang]</td>
					<td>
						<button class='btn btn-danger btn-sm' onclick='del_akses_jenjang($dt[id_user_jenjang]);' title='Hapus Akses'>
							<i class='bx bx-trash'></i>
						</button>
					</td>
				</tr>
			";
		}
		if($no==1)
		{
			$tbody .= "
				<tr>
					<td class='tc' colspan='4'>Tidak Ada Data</td>
				</td>
			";
		}
		$table = "
			<table class='table table-sm table-bordered'>
			<thead class='table-light'>
				<tr>
					<th>No.</th>
					<th>Nama Pengguna</th>
					<th>Wilayah</th>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
	}
	
	function save_akses_jenjang()
	{
		$cb['stat']=0;
		$cb['ket']='';
		$kode=$_POST['kode'];
		$jenjang=$_POST['jenjang'];
		$cdt = db_get("SELECT 
							COUNT(*) 
						FROM 
							data_user_jenjang 
						WHERE 
							id_jenjang = '$jenjang' 
							AND id_user = '$kode'");
		if($cdt>0)
		{
			$cb['stat']=1;
			$cb['ket']='Akses Jenjang sudah ditambahkan sebelumnya';
		}
		else
		{
			$sql = "INSERT INTO 
						data_user_jenjang(
							id_user,
							id_jenjang
						) 
					VALUES 
						(
							'$kode',
							'$jenjang'
						)";
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
	
	function del_akses_jenjang()
	{
		$id_akses = $_POST['id_akses'];
		$cb['stat']=0;
		$cb['ket']='';
		$sql = "DELETE FROM data_user_jenjang WHERE id_user_jenjang=$id_akses";
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
}
?>