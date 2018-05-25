<?php

/**
 * @param string $filename
 * @return bool|mixed
 */
function config($filename, $objectForMap = false, $ext = "php"){
    return \Racine\Config::get($filename, $objectForMap, $ext);
}

/**
 * @return string
 */
function base_path() {
    return config('app')['path'];
}


/**
 * @param string $uri
 * @return string
 */
function path($uri = ''){
    return base_path().ltrim($uri, '\\/');
}

/**
 * @param $path
 * @return string
 */
function asset($path){
    if(!defined('_PUBLIC_DIRNAME_')) define('_PUBLIC_DIRNAME_', '');
    return path().ltrim(trim(_PUBLIC_DIRNAME_, '/').DIRECTORY_SEPARATOR.$path, '\\/');
}

/**
 * @return \Racine\Http\Request
 */
function request(){
    return \Racine\Application::getInstance()->getRequest();
}

/**
 * @return \Racine\Logger\Logger
 */
function logger(){
    return \Racine\Application::getInstance()->getLogger();
}

/**
 * @return null|\Racine\Http\Session
 */
function session(){
    return request()->getSession();
}

/**
 * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
 */
function flash(){
    $session = session();
    if(!is_null($session) && method_exists($session, 'getFlashBag'))
        return $session->getFlashBag();
    return null;
}

/**
 * @return \Racine\Security\Authentication\Token\TokenInterface|null
 */
function token(){
    return \Racine\Application::getInstance()->getToken();
}

/**
 * @return null|\Racine\Security\User\UserInterface|\Racine\Model
 */
function user(){
    if(token() instanceof \Racine\Security\Authentication\Token\TokenInterface)
        return token()->getUser();
    return null;
}

/**
 * Retreive real internal uri for a given request uri
 *
 * @param string $uri
 */
function internal_request_uri($uri = null){
    if(is_null($uri)){
        $uri = request()->getRequestUri();
    }
    $pos = strpos($uri, config('app')['path']);
    if($pos === 0){
        return substr($uri, strlen(config('app')['path'])-1);
    }else{
        return $uri;
    }
    
}

/**
 * @param array $array
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

/**
 * @param array $array
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

function normalize_validator_messages($errors){
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

/**
 * @param int $size
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

/**
 * @param $array
 * @return array
 */
function super_array_unique($array)
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

/**
 * @param $array
 * @param string $key
 * @return array
 */
function unique_multidim_array($array, $key) {
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

/**
 * @param $name
 * @param null $default
 * @return array|false|null|string
 */
function env($name, $default = null) {
    if(getenv($name) === false) {
        return $default;
    }
    return getenv($name);
}