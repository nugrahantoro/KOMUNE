<?php

class Inp_Kas_Out extends Controller{
	
	function load_list_trans($print = 0)
	{
		$pt=$_POST;
		$tgl_awal = tgl_indo($_POST['tgl_awal']);
		$tgl_akhir = tgl_indo($_POST['tgl_akhir']);
		$que = "SELECT
					kas_transaksi.*,
					nama_jns_pengeluaran,
					nama_kat_pengeluaran ,
					nama_subkat_pengeluaran ,
					DATE_FORMAT(tanggal,'%d-%m-%Y %H:%i') as tgl,
					kas_jenis_pengeluaran.del_stat as del_jns,
					kas_kategori_pengeluaran.del_stat as del_kat,
					kas_subkategori_pengeluaran.del_stat as del_subkat
				FROM
					kas_transaksi
					INNER JOIN kas_jenis_pengeluaran ON kas_transaksi.id_jns_pengeluaran = kas_jenis_pengeluaran.id_jns_pengeluaran
					LEFT JOIN kas_kategori_pengeluaran ON kas_transaksi.id_kat_pengeluaran = kas_kategori_pengeluaran.id_kat_pengeluaran
					LEFT JOIN kas_subkategori_pengeluaran ON kas_transaksi.id_subkat_pengeluaran = kas_subkategori_pengeluaran.id_subkat_pengeluaran
				WHERE
					tanggal BETWEEN '$tgl_awal 00:00:00' AND '$tgl_akhir 23:59:59'
					AND kas_transaksi.del_stat = 0
					AND tipe = 2
					AND kas_transaksi.id_jenjang = $pt[id_jenjang]
				ORDER BY tanggal DESC, id_transaksi DESC";
					
		$res = db_query($que);
		$tbody = '';
		$chk_month = db_get("SELECT
						IF
							(
								DATE_FORMAT(NOW(), '%Y-%m') = '2024-01' OR
								DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m') = '2024-01',
								1,
								0
							) AS bln_ini");
		while($dt=mysqli_fetch_array($res))
		{
			$act = "";
			if($chk_month==1 && $print==0)
			{
				$act.="
					<button class='btn btn-xs btn-danger' onclick='del_transaksi($dt[id_transaksi]);' title='Hapus'> 
								<i class='bx bx-trash'></i>
						</button>";
			}
		
			$act = "
					<button class='btn btn-xs btn-info' onclick='edit_data($dt[id_transaksi]);' title='Edit Data'> 
								<i class='bx bx-pencil'></i>
						</button>";
			
			
			$nm_subkat='';
			if(isset($dt['nama_subkat_pengeluaran']))
			{
				$nm_subkat = "<br>
								> $dt[nama_subkat_pengeluaran]";
								
			}
			
			$jns_sty='';
			$kat_sty='';
			$subkat_sty='';
			if($dt['del_jns']==1)
			{
				$jns_sty='style="color:darkorange;" title="Jenis Dihapus"';
				$kat_sty='style="color:darkorange;" title="Kategori Dihapus"';
				$subkat_sty='style="color:darkorange;" title="Subkategori Dihapus"';
			}
			else if($dt['del_kat']==1)
			{
				$kat_sty='style="color:darkorange;" title="Kategori Dihapus"';
				$subkat_sty='style="color:darkorange;" title="Subkategori Dihapus"';
			}
			else if($dt['del_subkat']==1)
			{
				$subkat_sty='style="color:darkorange;" title="Subkategori Dihapus"';
			}
			
			$tbody.="
			<tr class='cari-item' id_trans = '$dt[id_transaksi]'>
				<td>$dt[tgl]</td>
				<td class='cari-q' $jns_sty>$dt[nama_jns_pengeluaran]</td>
				<td class='cari-q' $kat_sty>$dt[nama_kat_pengeluaran] <span $subkat_sty>$nm_subkat</span></td>
				<td class='cari-q'>$dt[keterangan]</td>
				<td class='text-end'>".rupiah($dt['nominal_out'])."</td>
				<td>$act</td>
			</tr>";
		}
		$no_result = "display:none;";
		if($tbody =='')
		{
			$no_result='';
		}
		if($print==0)
		{
			$tbody.="<tr>
					<td class='tc no_result' colspan='6' style='$no_result'>Tidak Ada Data</td>
				</tr>";
		}
		$border = '';
		if($print==1)
		{
			$border="border = '1'";
		}
		$table = "
		<table class='table table-striped table-sm' $border>
			<thead>
				<th>Tanggal</th>
				<th>Jenis</th>
				<th>Kategori</th>
				<th>Keterangan</th>
				<th>Nominal</th>
				<th></th>
			</thead>
			<tbody>
				$tbody
			</tbody>
		</table>";
		
		if($print==1)
		{
			return $table;
		}
		
		echo $table;
	}
	
	function load_jenis_kat()
	{
		$pt=$_POST;
		$que_jns = "SELECT * FROM kas_jenis_pengeluaran WHERE del_stat =0 AND id_jenjang = $pt[id_jenjang] ORDER BY LENGTH(ref),ref";
		$res = db_query($que_jns);
		$in_jenis="<option value='0'>-pilih-</option>";
		while($dt = mysqli_fetch_array($res))
		{
			$in_jenis.="
					<option value = '$dt[id_jns_pengeluaran]'>$dt[ref] $dt[nama_jns_pengeluaran]</option>";
		}
		
		$que_jns = "SELECT * FROM kas_kategori_pengeluaran WHERE del_stat =0 ORDER BY LENGTH(ref),ref";
		$res = db_query($que_jns);
		$kat = [];
		while($dt = mysqli_fetch_assoc($res))
		{
			$idj=$dt['id_jns_pengeluaran'];
			if(!isset($kat[$idj]))
			{
				$kat[$idj]=[];
			}
			$kat[$idj][] = $dt;
		}
		
		$que_kat = "SELECT * FROM kas_subkategori_pengeluaran WHERE del_stat =0 ORDER BY LENGTH(ref),ref";
		$res = db_query($que_kat);
		$subkat = [];
		while($dt = mysqli_fetch_assoc($res))
		{
			$idk=$dt['id_kat_pengeluaran'];
			if(!isset($subkat[$idk]))
			{
				$subkat[$idk]=[];
			}
			$subkat[$idk][] = $dt;
		}
		
		$data = [];
		$data['in_jenis']=$in_jenis;
		$data['kategori']=$kat;
		$data['subkat']=$subkat;
		
		echo json_encode($data);
	}
	
	function save_pengeluaran()
	{
		$pt = $_POST;
		$pt['tanggal']=$this->datetime_db($pt['tanggal']);
		$idu = $_SESSION['id'];
		if($pt['mode']==1)
		{
			$que = "INSERT INTO kas_transaksi(
						tanggal,
						keterangan,
						nominal_out,
						id_jns_pengeluaran,
						id_kat_pengeluaran,
						id_subkat_pengeluaran,
						id_user,
						tipe,
						id_jenjang
					)
					VALUES
					(
						'$pt[tanggal]',
						'$pt[pengeluaran]',
						'$pt[nominal]',
						'$pt[jenis]',
						'$pt[kategori]',
						'$pt[subkategori]',
						'$idu',
						2,
						$pt[id_jenjang]
					)
					";
		}
		else
		{
			$que = "UPDATE `kas_transaksi` 
					SET 
					`tanggal` = '$pt[tanggal]',
					`keterangan` = '$pt[pengeluaran]',
					`nominal_out` = $pt[nominal],
					`id_jns_pengeluaran` = $pt[jenis],
					`id_kat_pengeluaran` = $pt[kategori],
					`id_subkat_pengeluaran` = $pt[subkategori],
					`id_user` = '$idu',	
					`id_jenjang` = $pt[id_jenjang]
					WHERE
						`id_transaksi` = $pt[id_trans]";
		}
		db_query($que);
		echo 'ok';
	}
	
