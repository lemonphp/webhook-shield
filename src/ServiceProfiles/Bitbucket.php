<?php

namespace Lemon\WebhookShield\ServiceProfiles;

use Psr\Http\Message\ServerRequestInterface;
use Lemon\WebhookShield\ServiceProfileInterface;

/**
 * Class Bitbucket service profile
 *
 * @package     Lemon\WebhookShield\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Bitbucket implements ServiceProfileInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Bitbucket service profile constructor.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Check the given method is allowed
     *
     * @param  string $method
     * @return bool
     */
    public function isAllowedMethod(string $method): bool
    {
        return strtoupper($method) === 'POST';
    }

    /**
     * List required request header fields for checking
     *
     * @return array
     */
    public function requiredHeaderKeys(): array
    {
        return ['X-Hub-Signature'];
    }

    /**
     * Verify request
     *
     * @param  ServerRequestInterface $request
     * @return bool
     */
    public function verify(ServerRequestInterface $request): bool
    {
        $generated = hash_hmac('sha256', $request->getBody()->getContents(), $this->token);

        return hash_equals($generated, $request->getHeaderLine('X-Hub-Signature'));
    }
}
