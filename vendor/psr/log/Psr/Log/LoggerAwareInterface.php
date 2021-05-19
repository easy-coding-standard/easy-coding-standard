<?php

namespace ECSPrefix20210519\Psr\Log;

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
    public function setLogger(\ECSPrefix20210519\Psr\Log\LoggerInterface $logger);
}
