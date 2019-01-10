<?php

namespace Lemon\WebhookShield\Services;

use Lemon\WebhookShield\ServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Trello
 *
 * @package     Lemon\WebhookShieldMiddleware\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Trello implements ServiceInterface
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * Facebook service constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->secret = $config['secret'] ?? '';
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
