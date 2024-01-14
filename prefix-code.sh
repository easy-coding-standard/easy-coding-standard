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

# 2. scope it -
note "Downloading php-scoper 0.18.10"
# released 2023-12
wget https://github.com/humbug/php-scoper/releases/download/0.18.10/php-scoper.phar -N --no-verbose


note "Running php-scoper"

# Work around possible PHP memory limits
php -d memory_limit=-1 php-scoper.phar add-prefix bin config src vendor composer.json --config scoper.php --force --ansi --output-dir scoped-code

# the output code is in "/scoped-code", lets move it up
# the local directories have to be empty to move easily
rm -r bin config src vendor composer.json
mv scoped-code/* .

note "Show prefixed files"
ls -l .

note "Dumping Composer Autoload"
composer dump-autoload --ansi --classmap-authoritative --no-dev

# make bin/ecs runnable without "php"
chmod 777 "bin/ecs"
chmod 777 "bin/ecs.php"

note "Finished"
