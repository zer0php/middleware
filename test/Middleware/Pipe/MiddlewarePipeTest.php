<?php

namespace Test\Middleware\Pipe;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zero\Middleware\Exception\MissingResponseException;
use Zero\Middleware\Pipe\MiddlewarePipe;

class MiddlewarePipeTest extends TestCase
{

    /**
     * @test
     */
    public function handle_WithoutGivenMiddlewares_ThrowsException()
    {
        $this->expectException(MissingResponseException::class);
        $pipe = new MiddlewarePipe();
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $pipe->handle($requestMock);
    }

    /**
     * @test
     */
    public function handle_GivenMiddlewares_InvokeMiddlewaresProcessMethod()
    {
        $pipe = new MiddlewarePipe();
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $middlewareMock = $this->createMock(MiddlewareInterface::class);
        $middlewareMock
            ->expects($this->once())
            ->method('process')
            ->with($requestMock, $pipe)
            ->willReturnCallback(function(ServerRequestInterface $requestMock, RequestHandlerInterface $handler) {
                return $handler->handle($requestMock);
            });

        $secondMiddlewareMock = $this->createMock(MiddlewareInterface::class);
        $secondMiddlewareMock
            ->expects($this->once())
            ->method('process')
            ->with($requestMock, $pipe)
            ->willReturn($responseMock);

        $pipe->push($middlewareMock);
        $pipe->push($secondMiddlewareMock);
        $response = $pipe->handle($requestMock);
        $this->assertSame($response, $responseMock);
    }

}
