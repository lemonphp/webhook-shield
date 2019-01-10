<?php

namespace Lemon\WebhookShield\Services;

use Lemon\WebhookShield\ServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Gitlab
 *
 * @package     Lemon\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Gitlab implements ServiceInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * Gitlab service constructor.
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
