<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Runner;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\DirectoryInterface;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\FileReader;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFileProcessedEvent;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220220\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ECSPrefix20220220\Symfony\Component\Filesystem\Exception\IOException;
use ECSPrefix20220220\Symfony\Contracts\EventDispatcher\Event;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class Runner
{
    /**
     * @var DifferInterface
     */
    private $differ;
    /**
     * @var DirectoryInterface
     */
    private $directory;
    /**
     * @var null|EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var ErrorsManager
     */
    private $errorsManager;
    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;
    /**
     * @var bool
     */
    private $isDryRun;
    /**
     * @var LinterInterface
     */
    private $linter;
    /**
     * @var \Traversable
     */
    private $finder;
    /**
     * @var FixerInterface[]
     */
    private $fixers;
    /**
     * @var bool
     */
    private $stopOnViolation;
    public function __construct($finder, array $fixers, \PhpCsFixer\Differ\DifferInterface $differ, ?\ECSPrefix20220220\Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher, \PhpCsFixer\Error\ErrorsManager $errorsManager, \PhpCsFixer\Linter\LinterInterface $linter, bool $isDryRun, \PhpCsFixer\Cache\CacheManagerInterface $cacheManager, ?\PhpCsFixer\Cache\DirectoryInterface $directory = null, bool $stopOnViolation = \false)
    {
        $this->finder = $finder;
        $this->fixers = $fixers;
        $this->differ = $differ;
        $this->eventDispatcher = $eventDispatcher;
        $this->errorsManager = $errorsManager;
        $this->linter = $linter;
        $this->isDryRun = $isDryRun;
        $this->cacheManager = $cacheManager;
        $this->directory = $directory ?: new \PhpCsFixer\Cache\Directory('');
        $this->stopOnViolation = $stopOnViolation;
    }
    public function fix() : array
    {
        $changed = [];
        $finder = $this->finder;
        $finderIterator = $finder instanceof \IteratorAggregate ? $finder->getIterator() : $finder;
        $fileFilteredFileIterator = new \PhpCsFixer\Runner\FileFilterIterator($finderIterator, $this->eventDispatcher, $this->cacheManager);
        $collection = $this->linter->isAsync() ? new \PhpCsFixer\Runner\FileCachingLintingIterator($fileFilteredFileIterator, $this->linter) : new \PhpCsFixer\Runner\FileLintingIterator($fileFilteredFileIterator, $this->linter);
        /** @var \SplFileInfo $file */
        foreach ($collection as $file) {
            $fixInfo = $this->fixFile($file, $collection->currentLintingResult());
            // we do not need Tokens to still caching just fixed file - so clear the cache
            \PhpCsFixer\Tokenizer\Tokens::clearCache();
            if (null !== $fixInfo) {
                $name = $this->directory->getRelativePathTo($file->__toString());
                $changed[$name] = $fixInfo;
                if ($this->stopOnViolation) {
                    break;
                }
            }
        }
        return $changed;
    }
    private function fixFile(\SplFileInfo $file, \PhpCsFixer\Linter\LintingResultInterface $lintingResult) : ?array
    {
        $name = $file->getPathname();
        try {
            $lintingResult->check();
        } catch (\PhpCsFixer\Linter\LintingException $e) {
            $this->dispatchEvent(\PhpCsFixer\FixerFileProcessedEvent::NAME, new \PhpCsFixer\FixerFileProcessedEvent(\PhpCsFixer\FixerFileProcessedEvent::STATUS_INVALID));
            $this->errorsManager->report(new \PhpCsFixer\Error\Error(\PhpCsFixer\Error\Error::TYPE_INVALID, $name, $e));
            return null;
        }
        $old = \PhpCsFixer\FileReader::createSingleton()->read($file->getRealPath());
        $tokens = \PhpCsFixer\Tokenizer\Tokens::fromCode($old);
        $oldHash = $tokens->getCodeHash();
        $newHash = $oldHash;
        $new = $old;
        $appliedFixers = [];
        try {
            foreach ($this->fixers as $fixer) {
                // for custom fixers we don't know is it safe to run `->fix()` without checking `->supports()` and `->isCandidate()`,
                // thus we need to check it and conditionally skip fixing
                if (!$fixer instanceof \PhpCsFixer\AbstractFixer && (!$fixer->supports($file) || !$fixer->isCandidate($tokens))) {
                    continue;
                }
                $fixer->fix($file, $tokens);
                if ($tokens->isChanged()) {
                    $tokens->clearEmptyTokens();
                    $tokens->clearChanged();
                    $appliedFixers[] = $fixer->getName();
                }
            }
        } catch (\ParseError $e) {
            $this->dispatchEvent(\PhpCsFixer\FixerFileProcessedEvent::NAME, new \PhpCsFixer\FixerFileProcessedEvent(\PhpCsFixer\FixerFileProcessedEvent::STATUS_LINT));
            $this->errorsManager->report(new \PhpCsFixer\Error\Error(\PhpCsFixer\Error\Error::TYPE_LINT, $name, $e));
            return null;
        } catch (\Throwable $e) {
            $this->processException($name, $e);
            return null;
        }
        $fixInfo = null;
        if (!empty($appliedFixers)) {
            $new = $tokens->generateCode();
            $newHash = $tokens->getCodeHash();
        }
        // We need to check if content was changed and then applied changes.
        // But we can't simply check $appliedFixers, because one fixer may revert
        // work of other and both of them will mark collection as changed.
        // Therefore we need to check if code hashes changed.
        if ($oldHash !== $newHash) {
            $fixInfo = ['appliedFixers' => $appliedFixers, 'diff' => $this->differ->diff($old, $new, $file)];
            try {
                $this->linter->lintSource($new)->check();
            } catch (\PhpCsFixer\Linter\LintingException $e) {
                $this->dispatchEvent(\PhpCsFixer\FixerFileProcessedEvent::NAME, new \PhpCsFixer\FixerFileProcessedEvent(\PhpCsFixer\FixerFileProcessedEvent::STATUS_LINT));
                $this->errorsManager->report(new \PhpCsFixer\Error\Error(\PhpCsFixer\Error\Error::TYPE_LINT, $name, $e, $fixInfo['appliedFixers'], $fixInfo['diff']));
                return null;
            }
            if (!$this->isDryRun) {
                $fileName = $file->getRealPath();
                if (!\file_exists($fileName)) {
                    throw new \ECSPrefix20220220\Symfony\Component\Filesystem\Exception\IOException(\sprintf('Failed to write file "%s" (no longer) exists.', $file->getPathname()), 0, null, $file->getPathname());
                }
                if (\is_dir($fileName)) {
                    throw new \ECSPrefix20220220\Symfony\Component\Filesystem\Exception\IOException(\sprintf('Cannot write file "%s" as the location exists as directory.', $fileName), 0, null, $fileName);
                }
                if (!\is_writable($fileName)) {
                    throw new \ECSPrefix20220220\Symfony\Component\Filesystem\Exception\IOException(\sprintf('Cannot write to file "%s" as it is not writable.', $fileName), 0, null, $fileName);
                }
                if (\false === @\file_put_contents($fileName, $new)) {
                    $error = \error_get_last();
                    throw new \ECSPrefix20220220\Symfony\Component\Filesystem\Exception\IOException(\sprintf('Failed to write file "%s", "%s".', $fileName, $error ? $error['message'] : 'no reason available'), 0, null, $fileName);
                }
            }
        }
        $this->cacheManager->setFile($name, $new);
        $this->dispatchEvent(\PhpCsFixer\FixerFileProcessedEvent::NAME, new \PhpCsFixer\FixerFileProcessedEvent($fixInfo ? \PhpCsFixer\FixerFileProcessedEvent::STATUS_FIXED : \PhpCsFixer\FixerFileProcessedEvent::STATUS_NO_CHANGES));
        return $fixInfo;
    }
    /**
     * Process an exception that occurred.
     */
    private function processException(string $name, \Throwable $e) : void
    {
        $this->dispatchEvent(\PhpCsFixer\FixerFileProcessedEvent::NAME, new \PhpCsFixer\FixerFileProcessedEvent(\PhpCsFixer\FixerFileProcessedEvent::STATUS_EXCEPTION));
        $this->errorsManager->report(new \PhpCsFixer\Error\Error(\PhpCsFixer\Error\Error::TYPE_EXCEPTION, $name, $e));
    }
    private function dispatchEvent(string $name, \ECSPrefix20220220\Symfony\Contracts\EventDispatcher\Event $event) : void
    {
        if (null === $this->eventDispatcher) {
            return;
        }
        $this->eventDispatcher->dispatch($event, $name);
    }
}
