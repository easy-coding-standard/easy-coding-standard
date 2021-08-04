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
namespace ECSPrefix20210804\PHPUnit\Util\Xml;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class SchemaDetector
{
    /**
     * @throws Exception
     */
    public function detect(string $filename) : \ECSPrefix20210804\PHPUnit\Util\Xml\SchemaDetectionResult
    {
        $document = (new \ECSPrefix20210804\PHPUnit\Util\Xml\Loader())->loadFile($filename, \false, \true, \true);
        foreach (['9.2', '8.5'] as $candidate) {
            $schema = (new \ECSPrefix20210804\PHPUnit\Util\Xml\SchemaFinder())->find($candidate);
            if (!(new \ECSPrefix20210804\PHPUnit\Util\Xml\Validator())->validate($document, $schema)->hasValidationErrors()) {
                return new \ECSPrefix20210804\PHPUnit\Util\Xml\SuccessfulSchemaDetectionResult($candidate);
            }
        }
        return new \ECSPrefix20210804\PHPUnit\Util\Xml\FailedSchemaDetectionResult();
    }
}
