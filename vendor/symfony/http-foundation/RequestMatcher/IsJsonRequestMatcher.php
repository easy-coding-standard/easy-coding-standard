<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202302\Symfony\Component\HttpFoundation\RequestMatcher;

use ECSPrefix202302\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202302\Symfony\Component\HttpFoundation\RequestMatcherInterface;
/**
 * Checks the Request content is valid JSON.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class IsJsonRequestMatcher implements RequestMatcherInterface
{
    public function matches(Request $request) : bool
    {
        try {
            \json_decode($request->getContent(), \true, 512, \JSON_BIGINT_AS_STRING);
        } catch (\JsonException $exception) {
            return \false;
        }
        return \true;
    }
}
