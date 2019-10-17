<?php

namespace Lemon\WebhookShield;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface service profile
 *
 * @package     Lemon\WebhookShield
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
interface ServiceProfileInterface
{
    /**
     * Check the given method is allowed
     *
     * @param  string $method
     * @return bool
     */
    public function isAllowedMethod(string $method): bool;

    /**
     * List required request header fields for checking
     *
     * @return array
     */
    public function requiredHeaderKeys(): array;

    /**
     * Verify request
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function verify(ServerRequestInterface $request): bool;
}
