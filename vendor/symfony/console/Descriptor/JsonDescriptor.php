<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Console\Descriptor;

use ECSPrefix20210507\Symfony\Component\Console\Application;
use ECSPrefix20210507\Symfony\Component\Console\Command\Command;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputArgument;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputDefinition;
use ECSPrefix20210507\Symfony\Component\Console\Input\InputOption;
/**
 * JSON descriptor.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 *
 * @internal
 */
class JsonDescriptor extends \ECSPrefix20210507\Symfony\Component\Console\Descriptor\Descriptor
{
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputArgument $argument
     */
    protected function describeInputArgument($argument, array $options = [])
    {
        $this->writeData($this->getInputArgumentData($argument), $options);
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption $option
     */
    protected function describeInputOption($option, array $options = [])
    {
        $this->writeData($this->getInputOptionData($option), $options);
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputDefinition $definition
     */
    protected function describeInputDefinition($definition, array $options = [])
    {
        $this->writeData($this->getInputDefinitionData($definition), $options);
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Command\Command $command
     */
    protected function describeCommand($command, array $options = [])
    {
        $this->writeData($this->getCommandData($command), $options);
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Application $application
     */
    protected function describeApplication($application, array $options = [])
    {
        $describedNamespace = isset($options['namespace']) ? $options['namespace'] : null;
        $description = new \ECSPrefix20210507\Symfony\Component\Console\Descriptor\ApplicationDescription($application, $describedNamespace, \true);
        $commands = [];
        foreach ($description->getCommands() as $command) {
            $commands[] = $this->getCommandData($command);
        }
        $data = [];
        if ('UNKNOWN' !== $application->getName()) {
            $data['application']['name'] = $application->getName();
            if ('UNKNOWN' !== $application->getVersion()) {
                $data['application']['version'] = $application->getVersion();
            }
        }
        $data['commands'] = $commands;
        if ($describedNamespace) {
            $data['namespace'] = $describedNamespace;
        } else {
            $data['namespaces'] = \array_values($description->getNamespaces());
        }
        $this->writeData($data, $options);
    }
    /**
     * Writes data as json.
     */
    private function writeData(array $data, array $options)
    {
        $flags = isset($options['json_encoding']) ? $options['json_encoding'] : 0;
        $this->write(\json_encode($data, $flags));
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputArgument $argument
     * @return mixed[]
     */
    private function getInputArgumentData($argument)
    {
        return ['name' => $argument->getName(), 'is_required' => $argument->isRequired(), 'is_array' => $argument->isArray(), 'description' => \preg_replace('/\\s*[\\r\\n]\\s*/', ' ', $argument->getDescription()), 'default' => \INF === $argument->getDefault() ? 'INF' : $argument->getDefault()];
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption $option
     * @return mixed[]
     */
    private function getInputOptionData($option)
    {
        return ['name' => '--' . $option->getName(), 'shortcut' => $option->getShortcut() ? '-' . \str_replace('|', '|-', $option->getShortcut()) : '', 'accept_value' => $option->acceptValue(), 'is_value_required' => $option->isValueRequired(), 'is_multiple' => $option->isArray(), 'description' => \preg_replace('/\\s*[\\r\\n]\\s*/', ' ', $option->getDescription()), 'default' => \INF === $option->getDefault() ? 'INF' : $option->getDefault()];
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputDefinition $definition
     * @return mixed[]
     */
    private function getInputDefinitionData($definition)
    {
        $inputArguments = [];
        foreach ($definition->getArguments() as $name => $argument) {
            $inputArguments[$name] = $this->getInputArgumentData($argument);
        }
        $inputOptions = [];
        foreach ($definition->getOptions() as $name => $option) {
            $inputOptions[$name] = $this->getInputOptionData($option);
        }
        return ['arguments' => $inputArguments, 'options' => $inputOptions];
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Command\Command $command
     * @return mixed[]
     */
    private function getCommandData($command)
    {
        $command->mergeApplicationDefinition(\false);
        return ['name' => $command->getName(), 'usage' => \array_merge([$command->getSynopsis()], $command->getUsages(), $command->getAliases()), 'description' => $command->getDescription(), 'help' => $command->getProcessedHelp(), 'definition' => $this->getInputDefinitionData($command->getDefinition()), 'hidden' => $command->isHidden()];
    }
}
