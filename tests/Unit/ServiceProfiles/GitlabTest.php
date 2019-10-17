<?php

namespace Lemon\WebhookShield\Tests\Unit\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Lemon\WebhookShield\ServiceProfiles\Gitlab;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class GitlabTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class GitlabTest extends TestCase
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
            [$this->createTestRequest('bar'), false],
            [$this->createTestRequest('foo'), true],
        ];
    }

    /**
     * @param  string $token
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    protected function createTestRequest(string $token)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())->method('getHeaderLine')->with('X-Gitlab-Token')->willReturn($token);

        return $request;
    }

    /**
     * Create test service profile
     *
     * @return \Lemon\WebhookShield\ServiceProfileInterface
     */
    protected function createTestProfile(): ServiceProfileInterface
    {
        return new Gitlab('foo');
    }

    /**
     * Get expected list header keys of service profile
     *
     * @return array
     */
    protected function expectedHeaderKeys(): array
    {
        return ['X-Gitlab-Token'];
    }
}