	function del_transaksi()
	{
		$id_transaksi = $_POST['id_trans'];
		$idu = $_SESSION['id'];
		$que = "UPDATE kas_transaksi SET 
					del_stat = 1,
					del_tanggal = NOW(),
					del_id_user = '$idu'
				WHERE id_transaksi = $id_transaksi";
		$del= db_query($que);
		$data['stat']=0;
		$data['ket']='';
		if($del)
		{
			$data['stat']=1;
		}
		echo json_encode($data);
		
	}
	
	function download_excel()
	{
		$_POST = $_GET;
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Pemasukan_Kas_".$_POST['periode'].".xls");
		$table = $this->load_list_trans(1);
		echo $table;
	}
	
	function load_list_jenis()
	{
		$pt=$_POST;
		$que_jns = "SELECT
						tb_jns.*,
						COUNT( id_kat_pengeluaran ) AS jml_sub,
						GROUP_CONCAT( CONCAT( tb_kat.ref, ' ', nama_kat_pengeluaran ) ORDER BY LENGTH(tb_kat.ref), tb_kat.ref SEPARATOR '</li><li>') as nama_sub
					FROM
						kas_jenis_pengeluaran AS tb_jns
						LEFT JOIN kas_kategori_pengeluaran AS tb_kat ON tb_jns.id_jns_pengeluaran = tb_kat.id_jns_pengeluaran 
						AND tb_kat.del_stat = 0 
					WHERE
						tb_jns.del_stat = 0 
						AND id_jenjang = $pt[id_jenjang]
					GROUP BY
						tb_jns.id_jns_pengeluaran 
					ORDER BY
						LENGTH( tb_jns.ref ),
						tb_jns.ref";
		$res = db_query($que_jns);
		$tbody='';
		while($dt = mysqli_fetch_array($res))
		{
			$tooltip='';
			if($dt['jml_sub']>0)
			{
				$tooltip=' data-bs-toggle="tooltip" 
						data-bs-html="true" 
						data-bs-placement="right" 
						data-bs-title="<em>Kategori</em> <br> <b><ul><li>'.$dt['nama_sub'].'</li></ul></b>"';
			}
			$act="
					<button class='btn btn-xs btn-dark' onclick='modal_jenis(2,$dt[id_jns_pengeluaran]);' title='Edit'> 
								<i class='bx bx-pencil'></i> 
						</button>";
			$tbody.="
				<tr class='cari-item' id_jenis='$dt[id_jns_pengeluaran]' onclick='click_jenis(this,$dt[id_jns_pengeluaran]);'  $tooltip>
					<td class='cari-q'>$dt[ref]</td>
					<td class='cari-q '>$dt[nama_jns_pengeluaran]</td>
					<td class='text-end' style='white-space:nowrap;'>$act</td>
				</tr>";
		}
		$no_result = "display:none;";
		if($tbody =='')
		{
			$no_result='';
		}
		$tbody.="<tr>
					<td class='tc no_result' colspan='6' style='$no_result'>Tidak Ada Data</td>
				</tr>";
		$table ="
		<table class='table  table-sm'>
			<thead>
				<th>Ref</th>
				<th>Nama</th>
				<th></th>
			</thead>
			<tbody>
				$tbody
			</tbody>
		</table>";
		echo $table;
	}
	
