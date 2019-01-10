<?php

namespace Lemon\WebhookShield\Tests\Unit\Services;

use Lemon\WebhookShield\ServiceInterface;
use Lemon\WebhookShield\Services\Gitlab;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class GitlabTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class GitlabTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Gitlab
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new Gitlab([
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
     * @param  string $token
     * @return ServerRequestInterface
     */
    protected function createTestRequest(string $token)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())->method('getHeaderLine')->with('X-Gitlab-Token')->willReturn($token);

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
        $this->assertEquals(['X-Gitlab-Token'], $this->service->headerKeys());
    }

    /**
     *
     */
    public function testVerifyValidRequest()
    {
        $request = $this->createTestRequest('foo');

        $this->assertTrue($this->service->verify($request));
    }

    /**
     *
     */
    public function testVerifyInvalidRequest()
    {
        $request = $this->createTestRequest('bar');

        $this->assertFalse($this->service->verify($request));
    }
}
