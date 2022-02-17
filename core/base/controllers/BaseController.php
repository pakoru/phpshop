<?php


namespace core\base\controllers;


use core\base\exceptions\RouteException;
use core\base\settings\Settings;

abstract class BaseController
{
    use \core\base\controllers\BaseMethods;

    protected $page;
    protected $errors;

    protected $controller;
    protected $inputMethod;
    protected $outputMethod;
    protected $parameters;

    public function route(){
      $controller = str_replace('/', '\\', $this->controller);   // / -> \\
       try{

           $object = new \ReflectionMethod($controller, 'request');// when reflectionmethod created it's looking for request
           $args = [
               'parameters' => $this->parameters,
               'inputMethod' => $this->inputMethod,
               'outputMethod' => $this->outputMethod
           ];

           $object->invoke(new $controller, $args);

       }catch(\ReflectionException $e){
           throw new RouteException($e->getMessage());
       }

    }
    public function request($args){
        $this->parameters = $args['parameters'];

        $inputData = $args['inputMethod'];
        $outputData = $args['outputMethod'];

        $data = $this->$inputData();

        if(method_exists($this, $outputData)){
            $page = $this->$outputData($data);
            if($page) $this->page = $page;
        }
        elseif($data){
            $this->page = $data;
        }


        if ($this->errors){
            $this->writeLog();
        }

        $this->getPage();
    }

    protected function render($path = '', $parameters = []){  // шаблонизатор

        extract ($parameters); // создает переменные из массива вида ключ - значение

        if(!$path){

            $class = new \ReflectionClass($this);

            $space = str_replace( '\\', '/',$class->getNamespaceName() . '\\');
            $routes = Settings::get('routes');

            if($space === $routes['user']['path']) $template = TEMPLATE;
                else $template = ADMIN_TEMPLATE;

            $path = $template . explode('controller', strtolower((new \ReflectionClass($this))->getShortName()))[0]; // разбор строки и преобразование имени класса в нижний регистр
        };

        ob_start();     // буфер обмена

        if(!@include_once $path . '.php') throw new RouteException('Template "'. $path . '" does not exist');

        return ob_get_clean();

    }

    protected function getPage(){
        if(is_array($this->page)){
            foreach ($this->page as $block) echo $block;
        }else{
            echo $this->page;
        }
        exit();
    }
}