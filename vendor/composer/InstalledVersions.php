<?php

namespace ConfigTransformer20210601\Composer;

use ConfigTransformer20210601\Composer\Autoload\ClassLoader;
use ConfigTransformer20210601\Composer\Semver\VersionParser;
class InstalledVersions
{
    private static $installed = array('root' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(), 'reference' => NULL, 'name' => 'symplify/config-transformer'), 'versions' => array('nette/neon' => array('pretty_version' => 'v3.2.2', 'version' => '3.2.2.0', 'aliases' => array(), 'reference' => 'e4ca6f4669121ca6876b1d048c612480e39a28d5'), 'nette/utils' => array('pretty_version' => 'v3.2.2', 'version' => '3.2.2.0', 'aliases' => array(), 'reference' => '967cfc4f9a1acd5f1058d76715a424c53343c20c'), 'nikic/php-parser' => array('pretty_version' => 'v4.10.5', 'version' => '4.10.5.0', 'aliases' => array(), 'reference' => '4432ba399e47c66624bc73c8c0f811e5c109576f'), 'psr/cache' => array('pretty_version' => '2.0.0', 'version' => '2.0.0.0', 'aliases' => array(), 'reference' => '213f9dbc5b9bfbc4f8db86d2838dc968752ce13b'), 'psr/cache-implementation' => array('provided' => array(0 => '1.0|2.0')), 'psr/container' => array('pretty_version' => '1.1.1', 'version' => '1.1.1.0', 'aliases' => array(), 'reference' => '8622567409010282b7aeebe4bb841fe98b58dcaf'), 'psr/container-implementation' => array('provided' => array(0 => '1.0')), 'psr/event-dispatcher' => array('pretty_version' => '1.0.0', 'version' => '1.0.0.0', 'aliases' => array(), 'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0'), 'psr/event-dispatcher-implementation' => array('provided' => array(0 => '1.0')), 'psr/log' => array('pretty_version' => '1.1.4', 'version' => '1.1.4.0', 'aliases' => array(), 'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11'), 'psr/log-implementation' => array('provided' => array(0 => '1.0')), 'psr/simple-cache-implementation' => array('provided' => array(0 => '1.0')), 'symfony/cache' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '44fd0f97d1fb198d344f22379dfc56af2221e608'), 'symfony/cache-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => 'c0446463729b89dd4fa62e9aeecc80287323615d'), 'symfony/cache-implementation' => array('provided' => array(0 => '1.0|2.0')), 'symfony/config' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '9f4a448c2d7fd2c90882dfff930b627ddbe16810'), 'symfony/console' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '058553870f7809087fa80fa734704a21b9bcaeb2'), 'symfony/dependency-injection' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '94d973cb742d8c5c5dcf9534220e6b73b09af1d4'), 'symfony/deprecation-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => '5f38c8804a9e97d23e0c8d63341088cd8a22d627'), 'symfony/error-handler' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '0e6768b8c0dcef26df087df2bbbaa143867a59b2'), 'symfony/event-dispatcher' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '67a5f354afa8e2f231081b3fa11a5912f933c3ce'), 'symfony/event-dispatcher-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => '69fee1ad2332a7cbab3aca13591953da9cdb7a11'), 'symfony/event-dispatcher-implementation' => array('provided' => array(0 => '2.0')), 'symfony/expression-language' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => 'e3c136ac5333b0d2ca9de9e7e3efe419362aa046'), 'symfony/filesystem' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '348116319d7fb7d1faa781d26a48922428013eb2'), 'symfony/finder' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '0ae3f047bed4edff6fd35b26a9a6bfdc92c953c6'), 'symfony/http-client-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => '7e82f6084d7cae521a75ef2cb5c9457bbda785f4'), 'symfony/http-foundation' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '31f25d99b329a1461f42bcef8505b54926a30be6'), 'symfony/http-kernel' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => 'f8e8f5391b6909e2f0ba8c12220ab7af3050eb4f'), 'symfony/polyfill-ctype' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => '46cd95797e9df938fdd2b03693b5fca5e64b01ce'), 'symfony/polyfill-intl-grapheme' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => '24b72c6baa32c746a4d0840147c9715e42bb68ab'), 'symfony/polyfill-intl-normalizer' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => '8590a5f561694770bdcd3f9b5c69dde6945028e8'), 'symfony/polyfill-mbstring' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => '2df51500adbaebdc4c38dea4c89a2e131c45c8a1'), 'symfony/polyfill-php73' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => 'fba8933c384d6476ab14fb7b8526e5287ca7e010'), 'symfony/polyfill-php80' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => 'eca0bf41ed421bed1b57c4958bab16aa86b757d0'), 'symfony/polyfill-php81' => array('pretty_version' => 'v1.23.0', 'version' => '1.23.0.0', 'aliases' => array(), 'reference' => 'e66119f3de95efc359483f810c4c3e6436279436'), 'symfony/service-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => 'f040a30e04b57fbcc9c6cbcf4dbaa96bd318b9bb'), 'symfony/service-implementation' => array('provided' => array(0 => '1.0|2.0')), 'symfony/string' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => 'a9a0f8b6aafc5d2d1c116dcccd1573a95153515b'), 'symfony/var-dumper' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '1d3953e627fe4b5f6df503f356b6545ada6351f3'), 'symfony/var-exporter' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '7a7c9dd972541f78e7815c03c0bae9f81e0e9dbb'), 'symfony/yaml' => array('pretty_version' => 'v5.3.0', 'version' => '5.3.0.0', 'aliases' => array(), 'reference' => '3bbcf262fceb3d8f48175302e6ba0ac96e3a5a11'), 'symplify/autowire-array-parameter' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '082531e1758f170dec639ec9cd5858f94bc208f6'), 'symplify/composer-json-manipulator' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'a58d9f73bb7f756b720428566761854a44d86641'), 'symplify/config-transformer' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(), 'reference' => NULL), 'symplify/console-package-builder' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '072420b8373cd28e617dbccd7abdef4e5a5a2871'), 'symplify/easy-testing' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'd12b5b2772dc757b3b6141819fac9b71287095e4'), 'symplify/package-builder' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'be792b98451e1d6098dc76fcbcc64a664b597239'), 'symplify/php-config-printer' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'ee5ec2d04287befa14d8be7564f1432ce0140540'), 'symplify/smart-file-system' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '2dea618353e3da36cb4244b28f0ca41387d764f2'), 'symplify/symplify-kernel' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'aa262eae2070f2349bdcf1690f88f2a93c56bf5c')));
    private static $canGetVendors;
    private static $installedByVendor = array();
    public static function getInstalledPackages()
    {
        $packages = array();
        foreach (self::getInstalled() as $installed) {
            $packages[] = \array_keys($installed['versions']);
        }
        if (1 === \count($packages)) {
            return $packages[0];
        }
        return \array_keys(\array_flip(\call_user_func_array('array_merge', $packages)));
    }
    public static function isInstalled($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (isset($installed['versions'][$packageName])) {
                return \true;
            }
        }
        return \false;
    }
    public static function satisfies(\ConfigTransformer20210601\Composer\Semver\VersionParser $parser, $packageName, $constraint)
    {
        $constraint = $parser->parseConstraints($constraint);
        $provided = $parser->parseConstraints(self::getVersionRanges($packageName));
        return $provided->matches($constraint);
    }
    public static function getVersionRanges($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            $ranges = array();
            if (isset($installed['versions'][$packageName]['pretty_version'])) {
                $ranges[] = $installed['versions'][$packageName]['pretty_version'];
            }
            if (\array_key_exists('aliases', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['aliases']);
            }
            if (\array_key_exists('replaced', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['replaced']);
            }
            if (\array_key_exists('provided', $installed['versions'][$packageName])) {
                $ranges = \array_merge($ranges, $installed['versions'][$packageName]['provided']);
            }
            return \implode(' || ', $ranges);
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['version'])) {
                return null;
            }
            return $installed['versions'][$packageName]['version'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getPrettyVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['pretty_version'])) {
                return null;
            }
            return $installed['versions'][$packageName]['pretty_version'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getReference($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }
            if (!isset($installed['versions'][$packageName]['reference'])) {
                return null;
            }
            return $installed['versions'][$packageName]['reference'];
        }
        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }
    public static function getRootPackage()
    {
        $installed = self::getInstalled();
        return $installed[0]['root'];
    }
    public static function getRawData()
    {
        @\trigger_error('getRawData only returns the first dataset loaded, which may not be what you expect. Use getAllRawData() instead which returns all datasets for all autoloaders present in the process.', \E_USER_DEPRECATED);
        return self::$installed;
    }
    public static function getAllRawData()
    {
        return self::getInstalled();
    }
    public static function reload($data)
    {
        self::$installed = $data;
        self::$installedByVendor = array();
    }
    private static function getInstalled()
    {
        if (null === self::$canGetVendors) {
            self::$canGetVendors = \method_exists('ConfigTransformer20210601\\Composer\\Autoload\\ClassLoader', 'getRegisteredLoaders');
        }
        $installed = array();
        if (self::$canGetVendors) {
            foreach (\ConfigTransformer20210601\Composer\Autoload\ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
                if (isset(self::$installedByVendor[$vendorDir])) {
                    $installed[] = self::$installedByVendor[$vendorDir];
                } elseif (\is_file($vendorDir . '/composer/installed.php')) {
                    $installed[] = self::$installedByVendor[$vendorDir] = (require $vendorDir . '/composer/installed.php');
                }
            }
        }
        $installed[] = self::$installed;
        return $installed;
    }
}
