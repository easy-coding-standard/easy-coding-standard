<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210510\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210510\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
trait DecorateTrait
{
    /**
     * Sets the service that this service is decorating.
     *
     * @param string|null $id The decorated service id, use null to remove decoration
     *
     * @return $this
     *
     * @throws InvalidArgumentException in case the decorated service id and the new decorated service id are equals
     * @param string $renamedId
     * @param int $priority
     * @param int $invalidBehavior
     */
    public final function decorate($id, $renamedId = null, $priority = 0, $invalidBehavior = \ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
    {
        $renamedId = (string) $renamedId;
        $priority = (int) $priority;
        $invalidBehavior = (int) $invalidBehavior;
        $this->definition->setDecoratedService($id, $renamedId, $priority, $invalidBehavior);
        return $this;
    }
}
