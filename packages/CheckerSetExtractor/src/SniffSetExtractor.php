<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\CheckerSetExtractor;

use SimpleXMLElement;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Exception\MissingSniffSetException;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Sniff\SniffNaming;
use Symplify\EasyCodingStandard\CheckerSetExtractor\Sniff\XmlConfigurationExtractor;
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

    public function __construct(SniffNaming $sniffNaming, XmlConfigurationExtractor $xmlConfigurationExtractor)
    {
        $this->sniffNaming = $sniffNaming;
        $this->xmlConfigurationExtractor = $xmlConfigurationExtractor;
    }

    /**
     * @return mixed[]
     */
    public function extract(string $name): array
    {
        $this->ensureSetExists($name);

        $sniffs = [];
        $sniffs = $this->addSniffsFromSniffSet($sniffs, $name);

        return $sniffs;
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
        $installedStandards = (new Finder)->files()
            ->in(VendorDirProvider::provide())
            ->name('ruleset.xml')
            ->getIterator();

        return array_keys(iterator_to_array($installedStandards));
    }

    private function ensureSetExists(string $name): void
    {
        $availableSniffSetNames = array_keys($this->getSniffSets());
        if (! in_array($name, $availableSniffSetNames, true)) {
            throw new MissingSniffSetException(sprintf(
                'Set "%s" was not found. Try one of: "%s.',
                $name,
                implode(', ', $availableSniffSetNames)
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
    private function addSniffsFromSniffSet(array $sniffs, string $name): array
    {
        $sniffSetFile = $this->getSniffSets()[$name];
        $sniffSetXml = simplexml_load_file($sniffSetFile);

        foreach ($sniffSetXml->rule as $ruleXmlElement) {
            if ($this->isRuleXmlElementSkipped($ruleXmlElement)) {
                continue;
            }

            $ruleId = (string) $ruleXmlElement['ref'];

            // is ruleset => recurse!
            if (isset($this->getSniffSets()[$ruleId])) {
                $sniffs += $this->addSniffsFromSniffSet($sniffs, $ruleId);
                continue;
            }

            $sniffClass = $this->sniffNaming->guessSniffClassByName($ruleId);
            $configuration = $this->xmlConfigurationExtractor->extractFromRuleXmlElement($ruleXmlElement);
            $sniffs[$sniffClass] = $configuration;
        }

        return $sniffs;
    }
}
