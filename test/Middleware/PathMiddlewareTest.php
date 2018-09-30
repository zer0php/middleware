<?php

namespace Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zero\Middleware\PathMiddleware;

class PathMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function process_GivenPathAndMiddlewareWithoutGivenRequestPath_DelegateHandler()
    {
        $middlewareMock = $this->createMock(MiddlewareInterface::class);

        $requestMock = $this->getRequestMock(null);

        $responseMock = $this->createMock(ResponseInterface::class);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock
            ->expects($this->once())
            ->method('handle')
            ->with($requestMock)
            ->willReturn($responseMock);

        $middleware = new PathMiddleware('/', $middlewareMock);
        $middleware->process($requestMock, $handlerMock);
    }

    /**
     * @test
     */
    public function process_GivenTestPathAndMiddlewareWithRequestPath_DelegateMiddleware()
    {
        $requestMock = $this->getRequestMock('/test');
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $middlewareMock = $this->createMock(MiddlewareInterface::class);
        $middlewareMock
            ->expects($this->once())
            ->method('process')
            ->with($requestMock, $handlerMock)
            ->willReturn($responseMock);

        $middleware = new PathMiddleware('/test', $middlewareMock);
        $middleware->process($requestMock, $handlerMock);
    }

    private function getRequestMock($path)
    {
        $uriMock = $this->createMock(UriInterface::class);
        $uriMock
            ->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uriMock);

        return $requestMock;
    }
}
