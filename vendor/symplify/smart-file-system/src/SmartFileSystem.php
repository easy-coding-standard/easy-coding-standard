<?php

namespace Symplify\SmartFileSystem;

use ECSPrefix20210510\Nette\Utils\Strings;
use ECSPrefix20210510\Symfony\Component\Filesystem\Exception\IOException;
use ECSPrefix20210510\Symfony\Component\Filesystem\Filesystem;
/**
 * @see \Symplify\SmartFileSystem\Tests\SmartFileSystem\SmartFileSystemTest
 */
final class SmartFileSystem extends \ECSPrefix20210510\Symfony\Component\Filesystem\Filesystem
{
    /**
     * @var string
     * @see https://regex101.com/r/tx6eyw/1
     */
    const BEFORE_COLLON_REGEX = '#^\\w+\\(.*?\\): #';
    /**
     * @see https://github.com/symfony/filesystem/pull/4/files
     * @param string $filename
     * @return string
     */
    public function readFile($filename)
    {
        $filename = (string) $filename;
        $source = @\file_get_contents($filename);
        if (!$source) {
            $message = \sprintf('Failed to read "%s" file: "%s"', $filename, $this->getLastError());
            throw new \ECSPrefix20210510\Symfony\Component\Filesystem\Exception\IOException($message, 0, null, $filename);
        }
        return $source;
    }
    /**
     * @param string $filename
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    public function readFileToSmartFileInfo($filename)
    {
        $filename = (string) $filename;
        return new \Symplify\SmartFileSystem\SmartFileInfo($filename);
    }
    /**
     * Converts given HTML code to plain text
     *
     * @source https://github.com/nette/utils/blob/e7bd59f1dd860d25dbbb1ac720dddd0fa1388f4c/src/Utils/Html.php#L325-L331
     * @param string $html
     * @return string
     */
    public function htmlToText($html)
    {
        $html = (string) $html;
        $content = \strip_tags($html);
        return \html_entity_decode($content, \ENT_QUOTES | \ENT_HTML5, 'UTF-8');
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return mixed[]
     */
    public function resolveFilePathsFromFileInfos(array $fileInfos)
    {
        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRelativeFilePathFromCwd();
        }
        return $filePaths;
    }
    /**
     * Returns the last PHP error as plain string.
     *
     * @source https://github.com/nette/utils/blob/ab8eea12b8aacc7ea5bdafa49b711c2988447994/src/Utils/Helpers.php#L31-L40
     * @return string
     */
    private function getLastError()
    {
        $message = isset(\error_get_last()['message']) ? \error_get_last()['message'] : '';
        $message = \ini_get('html_errors') ? $this->htmlToText($message) : $message;
        return \ECSPrefix20210510\Nette\Utils\Strings::replace($message, self::BEFORE_COLLON_REGEX, '');
    }
}
