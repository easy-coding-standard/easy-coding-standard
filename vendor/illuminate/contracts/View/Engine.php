<?php

namespace ECSPrefix202405\Illuminate\Contracts\View;

interface Engine
{
    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array  $data
     * @return string
     */
    public function get($path, array $data = []);
}
