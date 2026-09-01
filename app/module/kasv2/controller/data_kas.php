<?php

class Data_Kas extends Kasv2{
	
	function list_kas()
	{
		$que = "SELECT * FROM kas_jenis WHERE del_stat = 0 ORDER BY nama_kas";
		$res = db_query($que);
		$tbody='';
		$no=1;
		while($dt = mysqli_fetch_array($res))
		{
			$saldo = 0;
			$tbody.="
				<tr>
					<td>".$no++."</td>
					<td>".$dt['nama_kas']."</td>
					<td class='text-end'>".rupiah($saldo)."</td>
					<td class='text-center'>
						".'
							<div class="btn-group">
                                                <button type="button" class="btn btn-success btn-sm" onclick="modal_transfer(1,0,'.$dt['id_jns_kas'].')">Transfer</button>
                                                <button type="button" class="btn btn-success btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <div class="dropdown-menu" style="">
                                                    <a class="dropdown-item btn-sm" href="#" onclick="event.preventDefault();modal_jns_kas(2,'.$dt['id_jns_kas'].')">
														<i class="bx bx-pencil"></i> Edit
													</a>
                                                </div>
                                            </div>
						'."
					</td>
				</tr>
			";
		}
		$table = "
			<table class='table-bordered table table-sm'>
			<thead>
				<tr>
					<th class='text-center'>#</th>
					<th class='text-center'>Jenis Kas</th>
					<th class='text-center'>Saldo</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
				
	}
	
	function load_select_jenis()
	{
		$que = "SELECT * FROM kas_jenis WHERE del_stat = 0 ORDER BY nama_jns_kas";
		$res = db_query($que);
		$options = '<option value = 0>-pilih-</option>';
		while($dt = mysqli_fetch_array($res))
		{
			$options.="<option value = $dt[id_jns_kas]>$dt[nama_jns_kas]</option>";
		}
		echo $options;
	}
	
	function list_trans_transfer()
	{
		$query = "SELECT
					id_transfer,
					tanggal,
					tb_sumber.nama_kas AS sumber,
					tb_tujuan.nama_kas AS tujuan,
					nominal,
					keterangan
				FROM
					kas_transaksi_transfer
					INNER JOIN kas_jenis AS tb_sumber ON tb_sumber.id_jns_kas = kas_transaksi_transfer.id_jns_kas_from
					INNER JOIN kas_jenis AS tb_tujuan ON tb_tujuan.id_jns_kas = kas_transaksi_transfer.id_jns_kas_to 
				WHERE
					kas_transaksi_transfer.del = 0 
				ORDER BY
					tanggal DESC";
		$res = db_query($query);
		$tbody='';
		while($dt=mysqli_fetch_array($res))
		{
			$tbody.="
				<tr>
					<td>$dt[tanggal]</td>
					<td>$dt[sumber]</td>
					<td>$dt[tujuan]</td>
					<td>".rupiah($dt['nominal'])."</td>
					<td>$dt[keterangan]</td>
					<td></td>
				</tr>
			
			";
		}
		$table = "
			<table class='table-stripped table table-sm'>
			<thead>
				<tr>
					<th >Tanggal</th>
					<th >Sumber</th>
					<th >Tujuan</th>
					<th >Nominal</th>
					<th>Keterangan</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				$tbody
			</tbody>
			</table>";
		echo $table;
	}
	
	function get_jns_kas()
	{
		$que = "SELECT * FROM kas_jenis WHERE id_jns_kas = $_POST[id_jns_kas]";
		$data = db_assoc($que);
		echo json_encode($data);
	}
	
	function save_jns_kas()
	{
		$pt = $_POST;
		if($pt['mode']==1)
		{
			$que = "INSERT INTO kas_jenis(
						nama_kas,
						saldo_awal
					)
					VALUES
					(
						'$pt[nama_jns_kas]',
						$pt[saldo_awal]
					)
					";
		}
		else
		{
			$que = "UPDATE `kas_jenis` 
					SET 
					`nama_kas` = '$pt[nama_jns_kas]',
					`saldo_awal` = '$pt[saldo_awal]'
					WHERE
						`id_jns_kas` = $pt[id_jns_kas]";
		}
		db_query($que);
		echo 'ok';
	}
	
	function test()
	{
		$periode = $this->get_periode();
		$saldo = $this->get_saldo_akhir($periode,5);
		echo $saldo;
	}
	
	function getLastDayOfMonth($yearMonth) {
		$lastDayOfMonth = date('Y-m-t', strtotime("$yearMonth-01"));
		return $lastDayOfMonth;
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