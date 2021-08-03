<?php

declare (strict_types=1);
/*
 * This file is part of PharIo\Manifest.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PharIo\Manifest;

use ECSPrefix20210803\PharIo\Version\Exception as VersionException;
use ECSPrefix20210803\PharIo\Version\Version;
use ECSPrefix20210803\PharIo\Version\VersionConstraintParser;
class ManifestDocumentMapper
{
    public function map(\ECSPrefix20210803\PharIo\Manifest\ManifestDocument $document) : \ECSPrefix20210803\PharIo\Manifest\Manifest
    {
        try {
            $contains = $document->getContainsElement();
            $type = $this->mapType($contains);
            $copyright = $this->mapCopyright($document->getCopyrightElement());
            $requirements = $this->mapRequirements($document->getRequiresElement());
            $bundledComponents = $this->mapBundledComponents($document);
            return new \ECSPrefix20210803\PharIo\Manifest\Manifest(new \ECSPrefix20210803\PharIo\Manifest\ApplicationName($contains->getName()), new \ECSPrefix20210803\PharIo\Version\Version($contains->getVersion()), $type, $copyright, $requirements, $bundledComponents);
        } catch (\ECSPrefix20210803\PharIo\Version\Exception $e) {
            throw new \ECSPrefix20210803\PharIo\Manifest\ManifestDocumentMapperException($e->getMessage(), (int) $e->getCode(), $e);
        } catch (\ECSPrefix20210803\PharIo\Manifest\Exception $e) {
            throw new \ECSPrefix20210803\PharIo\Manifest\ManifestDocumentMapperException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
    private function mapType(\ECSPrefix20210803\PharIo\Manifest\ContainsElement $contains) : \ECSPrefix20210803\PharIo\Manifest\Type
    {
        switch ($contains->getType()) {
            case 'application':
                return \ECSPrefix20210803\PharIo\Manifest\Type::application();
            case 'library':
                return \ECSPrefix20210803\PharIo\Manifest\Type::library();
            case 'extension':
                return $this->mapExtension($contains->getExtensionElement());
        }
        throw new \ECSPrefix20210803\PharIo\Manifest\ManifestDocumentMapperException(\sprintf('Unsupported type %s', $contains->getType()));
    }
    private function mapCopyright(\ECSPrefix20210803\PharIo\Manifest\CopyrightElement $copyright) : \ECSPrefix20210803\PharIo\Manifest\CopyrightInformation
    {
        $authors = new \ECSPrefix20210803\PharIo\Manifest\AuthorCollection();
        foreach ($copyright->getAuthorElements() as $authorElement) {
            $authors->add(new \ECSPrefix20210803\PharIo\Manifest\Author($authorElement->getName(), new \ECSPrefix20210803\PharIo\Manifest\Email($authorElement->getEmail())));
        }
        $licenseElement = $copyright->getLicenseElement();
        $license = new \ECSPrefix20210803\PharIo\Manifest\License($licenseElement->getType(), new \ECSPrefix20210803\PharIo\Manifest\Url($licenseElement->getUrl()));
        return new \ECSPrefix20210803\PharIo\Manifest\CopyrightInformation($authors, $license);
    }
    private function mapRequirements(\ECSPrefix20210803\PharIo\Manifest\RequiresElement $requires) : \ECSPrefix20210803\PharIo\Manifest\RequirementCollection
    {
        $collection = new \ECSPrefix20210803\PharIo\Manifest\RequirementCollection();
        $phpElement = $requires->getPHPElement();
        $parser = new \ECSPrefix20210803\PharIo\Version\VersionConstraintParser();
        try {
            $versionConstraint = $parser->parse($phpElement->getVersion());
        } catch (\ECSPrefix20210803\PharIo\Version\Exception $e) {
            throw new \ECSPrefix20210803\PharIo\Manifest\ManifestDocumentMapperException(\sprintf('Unsupported version constraint - %s', $e->getMessage()), (int) $e->getCode(), $e);
        }
        $collection->add(new \ECSPrefix20210803\PharIo\Manifest\PhpVersionRequirement($versionConstraint));
        if (!$phpElement->hasExtElements()) {
            return $collection;
        }
        foreach ($phpElement->getExtElements() as $extElement) {
            $collection->add(new \ECSPrefix20210803\PharIo\Manifest\PhpExtensionRequirement($extElement->getName()));
        }
        return $collection;
    }
    private function mapBundledComponents(\ECSPrefix20210803\PharIo\Manifest\ManifestDocument $document) : \ECSPrefix20210803\PharIo\Manifest\BundledComponentCollection
    {
        $collection = new \ECSPrefix20210803\PharIo\Manifest\BundledComponentCollection();
        if (!$document->hasBundlesElement()) {
            return $collection;
        }
        foreach ($document->getBundlesElement()->getComponentElements() as $componentElement) {
            $collection->add(new \ECSPrefix20210803\PharIo\Manifest\BundledComponent($componentElement->getName(), new \ECSPrefix20210803\PharIo\Version\Version($componentElement->getVersion())));
        }
        return $collection;
    }
    private function mapExtension(\ECSPrefix20210803\PharIo\Manifest\ExtensionElement $extension) : \ECSPrefix20210803\PharIo\Manifest\Extension
    {
        try {
            $versionConstraint = (new \ECSPrefix20210803\PharIo\Version\VersionConstraintParser())->parse($extension->getCompatible());
            return \ECSPrefix20210803\PharIo\Manifest\Type::extension(new \ECSPrefix20210803\PharIo\Manifest\ApplicationName($extension->getFor()), $versionConstraint);
        } catch (\ECSPrefix20210803\PharIo\Version\Exception $e) {
            throw new \ECSPrefix20210803\PharIo\Manifest\ManifestDocumentMapperException(\sprintf('Unsupported version constraint - %s', $e->getMessage()), (int) $e->getCode(), $e);
        }
    }
}
