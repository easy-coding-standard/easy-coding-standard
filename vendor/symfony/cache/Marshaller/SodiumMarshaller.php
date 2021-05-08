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
use ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException;
/**
 * Encrypt/decrypt values using Libsodium.
 *
 * @author Ahmed TAILOULOUTE <ahmed.tailouloute@gmail.com>
 */
class SodiumMarshaller implements \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface
{
    private $marshaller;
    private $decryptionKeys;
    /**
     * @param string[] $decryptionKeys The key at index "0" is required and is used to decrypt and encrypt values;
     *                                 more rotating keys can be provided to decrypt values;
     *                                 each key must be generated using sodium_crypto_box_keypair()
     */
    public function __construct(array $decryptionKeys, \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\MarshallerInterface $marshaller = null)
    {
        if (!self::isSupported()) {
            throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\CacheException('The "sodium" PHP extension is not loaded.');
        }
        if (!isset($decryptionKeys[0])) {
            throw new \ECSPrefix20210508\Symfony\Component\Cache\Exception\InvalidArgumentException('At least one decryption key must be provided at index "0".');
        }
        $this->marshaller = isset($marshaller) ? $marshaller : new \ECSPrefix20210508\Symfony\Component\Cache\Marshaller\DefaultMarshaller();
        $this->decryptionKeys = $decryptionKeys;
    }
    /**
     * @return bool
     */
    public static function isSupported()
    {
        return \function_exists('sodium_crypto_box_seal');
    }
    /**
     * {@inheritdoc}
     * @param mixed[]|null $failed
     * @return mixed[]
     */
    public function marshall(array $values, &$failed)
    {
        $encryptionKey = \sodium_crypto_box_publickey($this->decryptionKeys[0]);
        $encryptedValues = [];
        foreach ($this->marshaller->marshall($values, $failed) as $k => $v) {
            $encryptedValues[$k] = \sodium_crypto_box_seal($v, $encryptionKey);
        }
        return $encryptedValues;
    }
    /**
     * {@inheritdoc}
     * @param string $value
     */
    public function unmarshall($value)
    {
        $value = (string) $value;
        foreach ($this->decryptionKeys as $k) {
            if (\false !== ($decryptedValue = @\sodium_crypto_box_seal_open($value, $k))) {
                $value = $decryptedValue;
                break;
            }
        }
        return $this->marshaller->unmarshall($value);
    }
}
