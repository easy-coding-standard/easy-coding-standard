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
    public const PACKAGE_VERSION = 'd255b723441b1465f2ceb114d68c7b6dd1546fc7';
    /**
     * @api
     * @var string
     */
    public const RELEASE_DATE = '2022-08-08 20:10:18';
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
