<?php

namespace Symplify\SetConfigResolver\Console;

use Symfony\Component\Console\Input\InputInterface;

final class OptionValueResolver
{
    /**
     * @param string[] $optionNames
     * @return string|null
     */
    public function getOptionValue(InputInterface $input, array $optionNames)
    {
        foreach ($optionNames as $optionName) {
            if ($input->hasParameterOption($optionName, true)) {
                return $input->getParameterOption($optionName, null, true);
            }
        }

        return null;
    }
}
