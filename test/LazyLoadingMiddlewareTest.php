<?php

namespace Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zero\Middleware\LazyLoadingMiddleware;

class LazyLoadingMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function process_GivenContainerAndMiddlewareName_InvokeNewlyCreatedMiddlewareInstanceProcess()
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);

        $middlewareMock = $this->createMock(MiddlewareInterface::class);
        $middlewareMock
            ->expects($this->once())
            ->method('process')
            ->with($requestMock, $handlerMock)
            ->willReturn($responseMock);

        $containerMock = $this->createMock(ContainerInterface::class);
        $containerMock
            ->expects($this->once())
            ->method('get')
            ->with('testMiddleware')
            ->willReturn($middlewareMock);

        $middleware = new LazyLoadingMiddleware($containerMock, 'testMiddleware');
        $response = $middleware->process($requestMock, $handlerMock);
        $this->assertSame($responseMock, $response);
    }
}
