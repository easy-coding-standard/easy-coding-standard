<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Application\Version;

use DateTime;
use ECSPrefix20211230\Symfony\Component\Console\Command\Command;
use ECSPrefix20211230\Symfony\Component\Process\Process;
use Symplify\EasyCodingStandard\Exception\VersionException;
/**
 * Inspired by https://github.com/composer/composer/blob/master/src/Composer/Composer.php See
 * https://github.com/composer/composer/blob/6587715d0f8cae0cd39073b3bc5f018d0e6b84fe/src/Composer/Compiler.php#L208
 */
final class StaticVersionResolver
{
    /**
     * @var string
     */
    public const PACKAGE_VERSION = '0a15564fe9fadd956ec1d052c8f791df8ae5fbbf';
    /**
     * @var string
     */
    public const RELEASE_DATE = '2021-12-30 11:37:11';
    public static function resolvePackageVersion() : string
    {
        $process = new \ECSPrefix20211230\Symfony\Component\Process\Process(['git', 'log', '--pretty="%H"', '-n1', 'HEAD'], __DIR__);
        if ($process->run() !== \ECSPrefix20211230\Symfony\Component\Console\Command\Command::SUCCESS) {
            throw new \Symplify\EasyCodingStandard\Exception\VersionException('You must ensure to run compile from composer git repository clone and that git binary is available.');
        }
        $version = \trim($process->getOutput());
        return \trim($version, '"');
    }
    public static function resolverReleaseDateTime() : \DateTime
    {
        $process = new \ECSPrefix20211230\Symfony\Component\Process\Process(['git', 'log', '-n1', '--pretty=%ci', 'HEAD'], __DIR__);
        if ($process->run() !== \ECSPrefix20211230\Symfony\Component\Console\Command\Command::SUCCESS) {
            throw new \Symplify\EasyCodingStandard\Exception\VersionException('You must ensure to run compile from composer git repository clone and that git binary is available.');
        }
        return new \DateTime(\trim($process->getOutput()));
    }
}
