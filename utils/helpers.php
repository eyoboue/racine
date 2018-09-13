<?php

if(!function_exists('config')){
    
    /**
     * @param string $filename
     * @param bool $objectForMap
     * @param string $ext
     * @return mixed
     */
    function config($filename, $objectForMap = false, $ext = "php"){
        return \Racine\Config::get($filename, $objectForMap, $ext);
    }
}

if(!function_exists('base_path')){
    /**
     * @return string
     */
    function base_path() {
        return config('app')['path'];
    }
}

if(!function_exists('path')){
    /**
     * @param string $uri
     * @return string
     */
    function path($uri = ''){
        return base_path().ltrim($uri, '\\/');
    }
}

if(!function_exists('asset')){
    /**
     * @param string $path
     * @return string
     */
    function asset($path){
        if(!defined('_PUBLIC_DIRNAME_')) define('_PUBLIC_DIRNAME_', '');
        return path().ltrim(trim(_PUBLIC_DIRNAME_, '/').DIRECTORY_SEPARATOR.$path, '\\/');
    }
}

if(!function_exists('request')){
    /**
     * @return \Racine\Http\Request
     */
    function request(){
        return \Racine\Application::getInstance()->getRequest();
    }
}

if(!function_exists('logger')){
    /**
     * @return \Racine\Logger\Logger
     */
    function logger(){
        return \Racine\Application::getInstance()->getLogger();
    }
}

if(!function_exists('session')){
    /**
     * @return null|\Racine\Http\Session
     */
    function session(){
        return request()->getSession();
    }
}

if(!function_exists('flash')){
    /**
     * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
     */
    function flash(){
        $session = session();
        if(!is_null($session) && method_exists($session, 'getFlashBag'))
            return $session->getFlashBag();
        return null;
    }
}

if(!function_exists('token')){
    /**
     * @return \Racine\Security\Authentication\Token\TokenInterface|null
     */
    function token(){
        return \Racine\Application::getInstance()->getToken();
    }
}

if(!function_exists('user')){
    /**
     * @return null|\Racine\Security\User\UserInterface|\Racine\Model
     */
    function user(){
        if(token() instanceof \Racine\Security\Authentication\Token\TokenInterface)
            return token()->getUser();
        return null;
    }
}

if(!function_exists('internal_request_uri')){
    /**
     * Retrieve real internal uri for a given request uri
     *
     * @param string $uri
     * @return string
     */
    function internal_request_uri($uri = null){
        $path = config('app')['path'];
        if(empty($uri)){
            $uri = request()->getRequestUri();
        }
        if(empty($path)) return $uri;
        $pos = strpos($uri, $path);
        if($pos === 0){
            return substr($uri, strlen(config('app')['path'])-1);
        }else{
            return $uri;
        }
    }
}

if(!function_exists('model_collection_to_array')){
    /**
     * @param array $array
     * @param array $options
     * @return array
     */
    function model_collection_to_array(array $array = [], array $options = [])
    {
        return array_map(
            function($value) use ($options){
                return $value->to_array($options);
            },
            $array
        );
    }
}

if(!function_exists('model_collection_to_json')){
    /**
     * @param array $array
     * @param array $options
     * @return array
     */
    function model_collection_to_json(array $array = [], array $options = [])
    {
        return array_map(
            function($value) use ($options){
                return $value->to_json($options);
            },
            $array
        );
    }
}

if(!function_exists('normalize_validator_messages')){
    /**
     * @param array $errors
     * @return array
     */
    function normalize_validator_messages(array $errors){
        $normalized_messages = [];
        foreach ($errors as $field => $error){
            foreach ($error as $message){
                $human_attr = \ActiveRecord\Utils::human_attribute($field);
                if(strstr($message, $human_attr)){
                    $normalized_messages[$field][] = substr($message, strlen($human_attr)+1);
                }
            }
        }
        return $normalized_messages;
    }
}

if(!function_exists('convertToReadableSize')){
    
    /**
     * @param number $size
     * @param string $lang
     * @return string
     */
    function convertToReadableSize($size, $lang = 'fr'){
        $suffix = [
            'fr' => ["octets", "Ko", "Mo", "Go", "To"],
            'en' => ["bytes", "KB", "MB", "GB", "TB"],
        ];
        if(!is_numeric($size)) $size = 0;
        if($size <= 0) return '0 '. $suffix[$lang][0];
        $base = log($size) / log(1024);
        $f_base = floor($base);
        return round(pow(1024, $base - floor($base)), 1) .' '. $suffix[$lang][$f_base];
    }
}

if(!function_exists('super_array_unique')){
    /**
     * @param array $array
     * @return array
     */
    function super_array_unique(array $array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));
        foreach ($result as $key => $value)
        {
            if ( is_array($value) )
            {
                $result[$key] = super_array_unique($value);
            }
        }
        return $result;
    }
}

if(!function_exists('unique_multidim_array')){
    /**
     * @param array $array
     * @param string $key
     * @return array
     */
    function unique_multidim_array(array $array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();
        
        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
}

if(!function_exists('slugit')){
    /**
     * @param string $str
     * @param array $replace
     * @param string $delimiter
     * @return null|string|string[]
     */
    function slugit($str, $replace=array(), $delimiter='-') {
        if ( !empty($replace) ) {
            $str = str_replace((array)$replace, ' ', $str);
        }
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        return $clean;
    }
}

if(!function_exists('env')){
    /**
     * @param string $name
     * @param null $default
     * @return array|false|null|string
     */
    function env($name, $default = null) {
        $value = getenv($name);
        if($value === false) {
            return $default;
        }
        return $value;
    }
}

if(!function_exists('dd')){
    /**
     * @param $var
     */
    function dd($var){
        dump($var);
        die();
    }
}