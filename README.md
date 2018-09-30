# ZeroPHP Middleware

Usage:
```php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zero\Middleware\CallableMiddleware;
use Zero\Middleware\LazyLoadingMiddleware;
use Zero\Middleware\PathMiddleware;
use Zero\Middleware\Pipe\MiddlewarePipe;

$testMiddleware = new CallableMiddleware(function(ServerRequestInterface $request, RequestHandlerInterface $handler) {
    return $handler->handle($request->withAttribute('name', 'Test'));
});
/* @var $container \Psr\Container\ContainerInterface */
$container = new Container([
    'testMiddleware' => $testMiddleware
]);
$lazyMiddleware = new LazyLoadingMiddleware($container, 'testMiddleware');

$pathCallableMiddleware = new CallableMiddleware(function(ServerRequestInterface $request, RequestHandlerInterface $handler) {
    /* @var $response \Psr\Http\Message\ResponseInterface */
    $response = new Response('Hello ' . $request->getAttribute('name'));
    return $response;
});
$pathMiddleware = new PathMiddleware('/test', $pathCallableMiddleware);

$pipe = new MiddlewarePipe();
$pipe->push($lazyMiddleware);
$pipe->push($pathMiddleware);

/* @var $serverRequest ServerRequestInterface */
$serverRequest = new ServerRequest('GET', '/test');
echo $pipe->handle($serverRequest)->getBody(); //Hello Test
```

Cli Usage:
```php
//script.php test test2 -a --arg "simple value" --arg2="simple value2"

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zero\Middleware\ArgumentParserMiddleware;
use Zero\Middleware\CallableMiddleware;
use Zero\Middleware\PathMiddleware;
use Zero\Middleware\Pipe\MiddlewarePipe;

$pathCallableMiddleware = new CallableMiddleware(function(ServerRequestInterface $request, RequestHandlerInterface $handler) {
    $params = $request->getQueryParams(); //['a' => '', 'arg' => 'simple value', 'arg2' => 'simple value2']
    return new Response($params['arg']);
});
$pathMiddleware = new PathMiddleware('/test/test2', $pathCallableMiddleware);

$pipe = new MiddlewarePipe();
$pipe->push(new ArgumentParserMiddleware());
$pipe->push($pathMiddleware);

/* @var $serverRequest ServerRequestInterface */
$serverRequest = ServerRequest::fromGlobals();
echo $pipe->handle($serverRequest)->getBody(); //simple value
```