<?php

declare (strict_types=1);
namespace ECSPrefix20210517\Symplify\SetConfigResolver\Bootstrap;

use ECSPrefix20210517\Nette\Utils\ObjectHelpers;
use ECSPrefix20210517\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20210517\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix20210517\Symplify\SetConfigResolver\Exception\SetNotFoundException;
/**
 * @see \Symplify\SetConfigResolver\Tests\Bootstrap\InvalidSetReporterTest
 */
final class InvalidSetReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct()
    {
        $symfonyStyleFactory = new \ECSPrefix20210517\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory();
        $this->symfonyStyle = $symfonyStyleFactory->create();
    }
    /**
     * @return void
     */
    public function report(\ECSPrefix20210517\Symplify\SetConfigResolver\Exception\SetNotFoundException $setNotFoundException)
    {
        $message = $setNotFoundException->getMessage();
        $suggestedSet = \ECSPrefix20210517\Nette\Utils\ObjectHelpers::getSuggestion($setNotFoundException->getAvailableSetNames(), $setNotFoundException->getSetName());
        if ($suggestedSet !== null) {
            $message .= \sprintf('. Did you mean "%s"?', $suggestedSet);
            $this->symfonyStyle->error($message);
        } elseif ($setNotFoundException->getAvailableSetNames() !== []) {
            $this->symfonyStyle->error($message);
            $this->symfonyStyle->note('Pick one of:');
            $this->symfonyStyle->listing($setNotFoundException->getAvailableSetNames());
        }
    }
}
