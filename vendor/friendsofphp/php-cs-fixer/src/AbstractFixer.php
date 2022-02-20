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
namespace PhpCsFixer;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOption;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractFixer implements \PhpCsFixer\Fixer\FixerInterface
{
    /**
     * @var null|array<string, mixed>
     */
    protected $configuration;
    /**
     * @var WhitespacesFixerConfig
     */
    protected $whitespacesConfig;
    /**
     * @var null|FixerConfigurationResolverInterface
     */
    private $configurationDefinition;
    public function __construct()
    {
        if ($this instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface) {
            try {
                $this->configure([]);
            } catch (\PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException $e) {
                // ignore
            }
        }
        if ($this instanceof \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface) {
            $this->whitespacesConfig = $this->getDefaultWhitespacesFixerConfig();
        }
    }
    public final function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        if ($this instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface && null === $this->configuration) {
            throw new \PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException($this->getName(), 'Configuration is required.');
        }
        if (0 < $tokens->count() && $this->isCandidate($tokens) && $this->supports($file)) {
            $this->applyFix($file, $tokens);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky() : bool
    {
        return \false;
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        $nameParts = \explode('\\', static::class);
        $name = \substr(\end($nameParts), 0, -\strlen('Fixer'));
        return \PhpCsFixer\Utils::camelCaseToUnderscore($name);
    }
    /**
     * {@inheritdoc}
     */
    public function getPriority() : int
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file) : bool
    {
        return \true;
    }
    public function configure(array $configuration) : void
    {
        if (!$this instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface) {
            throw new \LogicException('Cannot configure using Abstract parent, child not implementing "PhpCsFixer\\Fixer\\ConfigurableFixerInterface".');
        }
        foreach ($this->getConfigurationDefinition()->getOptions() as $option) {
            if (!$option instanceof \PhpCsFixer\FixerConfiguration\DeprecatedFixerOption) {
                continue;
            }
            $name = $option->getName();
            if (\array_key_exists($name, $configuration)) {
                \PhpCsFixer\Utils::triggerDeprecation(new \InvalidArgumentException(\sprintf('Option "%s" for rule "%s" is deprecated and will be removed in version %d.0. %s', $name, $this->getName(), \PhpCsFixer\Console\Application::getMajorVersion() + 1, \str_replace('`', '"', $option->getDeprecationMessage()))));
            }
        }
        try {
            $this->configuration = $this->getConfigurationDefinition()->resolve($configuration);
        } catch (\ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\MissingOptionsException $exception) {
            throw new \PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException($this->getName(), \sprintf('Missing required configuration: %s', $exception->getMessage()), $exception);
        } catch (\PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException $exception) {
            throw new \PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException($this->getName(), \sprintf('Invalid configuration for env: %s', $exception->getMessage()), $exception);
        } catch (\ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\ExceptionInterface $exception) {
            throw new \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException($this->getName(), \sprintf('Invalid configuration: %s', $exception->getMessage()), $exception);
        }
    }
    public function getConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        if (!$this instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface) {
            throw new \LogicException(\sprintf('Cannot get configuration definition using Abstract parent, child "%s" not implementing "PhpCsFixer\\Fixer\\ConfigurableFixerInterface".', static::class));
        }
        if (null === $this->configurationDefinition) {
            $this->configurationDefinition = $this->createConfigurationDefinition();
        }
        return $this->configurationDefinition;
    }
    public function setWhitespacesConfig(\PhpCsFixer\WhitespacesFixerConfig $config) : void
    {
        if (!$this instanceof \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface) {
            throw new \LogicException('Cannot run method for class not implementing "PhpCsFixer\\Fixer\\WhitespacesAwareFixerInterface".');
        }
        $this->whitespacesConfig = $config;
    }
    protected abstract function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void;
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        if (!$this instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface) {
            throw new \LogicException('Cannot create configuration definition using Abstract parent, child not implementing "PhpCsFixer\\Fixer\\ConfigurableFixerInterface".');
        }
        throw new \LogicException('Not implemented.');
    }
    private function getDefaultWhitespacesFixerConfig() : \PhpCsFixer\WhitespacesFixerConfig
    {
        static $defaultWhitespacesFixerConfig = null;
        if (null === $defaultWhitespacesFixerConfig) {
            $defaultWhitespacesFixerConfig = new \PhpCsFixer\WhitespacesFixerConfig('    ', "\n");
        }
        return $defaultWhitespacesFixerConfig;
    }
}
