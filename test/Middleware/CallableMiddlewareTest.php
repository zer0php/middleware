<?php

namespace Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function process_GivenCallback_InvokeCallback()
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $callable = function($request, $handler) use($requestMock, $handlerMock) {
            $this->assertSame($requestMock, $request);
            $this->assertSame($handlerMock, $handler);
            return $this->createMock(ResponseInterface::class);
        };

        $middleware = new \Zero\Middleware\CallableMiddleware($callable);
        $middleware->process($requestMock, $handlerMock);
    }
}
