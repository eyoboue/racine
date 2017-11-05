<?php

namespace Racine\Security\Http;


use Racine\Http\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class FirewallMap implements FirewallMapInterface
{
    private $map = array();
    
    /**
     * @param RequestMatcherInterface $requestMatcher
     * @param array                   $listeners
     */
    public function add(RequestMatcherInterface $requestMatcher = null, array $listeners = array())
    {
        $this->map[] = array($requestMatcher, $listeners);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getListeners(Request $request)
    {
        foreach ($this->map as $elements) {
            if (null === $elements[0] || $elements[0]->matches($request)) {
                return array($elements[1]);
            }
        }
        
        return array(array());
    }
}