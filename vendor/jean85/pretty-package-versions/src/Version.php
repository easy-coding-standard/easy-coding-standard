<?php

namespace ECSPrefix20210507\Jean85;

class Version
{
    const SHORT_COMMIT_LENGTH = 7;
    /** @var string */
    private $packageName;
    /** @var string */
    private $prettyVersion;
    /** @var string */
    private $reference;
    /** @var bool */
    private $versionIsTagged;
    const NO_REFERENCE_TEXT = '{no reference}';
    /**
     * @param string|null $reference
     * @param string $packageName
     * @param string $prettyVersion
     */
    public function __construct($packageName, $prettyVersion, $reference = null)
    {
        $this->packageName = $packageName;
        $this->prettyVersion = $prettyVersion;
        $this->reference = isset($reference) ? $reference : self::NO_REFERENCE_TEXT;
        $this->versionIsTagged = \preg_match('/[^v\\d.]/', $this->getShortVersion()) === 0;
    }
    /**
     * @return string
     */
    public function getPrettyVersion()
    {
        if ($this->versionIsTagged) {
            return $this->prettyVersion;
        }
        return $this->getVersionWithShortReference();
    }
    /**
     * @return string
     */
    public function getFullVersion()
    {
        return $this->prettyVersion . '@' . $this->getReference();
    }
    /**
     * @deprecated
     * @return string
     */
    public function getVersionWithShortCommit()
    {
        return $this->getVersionWithShortReference();
    }
    /**
     * @return string
     */
    public function getVersionWithShortReference()
    {
        return $this->prettyVersion . '@' . $this->getShortReference();
    }
    /**
     * @return string
     */
    public function getPackageName()
    {
        return $this->packageName;
    }
    /**
     * @return string
     */
    public function getShortVersion()
    {
        return $this->prettyVersion;
    }
    /**
     * @deprecated
     * @return string
     */
    public function getCommitHash()
    {
        return $this->getReference();
    }
    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
    /**
     * @deprecated
     * @return string
     */
    public function getShortCommitHash()
    {
        return $this->getShortReference();
    }
    /**
     * @return string
     */
    public function getShortReference()
    {
        return \substr($this->reference, 0, self::SHORT_COMMIT_LENGTH);
    }
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getPrettyVersion();
    }
}
