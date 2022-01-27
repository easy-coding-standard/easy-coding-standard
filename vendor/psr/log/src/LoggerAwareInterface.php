<?php

namespace ECSPrefix20220127\Psr\Log;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(\ECSPrefix20220127\Psr\Log\LoggerInterface $logger) : void;
}
