<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\VarDumper\Caster;

use ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * Casts common resource types to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 */
class ResourceCaster
{
    /**
     * @param \CurlHandle|resource $h
     *
     * @return array
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castCurl($h, array $a, $stub, $isNested)
    {
        return \curl_getinfo($h);
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castDba($dba, array $a, $stub, $isNested)
    {
        $list = \dba_list();
        $a['file'] = $list[(int) $dba];
        return $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castProcess($process, array $a, $stub, $isNested)
    {
        return \proc_get_status($process);
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castStream($stream, array $a, $stub, $isNested)
    {
        $a = \stream_get_meta_data($stream) + static::castStreamContext($stream, $a, $stub, $isNested);
        if (isset($a['uri'])) {
            $a['uri'] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\LinkStub($a['uri']);
        }
        return $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castStreamContext($stream, array $a, $stub, $isNested)
    {
        return @\stream_context_get_params($stream) ?: $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     */
    public static function castGd($gd, array $a, $stub, $isNested)
    {
        $a['size'] = \imagesx($gd) . 'x' . \imagesy($gd);
        $a['trueColor'] = \imageistruecolor($gd);
        return $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castMysqlLink($h, array $a, $stub, $isNested)
    {
        $a['host'] = \mysql_get_host_info($h);
        $a['protocol'] = \mysql_get_proto_info($h);
        $a['server'] = \mysql_get_server_info($h);
        return $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castOpensslX509($h, array $a, $stub, $isNested)
    {
        $stub->cut = -1;
        $info = \openssl_x509_parse($h, \false);
        $pin = \openssl_pkey_get_public($h);
        $pin = \openssl_pkey_get_details($pin)['key'];
        $pin = \array_slice(\explode("\n", $pin), 1, -2);
        $pin = \base64_decode(\implode('', $pin));
        $pin = \base64_encode(\hash('sha256', $pin, \true));
        $a += ['subject' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\EnumStub(\array_intersect_key($info['subject'], ['organizationName' => \true, 'commonName' => \true])), 'issuer' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\EnumStub(\array_intersect_key($info['issuer'], ['organizationName' => \true, 'commonName' => \true])), 'expiry' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(\date(\DateTime::ISO8601, $info['validTo_time_t']), $info['validTo_time_t']), 'fingerprint' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\EnumStub(['md5' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(\wordwrap(\strtoupper(\openssl_x509_fingerprint($h, 'md5')), 2, ':', \true)), 'sha1' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(\wordwrap(\strtoupper(\openssl_x509_fingerprint($h, 'sha1')), 2, ':', \true)), 'sha256' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(\wordwrap(\strtoupper(\openssl_x509_fingerprint($h, 'sha256')), 2, ':', \true)), 'pin-sha256' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub($pin)])];
        return $a;
    }
}
