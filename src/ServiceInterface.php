<?php

namespace Lemon\WebhookShield;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ServiceInterface
 *
 * @package     Lemon\WebhookShield
 * @author      Oanh Nguyen <oanhnn.bk@gmail.com>
 * @license     The MIT License
 */
interface ServiceInterface
{
    /**
     * List all allowed HTTP methods
     *
     * @return string[]
     */
    public function allowMethods(): array;

    /**
     * List request header fields for checking
     *
     * @return array
     */
    public function headerKeys(): array;

    /**
     * Verify request
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function verify(ServerRequestInterface $request): bool;
}
