<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\CheckerSetExtractor;

use SimpleXMLElement;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Exception\MissingSniffSetException;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Sniff\SniffNaming;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Sniff\XmlConfigurationExtractor;
use Symplify\EasyCodingStandard\SniffRunner\Sniff\Finder\SniffFinder;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

final class SniffSetExtractor
{
    /**
     * @var string[]
     */
    private $sniffSets = [];

    /**
     * @var SniffNaming
     */
    private $sniffNaming;

    /**
     * @var XmlConfigurationExtractor
     */
    private $xmlConfigurationExtractor;

    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    public function __construct(
        SniffNaming $sniffNaming,
        XmlConfigurationExtractor $xmlConfigurationExtractor,
        SniffFinder $sniffFinder
    ) {
        $this->sniffNaming = $sniffNaming;
        $this->xmlConfigurationExtractor = $xmlConfigurationExtractor;
        $this->sniffFinder = $sniffFinder;
    }

    /**
     * @return mixed[]
     */
    public function extract(string $name): array
    {
        $this->ensureSetExists($name);

        $sniffs = [];
        $sniffs = $this->addSniffsFromOwnSet($sniffs, $this->getRulesetXmlFileForSetName($name));

        return $this->addSniffsFromSniffSet($sniffs, $name);
    }

    /**
     * @return string[]
     */
    private function getSniffSets(): array
    {
        if ($this->sniffSets) {
            return $this->sniffSets;
        }

        foreach ($this->findRulesetFiles() as $rulesetFile) {
            $rulesetXml = simplexml_load_file($rulesetFile);
            $setName = (string) $rulesetXml['name'];
            $this->sniffSets[$setName] = $rulesetFile;
        }

        return $this->sniffSets;
    }

    /**
     * @return string[]
     */
    private function findRulesetFiles(): array
    {
        $installedStandards = Finder::create()->files()
            ->in(VendorDirProvider::provide() . '/squizlabs/php_codesniffer/src')
            ->name('ruleset.xml')
            ->getIterator();

        return array_keys(iterator_to_array($installedStandards));
    }

    private function ensureSetExists(string $name): void
    {
        if (! isset($this->getSniffSets()[$name])) {
            throw new MissingSniffSetException(sprintf(
                'Set "%s" was not found. Try one of: "%s.',
                $name,
                implode(', ', array_keys($this->getSniffSets()))
            ));
        }
    }

    private function isRuleXmlElementSkipped(SimpleXMLElement $ruleXmlElement): bool
    {
        if (! isset($ruleXmlElement['ref'])) {
            return true;
        }

        if (isset($ruleXmlElement->severity)) {
            return $this->sniffNaming->isSniffName((string) $ruleXmlElement['ref']);
        }

        return false;
    }

    /**
     * @param mixed[] $sniffs
     * @return mixed[]
     */
    private function addSniffsFromOwnSet(array $sniffs, string $sniffSetFile): array
    {
        $sniffDir = dirname($sniffSetFile) . '/Sniffs';
        if (! is_dir($sniffDir)) {
            return [];
        }

        $ownSniffs = $this->sniffFinder->findAllSniffClassesInDirectory($sniffDir);
        $normalizedOwnSniffs = array_fill_keys($ownSniffs, null);
        $sniffs += $normalizedOwnSniffs;

        return $sniffs;
    }

    /**
     * @param mixed[] $sniffs
     * @return mixed[]
     */
    private function addSniffsFromSniffSet(array $sniffs, string $sniffSetName): array
    {
        $sniffSetFile = $this->getSniffSets()[$sniffSetName];
        $sniffSetXml = simplexml_load_file($sniffSetFile);

        foreach ($sniffSetXml->rule as $ruleXmlElement) {
            if ($this->isRuleXmlElementSkipped($ruleXmlElement)) {
                continue;
            }

            $ruleId = (string) $ruleXmlElement['ref'];

            if ($this->isRuleSet($ruleId)) {
                $sniffs += $this->addSniffsFromSniffSet($sniffs, $ruleId);

                continue;
            }

            $sniffClass = $this->sniffNaming->guessSniffClassByName($ruleId);
            $configuration = $this->xmlConfigurationExtractor->extractFromRuleXmlElement($ruleXmlElement);
            $sniffs[$sniffClass] = $configuration;
        }

        return $sniffs;
    }

    private function isRuleSet(string $name): bool
    {
        return isset($this->getSniffSets()[$name]);
    }

    /**
     * @return mixed|string
     */
    private function getRulesetXmlFileForSetName(string $name)
    {
        return $this->getSniffSets()[$name];
    }
}
