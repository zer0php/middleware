<?php

namespace Zero\Middleware\Pipe;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;
use Zero\Middleware\Exception\MissingResponseException;

/**
 * Class MiddlewarePipe
 * @author Mohos Tamás <tomi@mohos.name>
 * @package Zero\Middleware\Pipe
 */
class MiddlewarePipe implements RequestHandlerInterface
{
    /**
     * @var SplQueue
     */
    private $queue;

    public function __construct()
    {
        $this->queue = new SplQueue();
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function push(MiddlewareInterface $middleware)
    {
        $this->queue->push($middleware);
    }

    /**
     * Handle the request and return a response.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->queue->isEmpty()) {
            return $this->shiftMiddleware()->process($request, $this);
        }
        throw new MissingResponseException('Last middleware executed did not return a response!');
    }

    /**
     * @return MiddlewareInterface
     */
    private function shiftMiddleware(): MiddlewareInterface
    {
        return $this->queue->shift();
    }
}