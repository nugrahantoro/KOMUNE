<?php

class Konfig_Bayar extends Controller
{

	function load_kelas()
	{
		$pt = $_POST;
		$data = '';
		//$level = "AND level = $pt[id_level]";
		if ($pt['id_level'] = "all") {
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
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$no++;
			$tbody .= "
				<tr class='cari-item kelas-item' id_kelas='$dt[id_kelas]' onclick='kb_click_kelas(this);'>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_kelas]</td>
					<td class='tc'>$dt[jumlah_siswa]</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc kb_kelas_no_result'  style='$no_res'>
					<td colspan='5'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Nama Wilayah</th>
					<th>Jumlah Warga</th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
	}

	function load_list_trans()
	{
		$pt = $_POST;
		$data = [];

		///TABEL PER SISWA
		$kelas = "AND data_kelas.id_kelas = $pt[id_kelas]";
		if ($pt['id_kelas'] == "all") {
			$kelas = '';;
		}
		$id_jenis_saku = db_get("SELECT id_jenis_transaksi FROM master_jenis_transaksi WHERE nama_transaksi='Uang Saku'");
		$sql = "SELECT
					data_siswa.id_siswa,
					data_siswa.nama_siswa,
					data_siswa.gender,
					nama_kelas,
					COUNT(*)-1 as jml_ketetapan
				FROM
					data_siswa
					LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN  data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas
					LEFT JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
					LEFT JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi
				WHERE
					data_kelas.id_jenjang = $pt[id_jenjang]
					$kelas
					AND master_alokasi_transaksi_all.id_sesi = $pt[id_ta]
				GROUP BY id_siswa
				ORDER BY 
					nama_kelas, nama_siswa";
		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$no++;
			$tbody .= "
				<tr class='cari-item siswa-item' id='tr_$dt[id_siswa]'>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td>$dt[gender]</td>
					<td>$dt[nama_kelas]</td>
					<td class='tc'>$dt[jml_ketetapan]</td>
					<td  class='tc'>
						<button class='btn btn-xs btn-dark'  onclick='modal_show_trans($dt[id_siswa],0);'>
							<i class='bx bx-show '></i> 
						Lihat</button>
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc list_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Nama Warga</th>
					<th>Gender</th>
					<th>Wilayah</th>
					<th>Total Iuran</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		$data['per_siswa'] = $table;

		///TABEL PER SISWA
		$kelas = "AND master_alokasi_transaksi_all.id_kelas = $pt[id_kelas]";
		if ($pt['id_kelas'] == "all") {
			$kelas = '';;
		}
		$id_jenis_saku = db_get("SELECT id_jenis_transaksi FROM master_jenis_transaksi WHERE nama_transaksi='Uang Saku'");
		$sql = "SELECT
					master_alokasi_transaksi_all.id_jenis_transaksi,
					nama_transaksi,
					nama_kelas,
					data_kelas.id_kelas,
					count( id_master_trans_sis ) AS jumlah 
				FROM
					master_alokasi_transaksi_all
					INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
					INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
					INNER JOIN data_kelas ON data_kelas.id_kelas = master_alokasi_transaksi_all.id_kelas 
				WHERE
					nama_transaksi NOT LIKE '%Uang Saku%'
					$kelas
				GROUP BY
					master_alokasi_transaksi_all.id_jenis_transaksi 
				ORDER BY
				master_alokasi_transaksi_all.id_jenis_transaksi ASC";
		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		while ($dt = mysqli_fetch_array($res)) {
			$no++;
			$tbody .= "
				<tr class='cari-item siswa-item' id_jns_trans='tr_$dt[id_jenis_transaksi]'>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_transaksi]</td>
					<td>$dt[nama_kelas]</td>
					<td class='tc'>$dt[jumlah]</td>
					<td  class='tc'>
						<button class='btn btn-xs btn-dark'  onclick='modal_show_trans(0,$dt[id_jenis_transaksi]);'>
							<i class='bx bx-show '></i> 
						Lihat</button>
					</td>
				</tr>";
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc list_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Jenis Iuran</th>
					<th>Wilayah</th>
					<th>Total Iuran (warga)</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		$data['per_jns_trans'] = $table;

		echo json_encode($data);
	}

