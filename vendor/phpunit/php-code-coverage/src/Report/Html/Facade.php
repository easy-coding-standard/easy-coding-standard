<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Html;

use const DIRECTORY_SEPARATOR;
use function copy;
use function date;
use function dirname;
use function substr;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\CodeCoverage;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Directory as DirectoryUtil;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
final class Facade
{
    /**
     * @var string
     */
    private $templatePath;
    /**
     * @var string
     */
    private $generator;
    /**
     * @var int
     */
    private $lowUpperBound;
    /**
     * @var int
     */
    private $highLowerBound;
    public function __construct(int $lowUpperBound = 50, int $highLowerBound = 90, string $generator = '')
    {
        $this->generator = $generator;
        $this->highLowerBound = $highLowerBound;
        $this->lowUpperBound = $lowUpperBound;
        $this->templatePath = __DIR__ . '/Renderer/Template/';
    }
    public function process(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\CodeCoverage $coverage, string $target) : void
    {
        $target = $this->directory($target);
        $report = $coverage->getReport();
        $date = \date('D M j G:i:s T Y');
        $dashboard = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Html\Dashboard($this->templatePath, $this->generator, $date, $this->lowUpperBound, $this->highLowerBound, $coverage->collectsBranchAndPathCoverage());
        $directory = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Html\Directory($this->templatePath, $this->generator, $date, $this->lowUpperBound, $this->highLowerBound, $coverage->collectsBranchAndPathCoverage());
        $file = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Html\File($this->templatePath, $this->generator, $date, $this->lowUpperBound, $this->highLowerBound, $coverage->collectsBranchAndPathCoverage());
        $directory->render($report, $target . 'index.html');
        $dashboard->render($report, $target . 'dashboard.html');
        foreach ($report as $node) {
            $id = $node->id();
            if ($node instanceof \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory) {
                \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Directory::create($target . $id);
                $directory->render($node, $target . $id . '/index.html');
                $dashboard->render($node, $target . $id . '/dashboard.html');
            } else {
                $dir = \dirname($target . $id);
                \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Directory::create($dir);
                $file->render($node, $target . $id);
            }
        }
        $this->copyFiles($target);
    }
    private function copyFiles(string $target) : void
    {
        $dir = $this->directory($target . '_css');
        \copy($this->templatePath . 'css/bootstrap.min.css', $dir . 'bootstrap.min.css');
        \copy($this->templatePath . 'css/nv.d3.min.css', $dir . 'nv.d3.min.css');
        \copy($this->templatePath . 'css/style.css', $dir . 'style.css');
        \copy($this->templatePath . 'css/custom.css', $dir . 'custom.css');
        \copy($this->templatePath . 'css/octicons.css', $dir . 'octicons.css');
        $dir = $this->directory($target . '_icons');
        \copy($this->templatePath . 'icons/file-code.svg', $dir . 'file-code.svg');
        \copy($this->templatePath . 'icons/file-directory.svg', $dir . 'file-directory.svg');
        $dir = $this->directory($target . '_js');
        \copy($this->templatePath . 'js/bootstrap.min.js', $dir . 'bootstrap.min.js');
        \copy($this->templatePath . 'js/popper.min.js', $dir . 'popper.min.js');
        \copy($this->templatePath . 'js/d3.min.js', $dir . 'd3.min.js');
        \copy($this->templatePath . 'js/jquery.min.js', $dir . 'jquery.min.js');
        \copy($this->templatePath . 'js/nv.d3.min.js', $dir . 'nv.d3.min.js');
        \copy($this->templatePath . 'js/file.js', $dir . 'file.js');
    }
    private function directory(string $directory) : string
    {
        if (\substr($directory, -1, 1) != \DIRECTORY_SEPARATOR) {
            $directory .= \DIRECTORY_SEPARATOR;
        }
        \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Directory::create($directory);
        return $directory;
    }
}