	function load_data_jenis()
	{
		$id_jenis = $_POST['id_jenis'];
		$data = db_assoc("SELECT * FROM kas_jenis_pengeluaran WHERE id_jns_pengeluaran = $id_jenis");
		echo json_encode($data);
	}
	
	function save_jenis()
	{
		$data=[];
		$data['stat']=0;
		$data['ket']='';
		$pt = $_POST;
		$num = "SELECT 
					COUNT(*) 
				FROM 
					kas_jenis_pengeluaran 
				WHERE 
					ref = '$pt[ref]' AND nama_jns_pengeluaran = '$pt[nama]'
					AND id_jns_pengeluaran != $pt[id_jenis] AND id_jenjang = $pt[id_jenjang]
					AND del_stat = 0 ";
		$num=db_get($num);
		if($num>0)
		{
			$data['ket']="Sudah ada item lain dengan data yg sama";
		}
		else
		{
			if($pt['mode']==1)
			{
				$que = "INSERT INTO kas_jenis_pengeluaran(ref,nama_jns_pengeluaran, id_jenjang)
						VALUES ('$pt[ref]','$pt[nama]', $pt[id_jenjang])";
			}
			else
			{
				$que = "UPDATE kas_jenis_pengeluaran SET	
							ref = '$pt[ref]', nama_jns_pengeluaran = '$pt[nama]',
							id_jenjang = $pt[id_jenjang]
						WHERE id_jns_pengeluaran = $pt[id_jenis]";
			}
			$res = db_query($que);
			if($res)
			{
				$data['stat']=1;
			}
			else
			{
				$data['ket']="Gagal";
			}
		}
		echo json_encode($data);
	}
	
	function del_jenis()
	{
		$id_jenis = $_POST['id_jenis'];
		$que = "UPDATE kas_jenis_pengeluaran SET del_stat = 1 WHERE id_jns_pengeluaran = $id_jenis";
		db_query($que);
	}
	
