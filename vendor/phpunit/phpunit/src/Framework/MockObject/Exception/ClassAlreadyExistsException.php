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
namespace ECSPrefix20210803\PHPUnit\Framework\MockObject;

use function sprintf;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ClassAlreadyExistsException extends \ECSPrefix20210803\PHPUnit\Framework\Exception implements \ECSPrefix20210803\PHPUnit\Framework\MockObject\Exception
{
    public function __construct(string $className)
    {
        parent::__construct(\sprintf('Class "%s" already exists', $className));
    }
}
