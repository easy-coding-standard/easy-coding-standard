<?php

namespace ECSPrefix202408\Illuminate\Contracts\Foundation;

interface ExceptionRenderer
{
    /**
     * Renders the given exception as HTML.
     *
     * @param  \Throwable  $throwable
     * @return string
     */
    public function render($throwable);
}
