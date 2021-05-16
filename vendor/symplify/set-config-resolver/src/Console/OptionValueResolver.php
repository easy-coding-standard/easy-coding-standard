<?php

namespace ECSPrefix20210516\Symplify\SetConfigResolver\Console;

use ECSPrefix20210516\Symfony\Component\Console\Input\InputInterface;
final class OptionValueResolver
{
    /**
     * @param string[] $optionNames
     * @return string|null
     */
    public function getOptionValue(\ECSPrefix20210516\Symfony\Component\Console\Input\InputInterface $input, array $optionNames)
    {
        foreach ($optionNames as $optionName) {
            if ($input->hasParameterOption($optionName, \true)) {
                return $input->getParameterOption($optionName, null, \true);
            }
        }
        return null;
    }
}
