<?php

namespace Racine\Http;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    protected function preparePathInfo()
    {
        $uri = $this->getRequestUri();
        $path = config('app')['path'];
        if(empty($path)) return $uri;
        $pos = strpos($uri, $path);
        if($pos === 0){
            return substr($uri, strlen(config('app')['path'])-1);
        }else{
            return $uri;
        }
    }
    
}