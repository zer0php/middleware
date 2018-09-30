<?php

namespace Zero\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class PathMiddleware
 * @author Mohos Tamás <tomi@mohos.name>
 * @package Zero\Middleware
 */
class PathMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var MiddlewareInterface
     */
    private $middleware;

    /**
     * @param string $path
     * @param MiddlewareInterface $middleware
     */
    public function __construct(string $path, MiddlewareInterface $middleware)
    {
        $this->path = $path;
        $this->middleware = $middleware;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if($request->getUri()->getPath() === $this->path) {
            return $this->middleware->process($request, $handler);
        }
        return $handler->handle($request);
    }
}