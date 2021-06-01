<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\VarDumper\Command\Descriptor;

use ConfigTransformer20210601\Symfony\Component\Console\Output\OutputInterface;
use ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\Data;
/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
interface DumpDescriptorInterface
{
    /**
     * @return void
     */
    public function describe(\ConfigTransformer20210601\Symfony\Component\Console\Output\OutputInterface $output, \ConfigTransformer20210601\Symfony\Component\VarDumper\Cloner\Data $data, array $context, int $clientId);
}
