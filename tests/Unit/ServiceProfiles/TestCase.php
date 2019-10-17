<?php

namespace Lemon\WebhookShield\Tests\Unit\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Class TestCase
 *
 * @package     Lemon\WebhookShield\Tests\Unit\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Create list method and expected result pairs for test
     *
     * @return array
     */
    abstract public function dataForTestAllowMethods();

    /**
     * Create list request and expected result pairs for test
     *
     * @return array
     */
    abstract public function dataForTestVerifyRequest();

    /**
     * Create test service profile
     *
     * @return \Lemon\WebhookShield\ServiceProfileInterface
     */
    abstract protected function createTestProfile(): ServiceProfileInterface;

    /**
     * Get expected list header keys of service profile
     *
     * @return array
     */
    abstract protected function expectedHeaderKeys(): array;

    /**
     * Test check request method
     *
     * @param  string $method
     * @param  bool   $expected
     * @dataProvider dataForTestAllowMethods
     */
    public function testAllowMethods(string $method, bool $expected)
    {
        $profile = $this->createTestProfile();

        $this->assertSame($expected, $profile->isAllowedMethod($method));
    }

    /**
     * Test check request headers
     */
    public function testRequiredHeaderKeys()
    {
        $profile = $this->createTestProfile();

        $this->assertEquals($this->expectedHeaderKeys(), $profile->requiredHeaderKeys());
    }

    /**
     * Test check request signature
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     * @param  bool                                     $expected
     * @dataProvider dataForTestVerifyRequest
     */
    public function testVerifyRequest($request, bool $expected)
    {
        $profile = $this->createTestProfile();

        $this->assertSame($expected, $profile->verify($request));
    }
}
