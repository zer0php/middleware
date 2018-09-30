<?php

namespace Zero\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ArgumentParserMiddleware
 * @author Mohos Tamás <tomi@mohos.name>
 * @package Zero\Middleware
 */
class ArgumentParserMiddleware implements MiddlewareInterface
{

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $args = $this->getArguments($request, 1);
        if ($args) {
            list($paths, $params) = $this->parseArgs($args);
            $path = '/' . implode('/', $paths);
            $request = $request
                ->withUri($request->getUri()->withPath($path))
                ->withQueryParams($params);
        }
        return $handler->handle($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @param int $offset
     * @return array
     */
    private function getArguments(ServerRequestInterface $request, int $offset)
    {
        $serverParams = $request->getServerParams();
        return isset($serverParams['argv']) ? array_slice($serverParams['argv'], $offset) : [];
    }

    /**
     * @param array $args
     * @return array [paths, arguments]
     */
    private function parseArgs(array $args): array
    {
        $paths = [];
        $params = [];
        foreach($args as $arg) {
            if(strpos($arg, '-') === 0) {
                list($key, $value) = $this->parseParams($arg);
                $params[$key] = $value;
            } else if(end($params) === '') {
                $params[key($params)] = $arg;
            } else {
                $paths[] = $arg;
            }
        }
        return [$paths, $params];
    }

    /**
     * @param string $arg
     * @return array [key, value]
     */
    private function parseParams(string $arg): array
    {
        $key = preg_replace('/^[\-]+/', '', $arg);
        if (strpos($key, '=') !== false) {
            list($key, $argValue) = explode('=', $key, 2);
            $value = str_replace('"', '', $argValue);
        } else {
            $value = '';
        }
        return [$key, $value];
    }
}