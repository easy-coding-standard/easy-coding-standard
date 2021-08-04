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
namespace ECSPrefix20210804\PHPUnit\Framework\MockObject;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class SoapExtensionNotAvailableException extends \ECSPrefix20210804\PHPUnit\Framework\Exception implements \ECSPrefix20210804\PHPUnit\Framework\MockObject\Exception
{
    public function __construct()
    {
        parent::__construct('The SOAP extension is required to generate a test double from WSDL');
    }
}
