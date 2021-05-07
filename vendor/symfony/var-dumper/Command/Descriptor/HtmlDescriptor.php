<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\VarDumper\Command\Descriptor;

use ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Data;
use ECSPrefix20210507\Symfony\Component\VarDumper\Dumper\HtmlDumper;
/**
 * Describe collected data clones for html output.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 *
 * @final
 */
class HtmlDescriptor implements \ECSPrefix20210507\Symfony\Component\VarDumper\Command\Descriptor\DumpDescriptorInterface
{
    private $dumper;
    private $initialized = \false;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Dumper\HtmlDumper $dumper
     */
    public function __construct($dumper)
    {
        $this->dumper = $dumper;
    }
    /**
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\Console\Output\OutputInterface $output
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Data $data
     * @param int $clientId
     */
    public function describe($output, $data, array $context, $clientId)
    {
        if (!$this->initialized) {
            $styles = \file_get_contents(__DIR__ . '/../../Resources/css/htmlDescriptor.css');
            $scripts = \file_get_contents(__DIR__ . '/../../Resources/js/htmlDescriptor.js');
            $output->writeln("<style>{$styles}</style><script>{$scripts}</script>");
            $this->initialized = \true;
        }
        $title = '-';
        if (isset($context['request'])) {
            $request = $context['request'];
            $controller = "<span class='dumped-tag'>{$this->dumper->dump($request['controller'], \true, ['maxDepth' => 0])}</span>";
            $title = \sprintf('<code>%s</code> <a href="%s">%s</a>', $request['method'], $uri = $request['uri'], $uri);
            $dedupIdentifier = $request['identifier'];
        } elseif (isset($context['cli'])) {
            $title = '<code>$ </code>' . $context['cli']['command_line'];
            $dedupIdentifier = $context['cli']['identifier'];
        } else {
            $dedupIdentifier = \uniqid('', \true);
        }
        $sourceDescription = '';
        if (isset($context['source'])) {
            $source = $context['source'];
            $projectDir = isset($source['project_dir']) ? $source['project_dir'] : null;
            $sourceDescription = \sprintf('%s on line %d', $source['name'], $source['line']);
            if (isset($source['file_link'])) {
                $sourceDescription = \sprintf('<a href="%s">%s</a>', $source['file_link'], $sourceDescription);
            }
        }
        $isoDate = $this->extractDate($context, 'c');
        $tags = \array_filter(['controller' => isset($controller) ? $controller : null, 'project dir' => isset($projectDir) ? $projectDir : null]);
        $output->writeln(<<<HTML
<article data-dedup-id="{$dedupIdentifier}">
    <header>
        <div class="row">
            <h2 class="col">{$title}</h2>
            <time class="col text-small" title="{$isoDate}" datetime="{$isoDate}">
                {$this->extractDate($context)}
            </time>
        </div>
        {$this->renderTags($tags)}
    </header>
    <section class="body">
        <p class="text-small">
            {$sourceDescription}
        </p>
        {$this->dumper->dump($data, \true)}
    </section>
</article>
HTML
);
    }
    /**
     * @param string $format
     * @return string
     */
    private function extractDate(array $context, $format = 'r')
    {
        return \date($format, $context['timestamp']);
    }
    /**
     * @return string
     */
    private function renderTags(array $tags)
    {
        if (!$tags) {
            return '';
        }
        $renderedTags = '';
        foreach ($tags as $key => $value) {
            $renderedTags .= \sprintf('<li><span class="badge">%s</span>%s</li>', $key, $value);
        }
        return <<<HTML
<div class="row">
    <ul class="tags">
        {$renderedTags}
    </ul>
</div>
HTML;
    }
}
