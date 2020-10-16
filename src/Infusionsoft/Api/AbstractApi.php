<?php

namespace Infusionsoft\Api;

use Infusionsoft\Infusionsoft;

/**
 * Class AbstractApi
 * @property \Infusionsoft\Infusionsoft client
 * @package Infusionsoft\Api
 */
abstract class AbstractApi
{
    /**
     * AbstractApi constructor.
     *
     * @param \Infusionsoft\Infusionsoft $client
     */
    public function __construct(Infusionsoft $client)
    {
        $this->client = $client;
    }
}
