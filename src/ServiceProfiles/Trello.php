<?php

namespace Lemon\WebhookShield\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Trello service profile
 *
 * @package     Lemon\WebhookShield\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Trello implements ServiceProfileInterface
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * Trello service profile constructor.
     *
     * @param string $secret
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
        return ['X-Trello-Webhook'];
    }

    /**
     * Verify request
     *
     * @param  ServerRequestInterface $request
     * @return bool
     */
    public function verify(ServerRequestInterface $request): bool
    {
        $content = trim($request->getBody()->getContents()) . strval($request->getUri());
        $generated = base64_encode(hash_hmac('sha1', $content, $this->secret, true));

        return hash_equals($generated, $request->getHeaderLine('X-Trello-Webhook'));
    }
}
