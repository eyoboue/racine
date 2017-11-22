<?php

namespace Racine\Http;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    protected function preparePathInfo()
    {
        $uri = $this->getRequestUri();
        $pos = strpos($uri, config('app')['path']);
        if($pos === 0){
            return substr($uri, strlen(config('app')['path'])-1);
        }else{
            return $uri;
        }
    }
    
}