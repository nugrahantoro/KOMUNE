<?php

class Event extends Controller{

	function tampil_data_event()
	{
		$dt = $_POST;
		$query = "
			SELECT
				a.*,
				b.nama_kelas,
				c.nama_us
			FROM
				tbl_master_event a
				LEFT JOIN data_kelas b ON b.id_kelas = a.akses_kelas
				INNER JOIN users_log c ON c.id = a.id_user
			WHERE
				a.id_jenjang = $dt[id_jenjang] 
				AND a.id_sesi = $dt[id_ta]  
			ORDER BY
				a.id
		";
		$res = db_query($query);
		$no = 1;
		$tbody = '';
		while($dt=mysqli_fetch_array($res))
		{
			$tanggal1 = date("Y-m-d");
			$tanggal2 = $dt['tgl_selesai'];
			if (strtotime($tanggal1) > strtotime($tanggal2)) {
				$btn = "";
			}
			else{
				$btn = "
					<button class='btn btn-xs btn-dark btn-rounded' onclick=modal_event(event,2,'$dt[id]')>
						<i class='bx bx-edit'></i> Edit
					</button>
					<button class='btn btn-xs btn-info btn-rounded' onclick=modal_cetak('$dt[id]')>
						Cetak QR Code
					</button>
				";	
			}

			if ($dt['akses_kelas'] == "all") {
				$kelas = "Semua Kelas";
			}
			else{
				$kelas = $dt['nama_kelas'];
			}
			
			$tbody.="
				<tr class='cari-item'>
					<td>".$no++."</td>
					<td class='cari-q'>".$kelas."</td>
					<td>".$dt['tid_alat']."</td>
					<td class='cari-q'>".$dt['nama_event']."</td>
					<td>".$dt['keterangan']."</td>
					<td>".date('d F Y', strtotime($dt['tgl_mulai']))." ".$dt['jam_mulai']."</td>
					<td>".date('d F Y', strtotime($dt['tgl_selesai']))." ".$dt['jam_selesai']."</td>
					<td>".date('d F Y', strtotime(substr($dt['created_at'], 0,10)))."</td>
					<td>".$dt['nama_us']."</td>
					<td>".$btn."</td>
				</tr>
			";
		}

		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr class=' tc event_no_result' style='$no_res'>
					<td colspan='9'>Tidak Ada Data</td>
				</tr>";

		$table = "
			<table class='table table-sm m-0' style='font-size: 12px;'>
				<thead>
				<tr>
					<th width='3%'>#</th>
					<th>Kelas</th>
					<th>TID</th>
					<th>Nama Event</th>
					<th>Keterangan</th>
					<th>Mulai</th>
					<th>Selesai</th>
					<th>Tgl. buat</th>
					<th>User</th>
					<th></th>
				</tr>
				</thead>
				<tbody class='table-hover'>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}

