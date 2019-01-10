<?php

namespace Lemon\WebhookShield\Tests\Unit\Services;

use Lemon\WebhookShield\ServiceInterface;
use Lemon\WebhookShield\Services\Shopify;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class ShopifyTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class ShopifyTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Shopify
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new Shopify([
            'token' => 'foo',
        ]);
    }

    /**
     * Tear down after test
     */
    protected function tearDown()
    {
        unset($this->service);

        parent::tearDown();
    }

    /**
     * @param  string $content
     * @param  string $token
     * @return ServerRequestInterface
     */
    protected function createTestRequest(string $content, string $token)
    {
        $signature = base64_encode(hash_hmac('sha256', $content, $token, true));

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->any())->method('getContents')->willReturn($content);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())->method('getBody')->willReturn($body);
        $request->expects($this->once())
            ->method('getHeaderLine')
            ->with('X-Shopify-Hmac-SHA256')
            ->willReturn($signature);

        return $request;
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(ServiceInterface::class, $this->service);
        $this->assertEquals('foo', $this->getNonPublicProperty($this->service, 'token'));
    }

    /**
     *
     */
    public function testAllowMethods()
    {
        $this->assertEquals(['POST'], $this->service->allowMethods());
    }

    /**
     *
     */
    public function testHeadersFields()
    {
        $this->assertEquals(['X-Shopify-Hmac-SHA256'], $this->service->headerKeys());
    }

    /**
     *
     */
    public function testVerifyValidRequest()
    {
        $request = $this->createTestRequest('test-content', 'foo');

        $this->assertTrue($this->service->verify($request));
    }

    /**
     *
     */
    public function testVerifyInvalidRequest()
    {
        $request = $this->createTestRequest('test-content', 'bar');

        $this->assertFalse($this->service->verify($request));
    }
}
