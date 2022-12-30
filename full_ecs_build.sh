#!/usr/bin/env bash

composer update --no-progress --ansi

rm -rf tests packages-tests

# downgrade with rector
mkdir rector-local
composer require rector/rector --working-dir rector-local
rector-local/vendor/bin/rector process . --config build/rector-downgrade-php-72.php --ansi

# prefix
sh build/build-ecs-scoped.sh . .

