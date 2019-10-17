<?php

namespace Lemon\WebhookShield;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class WebhookShield Middleware
 *
 * @package     Lemon\WebhookShield
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class WebhookShieldMiddleware implements MiddlewareInterface
{
    /**
     * @var ServiceProfileInterface
     */
    protected $profile;

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * Constructor
     *
     * @param  ServiceProfileInterface         $profile
     * @param  ResponseFactoryInterface $responseFactory
     * @return void
     */
    public function __construct(ServiceProfileInterface $profile, ResponseFactoryInterface $responseFactory)
    {
        $this->profile = $profile;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     *
     * @param  ServerRequestInterface  $request
     * @param  RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->passes($request)) {
            return $handler->handle($request);
        }

        return $this->responseFactory->createResponse(400, 'Bad request');
    }

    /**
     * @param  ServerRequestInterface $request
     * @return bool
     */
    protected function passes(ServerRequestInterface $request): bool
    {
        if (!$this->profile->isAllowedMethod($request->getMethod())) {
            return false;
        }

        foreach ($this->profile->requiredHeaderKeys() as $headerKey) {
            if (!$request->hasHeader($headerKey)) {
                return false;
            }
        }

        return $this->profile->verify($request);
    }
}
