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

use function count;
use function sprintf;
use function str_repeat;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\AbstractNode as Node;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
use ECSPrefix20210804\SebastianBergmann\Template\Template;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Directory extends \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Report\Html\Renderer
{
    public function render(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory $node, string $file) : void
    {
        $templateName = $this->templatePath . ($this->hasBranchCoverage ? 'directory_branch.html' : 'directory.html');
        $template = new \ECSPrefix20210804\SebastianBergmann\Template\Template($templateName, '{{', '}}');
        $this->setCommonTemplateVariables($template, $node);
        $items = $this->renderItem($node, \true);
        foreach ($node->directories() as $item) {
            $items .= $this->renderItem($item);
        }
        foreach ($node->files() as $item) {
            $items .= $this->renderItem($item);
        }
        $template->setVar(['id' => $node->id(), 'items' => $items]);
        $template->renderTo($file);
    }
    private function renderItem(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\AbstractNode $node, bool $total = \false) : string
    {
        $data = ['numClasses' => $node->numberOfClassesAndTraits(), 'numTestedClasses' => $node->numberOfTestedClassesAndTraits(), 'numMethods' => $node->numberOfFunctionsAndMethods(), 'numTestedMethods' => $node->numberOfTestedFunctionsAndMethods(), 'linesExecutedPercent' => $node->percentageOfExecutedLines()->asFloat(), 'linesExecutedPercentAsString' => $node->percentageOfExecutedLines()->asString(), 'numExecutedLines' => $node->numberOfExecutedLines(), 'numExecutableLines' => $node->numberOfExecutableLines(), 'branchesExecutedPercent' => $node->percentageOfExecutedBranches()->asFloat(), 'branchesExecutedPercentAsString' => $node->percentageOfExecutedBranches()->asString(), 'numExecutedBranches' => $node->numberOfExecutedBranches(), 'numExecutableBranches' => $node->numberOfExecutableBranches(), 'pathsExecutedPercent' => $node->percentageOfExecutedPaths()->asFloat(), 'pathsExecutedPercentAsString' => $node->percentageOfExecutedPaths()->asString(), 'numExecutedPaths' => $node->numberOfExecutedPaths(), 'numExecutablePaths' => $node->numberOfExecutablePaths(), 'testedMethodsPercent' => $node->percentageOfTestedFunctionsAndMethods()->asFloat(), 'testedMethodsPercentAsString' => $node->percentageOfTestedFunctionsAndMethods()->asString(), 'testedClassesPercent' => $node->percentageOfTestedClassesAndTraits()->asFloat(), 'testedClassesPercentAsString' => $node->percentageOfTestedClassesAndTraits()->asString()];
        if ($total) {
            $data['name'] = 'Total';
        } else {
            $up = \str_repeat('../', \count($node->pathAsArray()) - 2);
            $data['icon'] = \sprintf('<img src="%s_icons/file-code.svg" class="octicon" />', $up);
            if ($node instanceof \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory) {
                $data['name'] = \sprintf('<a href="%s/index.html">%s</a>', $node->name(), $node->name());
                $data['icon'] = \sprintf('<img src="%s_icons/file-directory.svg" class="octicon" />', $up);
            } elseif ($this->hasBranchCoverage) {
                $data['name'] = \sprintf('%s <a class="small" href="%s.html">[line]</a> <a class="small" href="%s_branch.html">[branch]</a> <a class="small" href="%s_path.html">[path]</a>', $node->name(), $node->name(), $node->name(), $node->name());
            } else {
                $data['name'] = \sprintf('<a href="%s.html">%s</a>', $node->name(), $node->name());
            }
        }
        $templateName = $this->templatePath . ($this->hasBranchCoverage ? 'directory_item_branch.html' : 'directory_item.html');
        return $this->renderItemTemplate(new \ECSPrefix20210804\SebastianBergmann\Template\Template($templateName, '{{', '}}'), $data);
    }
}
