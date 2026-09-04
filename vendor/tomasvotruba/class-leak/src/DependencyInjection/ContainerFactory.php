<?php

declare (strict_types=1);
namespace ECSPrefix202609\TomasVotruba\ClassLeak\DependencyInjection;

use ECSPrefix202609\Entropy\Container\Container;
use ECSPrefix202609\PhpParser\Parser;
use ECSPrefix202609\PhpParser\ParserFactory;
/**
 * @api
 */
final class ContainerFactory
{
    /**
     * @api
     */
    public function create(): Container
    {
        $container = new Container();
        $container->autodiscover(__DIR__ . '/..');
        $container->service(Parser::class, static function (): Parser {
            $parserFactory = new ParserFactory();
            return $parserFactory->createForHostVersion();
        });
        return $container;
    }
}
