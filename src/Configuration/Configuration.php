<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Configuration;

use ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface;
use ConfigTransformer20210601\Symplify\ConfigTransformer\Guard\InputValidator;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format;
use ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option;
use ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
final class Configuration implements \ConfigTransformer20210601\Symplify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface
{
    /**
     * @var string[]
     */
    const ALLOWED_OUTPUT_FORMATS = [\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::PHP];
    /**
     * @var string[]
     */
    const ALLOWED_INPUT_FORMATS = [\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::XML, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YML, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML];
    /**
     * @var string
     */
    private $outputFormat;
    /**
     * @var string
     */
    private $inputFormat;
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
     * @var InputValidator
     */
    private $inputValidator;
    public function __construct(\ConfigTransformer20210601\Symplify\ConfigTransformer\Guard\InputValidator $inputValidator)
    {
        $this->inputValidator = $inputValidator;
    }
    /**
     * @return void
     */
    public function populateFromInput(\ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface $input)
    {
        $this->source = (array) $input->getArgument(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::SOURCES);
        $this->targetSymfonyVersion = \floatval($input->getOption(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::TARGET_SYMFONY_VERSION));
        $this->isDryRun = \boolval($input->getOption(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::DRY_RUN));
        $this->resolveInputFormat($input);
        $this->resolveOutputFormat($input);
    }
    public function getOutputFormat() : string
    {
        return $this->outputFormat;
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
    public function getInputFormat() : string
    {
        return $this->inputFormat;
    }
    /**
     * @return void
     */
    public function changeSymfonyVersion(float $symfonyVersion)
    {
        $this->targetSymfonyVersion = $symfonyVersion;
    }
    /**
     * @return void
     */
    public function changeInputFormat(string $inputFormat)
    {
        $this->setInputFormat($inputFormat);
    }
    /**
     * @return void
     */
    public function changeOutputFormat(string $outputFormat)
    {
        $this->setOutputFormat($outputFormat);
    }
    /**
     * @return string[]
     */
    public function getInputSuffixes() : array
    {
        if ($this->inputFormat === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML) {
            return [\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YML];
        }
        return [$this->inputFormat];
    }
    /**
     * @return void
     */
    private function resolveInputFormat(\ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface $input)
    {
        /** @var string $inputFormat */
        $inputFormat = (string) $input->getOption(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::INPUT_FORMAT);
        $inputFormat = $this->resolveEmptyInputFallback($input, $inputFormat);
        $this->setInputFormat($inputFormat);
    }
    /**
     * @return void
     */
    private function resolveOutputFormat(\ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface $input)
    {
        /** @var string $outputFormat */
        $outputFormat = (string) $input->getOption(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::OUTPUT_FORMAT);
        $this->setOutputFormat($outputFormat);
    }
    /**
     * @return void
     */
    private function setOutputFormat(string $outputFormat)
    {
        $this->inputValidator->validateFormatValue($outputFormat, self::ALLOWED_OUTPUT_FORMATS, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::OUTPUT_FORMAT);
        $this->outputFormat = $outputFormat;
    }
    /**
     * @return void
     */
    private function setInputFormat(string $inputFormat)
    {
        $this->inputValidator->validateFormatValue($inputFormat, self::ALLOWED_INPUT_FORMATS, \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::INPUT_FORMAT);
        if ($inputFormat === \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YML) {
            $inputFormat = \ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Format::YAML;
        }
        $this->inputFormat = $inputFormat;
    }
    /**
     * Autoresolve input format in case of 1 file is provided and no "--input-format"
     */
    private function resolveEmptyInputFallback(\ConfigTransformer20210601\Symfony\Component\Console\Input\InputInterface $input, string $inputFormat) : string
    {
        if ($inputFormat !== '') {
            return $inputFormat;
        }
        $source = (array) $input->getArgument(\ConfigTransformer20210601\Symplify\ConfigTransformer\ValueObject\Option::SOURCES);
        // nothing we can do
        if (\count($source) !== 1) {
            return '';
        }
        $singleSource = $source[0];
        if (!\file_exists($singleSource)) {
            return '';
        }
        if (!\is_file($singleSource)) {
            return '';
        }
        $sourceFileInfo = new \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo($singleSource);
        return $sourceFileInfo->getSuffix();
    }
}
