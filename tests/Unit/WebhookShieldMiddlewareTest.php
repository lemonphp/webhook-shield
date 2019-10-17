<?php

namespace Lemon\WebhookShield\Tests\Unit;

use Lemon\WebhookShield\ServiceProfileInterface;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use Lemon\WebhookShield\WebhookShieldMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class WebhookShieldMiddlewareTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class WebhookShieldMiddlewareTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $factory = $this->createMock(ResponseFactoryInterface::class);
        $profile = $this->createMock(ServiceProfileInterface::class);

        $middleware = new WebhookShieldMiddleware($profile, $factory);

        $this->assertSame($profile, $this->getNonPublicProperty($middleware, 'profile'));
        $this->assertSame($factory, $this->getNonPublicProperty($middleware, 'responseFactory'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testHandleVaildRequest()
    {
        $request = $this->createTestRequest('POST', ['foo', 'bar']);
        $response = $this->createMock(ResponseInterface::class);

        $factory = $this->createMock(ResponseFactoryInterface::class);
        $factory->expects($this->never())->method('createResponse');

        $handler = $this->createTestHandler($response);

        $profile = $this->createMock(ServiceProfileInterface::class);
        $profile->expects($this->once())->method('isAllowedMethod')->with('POST')->willReturn(true);
        $profile->expects($this->once())->method('requiredHeaderKeys')->willReturn(['foo']);
        $profile->expects($this->once())->method('verify')->with($request)->willReturn(true);

        $middleware = new WebhookShieldMiddleware($profile, $factory);

        $result = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testHandleDisallowedMethodRequest()
    {
        $request = $this->createTestRequest('GET', ['foo', 'bar']);
        $response = $this->createMock(ResponseInterface::class);

        $factory = $this->createMock(ResponseFactoryInterface::class);
        $factory->expects($this->once())->method('createResponse')->with(400, 'Bad request')->willReturn($response);

        $handler = $this->createTestHandler();

        $profile = $this->createMock(ServiceProfileInterface::class);
        $profile->expects($this->once())->method('isAllowedMethod')->with('GET')->willReturn(false);
        $profile->expects($this->never())->method('requiredHeaderKeys');
        $profile->expects($this->never())->method('verify');

        $middleware = new WebhookShieldMiddleware($profile, $factory);

        $result = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testHandleMissingHeadersRequest()
    {
        $request = $this->createTestRequest('POST', ['bar']);
        $response = $this->createMock(ResponseInterface::class);

        $factory = $this->createMock(ResponseFactoryInterface::class);
        $factory->expects($this->once())->method('createResponse')->with(400, 'Bad request')->willReturn($response);

        $handler = $this->createTestHandler();

        $profile = $this->createMock(ServiceProfileInterface::class);
        $profile->expects($this->once())->method('isAllowedMethod')->with('POST')->willReturn(true);
        $profile->expects($this->once())->method('requiredHeaderKeys')->willReturn(['foo']);
        $profile->expects($this->never())->method('verify');

        $middleware = new WebhookShieldMiddleware($profile, $factory);

        $result = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * @throws \ReflectionException
     */
    public function testHandleInvalidRequest()
    {
        $request = $this->createTestRequest('POST', ['foo', 'bar']);
        $response = $this->createMock(ResponseInterface::class);

        $factory = $this->createMock(ResponseFactoryInterface::class);
        $factory->expects($this->once())->method('createResponse')->with(400, 'Bad request')->willReturn($response);

        $handler = $this->createTestHandler();

        $profile = $this->createMock(ServiceProfileInterface::class);
        $profile->expects($this->once())->method('isAllowedMethod')->with('POST')->willReturn(true);
        $profile->expects($this->once())->method('requiredHeaderKeys')->willReturn(['foo']);
        $profile->expects($this->once())->method('verify')->with($request)->willReturn(false);

        $middleware = new WebhookShieldMiddleware($profile, $factory);

        $result = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * Create mock http request object
     *
     * @param  string $method
     * @param  array  $headerKeys
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createTestRequest(string $method, array $headerKeys = [])
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->expects($this->any())->method('getMethod')->willReturn($method);
        $request->expects($this->any())->method('hasHeader')->willReturnCallback(
            function ($key) use ($headerKeys) {
                return in_array($key, $headerKeys);
            }
        );

        return $request;
    }

    /**
     * Create mock http request handler object
     *
     * @param  \Psr\Http\Message\ResponseInterface|null $response
     * @return \Psr\Http\Server\RequestHandlerInterface
     */
    protected function createTestHandler($response = null)
    {
        $handler = $this->createMock(RequestHandlerInterface::class);

        if (is_null($response)) {
            $handler->expects($this->never())->method('handle');
        } else {
            $handler->expects($this->any())->method('handle')->willReturn($response);
        }

        return $handler;
    }
}
