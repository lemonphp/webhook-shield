<?php

namespace Lemon\WebhookShield\Services;

use Psr\Http\Message\ServerRequestInterface;
use Lemon\WebhookShield\ServiceInterface;

/**
 * Class Bitbucket
 *
 * @package     Lemon\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Bitbucket implements ServiceInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Bitbucket service constructor.
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
