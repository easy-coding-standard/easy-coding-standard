<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210510\Symfony\Component\HttpFoundation\Session\Storage\Handler;

use ECSPrefix20210510\Symfony\Component\Cache\Marshaller\MarshallerInterface;
/**
 * @author Ahmed TAILOULOUTE <ahmed.tailouloute@gmail.com>
 */
class IdentityMarshaller implements \ECSPrefix20210510\Symfony\Component\Cache\Marshaller\MarshallerInterface
{
    /**
     * {@inheritdoc}
     * @param mixed[]|null $failed
     * @return mixed[]
     */
    public function marshall(array $values, &$failed)
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
     * @return string
     */
    public function unmarshall($value)
    {
        $value = (string) $value;
        return $value;
    }
}
