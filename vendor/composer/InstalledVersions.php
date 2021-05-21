<?php

namespace ECSPrefix20210521\Composer;

use ECSPrefix20210521\Composer\Autoload\ClassLoader;
use ECSPrefix20210521\Composer\Semver\VersionParser;
class InstalledVersions
{
    private static $installed = array('root' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => NULL, 'name' => 'symplify/easy-coding-standard'), 'versions' => array('composer/semver' => array('pretty_version' => '3.2.4', 'version' => '3.2.4.0', 'aliases' => array(), 'reference' => 'a02fdf930a3c1c3ed3a49b5f63859c0c20e10464'), 'composer/xdebug-handler' => array('pretty_version' => '2.0.1', 'version' => '2.0.1.0', 'aliases' => array(), 'reference' => '964adcdd3a28bf9ed5d9ac6450064e0d71ed7496'), 'doctrine/annotations' => array('pretty_version' => '1.13.1', 'version' => '1.13.1.0', 'aliases' => array(), 'reference' => 'e6e7b7d5b45a2f2abc5460cc6396480b2b1d321f'), 'doctrine/lexer' => array('pretty_version' => '1.2.1', 'version' => '1.2.1.0', 'aliases' => array(), 'reference' => 'e864bbf5904cb8f5bb334f99209b48018522f042'), 'friendsofphp/php-cs-fixer' => array('pretty_version' => 'v3.0.0', 'version' => '3.0.0.0', 'aliases' => array(), 'reference' => 'c15377bdfa8d1ecf186f1deadec39c89984e1167'), 'nette/caching' => array('pretty_version' => 'v3.1.1', 'version' => '3.1.1.0', 'aliases' => array(), 'reference' => '3e771c589dee414724be473c24ad16dae50c1960'), 'nette/finder' => array('pretty_version' => 'v2.5.2', 'version' => '2.5.2.0', 'aliases' => array(), 'reference' => '4ad2c298eb8c687dd0e74ae84206a4186eeaed50'), 'nette/neon' => array('pretty_version' => 'v3.2.2', 'version' => '3.2.2.0', 'aliases' => array(), 'reference' => 'e4ca6f4669121ca6876b1d048c612480e39a28d5'), 'nette/utils' => array('pretty_version' => 'v3.2.2', 'version' => '3.2.2.0', 'aliases' => array(), 'reference' => '967cfc4f9a1acd5f1058d76715a424c53343c20c'), 'php-cs-fixer/diff' => array('pretty_version' => 'v2.0.2', 'version' => '2.0.2.0', 'aliases' => array(), 'reference' => '29dc0d507e838c4580d018bd8b5cb412474f7ec3'), 'psr/cache' => array('pretty_version' => '3.0.0', 'version' => '3.0.0.0', 'aliases' => array(), 'reference' => 'aa5030cfa5405eccfdcb1083ce040c2cb8d253bf'), 'psr/container' => array('pretty_version' => '1.1.1', 'version' => '1.1.1.0', 'aliases' => array(), 'reference' => '8622567409010282b7aeebe4bb841fe98b58dcaf'), 'psr/container-implementation' => array('provided' => array(0 => '1.0')), 'psr/event-dispatcher' => array('pretty_version' => '1.0.0', 'version' => '1.0.0.0', 'aliases' => array(), 'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0'), 'psr/event-dispatcher-implementation' => array('provided' => array(0 => '1.0')), 'psr/log' => array('pretty_version' => '1.1.4', 'version' => '1.1.4.0', 'aliases' => array(), 'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11'), 'psr/log-implementation' => array('provided' => array(0 => '1.0')), 'sebastian/diff' => array('pretty_version' => '4.0.4', 'version' => '4.0.4.0', 'aliases' => array(), 'reference' => '3461e3fccc7cfdfc2720be910d3bd73c69be590d'), 'squizlabs/php_codesniffer' => array('pretty_version' => '3.6.0', 'version' => '3.6.0.0', 'aliases' => array(), 'reference' => 'ffced0d2c8fa8e6cdc4d695a743271fab6c38625'), 'symfony/config' => array('pretty_version' => 'v5.2.8', 'version' => '5.2.8.0', 'aliases' => array(), 'reference' => '8dfa5f8adea9cd5155920069224beb04f11d6b7e'), 'symfony/console' => array('pretty_version' => 'v5.2.8', 'version' => '5.2.8.0', 'aliases' => array(), 'reference' => '864568fdc0208b3eba3638b6000b69d2386e6768'), 'symfony/dependency-injection' => array('pretty_version' => 'v5.2.9', 'version' => '5.2.9.0', 'aliases' => array(), 'reference' => '2761ca2f7e2f41af3a45951e1ce8c01f121245eb'), 'symfony/deprecation-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => '5f38c8804a9e97d23e0c8d63341088cd8a22d627'), 'symfony/error-handler' => array('pretty_version' => 'v5.2.8', 'version' => '5.2.8.0', 'aliases' => array(), 'reference' => '1416bc16317a8188aabde251afef7618bf4687ac'), 'symfony/event-dispatcher' => array('pretty_version' => 'v5.2.4', 'version' => '5.2.4.0', 'aliases' => array(), 'reference' => 'd08d6ec121a425897951900ab692b612a61d6240'), 'symfony/event-dispatcher-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => '69fee1ad2332a7cbab3aca13591953da9cdb7a11'), 'symfony/event-dispatcher-implementation' => array('provided' => array(0 => '2.0')), 'symfony/filesystem' => array('pretty_version' => 'v5.2.7', 'version' => '5.2.7.0', 'aliases' => array(), 'reference' => '056e92acc21d977c37e6ea8e97374b2a6c8551b0'), 'symfony/finder' => array('pretty_version' => 'v5.2.9', 'version' => '5.2.9.0', 'aliases' => array(), 'reference' => 'ccccb9d48ca42757dd12f2ca4bf857a4e217d90d'), 'symfony/http-client-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => '7e82f6084d7cae521a75ef2cb5c9457bbda785f4'), 'symfony/http-foundation' => array('pretty_version' => 'v5.2.8', 'version' => '5.2.8.0', 'aliases' => array(), 'reference' => 'e8fbbab7c4a71592985019477532629cb2e142dc'), 'symfony/http-kernel' => array('pretty_version' => 'v5.2.9', 'version' => '5.2.9.0', 'aliases' => array(), 'reference' => 'eb540ef6870dbf33c92e372cfb869ebf9649e6cb'), 'symfony/options-resolver' => array('pretty_version' => 'v5.2.4', 'version' => '5.2.4.0', 'aliases' => array(), 'reference' => '5d0f633f9bbfcf7ec642a2b5037268e61b0a62ce'), 'symfony/polyfill-ctype' => array('pretty_version' => 'v1.22.1', 'version' => '1.22.1.0', 'aliases' => array(), 'reference' => 'c6c942b1ac76c82448322025e084cadc56048b4e'), 'symfony/polyfill-intl-grapheme' => array('pretty_version' => 'v1.22.1', 'version' => '1.22.1.0', 'aliases' => array(), 'reference' => '5601e09b69f26c1828b13b6bb87cb07cddba3170'), 'symfony/polyfill-intl-normalizer' => array('pretty_version' => 'v1.22.1', 'version' => '1.22.1.0', 'aliases' => array(), 'reference' => '43a0283138253ed1d48d352ab6d0bdb3f809f248'), 'symfony/polyfill-mbstring' => array('pretty_version' => 'v1.22.1', 'version' => '1.22.1.0', 'aliases' => array(), 'reference' => '5232de97ee3b75b0360528dae24e73db49566ab1'), 'symfony/polyfill-php72' => array('pretty_version' => 'v1.22.1', 'version' => '1.22.1.0', 'aliases' => array(), 'reference' => 'cc6e6f9b39fe8075b3dabfbaf5b5f645ae1340c9'), 'symfony/polyfill-php73' => array('pretty_version' => 'v1.22.1', 'version' => '1.22.1.0', 'aliases' => array(), 'reference' => 'a678b42e92f86eca04b7fa4c0f6f19d097fb69e2'), 'symfony/polyfill-php80' => array('pretty_version' => 'v1.22.1', 'version' => '1.22.1.0', 'aliases' => array(), 'reference' => 'dc3063ba22c2a1fd2f45ed856374d79114998f91'), 'symfony/process' => array('pretty_version' => 'v5.2.7', 'version' => '5.2.7.0', 'aliases' => array(), 'reference' => '98cb8eeb72e55d4196dd1e36f1f16e7b3a9a088e'), 'symfony/service-contracts' => array('pretty_version' => 'v2.4.0', 'version' => '2.4.0.0', 'aliases' => array(), 'reference' => 'f040a30e04b57fbcc9c6cbcf4dbaa96bd318b9bb'), 'symfony/service-implementation' => array('provided' => array(0 => '1.0|2.0')), 'symfony/stopwatch' => array('pretty_version' => 'v5.2.7', 'version' => '5.2.7.0', 'aliases' => array(), 'reference' => 'd99310c33e833def36419c284f60e8027d359678'), 'symfony/string' => array('pretty_version' => 'v5.2.8', 'version' => '5.2.8.0', 'aliases' => array(), 'reference' => '01b35eb64cac8467c3f94cd0ce2d0d376bb7d1db'), 'symfony/var-dumper' => array('pretty_version' => 'v5.2.8', 'version' => '5.2.8.0', 'aliases' => array(), 'reference' => 'd693200a73fae179d27f8f1b16b4faf3e8569eba'), 'symplify/autowire-array-parameter' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '082531e1758f170dec639ec9cd5858f94bc208f6'), 'symplify/coding-standard' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '93bd0efc7dc3ec640c1d49b1bc34dec3df759ff6'), 'symplify/composer-json-manipulator' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'a58d9f73bb7f756b720428566761854a44d86641'), 'symplify/console-color-diff' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '1572b114d39499757fa2d7d46367fc41ba07e006'), 'symplify/console-package-builder' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '072420b8373cd28e617dbccd7abdef4e5a5a2871'), 'symplify/easy-coding-standard' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => NULL), 'symplify/easy-testing' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'd12b5b2772dc757b3b6141819fac9b71287095e4'), 'symplify/package-builder' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'be792b98451e1d6098dc76fcbcc64a664b597239'), 'symplify/rule-doc-generator-contracts' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'b661f9642938eb64d076c4eff25ad4ffc439ef8c'), 'symplify/skipper' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '5db8993e3167f28b60516b17de50c937df17ba75'), 'symplify/smart-file-system' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => '2dea618353e3da36cb4244b28f0ca41387d764f2'), 'symplify/symplify-kernel' => array('pretty_version' => 'dev-main', 'version' => 'dev-main', 'aliases' => array(0 => '9.4.x-dev'), 'reference' => 'aa262eae2070f2349bdcf1690f88f2a93c56bf5c')));
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
    public static function satisfies(\ECSPrefix20210521\Composer\Semver\VersionParser $parser, $packageName, $constraint)
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
        return self::$installed;
    }
    public static function reload($data)
    {
        self::$installed = $data;
        self::$installedByVendor = array();
    }
    private static function getInstalled()
    {
        if (null === self::$canGetVendors) {
            self::$canGetVendors = \method_exists('ECSPrefix20210521\\Composer\\Autoload\\ClassLoader', 'getRegisteredLoaders');
        }
        $installed = array();
        if (self::$canGetVendors) {
            foreach (\ECSPrefix20210521\Composer\Autoload\ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
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
