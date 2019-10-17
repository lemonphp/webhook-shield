<?php

namespace Lemon\WebhookShield\Tests\Unit\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Lemon\WebhookShield\ServiceProfiles\Mailgun;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class MailgunTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class MailgunTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * Test constuctor
     */
    public function testConstructor()
    {
        $profile = $this->createTestProfile();

        $this->assertInstanceOf(ServiceProfileInterface::class, $profile);
        $this->assertEquals('foo', $this->getNonPublicProperty($profile, 'token'));
        $this->assertEquals(360, $this->getNonPublicProperty($profile, 'tolerance'));
    }

    /**
     * Create list method and expected result pairs for test
     *
     * @return array
     */
    public function dataForTestAllowMethods()
    {
        return [
            ['POST', true],
            ['GET', false],
            ['PUT', false],
            ['PATCH', false],
            ['HEAD', false],
            ['DELETE', false],
        ];
    }

    /**
     * Create list request and expected result pairs for test
     *
     * @return array
     */
    public function dataForTestVerifyRequest()
    {
        return [
            [$this->createTestRequest(['token' => 'baz', 'timestamp' => time()], 'foo'), true],
            [$this->createTestRequest(['tok3n' => 'baz', 'timestamp' => time()], 'foo'), false],
            [$this->createTestRequest(['token' => 'baz', 'timestamp' => strtotime('10 minutes ago')], 'foo'), false],
            [$this->createTestRequest(['token' => 'baz', 'timestamp' => time()], 'bar'), false],
            [$this->createTestRequest(['token' => 'baz', 'tim3stamp' => time()], 'foo'), false],
        ];
    }

    /**
     * Create test request
     *
     * @param  array  $data
     * @param  string $token
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createTestRequest(array $data, string $token)
    {
        $data['signature'] = hash_hmac('sha256', ($data['timestamp'] ?? '') . ($data['token'] ?? ''), $token);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())->method('getParsedBody')->willReturn($data);

        return $request;
    }

    /**
     * Create test service profile
     *
     * @return \Lemon\WebhookShield\ServiceProfileInterface
     */
    protected function createTestProfile(): ServiceProfileInterface
    {
        return new Mailgun([
            'token' => 'foo',
            'tolerance' => 360,
        ]);
    }

    /**
     * Get expected list header keys of service profile
     *
     * @return array
     */
    protected function expectedHeaderKeys(): array
    {
        return [];
    }
}
