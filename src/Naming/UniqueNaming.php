<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\Naming;

final class UniqueNaming
{
    /**
     * @var array<string, int>
     */
    private $existingNames = [];
    public function uniquateName(string $name) : string
    {
        if (isset($this->existingNames[$name])) {
            $serviceNameCounter = $this->existingNames[$name];
            $this->existingNames[$name] = ++$serviceNameCounter;
            return $name . '.' . $serviceNameCounter;
        }
        $this->existingNames[$name] = 1;
        return $name;
    }
}
