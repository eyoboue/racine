<?php

namespace Racine\Event;

use Racine\Http\Response;

/**
 * Allows to create a response for a request.
 *
 * Call setResponse() to set the response that will be returned for the
 * current request. The propagation of this event is stopped as soon as a
 * response is set.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class GetResponseEvent extends RacineEvent
{
    /**
     * The response object.
     *
     * @var Response
     */
    private $response;
    
    /**
     * Returns the response object.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * Sets a response and stops event propagation.
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        
        $this->stopPropagation();
    }
    
    /**
     * Returns whether a response was set.
     *
     * @return bool Whether a response was set
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
}