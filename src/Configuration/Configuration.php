<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration;

use ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
final class Configuration implements \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface
{
    /**
     * @var string[]
     */
    private $source = [];
    /**
     * @var float
     */
    private $targetSymfonyVersion;
    /**
     * @var bool
     */
    private $isDryRun = \false;
    /**
     * @return void
     */
    public function populateFromInput(\ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface $input)
    {
        $this->source = (array) $input->getArgument(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::SOURCES);
        $this->targetSymfonyVersion = \floatval($input->getOption(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::TARGET_SYMFONY_VERSION));
        $this->isDryRun = \boolval($input->getOption(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::DRY_RUN));
    }
    /**
     * @return string[]
     */
    public function getSource() : array
    {
        return $this->source;
    }
    public function isAtLeastSymfonyVersion(float $symfonyVersion) : bool
    {
        return $this->targetSymfonyVersion >= $symfonyVersion;
    }
    public function isDryRun() : bool
    {
        return $this->isDryRun;
    }
    /**
     * @return void
     */
    public function changeSymfonyVersion(float $symfonyVersion)
    {
        $this->targetSymfonyVersion = $symfonyVersion;
    }
    /**
     * @return string[]
     */
    public function getInputSuffixes() : array
    {
        return [\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YML, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::XML];
    }
}
