<?php

namespace Lemon\WebhookShield\Tests\Unit\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Lemon\WebhookShield\ServiceProfiles\Bitbucket;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class BitbucketTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class BitbucketTest extends TestCase
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
            [$this->createTestRequest('test-content', 'bar'), false],
            [$this->createTestRequest('test-content', 'foo'), true],
        ];
    }

    /**
     * Create test request
     *
     * @param  string $content
     * @param  string $token
     * @return \Psr\Http\Message\ServerRequestInterface
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
     * Create test service profile
     *
     * @return \Lemon\WebhookShield\ServiceProfileInterface
     */
    protected function createTestProfile(): ServiceProfileInterface
    {
        return new Bitbucket('foo');
    }

    /**
     * Get expected list header keys of service profile
     *
     * @return array
     */
    protected function expectedHeaderKeys(): array
    {
        return ['X-Hub-Signature'];
    }
}
