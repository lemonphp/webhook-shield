<?php

namespace Lemon\WebhookShield\Tests\Unit;

use Lemon\WebhookShield\ServiceInterface;
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
        $service = $this->createMock(ServiceInterface::class);
        $factory = $this->createMock(ResponseFactoryInterface::class);

        $middleware = new WebhookShieldMiddleware($service, $factory);

        $this->assertSame($service, $this->getNonPublicProperty($middleware, 'service'));
        $this->assertSame($factory, $this->getNonPublicProperty($middleware, 'responseFactory'));
    }

    /**
     * @throws \ReflectionException
     */
    public function testPassesSuccess()
    {
        $service = $this->createMock(ServiceInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $factory = $this->createMock(ResponseFactoryInterface::class);

        $request->expects($this->once())->method('getMethod')->willReturn('POST');
        $request->expects($this->once())->method('hasHeader')->with('foo')->willReturn(true);

        $service->expects($this->once())->method('allowMethods')->willReturn(['POST']);
        $service->expects($this->once())->method('headerKeys')->willReturn(['foo']);
        $service->expects($this->once())->method('verify')->with($request)->willReturn(true);

        $middleware = new WebhookShieldMiddleware($service, $factory);

        $this->assertTrue($this->invokeNonPublicMethod($middleware, 'passes', $request));
    }

    /**
     * @throws \ReflectionException
     */
    public function testPassesWhenDisallowMethod()
    {
        $service = $this->createMock(ServiceInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $factory = $this->createMock(ResponseFactoryInterface::class);

        $request->expects($this->once())->method('getMethod')->willReturn('GET');
        $request->expects($this->never())->method('hasHeader');

        $service->expects($this->once())->method('allowMethods')->willReturn(['POST']);
        $service->expects($this->never())->method('headerKeys');
        $service->expects($this->never())->method('verify');

        $middleware = new WebhookShieldMiddleware($service, $factory);

        $this->assertFalse($this->invokeNonPublicMethod($middleware, 'passes', $request));
    }

    /**
     * @throws \ReflectionException
     */
    public function testPassesWhenMissingHeaders()
    {
        $service = $this->createMock(ServiceInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $factory = $this->createMock(ResponseFactoryInterface::class);

        $request->expects($this->once())->method('getMethod')->willReturn('POST');
        $request->expects($this->once())->method('hasHeader')->with('foo')->willReturn(false);

        $service->expects($this->once())->method('allowMethods')->willReturn(['POST']);
        $service->expects($this->once())->method('headerKeys')->willReturn(['foo']);
        $service->expects($this->never())->method('verify');

        $middleware = new WebhookShieldMiddleware($service, $factory);

        $this->assertFalse($this->invokeNonPublicMethod($middleware, 'passes', $request));
    }

    /**
     * @throws \ReflectionException
     */
    public function testPassesWhenFailingVerified()
    {
        $service = $this->createMock(ServiceInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $factory = $this->createMock(ResponseFactoryInterface::class);

        $request->expects($this->once())->method('getMethod')->willReturn('POST');
        $request->expects($this->once())->method('hasHeader')->with('foo')->willReturn(true);

        $service->expects($this->once())->method('allowMethods')->willReturn(['POST']);
        $service->expects($this->once())->method('headerKeys')->willReturn(['foo']);
        $service->expects($this->once())->method('verify')->with($request)->willReturn(false);

        $middleware = new WebhookShieldMiddleware($service, $factory);

        $this->assertFalse($this->invokeNonPublicMethod($middleware, 'passes', $request));
    }

    /**
     * Test handle a request, that did passed
     */
    public function testHandlePassedRequest()
    {
        $service = $this->createMock(ServiceInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $factory = $this->createMock(ResponseFactoryInterface::class);

        $request->expects($this->once())->method('getMethod')->willReturn('POST');
        $request->expects($this->once())->method('hasHeader')->with('foo')->willReturn(true);

        $service->expects($this->once())->method('allowMethods')->willReturn(['POST']);
        $service->expects($this->once())->method('headerKeys')->willReturn(['foo']);
        $service->expects($this->once())->method('verify')->with($request)->willReturn(true);

        $factory->expects($this->never())->method('createResponse');

        $response = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->with($request)->willReturn($response);

        $middleware = new WebhookShieldMiddleware($service, $factory);

        $result = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($response, $result);
    }

    /**
     * Test handle a request, that didn't passed
     */
    public function testHandleDontPassedRequest()
    {
        $service = $this->createMock(ServiceInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $factory = $this->createMock(ResponseFactoryInterface::class);

        $request->expects($this->once())->method('getMethod')->willReturn('POST');
        $request->expects($this->once())->method('hasHeader')->with('foo')->willReturn(true);

        $service->expects($this->once())->method('allowMethods')->willReturn(['POST']);
        $service->expects($this->once())->method('headerKeys')->willReturn(['foo']);
        $service->expects($this->once())->method('verify')->with($request)->willReturn(false);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $response = $this->createMock(ResponseInterface::class);
        $factory->expects($this->once())->method('createResponse')->with(400, 'Bad request')->willReturn($response);

        $middleware = new WebhookShieldMiddleware($service, $factory);

        $result = $middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame($response, $result);
    }
}
