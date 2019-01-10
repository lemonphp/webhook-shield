<?php

namespace Lemon\WebhookShield\Services;

use Lemon\WebhookShield\ServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Shopify
 *
 * @package     Lemon\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Shopify implements ServiceInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Shopify service constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->token = $config['token'] ?? '';
    }

    /**
     * List all allowed HTTP methods
     *
     * @return string[]
     */
    public function allowMethods(): array
    {
        return ['POST'];
    }

    /**
     * List request header fields for checking
     *
     * @return array
     */
    public function headerKeys(): array
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
