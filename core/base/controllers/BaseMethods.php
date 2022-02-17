<?php


namespace core\base\controllers;


trait BaseMethods
{
    protected $styles;
    protected $scripts;
    protected function init($admin = false){

        if(!$admin){
            if(USER_CSS_JS['styles']){
                foreach(USER_CSS_JS['styles'] as $item) $this->styles[] = PATH . TEMPLATE . trim($item, '/');
            }

            if(USER_CSS_JS['styles']){
                foreach(USER_CSS_JS['scripts'] as $item) $this->styles[] = PATH . TEMPLATE . trim($item, '/');
            }
        }else{
            if(ADMIN_CSS_JS['styles']){
                foreach(USER_CSS_JS['styles'] as $item) $this->styles[] = PATH . TEMPLATE . trim($item, '/');
            }

            if(ADMIN_CSS_JS['styles']){
                foreach(USER_CSS_JS['scripts'] as $item) $this->styles[] = PATH . TEMPLATE . trim($item, '/');
            }
        }
    }
    protected function clearStr($str){  // метод отчистки для строк

        if(is_array($str)) {
            foreach ($str as $key=>$item) $str[$key] = trim(strip_tags($item));
            return $str;
        }else{
            return trim(strip_tags($str));
        }
    }
    protected function clearNum($num){ // простой способ из строки в число
        return $num * 1;
    }

    protected function isPost(){
        return $_SERVER['REQUEST_METHOD'] == 'POST'; // true если метод совпадет
    }

    protected function isAjax(){ // если метод придет из jqery то появится ячейка со значением xml http request
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'; // isset - существует ли ячейка
    }

    protected function redirect($http = false, $code = false){  // метод перенаправления
        if($code){
           $codes = ['301' => 'HTTP/1.1 301 Move Permanently'];

           if($codes[$code]){
               header($codes[$code]);
           }
        }
        if($http) $redirect = $http;
        else $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : PATH;

        header('Location: ' . $redirect);
        exit;
    }
    protected function writeLog($message, $file = 'log.txt', $event = 'Fault'){

        $dateTime = new \DateTime();
        $str = $event . ': ' . $dateTime->format('d-m-Y G:i:s') . ' - ' . $message . "\r\n";

        file_put_contents('log/' . $file, $str, FILE_APPEND);
    }
}