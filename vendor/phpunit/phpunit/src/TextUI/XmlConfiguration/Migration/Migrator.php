<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration;

use function sprintf;
use ECSPrefix20210803\PHPUnit\Util\Xml\Exception as XmlException;
use ECSPrefix20210803\PHPUnit\Util\Xml\Loader as XmlLoader;
use ECSPrefix20210803\PHPUnit\Util\Xml\SchemaDetector;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Migrator
{
    /**
     * @throws Exception
     * @throws MigrationBuilderException
     * @throws MigrationException
     * @throws XmlException
     */
    public function migrate(string $filename) : string
    {
        $origin = (new \ECSPrefix20210803\PHPUnit\Util\Xml\SchemaDetector())->detect($filename);
        if (!$origin->detected()) {
            throw new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Exception(\sprintf('"%s" is not a valid PHPUnit XML configuration file that can be migrated', $filename));
        }
        $configurationDocument = (new \ECSPrefix20210803\PHPUnit\Util\Xml\Loader())->loadFile($filename, \false, \true, \true);
        foreach ((new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\MigrationBuilder())->build($origin->version()) as $migration) {
            $migration->migrate($configurationDocument);
        }
        $configurationDocument->formatOutput = \true;
        $configurationDocument->preserveWhiteSpace = \false;
        return $configurationDocument->saveXML();
    }
}
