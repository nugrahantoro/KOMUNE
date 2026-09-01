<?php
class Sistem extends Controller
{
    var $content;

    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Data User";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("main/view/data_user");
        $this->view("main/view/footer");
    }
	
    public function data_user($param1 = "", $param2 = "")
    {
        $data["title"] = "Data User";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("sistem/view/v_data_user");
        $this->view("main/view/footer");
    }
	
    public function manajemen_user($param1 = "", $param2 = "")
    {
        $data["title"] = "Manajemen User";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("sistem/view/v_manajemen_user");
        $this->view("main/view/footer");
    }
	
    public function ta_jenjang($param1 = "", $param2 = "")
    {
        $data["title"] = "Tahun Ajaran & Jenjang";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("sistem/view/v_ta_jenjang");
        $this->view("main/view/footer");
    }
	
    public function manajemen_menu($param1 = "", $param2 = "")
    {
        $data["title"] = "Manajemen Menu";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("sistem/view/v_manajemen_menu");
        $this->view("main/view/footer");
    }
	
	public function ex($param1,$params2)
	{
		$ex_controller = '';
		if (file_exists("app/module/sistem/controller/$param1.php"))
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
				call_user_func_array([$ex_controller,$params2],[]);
				}
			}
		}
	}
	
    public function js($param1)
    {
        $this->get_js("sistem/js/" .$param1);
    }
}

?>
