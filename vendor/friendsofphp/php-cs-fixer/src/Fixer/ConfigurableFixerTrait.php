<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException;
use PhpCsFixer\Console\Application;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOption;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException;
use PhpCsFixer\Utils;
use ECSPrefix202503\Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use ECSPrefix202503\Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @template TFixerInputConfig of array<string, mixed>
 * @template TFixerComputedConfig of array<string, mixed>
 */
trait ConfigurableFixerTrait
{
    /**
     * @var null|TFixerComputedConfig
     */
    protected $configuration;
    /**
     * @var \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface|null
     */
    private $configurationDefinition;
    /**
     * @param TFixerInputConfig $configuration
     */
    public final function configure(array $configuration) : void
    {
        $this->configurePreNormalisation($configuration);
        foreach ($this->getConfigurationDefinition()->getOptions() as $option) {
            if (!$option instanceof DeprecatedFixerOption) {
                continue;
            }
            $name = $option->getName();
            if (\array_key_exists($name, $configuration)) {
                Utils::triggerDeprecation(new \InvalidArgumentException(\sprintf('Option "%s" for rule "%s" is deprecated and will be removed in version %d.0. %s', $name, $this->getName(), Application::getMajorVersion() + 1, \str_replace('`', '"', $option->getDeprecationMessage()))));
            }
        }
        try {
            $this->configuration = $this->getConfigurationDefinition()->resolve($configuration);
            // @phpstan-ignore-line ->configuration typehint is autogenerated base on ConfigurationDefinition
        } catch (MissingOptionsException $exception) {
            throw new RequiredFixerConfigurationException($this->getName(), \sprintf('Missing required configuration: %s', $exception->getMessage()), $exception);
        } catch (InvalidOptionsForEnvException $exception) {
            throw new InvalidForEnvFixerConfigurationException($this->getName(), \sprintf('Invalid configuration for env: %s', $exception->getMessage()), $exception);
        } catch (ExceptionInterface $exception) {
            throw new InvalidFixerConfigurationException($this->getName(), \sprintf('Invalid configuration: %s', $exception->getMessage()), $exception);
        }
        $this->configurePostNormalisation();
    }
    public final function getConfigurationDefinition() : FixerConfigurationResolverInterface
    {
        if (null === $this->configurationDefinition) {
            $this->configurationDefinition = $this->createConfigurationDefinition();
        }
        return $this->configurationDefinition;
    }
    public abstract function getName() : string;
    /**
     * One can override me.
     *
     * @param TFixerInputConfig $configuration
     */
    protected function configurePreNormalisation(array &$configuration) : void
    {
        // 🤔 ideally this method won't be needed, maybe we can remove it over time
    }
    /**
     * One can override me.
     */
    protected function configurePostNormalisation() : void
    {
        // 🤔 ideally this method won't be needed, maybe we can remove it over time
    }
    protected abstract function createConfigurationDefinition() : FixerConfigurationResolverInterface;
}
