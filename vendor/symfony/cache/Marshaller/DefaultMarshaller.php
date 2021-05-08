<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Cache\Marshaller;

use ECSPrefix20210508\Symfony\Component\Cache\Exception\CacheException;
/**
 * Serializes/unserializes values using igbinary_serialize() if available, serialize() otherwise.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DefaultMarshaller implements \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface
{
    private $useIgbinarySerialize = \true;
    /**
     * @param bool $useIgbinarySerialize
     */
    public function __construct($useIgbinarySerialize = null)
    {
        if (null === $useIgbinarySerialize) {
            $useIgbinarySerialize = \extension_loaded('igbinary') && (\PHP_VERSION_ID < 70400 || \version_compare('3.1.6', \phpversion('igbinary'), '<='));
        } elseif ($useIgbinarySerialize && (!\extension_loaded('igbinary') || \PHP_VERSION_ID >= 70400 && \version_compare('3.1.6', \phpversion('igbinary'), '>'))) {
            throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\CacheException(\extension_loaded('igbinary') && \PHP_VERSION_ID >= 70400 ? 'Please upgrade the "igbinary" PHP extension to v3.1.6 or higher.' : 'The "igbinary" PHP extension is not loaded.');
        }
        $this->useIgbinarySerialize = $useIgbinarySerialize;
    }
    /**
     * {@inheritdoc}
     * @param mixed[]|null $failed
     * @return mixed[]
     */
    public function marshall(array $values, &$failed)
    {
        $serialized = $failed = [];
        foreach ($values as $id => $value) {
            try {
                if ($this->useIgbinarySerialize) {
                    $serialized[$id] = \igbinary_serialize($value);
                } else {
                    $serialized[$id] = \serialize($value);
                }
            } catch (\Exception $e) {
                $failed[] = $id;
            }
        }
        return $serialized;
    }
    /**
     * {@inheritdoc}
     * @param string $value
     */
    public function unmarshall($value)
    {
        if ('b:0;' === $value) {
            return \false;
        }
        if ('N;' === $value) {
            return null;
        }
        static $igbinaryNull;
        if ($value === (isset($igbinaryNull) ? $igbinaryNull : ($igbinaryNull = \extension_loaded('igbinary') ? \igbinary_serialize(null) : \false))) {
            return null;
        }
        $unserializeCallbackHandler = \ini_set('unserialize_callback_func', __CLASS__ . '::handleUnserializeCallback');
        try {
            if (':' === (isset($value[1]) ? $value[1] : ':')) {
                if (\false !== ($value = \unserialize($value))) {
                    return $value;
                }
            } elseif (\false === $igbinaryNull) {
                throw new \RuntimeException('Failed to unserialize values, did you forget to install the "igbinary" extension?');
            } elseif (null !== ($value = \igbinary_unserialize($value))) {
                return $value;
            }
            throw new \DomainException(\error_get_last() ? \error_get_last()['message'] : 'Failed to unserialize values.');
        } catch (\Error $e) {
            throw new \ErrorException($e->getMessage(), $e->getCode(), \E_ERROR, $e->getFile(), $e->getLine());
        } finally {
            \ini_set('unserialize_callback_func', $unserializeCallbackHandler);
        }
    }
    /**
     * @internal
     * @param string $class
     */
    public static function handleUnserializeCallback($class)
    {
        throw new \DomainException('Class not found: ' . $class);
    }
}
