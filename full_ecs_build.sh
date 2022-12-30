#!/usr/bin/env bash

composer update --no-progress --ansi

cp -r . ecs-build

rm -rf ecs-build/tests ecs-build/packages-tests

# downgrade
vendor/bin/rector process ecs-build --config build/config/config-downgrade.php -a ecs-build/vendor/autoload.php --ansi

# prefix
sh build/build-ecs-scoped.sh ecs-build ecs-prefixed-downgraded
