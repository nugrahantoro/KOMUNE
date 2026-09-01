<?php
class Mobileapp extends Controller
{
    var $content;

    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Informasi & Pengumuman";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("mobileapp/view/v_data_mobileapp");
        $this->view("main/view/footer");
    }

    public function agenda($param1 = "", $param2 = "")
    {
        $data["title"] = "Agenda";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("mobileapp/view/v_agenda");
        $this->view("main/view/footer");
    }
	
    public function slide($param1 = "", $param2 = "")
    {
        $data["title"] = "Slide Informasi";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("mobileapp/view/v_data_mobileapp");
        $this->view("main/view/footer");
    }

    public function aktivitas($param1 = "", $param2 = "")
    {
        $data["title"] = "Aktivitas Siswa";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("mobileapp/view/v_aktivitas");
        $this->view("main/view/footer");
    }

    public function officer($param1 = "", $param2 = "")
    {
        $data["title"] = "Manajemen User Officer";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("mobileapp/view/v_officer");
        $this->view("main/view/footer");
    }

    public function alat($param1 = "", $param2 = "")
    {
        $data["title"] = "Manajemen Alat";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("mobileapp/view/v_alat");
        $this->view("main/view/footer");
    }

    public function event($param1 = "", $param2 = "")
    {
        $data["title"] = "Manajemen Event";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("mobileapp/view/v_event");
        $this->view("main/view/footer");
    }

    public function daftar_libur($param1 = "", $param2 = "")
    {
        $data["title"] = "Daftar Libur";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("mobileapp/view/v_daftar_libur");
        $this->view("main/view/footer");
    }
	
    public function ex($param1,$params2)
    {
        $ex_controller = '';
        if (file_exists("app/module/mobileapp/controller/$param1.php"))
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
        $this->get_js("mobileapp/js/" .$param1);
    }
}

?>
