<?php

namespace Racine\Http;


class Response extends \Symfony\Component\HttpFoundation\Response
{
    public function __construct($content = '', $status = 200, array $headers = array())
    {
        $securityHeaders = [
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block'
        ];

        parent::__construct($content, $status, array_merge($securityHeaders, $headers));
    }
}