<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DI;

use Nette\DI\CompilerExtension;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\CheckerFilter;
use Symplify\EasyCodingStandard\Configuration\Option\FixersOption;
use Symplify\EasyCodingStandard\Configuration\Option\SniffsOption;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;

final class CheckerExtension extends CompilerExtension
{
    /**
     * @var CheckerConfigurationNormalizer
     */
    private $configurationNormalizer;

    /**
     * @var CheckerTypeValidator
     */
    private $checkerTypeValidator;

    /**
     * @var CheckerFilter
     */
    private $checkerFilter;

    public function __construct()
    {
        $this->configurationNormalizer = new CheckerConfigurationNormalizer;
        $this->checkerTypeValidator = new CheckerTypeValidator;
        $this->checkerFilter = new CheckerFilter;
    }

    public function loadConfiguration(): void
    {
        $checkers = $this->configurationNormalizer->normalize($this->getConfig());
        $this->checkerTypeValidator->validate(array_keys($checkers));
        $this->setCheckersToGlobalParameters($checkers);
    }

    /**
     * @param mixed[][] $checkers
     */
    private function setCheckersToGlobalParameters(array $checkers): void
    {
        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->parameters[SniffsOption::NAME] = $this->checkerFilter->filterSniffs($checkers);
        $containerBuilder->parameters[FixersOption::NAME] = $this->checkerFilter->filterFixers($checkers);
    }
}
