<?php
class Doc extends Controller
{
	function index()
	{
		echo "404 :')";
	}
	
	function surat_tagihan()
	{
		$this->run_class_function('laporan_rekap_tagihan/htmltables');
	}
	
	
	
	/////// RETAIL 
	function tap_kartu()
	{
		$this->run_class_function("retail/ex/penjualan/tap_kartu_set");
	}
	
	function get_faktur($mode,$id_user='ex1')
	{
		$_SESSION['id'] =$id_user;
		echo $this->run_class_function("retail/get_no_faktur/$mode");
	}
	
	function cetak_nota($mode,$id_transaksi='0')
	{
		$_GET['id_transaksi'] =$id_transaksi;
		echo $this->run_class_function("retail/ex/$mode/cetak_nota");
	}
	
	////////
	
	
	function test()
	{
		$_GET['periode']='2024-01';
		$this->run_class_function('konfig_tu/ex/lap_kas/cetak_neraca');
	}
	
	
}

?>