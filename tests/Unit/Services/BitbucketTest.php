<?php

namespace Lemon\WebhookShield\Tests\Unit\Services;

use Lemon\WebhookShield\ServiceInterface;
use Lemon\WebhookShield\Services\Bitbucket;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class BitbucketTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class BitbucketTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Bitbucket
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new Bitbucket([
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
        $signature = hash_hmac('sha256', $content, $token);

        $body = $this->createMock(StreamInterface::class);
        $body->expects($this->any())->method('getContents')->willReturn($content);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())->method('getBody')->willReturn($body);
        $request->expects($this->once())->method('getHeaderLine')->with('X-Hub-Signature')->willReturn($signature);

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
        $this->assertEquals(['X-Hub-Signature'], $this->service->headerKeys());
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