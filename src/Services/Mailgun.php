<?php

namespace Lemon\WebhookShield\Services;

use Lemon\WebhookShield\ServiceInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Mailgun
 *
 * @package     Lemon\WebhookShield\Services
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Mailgun implements ServiceInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $tolerance;

    /**
     * Mailgun constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->token = $config['token'] ?? '';
        $this->tolerance = $config['tolerance'] ?? 300; // default tolerance is 5 minutes
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
        return [];
    }

    /**
     * Verify request
     *
     * @param  ServerRequestInterface $request
     * @return bool
     */
    public function verify(ServerRequestInterface $request): bool
    {
        $params = $request->getParsedBody();

        if (!isset($params['timestamp']) || !isset($params['token']) || !isset($params['signature'])) {
            return false;
        }

        if (abs(time() - $params['timestamp']) > $this->tolerance) {
            return false;
        }

        $generated = hash_hmac('sha256', $params['timestamp'] . $params['token'], $this->token);

        return hash_equals($generated, $params['signature']);
    }
}
