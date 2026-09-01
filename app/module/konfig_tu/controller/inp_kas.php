<?php

class Inp_Kas extends Controller{
	
	function load_list_trans($print = 0)
	{
		$tgl_awal = tgl_indo($_POST['tgl_awal']);
		$tgl_akhir = tgl_indo($_POST['tgl_akhir']);
		$que = "SELECT
					kas_transaksi.*,
					nama_jns_pemasukan,
					nama_kat_pemasukan ,
					DATE_FORMAT(tanggal,'%d-%m-%Y %H:%i') as tgl
				FROM
					kas_transaksi
					INNER JOIN kas_jenis_pemasukan ON kas_transaksi.id_jns_pemasukan = kas_jenis_pemasukan.id_jns_pemasukan
					INNER JOIN kas_kategori_pemasukan ON kas_transaksi.id_kat_pemasukan = kas_kategori_pemasukan.id_kat_pemasukan
				WHERE
					tanggal BETWEEN '$tgl_awal 00:00:00' AND '$tgl_akhir 23:59:59'
					AND kas_transaksi.del_stat = 0
				ORDER BY tanggal DESC";
					
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
			$tbody.="
			<tr class='cari-item'>
				<td>$dt[tgl]</td>
				<td class='cari-q'>$dt[nama_jns_pemasukan]</td>
				<td class='cari-q'>$dt[nama_kat_pemasukan]</td>
				<td class='cari-q'>$dt[keterangan]</td>
				<td class='text-end'>".rupiah($dt['nominal_in'])."</td>
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
		$que_jns = "SELECT * FROM kas_jenis_pemasukan WHERE del_stat =0";
		$res = db_query($que_jns);
		$in_jenis="<option value='0'>-pilih-</option>";
		while($dt = mysqli_fetch_array($res))
		{
			$in_jenis.="
					<option value = '$dt[id_jns_pemasukan]'>$dt[ref] $dt[nama_jns_pemasukan]</option>";
		}
		
		$que_jns = "SELECT * FROM kas_kategori_pemasukan WHERE del_stat =0";
		$res = db_query($que_jns);
		$kat = [];
		while($dt = mysqli_fetch_assoc($res))
		{
			$idj=$dt['id_jns_pemasukan'];
			if(!isset($kat[$idj]))
			{
				$kat[$idj]=[];
			}
			$kat[$idj][] = $dt;
		}
		
		$data = [];
		$data['in_jenis']=$in_jenis;
		$data['kategori']=$kat;
		
		echo json_encode($data);
	}
	
	function save_pemasukan()
	{
		$pt = $_POST;
		$pt['tanggal']=$this->datetime_db($pt['tanggal']);
		$idu = $_SESSION['id'];
		$que = "INSERT INTO kas_transaksi(
					tanggal,
					keterangan,
					nominal_in,
					id_jns_pemasukan,
					id_kat_pemasukan,
					id_user,
					tipe
				)
				VALUES
				(
					'$pt[tanggal]',
					'$pt[pemasukan]',
					'$pt[nominal]',
					'$pt[jenis]',
					'$pt[kategori]',
					'$idu',
					1
				)
				";
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
		$que_jns = "SELECT * FROM kas_jenis_pemasukan WHERE del_stat =0 ORDER by ref";
		$res = db_query($que_jns);
		$tbody='';
		while($dt = mysqli_fetch_array($res))
		{
			$act="
					<button class='btn btn-xs btn-dark' onclick='modal_jenis(2,$dt[id_jns_pemasukan]);' title='Edit'> 
								<i class='bx bx-pencil'></i> Edit
						</button>";
			$tbody.="
				<tr class='cari-item' id_jenis='$dt[id_jns_pemasukan]' onclick='click_jenis(this,$dt[id_jns_pemasukan]);'>
					<td class='cari-q'>$dt[ref]</td>
					<td class='cari-q '>$dt[nama_jns_pemasukan]</td>
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
	
	function load_data_jenis()
	{
		$id_jenis = $_POST['id_jenis'];
		$data = db_assoc("SELECT * FROM kas_jenis_pemasukan WHERE id_jns_pemasukan = $id_jenis");
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
					kas_jenis_pemasukan 
				WHERE 
					ref = '$pt[ref]' AND nama_jns_pemasukan = '$pt[nama]'
					AND id_jns_pemasukan != $pt[id_jenis]
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
				$que = "INSERT INTO kas_jenis_pemasukan(ref,nama_jns_pemasukan)
						VALUES ('$pt[ref]','$pt[nama]')";
			}
			else
			{
				$que = "UPDATE kas_jenis_pemasukan SET	
							ref = '$pt[ref]', nama_jns_pemasukan = '$pt[nama]'
						WHERE id_jns_pemasukan = $pt[id_jenis]";
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
		$que = "UPDATE kas_jenis_pemasukan SET del_stat = 1 WHERE id_jns_pemasukan = $id_jenis";
		db_query($que);
	}
	
	function load_list_kat()
	{
		$id_jenis = $_POST['id_jenis'];
		$que_kat = "SELECT * FROM kas_kategori_pemasukan WHERE del_stat =0 AND id_jns_pemasukan = $id_jenis ORDER by ref";
		$res = db_query($que_kat);
		$tbody='';
		while($dt = mysqli_fetch_array($res))
		{
			$act="
					<button class='btn btn-xs btn-dark' onclick='modal_kat(2,$dt[id_kat_pemasukan]);' title='Edit'> 
								<i class='bx bx-pencil'></i>
						</button>";
			$tbody.="
				<tr class='cari-item' id_kat='$dt[id_kat_pemasukan]' onclick='click_kat(this,$dt[id_kat_pemasukan]);'>
					<td class='cari-q'>$dt[ref]</td>
					<td class='cari-q '>$dt[nama_kat_pemasukan]</td>
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
		$data = db_assoc("SELECT * FROM kas_kategori_pemasukan WHERE id_kat_pemasukan = $id_kat");
		echo json_encode($data);
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
					kas_kategori_pemasukan 
				WHERE 
					ref = '$pt[ref]' AND nama_kat_pemasukan = '$pt[nama]'
					AND id_kat_pemasukan != $pt[id_kat]
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
				$que = "INSERT INTO kas_kategori_pemasukan(ref,nama_kat_pemasukan,id_jns_pemasukan)
						VALUES ('$pt[ref]','$pt[nama]',$pt[id_jenis])";
			}
			else
			{
				$que = "UPDATE kas_kategori_pemasukan SET	
							ref = '$pt[ref]', nama_kat_pemasukan = '$pt[nama]'
						WHERE id_kat_pemasukan = $pt[id_kat]";
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
		$que = "UPDATE kas_kategori_pemasukan SET del_stat = 1 WHERE id_kat_pemasukan = $id_kat";
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