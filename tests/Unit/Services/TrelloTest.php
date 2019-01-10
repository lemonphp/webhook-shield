<?php

namespace Lemon\WebhookShield\Tests\Unit\Services;

use Lemon\WebhookShield\ServiceInterface;
use Lemon\WebhookShield\Services\Trello;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class TrelloTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class TrelloTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Trello
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new Trello([
            'secret' => 'foo',
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
     * @param  string $secret
     * @return ServerRequestInterface
     */
    protected function createTestRequest(string $content, string $secret)
    {
        $url = 'https://example.com';
        $signature = base64_encode(hash_hmac('sha1', trim($content) . $url, $secret, true));

        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())->method('__toString')->willReturn($url);

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->any())->method('getContents')->willReturn($content);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())->method('getBody')->willReturn($body);
        $request->expects($this->once())->method('getUri')->willReturn($uri);
        $request->expects($this->once())->method('getHeaderLine')->with('X-Trello-Webhook')->willReturn($signature);

        return $request;
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(ServiceInterface::class, $this->service);
        $this->assertEquals('foo', $this->getNonPublicProperty($this->service, 'secret'));
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
        $this->assertEquals(['X-Trello-Webhook'], $this->service->headerKeys());
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
