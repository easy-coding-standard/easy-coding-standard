#!/usr/bin/env bash

# add patches
composer install --ansi

# but skip dev dependencies
composer update --no-dev --ansi

# remove tests and useless files, to make downgraded, scoped and deployed codebase as small as possible
rm -rf tests packages-tests packages/changed-files-detector/tests packages/configuration/tests packages/fixer-runner/tests packages/sniff-runner/tests packages/snippet-formatter/tests vendor/symfony/event-dispatcher vendor/symfony/event-dispatcher-contracts vendor/symfony/process vendor/symfony/stopwatch vendor/squizlabs/php_codesniffer/tests vendor/squizlabs/php_codesniffer/src/Standards/Generic/Tests vendor/squizlabs/php_codesniffer/src/Standards/MySource/Tests vendor/squizlabs/php_codesniffer/src/Standards/PEAR/Tests vendor/squizlabs/php_codesniffer/src/Standards/PSR1/Tests vendor/squizlabs/php_codesniffer/src/Standards/PSR12/Tests vendor/squizlabs/php_codesniffer/src/Standards/PSR2/Tests vendor/squizlabs/php_codesniffer/src/Standards/Squiz/Tests vendor/squizlabs/php_codesniffer/src/Standards/Zend/Tests vendor/squizlabs/php_codesniffer/src/Standards/Generic/Docs vendor/squizlabs/php_codesniffer/src/Standards/MySource/Docs vendor/squizlabs/php_codesniffer/src/Standards/PEAR/Docs vendor/squizlabs/php_codesniffer/src/Standards/PSR1/Docs vendor/squizlabs/php_codesniffer/src/Standards/PSR12/Docs vendor/squizlabs/php_codesniffer/src/Standards/PSR2/Docs vendor/squizlabs/php_codesniffer/src/Standards/Squiz/Docs vendor/squizlabs/php_codesniffer/src/Standards/Zend/Docs vendor/squizlabs/php_codesniffer/src/Reports vendor/squizlabs/php_codesniffer/src/Filters vendor/squizlabs/php_codesniffer/src/Generators vendor/friendsofphp/php-cs-fixer/src/Linter vendor/friendsofphp/php-cs-fixer/src/Runner vendor/friendsofphp/php-cs-fixer/src/Documentation vendor/friendsofphp/php-cs-fixer/src/Cache vendor/friendsofphp/php-cs-fixer/src/Console/Output vendor/friendsofphp/php-cs-fixer/src/Console/Report vendor/friendsofphp/php-cs-fixer/src/Console/SelfUpdate vendor/friendsofphp/php-cs-fixer/src/Console/Application.php vendor/friendsofphp/php-cs-fixer/src/Console/Command/DescribeCommand.php vendor/friendsofphp/php-cs-fixer/src/Console/Command/Documentation.php vendor/friendsofphp/php-cs-fixer/src/Console/Command/FixCommand.php vendor/friendsofphp/php-cs-fixer/src/Console/Command/HelpCommand.php vendor/friendsofphp/php-cs-fixer/src/Console/Command/ListSetsCommand.php vendor/friendsofphp/php-cs-fixer/src/Console/Command/SelfUpdateCommand.php vendor/friendsofphp/php-cs-fixer/src/Console/Command/ListFilesCommand.php vendor/friendsofphp/php-cs-fixer/src/Console/Command/DocumentationCommand.php

# downgrade with rector
mkdir rector-local
composer require rector/rector --working-dir rector-local
rector-local/vendor/bin/rector process bin src packages vendor --config build/rector-downgrade-php-72.php --ansi

# prefix
sh prefix-code.sh
