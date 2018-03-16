<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console;

use Jean85\PrettyVersions;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class Application extends SymfonyApplication
{
    public function __construct()
    {
        $version = PrettyVersions::getVersion('symplify/easy-coding-standard');

        parent::__construct('EasyCodingStandard', $version->getPrettyVersion());
    }

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
            'config',
            null,
            InputOption::VALUE_REQUIRED,
            'Path to config file.',
            'easy-coding-standard.(yml|yaml)'
        ));

        $inputDefinition->addOption(new InputOption(
            'level',
            null,
            InputOption::VALUE_REQUIRED,
            'Finds config by shortcut name.'
        ));
    }
}
