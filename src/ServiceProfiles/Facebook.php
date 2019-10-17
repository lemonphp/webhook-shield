<?php

namespace Lemon\WebhookShield\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Facebook service profile
 *
 * @package     Lemon\WebhookShield\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Facebook implements ServiceProfileInterface
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * Facebook service profile constructor.
     *
     * @param  string $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
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
        $generated = 'sha1=' . hash_hmac('sha1', $request->getBody()->getContents(), $this->secret);

        return hash_equals($generated, $request->getHeaderLine('X-Hub-Signature'));
    }
}
