<?php

class Mst_Transaksi extends Controller{
	
	function list_mst_transaksi()
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
				nama_kat_pemasukan
			FROM
				master_jenis_transaksi
				LEFT JOIN kategori_trans ON kategori_trans.id_kat_trans = master_jenis_transaksi.id_kategori_laporan
				LEFT JOIN kategori_laporan ON kategori_laporan.id_kat_laporan = master_jenis_transaksi.id_kategori_laporan
				LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran
				LEFT JOIN kas_kategori_pemasukan ON master_jenis_transaksi.id_kat_pemasukan = kas_kategori_pemasukan.id_kat_pemasukan
			WHERE
				master_jenis_transaksi.id_sesi = $pt[id_ta] 
				AND master_jenis_transaksi.id_jenjang = $pt[id_jenjang] 
			ORDER BY
				id_jenis_transaksi ASC 
				";
		$res = db_query($sql);
		$tbody = '';
		$no = 1;
		while($dt = mysqli_fetch_array($res))
		{
			$td_status = "<td class='table-warning'>Non-Kelompok</td>";
			if($dt['status_kelompok']==1)
			{
				$td_status = "<td class='table-info'>Kelompok</td>";
			}
			$td_cicil = "<td class='table-danger'>Tidak</td>";
			if($dt['status_kelompok']==0 && $dt['cicil']==1)
			{
				$td_cicil = "<td class='table-success'>Ya</td>";
			}
			else if($dt['status_kelompok']==1 && $dt['kondisi_khusus']=='popup')
			{
				$td_cicil = "<td class='table-success'>Bisa Dicicil</td>";
			}
			/*$tbody .= "
				<tr class='cari-item'>
					<td>".$no++."</td>
					<td class='cari-q'>".$dt['nama_transaksi']."</td>
					<td>".$dt['nama_kategori']."</td>
					$td_status
					$td_cicil
					<td>".$dt['nama_kategori_laporan']."</td>
					<td>".$dt['nama_kategori_pengeluaran']."</td>
					<td>".$dt['nama_kat_pemasukan']."</td>
					<td class='tc td_last' style='white-space: nowrap;'>
						<span class=''>
							<button class='btn btn-sm btn-primary' onclick='modal_mst_trans(2,$dt[id_jenis_transaksi]);'> 
								<i class='bx bx-edit'></i> Edit
							</button>
							<button class='btn btn-sm btn-danger' onclick='del_mst_trans($dt[id_jenis_transaksi]);'> 
								<i class='bx bx-trash'></i>
							</button>
						</span>
					</td>
				</tr>
			";*/
			$tbody .= "
				<tr class='cari-item'>
					<td>".$no++."</td>
					<td class='cari-q'>".$dt['nama_transaksi']."</td>
					<td>".substr($dt['tgl'], 0,10)."</td>
					$td_status
					$td_cicil
					<td>".$dt['nama_kategori']."</td>
					<td>".$dt['nama_kategori_laporan']."</td>
					<td>".$dt['nama_kategori_pengeluaran']."</td>
					<td class='tc td_last' style='white-space: nowrap;'>
						<span class=''>
							<button class='btn btn-sm btn-primary' onclick='modal_mst_trans(2,$dt[id_jenis_transaksi]);'> 
								<i class='bx bx-edit'></i> Edit
							</button>
							<button class='btn btn-sm btn-danger' onclick='del_mst_trans($dt[id_jenis_transaksi]);'> 
								<i class='bx bx-trash'></i>
							</button>
						</span>
					</td>
				</tr>
			";
		}
		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr  class=' tc trans_no_result'  style='$no_res'>
					<td colspan='8'>Tidak Ada Data</td>
				</tr>";
		/*$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama Iuran</th>
					<th>Kategori Iuran</th>
					<th>Tipe Pembayaran</th>
					<th>Cicilan</th>
					<th>Kelompok Laporan</th>
					<th>Kategori Pengeluaran Dana</th>
					<th>Pemasukan Kas Komunitas</th>
					<th></th>
				</tr>
			</thead>
			<tbody class='table-hover'>
				$tbody
			</tbody>
			</table>";*/
			$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>No.</th>
					<th>Nama Iuran</th>
					<th>Batas Pembayaran</th>
					<th>Status Iuran</th>
					<th>Bisa Dicicil</th>
					<th>Jenis Iuran</th>
					<th>Alokasi Dana</th>
					<th>Pos Kas</th>
					<th></th>
				</tr>
			</thead>
			<tbody class='table-hover'>
				$tbody
			</tbody>
			</table>";
			echo $table;
	}
	
	function load_mst_kat_transaksi()
	{
		$pt = $_POST;
		$sql = "SELECT * FROM kategori_trans WHERE id_sesi='$pt[id_ta]' AND id_jenjang='$pt[id_jenjang]'";
		$res = db_query($sql);
		$tbody = '';
		$no = 1;
		while($dt= mysqli_fetch_array($res))
		{
			$tbody .= "
			<tr>
				<td>".$no++."</td>
				<td class='nama_kat'>".$dt['nama_kategori']."</td>
				<td class='tc td_last'>
					<span class='act_item'>
						<button class='btn btn-xs btn-primary' onclick='save_mst_kat_transaksi(2,$dt[id_kat_trans]);'> 
							<i class='bx bx-save'></i> Simpan
						</button>
						<button class='btn btn-xs btn-danger' onclick='del_mst_kat_transaksi($dt[id_kat_trans]);'> 
							<i class='bx bx-trash'></i>
						</button>
					</span>
				</td>
			</tr>
			";
		}
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Nama Jenis</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>
		";
		echo $table;
	}
	
	function save_mst_kat_transaksi()
	{
		$dt = $_POST;
		$cb['stat']=0;
		$cb['ket']='';
		$dup = db_get("SELECT COUNT(*) FROM kategori_trans WHERE 
						nama_kategori='$dt[nama_master]'  
						AND id_sesi=$dt[id_ta]
						AND id_jenjang=$dt[id_jenjang]
						AND id_kat_trans != $dt[id_master]");
		if($dup>0)
		{
			$cb['ket']="Nama Kategori sudah terdaftar, gunakan nama lain";
		}
		else
		{
			if($dt['mode']==1)
			{
				$sql="
					INSERT INTO 
					kategori_trans(
							nama_kategori,
							id_sesi,
							id_jenjang
						) 
					VALUES (
						'$dt[nama_master]',
						$dt[id_ta],
						$dt[id_jenjang]
					)";
			}
			else
			{
				$sql="
						UPDATE  
							kategori_trans
						SET 
							nama_kategori='$dt[nama_master]'
						WHERE
							id_kat_trans='$dt[id_master]'
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
	
	function del_mst_kat_transaksi()
	{
		$id=$_POST['id_master'];
		$sql = "DELETE FROM kategori_laporan WHERE id_kat_trans=$id";
		$res = db_query($sql);
		$data=[];
		$data['stat']=0;
		if($res)
		{
			$data['stat']=1;
		}
		echo json_encode($data);
	}
	
	function load_mst_kat_laporan()
	{
		$pt = $_POST;
		$sql = "SELECT * FROM kategori_laporan WHERE id_sesi='$pt[id_ta]' AND id_jenjang='$pt[id_jenjang]'";
		$res = db_query($sql);
		$tbody = '';
		$no = 1;
		while($dt= mysqli_fetch_array($res))
		{
			$tbody .= "
			<tr>
				<td>".$no++."</td>
				<td class='nama_kat'>".$dt['nama_kategori_laporan']."</td>
				<td class='tc td_last'>
					<span class='act_item'>
						<button class='btn btn-xs btn-primary' onclick='save_mst_kat_laporan(2,$dt[id_kat_laporan]);'> 
							<i class='bx bx-save'></i> Simpan
						</button>
						<button class='btn btn-xs btn-danger' onclick='del_mst_kat_laporan($dt[id_kat_laporan]);'> 
							<i class='bx bx-trash'></i>
						</button>
					</span>
				</td>
			</tr>
			";
		}
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Nama Alokasi</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>
		";
		echo $table;
	}
	
	function save_mst_kat_laporan()
	{
		$dt = $_POST;
		$cb['stat']=0;
		$cb['ket']='';
		$dup = db_get("SELECT COUNT(*) FROM kategori_laporan WHERE 
						nama_kategori_laporan='$dt[nama_master]'  
						AND id_sesi=$dt[id_ta]
						AND id_jenjang=$dt[id_jenjang]
						AND id_kat_laporan != $dt[id_master]");
		if($dup>0)
		{
			$cb['ket']="Nama Kategori sudah terdaftar, gunakan nama lain";
		}
		else
		{
			if($dt['mode']==1)
			{
				$sql="
					INSERT INTO 
					kategori_laporan(
							nama_kategori_laporan,
							id_sesi,
							id_jenjang
						) 
					VALUES (
						'$dt[nama_master]',
						$dt[id_ta],
						$dt[id_jenjang]
					)";
			}
			else
			{
				$sql="
						UPDATE  
							kategori_laporan
						SET 
							nama_kategori_laporan='$dt[nama_master]'
						WHERE
							id_kat_laporan='$dt[id_master]'
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
	
	function del_mst_kat_laporan()
	{
		$id=$_POST['id_master'];
		$res = false;
		$data=[];
		$data['stat']=0;
		$chk = db_get("SELECT COUNT(*) FROM master_jenis_transaksi WHERE id_kategori_laporan='$id'");
		if($chk==0)
		{
			$sql = "DELETE FROM kategori_laporan WHERE id_kat_laporan=$id";
			$res = db_query($sql);
		}
		else
		{
			$data['ket']="Tidak dapat dihapus, kategori sudah dipakai!";
		}
		if($res)
		{
			$data['stat']=1;
		}
		echo json_encode($data);
	}
	
	function load_mst_kat_pengeluaran()
	{
		$pt = $_POST;
		$sql = "SELECT * FROM kategori_pengeluaran WHERE id_sesi='$pt[id_ta]' AND id_jenjang='$pt[id_jenjang]'";
		$res = db_query($sql);
		$tbody = '';
		$no = 1;
		while($dt= mysqli_fetch_array($res))
		{
			$tbody .= "
			<tr>
				<td>".$no++."</td>
				<td class='nama_kat'>".$dt['nama_kategori_pengeluaran']."</td>
				<td class='tc td_last'>
					<span class='act_item'>
						<button class='btn btn-xs btn-primary' onclick='save_mst_kat_pengeluaran(2,$dt[id_kategori_pengeluaran]);'> 
							<i class='bx bx-save'></i> Simpan
						</button>
						<button class='btn btn-xs btn-danger' onclick='del_mst_kat_pengeluaran($dt[id_kategori_pengeluaran]);'> 
							<i class='bx bx-trash'></i>
						</button>
					</span>
				</td>
			</tr>
			";
		}
		$table = "
			<table class='table table-sm m-0'>
			<thead>
				<tr>
					<th>#</th>
					<th>Nama Pos Kas</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>
		";
		echo $table;
	}
	
	function save_mst_kat_pengeluaran()
	{
		$dt = $_POST;
		$cb['stat']=0;
		$cb['ket']='';
		$dup = db_get("SELECT COUNT(*) FROM kategori_pengeluaran WHERE 
						nama_kategori_pengeluaran='$dt[nama_master]'  
						AND id_sesi=$dt[id_ta]
						AND id_jenjang=$dt[id_jenjang]
						AND id_kategori_pengeluaran != $dt[id_master]");
		if($dup>0)
		{
			$cb['ket']="Nama Kategori sudah terdaftar, gunakan nama lain";
		}
		else
		{
			if($dt['mode']==1)
			{
				$sql="
					INSERT INTO 
					kategori_pengeluaran(
							nama_kategori_pengeluaran,
							id_sesi,
							id_jenjang
						) 
					VALUES (
						'$dt[nama_master]',
						$dt[id_ta],
						$dt[id_jenjang]
					)";
			}
			else
			{
				$sql="
						UPDATE  
							kategori_pengeluaran
						SET 
							nama_kategori_pengeluaran='$dt[nama_master]'
						WHERE
							id_kategori_pengeluaran='$dt[id_master]'
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
	
	function del_mst_kat_pengeluaran()
	{
		$id=$_POST['id_master'];
		$res = false;
		$data=[];
		$data['stat']=0;
		$chk = db_get("SELECT COUNT(*) FROM master_jenis_transaksi WHERE id_kategori_pengeluaran='$id'");
		if($chk==0)
		{
			$sql = "DELETE FROM kategori_pengeluaran WHERE id_kategori_pengeluaran=$id";
			$res = db_query($sql);
		}
		else
		{
			$data['ket']="Tidak dapat dihapus, kategori sudah dipakai!";
		}
		if($res)
		{
			$data['stat']=1;
		}
		echo json_encode($data);
	}

	function load_mst_jns_transaksi()
	{
		$pt = $_POST;
		$id_ta=$pt['id_ta'];
		$id_jenjang=$pt['id_jenjang'];
		///JATUH TEMPO
		$ta = db_assoc("SELECT * FROM data_tahunajar WHERE id_tahunajar = $pt[id_ta]");
		$start_date = $ta['tgl_mulai'];
		$start_date = date('Y-m-d', strtotime($ta['tgl_mulai'] . ' +0 month'));
		$end_date = date('Y-m-d', strtotime($ta['tgl_selesai'] . ' +0 month'));
		$current_date = strtotime($start_date);
		$month_names = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		$option='';
		while ($current_date <= strtotime($end_date)) {
		  $month_number = date('n', $current_date);
		  $month_name = $month_names[$month_number-1];
		  $year = date('Y', $current_date);
		  $formatted_date = date('d-m-Y', strtotime('15-' . $month_number . '-' . $year));
		  $date_value = date('Y-m-d', strtotime('15-' . $month_number . '-' . $year));
		  
		  $option.= "<option value='$date_value'>Bulan " . $month_name . ", " . $formatted_date . "</option>";
		  
		  $current_date = strtotime('+1 month', $current_date);
		}
		$data['jatuh_tempo']="<option value='-'>-pilih-</option>".$option;
		
		///KELOMPOK TRANSAKSI
		$que = "SELECT * FROM kategori_trans WHERE id_sesi='$id_ta' AND id_jenjang='$id_jenjang'";
		$res = db_query($que);
		$option='';
		while($dt = mysqli_fetch_array($res))
		{
			$option.="<option value='$dt[id_kat_trans]'>$dt[nama_kategori]</option>";
		}
		$data['kelompok_transaksi']="<option value='-'>-pilih-</option>".$option;
		
		///KELOMPOK LAPORAN
		$que = "SELECT * FROM kategori_laporan WHERE id_sesi='$id_ta' AND id_jenjang='$id_jenjang'";
		$res = db_query($que);
		$option='';
		while($dt = mysqli_fetch_array($res))
		{
			$option.="<option value='$dt[id_kat_laporan]'>$dt[nama_kategori_laporan]</option>";
		}
		$data['kelompok_laporan']="<option value='-'>-pilih-</option>".$option;
		
		///KELOMPOK PENGELUARAN
		$que = "SELECT * FROM kategori_pengeluaran WHERE id_sesi='$id_ta' AND id_jenjang='$id_jenjang'";
		$res = db_query($que);
		$option='';
		while($dt = mysqli_fetch_array($res))
		{
			$option.="<option value='$dt[id_kategori_pengeluaran]'>$dt[nama_kategori_pengeluaran]</option>";
		}
		$data['kelompok_pengeluaran']="<option value='-'>-pilih-</option>".$option;
		
		if($pt['id_mst']!=0)
		{
			$mst = db_assoc("SELECT
								id_jenis_transaksi,
								kategori_pengeluaran.id_kategori_pengeluaran,
								kategori_laporan.id_kat_laporan,
								IFNULL(kategori_trans.id_kat_trans,'-') as id_kat_trans,
								IFNULL(cicil,'null') as cicil,
								master_jenis_transaksi.nama_transaksi,
								DATE(tgl) as tgl,
								urut,
								status_kelompok,
								id_kategori_laporan,
								id_jns_pemasukan,
								id_kat_pemasukan,
								id_subkat_pemasukan,
								kondisi_khusus
							FROM
								master_jenis_transaksi
								LEFT JOIN kategori_trans ON kategori_trans.id_kat_trans = master_jenis_transaksi.urut
								LEFT JOIN kategori_laporan ON kategori_laporan.id_kat_laporan = master_jenis_transaksi.id_kategori_laporan
								LEFT JOIN kategori_pengeluaran ON kategori_pengeluaran.id_kategori_pengeluaran = master_jenis_transaksi.id_kategori_pengeluaran 
							WHERE
								master_jenis_transaksi.id_sesi = $pt[id_ta] 
								AND master_jenis_transaksi.id_jenjang = $pt[id_jenjang]
								AND id_jenis_transaksi = $pt[id_mst]");
			//echo json_encode($mst);die;
			$data = array_merge($data,$mst);
		}
		else
		{
			$data['nama_transaksi']='';
			$data['id_kategori_laporan']='-';
			$data['id_kategori_pengeluaran']='-';
			$data['id_kat_trans']='-';
			$data['cicil']='-';
			$data['tgl']='-';
			$data['status_kelompok']='-';
			$data['kondisi_khusus']='-';
		}
		
		echo json_encode($data);
	}
	
	function save_mst_trans()
	{
		$pt = $_POST;
		if($pt['mode']==1)
		{
			if($pt['status_trans']==0)
			{
				$exfield=', cicil';
				$exval=", $pt[cicilan]";
			}
			else
			{
				$exfield=', urut, kel, kondisi_khusus';
				$exval=", '$pt[id_kelompok_trans]', '$pt[id_kelompok_trans]', '$pt[cicilan_khusus]'";
			}
			$sql = "INSERT INTO master_jenis_transaksi (
							nama_transaksi,
							id_sesi,
							tgl,
							status_kelompok,
							id_kategori_laporan,
							id_kategori_pengeluaran,
							id_jenjang
							$exfield
						)
					VALUES
					(
							'$pt[nama_trans]',
							'$pt[id_ta]',
							'$pt[jatuh_tempo]',
							'$pt[status_trans]',
							'$pt[id_kelompok_laporan]',
							$pt[id_kelompok_pengeluaran],
							'$pt[id_jenjang]'
							$exval
					)";
		}
		else
		{
			if($pt['status_trans']==0)
			{
				$exupd=", cicil = $pt[cicilan], urut = null, kel = null, kondisi_khusus = 'biasa'";
			}
			else
			{
				$exupd=", cicil = null, urut = '$pt[id_kelompok_trans]', kel = '$pt[id_kelompok_trans]', kondisi_khusus = '$pt[cicilan_khusus]'";
			}
			$sql = "UPDATE master_jenis_transaksi SET
						nama_transaksi = '$pt[nama_trans]',
						id_sesi = '$pt[id_ta]',
						tgl = '$pt[jatuh_tempo]',
						status_kelompok = '$pt[status_trans]',
						id_kategori_laporan = '$pt[id_kelompok_laporan]',
						id_kategori_pengeluaran = $pt[id_kelompok_pengeluaran],
						id_jenjang = '$pt[id_jenjang]'
						$exupd 
					WHERE
						id_jenis_transaksi = $pt[id_master] ";
		}
		$cb=[];
		$cb['stat']=0;
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
	
	function del_mst_trans()
	{
		$id=$_POST['id_master'];
		$res = false;
		$data=[];
		$data['stat']=0;
		$chk = db_get("SELECT COUNT(*) FROM master_alokasi_transaksi_all WHERE id_jenis_transaksi=$id");
		if($chk==0)
		{
			$sql = "DELETE FROM master_jenis_transaksi WHERE id_jenis_transaksi=$id";
			$res = db_query($sql);
		}
		else
		{
			$data['ket']="Tidak dapat dihapus, Jenis Transaksi sudah dipakai!";
		}
		if($res)
		{
			$data['stat']=1;
		}
		echo json_encode($data);
	}
	
	function load_kategori_laporan()
	{
		$que_kat = "SELECT
						* 
					FROM
						kas_kategori_pemasukan 
					WHERE
						del_stat = 0 
						AND id_jns_pemasukan IN (13,14)
					ORDER BY
						ref";
		$res = db_query($que_kat);
		$in_kat="<option value='0'>-pilih-</option>";
		while($dt = mysqli_fetch_array($res))
		{
			$in_kat.="
					<option value = '$dt[id_kat_pemasukan]'>$dt[ref] $dt[nama_kat_pemasukan]</option>";
		}
		
		$que_kat = "SELECT * FROM kas_subkategori_pemasukan WHERE del_stat =0 ORDER BY LENGTH(ref),ref";
		$res = db_query($que_kat);
		$subkat = [];
		while($dt = mysqli_fetch_assoc($res))
		{
			$idk=$dt['id_kat_pemasukan'];
			if(!isset($subkat[$idk]))
			{
				$subkat[$idk]=[];
			}
			$subkat[$idk][] = $dt;
		}
		
		$data = [];
		$data['in_kat']=$in_kat;
		$data['subkat']=$subkat;
		
		echo json_encode($data);
	} 
}

?>