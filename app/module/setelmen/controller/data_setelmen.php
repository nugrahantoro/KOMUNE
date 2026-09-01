<?php

class Data_Setelmen extends Controller{

	function tampil_data_pembayaran()
	{
		$dt = $_POST;
		$a = $dt['tgl_awal'];
		$b = $dt['tgl_akhir'];
		$b = date('Y-m-d H:i:s', strtotime($b . ' +1 day'));

		$query = "
			SELECT
				tgl_oto,
				referensi
			FROM
				transaksi_siswa 			
			WHERE
				office NOT IN ( 'teller', 'Uang saku' ) 
				AND (tgl_oto BETWEEN '".$a."' AND '".$b."')
				AND kirim = '1' 
			GROUP BY
				referensi
			ORDER BY
				referensi DESC
		";
		$res = db_query($query);
		$tbody = '';
		$no = 1;
		$n = 0;
		$total = 0;
		$admin = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$tbody .= "
				<tr>
					<td>".$no++."</td>
					<td><center>".substr($dt['tgl_oto'], 0,10)."</center></td> 
			";

			$tbody .= "
					<td style='color: brown;'><center><b>".$dt['referensi']."</b></center>
			";

			$tbody .= "
						<hr>
						<table class='table table-sm table-bordered text-sm' style='font-size: 10px;'>
							<tr class='table-info'>
								<th><center>Nama</center></th>
								<th><center>Nomor VA</center></th>
								<th><center>Jumlah</center></th>
								<th><center>Transaksi</center></th>
								<th><center>Status Mutasi</center></th>
							</tr>
			";

			$q = "
				SELECT
					ts.tgl_oto,
					ts.referensi,
					ts.kirim,
					ts.office,
					ts.nominal_dibayarkan,
					ts.adm,
					mj.id_jenis_transaksi,
					mj.nama_transaksi,
					ds.nama_siswa,
					SUM( ts.nominal_dibayarkan ) AS nominal_dibayarkan,
					ior.data_json,
					GROUP_CONCAT( mj.nama_transaksi ) AS nm_transaksi,
					(SELECT SUM(nominal_dibayarkan) FROM transaksi_siswa WHERE referensi = '$dt[referensi]') AS tot_tal
				FROM
					transaksi_siswa ts
					JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi
					JOIN data_siswa ds ON ds.id_siswa = ts.nis_from_idsiswa
					JOIN idorder ior ON ior.id_order = ts.id_where
				WHERE
					ts.office NOT IN ( 'teller', 'Uang saku' ) 
					-- AND (tgl_oto BETWEEN '".$a."' AND '".$b."')
					AND ts.referensi = '".$dt['referensi']."'
					AND ts.kirim = '1'
				GROUP BY
					ts.id_where,
					DATE( ts.tgl_oto )
				ORDER BY
					ts.referensi DESC
			";
			$resp = db_query($q);
			$cek = mysqli_num_rows($resp);
			while($dts=mysqli_fetch_array($resp))
			{
				$total = $total+$dts['nominal_dibayarkan'];
				$bt = "<button class='btn btn-xs  btn-success' disabled=disabled>Sudah Diproses</button>";
				$n++;
				$data=json_decode($dts['data_json']);

				if ($data) {
					$pecah_va = substr($data->{'virtual_account'}, 0,2);

					$tbody .= "
							<tr>
								<td>".$dts['nama_siswa']."</td>
								<td>".$data->{'virtual_account'}."</td>
								<td>".number_format($dts['nominal_dibayarkan'],0,"",".")."</td>
								<td>".$dts['nm_transaksi']."</td>
								<td>".$bt."</td>
							</tr>
					";
				}
			}

			$q = "
				SELECT SUM( nominal_dibayarkan ) AS total, SUM( adm ) AS total_admin FROM transaksi_siswa WHERE referensi = '".$dt['referensi']."'
			";
			$resp = db_query($q);
			while($dts=mysqli_fetch_array($resp))
			{
				$tbody .= "
					<tr class='table-info'>
						<th colspan='2'><center>Total Setelmen</center></th>
						<th colspan='3'>".rupiah($dts['total'])."</th>
					</tr>
				";
			}

			$tbody .= "
						</table>
					</td>
			";

			$tbody .= "
				</tr>
			";
		}
		$table = "
			<button class='btn btn-success' onclick='cetak_excel_saja();'>Cetak Excel</button><br><br>
			<table class='table table-sm table-bordered text-sm'>
				<thead class='table-warning'>
				<tr>
					<th width='3%'>No.</th>
					<th width='13%'><center>Tgl. Pemindahan</center></th>
					<th><center>Nomor Referensi</center></th>
				</tr>
				</thead>
				<tbody>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}

	function cetak_excel_saja()
	{
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=laporan_harian.xls");
		global $con;
		$tgl_awal=isset($_GET['tgl_awal'])? $_GET['tgl_awal']:"";
		$tgl_akhir=isset($_GET['tgl_akhir'])? $_GET['tgl_akhir']:"";
		$tgl_akhir = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' +1 day'));

		$query = "
			SELECT
				tgl_oto,
				referensi
			FROM
				transaksi_siswa 			
			WHERE
				office NOT IN ( 'teller', 'Uang saku' ) 
				AND (tgl_oto BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."')
				AND kirim = '1' 
			GROUP BY
				referensi
			ORDER BY
				referensi DESC
		";
		$res = db_query($query);
		$tbody = '';
		$no = 1;
		$n = 0;
		$total = 0;
		$admin = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$tbody .= "
				<tr>
					<td>".$no++."</td>
					<td><center>".substr($dt['tgl_oto'], 0,10)."</center></td> 
			";

			$tbody .= "
					<td style='color: brown;'><center><b>".$dt['referensi']."</b></center>
			";

			$tbody .= "
						<hr>
						<table class='table table-sm table-bordered text-sm' style='font-size: 10px;'>
							<tr class='table-info'>
								<th><center>Nama</center></th>
								<th><center>Nomor VA</center></th>
								<th><center>Jumlah</center></th>
								<th><center>Transaksi</center></th>
								<th><center>Status Mutasi</center></th>
							</tr>
			";

			$q = "
				SELECT
					ts.tgl_oto,
					ts.referensi,
					ts.kirim,
					ts.office,
					ts.nominal_dibayarkan,
					ts.adm,
					mj.id_jenis_transaksi,
					mj.nama_transaksi,
					ds.nama_siswa,
					SUM( ts.nominal_dibayarkan ) AS nominal_dibayarkan,
					ior.data_json,
					GROUP_CONCAT( mj.nama_transaksi ) AS nm_transaksi,
					(SELECT SUM(nominal_dibayarkan) FROM transaksi_siswa WHERE referensi = '$dt[referensi]') AS tot_tal
				FROM
					transaksi_siswa ts
					JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi
					JOIN data_siswa ds ON ds.id_siswa = ts.nis_from_idsiswa
					JOIN idorder ior ON ior.id_order = ts.id_where
				WHERE
					ts.office NOT IN ( 'teller', 'Uang saku' ) 
					-- AND (tgl_oto BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."')
					AND ts.referensi = '".$dt['referensi']."'
					AND ts.kirim = '1'
				GROUP BY
					ts.id_where,
					DATE( ts.tgl_oto )
				ORDER BY
					ts.referensi DESC
			";
			$resp = db_query($q);
			$cek = mysqli_num_rows($resp);
			while($dts=mysqli_fetch_array($resp))
			{
				$total = $total+$dts['nominal_dibayarkan'];
				$bt = "<button class='btn btn-xs  btn-success' disabled=disabled>Sudah Diproses</button>";
				$n++;
				$data=json_decode($dts['data_json']);

				if ($data) {
					$pecah_va = substr($data->{'virtual_account'}, 0,2);

					$tbody .= "
							<tr>
								<td>".$dts['nama_siswa']."</td>
								<td>".$data->{'virtual_account'}."</td>
								<td>".number_format($dts['nominal_dibayarkan'],0,"",".")."</td>
								<td>".$dts['nm_transaksi']."</td>
								<td>".$bt."</td>
							</tr>
					";
				}
			}

			$q = "
				SELECT SUM( nominal_dibayarkan ) AS total, SUM( adm ) AS total_admin FROM transaksi_siswa WHERE referensi = '".$dt['referensi']."'
			";
			$resp = db_query($q);
			while($dts=mysqli_fetch_array($resp))
			{
				$tbody .= "
					<tr class='table-info'>
						<th colspan='2'><center>Total Setelmen</center></th>
						<th colspan='3'>".rupiah($dts['total'])."</th>
					</tr>
				";
			}

			$tbody .= "
						</table>
					</td>
			";

			$tbody .= "
				</tr>
			";
		}
		$table = "
			<table class='table table-sm table-bordered text-sm'>
				<thead class='table-warning'>
				<tr>
					<th width='3%'>No.</th>
					<th width='13%'><center>Tgl. Pemindahan</center></th>
					<th><center>Nomor Referensi</center></th>
				</tr>
				</thead>
				<tbody>
					$tbody
				</tbody>
			</table>";
		echo $table;

	}

	function tampil_data_saku()
	{
		$dt = $_POST;
		$a = $dt['tgl_awal'];
		$b = $dt['tgl_akhir'];
		$b = date('Y-m-d H:i:s', strtotime($b . ' +1 day'));

		$query = "
			SELECT
				tgl_oto,
				referensi
			FROM
				tbl_uangku
			WHERE
				id_tbl_status_transaksi = '1' 
				AND (tanggal BETWEEN '".$a."' AND '".$b."')
				AND kirim = '1' 
			GROUP BY
				referensi 
			ORDER BY
				referensi DESC
		";
		$res = db_query($query);
		$tbody = '';
		$no = 1;
		$n = 0;
		$total = 0;
		$total_admin = 0;
		$grandtot = 0;
		$admin = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$tbody .= "
				<tr>
					<td>".$no++."</td>
					<td><center>".substr($dt['tgl_oto'], 0,10)."</center></td> 
			";

			$tbody .= "
					<td style='color: brown;'><center><b>".$dt['referensi']."</b></center>
			";

			$tbody .= "
						<hr>
						<table class='table table-sm table-bordered text-sm' style='font-size: 10px;'>
							<tr class='table-info'>
								<th><center>Nama</center></th>
								<th><center>Nomor VA</center></th>
								<th><center>Jumlah</center></th>
								<th><center>Transaksi</center></th>
								<th><center>Status Mutasi</center></th>
							</tr>
			";

			$q = "
				SELECT
					tbl_uangku.no_virtualaccount,
					tbl_uangku.jumlah,
					tbl_uangku.adm,
					data_va_uangsaku.no_va,
					data_siswa.nama_siswa
				FROM
					tbl_uangku
					INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa
					INNER JOIN user ON user.nis_from_idsiswa = data_siswa.id_siswa
					INNER JOIN data_va_uangsaku ON data_va_uangsaku.id_user = `user`.id_user
				WHERE
					id_tbl_status_transaksi = '1' 
					-- AND (tanggal BETWEEN '".$a."' AND '".$b."')
					AND referensi = '".$dt['referensi']."'
					AND kirim = '1' 
				ORDER BY
					tbl_uangku.referensi DESC
			";
			$resp = db_query($q);
			while($dts=mysqli_fetch_array($resp))
			{
				$bt = "<button class='btn btn-xs  btn-success' disabled=disabled>Sudah Diproses</button>";
				$n++;
				if ($dts['no_virtualaccount']=="") {
					$pecah_va = substr($dts['no_va'], 0,2);
					$no_va = $dts['no_va'];
				}
				else{
					$pecah_va = substr($dts['no_virtualaccount'], 0,2);
					$no_va = $dts['no_virtualaccount'];
				}
				$tbody .= "
						<tr>
							<td>".$dts['nama_siswa']."</td>
							<td>".$no_va."</td>
							<td>".number_format($dts['jumlah'],0,"",".")."</td>
							<td>Top Up Uangku</td>
							<td>".$bt."</td>
						</tr>
				";

				$total = $total+$dts['jumlah'];
			}

			$q = "
				SELECT SUM( jumlah ) AS total, SUM( nominal_admin ) AS total_admin FROM tbl_uangku WHERE referensi = '".$dt['referensi']."'
			";
			$resp = db_query($q);
			while($dts=mysqli_fetch_array($resp))
			{
				$tbody .= "
					<tr class='table-info'>
						<th colspan='2'><center>Total Setelmen</center></th>
						<th colspan='3'>".rupiah($dts['total'])."</th>
					</tr>
				";
			}

			$tbody .= "
						</table>
					</td>
			";

			$tbody .= "
				</tr>
			";
		}
		$table = "
			<button class='btn btn-success' onclick='cetak_excel_saja_saku();'>Cetak Excel</button><br><br>
			<table class='table table-sm table-bordered text-sm'>
				<thead class='table-warning'>
				<tr>
					<th width='3%'>No.</th>
					<th width='13%'><center>Tgl. Pemindahan</center></th>
					<th><center>Nomor Referensi</center></th>
				</tr>
				</thead>
				<tbody>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}

	function cetak_excel_saja_saku()
	{
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=laporan_harian.xls");
		global $con;
		$tgl_awal=isset($_GET['tgl_awal'])? $_GET['tgl_awal']:"";
		$tgl_akhir=isset($_GET['tgl_akhir'])? $_GET['tgl_akhir']:"";
		$tgl_akhir = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' +1 day'));

		$query = "
			SELECT
				tgl_oto,
				referensi
			FROM
				tbl_uangku
			WHERE
				id_tbl_status_transaksi = '1' 
				AND (tanggal BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."')
				AND kirim = '1' 
			GROUP BY
				referensi 
			ORDER BY
				referensi DESC
		";
		$res = db_query($query);
		$tbody = '';
		$no = 1;
		$n = 0;
		$total = 0;
		$total_admin = 0;
		$grandtot = 0;
		$admin = 0;
		while($dt=mysqli_fetch_array($res))
		{
			$tbody .= "
				<tr>
					<td>".$no++."</td>
					<td><center>".substr($dt['tgl_oto'], 0,10)."</center></td> 
			";

			$tbody .= "
					<td style='color: brown;'><center><b>".$dt['referensi']."</b></center>
			";

			$tbody .= "
						<hr>
						<table class='table table-sm table-bordered text-sm' style='font-size: 10px;'>
							<tr class='table-info'>
								<th><center>Nama</center></th>
								<th><center>Nomor VA</center></th>
								<th><center>Jumlah</center></th>
								<th><center>Transaksi</center></th>
								<th><center>Status Mutasi</center></th>
							</tr>
			";

			$q = "
				SELECT
					tbl_uangku.no_virtualaccount,
					tbl_uangku.jumlah,
					tbl_uangku.adm,
					data_va_uangsaku.no_va,
					data_siswa.nama_siswa
				FROM
					tbl_uangku
					INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa
					INNER JOIN user ON user.nis_from_idsiswa = data_siswa.id_siswa
					INNER JOIN data_va_uangsaku ON data_va_uangsaku.id_user = `user`.id_user
				WHERE
					id_tbl_status_transaksi = '1' 
					-- AND (tanggal BETWEEN '".$tgl_awal."' AND '".$tgl_akhir."')
					AND referensi = '".$dt['referensi']."'
					AND kirim = '1' 
				ORDER BY
					tbl_uangku.referensi DESC
			";
			$resp = db_query($q);
			while($dts=mysqli_fetch_array($resp))
			{
				$bt = "<button class='btn btn-xs  btn-success' disabled=disabled>Sudah Diproses</button>";
				$n++;
				if ($dts['no_virtualaccount']=="") {
					$pecah_va = substr($dts['no_va'], 0,2);
					$no_va = $dts['no_va'];
				}
				else{
					$pecah_va = substr($dts['no_virtualaccount'], 0,2);
					$no_va = $dts['no_virtualaccount'];
				}
				$tbody .= "
						<tr>
							<td>".$dts['nama_siswa']."</td>
							<td>".$no_va."</td>
							<td>".number_format($dts['jumlah'],0,"",".")."</td>
							<td>Top Up Uangku</td>
							<td>".$bt."</td>
						</tr>
				";

				$total = $total+$dts['jumlah'];
			}

			$q = "
				SELECT SUM( jumlah ) AS total, SUM( nominal_admin ) AS total_admin FROM tbl_uangku WHERE referensi = '".$dt['referensi']."'
			";
			$resp = db_query($q);
			while($dts=mysqli_fetch_array($resp))
			{
				$tbody .= "
					<tr class='table-info'>
						<th colspan='2'><center>Total Setelmen</center></th>
						<th colspan='3'>".rupiah($dts['total'])."</th>
					</tr>
				";
			}

			$tbody .= "
						</table>
					</td>
			";

			$tbody .= "
				</tr>
			";
		}
		$table = "
			<table class='table table-sm table-bordered text-sm'>
				<thead class='table-warning'>
				<tr>
					<th width='3%'>No.</th>
					<th width='13%'><center>Tgl. Pemindahan</center></th>
					<th><center>Nomor Referensi</center></th>
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