	function load_trans_view()
	{
		$pt = $_POST;
		$data = [];
		$kelas = "AND data_kelas.id_kelas = $pt[id_kelas]";
		if ($pt['id_kelas'] == "all") {
			$kelas = '';;
		}
		$id_siswa = '';
		if ($pt['id_siswa'] != 0) {
			$id_siswa = "AND data_siswa.id_siswa = $pt[id_siswa]";
		}
		$id_jns_trans = '';
		if ($pt['id_jns_trans'] != 0) {
			$id_jns_trans = "AND master_alokasi_transaksi_all.id_jenis_transaksi = $pt[id_jns_trans]";
		}
		$id_jenis_saku = db_get("SELECT id_jenis_transaksi FROM master_jenis_transaksi WHERE nama_transaksi='Uang Saku'");
		$sql = "SELECT
					master_alokasi_transaksi_all.id_master_trans_sis as id_mst_trans,
					data_siswa.id_siswa,
					data_siswa.nama_siswa,
					data_siswa.gender,
					nama_kelas,
					nama_transaksi,
					nominal,
					DATE( master_alokasi_transaksi_all.tgl_jth ) AS tgl_jatuh_tempo,
					SUM(IFNULL(nominal_dibayarkan,0)) as nominal_dibayarkan
				FROM
					data_siswa
					LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN  data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas
					LEFT JOIN master_alokasi_transaksi_all ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
					LEFT JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi
					LEFT JOIN transaksi_siswa ON transaksi_siswa.id_master_trans_sis = master_alokasi_transaksi_all.id_master_trans_sis
				WHERE
					data_kelas.id_jenjang = $pt[id_jenjang]
					AND master_alokasi_transaksi_all.id_jenis_transaksi != $id_jenis_saku
					$id_siswa
					$id_jns_trans
					$kelas
					AND master_alokasi_transaksi_all.id_sesi = $pt[id_ta]
				GROUP BY master_alokasi_transaksi_all.id_master_trans_sis
				ORDER BY 
					nama_kelas, nama_siswa";
		//echo $sql; die;
		$res = db_query($sql);
		$tbody = '';
		$no = 0;
		$trans = [];
		while ($dt = mysqli_fetch_array($res)) {
			$no++;
			$dt['tgl_jatuh_tempo'] = tgl_indo($dt['tgl_jatuh_tempo']);
			$hps = '';
			if ($dt['nominal_dibayarkan'] == 0) {
				$hps = "
						<button class='btn btn-xs btn-danger' onclick='kb_del_trans($dt[id_mst_trans]);'> 
								<i class='bx bx-trash'></i>
						</button>";
			}
			$tbody .= "
				<tr class='cari-item trans-item' id_trans='$dt[id_mst_trans]'>
					<td class='tc'>
						<input type='checkbox' class='chk_item' id_trans='$dt[id_mst_trans]' >
					</td>
					<td class='tc'>$no</td>
					<td  class='cari-q'>$dt[nama_siswa]</td>
					<td>$dt[gender]</td>
					<td>$dt[nama_kelas]</td>
					<td  class='cari-q'>$dt[nama_transaksi]</td>
					<td style='text-align:right;'>" . rupiah($dt['nominal']) . "</td>
					<td class='tc'>" . $dt['tgl_jatuh_tempo'] . "</td>
					<td  class='' style='white-space:nowrap;'>
						<button class='btn btn-xs btn-dark' onclick='kb_edit_trans($dt[id_mst_trans]);'>
							<i class='bx bx-edit '></i> 
						edit</button>
						$hps
					</td>
				</tr>";
			$trans[$dt['id_mst_trans']] = $dt;
		}

		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class=' tc list_no_result'  style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th class='tc'>
						<input type='checkbox'>
					</th>
					<th>#</th>
					<th>Nama Warga</th>
					<th>Gender</th>
					<th>Wilayah</th>
					<th>Nama Iuran</th>
					<th>Nominal Iuran</th>
					<th>Batas Pembayaran</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		$data['trans_siswa'] = $table;
		$data['trans'] = $trans;
		echo json_encode($data);
	}

