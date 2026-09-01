<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

$controller_name = '';

class App
{
    protected $controller = 'main';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        global $controller_name;

        $url = $this->parseURL();

        if (empty($url) || empty($url[0])) {
            $url = isset($_SESSION['id']) ? ['main'] : ['login'];
        }

        $targetModule = preg_replace('/[^a-zA-Z0-9_\-]/', '', $url[0]);

        $publicControllers = defined('PUBLIC_CONTROLLER') ? PUBLIC_CONTROLLER : ['doc', 'feed'];

        if (!in_array($targetModule, $publicControllers) && !isset($_SESSION['id'])) {
            $targetModule = 'login';
        }

        $controllerPath = 'app/module/' . $targetModule . '/controller/' . $targetModule . '.php';

        if (file_exists($controllerPath)) {
            $this->controller = $targetModule;
            unset($url[0]);
        } else {
            $this->controller = 'main';
            $this->method = 'page_not_found';
        }

        $controller_name = $this->controller;
        require_once 'app/module/' . $this->controller . '/controller/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Method
        if (isset($url[1])) {
            $targetMethod = preg_replace('/[^a-zA-Z0-9_]/', '', $url[1]);
            if (method_exists($this->controller, $targetMethod)) {
                $this->method = $targetMethod;
                unset($url[1]);
            }
        }

        // Parameters
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // Run Controller & Method
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode("/", $url);
        }
        return [];
    }
}