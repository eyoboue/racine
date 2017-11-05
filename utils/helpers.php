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
    return path()._PUBLIC_DIRNAME_.DIRECTORY_SEPARATOR.ltrim($path, '\\/');
}

/**
 * @return \Racine\Http\Request
 */
function request(){
    return \Racine\Application::getInstance()->getRequest();
}

/**
 * @return \Racine\Logger
 */
function logger(){
    return \Racine\Application::getInstance()->getLogger();
}

/**
 * @return null|\Symfony\Component\HttpFoundation\Session\SessionInterface
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
    return token()->getUser();
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