	function load_list_kat()
	{
		$id_jenis = $_POST['id_jenis'];
		$que_kat = "SELECT
						tb_kat.*,
						COUNT( id_subkat_pengeluaran ) AS jml_sub,
						GROUP_CONCAT( CONCAT( tb_subkat.ref, ' ', nama_subkat_pengeluaran ) ORDER BY LENGTH( tb_subkat.ref ), tb_subkat.ref SEPARATOR '</li><li>' ) AS nama_sub 
					FROM
						kas_kategori_pengeluaran AS tb_kat
						LEFT JOIN kas_subkategori_pengeluaran AS tb_subkat ON tb_kat.id_kat_pengeluaran = tb_subkat.id_kat_pengeluaran 
						AND tb_subkat.del_stat = 0 
					WHERE
						tb_kat.del_stat = 0 
						AND id_jns_pengeluaran = $id_jenis
					GROUP BY
						tb_kat.id_kat_pengeluaran 
					ORDER BY
						LENGTH( tb_kat.ref ),
	tb_kat.ref";
		$res = db_query($que_kat);
		$tbody='';
		while($dt = mysqli_fetch_array($res))
		{
			$tooltip='';
			if($dt['jml_sub']>0)
			{
				$tooltip=' data-bs-toggle="tooltip" 
						data-bs-html="true" 
						data-bs-placement="right" 
						data-bs-title="<em>Sub Kategori</em> <br> <b><ul><li>'.$dt['nama_sub'].'</li></ul></b>"';
			}
			$act="
					<button class='btn btn-xs btn-dark' onclick='modal_kat(2,$dt[id_kat_pengeluaran]);' title='Edit'> 
								<i class='bx bx-pencil'></i>
						</button>";
			$tbody.="
				<tr class='cari-item' id_kat='$dt[id_kat_pengeluaran]' onclick='click_kat(this,$dt[id_kat_pengeluaran]);' $tooltip>
					<td class='cari-q'>$dt[ref]</td>
					<td class='cari-q '>$dt[nama_kat_pengeluaran]</td>
					<td class='text-end'>$act</td>
				</tr>";
		}
		$no_result = "display:none;";
		if($tbody =='')
		{
			$no_result='';
		}
		$tbody.="<tr>
					<td class='tc no_result' colspan='6' style='$no_result'>Tidak Ada Data</td>
				</tr>";
		$table ="
		<table class='table  table-sm'>
			<thead>
				<th>Ref</th>
				<th>Nama</th>
				<th></th>
			</thead>
			<tbody>
				$tbody
			</tbody>
		</table>";
		echo $table;
	}
	
	function load_data_kat()
	{
		$id_kat = $_POST['id_kat'];
		$data = db_assoc("SELECT * FROM kas_kategori_pengeluaran WHERE id_kat_pengeluaran = $id_kat");
		echo json_encode($data);
	}
	
	function get_data_trans()
	{
		$id_trans = $_POST['id_trans'];
		$data=db_assoc("SELECT * FROM kas_transaksi WHERE id_transaksi = $id_trans");
		$data['tanggal']=db_get("SELECT DATE_FORMAT('$data[tanggal]','%d-%m-%Y %h:%i')");
		//$data['tanggal']=db_get("SELECT DATE_FORMAT('$data[tanggal]','%Y-%m-%d %h:%i')");
		echo json_encode($data,JSON_PRETTY_PRINT);
	}
	
	function save_kat()
	{
		$data=[];
		$data['stat']=0;
		$data['ket']='';
		$pt = $_POST;
		$num = "SELECT 
					COUNT(*) 
				FROM 
					kas_kategori_pengeluaran 
				WHERE 
					ref = '$pt[ref]' AND nama_kat_pengeluaran = '$pt[nama]'
					AND id_kat_pengeluaran != $pt[id_kat]
					AND del_stat = 0 ";
		$num=db_get($num);
		if($num>0)
		{
			$data['ket']="Sudah ada item lain dengan data yg sama";
		}
		else
		{
			if($pt['mode']==1)
			{
				$que = "INSERT INTO kas_kategori_pengeluaran(ref,nama_kat_pengeluaran,id_jns_pengeluaran)
						VALUES ('$pt[ref]','$pt[nama]',$pt[id_jenis])";
			}
			else
			{
				$que = "UPDATE kas_kategori_pengeluaran SET	
							ref = '$pt[ref]', nama_kat_pengeluaran = '$pt[nama]'
						WHERE id_kat_pengeluaran = $pt[id_kat]";
			}
			$res = db_query($que);
			if($res)
			{
				$data['stat']=1;
			}
			else
			{
				$data['ket']="Gagal";
			}
		}
		echo json_encode($data);
	}
	
