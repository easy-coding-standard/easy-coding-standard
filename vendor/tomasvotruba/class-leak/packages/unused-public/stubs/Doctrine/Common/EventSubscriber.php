<?php

declare (strict_types=1);
namespace ECSPrefix202609\Doctrine\Common;

interface EventSubscriber
{
    /**
     * @return string[]
     */
    public function getSubscribedEvents();
}
