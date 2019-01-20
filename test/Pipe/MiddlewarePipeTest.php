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
     * @var MiddlewarePipe
     */
    private $pipe;

    protected function setUp()
    {
        $this->pipe = new MiddlewarePipe();
    }

    /**
     * @test
     */
    public function handle_WithoutGivenMiddlewares_ThrowsException()
    {
        $this->expectException(MissingResponseException::class);

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $this->pipe->handle($requestMock);
    }

    /**
     * @test
     */
    public function handle_GivenMiddlewares_InvokeMiddlewaresProcessMethod()
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);

        $middlewareMock = $this->createMock(MiddlewareInterface::class);
        $middlewareMock
            ->expects($this->once())
            ->method('process')
            ->with($requestMock, $this->pipe)
            ->willReturnCallback(function (ServerRequestInterface $requestMock, RequestHandlerInterface $handler) {
                return $handler->handle($requestMock);
            });

        $secondMiddlewareMock = $this->createMock(MiddlewareInterface::class);
        $secondMiddlewareMock
            ->expects($this->once())
            ->method('process')
            ->with($requestMock, $this->pipe)
            ->willReturn($responseMock);

        $this->pipe->push($middlewareMock);
        $this->pipe->push($secondMiddlewareMock);
        $response = $this->pipe->handle($requestMock);
        $this->assertSame($response, $responseMock);
    }

}
