<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class Application extends SymfonyApplication
{
    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $this->removeUnusedOptions($inputDefinition);
        $this->addExtraOptions($inputDefinition);
        return $inputDefinition;
    }

    private function removeUnusedOptions(InputDefinition $inputDefinition): void
    {
        $options = $inputDefinition->getOptions();
        unset($options['quiet'], $options['version'], $options['no-interaction']);
        $inputDefinition->setOptions($options);
    }

    private function addExtraOptions(InputDefinition $inputDefinition): void
    {
        $inputDefinition->addOption(new InputOption(
            '--config',
            null,
            InputOption::VALUE_REQUIRED,
            'Path to config file.',
            getcwd() . '/easy-coding-standard.neon'
        ));
    }
}