	function edit_ketetapan_nom_sis()
	{
		die;
		$pt = $_POST;
		$ids = $pt['id_mst_trans'];
		$nom = $pt['nominal'];
		$tam = 0;
		$arr = explode(",", $ids);
		$all = "";
		$table = '';
		$data = [];
		if ($nom != '') {
			$record = "
			<label style='bold'>Pastikan cetak report sebelum pop up diclose</label><br>
			<button class='btn btn-primary btn-xs' onclick='cetak_report();'> Cetak Report</button><br><br>
			<div id='hasil_report'>
			<table id='tabsex'  border='1' style='padding:2px;'>
				<tr>
					<th width='5%'>No</th>
					<th>Nama Siswa</th>
					<th>Nama Transaksi</th>
					<th>Ketetapan Sebelum</th>
					<th>Ketetapan Sesudah</th>
					<th>Nominal Yang Sudah Dibayarkan</th>
					<th width='10%'>Sisa</th>
					<th width='25%'>Keterangan</th>
				</tr>
			";
			for ($i = 0; $i < count($arr); $i++) {
				$cek = mysqli_query($con, "SELECT
					nominal,
					nama_siswa,
					nama_transaksi
				FROM
					master_alokasi_transaksi_all
					INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
					INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa 
				WHERE
					id_master_trans_sis =$arr[$i]");
				$cl = mysqli_fetch_row($cek);
				$nom_sebelum = $cl[0];
				$nomor = $i + 1;
				$record .= "
					<tr>
						<td>$nomor</td>
						<td>$cl[1]</td>
						<td>$cl[2]</td>
						<td>$cl[0]</td>
						<td>$nom</td>
			";

				if ($nom > $nom_sebelum) {
					$sta = "";
					$que = mysqli_query($con, "SELECT
					SUM( nominal_dibayarkan ) AS byr
				FROM
					transaksi_siswa 
				WHERE
					id_master_trans_sis =$arr[$i] ");
					$sh = mysqli_fetch_row($que);
					//nominal ketetapan lebih dari ketetapan seblumnya == SEMUA BELUM LUNAS
					$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom',sta=null WHERE id_master_trans_sis=$arr[$i]");
					if ($upd) {
						$tam++;
						$sta = "Berhasil [ Belum Lunas ]";
					} else {
						$sta = "Gagal";
					}

					$record .= "
					<td>$sh[0]</td>
					<td> - </td>
					<td>$sta</td>
				";
				} else {
					$sta = "";
					$que = mysqli_query($con, "SELECT
					SUM( nominal_dibayarkan ) AS byr,
					nis_from_idsiswa,
					nama_transaksi,
					office
				FROM
					transaksi_siswa 
				INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
				WHERE
					id_master_trans_sis =$arr[$i] ");
					$sh = mysqli_fetch_row($que);
					$nm = floatval($sh[0]);
					$id_sis = $sh[1];
					$nama_tr = "Sisa pembayaran " . $sh[2];
					$sisa = $nm - $nom;

					if ($sisa > 0) {
						$sisauang = $sisa;
					} else {
						$sisauang = " - ";
					}

					$record .= "
							<td>$nm</td>
							<td>$sisauang</td>
						";

					if ($nm > $nom) {
						if ($sh[3] != 'teller') {
							$record .= "
									<td><label style='background-color:red;color:white;'>Tidak diupdate - Pembayaran Online [ Nominal tidak boleh diupdate kurang dari yang sudah dibayarkan ] </label> </td>
								";
						} else {
							//nominal bayar lebih dari nominal ketetapan == LUNAS SISA
							$sql = mysqli_query($con, "SELECT (nominal_dibayarkan) as byr,id_transaksi FROM transaksi_siswa WHERE id_master_trans_sis=$arr[$i] ORDER BY tgl DESC");
							$count = mysqli_num_rows($sql);
							$perbedaan = $nm - $nom;
							$kn = 0;
							while ($rs = mysqli_fetch_array($sql)) {
								$nominal = $rs['byr'];
								$kurang = $perbedaan - $nominal;
								if ($kurang > 0 || $kurang == 0) {
									//hapus transaksi pembayaran
									$hps = mysqli_query($con, "DELETE FROM transaksi_siswa WHERE id_transaksi=$rs[id_transaksi]");
								} else {
									//nominal transaksi dikurangi
									$krng = mysqli_query($con, "UPDATE transaksi_siswa SET nominal_dibayarkan=nominal_dibayarkan-$perbedaan WHERE id_transaksi=$rs[id_transaksi]");
									break;
								}
								$perbedaan = $kurang;
							}

							//insert sisa uang
							$ins = mysqli_query($con, "INSERT INTO tbl_uangku (id_siswa,id_tbl_status_transaksi,jumlah,keterangan) VALUES('$id_sis','3','$sisa','$nama_tr')");
							//update master ketetpan
							$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom',sta=1 WHERE id_master_trans_sis=$arr[$i]");

							if ($upd) {
								$sta .= "Berhasil ";
							} else {
								$sta .= "Gagal ";
							}

							if ($ins) {
								$sta .= "[Lunas, sisa bayar di masukkan ke uang saku]";
							} else {
								$sta .= "[sisa gagal masuk di uang saku]";
							}

							$record .= "
											<td>$sta</td>
										";
						}
					} else if ($nm < $nom) {
						//nominal bayar kurang dari nominal ketetapan == BELUM LUNAS
						$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom',sta=null WHERE id_master_trans_sis=$arr[$i]");
						if ($upd) {
							$record .= "
									<td>Berhasil [Belum Lunas]</td>
								";
						} else {
							$record .= "
									<td>Gagal</td>
								";
						}
					} else if ($nm == $nom) {
						//nominal bayar sama dengan nominal ketetapan == LUNAS
						$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom',sta='1' WHERE id_master_trans_sis=$arr[$i]");
						if ($upd) {
							$record .= "
									<td>Berhasil [Lunas]</td>
								";
						} else {
							$record .= "
										<td>Gagal</td>
									";
						}
					}
				}

				$table .= "</tr>";
			}

			$table .= "</table>
				</div>
			";
			$table .= $record;
		}
		$data['table'] = $table;

		//Ubah Tanggal 
		$tgl_jth = $pt['tgl_jatuh_tempo'];
		if ($tgl_jth != '') {
			$que = "UPDATE master_alokasi_transaksi_all SET tgl_jth = '$tgl_jth' WHERE id_master_trans_sis IN ($ids)";
			db_query($que);
		}
	}

	function load_add_trans_list()
	{
		$pt = $_POST;
		$sql = "SELECT
				kategori_pengeluaran.nama_kategori_pengeluaran,
				kategori_laporan.nama_kategori_laporan,
				kategori_trans.nama_kategori,
				cicil,
				master_jenis_transaksi.id_kategori_pengeluaran,
				id_jenis_transaksi,
				master_jenis_transaksi.nama_transaksi,
				tgl,
				urut,
				status_kelompok,
				id_kategori_laporan,
				kondisi_khusus,
				tgl
			FROM
				master_jenis_transaksi
				LEFT JOIN kategori_trans ON kategori_trans.id_kat_trans = master_jenis_transaksi.urut
				LEFT JOIN kategori_laporan ON kategori_laporan.id_kat_laporan = master_jenis_transaksi.id_kategori_laporan
				LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran 
			WHERE
				master_jenis_transaksi.id_sesi = $pt[id_ta] 
				AND master_jenis_transaksi.id_jenjang = $pt[id_jenjang] 
				";
		$res = db_query($sql);
		$no = 1;
		$tbody = '';
		while ($dt = mysqli_fetch_array($res)) {
			$tbody .= "
			<tr class='cari-item' id='add_trans_$dt[id_jenis_transaksi]'>
				<td class='tc'>
						<input type='checkbox' class='chk_item' id_mst_trans='$dt[id_jenis_transaksi]' >
				</td>
				<td class='chk_trigger'>" . $no++ . "</td>
				<td class='cari-q chk_trigger'>" . $dt['nama_transaksi'] . "</td>
				<td>
					<input class='form-control form-control-sm tgl_jth' type='text' value='" . tgl_indo($dt['tgl']) . "'>
				</td>
				<td>
					<input class='form-control form-control-sm nominal number' type='text'>
				</td>
			</tr>
			";
		}
		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class='tc no_result'  style='$no_res'>
					<td colspan='5'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th class='tc'>
						<input type='checkbox'>
					</th>
					<th>#</th>
					<th>Nama Iuran</th>
					<th>Batas Pembayaran</th>
					<th>Nominal Iuran</th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>
		";
		echo $table;
	}

	function load_add_siswa_list()
	{
		$pt = $_POST;
		$sql = "SELECT
					data_siswa.id_siswa,
					data_siswa.nama_siswa,
					data_siswa.gender,
					nama_kelas
				FROM
					data_siswa
					LEFT JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = data_siswa.id_siswa
					LEFT JOIN  data_kelas ON penempatan_siswa.id_kelas = data_kelas.id_kelas
				WHERE
					data_kelas.id_jenjang = $pt[id_jenjang]
					AND data_kelas.id_kelas=$pt[id_kelas]
					AND data_kelas.id_sesi = $pt[id_ta]
				ORDER BY 
					nama_kelas, nama_siswa";
		$res = db_query($sql);
		$no = 1;
		$tbody = '';
		while ($dt = mysqli_fetch_array($res)) {
			$tbody .= "
			<tr class='cari-item'>
				<td class='tc'>
						<input type='checkbox' class='chk_item' id_siswa='$dt[id_siswa]' >
				</td>
				<td>" . $no++ . "</td>
				<td class='cari-q'>" . $dt['nama_siswa'] . "</td>
				<td class='cari-q'>" . $dt['gender'] . "</td>
				<td class='cari-q'>" . $dt['nama_kelas'] . "</td>
			</tr>
			";
		}
		$no_res = "display:none;";
		if (mysqli_num_rows($res) == 0) {
			$no_res = "";
		}
		$tbody .= "<tr  class='tc no_result'  style='$no_res'>
					<td colspan='5'>Tidak Ada Data</td>
				</tr>";
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th class='tc'>
						<input type='checkbox'>
					</th>
					<th>#</th>
					<th>Nama Warga</th>
					<th>Gender</th>
					<th>Wilayah</th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>
		";
		echo $table;
	}

	function add_ketetapan()
	{
		$pt = $_POST;
		$trans = json_decode($pt['trans'], true);
		$id_mst = [];
		foreach ($trans as $dt) {
			array_push($id_mst, $dt['id_mst_trans']);
		}
		$que_chk = "SELECT 
					id_jenis_transaksi,
					nis_from_idsiswa 
				FROM 
					master_alokasi_transaksi_all 
				WHERE
					id_jenis_transaksi IN (" . implode(', ', $id_mst) . ")
					AND nis_from_idsiswa IN (" . implode(', ', $pt['id_siswa']) . ")";
		$res = db_query($que_chk);
		$exist = [];
		while ($dt = mysqli_fetch_array($res)) {
			array_push($exist, $dt['id_jenis_transaksi'] . '-' . $dt['nis_from_idsiswa']);
		}
		$values = [];
		$count = 0;
		$id_tmp = uniqid();
		foreach ($trans as $tr) {
			foreach ($pt['id_siswa'] as $id_siswa) {
				if (!in_array($tr['id_mst_trans'] . '-' . $id_siswa, $exist)) {
					$val = "(
								'$id_tmp',
								$tr[id_mst_trans],
								$pt[id_kelas],
								$id_siswa,
								$tr[nominal],
								$pt[id_ta],
								'" . tgl_indo($tr['tgl_jatuh_tempo']) . "'
							)";
					array_push($values, $val);
					$count++;
				}
			}
		}
		$values = implode(', ', $values);
		$q_insert = "
				INSERT INTO master_alokasi_transaksi_all(
					id_tmp,
					id_jenis_transaksi,
					id_kelas,
					nis_from_idsiswa,
					nominal,
					id_sesi,
					tgl_jth
				)
				VALUES 
				$values";
		if ($count > 0) {
			$res = db_query($q_insert);
		}
		$data = [];
		$data['count'] = $count;
		echo json_encode($data);

		///INSERT JURNAL AKUNTANSI
		$sql = "SELECT id_master_trans_sis FROM master_alokasi_transaksi_all WHERE id_tmp = '$id_tmp'";
		$res = db_query($sql);
		while ($dt = mysqli_fetch_array($res)) {
			$this->run_class_function("akuntansi/jurnal_create_tagihan/$dt[id_master_trans_sis]");
		}
	}

	function save_edit_trans()
	{
		global $con;
		$ids = $_POST['id_mst_trans'];
		$nom = $_POST['nominal'];
		$tgl_jth = $_POST['tgl_jatuh_tempo'];
		$tam = 0;
		$arr = explode(",", $ids);
		$all = "";

		if ($tgl_jth != '') {
			$sql = "UPDATE master_alokasi_transaksi_all SET tgl_jth = '" . tgl_indo($tgl_jth) . "' WHERE id_master_trans_sis IN ($ids)";
			db_query($sql);
		}

		if ($nom == '') {
			die;
		}

		$record = "
		<label style='bold'>Pastikan cetak report sebelum pop up diclose</label><br>
		<div class='mb-2' style='float:right;'>
			<button class='btn btn-info btn-sm' onclick='cetak_report_edit_trans();'> Cetak Report</button>
		</div>
		<div id='hasil_report_edit'>
		<table class='table table-sm table-bordered' border='0.5' cellpadding='4px'>
			<tr>
				<th width='5%'>No</th>
				<th>Nama Siswa</th>
				<th>Nama Transaksi</th>
				<th>Ketetapan Sebelum</th>
				<th>Ketetapan Sesudah</th>
				<th>Nominal Yang Sudah Dibayarkan</th>
				<th>Sisa</th>
				<th>Keterangan</th>
			</tr>
		";
		for ($i = 0; $i < count($arr); $i++) {
			$cek = mysqli_query($con, "SELECT
				nominal,
				nama_siswa,
				nama_transaksi
			FROM
				master_alokasi_transaksi_all
				INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
				INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa 
			WHERE
				id_master_trans_sis =$arr[$i]");
			$cl = mysqli_fetch_row($cek);
			$nom_sebelum = $cl[0];
			$nomor = $i + 1;
			$record .= "
				<tr>
					<td>$nomor</td>
					<td>$cl[1]</td>
					<td>$cl[2]</td>
					<td>$cl[0]</td>
					<td>$nom</td>
		";

			/* echo "
				<hr>
				nom = $nom, $nom_sebelum";
			die; */

			if ($nom > $nom_sebelum) {
				$sta = "";
				$que = mysqli_query($con, "SELECT
				SUM( nominal_dibayarkan ) AS byr
			FROM
				transaksi_siswa 
			WHERE
				id_master_trans_sis =$arr[$i] ");
				$sh = mysqli_fetch_row($que);
				//nominal ketetapan lebih dari ketetapan seblumnya == SEMUA BELUM LUNAS
				$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom',sta=null WHERE id_master_trans_sis=$arr[$i]");
				if ($upd) {
					$tam++;
					$sta = "Berhasil [ Belum Lunas ]";
				} else {
					$sta = "Gagal";
				}

				$record .= "
				<td>$sh[0]</td>
				<td> - </td>
				<td>$sta</td>
			";
			} else {

				$sta = "";
				$que = mysqli_query($con, "SELECT
				SUM( nominal_dibayarkan ) AS byr,
				nis_from_idsiswa,
				nama_transaksi,
				office
			FROM
				transaksi_siswa 
			INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=transaksi_siswa.id_jenis_transaksi
			WHERE
				id_master_trans_sis =$arr[$i] ");
				$sh = mysqli_fetch_row($que);
				$nm = floatval($sh[0]);
				$id_sis = $sh[1];
				$nama_tr = "Sisa pembayaran " . $sh[2];
				$nama_ambil = "Pengembalian dari pembayaran " . $sh[2];
				$sisa = $nm - $nom;

				if ($sisa > 0) {
					$sisauang = $sisa;
				} else {
					$sisauang = " - ";
				}

				$record .= "
						<td>$nm</td>
						<td>$sisauang</td>
					";

				if ($nm > $nom) {
					if ($sh[3] != 'teller') {
						$sql = mysqli_query($con, "SELECT (nominal_dibayarkan) as byr,id_transaksi,tgl_oto,referensi,kirim,mutasi FROM transaksi_siswa WHERE id_master_trans_sis=$arr[$i] ORDER BY tgl DESC");
						$count = mysqli_num_rows($sql);
						$perbedaan = $nm - $nom;
						$kn = 0;
						$referensi = '';
						$tgl_oto = '';
						$kirimin = '';
						$mutasiin = '';
						while ($rs = mysqli_fetch_array($sql)) {
							$nominal = $rs['byr'];
							$tgl_oto = $rs['tgl_oto'];
							$referensi = $rs['referensi'];
							$kirimin = $rs['kirim'];
							$mutasiin = $rs['mutasi'];
							$kurang = $perbedaan - $nominal;
							if ($kurang > 0 || $kurang == 0) {
								//hapus transaksi pembayaran
								$hps = mysqli_query($con, "DELETE FROM transaksi_siswa WHERE id_transaksi=$rs[id_transaksi]");
							} else {
								//nominal transaksi dikurangi
								$krng = mysqli_query($con, "UPDATE transaksi_siswa SET nominal_dibayarkan=nominal_dibayarkan-$perbedaan WHERE id_transaksi=$rs[id_transaksi]");
								break;
							}
							$perbedaan = $kurang;
						}

						//insert sisa uang
						if (is_null($tgl_oto)) {
							$tgl_oto = 'NULL';
						} else {
							$tgl_oto = "'$tgl_oto'";
						}
						if (is_null($kirimin)) {
							$kirimin = 'NULL';
						} else {
							$kirimin = "'$kirimin'";
						}
						echo $que = "INSERT INTO tbl_uangku (id_siswa,id_tbl_status_transaksi,jumlah,keterangan,tgl_oto,referensi,kirim,mutasi) 
								VALUES('$id_sis','3','$sisa','$nama_tr',$tgl_oto,'$referensi',$kirimin,'$mutasiin')";
						$ins = mysqli_query($con, $que);
						//update master ketetpan
						$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom',sta=1 WHERE id_master_trans_sis=$arr[$i]");

						if ($upd) {
							$sta .= "Berhasil ";
						} else {
							$sta .= "Gagal ";
						}

						if ($ins) {
							$sta .= "[Lunas, sisa bayar di masukkan ke uang saku]";
						} else {
							$sta .= "[sisa gagal masuk di uang saku]";
						}

						$record .= "
                                        <td>$sta</td>
                                    ";
					} else {

						//nominal bayar lebih dari nominal ketetapan == LUNAS SISA
						$sql = mysqli_query($con, "SELECT (nominal_dibayarkan) as byr,id_transaksi FROM transaksi_siswa WHERE id_master_trans_sis=$arr[$i] ORDER BY tgl DESC");
						$count = mysqli_num_rows($sql);
						$perbedaan = $nm - $nom;
						$kn = 0;
						while ($rs = mysqli_fetch_array($sql)) {
							$nominal = $rs['byr'];
							$kurang = $perbedaan - $nominal;
							if ($kurang > 0 || $kurang == 0) {
								//hapus transaksi pembayaran
								$hps = mysqli_query($con, "DELETE FROM transaksi_siswa WHERE id_transaksi=$rs[id_transaksi]");
							} else {
								//nominal transaksi dikurangi
								$krng = mysqli_query($con, "UPDATE transaksi_siswa SET nominal_dibayarkan=nominal_dibayarkan-$perbedaan WHERE id_transaksi=$rs[id_transaksi]");
								break;
							}
							$perbedaan = $kurang;
						}

						//insert sisa uang
						$ins = mysqli_query($con, "INSERT INTO tbl_uangku (id_siswa,id_tbl_status_transaksi,jumlah,keterangan) VALUES('$id_sis','3','$sisa','$nama_tr')");
						//update master ketetpan
						$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom',sta=1 WHERE id_master_trans_sis=$arr[$i]");

						if ($upd) {
							$sta .= "Berhasil ";
						} else {
							$sta .= "Gagal ";
						}

						if ($ins) {
							$sta .= "[Lunas, sisa bayar di masukkan ke uang saku]";
						} else {
							$sta .= "[sisa gagal masuk di uang saku]";
						}

						$record .= "
										<td>$sta</td>
									";
					}
				} else if ($nm < $nom) {
					//nominal bayar kurang dari nominal ketetapan == BELUM LUNAS
					$ambil_saku = mysqli_query($con, "SELECT jumlah FROM tbl_uangku WHERE id_siswa='$id_sis' AND keterangan = '$nama_tr'");
					$jumlah_saku = '';
					$jumlah_transaksi = 0;
					while ($sk = mysqli_fetch_array($ambil_saku)) {
						$jumlah_saku = $sk['jumlah'];
					}
					ob_start();
					$ambil_transaksi = mysqli_query($con, "SELECT nominal_dibayarkan FROM transaksi_siswa WHERE id_master_trans_sis=$arr[$i]");
					while ($ks = mysqli_fetch_array($ambil_transaksi)) {
						$jumlah_transaksi = $ks['nominal_dibayarkan'];
					}
					$jumlah_semua = $jumlah_transaksi + $jumlah_saku;
					ob_end_clean();
					$upd_1 = mysqli_query($con, "UPDATE transaksi_siswa SET nominal_ketetapan='$jumlah_semua', nominal_dibayarkan='$jumlah_semua' WHERE id_master_trans_sis=$arr[$i]");
					$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom' WHERE id_master_trans_sis=$arr[$i]");
					if ($upd) {
						$record .= "
								<td>Berhasil [Belum Lunas]</td>
							";
					} else {
						$record .= "
								<td>Gagal</td>
							";
					}
					$rubah_uang = mysqli_query($con, "DELETE FROM tbl_uangku WHERE id_siswa = '$id_sis' AND keterangan = '$nama_tr'");
				} else if ($nm == $nom) {
					//nominal bayar sama dengan nominal ketetapan == LUNAS
					$upd = mysqli_query($con, "UPDATE master_alokasi_transaksi_all SET nominal='$nom',sta='1' WHERE id_master_trans_sis=$arr[$i]");
					if ($upd) {
						$record .= "
								<td>Berhasil [Lunas]</td>
							";
					} else {
						$record .= "
									<td>Gagal</td>
								";
					}
				}
			}

			$record .= "</tr>";

			///AKUNTANSI UPDATE KETETAPAN
			$this->run_class_function("akuntansi/jurnal_update_tagihan/$arr[$i]");
		}

		$record .=  "</table>
			</div>
		";
		echo $record;
	}

	function cetak_hasil_report()
	{
		ob_start();
		global $con;
		require_once('app/lib/tcpdf/tcpdf.php');
		$id = isset($_GET['data']) ? $_GET['data'] : "";
		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006f', PDF_HEADER_STRING);
		// set header and footer fonts
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		// set auto page breaks
		$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf = new TCPDF('P', 'mm', array(330, 215));
		$pdf->SetPrintHeader(false);
		$pdf->SetPrintFooter(false);

		$pdf->AddPage();
		$tangg = date("d-m-Y");
		$pdf->SetXY(10, 0);
		$pdf->SetFont('Times', '', 11);
		$pdf->Ln(2);
		$html = "<table>
	<tr>
		REPORT EDIT NOMINAL KETETAPAN [ $tangg ]
	</tr>
</table>";

		$pdf->writeHTML($html, true, false, true, false, '');
		$style = array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
		$stylet = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));

		$pdf->Line(10, 10, 210, 10, $style);
		$pdf->Line(10, 10, 210, 10, $stylet);
		$pdf->Ln(12);
		$pdf->writeHTML($id, true, false, true, false, '');
		ob_end_clean();
		$pdf->Output('Laporan_edit_nominal.pdf', 'I');
	}

	function del_trans()
	{
		global $con;
		$ids = $_POST['id_trans'];
		$sdh = 0;
		$idall = "";
		$qrf = mysqli_query($con, "SELECT id_master_trans_sis id FROM transaksi_siswa WHERE id_master_trans_sis IN($ids)");
		$hi = mysqli_num_rows($qrf);

		$qry = mysqli_query($con, "SELECT GROUP_CONCAT(id_master_trans_sis) as id FROM transaksi_siswa WHERE id_master_trans_sis IN($ids)");

		if ($hi > 0) {
			while ($rs = mysqli_fetch_array($qry)) {
				$sdh = $rs['id'];
			}
			$ar = "HAVING id NOT IN ($sdh)";
		} else {
			$ar = "ORDER BY id ASC";
		}


		$qr = mysqli_query($con, "
		SELECT
			GROUP_CONCAT(id) as semua
		FROM
			(
		SELECT
			tbl_ketetapan.id 
		FROM
			( SELECT id_master_trans_sis AS id FROM master_alokasi_transaksi_all WHERE id_master_trans_sis IN ( $ids ) ) AS tbl_ketetapan 
		$ar
			) AS tbl_all
		");

		while ($hs = mysqli_fetch_array($qr)) {
			$idall = $hs['semua'];
		}

		$del = mysqli_query($con, "DELETE FROM master_alokasi_transaksi_all WHERE id_master_trans_sis IN ($idall)");
		$count = mysqli_affected_rows($con);

		echo "Data yang di hapus sebanyak: " . $count;

		///AKUNTANSI HAPUS KETETAPAN
		$arr_id = explode(',', $idall);
		foreach ($arr_id as $id_master_trans_sis) {
			$this->run_class_function("akuntansi/jurnal_delete_tagihan/$id_master_trans_sis");
		}
	}
}
