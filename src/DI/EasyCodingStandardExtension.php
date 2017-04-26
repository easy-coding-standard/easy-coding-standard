<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DI;

use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\EasyCodingStandard\Application\Application;
use Symplify\EasyCodingStandard\Configuration\CheckerConfigurationNormalizer;
use Symplify\EasyCodingStandard\Configuration\Option\CheckersOption;
use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\Validator\CheckerTypeValidator;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class EasyCodingStandardExtension extends CompilerExtension
{
    /**
     * @var string[][]
     */
    private $checkers = [];

    /**
     * @var CheckerConfigurationNormalizer
     */
    private $configurationNormalizer;

    /**
     * @var CheckerTypeValidator
     */
    private $checkerTypeValidator;

    public function __construct()
    {
        $this->configurationNormalizer = new CheckerConfigurationNormalizer;
        $this->checkerTypeValidator = new CheckerTypeValidator;
    }

    public function loadConfiguration(): void
    {
        $checkers = $this->configurationNormalizer->normalize($this->getConfig());
        $this->checkerTypeValidator->validate(array_keys($checkers));
        $this->setCheckersToGlobalParameters($checkers);

        die;
        // todo: register them as services :)
        # 0. validate them :)

        # 1.
        // fixer this way
        // sniff that way
        // addDefinition()

        /// fixer
        // add method->configure

        // sniff
        // add addsetup ($... = $value)

        # drop fixer and sniff factory then ;)

        # 2.
        // collect them in beforeCompile in SniffRunnerExtension
        // collect them in beforeCompile in FixerRunnerExtension

        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    public function beforeCompile(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            Application::class,
            FileProcessorInterface::class,
            'addFileProcessor'
        );
    }

    /**
     * @param mixed[][] $checkers
     */
    private function setCheckersToGlobalParameters(array $checkers): void
    {
        $this->getContainerBuilder()->parameters[CheckersOption::NAME] = $checkers;
        // add fixers and sniffs already?
    }
}
