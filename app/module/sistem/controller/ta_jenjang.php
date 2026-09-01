<?php

class TA_Jenjang extends Controller{
	function tampil_data()
	{
		global $con;

		// Ensure is_active column exists
		if ($con) {
			@mysqli_query($con, "ALTER TABLE data_tahunajar ADD COLUMN is_active TINYINT(1) DEFAULT 0");
			$cActive = db_get("SELECT COUNT(*) FROM data_tahunajar WHERE is_active = 1");
			if ($cActive == 0) {
				@mysqli_query($con, "UPDATE data_tahunajar SET is_active = 1 ORDER BY tgl_mulai DESC LIMIT 1");
			}
		}

		$query = "SELECT * FROM data_tahunajar ORDER BY tgl_mulai DESC";
		$res = db_query($query);
		$tbody = '';
		$no = 1;
		if ($res) {
			while ($dt = mysqli_fetch_array($res))
			{
				$isActive = ($dt['is_active'] == 1);
				$statusBadge = $isActive 
					? "<span class='badge bg-success font-size-11'><i class='bx bx-check me-1'></i>Aktif</span>" 
					: "<span class='badge bg-secondary font-size-11'>Tidak Aktif</span>";

				$btnAktif = $isActive
					? "<button class='btn btn-xs btn-success' disabled title='Periode ini sedang aktif'><i class='bx bx-check'></i> Aktif</button>"
					: "<button class='btn btn-xs btn-outline-primary' onclick='set_aktif_ta($dt[id_tahunajar])' title='Jadikan Periode Aktif'><i class='bx bx-power-off'></i> Aktifkan</button>";

				$tglMulai = !empty($dt['tgl_mulai']) ? date('d-m-Y', strtotime($dt['tgl_mulai'])) : '-';
				$tglSelesai = !empty($dt['tgl_selesai']) ? date('d-m-Y', strtotime($dt['tgl_selesai'])) : '-';

				$tbody .= "<tr>
								<td class='text-center text-muted'>".$no++."</td>
								<td class='fw-semibold text-dark'>".$dt['nama_tahunajar']."</td> 
								<td class='text-muted font-size-12'>".$tglMulai."</td>
								<td class='text-muted font-size-12'>".$tglSelesai."</td>
								<td class='text-center'>".$statusBadge."</td>
								<td class='text-center'>
									<div class='d-flex gap-1 justify-content-center'>
										$btnAktif
										<button class='btn btn-xs btn-dark' onclick=modal_ta(2,'$dt[id_tahunajar]') title='Edit'>
											<i class='bx bx-edit'></i>
										</button>
										<button class='btn btn-xs btn-danger' onclick=del_ta('$dt[id_tahunajar]') title='Hapus'>
											<i class='bx bx-trash'></i>
										</button>
									</div>
								</td>
						</tr>";
			}
		}

		if (empty($tbody)) {
			$tbody = "<tr><td colspan='6' class='text-center py-3 text-muted'>Belum ada data periode operasional</td></tr>";
		}

		$table = "
			<div class='table-responsive'>
				<table class='table table-sm table-hover align-middle mb-0' style='font-size:13px;'>
					<thead class='table-light text-muted font-size-11 text-uppercase'>
					<tr>
						<th width='5%' class='text-center'>No</th>
						<th>Nama Periode</th>
						<th width='15%'>Mulai</th>
						<th width='15%'>Selesai</th>
						<th width='15%' class='text-center'>Status</th>
						<th width='22%' class='text-center'>Aksi</th>
					</tr>
					</thead>
					<tbody>
						$tbody
					</tbody>
				</table>
			</div>";
		echo $table;
	}
	
