<?php

class Akuntansi extends Controller
{
	function jurnal_create_tagihan($id_master_trans_sis)
	{
		$sql = "SELECT
					nama_transaksi,
					nominal 
				FROM
					master_alokasi_transaksi_all
					INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi 
				WHERE
					id_master_trans_sis = $id_master_trans_sis";
		$tgh = db_assoc($sql);
		
		//Akun Debit
		$id_akun = 100;
		$ins_debit = "
				INSERT INTO akuntan_tbl_jurnal_transaksi(
					id_akun,
					deskripsi,
					debit,
					id_master_trans_sis
				)
				VALUES (
					$id_akun,
					'$tgh[nama_transaksi]',
					$tgh[nominal],
					$id_master_trans_sis
				)";
		db_query($ins_debit);
		
		//Akun Kredit
		$id_akun = 200;
		$ins_kredit = "
				INSERT INTO akuntan_tbl_jurnal_transaksi(
					id_akun,
					deskripsi,
					kredit,
					id_master_trans_sis
				)
				VALUES (
					$id_akun,
					'$tgh[nama_transaksi]',
					$tgh[nominal],
					$id_master_trans_sis
				)";
		db_query($ins_kredit);
	}
	
	function jurnal_update_tagihan($id_master_trans_sis)
	{
		$sql = "SELECT
					nama_transaksi,
					nominal 
				FROM
					master_alokasi_transaksi_all
					INNER JOIN master_jenis_transaksi ON master_alokasi_transaksi_all.id_jenis_transaksi = master_jenis_transaksi.id_jenis_transaksi 
				WHERE
					id_master_trans_sis = $id_master_trans_sis";
		$tgh = db_assoc($sql);
		
		//Akun Debit
		$id_akun = 100;
		$upd_debit = "
				UPDATE akuntan_tbl_jurnal_transaksi
				SET 
					deskripsi= '$tgh[nama_transaksi]',
					debit = $tgh[nominal]
				WHERE
					id_master_trans_sis = $id_master_trans_sis
					AND id_akun = $id_akun
				";
		db_query($upd_debit);
		
		//Akun Kredit
		$id_akun = 200;
		$upd_kredit = "
				UPDATE akuntan_tbl_jurnal_transaksi
				SET 
					deskripsi= '$tgh[nama_transaksi]',
					kredit = $tgh[nominal]
				WHERE
					id_master_trans_sis = $id_master_trans_sis
					AND id_akun = $id_akun
				";
		db_query($upd_kredit);
		
	}
	
	function jurnal_delete_tagihan($id_master_trans_sis)
	{
		$que = "UPDATE akuntan_tbl_jurnal_transaksi SET del_stat = 1 WHERE id_master_trans_sis = $id_master_trans_sis";
		//$que = "DELETE FROM akuntan_tbl_jurnal_transaksi WHERE id_master_trans_sis = $id_master_trans_sis";
		db_query($que);
	}
	
	function test($param)
	{
		echo 'oke '.$param;
	}
	
	function run_func()
	{
		/* 
		require_once 'app/module/akuntansi/controller/akuntansi.php';
		$akuntan = new Akuntansi();
		$akuntan->test('bre');
		
		OR
		
		$this->run_class_function('akuntansi/test/bre');
		
		 */
		 
	}
	
	
}

?>