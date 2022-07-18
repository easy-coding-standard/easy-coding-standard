<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application\Version;

use DateTime;
use Symplify\EasyCodingStandard\Exception\VersionException;
/**
 * Inspired by https://github.com/composer/composer/blob/master/src/Composer/Composer.php See
 * https://github.com/composer/composer/blob/6587715d0f8cae0cd39073b3bc5f018d0e6b84fe/src/Composer/Compiler.php#L208
 */
final class StaticVersionResolver
{
    /**
     * @api
     * @var string
     */
    public const PACKAGE_VERSION = '534bb6a8dd5c33db0a97fa4c382e0472f2354911';
    /**
     * @api
     * @var string
     */
    public const RELEASE_DATE = '2022-07-18 23:18:08';
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
