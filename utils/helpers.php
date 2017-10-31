<?php

/**
 * @param string $filename
 * @return bool|mixed
 */
function config($filename, $objectForMap = true, $ext = "php"){
    return \Racine\Config::get($filename, $objectForMap, $ext);
}

/**
 * @return string
 */
function base_path() {
    return config('app')->path;
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
 * @return null|\Racine\Http\Session
 */
function session(){
    return request()->getSession();
}

/**
 * @return \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface
 */
function flash(){
    return session()->getFlashBag();
}

/**
 * @return \Racine\Http\Security\Authentication|null
 */
function auth(){
    return \App\Http\Security\Authentication::getInstance();
}

/**
 * @return null|\App\Models\User
 */
function user(){
    return auth()->getUser();
}

/**
 * @return null|string|void
 */
function auth_token(){
    $user = user();
    if(!is_null($user)){
        return $user->getToken();
    }else{
        return null;
    }
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
    $pos = strpos($uri, config('app')->path);
    if($pos === 0){
        return substr($uri, strlen(config('app')->path)-1);
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