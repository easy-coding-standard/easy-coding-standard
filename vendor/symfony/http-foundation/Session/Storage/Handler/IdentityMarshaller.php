<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpFoundation\Session\Storage\Handler;

use ECSPrefix20211002\Symfony\Component\Cache\Marshaller\MarshallerInterface;
/**
 * @author Ahmed TAILOULOUTE <ahmed.tailouloute@gmail.com>
 */
class IdentityMarshaller implements \ECSPrefix20211002\Symfony\Component\Cache\Marshaller\MarshallerInterface
{
    /**
     * {@inheritdoc}
     * @param mixed[] $values
     * @param mixed[]|null $failed
     */
    public function marshall($values, &$failed) : array
    {
        foreach ($values as $key => $value) {
            if (!\is_string($value)) {
                throw new \LogicException(\sprintf('%s accepts only string as data.', __METHOD__));
            }
        }
        return $values;
    }
    /**
     * {@inheritdoc}
     * @param string $value
     */
    public function unmarshall($value) : string
    {
        return $value;
    }
}
