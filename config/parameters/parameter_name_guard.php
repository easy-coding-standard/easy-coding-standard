<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('correct_to_typos', [
        'skip' => [
            '#skip.{1}#', 'exclude', 'ignore', 'exclude_checkers', 'exclude_checker', 'excluded_checker', 'excluded_checkers', 'skip_checker', 'skip_checkers',
        ],
        'line_length' => [
            'line_lenght', 'lineLength', 'lineLenght', 'line_size', 'lineSize',
        ],
        'exclude_files' => [
            'exclude_file', 'excluded_file', 'exclude_dir', 'excluded_dir', 'excluded_dirs', 'exclude_path', 'exclude_paths', 'excluded_path', 'excluded_paths', 'skip_dirs', 'skip_files',
        ],
    ]);
};
