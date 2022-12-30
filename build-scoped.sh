#!/usr/bin/env bash

# inspired from https://github.com/rectorphp/rector/blob/main/build/build-rector-scoped.sh

# see https://stackoverflow.com/questions/66644233/how-to-propagate-colors-from-bash-script-to-github-action?noredirect=1#comment117811853_66644233
export TERM=xterm-color

# show errors
set -e

# script fails if trying to access to an undefined variable
set -u


# functions
note()
{
    MESSAGE=$1;
    printf "\n";
    echo "\033[0;33m[NOTE] $MESSAGE\033[0m";
}

# ---------------------------

# 2. scope it
note "Running scoper"
# @todo upgrade to 0.18.1
wget https://github.com/humbug/php-scoper/releases/download/0.17.5/php-scoper.phar -N --no-verbose

# Work around possible PHP memory limits
php -d memory_limit=-1 php-scoper.phar add-prefix bin config src packages vendor composer.json --config scoper.php --force --ansi --output-dir .

note "Show prefixed files"
ls -l .

note "Dumping Composer Autoload"
composer dump-autoload --ansi --classmap-authoritative --no-dev

# make bin/ecs runnable without "php"
chmod 777 "bin/ecs"
chmod 777 "bin/ecs.php"

note "Finished"
