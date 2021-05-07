<?php

namespace Symplify\SetConfigResolver\Console;

use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
final class OptionValueResolver
{
    /**
     * @param string[] $optionNames
     * @return string|null
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     */
    public function getOptionValue($input, array $optionNames)
    {
        foreach ($optionNames as $optionName) {
            if ($input->hasParameterOption($optionName, \true)) {
                return $input->getParameterOption($optionName, null, \true);
            }
        }
        return null;
    }
}
