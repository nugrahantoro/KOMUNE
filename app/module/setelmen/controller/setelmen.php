<?php
class Setelmen extends Controller
{
    var $content;

    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Setelmen Dana";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("main/view/data_setelmen");
        $this->view("main/view/footer");
    }
	
    public function data_setelmen($param1 = "", $param2 = "")
    {
        $data["title"] = "Setelmen Dana";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("setelmen/view/v_data_setelmen");
        $this->view("main/view/footer");
    }
	
    public function ex($param1,$params2)
    {
        $ex_controller = '';
        if (file_exists("app/module/setelmen/controller/$param1.php"))
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
        $this->get_js("setelmen/js/" .$param1);
    }
}

?>
