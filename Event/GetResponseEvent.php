<?php

namespace Racine\Event;

use Racine\Application;
use Symfony\Component\HttpFoundation\Response;


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
     * @var Application
     */
    private $application;
    
    /**
     * The response object.
     *
     * @var Response
     */
    private $response;
    
    /**
     * GetResponseEvent constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
        
        parent::__construct($application->getRequest());
    }
    
    
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
    
    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
    
}