	function load_qrcode(){
		require_once('app/lib/phpqrcode/qrlib.php');

		$id = $_POST['id_event'];

		$query = "SELECT nama_event, qr_code FROM tbl_master_event WHERE id = $id";
		$res = db_query($query);
		$event = "";
		$content = 'https://www.example.com';
		while($dt=mysqli_fetch_array($res))
		{
			$event = $dt['nama_event'];
			$content = $dt['qr_code'];
		}

		$path = '/home/smartbilling/www-dev/porting/qrcodes/';
		$file = $path . str_replace(" ", "_", $event) .'.png';

		if (!is_dir($path)) {
		    mkdir($path, 0777, true);
		}

		$size = 10;

		QRcode::png($content, $file, QR_ECLEVEL_L, $size, 2);

		// Resize the QR code to 300x300 pixels
		$src = imagecreatefrompng($file);
		$dest = imagecreatetruecolor(300, 300);
		imagecopyresampled($dest, $src, 0, 0, 0, 0, 300, 300, imagesx($src), imagesy($src));
		imagepng($dest, $file);
		imagedestroy($src);
		imagedestroy($dest);

		$url_qrcode = "https://dev.sekolah-smartbilling.net/porting/qrcodes/" . str_replace(" ", "_", $event) . ".png";

		echo '
			<div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span></span> QR Code Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modal_body1">
                <center>
					<h4>'.$event.'</h4>
					<img src="https://dev.sekolah-smartbilling.net/porting/qrcodes/' . str_replace(" ", "_", $event) . '.png"/>
				<center>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_cetak_qr" onclick="download_qrcode(\'' . $url_qrcode . '\');"><span>Download</span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
		';
	}

	function tampil_data_kelas(){
		$pt = $_POST;
		$query = "
			SELECT id_kelas, nama_kelas FROM data_kelas WHERE id_sesi = $pt[id_ta] AND id_jenjang = $pt[id_jenjang] 
		";
		$res = db_query($query);
		$html = '
			<select class="form-select" id="frm_kelas">
            	<option value="all">Semua Kelas</option>
		';
		while($dt=mysqli_fetch_array($res))
		{
			$html.='
				<option value="'.$dt['id_kelas'].'">'.$dt['nama_kelas'].'</option>
			';
		}
		$html.='</select>';
		echo $html;
	}

	function get_data_event()
	{
		$id = $_POST['id_event'];
		$data = db_assoc("
			SELECT * FROM tbl_master_event WHERE id = '$id'
		");
		echo json_encode($data);
	}

	function save_event()
	{
		$id_user = $_SESSION['id'];
		$pt = $_POST;
		$cb['stat']=0;
		$cb['ket']='';
		$now = date("Y-m-d");
		if($pt['mode']==1)
		{
			$dup = db_get("SELECT COUNT(*) FROM tbl_master_event WHERE id_jenjang = '$pt[id_jenjang]' AND id_sesi = '$pt[id_ta]' AND nama_event = '$pt[nama_event]' AND DATE(created_at) = '$now'");
		    if($dup>0)
			{
				$cb['ket']="event sudah ada, gunakan nama lain";
			}
			else{
				$cek_id = "SELECT id FROM tbl_master_event ORDER BY id DESC LIMIT 1";
				$res = db_query($cek_id);
				$id = 1;
				while($dt=mysqli_fetch_array($res))
				{
					$id = $dt['id']+1;
				}

				$qr_code = $id."_".$id_user."_".str_replace(" ", "_", $pt['nama_event'])."_".$pt['tid'];

				$sql="
					INSERT INTO 
					tbl_master_event ( id_mode, id_jenjang, id_sesi, id_user, akses_kelas, nama_event, keterangan, tgl_mulai, jam_mulai, tgl_selesai, jam_selesai, tid_alat, qr_code ) 
					VALUES ( '6', '$pt[id_jenjang]', '$pt[id_ta]', '$id_user', '$pt[id_kelas]', '$pt[nama_event]', '$pt[keterangan]', '$pt[tgl_mulai]', '$pt[jam_mulai]', '$pt[tgl_selesai]', '$pt[jam_selesai]', '$pt[tid]', '$qr_code' )
				";	
			}
		}
		else{
			$qr_code = $pt['id']."_".$id_user."_".str_replace(" ", "_", $pt['nama_event'])."_".$pt['tid'];

			$sql="
				UPDATE  
					tbl_master_event
				SET 
					id_jenjang='$pt[id_jenjang]',
					id_sesi='$pt[id_ta]',
					id_user='$id_user',
					akses_kelas='$pt[id_kelas]',
					nama_event='$pt[nama_event]',
					keterangan='$pt[keterangan]',
					tgl_mulai='$pt[tgl_mulai]',
					jam_mulai='$pt[jam_mulai]',
					tgl_selesai='$pt[tgl_selesai]',
					jam_selesai='$pt[jam_selesai]',
					tid_alat='$pt[tid]',
					qr_code='$qr_code'
				WHERE
					id=$pt[id]
			";
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

	function del_event(){
		$pt = $_POST;
		db_query("UPDATE tbl_master_alat SET `default` = NULL WHERE `default` = '1' AND tid = ''");
		$sql="
			UPDATE tbl_master_alat SET `default` = NULL WHERE tid IS NULL AND `default` = '1';
		";
		$res=db_query($sql);
		if ($res) {
			$upd = db_assoc("UPDATE tbl_master_alat SET `default` = '1' WHERE id = $pt[id]");
			echo json_encode($upd);
		}
	}

	function tampil_data_event_lap()
	{
		$pt = $_POST;
		$query = "
			SELECT
				a.id,
				a.nama_event,
				a.akses_kelas
			FROM
				tbl_master_event a
			WHERE
				a.id_jenjang = $pt[id_jenjang] 
				AND a.id_sesi = $pt[id_ta]  
			ORDER BY
				a.id DESC
		";
		$res = db_query($query);
		$no = 1;
		$tbody = '';
		$hadir = 0;
		$tdkhadir = 0;
		while($dt=mysqli_fetch_array($res))
		{
			if ($dt['akses_kelas'] == "all") {
				$jml_daftar_hadir = db_get("
					SELECT
						COUNT(a.id_penempatan) AS jml
					FROM
						penempatan_siswa a
						JOIN data_kelas b ON b.id_kelas = a.id_kelas
					WHERE
						a.thn_ajar = $pt[id_ta]  
						AND b.id_jenjang = $pt[id_jenjang] 
				");
				$tap = db_get("
					SELECT COUNT(*) AS tap FROM tbl_absensi_event WHERE id_sesi = $pt[id_ta] AND id_jenjang = $pt[id_jenjang] AND id_event = $dt[id]
				");
				$hadir = $tap;
				$tdkhadir = $jml_daftar_hadir - $tap;
			}
			else{
				$jml_daftar_hadir = db_get("
					SELECT
						COUNT(a.id_penempatan) AS jml
					FROM
						penempatan_siswa a
						JOIN data_kelas b ON b.id_kelas = a.id_kelas
					WHERE
						a.thn_ajar = $pt[id_ta]
						AND b.id_jenjang = $pt[id_jenjang] 
						AND a.id_kelas IN ($dt[akses_kelas])
				");
				$tap = db_get("
					SELECT COUNT(*) AS tap FROM tbl_absensi_event WHERE id_sesi = $pt[id_ta] AND id_jenjang = $pt[id_jenjang] AND id_event = $dt[id]
				");
				$hadir = $tap;
				$tdkhadir = $jml_daftar_hadir - $tap;
			}

			$btn = "
				<button class='btn btn-xs btn-primary btn-rounded' onclick=lihat_absensi($dt[id])>
					<i class='bx bx-edit'></i> Lihat Absensi
				</button>
			";	
			
			$tbody.="
				<tr class='cari-item'>
					<td>".$no++."</td>
					<td class='cari-q'>".$dt['nama_event']."</td>
					<td>".$jml_daftar_hadir."</td>
					<td>".$hadir."</td>
					<td>".$tdkhadir."</td>
					<td>".$btn."</td>
				</tr>
			";
		}

		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr class=' tc lap_event_no_result' style='$no_res'>
					<td colspan='4'>Tidak Ada Data</td>
				</tr>";

		$table = "
			<table class='table table-sm m-0' style='font-size: 12px;'>
				<thead>
				<tr>
					<th width='3%'>#</th>
					<th>Event</th>
					<th>Daftar Hadir</th>
					<th>Hadir</th>
					<th>Tidak Hadir</th>
					<th></th>
				</tr>
				</thead>
				<tbody class='table-hover'>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}

	function tampil_data_absensi(){
		$pt = $_POST;
		$query = "
			SELECT
				a.*,
				b.nama_kelas
			FROM
				tbl_absensi_event a
				JOIN data_kelas b ON b.id_kelas = a.id_kelas
			WHERE
				a.id_event = $pt[id_event]
				AND a.id_jenjang = $pt[id_jenjang] 
				AND a.id_sesi = $pt[id_ta] 
		";
		$res = db_query($query);
		$no = 1;
		$tbody = '';
		$id_event = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$id_event = $dt['id_event'];
			$tbody.="
				<tr class='cari-item'>
					<td>".$no++."</td>
					<td>".date('d F Y', strtotime($dt['tanggal']))."</td>
					<td class='cari-q'>".$dt['nama_kelas']."</td>
					<td class='cari-q'>".$dt['nama']."</td>
					<td>".$dt['jam_masuk']."</td>
					<td>".$dt['jam_pulang']."</td>
					<td></td>
				</tr>
			";
		}
		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr class=' tc absensi_no_result' style='$no_res'>
					<td colspan='7'>Tidak Ada Data</td>
				</tr>";

		$table = "
			<button class='btn btn-sm btn-success' onclick='cetak_absensi($id_event);'>
                <i class='bx bx-printer '></i> Cetak Excel
            </button><hr>
			<table class='table table-sm m-0' style='font-size: 12px;'>
				<thead>
				<tr>
					<th width='3%'>#</th>
					<th>Tanggal</th>
					<th>Kelas</th>
					<th>Nama</th>
					<th>Masuk</th>
					<th>Pulang</th>
					<th></th>
				</tr>
				</thead>
				<tbody class='table-hover'>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}

	function cetak_absensi(){
		header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=laporan_kehadiran_event.xls");
		$pt = $_GET;

		$query = "
			SELECT
				a.*,
				b.nama_kelas,
				c.*
			FROM
				tbl_absensi_event a
				JOIN data_kelas b ON b.id_kelas = a.id_kelas
				JOIN tbl_master_event c ON c.id = a.id_event
			WHERE
				a.id_event = $pt[id_event]
				AND a.id_jenjang = $pt[id_jenjang] 
				AND a.id_sesi = $pt[id_ta] 
		";
		$res = db_query($query);
		$tbody = '';
		$no = 1;
		$event = '';
		$jml_daftar_hadir = 0;
		$hadir = 0;
		$tdkhadir = 0;
		$mulai = '';
		$selesai = '';
		while($dt=mysqli_fetch_array($res))
		{
			$event = $dt['nama_event'];
			$mulai = date('d F Y', strtotime($dt['tgl_mulai']))." ".$dt['jam_mulai'];
			$selesai = date('d F Y', strtotime($dt['tgl_selesai']))." ".$dt['jam_selesai'];

			if ($dt['akses_kelas'] == "all") {
				$jml_daftar_hadir = db_get("
					SELECT
						COUNT(a.id_penempatan) AS jml
					FROM
						penempatan_siswa a
						JOIN data_kelas b ON b.id_kelas = a.id_kelas
					WHERE
						a.thn_ajar = $pt[id_ta]  
						AND b.id_jenjang = $pt[id_jenjang] 
				");
				$tap = db_get("
					SELECT COUNT(*) AS tap FROM tbl_absensi_event WHERE id_sesi = $pt[id_ta] AND id_jenjang = $pt[id_jenjang] AND id_event = $dt[id]
				");
				$hadir = $tap;
				$tdkhadir = $jml_daftar_hadir - $tap;
			}
			else{
				$jml_daftar_hadir = db_get("
					SELECT
						COUNT(a.id_penempatan) AS jml
					FROM
						penempatan_siswa a
						JOIN data_kelas b ON b.id_kelas = a.id_kelas
					WHERE
						a.thn_ajar = $pt[id_ta]
						AND b.id_jenjang = $pt[id_jenjang] 
						AND a.id_kelas IN ($dt[akses_kelas])
				");
				$tap = db_get("
					SELECT COUNT(*) AS tap FROM tbl_absensi_event WHERE id_sesi = $pt[id_ta] AND id_jenjang = $pt[id_jenjang] AND id_event = $dt[id]
				");
				$hadir = $tap;
				$tdkhadir = $jml_daftar_hadir - $tap;
			}

			$tbody.="
				<tr>
					<td>".$no++."</td>
					<td>".date('d F Y', strtotime($dt['tanggal']))."</td>
					<td>".$dt['nama_kelas']."</td>
					<td>".$dt['nama']."</td>
					<td>".$dt['jam_masuk']."</td>
					<td>".$dt['jam_pulang']."</td>
				</tr>
			";
		}
		$table = "
			<center><h4>Laporan Absensi</h4></center>

			<table>
				<thead>
					<tr>
						<th>Nama Event</th>
						<td>:</td>
						<td>".$event."</td>
					</tr>
					<tr>
						<th> Tanggal Mulai</th>
						<td>:</td>
						<td>".$mulai."</td>
					</tr>
					<tr>
						<th>Tanggal Selesai</th>
						<td>:</td>
						<td>".$mulai."</td>
					</tr>
					<tr>
						<th>Daftar Hadir</th>
						<td>:</td>
						<td>".$jml_daftar_hadir."</td>
					</tr>
					<tr>
						<th>Hadir</th>
						<td>:</td>
						<td>".$hadir."</td>
					</tr>
					<tr>
						<th>Tidak Hadir</th>
						<td>:</td>
						<td>".$tdkhadir."</td>
					</tr>
				</thead>
			</table>

			<br>

			<table border='1'>
				<thead>
				<tr>
					<th>No</th>
					<th>Tanggal</th>
					<th>Kelas</th>
					<th>Nama Lengkap</th>
					<th>Jam Masuk</th>
					<th>Jam Pulang</th>
				</tr>
				</thead>
				<tbody>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}
}
?>