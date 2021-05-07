<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\VarDumper\Dumper;

use ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Data;
use ECSPrefix20210507\Symfony\Component\VarDumper\Dumper\ContextProvider\ContextProviderInterface;
/**
 * @author Kévin Thérage <therage.kevin@gmail.com>
 */
class ContextualizedDumper implements \ECSPrefix20210507\Symfony\Component\VarDumper\Dumper\DataDumperInterface
{
    private $wrappedDumper;
    private $contextProviders;
    /**
     * @param ContextProviderInterface[] $contextProviders
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Dumper\DataDumperInterface $wrappedDumper
     */
    public function __construct($wrappedDumper, array $contextProviders)
    {
        $this->wrappedDumper = $wrappedDumper;
        $this->contextProviders = $contextProviders;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Data $data
     */
    public function dump($data)
    {
        $context = [];
        foreach ($this->contextProviders as $contextProvider) {
            $context[\get_class($contextProvider)] = $contextProvider->getContext();
        }
        $this->wrappedDumper->dump($data->withContext($context));
    }
}
