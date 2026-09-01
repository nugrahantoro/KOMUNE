<?php
class Konfig_tu extends Controller
{
    var $content;

    public function index($param1 = "", $param2 = "")
    {
		global $database;
        $data["title"] = "Konfigurasi Pembayaran";
        $data["href"] = "";
        $data["dbn"] = $database;
        $this->view("main/view/header", $data);
        $this->view("konfig_tu/view/v_konfig_tu",$data);
        $this->view("main/view/footer");
    }
	
	public function transaksi_bayar($param1 = "", $param2 = "")
    {
        $data["title"] = "Transaksi Pembayaran";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("konfig_tu/view/v_transaksi_bayar");
        $this->view("main/view/footer");
    }
	
    public function transaksi_emoney($param1 = "", $param2 = "")
    {
        $data["title"] = "Transaksi E-Money";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("konfig_tu/view/v_transaksi_emoney");
        $this->view("main/view/footer");
    }

	public function pemasukan_kas($param1 = "", $param2 = "")
    {
        $data["title"] = "Pemasukan Kas";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("konfig_tu/view/v_inp_kas_in");
        $this->view("main/view/footer");
    }
	
	public function pengeluaran_kas($param1 = "", $param2 = "")
    {
        $data["title"] = "Pengeluaran Kas";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("konfig_tu/view/v_inp_kas_out");
        $this->view("main/view/footer");
    }
	
	public function laporan_kas($param1 = "", $param2 = "")
    {
        $data["title"] = "Laporan Kas";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("konfig_tu/view/v_lap_kas");
        $this->view("main/view/footer");
    }
	
	public function ex($param1,$params2,$params3=null)
	{
		$ex_controller = '';
		if (file_exists("app/module/konfig_tu/controller/$param1.php"))
		{
			$ex_controller=$param1;
		}
		if($ex_controller != '')
		{
			require_once "$param1.php";
			$ex_controller = new $ex_controller;
			//Method
			if(isset($params2))
			{
				if (method_exists($ex_controller,$params2))
				{
				//Run Controller & Method. Send Paramaters if exists
				return call_user_func_array([$ex_controller,$params2],[$params3]);
				}
			}
		}
	}
	
	public function js($param1)
    {
        $this->get_js("konfig_tu/js/" .$param1);
    }
}

?>
