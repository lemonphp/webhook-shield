<?php

namespace Lemon\WebhookShield\Services;

use Lemon\WebhookShield\ServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Facebook
 *
 * @package     Lemon\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Facebook implements ServiceInterface
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
