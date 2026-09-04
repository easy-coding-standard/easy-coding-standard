<?php

declare (strict_types=1);
namespace ECSPrefix202609\Symfony\Component\Form;

use ECSPrefix202609\Symfony\Component\OptionsResolver\OptionsResolver;
abstract class AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
    }
}
