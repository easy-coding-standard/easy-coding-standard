<?php

class Foo
{
    public function getDefinition(): string
    {
        return <<<'CODE'
class Definition
{
    public function getResult(): array
    {
        return array('test' => 'data');
    }
}
CODE;
    }
}
-----
<?php

class Foo
{
    public function getDefinition(): string
    {
        return <<<'CODE'
class Definition
{
    public function getResult(): array
    {
        return ['test' => 'data'];
    }
}
CODE;
    }
}
