<?php

class Agenda extends Controller{
	
	function load_data()
	{
		global $con;

		// Auto create table if not exists
		if ($con) {
			@mysqli_query($con, "CREATE TABLE IF NOT EXISTS agenda_warga (
				id_agenda INT AUTO_INCREMENT PRIMARY KEY,
				judul VARCHAR(255) NOT NULL,
				deskripsi TEXT NULL,
				kategori VARCHAR(100) NULL,
				tanggal DATE NULL,
				lokasi VARCHAR(255) NULL,
				status VARCHAR(50) DEFAULT 'aktif',
				id_sesi INT NULL,
				id_jenjang INT NULL,
				created_at DATETIME DEFAULT CURRENT_TIMESTAMP
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		}

		$dt = $_POST;
		$id_ta = $dt['id_ta'] ?? '';
		$id_jenjang = $dt['id_jenjang'] ?? '';

		$where = [];
		if (!empty($id_ta)) {
			$where[] = "id_sesi = '$id_ta'";
		}
		if (!empty($id_jenjang)) {
			$where[] = "id_jenjang = '$id_jenjang'";
		}
		$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

		$query = "
			SELECT 
				id_agenda,
				judul,
				kategori,
				tanggal,
				lokasi,
				deskripsi,
				status
			FROM agenda_warga 
			$whereSql
			ORDER BY tanggal DESC, created_at DESC
			LIMIT 50
		";

		$res = db_query($query);

		$no = 1;
		$tbody = '';

		if ($res) {
			while ($row = mysqli_fetch_array($res)) {

				$status = "";

				if ($row['status'] == 'aktif') {
					$status = "<span class='badge bg-success'>Aktif</span>";
				} elseif ($row['status'] == 'selesai') {
					$status = "<span class='badge bg-secondary'>Selesai</span>";
				} else {
					$status = "<span class='badge bg-warning text-dark'>Draft</span>";
				}

				$btn = "
					<div class='d-flex gap-1'>
						<button class='btn btn-sm btn-primary' onclick='modal_agenda(event,2,$row[id_agenda]);'>
							<i class='bx bx-edit'></i>
						</button>
						<button class='btn btn-sm btn-danger' onclick='del_agenda($row[id_agenda]);'>
							<i class='bx bx-trash'></i>
						</button>
					</div>
				";

				$tglFormatted = !empty($row['tanggal']) ? date('d M Y', strtotime($row['tanggal'])) : '-';

				$tbody .= "
					<tr class='cari-item'>
						<td>$no</td>

						<td class='cari-q'>
							<div class='fw-semibold text-dark'>$row[judul]</div>
							<small class='text-muted'>$row[kategori]</small>
						</td>

						<td>$row[deskripsi]</td>

						<td>$tglFormatted</td>

						<td>$row[lokasi]</td>

						<td>$status</td>

						<td width='12%'>$btn</td>
					</tr>
				";

				$no++;
			}
		}

		$no_res = "display:none;";

		if (!$res || mysqli_num_rows($res) == 0) {
			$no_res = "";
		}

		$tbody .= "
			<tr class='tc agenda_no_result' style='$no_res'>
				<td colspan='7' class='text-center py-4 text-muted'>
					Belum ada agenda warga yang tercatat
				</td>
			</tr>
		";

		$table = "
			<div class='table-responsive'>
				<table class='table table-hover align-middle mb-0' style='font-size:14px;'>

					<thead class='table-light'>
						<tr>
							<th width='4%'>#</th>
							<th>Agenda Warga</th>
							<th>Kegiatan</th>
							<th width='14%'>Tanggal</th>
							<th width='18%'>Lokasi</th>
							<th width='10%'>Status</th>
							<th width='12%'>Aksi</th>
						</tr>
					</thead>

					<tbody>
						$tbody
					</tbody>

				</table>
			</div>
		";

		echo $table;
	}

	function save_agenda()
	{
		global $con;
		$pt = Security::sanitize($_POST);

		$judul = mysqli_real_escape_string($con, $pt['judul'] ?? '');
		$deskripsi = mysqli_real_escape_string($con, $pt['kegiatan'] ?? '');
		$kategori = mysqli_real_escape_string($con, $pt['kategori'] ?? '');
		$tanggal = mysqli_real_escape_string($con, $pt['tanggal'] ?? date('Y-m-d'));
		$lokasi = mysqli_real_escape_string($con, $pt['lokasi'] ?? '');
		$status = mysqli_real_escape_string($con, $pt['status'] ?? 'aktif');
		$id_ta = mysqli_real_escape_string($con, $pt['id_ta'] ?? '1');
		$id_jenjang = mysqli_real_escape_string($con, $pt['id_jenjang'] ?? '1');

		if ($pt['mode'] == "1")
		{
			$query = "
				INSERT INTO agenda_warga
				(
					judul,
					deskripsi,
					kategori,
					tanggal,
					lokasi,
					status,
					id_sesi,
					id_jenjang,
					created_at
				)
				VALUES
				(
					'$judul',
					'$deskripsi',
					'$kategori',
					'$tanggal',
					'$lokasi',
					'$status',
					'$id_ta',
					'$id_jenjang',
					NOW()
				)
			";

			$proses = db_query($query);
			$newId = mysqli_insert_id($con);
			@audit_log('CREATE', 'agenda_warga', $newId, "Menambahkan agenda: $judul");

			$response = $proses ? ['stat' => 1, 'ket' => "Agenda warga berhasil ditambahkan"] : ['stat' => 0, 'ket' => "Gagal menambahkan agenda"];
		}
		else
		{
			$id_agenda = (int)($pt['id_agenda'] ?? 0);
			$upd = "
				UPDATE agenda_warga 
				SET 
					judul 			= '$judul',
					deskripsi 		= '$deskripsi',
					kategori 		= '$kategori',
					tanggal 		= '$tanggal',
					lokasi 	  		= '$lokasi',
					status 			= '$status'
				WHERE id_agenda = '$id_agenda'
			";

			$proses = db_query($upd);
			@audit_log('UPDATE', 'agenda_warga', $id_agenda, "Memperbarui agenda: $judul");

			$response = $proses ? ['stat' => 1, 'ket' => "Agenda warga berhasil diperbarui"] : ['stat' => 0, 'ket' => "Gagal memperbarui agenda"];
		}

		echo json_encode($response);
	}

	function del_agenda()
	{
		$pt = Security::sanitize($_POST);
		$id_agenda = (int)($pt['id_agenda'] ?? 0);

		$del = "DELETE FROM agenda_warga WHERE id_agenda = '$id_agenda'";
		$proses = db_query($del);
		@audit_log('DELETE', 'agenda_warga', $id_agenda, "Menghapus agenda warga ID $id_agenda");

		$response = $proses ? ['stat' => 1, 'ket' => "Agenda warga berhasil dihapus"] : ['stat' => 0, 'ket' => "Gagal menghapus agenda"];
		echo json_encode($response);
	}

	function get_data_agenda()
	{
		$pt = Security::sanitize($_POST);
		$id_agenda = (int)($pt['id_agenda'] ?? 0);
		$data = db_assoc("SELECT * FROM agenda_warga WHERE id_agenda = '$id_agenda'");
		echo json_encode($data);
	}
}

?>