<?php

namespace Lemon\WebhookShield\Tests\Unit\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Lemon\WebhookShield\ServiceProfiles\Trello;
use Lemon\WebhookShield\Tests\NonPublicAccessibleTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class TrelloTest
 *
 * @package     Lemon\WebhookShield\Tests\Unit\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class TrelloTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /**
     * Test constuctor
     */
    public function testConstructor()
    {
        $profile = $this->createTestProfile();

        $this->assertInstanceOf(ServiceProfileInterface::class, $profile);
        $this->assertEquals('foo', $this->getNonPublicProperty($profile, 'secret'));
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
     * @param  string $secret
     * @return \Psr\Http\Message\ServerRequestInterface
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
     * Create test service profile
     *
     * @return \Lemon\WebhookShield\ServiceProfileInterface
     */
    protected function createTestProfile(): ServiceProfileInterface
    {
        return new Trello('foo');
    }

    /**
     * Get expected list header keys of service profile
     *
     * @return array
     */
    protected function expectedHeaderKeys(): array
    {
        return ['X-Trello-Webhook'];
    }
}
