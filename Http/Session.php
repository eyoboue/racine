<?php


namespace Racine\Http;


use Racine\Application;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;;

use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class Session extends \Symfony\Component\HttpFoundation\Session\Session
{
    public function __construct(SessionStorageInterface $storage = null, AttributeBagInterface $attributes = null, FlashBagInterface $flashes = null, Application $application = null)
    {
        parent::__construct($storage, $attributes, $flashes);
        if($this->storage instanceof NativeSessionStorage){
            $this->storage->setOptions([
                'cookie_lifetime' => 60*60*12,
                'cookie_httponly' => true,
                'cookie_secure' => ($application instanceof Application && $application->getRequest()->isSecure()),
            ]);
        }
    
    }
}