<?php

namespace Lemon\WebhookShield\ServiceProfiles;

use Lemon\WebhookShield\ServiceProfileInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Mailgun service profile
 *
 * @package     Lemon\WebhookShield\ServiceProfiles
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
class Mailgun implements ServiceProfileInterface
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var int
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
