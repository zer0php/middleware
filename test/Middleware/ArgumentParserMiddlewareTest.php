<?php

namespace Test\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zero\Middleware\ArgumentParserMiddleware;

class ArgumentParserMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function process_WithoutArguments_ReturnRequestWithoutPathAndQueryParams()
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock
            ->expects($this->never())
            ->method('withUri');
        $requestMock
            ->expects($this->never())
            ->method('withQueryParams');
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock
            ->expects($this->once())
            ->method('handle')
            ->willReturn($this->createMock(ResponseInterface::class));
        $middleware = new ArgumentParserMiddleware();
        $middleware->process($requestMock, $handlerMock);
    }

    /**
     * @test
     */
    public function process_GivenArguments_ReturnRequestWithPathAndQueryParams()
    {
        $path = null;
        $uriMock = $this->createMock(UriInterface::class);
        $uriMock
            ->expects($this->once())
            ->method('withPath')
            ->willReturnCallback(function($path) use($uriMock) {
                $this->assertEquals('/test/test2', $path);
                return $uriMock;
            });

        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uriMock);
        $requestMock
            ->expects($this->once())
            ->method('withUri')
            ->willReturn($requestMock);
        $requestMock
            ->expects($this->once())
            ->method('withQueryParams')
            ->with([
                'v' => '',
                'file' => 'test-file.php',
                'dir' => 'test dir/',
                'output-dir' => 'output_dir'
            ])
            ->willReturn($requestMock);
        $requestMock
            ->expects($this->once())
            ->method('getServerParams')
            ->willReturn([
                'argv' => [
                    '',
                    'test',
                    'test2',
                    '-v',
                    '--file',
                    'test-file.php',
                    '--dir',
                    'test dir/',
                    '--output-dir="output_dir"',
                ]
            ]);
        $handlerMock = $this->createMock(RequestHandlerInterface::class);
        $handlerMock
            ->expects($this->once())
            ->method('handle')
            ->willReturn($this->createMock(ResponseInterface::class));
        $middleware = new ArgumentParserMiddleware();
        $middleware->process($requestMock, $handlerMock);
    }
}
