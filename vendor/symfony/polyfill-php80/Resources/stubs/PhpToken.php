<?php



if (\PHP_VERSION_ID < 80000 && \extension_loaded('tokenizer')) {
    class PhpToken extends \ECSPrefix20220604\Symfony\Polyfill\Php80\PhpToken
    {
    }
}
