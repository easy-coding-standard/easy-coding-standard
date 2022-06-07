<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Application\Version;

use DateTime;
use ECSPrefix20220607\Symplify\EasyCodingStandard\Exception\VersionException;
/**
 * Inspired by https://github.com/composer/composer/blob/master/src/Composer/Composer.php See
 * https://github.com/composer/composer/blob/6587715d0f8cae0cd39073b3bc5f018d0e6b84fe/src/Composer/Compiler.php#L208
 */
final class StaticVersionResolver
{
    /**
     * @var string
     */
    public const PACKAGE_VERSION = '5641af2cafea1809f3c69f30e054a4fae0d6b96c';
    /**
     * @var string
     */
    public const RELEASE_DATE = '2022-06-07 11:37:29';
    /**
     * @var int
     */
    private const SUCCESS_CODE = 0;
    public static function resolvePackageVersion() : string
    {
        // resolve current tag
        \exec('git tag --points-at', $tagExecOutput, $tagExecResultCode);
        if ($tagExecResultCode !== self::SUCCESS_CODE) {
            throw new VersionException('Ensure to run compile from composer git repository clone and that git binary is available.');
        }
        if ($tagExecOutput !== []) {
            $tag = $tagExecOutput[0];
            if ($tag !== '') {
                return $tag;
            }
        }
        \exec('git log --pretty="%H" -n1 HEAD', $commitHashExecOutput, $commitHashResultCode);
        if ($commitHashResultCode !== self::SUCCESS_CODE) {
            throw new VersionException('Ensure to run compile from composer git repository clone and that git binary is available.');
        }
        $version = \trim($commitHashExecOutput[0]);
        return \trim($version, '"');
    }
    public static function resolverReleaseDateTime() : DateTime
    {
        \exec('git log -n1 --pretty=%ci HEAD', $output, $resultCode);
        if ($resultCode !== self::SUCCESS_CODE) {
            throw new VersionException('Ensure to run compile from composer git repository clone and that git binary is available.');
        }
        return new DateTime(\trim($output[0]));
    }
}
