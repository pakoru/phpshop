<?php

namespace core\base\controllers;

use core\base\exceptions\RouteException;
use core\base\settings\Settings;
use core\base\settings\ShopSettings;

class RouteController
{

    static private $_instance;

    protected $routes;
    protected $controller;
    protected $inputMethod;
    protected $outputMethod;
    protected $parameters;


    private function __clone()
    {
    }

    static public function getInstance(){
        if(self::$_instance instanceof self){
            return self::$_instance;
        }
        return self::$_instance = new self;
    }


    private function __construct()
    {
        $adress_str = $_SERVER['REQUEST_URI']; // getting address bar
        if(strrpos($adress_str, '/') === strlen($adress_str) - 1 && strrpos($adress_str, '/') !== 0){
            $this->redirect(rtrim($adress_str, '/'), 301); // rtrim — Удаляет пробелы (или другие символы) из конца строки
        }

        $path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php')); // PHP SELF - index of script
        // substr Возвращает подстроку строки string, начинающейся с offset символа по счету и длиной length символов.
        // strpos — Возвращает позицию первого вхождения подстроки. Ищет позицию первого вхождения подстроки needle в строку haystack.

        if ($path === PATH){
            $this->routes = Settings::get('routes');
            if (!$this->routes) throw new RouteException('The site is under maintenance');

            if(strrpos($adress_str, $this->routes['admin']['alias']) === strlen(PATH)){  // trying to use Admin Panel

                // ADMINKA

            }else{  // user panel
                $url = explode('/', substr($adress_str, strlen(PATH)));  // cut localhost <- /path/path -> in array
                $hrUrl = $this->routes['user']['hrUrl'];
                $this->controller = $this->routes['user']['path'];
                $route = 'user';
            }
            $this->createRoute($route, $url);

            exit();
        }else{
            try{
                throw new \Exception('Incorrect site directory');
            }
            catch(\Exception $e){
                exit($e->getMessage());
            }
        }
    }
    private function createRoute($var, $arr){
        $route = [];

        if (!empty($arr[0])){
            if($this->routes[$var]['routes'][$arr[0]]) {
                $route = explode('/', $this->routes[$var]['routes'][$arr[0]]);

                $this->controller .= ucfirst($route[0].'Controller');                            // to Uppercase for controller
            }
            else{
                $this->controller .= ucfirst($arr[0].'Controller');
            }
        }else{
            $this->controller .= $this->routes['default']['controller'];
        }
        $this->inputMethod = $route[1] ? $route[1] : $this->routes['default']['inputMethod'];  // сокращенное if else
                            // если тут есть, то запишем роут 1, если пусто, то пишем в метод
        $this->outputMethod = $route[2] ? $route[2] : $this->routes['default']['outputMethod'];

        return;
    }
}
