<?php

namespace Zero\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class LazyLoadingMiddleware
 * @author Mohos Tamás <tomi@mohos.name>
 * @package Zero\Middleware
 */
class LazyLoadingMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var string
     */
    private $middlewareName;

    /**
     * @param ContainerInterface $container
     * @param $middlewareName
     */
    public function __construct(ContainerInterface $container, string $middlewareName)
    {
        $this->container = $container;
        $this->middlewareName = $middlewareName;
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
        return $this->getMiddleware($this->middlewareName)->process($request, $handler);
    }

    /**
     * @param string $middlewareName
     * @return MiddlewareInterface
     */
    private function getMiddleware(string $middlewareName): MiddlewareInterface
    {
        return $this->container->get($middlewareName);
    }
}