<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Parser;

use ConfigTransformer20210601\PhpParser\Error;
use ConfigTransformer20210601\PhpParser\ErrorHandler;
use ConfigTransformer20210601\PhpParser\Parser;
class Multiple implements \ConfigTransformer20210601\PhpParser\Parser
{
    /** @var Parser[] List of parsers to try, in order of preference */
    private $parsers;
    /**
     * Create a parser which will try multiple parsers in an order of preference.
     *
     * Parsers will be invoked in the order they're provided to the constructor. If one of the
     * parsers runs without throwing, it's output is returned. Otherwise the exception that the
     * first parser generated is thrown.
     *
     * @param Parser[] $parsers
     */
    public function __construct(array $parsers)
    {
        $this->parsers = $parsers;
    }
    public function parse(string $code, \ConfigTransformer20210601\PhpParser\ErrorHandler $errorHandler = null)
    {
        if (null === $errorHandler) {
            $errorHandler = new \ConfigTransformer20210601\PhpParser\ErrorHandler\Throwing();
        }
        list($firstStmts, $firstError) = $this->tryParse($this->parsers[0], $errorHandler, $code);
        if ($firstError === null) {
            return $firstStmts;
        }
        for ($i = 1, $c = \count($this->parsers); $i < $c; ++$i) {
            list($stmts, $error) = $this->tryParse($this->parsers[$i], $errorHandler, $code);
            if ($error === null) {
                return $stmts;
            }
        }
        throw $firstError;
    }
    private function tryParse(\ConfigTransformer20210601\PhpParser\Parser $parser, \ConfigTransformer20210601\PhpParser\ErrorHandler $errorHandler, $code)
    {
        $stmts = null;
        $error = null;
        try {
            $stmts = $parser->parse($code, $errorHandler);
        } catch (\ConfigTransformer20210601\PhpParser\Error $error) {
        }
        return [$stmts, $error];
    }
}
