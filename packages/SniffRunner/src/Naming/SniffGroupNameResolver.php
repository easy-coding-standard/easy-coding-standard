<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Naming;

use Gherkins\RegExpBuilderPHP\RegExp;
use Gherkins\RegExpBuilderPHP\RegExpBuilder;

final class SniffGroupNameResolver
{
    /**
     * @var RegExp
     */
    private $groupRegularExpression;

    public function resolveFromSniffClass(string $sniffClass) : string
    {
        $group = $this->getGroupRegularExpression()
            ->exec($sniffClass)[0];

        if ($group === 'CodingStandard') { // @todo: hotfix, outsource to class naming
            return $this->getMainVendor($sniffClass);
        }

        return $group;
    }

    private function getGroupRegularExpression() : RegExp
    {
        if ($this->groupRegularExpression) {
            return $this->groupRegularExpression;
        }

        return $this->groupRegularExpression = $this->createGroupRegularExpression();
    }

    private function createGroupRegularExpression(): RegExp
    {
        $builder = new RegExpBuilder;
        return $builder->anythingBut('\\') // after "\\"
            ->ahead($builder->getNew()->exactly(1)->of('\\Sniffs')) // before "\\Sniffs"
            ->getRegExp();
    }

    private function getMainVendor(string $class) : string
    {
        $classParts = explode('\\', $class);
        return $classParts[0];
    }
}
