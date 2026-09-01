<?php

class Daftar_libur extends Controller{

	function is_working_url($url) {
	      $handle = curl_init($url);
	      curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	      curl_setopt($handle, CURLOPT_NOBODY, true);
	      curl_exec($handle);
	     
	      $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	      curl_close($handle);
	     
	      if ($httpCode >= 200 && $httpCode < 300) {
	        return $httpCode;
	      }
	      else {
	        return $httpCode;
	      }
    }

	function load_data(){
		$dt = $_POST;
		$query = "
			SELECT a.*, b.nama_tahunajar FROM hari_libur_nasional a JOIN data_tahunajar b ON b.id_tahunajar = a.id_sesi WHERE a.id_sesi = '$dt[id_ta]' AND a.id_jenjang = $dt[id_jenjang] ORDER BY tanggal ASC
		";
		$res = db_query($query);
		$no = 1;
		$tbody = '';
		while($dt=mysqli_fetch_array($res))
		{
			$btn = "<button class='btn btn-sm btn-dark' onclick='modal_libur(event,2,$dt[id]);'>
						<i class='bx bx-edit'></i> Edit
					</button>";
			$tbody .= "
				<tr class='cari-item'>
					<td>".$no++."</td>
					<td width='20%' class='cari-q'>".tgl_indo($dt['tanggal'])."</td>
					<td class='cari-q'>".$dt['keterangan']."</td>
					<td class='cari-q'>".$dt['nama_tahunajar']."</td>
					<td width='8%'>".$btn."</td>
				</tr>
			";
		}

		$no_res = "display:none;";
		if(mysqli_num_rows($res)==0)
		{
			$no_res = "";
		}
		$tbody .="<tr class=' tc libur_no_result' style='$no_res'>
					<td colspan='6'>Tidak Ada Data</td>
				</tr>";

		$table = "
			<table class='table table-sm m-0' style='font-size: 12px;'>
				<thead>
				<tr>
					<th width='3%'>No.</th>
					<th>Tanggal</th>
					<th>Keterangan</th>
					<th>Tahun Ajaran</th>
					<th></th>
				</tr>
				</thead>
				<tbody class='table-hover'>
					$tbody
				</tbody>
			</table>";
		echo $table;
	}

	function get_data_libur(){
		$pt = $_POST;
		$data = db_assoc("SELECT id, tanggal, keterangan FROM hari_libur_nasional WHERE id = $pt[id_libur]");
		echo json_encode($data);
	}

	function save_libur(){
		$pt = $_POST;

        if ($pt['mode']=="1") {
        	$query = "
            	INSERT INTO 
            		hari_libur_nasional ( tanggal, keterangan, id_sesi, id_jenjang )
            	VALUES
                	( '$pt[tanggal]', '$pt[keterangan]', '$pt[id_ta]', '$pt[id_jenjang]' )
            ";
            $proses = db_query($query);
            if ($proses) {
            	$response =  array(
	            	'stat'	=> 1,
	            	'ket' 	=> "hari libur berhasil ditambah"
	            );
            }
            else{
            	$response =  array(
	            	'stat'	=> 0,
	            	'ket' 	=> "hari libur gagal ditambah"
	            );
            }
        }
        else{
        	$upd = "UPDATE hari_libur_nasional SET tanggal = '$pt[tanggal]', keterangan = '$pt[keterangan]' WHERE id = $pt[id_libur]";
        	$proses = db_query($upd);
            if ($proses) {
            	$response =  array(
	            	'stat'	=> 1,
	            	'ket' 	=> "hari libur berhasil dirubah"
	            );
            }
            else{
            	$response =  array(
	            	'stat'	=> 0,
	            	'ket' 	=> "hari libur gagal dirubah"
	            );
            }
        }
        echo json_encode($response);
	}

	function del_libur(){
		$pt = $_POST;

		$del = "DELETE FROM hari_libur_nasional WHERE id = $pt[id_libur]";
		$proses = db_query($del);
		if ($proses) {
			$response =  array(
	        	'stat'	=> 1,
	        	'ket' 	=> "hari libur berhasil dihapus"
	        );
		}
		else{
			$response =  array(
	        	'stat'	=> 0,
	        	'ket' 	=> "hari libur gagal dihapus"
	        );
		}
		echo json_encode($response);
	}
}
?>