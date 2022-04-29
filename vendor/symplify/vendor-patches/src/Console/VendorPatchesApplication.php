<?php

declare (strict_types=1);
namespace ECSPrefix20220429\Symplify\VendorPatches\Console;

use ECSPrefix20220429\Symfony\Component\Console\Application;
use ECSPrefix20220429\Symfony\Component\Console\Command\Command;
final class VendorPatchesApplication extends \ECSPrefix20220429\Symfony\Component\Console\Application
{
    /**
     * @param Command[] $commands
     */
    public function __construct(array $commands)
    {
        $this->addCommands($commands);
        parent::__construct();
    }
}
