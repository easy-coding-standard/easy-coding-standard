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
 * XML descriptor.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 *
 * @internal
 */
class XmlDescriptor extends \ECSPrefix20210507\Symfony\Component\Console\Descriptor\Descriptor
{
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputDefinition $definition
     * @return \DOMDocument
     */
    public function getInputDefinitionDocument($definition)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($definitionXML = $dom->createElement('definition'));
        $definitionXML->appendChild($argumentsXML = $dom->createElement('arguments'));
        foreach ($definition->getArguments() as $argument) {
            $this->appendDocument($argumentsXML, $this->getInputArgumentDocument($argument));
        }
        $definitionXML->appendChild($optionsXML = $dom->createElement('options'));
        foreach ($definition->getOptions() as $option) {
            $this->appendDocument($optionsXML, $this->getInputOptionDocument($option));
        }
        return $dom;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Command\Command $command
     * @return \DOMDocument
     */
    public function getCommandDocument($command)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($commandXML = $dom->createElement('command'));
        $command->mergeApplicationDefinition(\false);
        $commandXML->setAttribute('id', $command->getName());
        $commandXML->setAttribute('name', $command->getName());
        $commandXML->setAttribute('hidden', $command->isHidden() ? 1 : 0);
        $commandXML->appendChild($usagesXML = $dom->createElement('usages'));
        foreach (\array_merge([$command->getSynopsis()], $command->getAliases(), $command->getUsages()) as $usage) {
            $usagesXML->appendChild($dom->createElement('usage', $usage));
        }
        $commandXML->appendChild($descriptionXML = $dom->createElement('description'));
        $descriptionXML->appendChild($dom->createTextNode(\str_replace("\n", "\n ", $command->getDescription())));
        $commandXML->appendChild($helpXML = $dom->createElement('help'));
        $helpXML->appendChild($dom->createTextNode(\str_replace("\n", "\n ", $command->getProcessedHelp())));
        $definitionXML = $this->getInputDefinitionDocument($command->getDefinition());
        $this->appendDocument($commandXML, $definitionXML->getElementsByTagName('definition')->item(0));
        return $dom;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Application $application
     * @param string $namespace
     * @return \DOMDocument
     */
    public function getApplicationDocument($application, $namespace = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($rootXml = $dom->createElement('symfony'));
        if ('UNKNOWN' !== $application->getName()) {
            $rootXml->setAttribute('name', $application->getName());
            if ('UNKNOWN' !== $application->getVersion()) {
                $rootXml->setAttribute('version', $application->getVersion());
            }
        }
        $rootXml->appendChild($commandsXML = $dom->createElement('commands'));
        $description = new \ECSPrefix20210507\Symfony\Component\Console\Descriptor\ApplicationDescription($application, $namespace, \true);
        if ($namespace) {
            $commandsXML->setAttribute('namespace', $namespace);
        }
        foreach ($description->getCommands() as $command) {
            $this->appendDocument($commandsXML, $this->getCommandDocument($command));
        }
        if (!$namespace) {
            $rootXml->appendChild($namespacesXML = $dom->createElement('namespaces'));
            foreach ($description->getNamespaces() as $namespaceDescription) {
                $namespacesXML->appendChild($namespaceArrayXML = $dom->createElement('namespace'));
                $namespaceArrayXML->setAttribute('id', $namespaceDescription['id']);
                foreach ($namespaceDescription['commands'] as $name) {
                    $namespaceArrayXML->appendChild($commandXML = $dom->createElement('command'));
                    $commandXML->appendChild($dom->createTextNode($name));
                }
            }
        }
        return $dom;
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputArgument $argument
     */
    protected function describeInputArgument($argument, array $options = [])
    {
        $this->writeDocument($this->getInputArgumentDocument($argument));
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption $option
     */
    protected function describeInputOption($option, array $options = [])
    {
        $this->writeDocument($this->getInputOptionDocument($option));
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputDefinition $definition
     */
    protected function describeInputDefinition($definition, array $options = [])
    {
        $this->writeDocument($this->getInputDefinitionDocument($definition));
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Command\Command $command
     */
    protected function describeCommand($command, array $options = [])
    {
        $this->writeDocument($this->getCommandDocument($command));
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\Console\Application $application
     */
    protected function describeApplication($application, array $options = [])
    {
        $this->writeDocument($this->getApplicationDocument($application, isset($options['namespace']) ? $options['namespace'] : null));
    }
    /**
     * Appends document children to parent node.
     * @param \DOMNode $parentNode
     * @param \DOMNode $importedParent
     */
    private function appendDocument($parentNode, $importedParent)
    {
        foreach ($importedParent->childNodes as $childNode) {
            $parentNode->appendChild($parentNode->ownerDocument->importNode($childNode, \true));
        }
    }
    /**
     * Writes DOM document.
     * @param \DOMDocument $dom
     */
    private function writeDocument($dom)
    {
        $dom->formatOutput = \true;
        $this->write($dom->saveXML());
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputArgument $argument
     * @return \DOMDocument
     */
    private function getInputArgumentDocument($argument)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($objectXML = $dom->createElement('argument'));
        $objectXML->setAttribute('name', $argument->getName());
        $objectXML->setAttribute('is_required', $argument->isRequired() ? 1 : 0);
        $objectXML->setAttribute('is_array', $argument->isArray() ? 1 : 0);
        $objectXML->appendChild($descriptionXML = $dom->createElement('description'));
        $descriptionXML->appendChild($dom->createTextNode($argument->getDescription()));
        $objectXML->appendChild($defaultsXML = $dom->createElement('defaults'));
        $defaults = \is_array($argument->getDefault()) ? $argument->getDefault() : (\is_bool($argument->getDefault()) ? [\var_export($argument->getDefault(), \true)] : ($argument->getDefault() ? [$argument->getDefault()] : []));
        foreach ($defaults as $default) {
            $defaultsXML->appendChild($defaultXML = $dom->createElement('default'));
            $defaultXML->appendChild($dom->createTextNode($default));
        }
        return $dom;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputOption $option
     * @return \DOMDocument
     */
    private function getInputOptionDocument($option)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($objectXML = $dom->createElement('option'));
        $objectXML->setAttribute('name', '--' . $option->getName());
        $pos = \strpos($option->getShortcut(), '|');
        if (\false !== $pos) {
            $objectXML->setAttribute('shortcut', '-' . \substr($option->getShortcut(), 0, $pos));
            $objectXML->setAttribute('shortcuts', '-' . \str_replace('|', '|-', $option->getShortcut()));
        } else {
            $objectXML->setAttribute('shortcut', $option->getShortcut() ? '-' . $option->getShortcut() : '');
        }
        $objectXML->setAttribute('accept_value', $option->acceptValue() ? 1 : 0);
        $objectXML->setAttribute('is_value_required', $option->isValueRequired() ? 1 : 0);
        $objectXML->setAttribute('is_multiple', $option->isArray() ? 1 : 0);
        $objectXML->appendChild($descriptionXML = $dom->createElement('description'));
        $descriptionXML->appendChild($dom->createTextNode($option->getDescription()));
        if ($option->acceptValue()) {
            $defaults = \is_array($option->getDefault()) ? $option->getDefault() : (\is_bool($option->getDefault()) ? [\var_export($option->getDefault(), \true)] : ($option->getDefault() ? [$option->getDefault()] : []));
            $objectXML->appendChild($defaultsXML = $dom->createElement('defaults'));
            if (!empty($defaults)) {
                foreach ($defaults as $default) {
                    $defaultsXML->appendChild($defaultXML = $dom->createElement('default'));
                    $defaultXML->appendChild($dom->createTextNode($default));
                }
            }
        }
        return $dom;
    }
}
