<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation\RequestMatcher;

use ECSPrefix202306\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202306\Symfony\Component\HttpFoundation\RequestMatcherInterface;
/**
 * Checks the Request URL path info matches a regular expression.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class PathRequestMatcher implements RequestMatcherInterface
{
    /**
     * @var string
     */
    private $regexp;
    public function __construct(string $regexp)
    {
        $this->regexp = $regexp;
    }
    public function matches(Request $request) : bool
    {
        return \preg_match('{' . $this->regexp . '}', \rawurldecode($request->getPathInfo()));
    }
}
