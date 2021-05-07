<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\HttpKernel\DependencyInjection;

use ECSPrefix20210507\Symfony\Contracts\Service\ResetInterface;
/**
 * Resets provided services.
 *
 * @author Alexander M. Turek <me@derrabus.de>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class ServicesResetter implements \ECSPrefix20210507\Symfony\Contracts\Service\ResetInterface
{
    private $resettableServices;
    private $resetMethods;
    /**
     * @param \Traversable $resettableServices
     */
    public function __construct($resettableServices, array $resetMethods)
    {
        $this->resettableServices = $resettableServices;
        $this->resetMethods = $resetMethods;
    }
    public function reset()
    {
        foreach ($this->resettableServices as $id => $service) {
            foreach ((array) $this->resetMethods[$id] as $resetMethod) {
                $service->{$resetMethod}();
            }
        }
    }
}
