#!/usr/bin/env bash

# add patches
composer install --ansi

# but skip dev dependencies
composer update --no-dev --ansi

rm -rf tests packages-tests

# downgrade with rector
mkdir rector-local
composer require rector/rector --working-dir rector-local
rector-local/vendor/bin/rector process bin src packages vendor --config build/rector-downgrade-php-72.php --ansi

# prefix
sh build-ecs-scoped.sh

