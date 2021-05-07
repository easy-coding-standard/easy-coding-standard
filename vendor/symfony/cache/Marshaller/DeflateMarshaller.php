<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Cache\Marshaller;

use ECSPrefix20210507\Symfony\Component\Cache\Exception\CacheException;
/**
 * Compresses values using gzdeflate().
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DeflateMarshaller implements \ECSPrefix20210507\Symfony\Component\Cache\Marshaller\MarshallerInterface
{
    private $marshaller;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Cache\Marshaller\MarshallerInterface $marshaller
     */
    public function __construct($marshaller)
    {
        if (!\function_exists('gzdeflate')) {
            throw new \ECSPrefix20210507\Symfony\Component\Cache\Exception\CacheException('The "zlib" PHP extension is not loaded.');
        }
        $this->marshaller = $marshaller;
    }
    /**
     * {@inheritdoc}
     * @param mixed[]|null $failed
     * @return mixed[]
     */
    public function marshall(array $values, &$failed)
    {
        return \array_map('gzdeflate', $this->marshaller->marshall($values, $failed));
    }
    /**
     * {@inheritdoc}
     * @param string $value
     */
    public function unmarshall($value)
    {
        if (\false !== ($inflatedValue = @\gzinflate($value))) {
            $value = $inflatedValue;
        }
        return $this->marshaller->unmarshall($value);
    }
}
