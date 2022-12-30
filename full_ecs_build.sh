#!/usr/bin/env bash

composer update --no-progress --ansi

rm -rf tests packages-tests

# downgrade
vendor/bin/rector process . --config build/config/config-downgrade.php --ansi

# prefix
sh build/build-ecs-scoped.sh . .