	function tampil_data2()
	{
		global $con;

		// Ensure is_active column exists
		if ($con) {
			@mysqli_query($con, "ALTER TABLE data_jenjang ADD COLUMN is_active TINYINT(1) DEFAULT 0");
			$cActive = db_get("SELECT COUNT(*) FROM data_jenjang WHERE is_active = 1");
			if ($cActive == 0) {
				@mysqli_query($con, "UPDATE data_jenjang SET is_active = 1 ORDER BY id_jenjang ASC LIMIT 1");
			}
		}

		$query = "SELECT * FROM data_jenjang ORDER BY nama_jenjang ASC";
		$res = db_query($query); 
		$tbody = '';
		$no = 1;
		if ($res) {
			while ($dt = mysqli_fetch_array($res))
			{
				$isActive = ($dt['is_active'] == 1);
				$statusBadge = $isActive 
					? "<span class='badge bg-success font-size-11'><i class='bx bx-check me-1'></i>Aktif</span>" 
					: "<span class='badge bg-secondary font-size-11'>Tidak Aktif</span>";

				$btnAktif = $isActive
					? "<button class='btn btn-xs btn-success' disabled title='Wilayah ini sedang aktif'><i class='bx bx-check'></i> Aktif</button>"
					: "<button class='btn btn-xs btn-outline-primary' onclick='set_aktif_jenjang($dt[id_jenjang])' title='Jadikan Wilayah Aktif'><i class='bx bx-power-off'></i> Aktifkan</button>";

				$tbody .= "<tr>
								<td class='text-center text-muted'>".$no++."</td>
								<td class='fw-semibold text-dark'>".$dt['nama_jenjang']."</td> 
								<td class='text-center'>".$statusBadge."</td>
								<td class='text-center'>
									<div class='d-flex gap-1 justify-content-center'>
										$btnAktif
										<button class='btn btn-xs btn-dark' onclick=modal_jenjang(2,'$dt[id_jenjang]') title='Edit'>
											<i class='bx bx-edit'></i>
										</button>
										<button class='btn btn-xs btn-danger' onclick=del_jenjang('$dt[id_jenjang]') title='Hapus'>
											<i class='bx bx-trash'></i>
										</button>
									</div>
								</td>
						</tr>";
			}
		}

		if (empty($tbody)) {
			$tbody = "<tr><td colspan='4' class='text-center py-3 text-muted'>Belum ada data wilayah</td></tr>";
		}

		$table = "
			<div class='table-responsive'>
				<table class='table table-sm table-hover align-middle mb-0' style='font-size:13px;'>
					<thead class='table-light text-muted font-size-11 text-uppercase'>
					<tr>
						<th width='5%' class='text-center'>No</th>
						<th>Nama Wilayah</th>
						<th width='20%' class='text-center'>Status</th>
						<th width='30%' class='text-center'>Aksi</th>
					</tr>
					</thead>
					<tbody>
						$tbody
					</tbody>
				</table>
			</div>";
		echo $table;
	}

	function set_aktif_ta()
	{
		global $con;
		$id = (int)($_POST['kode'] ?? 0);
		$cb = ['stat' => 0, 'ket' => ''];

		if ($id > 0) {
			@mysqli_query($con, "UPDATE data_tahunajar SET is_active = 0");
			$res = @mysqli_query($con, "UPDATE data_tahunajar SET is_active = 1 WHERE id_tahunajar = '$id'");
			if ($res) {
				$nm = db_get("SELECT nama_tahunajar FROM data_tahunajar WHERE id_tahunajar = '$id'");
				@audit_log('UPDATE', 'sistem_periode', $id, "Mengaktifkan periode: $nm");
				$cb['stat'] = 1;
				$cb['ket'] = "Periode $nm berhasil diaktifkan";
			} else {
				$cb['ket'] = 'Gagal mengaktifkan periode';
			}
		}
		echo json_encode($cb);
	}

	function set_aktif_jenjang()
	{
		global $con;
		$id = (int)($_POST['kode'] ?? 0);
		$cb = ['stat' => 0, 'ket' => ''];

		if ($id > 0) {
			@mysqli_query($con, "UPDATE data_jenjang SET is_active = 0");
			$res = @mysqli_query($con, "UPDATE data_jenjang SET is_active = 1 WHERE id_jenjang = '$id'");
			if ($res) {
				$nm = db_get("SELECT nama_jenjang FROM data_jenjang WHERE id_jenjang = '$id'");
				@audit_log('UPDATE', 'sistem_wilayah', $id, "Mengaktifkan wilayah: $nm");
				$cb['stat'] = 1;
				$cb['ket'] = "Wilayah $nm berhasil diaktifkan";
			} else {
				$cb['ket'] = 'Gagal mengaktifkan wilayah';
			}
		}
		echo json_encode($cb);
	}

	function get_ta()
	{
		$kode = (int)($_POST['kode'] ?? 0);
		$data = db_assoc("SELECT * FROM data_tahunajar WHERE id_tahunajar = $kode LIMIT 1");
		echo json_encode($data);
	}

	function get_jenjang()
	{
		$kode = (int)($_POST['kode'] ?? 0);
		$data = db_assoc("SELECT * FROM data_jenjang WHERE id_jenjang = $kode LIMIT 1");
		echo json_encode($data);
	}
	
