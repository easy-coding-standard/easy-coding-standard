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

use ECSPrefix202306\Symfony\Component\ExpressionLanguage\Expression;
use ECSPrefix202306\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202306\Symfony\Component\HttpFoundation\RequestMatcherInterface;
/**
 * ExpressionRequestMatcher uses an expression to match a Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExpressionRequestMatcher implements RequestMatcherInterface
{
    /**
     * @var \Symfony\Component\ExpressionLanguage\ExpressionLanguage
     */
    private $language;
    /**
     * @var \Symfony\Component\ExpressionLanguage\Expression|string
     */
    private $expression;
    /**
     * @param \Symfony\Component\ExpressionLanguage\Expression|string $expression
     */
    public function __construct(ExpressionLanguage $language, $expression)
    {
        $this->language = $language;
        $this->expression = $expression;
    }
    public function matches(Request $request) : bool
    {
        return $this->language->evaluate($this->expression, ['request' => $request, 'method' => $request->getMethod(), 'path' => \rawurldecode($request->getPathInfo()), 'host' => $request->getHost(), 'ip' => $request->getClientIp(), 'attributes' => $request->attributes->all()]);
    }
}
