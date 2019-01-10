<?php

namespace Lemon\WebhookShield\Tests\Unit\Services;

use Lemon\WebhookShield\ServiceInterface;
use Lemon\WebhookShield\Services\Mailgun;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class MailgunTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class MailgunTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * @var Mailgun
     */
    protected $service;

    /**
     * Setting up before test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new Mailgun([
            'token' => 'foo',
            'tolerance' => 360,
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
     * @param  array  $data
     * @param  string $token
     * @return ServerRequestInterface
     */
    protected function createTestRequest(array $data, string $token)
    {
        $data['signature'] = hash_hmac('sha256', $data['timestamp'] . ($data['token'] ?? ''), $token);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())->method('getParsedBody')->willReturn($data);

        return $request;
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $this->assertInstanceOf(ServiceInterface::class, $this->service);
        $this->assertEquals('foo', $this->getNonPublicProperty($this->service, 'token'));
        $this->assertEquals(360, $this->getNonPublicProperty($this->service, 'tolerance'));
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
        $this->assertEquals([], $this->service->headerKeys());
    }

    /**
     * @param array $data
     * @param string $token
     * @param bool $excepted
     * @dataProvider dataTestVerify
     */
    public function testVerify(array $data, string $token, bool $excepted)
    {
        $request = $this->createTestRequest($data, $token);

        $this->assertSame($excepted, $this->service->verify($request));
    }

    /**
     * @return array
     */
    public function dataTestVerify()
    {
        return [
            [['token' => 'baz', 'timestamp' => time()], 'foo', true],
            [['bar' => 'baz', 'timestamp' => time()], 'foo', false],
            [['token' => 'baz', 'timestamp' => strtotime('10 minutes ago')], 'foo', false],
            [['token' => 'baz', 'timestamp' => time()], 'bar', false],
        ];
    }
}
