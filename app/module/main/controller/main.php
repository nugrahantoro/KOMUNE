<?php
class Main extends Controller
{
    var $content;

    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Beranda";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("main/view/template");
        $this->view("main/view/footer");
    }

    function page_not_found()
    {
        $this->view("main/view/not_found");
    }

    function rubah_edit()
    {
        $dt = Security::sanitize($_POST);
        $sesi = $_SESSION['id'] ?? null;
        if (!$sesi) {
            echo json_encode(['stat' => 0, 'ket' => 'Sesi tidak valid']);
            return;
        }

        $user = db_prepared_assoc("SELECT * FROM users_log WHERE id = ?", [$sesi], "i");
        if ($user && Security::verify_password($dt['lama'] ?? '', $user['password'])) {
            $newHash = Security::hash_password($dt['baru'] ?? '');
            db_prepared_query("UPDATE users_log SET password = ? WHERE id = ?", [$newHash, $sesi], "si");
            audit_log('UPDATE', 'auth', $sesi, "Pengguna mengubah kata sandi akun");
            $response = ['stat' => 1, 'ket' => "Kata sandi berhasil diperbarui"];
        } else {
            $response = ['stat' => 0, 'ket' => "Kata sandi lama tidak sesuai"];
        }
        echo json_encode($response);
    }

    function get_beranda()
    {
        $gt = $_GET;
		$all=false;
		$elem = $gt['elem'];
		if($elem=='')
		{
			$all = true;
		}
		
		//Tabel Transaksi Va
		if($elem=='div_data_va' || $all)
		{
			$dt["div_data_va"] = $this->data_va(1);
		}
		
		//Tabel Transaksi Saku
		if($elem=='div_data_saku' || $all)
		{
			$dt["div_data_saku"] = $this->data_saku(1);
		}
		
		//Tabel Transaksi Bulan
		if($elem=='div_trn_bulan' || $all)
		{
			$dt["div_trn_bulan"] = $this->bulan(1);
		}
		
		//Chart Bulan
		if($elem=='div_pers_bulan' || $all)
		{
			$dt["div_pers_bulan"] = $this->chart_bulan(1);
		}
		
		//Donut Bulan
		if($elem=='div_donut_bulan' || $all)
		{
			$dt["div_donut_bulan"] = $this->donut_bulan(1);
		}
		
		if($all)
		{
			$dt['div_trn_hari'] = $this->hari(1);
			$dt['div_trn_tahun'] = $this->tahun(1);
			$dt['div_pers_tahun'] = $this->chart_tahun(1);
			$dt['div_donut_tahun'] = $this->donut_tahun(1);
		}
		
        echo json_encode($dt);
    }

    function data_va($return = 0)
    {
        global $con;
        $tgl = isset($_GET["data_va_tgl"]) ? $_GET["data_va_tgl"] : "";
        $id_jenjang = isset($_GET["id_jenjang"]) ? $_GET["id_jenjang"] : "";

        $sql = "
			SELECT 
				ts.*,
				mj.id_jenis_transaksi,
				mj.nama_transaksi,
				ds.nama_siswa,
				SUM(ts.nominal_dibayarkan) as nominal_dibayarkan,
				ior.data_json,
				GROUP_CONCAT(mj.nama_transaksi) as nm_transaksi,
				la.refnum
			FROM
				transaksi_siswa ts
				JOIN master_jenis_transaksi mj ON mj.id_jenis_transaksi = ts.id_jenis_transaksi
				JOIN data_siswa ds ON ds.id_siswa = ts.nis_from_idsiswa
				JOIN idorder ior ON ior.id_order = ts.id_where
				JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = ts.nis_from_idsiswa
				JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas
				LEFT JOIN callback_link_aja_agratek la ON la.id_order = ts.id_where
			WHERE
				ts.office NOT IN ('teller','Uang saku') AND
				LEFT(ts.tgl, 7)='$tgl'
				AND data_kelas.id_jenjang = '$id_jenjang'
			GROUP BY id_where,substr(ts.tgl,1,16)
			ORDER BY 	referensi DESC, ts.tgl DESC";
        $que = mysqli_query($con, $sql);
        $data = [];
        $no = 0;
        $nama = "";
        $va = "";
        $bank = "";
        while ($dt = mysqli_fetch_assoc($que)) {
            $no++;
            if (is_null($dt["referensi"])) {
                $ref = $no;
            } else {
                $ref = $dt["referensi"];
            }
            if (!isset($data[$ref])) {
                $data[$ref] = [];
                $jml[$ref] = 0;
            }
            array_push($data[$ref], $dt);
            $jml[$ref] += $dt["nominal_dibayarkan"];
        }
        $no = 1;
        $tbody = "";
        foreach ($data as $ls) {
            $rows = count($ls);
            $ref = key($data);
            next($data);

            for ($i = 0; $i < count($ls); $i++) {
                $rs = $ls[$i];
                $obj = json_decode($rs["data_json"], true);

                if ($rs["refnum"] != "") {
                	$nama = $rs["nama_siswa"];
            		//$va = "LinkAja-Pendidikan: ".$rs["refnum"];
            		$va = "LinkAja-: ".$rs["refnum"];
                }
                else{
                	$nama = $obj["customer_name"];
            		$va = $obj["virtual_account"];
            		if ($obj["type"] == "createBSI") {
            			$bank = " - (BSI)";
            		}
            		else if ($obj["type"] == "createMandiri") {
            			$bank = " - (Mandiri)";
            		}
            		else if ($obj["type"] == "createCIMB") {
            			$bank = " - (CIMB Niaga Syariah)";
            		}
            		else if ($obj["type"] == "createCring") {
            			$bank = " - (BNI x CRING)";
            		}
            		else{
            			$bank = " - (BNI)";
            		}
                }

                if ($rs["kirim"] == 4) {
                    $bt =
                        "<button class='btn btn-sm w-xs  btn-primary' disabled=disabled>Verifikasi Bank</button>";
                } elseif ($rs["kirim"] == 1) {
                    $bt =
                        "<button class='btn btn-sm w-xs btn-success' disabled=disabled>Sudah Diproses</button>";
                } else {
                    $bt =
                        "<button class='btn btn-sm w-xs  btn-danger' disabled=disabled>Belum Diproses</button>";
                }
                $tbody .="<tr>
							<td>".$no++."</td>
							<td>".$rs["tgl"]."</td>
							<td>".$nama."</td>
							<td>".$va."</td>
							<td>".number_format($rs["nominal_dibayarkan"], 0, "", ".")."</td>
							<td>".$rs["nm_transaksi"]."</td>";
                if ($i == 0) {
                    $tbody .=
                        "
						<td rowspan='$rows'>".$rs["referensi"]."</td>
						<td rowspan='$rows'>".number_format($jml[$ref], 0, "", ".")."</td>";
                }
                $tbody .=
                    "<td>".$bt."</td>
						</tr>";
            }
        }

        $table =
         '
		  <div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">

					<table class="table table-bordered table-sm" width="100%" style="font-size:12px;" border=1>
						<thead class="table-light">
							<tr>
								<th width="3%">No</th>
								<th width="15%">Tanggal</th>
								<th width="20%">Nama</th>
								<th>Nomor Pembayaran</th>
								<th>Jumlah</th>
								<th>Transaksi</th>
								<th>No.Referensi</th>
								<th>Jumlah Total</th>
								<th>Status Transaksi</th>
							</tr>
						<thead>
			' .
            "$tbody 
					</table>
			</div>
		";
        if ($return == 1) {
            return $table;
        } else {
            echo $table;
        }
        mysql_close($con);
    }

    function data_saku($return=0)
    {
        global $con;
        $tgl = get('data_saku_tgl');
        $id_jenjang = isset($_GET["id_jenjang"]) ? $_GET["id_jenjang"] : "";
        $sql = "
					SELECT
						id_tbl_uangku,
						jumlah,
						tanggal,
						nama_siswa,
						no_va,
						no_virtualaccount,
						fee,
						bank,
						jasa,
						kirim,
						referensi,
						jumlah+IFNULL(fee,0)+IFNULL(bank,0)+IFNULL(jasa,0) as total 
					FROM
						tbl_uangku
						INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa
						INNER JOIN `user` ON `user`.nis_from_idsiswa = data_siswa.id_siswa
						INNER JOIN data_va_uangsaku ON data_va_uangsaku.id_user = `user`.id_user
						LEFT JOIN biaya_admin ON biaya_admin.id_biaya_admin = tbl_uangku.id_biaya_admin 
						INNER JOIN penempatan_siswa ON penempatan_siswa.nis_from_idsiswa = tbl_uangku.id_siswa
						INNER JOIN data_kelas ON data_kelas.id_kelas = penempatan_siswa.id_kelas
					WHERE
						id_tbl_status_transaksi = '1' AND
						LEFT(tbl_uangku.tanggal, 7)='$tgl'
						AND data_va_uangsaku.non_aktif IS NULL
						AND data_kelas.id_jenjang = '$id_jenjang'
					ORDER BY 	referensi DESC, tbl_uangku.tanggal DESC";
					// echo $sql;
        $que = mysqli_query($con, $sql);
        $data = [];
        $no = 0;
        while ($dt = mysqli_fetch_assoc($que)) {
            $no++;
            if (is_null($dt["referensi"])) {
                $ref = $no;
            } else {
                $ref = $dt["referensi"];
            }
            if (!isset($data[$ref])) {
                $data[$ref] = [];
                $jml[$ref] = 0;
            }
            array_push($data[$ref], $dt);
            $jml[$ref] += $dt["jumlah"];
        }
        $no = 1;
        $tbody = "";
        foreach ($data as $ls) {
            $rows = count($ls);
            $ref = key($data);
            next($data);
            for ($i = 0; $i < count($ls); $i++) {
                $rs = $ls[$i];
                if ($rs["kirim"] == 4) {
                    $bt =
                        "<button class='btn btn-sm  btn-primary' disabled=disabled>Verifikasi Bank</button>";
                } elseif ($rs["kirim"] == 1) {
                    $bt =
                        "<button class='btn btn-sm btn-success' disabled=disabled>Sudah Diproses</button>";
                } else {
                    $bt =
                        "<button class='btn btn-sm  btn-danger' disabled=disabled>Belum Diproses</button>";
                }
                $tbody .=
                    "
				<tr>
					<td>".$no++."</td>
					<td>".$rs["tanggal"]."</td>
					<td>".$rs["nama_siswa"]."</td>";
                if ($rs["no_virtualaccount"] != "") {
                    $tbody .= "<td>" .$rs["no_virtualaccount"] ."</td>";
                } else {
                    $tbody .= "<td>" .$rs["no_va"] ."</td>";
                }
                $tbody .=
                    "
					<td>".number_format($rs["jumlah"], 0, "", ".")."</td>
					<td>Top Up Dompet Digital</td>";
                if ($i == 0) {
                    $tbody .=
                        "
				<td rowspan='$rows'>".$rs["referensi"]."</td>
				<td rowspan='$rows'>".number_format($jml[$ref], 0, "", ".")."</td>";
                }
                $tbody .=
                    "<td>".$bt."</td>
				</tr>";
            }
        }
        $table =
            '
          <div style="max-height:350px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:scroll;">

                    <table class="table table-bordered table-sm" width="100%" style="font-size:12px;">
                        <tr>
                            <th width="3%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="20%">Nama</th>
                            <th>Virtual Account</th>
                            <th>Jumlah</th>
                            <th>Deskripsi Transaksi</th>
							<th>No.Referensi</th>
							<th>Total Transaksi</th>
                            <th>Status Transaksi</th>
                        </tr>' ."$tbody </table>";
		if ($return == 1) {
            return $table;
        } else {
            echo $table;
        }
        mysql_close($con);
    }
	
	function hari($return=0)
	{
		$a= date('Y-m-d');
		$id_tahunajar=get('id_th');
		$id_jenjang=get('id_jenjang');
		$elem ='';
		$sql= "
				(
				SELECT
					0 as id,
					'Terima Tunai' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel,
					SUM( infaq ) AS inf
				FROM
					transaksi_siswa
					INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa 
				WHERE
					office = 'teller' 
					AND LEFT ( transaksi_siswa.tgl, 10 ) = '$a' 
					AND transaksi_siswa.thn_aj = '$id_tahunajar' 
					AND data_siswa.id_jenjang = '$id_jenjang' 
				)
				UNION ALL
				(
				SELECT
					1 as id,
					'Bayar Klaim Uang Saku' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel,
					SUM( infaq ) AS inf 
				FROM
					transaksi_siswa
					INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa 
				WHERE
					office = 'Uang Saku' 
					AND LEFT ( transaksi_siswa.tgl, 10 ) = '$a' 
					AND transaksi_siswa.thn_aj = '$id_tahunajar' 
					AND data_siswa.id_jenjang = '$id_jenjang'
				)
				UNION ALL
				(
				SELECT
					2 as id,
					'Bayar Online' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel,
					SUM( infaq ) AS inf 
				FROM
					transaksi_siswa
					INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa 
				WHERE
					office = 'bank' 
					AND LEFT ( transaksi_siswa.tgl, 10 ) = '$a' 
					AND transaksi_siswa.thn_aj = '$id_tahunajar' 
					AND data_siswa.id_jenjang = '$id_jenjang'
				)
				UNION ALL
				(
				SELECT
					3 as id,
					'Uang Saku Masuk Online' as nama,
					SUM( jumlah ) AS jum_us_tel,
					null as inf
				FROM
					tbl_uangku
					INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa 
				WHERE
					id_tbl_status_transaksi = 1 
					AND LEFT ( tanggal, 10 ) = '$a' 
					AND data_siswa.id_jenjang = '$id_jenjang'
				)
				UNION ALL
				(
				SELECT
					4 as id,
					'Uang Saku Masuk Offline' as nama,
					SUM( jumlah ) AS jum_us_tel,
					null as inf
				FROM
					tbl_uangku 
					INNER JOIN data_siswa ON data_siswa.id_siswa=tbl_uangku.id_siswa
				WHERE
					id_tbl_status_transaksi = 3 
					AND LEFT ( tanggal, 10 ) = '$a' 
					AND data_siswa.id_jenjang='$id_jenjang'
				)
				UNION ALL
				(
				SELECT
					5 as id,
					'Setor Tabungan' as nama,
					SUM(nominal_tab) as jum_us_tel,
					null as inf
				FROM
					tabungan 
					INNER JOIN data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa
				WHERE
					LEFT ( tabungan.tgl, 10 ) = '$a' 
					AND data_siswa.id_jenjang='$id_jenjang'
				)
				UNION ALL
				(
				SELECT
					6 as id,
					'Penarikan Tabungan' as nama,
					SUM(nominal_amb) as jum_us_tel,
					null as inf
				FROM
					tabungan 
					INNER JOIN data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa
				WHERE
					LEFT ( tabungan.tgl, 10 ) = '$a'
					AND data_siswa.id_jenjang='$id_jenjang'
				)
				UNION ALL
				(
				SELECT 
					7 as id,
					'Pengeluaran Kas' as nama,
					SUM(pengeluaran_kas.nominal_pengeluaran) as jum_us_tel,
					null as inf
				FROM 
					pengeluaran_kas 
				WHERE 
					LEFT(pengeluaran_kas.tanggal, 10)='$a' 
					AND id_sesi='$id_tahunajar' 
					AND id_jenjang='$id_jenjang'
				)
				";
		$res = db_query($sql);
		$cls=[
				'table-warning',
				'table-warning',
				'table-warning',
				'table-warning',
				'table-warning',
				'table-danger',
				'table-danger',
				'table-warning'
			];
		$tbody = '';
		while($dt=mysqli_fetch_array($res))
		{
			$id=$dt['id'];
			$tbody.="
			<tr class='$cls[$id]'>
				<td>$dt[nama]</td>
				<td>Rp".rupiah($dt['jum_us_tel'])."</td>
			</tr>
			";
		}
		$table="
			<table class='table table-sm'>
			<tbody>
				$tbody
			</tbody>
			</table>
		";
		$elem.=$table;
		if($return==1)
		{
			return $elem;
		}
		else
		{
			echo $elem;
		}
	}

	function bulan($return=1)
	{
		$elem='';
		$tgl=get('trn_bulan_tgl');
		$id_tahunajar=get('id_th');
		$id_jenjang=get('id_jenjang');
		$b= $tgl;
		$bln=floatval(substr($tgl,5,2));
		$x=$bln-1;
		$sql = "
			(
				SELECT
					1 as id,
					'Terima Tunai' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel,
					SUM( infaq ) AS inf 
				FROM
					transaksi_siswa
					INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa 
				WHERE
					office = 'teller' 
					AND LEFT ( transaksi_siswa.tgl, 7 ) = '$b' 
					AND transaksi_siswa.thn_aj = '$id_tahunajar' 
					AND data_siswa.id_jenjang = '$id_jenjang'
			)
			UNION ALL
			(		
				SELECT
					2 as id,
					'Bayar Klaim Uang Saku' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel,
					SUM( infaq ) AS inf 
				FROM
					transaksi_siswa
					INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa 
				WHERE
					office = 'Uang Saku' 
					AND LEFT ( transaksi_siswa.tgl, 7 ) = '$b' 
					AND transaksi_siswa.thn_aj = '$id_tahunajar' 
					AND data_siswa.id_jenjang = '$id_jenjang'
			)
			UNION ALL
			(
				SELECT
					3 as id,
					'Bayar Online' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel,
					SUM( infaq ) AS inf 
				FROM
					transaksi_siswa
					INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa 
				WHERE
					office = 'bank' 
					AND LEFT ( transaksi_siswa.tgl, 7 ) = '$b' 
					AND transaksi_siswa.thn_aj = '$id_tahunajar' 
					AND data_siswa.id_jenjang = '$id_jenjang'
			)
			UNION ALL
			(
				SELECT
					4 as id,
					'Uang Saku Masuk Online' as nama,
					SUM( jumlah ) AS jum_us_tel ,
					NULL AS inf 
				FROM
					tbl_uangku
					INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa 
				WHERE
					id_tbl_status_transaksi = 1 
					AND LEFT ( tanggal, 7 ) = '$b' 
					AND data_siswa.id_jenjang = '$id_jenjang'
			)
			UNION ALL
			(
				SELECT
					5 as id,
					'Uang Saku Masuk Offline' as nama,
					SUM( jumlah ) AS jum_us_tel ,
					NULL AS inf 
				FROM
					tbl_uangku
					INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa 
				WHERE
					id_tbl_status_transaksi = 3 
					AND LEFT ( tanggal, 7 ) = '$b' 
					AND data_siswa.id_jenjang = '$id_jenjang'
			)
			UNION ALL
			(
				SELECT
					6 as id,
					'Setor Tabungan' as nama,
					SUM(nominal_tab) as jum_us_tel,
					NULL as inf
				FROM
					tabungan 
					INNER JOIN data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa
				WHERE
					LEFT ( tabungan.tgl, 7 ) = '$b' 
					AND data_siswa.id_jenjang='$id_jenjang'
			)
			UNION ALL
			(
				SELECT
					7 as id,
					'Penarikan Tabungan' as nama,
					SUM(nominal_amb) as jum_us_tel,
					NULL as inf
				FROM
					tabungan 
					INNER JOIN data_siswa ON data_siswa.id_siswa=tabungan.nis_from_idsiswa
				WHERE
					LEFT ( tabungan.tgl, 7 ) = '$b' 
					AND data_siswa.id_jenjang='$id_jenjang'
			)
			UNION ALL
			(		
				SELECT 
					8 as id,
					'Pengeluaran Kas' as nama,
					SUM(pengeluaran_kas.nominal_pengeluaran) as jum_us_tel ,
					NULL as inf
				FROM pengeluaran_kas 
				WHERE 
					LEFT(pengeluaran_kas.tanggal, 7)='$b' 
					AND id_sesi='$id_tahunajar' 
					AND id_jenjang='$id_jenjang'
			)
		";
		$query_qr="
				SELECT
					tb_ketetapan.*,
					tb_bayar.*,
					tb_ketetapan.ketetapan - IFNULL( tb_bayar.bayar, 0 ) AS tagihan,
					ROUND((tb_bayar.bayar/tb_ketetapan.ketetapan)*100,2) AS persentase
				FROM
					(
						SELECT
							SUM( nominal ) AS ketetapan,
							master_alokasi_transaksi_all.id_sesi
						FROM
							master_alokasi_transaksi_all
							INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi 
						WHERE
							LEFT(master_alokasi_transaksi_all.tgl_jth, 7)='$b' AND master_jenis_transaksi.id_jenjang='$id_jenjang'
					) AS tb_ketetapan
					LEFT JOIN (
						SELECT
							SUM( nominal_dibayarkan ) AS bayar,
							master_alokasi_transaksi_all.id_sesi
						FROM
							transaksi_siswa
							INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis = transaksi_siswa.id_master_trans_sis 
							INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi=master_alokasi_transaksi_all.id_jenis_transaksi
						WHERE
							LEFT(master_alokasi_transaksi_all.tgl_jth, 7)='$b' AND master_jenis_transaksi.id_jenjang='$id_jenjang'
					) AS tb_bayar ON tb_bayar.id_sesi = tb_ketetapan.id_sesi
				";
			$est=db_assoc($query_qr);
			$res = db_query($sql);
			$cls=[
					'',
					'table-warning',
					'table-warning',
					'table-warning',
					'table-warning',
					'table-warning',
					'table-danger',
					'table-danger',
					'table-warning'
				];
			$tbody = '';
			while($dt=mysqli_fetch_array($res))
			{
				$id=$dt['id'];
				$tbody.="
				<tr class='$cls[$id]'>
					<td>$dt[nama]</td>
					<td>Rp".rupiah($dt['jum_us_tel'])."</td>
				</tr>
				";
			}
			$table="
				<table class='table table-sm'>
				<tbody>
					$tbody
					<tr>
						<td>Estimasi</td>
						<td>Rp".rupiah($est['ketetapan'])."</td>
					</tr>
					<tr>
						<td>Dibayar</td>
						<td>Rp".rupiah($est['bayar'])."</td>
					</tr>
					<tr>
						<td>Tagihan</td>
						<td>Rp".rupiah($est['tagihan'])."</td>
					</tr>
				</tbody>
				</table>
			";
			$elem.=$table;
		$elem .='
			<div class="progress progress-xl">
				<div class="progress-bar  progress-bar-striped  progress-bar-animated" role="progressbar" 
					style="width: '.$est['persentase'].'%;" 
					aria-valuenow="'.$est['persentase'].'" aria-valuemin="0" aria-valuemax="100">
					'.$est['persentase'].'%
				</div>
			</div>';
		if($return==1)
		{
			return $elem;
		}
		else
		{
			echo $elem;
		}
	}
	
	function tahun($return=0)
	{
		$elem='';
		$id_tahunajar=get('id_th');
		$id_jenjang=get('id_jenjang');
		$ta = db_assoc("SELECT 
							* 
						FROM 
							data_tahunajar 
						WHERE 
							id_tahunajar = $id_tahunajar");
		$tanggalawal = $ta['tgl_mulai'];
		$tanggalakhir = $ta['tgl_selesai'];
		$sql="
			(
				SELECT
					1 as id,
					'Terima Tunai' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel 
				FROM
					transaksi_siswa
					INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
				WHERE
					transaksi_siswa.tgl BETWEEN '$tanggalawal' 
					AND '$tanggalakhir' 
					AND office = 'teller'
					AND master_jenis_transaksi.id_jenjang = '$id_jenjang'
					AND transaksi_siswa.thn_aj = '$id_tahunajar'
			)
			UNION ALL
			(
				SELECT
					2 as id,
					'Bayar Klaim Uang Saku' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel 
				FROM
					transaksi_siswa
					INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
				WHERE
					transaksi_siswa.tgl BETWEEN '$tanggalawal' 
					AND '$tanggalakhir' 
					AND office = 'Uang Saku'
					AND master_jenis_transaksi.id_jenjang = '$id_jenjang'
					AND transaksi_siswa.thn_aj = '$id_tahunajar'
			)
			UNION ALL
			(
				SELECT
					3 as id,
					'Bayar Online' as nama,
					SUM( transaksi_siswa.nominal_dibayarkan ) AS jum_us_tel
				FROM
					transaksi_siswa 
					JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
				WHERE
					office NOT IN ( 'teller', 'Uang Saku' ) 
					AND transaksi_siswa.tgl BETWEEN '$tanggalawal' 
					AND '$tanggalakhir'
					AND master_jenis_transaksi.id_jenjang = '$id_jenjang'
					AND transaksi_siswa.thn_aj = '$id_tahunajar'
			)
			UNION ALL
			(
				SELECT
					4 as id,
					'Uang Saku Masuk Online' as nama,
					SUM( jumlah ) AS jum_us_tel 
				FROM
					tbl_uangku
					INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa
				WHERE
					id_tbl_status_transaksi = '1' 
					AND tanggal BETWEEN '$tanggalawal' 
					AND '$tanggalakhir'
					AND data_siswa.id_sesi = '$id_tahunajar'
					AND data_siswa.id_jenjang = '$id_jenjang'
			)
			UNION ALL
			(
				SELECT
					5 as id,
					'Uang Saku Masuk Offline' as nama,
					SUM( jumlah ) AS jum_us_tel 
				FROM
					tbl_uangku
					INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa
				WHERE
					id_tbl_status_transaksi = '3' 
					AND tanggal BETWEEN '$tanggalawal' 
					AND '$tanggalakhir'
					AND data_siswa.id_sesi = '$id_tahunajar'
					AND data_siswa.id_jenjang = '$id_jenjang'
			)
			UNION ALL
			(
				SELECT
					6 as id,
					'Setor Tabungan' as nama,
					SUM( tabungan.nominal_tab ) AS jum_us_tel 
				FROM
					tabungan
					INNER JOIN data_siswa ON data_siswa.id_siswa = tabungan.nis_from_idsiswa
				WHERE
					tabungan.tgl BETWEEN '$tanggalawal' 
					AND '$tanggalakhir'
					AND data_siswa.id_jenjang = '$id_jenjang'
					AND data_siswa.id_sesi = '$id_tahunajar'
			)
			UNION ALL
			(
				SELECT
					7 as id,
					'Penarikan Tabungan' as nama,
					SUM( tabungan.nominal_amb ) AS jum_us_tel 
				FROM
					tabungan 
				INNER JOIN data_siswa ON data_siswa.id_siswa = tabungan.nis_from_idsiswa
				WHERE
					tabungan.tgl BETWEEN '$tanggalawal' 
					AND '$tanggalakhir'
					AND data_siswa.id_jenjang = '$id_jenjang'
					AND data_siswa.id_sesi = '$id_tahunajar'
			)
			UNION ALL
			(
				SELECT
					8 as id,
					'Saldo Tabungan' as nama,
					( tab_masuk - tab_keluar ) AS jum_us_tel 
				FROM
					(
					SELECT
						SUM( nominal_tab ) AS tab_masuk 
					FROM
						tabungan
						INNER JOIN data_siswa ON data_siswa.id_siswa = tabungan.nis_from_idsiswa 
					WHERE
						tgl BETWEEN '$tanggalawal' 
						AND '$tanggalakhir' 
						AND data_siswa.id_jenjang = '$id_jenjang' 
					) AS masuk
					INNER JOIN (
					SELECT
						SUM( nominal_amb ) AS tab_keluar
					FROM
						tabungan
						INNER JOIN data_siswa ON data_siswa.id_siswa = tabungan.nis_from_idsiswa 
					WHERE
						tgl BETWEEN '$tanggalawal' 
						AND '$tanggalakhir' 
					AND data_siswa.id_jenjang = '$id_jenjang' 
					) AS keluar
			)
			UNION ALL
			(
				SELECT
					9 as id,
					'Pengeluaran' as nama,
					SUM( pengeluaran_kas.nominal_pengeluaran ) AS jum_us_tel 
				FROM
					pengeluaran_kas
				WHERE
					tanggal BETWEEN '$tanggalawal' 
					AND '$tanggalakhir'
					AND id_jenjang = '$id_jenjang'
					AND id_sesi = '$id_tahunajar'
			)
		";
		$res = db_query($sql);
		$sql_saku = "SELECT
						saku_masuk,
						saku_keluar,
						( saku_masuk - saku_keluar ) AS saldo_uang_saku 
					FROM
						(
						SELECT
							SUM( jumlah ) AS saku_masuk 
						FROM
							tbl_uangku
							INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa 
						WHERE
							id_tbl_status_transaksi IN ( '3', '1' ) 
							AND tanggal BETWEEN '$tanggalawal' 
							AND '$tanggalakhir' 
							AND data_siswa.id_jenjang = '$id_jenjang' AND data_siswa.id_sesi='$id_tahunajar'
						) AS masuk
						INNER JOIN (
						SELECT
							SUM( jumlah ) AS saku_keluar 
						FROM
							tbl_uangku
							INNER JOIN data_siswa ON data_siswa.id_siswa = tbl_uangku.id_siswa 
						WHERE
							id_tbl_status_transaksi IN ( '2' ) 
							AND tanggal BETWEEN '$tanggalawal' 
							AND '$tanggalakhir' 
						AND data_siswa.id_jenjang = '$id_jenjang' AND data_siswa.id_sesi='$id_tahunajar'
						) AS keluar";
		$res_saku=db_assoc($sql_saku);
		$query_tahun = "
			SELECT
				ketetapan,
				bayar.*,
				ketetapan.ketetapan - IFNULL( bayar.bayar, 0 ) AS tagih,
				ROUND( ( bayar.bayar / ketetapan.ketetapan ) * 100, 2 ) AS persentase 
			FROM
			(SELECT
				SUM( nominal ) AS ketetapan,
				data_siswa.id_sesi
			FROM
				master_alokasi_transaksi_all
				INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa 
			WHERE
				master_alokasi_transaksi_all.id_sesi = $id_tahunajar AND data_siswa.id_jenjang='$id_jenjang') as ketetapan
				INNER JOIN
			(SELECT
				SUM( nominal_dibayarkan ) AS bayar,
				thn_aj,
				data_siswa.id_jenjang
			FROM
				transaksi_siswa
				INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa 
			WHERE
				transaksi_siswa.thn_aj = $id_tahunajar AND data_siswa.id_jenjang='$id_jenjang') as bayar 
			WHERE
				bayar.thn_aj = '$id_tahunajar' 
				AND bayar.id_jenjang = '$id_jenjang'";
		
		$qt=db_assoc($query_tahun);
		$cls=[
				'table-success',
				'table-success',
				'table-success',
				'table-success',
				'table-success',
				'table-success',
				'table-danger',
				'table-danger',
				'table-danger',
				'table-warning'
			];
		$tbody = '';
		while($dt=mysqli_fetch_array($res))
		{
			$id=$dt['id'];
			$tbody.="
			<tr class='$cls[$id]'>
				<td>$dt[nama]</td>
				<td>Rp".rupiah($dt['jum_us_tel'])."</td>
			</tr>
			";
			if($id==5)
			{
				$tbody.="
				<tr class='$cls[$id]'>
					<td>Total Uang Saku Masuk</td>
					<td>Rp".rupiah($res_saku['saku_masuk'])."</td>
				</tr>
				<tr class='$cls[$id]'>
					<td>Total Uang Saku Keluar</td>
					<td>Rp".rupiah($res_saku['saku_keluar'])."</td>
				</tr>
				<tr class='$cls[$id]'>
					<td>Total Saldo Uang Saku</td>
					<td>Rp".rupiah($res_saku['saldo_uang_saku'])."</td>
				</tr>
				";
			}
		}
		$table="
			<table class='table table-sm'>
			<tbody>
				$tbody
					<tr>
						<td>Estimasi</td>
						<td>Rp".rupiah($qt['ketetapan'])."</td>
					</tr>
					<tr>
						<td>Dibayar</td>
						<td>Rp".rupiah($qt['bayar'])."</td>
					</tr>
					<tr>
						<td>Tagihan</td>
						<td>Rp".rupiah($qt['tagih'])."</td>  
					</tr>
			</tbody>
			</table>
		";
		$elem.=$table;
		$elem .='
			<div class="progress progress-xl">
				<div class="progress-bar  progress-bar-striped  progress-bar-animated" role="progressbar" 
					style="width: '.$qt['persentase'].'%;" 
					aria-valuenow="'.$qt['persentase'].'" aria-valuemin="0" aria-valuemax="100">
					'.$qt['persentase'].'%
				</div>
			</div>';
		$elem.="
			<script>
				$('#spn_trn_ta').html('$ta[nama_tahunajar]');
			</script>";
		if($return==1)
		{
			return $elem;
		}
		else
		{
			echo $elem;
		}
	}
			
	
	function chart_bulan($return=0)
	{
		$rsltx[0]=get('id_th');
		$rsltj[0]=get('id_jenjang');
		$tgl = get('pers_bulan_tgl');
		$elem='';
		$q_bln ="
				SELECT
					tbl_all.id_kat_laporan,
					tbl_all.nama_transaksi,
					tbl_all.hitung,
					tbl_bayar.bayar,
					ROUND ((IFNULL( tbl_bayar.bayar, 0 ) / tbl_all.hitung) *100,2) AS persentase  
				FROM
					(
					SELECT
						kategori_laporan.nama_kategori_laporan,
						kategori_laporan.id_kat_laporan,
						SUM( nominal ) AS hitung,
						master_jenis_transaksi.nama_transaksi
					FROM
						master_alokasi_transaksi_all
						INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
						INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan=master_jenis_transaksi.id_kategori_laporan	
					WHERE
						LEFT(tgl_jth, 7) = '$tgl'
						AND data_siswa.id_sesi = '$rsltx[0]' 
						AND data_siswa.id_jenjang='$rsltj[0]'
					GROUP BY
						master_jenis_transaksi.id_kategori_laporan 
					) AS tbl_all
					INNER JOIN (
					SELECT
						kategori_laporan.nama_kategori_laporan,
						kategori_laporan.id_kat_laporan,
						SUM( nominal_dibayarkan ) AS bayar
					FROM
						transaksi_siswa
						INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis=transaksi_siswa.id_master_trans_sis
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
						INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
						INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan = master_jenis_transaksi.id_kategori_laporan 
					WHERE
						LEFT(tgl_jth, 7) = '$tgl'
						AND data_siswa.id_sesi = '$rsltx[0]' 
						AND data_siswa.id_jenjang = '$rsltj[0]'  
					GROUP BY
					master_jenis_transaksi.id_kategori_laporan 
					) AS tbl_bayar ON tbl_bayar.id_kat_laporan = tbl_all.id_kat_laporan";
		//echo $q_bln;die;
		//MONTH ( tgl_jth ) = MONTH ( NOW( ) ) 
		$quedua=db_query($q_bln);
		$c=0;
		while($ab=mysqli_fetch_array($quedua))
		{
			$de[$c]=$ab['persentase'];
			$df[$c]=$ab['nama_transaksi'];
			$c++;
		}
		if($c > 0){
			$persenbulan=implode(",",$de);
			$lblbulan=implode("','",$df);
		}else{
			$persenbulan=0;
			$lblbulan='_';
		}
		$dt['nilai']=$persenbulan;
		$dt['nama']="'".$lblbulan."'";
		$elem .= "
			<script>
				data={};
				data.nama=[".$dt['nama']."];
				data.nilai=[".$dt['nilai']."];
				chart_transaksi(1,data);
			</script>		
		";
		if($return==1)
		{
			return $elem;
		}
		else
		{
			echo $elem;
		}
	}
	
	function chart_tahun($return=0)
	{
		$rsltx[0]=get('id_th');
		$rsltj[0]=get('id_jenjang');
		$ta = db_assoc("SELECT * FROM data_tahunajar WHERE id_tahunajar = $rsltx[0]");
		$between ="tgl_jth BETWEEN '$ta[tgl_mulai]' AND '$ta[tgl_selesai]' ";
		$elem='';
		$q_bln ="
				SELECT
					tbl_all.id_kat_laporan,
					tbl_all.nama_transaksi,
					tbl_all.hitung,
					tbl_bayar.bayar,
					ROUND ((IFNULL( tbl_bayar.bayar, 0 ) / tbl_all.hitung) *100,2) AS persentase  
				FROM
					(
					SELECT
						kategori_laporan.nama_kategori_laporan,
						kategori_laporan.id_kat_laporan,
						SUM( nominal ) AS hitung,
						master_jenis_transaksi.nama_transaksi
					FROM
						master_alokasi_transaksi_all
						INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
						INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan=master_jenis_transaksi.id_kategori_laporan	
					WHERE
						$between
						AND data_siswa.id_sesi = '$rsltx[0]' 
						AND data_siswa.id_jenjang='$rsltj[0]'
					GROUP BY
						master_jenis_transaksi.id_kategori_laporan 
					) AS tbl_all
					INNER JOIN (
					SELECT
						kategori_laporan.nama_kategori_laporan,
						kategori_laporan.id_kat_laporan,
						SUM( nominal_dibayarkan ) AS bayar
					FROM
						transaksi_siswa
						INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis=transaksi_siswa.id_master_trans_sis
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
						INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
						INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan = master_jenis_transaksi.id_kategori_laporan 
					WHERE
						$between
						AND data_siswa.id_sesi = '$rsltx[0]' 
						AND data_siswa.id_jenjang = '$rsltj[0]'  
					GROUP BY
					master_jenis_transaksi.id_kategori_laporan 
					) AS tbl_bayar ON tbl_bayar.id_kat_laporan = tbl_all.id_kat_laporan";
		//echo $q_bln;die;
		//MONTH ( tgl_jth ) = MONTH ( NOW( ) ) 
		$quedua=db_query($q_bln);
		$c=0;
		while($ab=mysqli_fetch_array($quedua))
		{
			$de[$c]=$ab['persentase'];
			$df[$c]=$ab['nama_transaksi'];
			$c++;
		}
		if($c > 0){
			$persenbulan=implode(",",$de);
			$lblbulan=implode("','",$df);
		}else{
			$persenbulan=0;
			$lblbulan='_';
		}
		$dt['nilai']=$persenbulan;
		$dt['nama']="'".$lblbulan."'";
		$elem .= "
			<script>
				data={};
				data.nama=[".$dt['nama']."];
				data.nilai=[".$dt['nilai']."];
				chart_transaksi(2,data);
				$('#pers_trn_ta').html('$ta[nama_tahunajar]');
			</script>		
		";
		if($return==1)
		{
			return $elem;
		}
		else
		{
			echo $elem;
		}
	}

	function donut_bulan($return=0)
	{
		$rsltx[0]=get('id_th');
		$rsltj[0]=get('id_jenjang');
		$tgl = get('pers_bulan_tgl');
		$elem='';
		$q_bln ="
				SELECT
					tbl_all.id_kat_laporan,
					tbl_all.nama_transaksi,
					tbl_all.hitung,
					tbl_bayar.bayar,
					ROUND ((IFNULL( tbl_bayar.bayar, 0 ) / tbl_all.hitung) *100,2) AS persentase  
				FROM
					(
					SELECT
						kategori_laporan.nama_kategori_laporan,
						kategori_laporan.id_kat_laporan,
						SUM( nominal ) AS hitung,
						master_jenis_transaksi.nama_transaksi
					FROM
						master_alokasi_transaksi_all
						INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
						INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan=master_jenis_transaksi.id_kategori_laporan	
					WHERE
						LEFT(tgl_jth, 7) = '$tgl'
						AND data_siswa.id_sesi = '$rsltx[0]' 
						AND data_siswa.id_jenjang='$rsltj[0]'
					GROUP BY
						master_jenis_transaksi.id_kategori_laporan 
					) AS tbl_all
					INNER JOIN (
					SELECT
						kategori_laporan.nama_kategori_laporan,
						kategori_laporan.id_kat_laporan,
						SUM( nominal_dibayarkan ) AS bayar
					FROM
						transaksi_siswa
						INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis=transaksi_siswa.id_master_trans_sis
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
						INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
						INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan = master_jenis_transaksi.id_kategori_laporan 
					WHERE
						LEFT(tgl_jth, 7) = '$tgl'
						AND data_siswa.id_sesi = '$rsltx[0]' 
						AND data_siswa.id_jenjang = '$rsltj[0]'  
					GROUP BY
					master_jenis_transaksi.id_kategori_laporan 
					) AS tbl_bayar ON tbl_bayar.id_kat_laporan = tbl_all.id_kat_laporan";
		//echo $q_bln;die;
		//MONTH ( tgl_jth ) = MONTH ( NOW( ) ) 
		$quedua=db_query($q_bln);
		$c=0;
		while($ab=mysqli_fetch_array($quedua))
		{
			$de[$c]=$ab['persentase'];
			$df[$c]=$ab['nama_transaksi'];
			$c++;
		}
		if($c > 0){
			$persenbulan=implode(",",$de);
			$lblbulan=implode("','",$df);
		}else{
			$persenbulan=0;
			$lblbulan='_';
		}
		$dt['nilai']=$persenbulan;
		$dt['nama']="'".$lblbulan."'";
		$elem .= "
			<script>
				data={};
				data.nama=[".$dt['nama']."];
				data.nilai=[".$dt['nilai']."];
				donut_transaksi(1,data);
			</script>		
		";
		if($return==1)
		{
			return $elem;
		}
		else
		{
			echo $elem;
		}
	}
	
	function donut_tahun($return=0)
	{
		$rsltx[0]=get('id_th');
		$rsltj[0]=get('id_jenjang');
		$ta = db_assoc("SELECT * FROM data_tahunajar WHERE id_tahunajar = $rsltx[0]");
		$between ="tgl_jth BETWEEN '$ta[tgl_mulai]' AND '$ta[tgl_selesai]' ";
		$elem='';
		$q_bln ="
				SELECT
					tbl_all.id_kat_laporan,
					tbl_all.nama_transaksi,
					tbl_all.hitung,
					tbl_bayar.bayar,
					ROUND ((IFNULL( tbl_bayar.bayar, 0 ) / tbl_all.hitung) *100,2) AS persentase  
				FROM
					(
					SELECT
						kategori_laporan.nama_kategori_laporan,
						kategori_laporan.id_kat_laporan,
						SUM( nominal ) AS hitung,
						master_jenis_transaksi.nama_transaksi
					FROM
						master_alokasi_transaksi_all
						INNER JOIN data_siswa ON data_siswa.id_siswa = master_alokasi_transaksi_all.nis_from_idsiswa
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = master_alokasi_transaksi_all.id_jenis_transaksi
						INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan=master_jenis_transaksi.id_kategori_laporan	
					WHERE
						$between
						AND data_siswa.id_sesi = '$rsltx[0]' 
						AND data_siswa.id_jenjang='$rsltj[0]'
					GROUP BY
						master_jenis_transaksi.id_kategori_laporan 
					) AS tbl_all
					INNER JOIN (
					SELECT
						kategori_laporan.nama_kategori_laporan,
						kategori_laporan.id_kat_laporan,
						SUM( nominal_dibayarkan ) AS bayar
					FROM
						transaksi_siswa
						INNER JOIN master_alokasi_transaksi_all ON master_alokasi_transaksi_all.id_master_trans_sis=transaksi_siswa.id_master_trans_sis
						INNER JOIN master_jenis_transaksi ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
						INNER JOIN data_siswa ON data_siswa.id_siswa = transaksi_siswa.nis_from_idsiswa
						INNER JOIN kategori_laporan ON kategori_laporan.id_kat_laporan = master_jenis_transaksi.id_kategori_laporan 
					WHERE
						$between
						AND data_siswa.id_sesi = '$rsltx[0]' 
						AND data_siswa.id_jenjang = '$rsltj[0]'  
					GROUP BY
					master_jenis_transaksi.id_kategori_laporan 
					) AS tbl_bayar ON tbl_bayar.id_kat_laporan = tbl_all.id_kat_laporan";
		//echo $q_bln;die;
		//MONTH ( tgl_jth ) = MONTH ( NOW( ) ) 
		$quedua=db_query($q_bln);
		$c=0;
		while($ab=mysqli_fetch_array($quedua))
		{
			$de[$c]=$ab['persentase'];
			$df[$c]=$ab['nama_transaksi'];
			$c++;
		}
		if($c > 0){
			$persenbulan=implode(",",$de);
			$lblbulan=implode("','",$df);
		}else{
			$persenbulan=0;
			$lblbulan='_';
		}
		$dt['nilai']=$persenbulan;
		$dt['nama']="'".$lblbulan."'";
		$elem .= "
			<script>
				data={};
				data.nama=[".$dt['nama']."];
				data.nilai=[".$dt['nilai']."];
				donut_transaksi(2,data);
				$('#donut_trn_ta').html('$ta[nama_tahunajar]');
			</script>		
		";
		if($return==1)
		{
			return $elem;
		}
		else
		{
			echo $elem;
		}
	}


	function get_select_ta()
	{
		$ta=$_GET['id_th'];
		$bln= list_bulan_ta($ta);
		 $now=date("Y-m");
		echo "<select class='form-select form-select-sm select-inline'>";
		foreach ($bln as $dt)
		{
			$selected='';
			if($now==$dt['val'])
			{
				$selected='selected';
			}
			echo "<option value='$dt[val]' $selected>$dt[bulan]</option>";
		}
		echo "</select>";
	}
	
    public function js($param1)
    {
        $this->get_js("main/js/" .$param1);
    }
}

?>
