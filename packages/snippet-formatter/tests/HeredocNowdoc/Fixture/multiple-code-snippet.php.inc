<?php

class Foo
{
    public function getDefinition(): string
    {
        return <<<PHP
class Definition
{
    public function getResult(): array
    {
        return array('test' => 'data');
    }
}
PHP;
    }
}

class Bar
{
    public function getDefinition(): string
    {
        return <<<PHP
class Definition
{
    public function getResult(): array
    {
        return array('test' => 'data');
    }
}
PHP;
    }
}
-----
<?php

class Foo
{
    public function getDefinition(): string
    {
        return <<<PHP
class Definition
{
    public function getResult(): array
    {
        return ['test' => 'data'];
    }
}
PHP;
    }
}

class Bar
{
    public function getDefinition(): string
    {
        return <<<PHP
class Definition
{
    public function getResult(): array
    {
        return ['test' => 'data'];
    }
}
PHP;
    }
}
