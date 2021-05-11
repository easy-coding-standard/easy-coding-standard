<?php

namespace Symplify\ComposerJsonManipulator\ValueObject;

/**
 * @api
 */
final class ComposerJsonSection
{
    /**
     * @var string
     */
    const REPOSITORIES = 'repositories';
    /**
     * @var string
     */
    const REQUIRE_DEV = 'require-dev';
    /**
     * @var string
     */
    const REQUIRE = 'require';
    /**
     * @var string
     */
    const CONFLICT = 'conflict';
    /**
     * @var string
     */
    const PREFER_STABLE = 'prefer-stable';
    /**
     * @var string
     */
    const MINIMUM_STABILITY = 'minimum-stability';
    /**
     * @var string
     */
    const AUTOLOAD = 'autoload';
    /**
     * @var string
     */
    const AUTOLOAD_DEV = 'autoload-dev';
    /**
     * @var string
     */
    const REPLACE = 'replace';
    /**
     * @var string
     */
    const CONFIG = 'config';
    /**
     * @var string
     */
    const EXTRA = 'extra';
    /**
     * @var string
     */
    const NAME = 'name';
    /**
     * @var string
     */
    const DESCRIPTION = 'description';
    /**
     * @var string
     */
    const KEYWORDS = 'keywords';
    /**
     * @var string
     */
    const HOMEPAGE = 'homepage';
    /**
     * @var string
     */
    const LICENSE = 'license';
    /**
     * @var string
     */
    const SCRIPTS = 'scripts';
    /**
     * @var string
     */
    const BIN = 'bin';
    /**
     * @var string
     */
    const TYPE = 'type';
    /**
     * @var string
     */
    const AUTHORS = 'authors';
    /**
     * @var string
     * @see https://getcomposer.org/doc/articles/scripts.md#custom-descriptions-
     */
    const SCRIPTS_DESCRIPTIONS = 'scripts-descriptions';
    /**
     * @var string
     */
    const PROVIDES = 'provides';
    /**
     * @var string
     */
    const SUGGESTS = 'suggests';
    /**
     * @var string
     */
    const VERSION = 'version';
}