	function del_kat()
	{
		$id_kat = $_POST['id_kat'];
		$que = "UPDATE kas_kategori_pengeluaran SET del_stat = 1 WHERE id_kat_pengeluaran = $id_kat";
		db_query($que);
	}
	
	function load_list_subkat()
	{
		$id_kat = $_POST['id_kat'];
		$que_subkat = "SELECT * FROM kas_subkategori_pengeluaran WHERE del_stat =0 AND id_kat_pengeluaran = $id_kat ORDER by ref";
		$res = db_query($que_subkat);
		$tbody='';
		while($dt = mysqli_fetch_array($res))
		{
			$act="
					<button class='btn btn-xs btn-dark' onclick='modal_subkat(2,$dt[id_subkat_pengeluaran]);' title='Edit'> 
								<i class='bx bx-pencil'></i>
						</button>";
			$tbody.="
				<tr class='cari-item' id_subkat='$dt[id_subkat_pengeluaran]' onclick='click_subkat(this,$dt[id_subkat_pengeluaran]);'>
					<td class='cari-q'>$dt[ref]</td>
					<td class='cari-q '>$dt[nama_subkat_pengeluaran]</td>
					<td class='text-end'>$act</td>
				</tr>";
		}
		$no_result = "display:none;";
		if($tbody =='')
		{
			$no_result='';
		}
		$tbody.="<tr>
					<td class='tc no_result' colspan='6' style='$no_result'>Tidak Ada Data</td>
				</tr>";
		$table ="
		<table class='table  table-sm'>
			<thead>
				<th>Ref</th>
				<th>Nama</th>
				<th></th>
			</thead>
			<tbody>
				$tbody
			</tbody>
		</table>";
		echo $table;
	}
	
	function load_data_subkat()
	{
		$id_subkat = $_POST['id_subkat'];
		$data = db_assoc("SELECT * FROM kas_subkategori_pengeluaran WHERE id_subkat_pengeluaran = $id_subkat");
		echo json_encode($data);
	}
	
	function save_subkat()
	{
		$data=[];
		$data['stat']=0;
		$data['ket']='';
		$pt = $_POST;
		$num = "SELECT 
					COUNT(*) 
				FROM 
					kas_subkategori_pengeluaran 
				WHERE 
					ref = '$pt[ref]' AND nama_subkat_pengeluaran = '$pt[nama]'
					AND id_subkat_pengeluaran != $pt[id_subkat]
					AND del_stat = 0 ";
		$num=db_get($num);
		if($num>0)
		{
			$data['ket']="Sudah ada item lain dengan data yg sama";
		}
		else
		{
			if($pt['mode']==1)
			{
				$que = "INSERT INTO kas_subkategori_pengeluaran(ref,nama_subkat_pengeluaran,id_kat_pengeluaran)
						VALUES ('$pt[ref]','$pt[nama]',$pt[id_kat])";
			}
			else
			{
				$que = "UPDATE kas_subkategori_pengeluaran SET	
							ref = '$pt[ref]', nama_subkat_pengeluaran = '$pt[nama]'
						WHERE id_subkat_pengeluaran = $pt[id_subkat]";
			}
			$res = db_query($que);
			if($res)
			{
				$data['stat']=1;
			}
			else
			{
				$data['ket']="Gagal";
			}
		}
		echo json_encode($data);
	}
	
	function del_subkat()
	{
		$id_subkat = $_POST['id_subkat'];
		$que = "UPDATE kas_subkategori_pengeluaran SET del_stat = 1 WHERE id_subkat_pengeluaran = $id_subkat";
		db_query($que);
	}
	
	function datetime_db($inputDate) {
		$dateTime = DateTime::createFromFormat('d-m-Y H:i', $inputDate);

		if ($dateTime instanceof DateTime) {
			return $dateTime->format('Y-m-d H:i');
		} else {
			$dateTime = DateTime::createFromFormat('Y-m-d H:i', $inputDate);

			if ($dateTime instanceof DateTime) {
				return $dateTime->format('d-m-Y H:i');
			} else {
				return "Invalid date format";
			}
		}
	}
	
}

?>