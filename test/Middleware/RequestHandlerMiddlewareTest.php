<?php

namespace Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zero\Middleware\RequestHandlerMiddleware;

class RequestHandlerMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function process_InvokeRequestHandlerHandleMethod()
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock
            ->expects($this->once())
            ->method('handle')
            ->with($requestMock)
            ->willReturn($responseMock);
        $middleware = new RequestHandlerMiddleware($handlerMock);

        $middleware->process($requestMock, $handlerMock);
    }
}
