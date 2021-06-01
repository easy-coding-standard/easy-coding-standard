<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Guard;

use ConfigTransformer20210601\Symplify\ConfigTransformer\Exception\InvalidConfigurationException;
final class InputValidator
{
    /**
     * @param string[] $allowedValues
     * @return void
     */
    public function validateFormatValue(string $formatValue, array $allowedValues, string $optionKey)
    {
        if ($formatValue === '') {
            $message = \sprintf('Add missing "--%s" option to command line', $optionKey);
            throw new \ConfigTransformer20210601\Symplify\ConfigTransformer\Exception\InvalidConfigurationException($message);
        }
        if (\in_array($formatValue, $allowedValues, \true)) {
            return;
        }
        $message = \sprintf('"--%s" format "%s" is not supported. Pick one of "%s"', $optionKey, $formatValue, \implode('", ', $allowedValues));
        throw new \ConfigTransformer20210601\Symplify\ConfigTransformer\Exception\InvalidConfigurationException($message);
    }
}