	function save_ta()
	{
		global $con;
		$dt = Security::sanitize($_POST);
		$cb = ['stat' => 0, 'ket' => ''];
		$nama = mysqli_real_escape_string($con, $dt['nama'] ?? '');
		$tgl_mulai = !empty($dt['tgl_mulai']) ? date('Y-m-d', strtotime($dt['tgl_mulai'])) : date('Y-m-d');
		$tgl_selesai = !empty($dt['tgl_selesai']) ? date('Y-m-d', strtotime($dt['tgl_selesai'])) : date('Y-m-d');
		$kode = (int)($dt['kode'] ?? 0);

		if ($dt['mode'] == 1)
		{
			$sql = "INSERT INTO data_tahunajar(nama_tahunajar, tgl_mulai, tgl_selesai, is_active) VALUES ('$nama', '$tgl_mulai', '$tgl_selesai', 0)";
		}
		else
		{
			$sql = "UPDATE data_tahunajar SET nama_tahunajar = '$nama', tgl_mulai = '$tgl_mulai', tgl_selesai = '$tgl_selesai' WHERE id_tahunajar = '$kode'";
		}

		if (isset($sql)) {
			$res = db_query($sql);
			if ($res) {
				$cb['stat'] = 1;
				$cb['ket'] = "Data periode berhasil disimpan";
			} else {
				$cb['ket'] = 'Gagal menyimpan data periode';
			}
		}
		echo json_encode($cb);
	}
	
	function save_jenjang()
	{
		global $con;
		$dt = Security::sanitize($_POST);
		$cb = ['stat' => 0, 'ket' => ''];
		$nama = mysqli_real_escape_string($con, $dt['nama_jenjang'] ?? '');
		$kode = (int)($dt['kode'] ?? 0);

		if ($dt['mode'] == 1)
		{
			$sql = "INSERT INTO data_jenjang(nama_jenjang, is_active) VALUES ('$nama', 0)";
		}
		else
		{
			$sql = "UPDATE data_jenjang SET nama_jenjang = '$nama' WHERE id_jenjang = '$kode'";
		}

		if (isset($sql)) {
			$res = db_query($sql);
			if ($res) {
				$cb['stat'] = 1;
				$cb['ket'] = "Data wilayah berhasil disimpan";
			} else {
				$cb['ket'] = 'Gagal menyimpan data wilayah';
			}
		}
		echo json_encode($cb);
	}
	
	function del_ta()
	{
		$id = (int)($_POST['kode'] ?? 0);
		$cb = ['stat' => 0, 'ket' => ''];
		$sql = "DELETE FROM data_tahunajar WHERE id_tahunajar = '$id'";
		$res = db_query($sql);
		if ($res) {
			$cb['stat'] = 1;
			$cb['ket'] = 'Periode berhasil dihapus';
		} else {
			$cb['ket'] = 'Gagal menghapus periode';
		}
		echo json_encode($cb);
	}
	
	function del_jenjang()
	{
		$id = (int)($_POST['kode'] ?? 0);
		$cb = ['stat' => 0, 'ket' => ''];
		$sql = "DELETE FROM data_jenjang WHERE id_jenjang = '$id'";
		$res = db_query($sql);
		if ($res) {
			$cb['stat'] = 1;
			$cb['ket'] = 'Wilayah berhasil dihapus';
		} else {
			$cb['ket'] = 'Gagal menghapus wilayah';
		}
		echo json_encode($cb);
	}

	function form_akses_jenjang()
	{
		$res = db_query("SELECT * FROM data_jenjang ORDER BY nama_jenjang ASC");
		$jenjang = "<option value='all'>- Pilih Wilayah -</option>";
		if ($res) {
			while ($dt = mysqli_fetch_array($res)) {
				$jenjang .= "<option value='$dt[id_jenjang]'>$dt[nama_jenjang]</option>";
			}
		}
		
		$res2 = db_query("SELECT * FROM data_tahunajar ORDER BY tgl_mulai DESC");
		$user = "<option value='all'>- Pilih Periode -</option>";
		if ($res2) {
			while ($dt = mysqli_fetch_array($res2)) {
				$user .= "<option value='$dt[id_tahunajar]'>$dt[nama_tahunajar]</option>";
			}
		}
		
		$data['aj_jenjang'] = $jenjang;
		$data['aj_kode'] = $user;
		echo json_encode($data);
	}
}
?>