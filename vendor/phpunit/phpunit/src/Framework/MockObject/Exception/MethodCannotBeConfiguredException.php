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

use function sprintf;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class MethodCannotBeConfiguredException extends \ECSPrefix20210804\PHPUnit\Framework\Exception implements \ECSPrefix20210804\PHPUnit\Framework\MockObject\Exception
{
    public function __construct(string $method)
    {
        parent::__construct(\sprintf('Trying to configure method "%s" which cannot be configured because it does not exist, has not been specified, is final, or is static', $method));
    }
}
