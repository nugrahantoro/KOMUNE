<?php

class Controller {
	public	function __construct()
	{
		global $controller_name;
		$this->controller_name=$controller_name;
	}
	public function view($view,$data=[1,1])
	{
		//require_once '../app/views/'.$view.'.php';
		require_once 'app/module/'.$view.'.php';
	}	
	public function get_js($file)
	{
		if (!headers_sent()) {
			header("Content-Type: application/javascript; charset=UTF-8");
		}
		if (file_exists('app/module/' . $file)) {
			require_once 'app/module/' . $file;
		} else if (file_exists($file)) {
			require_once $file;
		}
	}	
	
	public function run_class_function($url='')
	{
		if($url=='')
		{
			return;
		}
		$url = explode("/",$url);
		$controller='';
		$method = '';
		$params = [];
		
		if(!is_null($url))
		{
			if (file_exists('app/module/'.$url[0].'/controller/'.$url[0].'.php'))
			{
				$controller=$url[0];
				unset($url[0]);
			}
			else
			{
				$url[1]='page_not_found';
			}
		}
		
		require_once 'app/module/'.$controller.'/controller/'.$controller.'.php';
		$controller = new $controller;
		//Method
		if(isset($url[1]))
		{
			if (method_exists($controller,$url[1]))
			{
				$method = $url[1];
				unset($url[1]);
			}
		}
		
		if($controller=='' || $method=='')
		{
			return;
		}
		
		//Paramater
		if(!empty($url))
		{
			$params = array_values($url);
		}
		//Run Controller & Method. Send Paramaters if exists
		return call_user_func_array([$controller,$method],$params);
	}
}

?>