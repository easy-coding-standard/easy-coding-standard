<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application;

use DateTime;
use ECSPrefix20210608\Symfony\Component\Process\Process;
use Symplify\EasyCodingStandard\Exception\VersionException;
use ECSPrefix20210608\Symplify\PackageBuilder\Console\ShellCode;
/**
 * Inspired by https://github.com/composer/composer/blob/master/src/Composer/Composer.php See
 * https://github.com/composer/composer/blob/6587715d0f8cae0cd39073b3bc5f018d0e6b84fe/src/Composer/Compiler.php#L208
 */
final class VersionResolver
{
    /**
     * @var string
     */
    const PACKAGE_VERSION = '538a5c302444477a7aa2aceb00bb9b8ae3f9f5a4';
    /**
     * @var string
     */
    const RELEASE_DATE = '2021-06-08 16:41:35';
    public static function resolvePackageVersion() : string
    {
        $process = new \ECSPrefix20210608\Symfony\Component\Process\Process(['git', 'log', '--pretty="%H"', '-n1', 'HEAD'], __DIR__);
        if ($process->run() !== \ECSPrefix20210608\Symplify\PackageBuilder\Console\ShellCode::SUCCESS) {
            throw new \Symplify\EasyCodingStandard\Exception\VersionException('You must ensure to run compile from composer git repository clone and that git binary is available.');
        }
        $version = \trim($process->getOutput());
        return \trim($version, '"');
    }
    public static function resolverReleaseDateTime() : \DateTime
    {
        $process = new \ECSPrefix20210608\Symfony\Component\Process\Process(['git', 'log', '-n1', '--pretty=%ci', 'HEAD'], __DIR__);
        if ($process->run() !== \ECSPrefix20210608\Symplify\PackageBuilder\Console\ShellCode::SUCCESS) {
            throw new \Symplify\EasyCodingStandard\Exception\VersionException('You must ensure to run compile from composer git repository clone and that git binary is available.');
        }
        return new \DateTime(\trim($process->getOutput()));
    }
}
