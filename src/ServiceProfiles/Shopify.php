<?php

namespace Lemon\WebhookShield\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Shopify service profile
 *
 * @package     Lemon\WebhookShield\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Shopify implements ServiceProfileInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Shopify service profile constructor.
     *
     * @param  string $token
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
        return ['X-Shopify-Hmac-SHA256'];
    }

    /**
     * Verify request
     *
     * @param  ServerRequestInterface $request
     * @return bool
     */
    public function verify(ServerRequestInterface $request): bool
    {
        $generated = base64_encode(hash_hmac('sha256', $request->getBody()->getContents(), $this->token, true));

        return hash_equals($generated, $request->getHeaderLine('X-Shopify-Hmac-SHA256'));
    }
}
