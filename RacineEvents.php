<?php

namespace Racine;


final class RacineEvents
{
    /**
     * The APPLICATION event occurs at the application is initialized
     *
     * Logger, Request, Database, Templating, EventDispatcher are initialized
     *
     * @Event
     *
     * @var string
     */
    const APPLICATION = 'racine.application';
    
    
    /**
     * The REQUEST event occurs at the very beginning of request
     * dispatching.
     *
     * This event allows you to create a response for a request before any
     * other code in the framework is executed. The event listener method
     * receives a Symfony\Component\HttpKernel\Event\GetResponseEvent
     * instance.
     *
     * @Event
     *
     * @var string
     */
    const REQUEST = 'racine.request';
    
    /**
     * The EXCEPTION event occurs when an uncaught exception appears.
     *
     * This event allows you to create a response for a thrown exception or
     * to modify the thrown exception. The event listener method receives
     * a Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent
     * instance.
     *
     * @Event
     *
     * @var string
     */
    const EXCEPTION = 'racine.exception';
    
    /**
     * The VIEW event occurs when the return value of a controller
     * is not a Response instance.
     *
     * This event allows you to create a response for the return value of the
     * controller. The event listener method receives a
     * Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent
     * instance.
     *
     * @Event
     *
     * @var string
     */
    const VIEW = 'racine.view';
    
    /**
     * The CONTROLLER event occurs once a controller was found for
     * handling a request.
     *
     * This event allows you to change the controller that will handle the
     * request. The event listener method receives a
     * Symfony\Component\HttpKernel\Event\FilterControllerEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const CONTROLLER = 'racine.controller';
    
    /**
     * The RESPONSE event occurs once a response was created for
     * replying to a request.
     *
     * This event allows you to modify or replace the response that will be
     * replied. The event listener method receives a
     * Symfony\Component\HttpKernel\Event\FilterResponseEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const RESPONSE = 'racine.response';
    
    /**
     * The TERMINATE event occurs once a response was sent.
     *
     * This event allows you to run expensive post-response jobs.
     * The event listener method receives a
     * Symfony\Component\HttpKernel\Event\PostResponseEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const TERMINATE = 'racine.terminate';
    
    /**
     * The FINISH_REQUEST event occurs when a response was generated for a request.
     *
     * This event allows you to reset the global and environmental state of
     * the application, when it was changed during the request.
     * The event listener method receives a
     * Symfony\Component\HttpKernel\Event\FinishRequestEvent instance.
     *
     * @Event
     *
     * @var string
     */
    const FINISH_REQUEST = 'racine.finish_request';
}