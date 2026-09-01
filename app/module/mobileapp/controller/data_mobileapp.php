<?php

class Data_Mobileapp extends Controller{

	function tampil_data_info()
	{
		$dt = $_POST;
		$query = "
			SELECT * FROM ref_informasi WHERE id_jenjang = '$dt[id_jenjang]' ORDER BY tgl, aktif DESC LIMIT 10
		";
		$res = db_query($query);
		$no = 1;
		$tbody = '';
		while($dt=mysqli_fetch_array($res))
		{
			if ($dt['aktif'] == "1") {
				$status = "<button type='button' class='btn btn-sm btn-info'>Aktif</button>";
				$btn = "
					<button type='button' class='btn btn-sm btn-danger' onclick='nonaktif($dt[id_informasi])'>Non-Aktifkan</button>
					<button class='btn btn-sm btn-dark' onclick='modal_slide(event,2,$dt[id_informasi]);'>
						<i class='bx bx-edit'></i> Edit
					</button>
				";
			}
			else{
				$status = "<button type='button' class='btn btn-sm btn-dark'>Non-Aktif</button>";
				$btn = "<button type='button' class='btn btn-sm btn-info' onclick='aktif($dt[id_informasi])'>Aktifkan</button>";
			}
			$tbody .= "
				<tr class='cari-item'>
					<td>".$no++."</td>
					<td width='20%'><img src='https://live.sekolah-smartbilling.net/assets/images/dev/$dt[image]' class='img-responsive' alt='$dt[judul]' width='200px'></td>
					<td width='20%' class='cari-q'>".$dt['judul']."</td>
					<td>".$dt['isi_informasi']."</td>
					<td width='8%'>".$status."</td>
					<td width='18%'>".$btn."</td>
				</tr>
			";
		}

		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr class=' tc slide_no_result' style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";

		$table = "
			<table class='table table-sm m-0' style='font-size: 12px;'>
				<thead>
				<tr>
					<th width='3%'>#</th>
					<th >Gambar</th>
					<th>Judul</th>
					<th>Informasi</th>
					<th>Status</th>
					<th></th>
				</tr>
				</thead>
				<tbody class='table-hover'>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}

	function get_data_slide(){
		$pt = $_POST;
		$data = db_assoc("SELECT id_informasi, image, judul, isi_informasi FROM ref_informasi WHERE id_informasi = $pt[id_informasi]");
		echo json_encode($data);
	}

	function aktif(){
		$pt = $_POST;
		$upd = db_assoc("UPDATE ref_informasi SET aktif = '1' WHERE id_informasi = $pt[id_informasi]");
		echo json_encode($upd);
	}

	function nonaktif(){
		$pt = $_POST;
		$upd = db_assoc("UPDATE ref_informasi SET aktif = '0' WHERE id_informasi = $pt[id_informasi]");
		echo json_encode($upd);
	}

	function save_slide()
	{
		$judul_gambar  = $_POST['judul'];
		$isi_informasi = $_POST['informasi'];
		$mode          = $_POST['mode'];
		$id_informasi  = $_POST['id_informasi'];
		$id_jenjang    = $_POST['id_jenjang'];

		$response = array(
			'stat' => 0,
			'ket'  => "Gagal"
		);

		// =========================
		// TAMBAH DATA
		// =========================
		if ($mode == "1") {

			// cek file
			if (!isset($_FILES['gambar'])) {

				echo json_encode([
					'stat' => 0,
					'ket' => 'FILES kosong',
					'files' => $_FILES
				]);

				return;
			}

			if ($_FILES['gambar']['error'] != 0) {

				echo json_encode([
					'stat' => 0,
					'ket' => 'Upload error',
					'error_code' => $_FILES['gambar']['error']
				]);

				return;
			}

			$file      = $_FILES['gambar'];
			$tmp_name  = $file['tmp_name'];
			$old_name  = $file['name'];

			$ext = strtolower(pathinfo($old_name, PATHINFO_EXTENSION));

			$allowed = array('jpg', 'jpeg', 'png');

			// validasi extension
			if (!in_array($ext, $allowed)) {

				$response = array(
					'stat' => 0,
					'ket'  => "Format gambar harus JPG, JPEG, atau PNG"
				);

				echo json_encode($response);
				return;
			}

			// nama file baru
			$filename = 'informasi_' . date('YmdHis') . '_' . rand(100,999) . '.' . $ext;

			// lokasi upload
			$upload_path = "/home/smartbilling/www-live/assets/images/dev/";
			$target_file = $upload_path . $filename;

			// upload file
			if (move_uploaded_file($tmp_name, $target_file)) {

				$tanggal = date('Y-m-d');

				$query = "
					INSERT INTO ref_informasi
					(
						judul,
						image,
						tgl,
						nama,
						aktif,
						isi_informasi,
						id_jenjang
					)
					VALUES
					(
						'$judul_gambar',
						'$filename',
						'$tanggal',
						'$filename',
						'1',
						'$isi_informasi',
						'$id_jenjang'
					)
				";

				$proses = db_query($query);

				if ($proses) {

					$response = array(
						'stat' => 1,
						'ket'  => "Informasi berhasil ditambah"
					);

				} else {

					$response = array(
						'stat' => 0,
						'ket'  => "Gagal simpan database"
					);

				}

			} else {

				$response = array(
					'stat' => 0,
					'ket'  => "Gagal upload gambar"
				);

			}

		}

		// =========================
		// EDIT DATA
		// =========================
		else {

			$upd = "
				UPDATE ref_informasi
				SET
					judul = '$judul_gambar',
					isi_informasi = '$isi_informasi'
				WHERE id_informasi = '$id_informasi'
			";

			$proses = db_query($upd);

			if ($proses) {

				$response = array(
					'stat' => 1,
					'ket'  => "Informasi berhasil diubah"
				);

			} else {

				$response = array(
					'stat' => 0,
					'ket'  => "Informasi gagal diubah"
				);

			}
		}

		echo json_encode($response);
	}

	function del_slide(){
		$pt = $_POST;

		$query = "SELECT image FROM ref_informasi WHERE id_informasi = $pt[id_informasi]";
		$res = db_query($query);
		$gambar = '';
		while($dt=mysqli_fetch_array($res)){
			$gambar = $dt['image'];
		}

		$del = "DELETE FROM ref_informasi WHERE id_informasi = $pt[id_informasi]";
		$proses = db_query($del);
		if ($proses) {
			$path = '/home/smartbilling/www-live/assets/images/dev/'.$gambar;
			if (file_exists($path)) {
				unlink($path);
			}

			$response =  array(
	        	'stat'	=> 1,
	        	'ket' 	=> "Informasi berhasil dihapus"
	        );
		}
		else{
			$response =  array(
	        	'stat'	=> 0,
	        	'ket' 	=> "Informasi gagal dihapus"
	        );
		}
		echo json_encode($response);
	}
}
?>