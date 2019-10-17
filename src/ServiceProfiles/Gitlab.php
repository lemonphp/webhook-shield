<?php

namespace Lemon\WebhookShield\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Gitlab service profile
 *
 * @package     Lemon\WebhookShield\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Gitlab implements ServiceProfileInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Gitlab service profile constructor.
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
        return ['X-Gitlab-Token'];
    }

    /**
     * Verify request
     *
     * @param  ServerRequestInterface $request
     * @return bool
     */
    public function verify(ServerRequestInterface $request): bool
    {
        return hash_equals($this->token, $request->getHeaderLine('X-Gitlab-Token'));
    }
}
