<?php

namespace Racine\Event;
use Racine\Http\Request;
use Racine\Http\Response;

/**
 * Allows to filter a Response object.
 *
 * You can call getResponse() to retrieve the current response. With
 * setResponse() you can set a new response that will be returned to the
 * browser.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class FilterResponseEvent extends RacineEvent
{
    /**
     * The current response object.
     *
     * @var Response
     */
    private $response;
    
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request);
        
        $this->setResponse($response);
    }
    
    /**
     * Returns the current response object.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * Sets a new response object.
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}